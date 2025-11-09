<?php
// admin/prices.php
require '../includes/connect.php';
session_start();
require_once 'log_activity.php';

header("Content-Type: text/html; charset=UTF-8");

$anyUpdated = false;

// ✅ Security check
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Super Admin', 'Admin'])) {
    echo "<div class='alert alert-danger'>Unauthorized access!</div>";
    exit();
}

// Initialize message variable
$message = '';
if (isset($_SESSION['success_message'])) {
    $message = '<div class="warning alert-success" 
        style="font-size:13px; padding:18px; margin:20px 0; text-align:left; font-weight:bold; border-radius:6px;">'
        . htmlspecialchars($_SESSION['success_message']) . 
    '</div>';
    unset($_SESSION['success_message']);
}

// ✅ Handle Add request FIRST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax']) && $_POST['ajax'] == 'add') {
    $response = ["success" => false, "error" => ""];

    $venue    = trim($_POST['venue'] ?? '');
    $name     = trim($_POST['name'] ?? '');
    $duration_hours     = trim($_POST['duration_hours'] ?? '');
    $day_type = $_POST['day_type'] ?? null;
    $duration = $_POST['duration'] ?? null;
    $price    = (float) preg_replace('/[^0-9.]/', '', (string)($_POST['price'] ?? '0'));
    $notes    = $_POST['notes'] ?? null;
    $catering = $_POST['affiliate_catering']?? null;
    $lights = $_POST['affiliate_lights']?? null;
    $inclusions = $_POST['inclusions']?? null;
    $max_guest = $_POST['max_guest']?? null;    

    $stmt = $conn->prepare("
        INSERT INTO prices (venue, name, duration_hours, day_type, duration, price, notes, affiliate_catering ,affiliate_lights ,inclusions, max_guest)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    if ($stmt) {
        $stmt->bind_param("ssssdissssi", $venue, $name, $duration_hours, $day_type, $duration, $price, $notes, $catering, $lights, $inclusions, $max_guest);
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['redirect'] = true;
            $_SESSION['success_message'] = 'Prices updated successfully!';
            log_activity($_SESSION['admin_id'], 'Price Management', 'Added new price record: ' . $name . ' (Venue: ' . $venue . ', Price: ' . $price . ')');
        } else {
            $response['error'] = "Database insert failed.";
        }
        $stmt->close();
    }

    echo json_encode($response);
    exit();
}

// ✅ Handle Archive request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax']) && $_POST['ajax'] == 'archive') {
    $response = ["success" => false, "error" => ""];

    if (!empty($_POST['archive_ids']) && is_array($_POST['archive_ids'])) {
        $ids = array_map('intval', $_POST['archive_ids']);
        $idsStr = implode(",", $ids);

        if ($idsStr) {
            $sql = "UPDATE prices SET is_archived = 1 WHERE id IN ($idsStr)";
            if ($conn->query($sql)) {
                $response['success'] = true;
                $response['redirect'] = true;
                $_SESSION['success_message'] = 'Prices updated successfully!';
                log_activity($_SESSION['admin_id'], 'Price Management', 'Archived price records with IDs: ' . $idsStr);
            } else {
                $response['error'] = "Database update failed.";
            }
        }
    } else {
        $response['error'] = "No records selected.";
    }

    echo json_encode($response);
    exit();
}

// ✅ Handle AJAX update request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax']) && $_POST['ajax'] == '1') {
    $response = ["success" => false];

    if (!empty($_POST['prices'])) {
        foreach ($_POST['prices'] as $id => $fields) {
            $id = (int)$id;

            if ($id > 0) {
                $stmt = $conn->prepare("
                    UPDATE prices 
                    SET name = ?, duration_hours = ?, day_type = ?, duration = ?, price = ?, notes = ?, 
                        affiliate_catering = ?, affiliate_lights = ?, inclusions = ?, max_guest = ?
                    WHERE id = ?
                ");
                if ($stmt) {
                    $name          = $fields['name'] ?? '';
                    $duration_hours = $fields['duration_hours'] ?? '';
                    $day_type      = $fields['day_type'] ?? null;
                    $duration      = $fields['duration'] ?? null;
                    $price         = (float) preg_replace('/[^0-9.]/', '', (string)($fields['price'] ?? '0'));
                    $notes         = $fields['notes'] ?? null;
                    $catering      = $fields['affiliate_catering'] ?? null;
                    $lights        = $fields['affiliate_lights'] ?? null;
                    $inclusions    = $fields['inclusions'] ?? null;
                    $max_guest     = !empty($fields['max_guest']) ? (int)$fields['max_guest'] : null;

                    $stmt->bind_param(
                        "ssssdssssii", 
                        $name, 
                        $duration_hours, 
                        $day_type, 
                        $duration, 
                        $price, 
                        $notes, 
                        $catering, 
                        $lights, 
                        $inclusions, 
                        $max_guest,
                        $id
                    );

                    if ($stmt->execute() && $stmt->affected_rows > 0) {
                        $anyUpdated = true;
                        log_activity($_SESSION['admin_id'], 'Price Management', 'Updated price record ID: ' . $id . ' (Name: ' . $name . ', Price: ' . $price . ')');
                    }
                    $stmt->close();
                }
            }
        }
        if ($anyUpdated) {
            $response['success'] = true;
            $response['redirect'] = true;
            $_SESSION['success_message'] = 'Prices updated successfully!';
        }
    }

    echo json_encode($response);
    exit();
}

// ✅ Fetch all prices grouped by venue, ordered by id
$prices = [];
$query = $conn->query("SELECT * FROM prices WHERE is_archived = 0 ORDER BY venue, id ASC");
if ($query) {
    while ($row = $query->fetch_assoc()) {
        $prices[$row['venue']][] = $row;
    }
} else {
    echo "<div class='alert alert-danger'>Failed to fetch prices: " . htmlspecialchars($conn->error) . "</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Prices</title>
  <link rel="stylesheet" href="../assets/css/admin/bootstrap.css">
  <link rel="icon" href="../assets/favicon.ico">
</head>
<body>
<div class="container-fluid">
  <h3>Manage Prices</h3>
  
  <!-- Success Message Display Area -->
  <?php echo $message; ?>

<?php foreach ($prices as $venue => $items): ?>
  <h4 class="mt-3"><?php echo htmlspecialchars($venue); ?></h4>
  <form method="POST" class="pricesForm">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Name</th>

        <?php if ($venue === 'Resort'): ?>
          <th>Duration (Hours)</th>
          <th>Duration (Month)</th>
          <th>Day Type</th>
          <th style="width:160px;">Price</th>
          <th>Maximum Guests (Package)</th>
          <th>Notes</th>


          <?php elseif ($venue === 'Room'): ?>
            <th>Duration (Hours)</th>
            <th style="width:500px;">Price</th>

          <?php elseif ($venue === 'Affiliates'): ?>
            <th style="width:160px;">Price</th>
            <th>Notes</th>

          <?php elseif ($venue === 'Mini Function Hall'): ?>
            <th>Day Type</th>
            <th style="width:160px;">Price</th>
            <th>Notes</th>
            <th>Maximum Guests (Package)</th>
            <th>Catering</th>
            <th>Lights & Sound</th>
            <th>Inclusions</th>

            <?php elseif (in_array($venue, ['Renatos Hall', 'Renatos Pavilion'])): ?>
              <th>Day Type</th>
              <th style="width:160px;">Price</th>
              <th>Notes</th>
              <th>Maximum Guests (Package)</th>
              <th>Catering</th>
              <th>Lights & Sound</th>
              <th>Inclusions</th>
            <?php endif; ?>


        </tr>
      </thead>
      <tbody>
          <?php foreach ($items as $row): ?>
            <tr>
              <!-- Name -->
              <td>
                <input type="text" class="form-control"
                      name="prices[<?php echo (int)$row['id']; ?>][name]"
                      value="<?php echo htmlspecialchars($row['name']); ?>">
              </td>

              <?php if ($venue === 'Resort'): ?>
      <td>
        <input type="text" class="form-control"
              name="prices[<?php echo (int)$row['id']; ?>][duration_hours]"
              value="<?php echo htmlspecialchars($row['duration_hours']); ?>">
      </td>
      <td>
        <input type="text" class="form-control"
              name="prices[<?php echo (int)$row['id']; ?>][duration]"
              value="<?php echo htmlspecialchars($row['duration']); ?>">
      </td>
      <td>
        <select class="form-control" name="prices[<?php echo (int)$row['id']; ?>][day_type]">
          <option value="">-- Select --</option>
          <option value="Weekdays" <?php if($row['day_type']=="Weekdays") echo "selected"; ?>>Weekdays</option>
          <option value="Weekends" <?php if($row['day_type']=="Weekends") echo "selected"; ?>>Weekends</option>
          <option value="Any_Day" <?php if($row['day_type']=="Any_Day") echo "selected"; ?>>Any Day</option>
        </select>
      </td>
      <td>
        <div class="input-group">
          <span class="input-group-addon">₱</span>
          <input type="text" class="form-control price-input"
                name="prices[<?php echo (int)$row['id']; ?>][price]"
                value="<?php echo number_format((float)$row['price'], 2); ?>">
        </div>
      </td>
      <td>
        <input type="number" class="form-control"
              name="prices[<?php echo (int)$row['id']; ?>][max_guest]"
              value="<?php echo htmlspecialchars($row['max_guest']); ?>">
      </td>
      <td>
        <input type="text" class="form-control"
              name="prices[<?php echo (int)$row['id']; ?>][notes]"
              value="<?php echo htmlspecialchars($row['notes']); ?>">
      </td>

          <?php elseif ($venue === 'Room'): ?>
              <td>
                  <input type="text" class="form-control"
                        name="prices[<?php echo (int)$row['id']; ?>][duration_hours]"
                        value="<?php echo htmlspecialchars($row['duration_hours'] ?? ''); ?>">
              </td>

            <td>
              <div class="input-group">
                <span class="input-group-addon">₱</span>
                <input type="text" class="form-control"
                      name="prices[<?php echo (int)$row['id']; ?>][price]"
                      value="<?php echo number_format((float)$row['price'], 2); ?>">
              </div>
            </td>

          <?php elseif ($venue === 'Affiliates'): ?>
            
            <td>
              <div class="input-group">
                <span class="input-group-addon">₱</span>
                <input type="text" class="form-control"
                      name="prices[<?php echo (int)$row['id']; ?>][price]"
                      value="<?php echo number_format((float)$row['price'], 2); ?>">
              </div>
            </td>

            <td>
              <input type="text" class="form-control"
                     name="prices[<?php echo (int)$row['id']; ?>][notes]"
                     value="<?php echo htmlspecialchars($row['notes']); ?>">
            </td>

<?php elseif ($venue === 'Mini Function Hall'): ?>
  <td>
    <select class="form-control" name="prices[<?php echo (int)$row['id']; ?>][day_type]">
      <option value="">-- Select --</option>
      <option value="Weekdays" <?php if($row['day_type']=="Weekdays") echo "selected"; ?>>Weekdays</option>
      <option value="Weekends" <?php if($row['day_type']=="Weekends") echo "selected"; ?>>Weekends</option>
      <option value="Any_Day" <?php if($row['day_type']=="Any_Day") echo "selected"; ?>>Any Day</option>
    </select>
  </td>

<td>
  <div class="input-group">
    <span class="input-group-addon">₱</span>
    <input type="text" class="form-control"
           name="prices[<?php echo (int)$row['id']; ?>][price]"
           value="<?php echo number_format((float)$row['price'], 2); ?>">
  </div>
</td>

  <td>
    <input type="text" class="form-control"
           name="prices[<?php echo (int)$row['id']; ?>][notes]"
           value="<?php echo htmlspecialchars($row['notes']); ?>">
  </td>

  <td>
    <input type="number" class="form-control"
           name="prices[<?php echo (int)$row['id']; ?>][max_guest]"
           value="<?php echo htmlspecialchars($row['max_guest']); ?>">
  </td>

  <td>
    <input type="text" class="form-control"
           name="prices[<?php echo (int)$row['id']; ?>][affiliate_catering]"
           value="<?php echo htmlspecialchars($row['affiliate_catering'] ?? ''); ?>">
  </td>
  <td>
    <input type="text" class="form-control"
           name="prices[<?php echo (int)$row['id']; ?>][affiliate_lights]"
           value="<?php echo htmlspecialchars($row['affiliate_lights'] ?? ''); ?>">
  </td>
  <td>
    <input type="text" class="form-control"
           name="prices[<?php echo (int)$row['id']; ?>][inclusions]"
           value="<?php echo htmlspecialchars($row['inclusions'] ?? ''); ?>">
  </td>

<?php elseif (in_array($venue, ['Renatos Hall', 'Renatos Pavilion'])): ?>
  <td>
    <select class="form-control" name="prices[<?php echo (int)$row['id']; ?>][day_type]">
      <option value="">-- Select --</option>
      <option value="Weekdays" <?php if($row['day_type']=="Weekdays") echo "selected"; ?>>Weekdays</option>
      <option value="Weekends" <?php if($row['day_type']=="Weekends") echo "selected"; ?>>Weekends</option>
      <option value="Any_Day" <?php if($row['day_type']=="Any_Day") echo "selected"; ?>>Any Day</option>
    </select>
  </td>

<td>
  <div class="input-group">
    <span class="input-group-addon">₱</span>
    <input type="text" class="form-control"
           name="prices[<?php echo (int)$row['id']; ?>][price]"
           value="<?php echo number_format((float)$row['price'], 2); ?>">
  </div>
</td>

  <td>
    <input type="text" class="form-control"
           name="prices[<?php echo (int)$row['id']; ?>][notes]"
           value="<?php echo htmlspecialchars($row['notes']); ?>">
  </td>

  <td>
    <input type="number" class="form-control"
           name="prices[<?php echo (int)$row['id']; ?>][max_guest]"
           value="<?php echo htmlspecialchars($row['max_guest']); ?>">
  </td>

  <td>
    <input type="text" class="form-control"
           name="prices[<?php echo (int)$row['id']; ?>][affiliate_catering]"
           value="<?php echo htmlspecialchars($row['affiliate_catering'] ?? ''); ?>">
  </td>
  <td>
    <input type="text" class="form-control"
           name="prices[<?php echo (int)$row['id']; ?>][affiliate_lights]"
           value="<?php echo htmlspecialchars($row['affiliate_lights'] ?? ''); ?>">
  </td>
  <td>
    <input type="text" class="form-control"
           name="prices[<?php echo (int)$row['id']; ?>][inclusions]"
           value="<?php echo htmlspecialchars($row['inclusions'] ?? ''); ?>">
  </td>
<?php endif; ?>

        </tr>
        
      <?php endforeach; ?>
      </tbody>
    </table>

    <button type="button" class="btn btn-primary saveBtn" disabled>Save Changes</button>
    <button type="button" class="btn btn-success addBtn" data-venue="<?php echo htmlspecialchars($venue); ?>">Add New</button>
    <button type="button" class="btn btn-warning archiveBtn" data-venue="<?php echo htmlspecialchars($venue); ?>">Archive</button>
  </form>
<?php endforeach; ?>

<!-- Confirm Modal -->
<div class="modal fade" id="confirmModal">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h4 class="modal-title">Confirm Update</h4></div>
    <div class="modal-body">Do you want to save changes?</div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      <button type="button" class="btn btn-success" id="confirmSave">Yes, Save</button>
    </div>
  </div></div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header">
      <h4 class="modal-title">Add New</h4>
      <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>
    <div class="modal-body">
      <div id="addMessages"></div>
      <form id="addForm">
        <input type="hidden" name="ajax" value="add">
        <input type="hidden" name="venue" id="venueField">
        <div id="addFormFields"></div>
      </form>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      <button type="button" class="btn btn-success" id="addSave">Save</button>
    </div>
  </div></div>
</div>

<!-- Archive Modal -->
<div class="modal fade" id="archiveModal">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header">
      <h4 class="modal-title">Archive Records</h4>
    </div>
    <div class="modal-body">
      <div id="archiveMessages"></div>
      <form id="archiveForm">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Select</th>
              <th>Name</th>
              <th>Duration</th>
              <th>Day Type</th>
              <th>Price</th>
              <th>Notes</th>
            </tr>
          </thead>
          <tbody id="archiveTableBody">
          </tbody>
        </table>
      </form>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      <button type="button" class="btn btn-warning" id="confirmArchive">Archive Selected</button>
    </div>
  </div></div>
</div>

<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

<script>
// Complete script section - place this in your <script> tags

$(function () {
  function buildAddForm(venue) {
    const v = (venue || '').toString().trim();
    const vl = v.toLowerCase();
    let html = '';

    if (vl === 'resort') {
      html = `
        <div class="form-group"><label>Name</label><input id="name" name="name" type="text" class="form-control"></div>
        <div class="form-group"><label>Duration (Hours)</label><input id="duration_hours" name="duration_hours" type="text" class="form-control"></div>
        <div class="form-group"><label>Duration (Month)</label><input id="duration" name="duration" type="text" class="form-control"></div>
        <div class="form-group"><label>Day Type</label>
          <select id="day_type" name="day_type" class="form-control">
            <option value="">-- Select --</option>
            <option value="Weekdays">Weekdays</option>
            <option value="Weekends">Weekends</option>
            <option value="Any_Day">Any Day</option>
          </select>
        </div>
        <div class="form-group"><label>Price</label><input id="price" name="price" type="text" class="form-control"></div>
        <div class="form-group"><label>Maximum Guests (Package)</label><input id="max_guest" name="max_guest" type="number" class="form-control"></div>
        <div class="form-group"><label>Notes</label><input id="notes" name="notes" type="text" class="form-control"></div>
      `;
    } else if (vl === 'room' || vl === 'rooms') {
      html = `
        <div class="form-group"><label>Name</label><input id="name" name="name" type="text" class="form-control"></div>
        <div class="form-group"><label>Duration (Hours)</label><input id="duration_hours" name="duration_hours" type="text" class="form-control"></div>
        <div class="form-group"><label>Price</label><input id="price" name="price" type="text" class="form-control"></div>
      `;
    } else if (vl === 'affiliates') {
      html = `
        <div class="form-group"><label>Name</label><input id="name" name="name" type="text" class="form-control"></div>
        <div class="form-group"><label>Price</label><input id="price" name="price" type="text" class="form-control"></div>
        <div class="form-group"><label>Notes</label><input id="notes" name="notes" type="text" class="form-control"></div>
      `;
    } else if (vl === 'mini function hall' || vl === 'renatos hall' || vl === 'renatos pavilion' || vl === 'pavilion') {
      html = `
        <div class="form-group"><label>Name</label><input id="name" name="name" type="text" class="form-control"></div>
        <div class="form-group"><label>Day Type</label>
          <select id="day_type" name="day_type" class="form-control">
            <option value="">-- Select --</option>
            <option value="Weekdays">Weekdays</option>
            <option value="Weekends">Weekends</option>
            <option value="Any_Day">Any Day</option>
          </select>
        </div>
        <div class="form-group"><label>Price</label><input id="price" name="price" type="text" class="form-control"></div>
        <div class="form-group"><label>Notes</label><input id="notes" name="notes" type="text" class="form-control"></div>
        <div class="form-group"><label>Maximum Guests (Package)</label><input id="max_guest" name="max_guest" type="number" class="form-control"></div>
        <div class="form-group"><label>Catering</label><input id="affiliate_catering" name="affiliate_catering" type="text" class="form-control"></div>
        <div class="form-group"><label>Lights & Sound</label><input id="affiliate_lights" name="affiliate_lights" type="text" class="form-control"></div>
        <div class="form-group"><label>Inclusions</label><input id="inclusions" name="inclusions" type="text" class="form-control"></div>
      `;
    } else {
      html = `
        <div class="form-group"><label>Name</label><input id="name" name="name" type="text" class="form-control"></div>
        <div class="form-group"><label>Price</label><input id="price" name="price" type="text" class="form-control"></div>
        <div class="form-group"><label>Notes</label><input id="notes" name="notes" type="text" class="form-control"></div>
      `;
    }

    $("#addFormFields").html(html);
  }

  $(document).on('input change', '#addForm .form-control', function () {
    $(this).removeClass('has-error');
    $("#addMessages").empty();
  });

  $(document).on('click', '.addBtn', function (e) {
    const venue = $(this).data('venue') || '';
    $("#venueField").val(venue);
    $("#addMessages").empty();
    buildAddForm(venue);
    $("#addForm .form-control").removeClass('has-error');
    $("#addForm")[0].reset();
    $("#addModal").modal('show');
  });

  function validateAddForm() {
    const venueRaw = $("#venueField").val() || '';
    const venue = venueRaw.trim().toLowerCase();

    const getVal = id => {
      const el = $('#' + id);
      return el.length ? (el.val() || '').toString().trim() : '';
    };
    const name = getVal('name');
    const duration_hours = getVal('duration_hours');
    const duration = getVal('duration');
    const day_type = getVal('day_type');
    const priceRaw = getVal('price');
    const notes = getVal('notes');

    const priceNum = parseFloat(priceRaw.replace(/,/g, '').replace(/[^0-9.]/g, '')) || 0;

    $("#addForm .form-group").removeClass('has-error');
    $("#addForm .help-block").remove();
    $("#addMessages").empty();

    let firstInvalidSelector = null;

    function markInvalid(selector, message = "This field is required") {
      if (!firstInvalidSelector) firstInvalidSelector = selector;
      const $el = $(selector);
      const $group = $el.closest('.form-group');
      $group.addClass('has-error');
      if (!$group.find(".help-block").length) {
        $group.append(`<span class="help-block">${message}</span>`);
      }
    }

    if (!venue) {
      $("#addMessages").html('<div class="alert alert-danger">Please select a venue.</div>');
      return false;
    }

    if (venue === 'affiliates') {
      if (!name) markInvalid('#name');
      if (!notes) markInvalid('#notes');
    } else if (venue === 'mini function hall' || venue === 'renatos hall' || venue === 'renatos pavilion' || venue === 'pavilion') {
      if (!name) markInvalid('#name');
      if (!day_type) markInvalid('#day_type');
      if (!(priceNum > 0)) markInvalid('#price');
    } else if (venue === 'resort') {
      if (!name) markInvalid('#name');
      if (!duration_hours) markInvalid('#duration_hours');
      if (!duration) markInvalid('#duration');
      if (!day_type) markInvalid('#day_type');
      if (!(priceNum > 0)) markInvalid('#price');
    } else if (venue === 'room' || venue === 'rooms') {
      if (!name) markInvalid('#name');
      if (!duration_hours) markInvalid('#duration_hours');
      if (!(priceNum > 0)) markInvalid('#price');
    } else {
      if (!name) markInvalid('#name');
      if (!(priceNum > 0)) markInvalid('#price');
    }

    if (firstInvalidSelector) {
      return false;
    }

    return true;
  }

  $(document).on("focus click", "#addForm .form-control", function () {
    const $group = $(this).closest('.form-group');
    $group.removeClass('has-error');
    $group.find(".help-block").remove();
  });

  $(document).on('click', '#addSave', function (e) {
    e.preventDefault();
    $("#addMessages").empty();

    if (!validateAddForm()) return;

    const $btn = $(this);
    $btn.prop('disabled', true);

    const formData = $("#addForm").serialize();
    $.post('prices.php', formData)
      .done(function (resp) {
        let res = resp;
        if (typeof resp === 'string') {
          try { res = JSON.parse(resp); } catch (e) { res = { success: false, error: 'Unexpected response' }; }
        }
        if (res && res.success && res.redirect) {
          $("#addModal").modal('hide');
          const timestamp = Date.now() + '-' + Math.random().toString(36).substr(2, 9);
          window.location.replace(`${window.location.pathname}?_=${timestamp}`);
        } else {
          $("#addMessages").html('<div class="alert alert-danger">'+ (res.error || 'Failed to add new record.') +'</div>');
        }
      })
      .fail(function () {
        $("#addMessages").html('<div class="alert alert-danger">Network or server error. Please try again.</div>');
      })
      .always(function () {
        $btn.prop('disabled', false);
      });
  });
});

// Save Changes Handler
$(function(){
  let activeForm = null;

  $(".pricesForm").on("input change", "input, select", function(){
    let form = $(this).closest("form");
    form.find(".saveBtn").prop("disabled", false);
  });

  $(".saveBtn").click(function(){
    activeForm = $(this).closest("form");
    $("#confirmModal").modal("show");
  });

  $("#confirmSave").click(function(){
    if (!activeForm) return;

    let changed = false;

    activeForm.find("input, select, textarea").each(function() {
      if ($(this).val() !== $(this).data("original")) {
        changed = true;
      }
    });

    if (!changed) {
      $("#confirmModal").modal("hide");
      return;
    }   

    let formData = activeForm.serialize();
    formData += "&ajax=1";

    $.post("prices.php", formData, function(response){
      try {
        let res = JSON.parse(response);
        if (res.success && res.redirect) {
          $("#confirmModal").modal("hide");
          $(".modal-backdrop").remove();
          const timestamp = Date.now() + '-' + Math.random().toString(36).substr(2, 9);
          window.location.replace(`${window.location.pathname}?_=${timestamp}`);
        } else {
          $("#confirmModal").modal("hide");
        }
      } catch(e) {
        $("#confirmModal").modal("hide");
      }
    });
  });
});

// Modal Reset Handlers
$('#addModal').on('hidden.bs.modal', function () {
  $("#addMessages").html("");
  $("#addForm")[0].reset();
});

$('#addModal').on('shown.bs.modal', function () {
  $("#addMessages").html("");
});

// Archive Modal Handler - FIXED ID EXTRACTION
$(".archiveBtn").click(function(){
  let venue = $(this).data("venue");
  $("#archiveMessages").html("");
  $("#archiveTableBody").html("");

  // Get the form associated with this button
  let form = $(this).closest("form");
  let rows = form.find("tbody tr");

  console.log("Archive modal opening for venue:", venue);
  console.log("Found rows:", rows.length);

  if (rows.length === 0) {
    $("#archiveMessages").html('<div class="alert alert-warning">No records found for this venue.</div>');
    $("#archiveModal").modal("show");
    return;
  }

  // Escape HTML function
  function escapeHtml(text) {
    let div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  rows.each(function(index){
    let $row = $(this);
    
    // Try multiple methods to find an input with an ID
    let allInputs = $row.find("input, select").filter(function() {
      let name = $(this).attr("name");
      return name && name.indexOf("prices[") !== -1;
    });
    
    console.log("Row", index, "- Found inputs:", allInputs.length);
    
    if (allInputs.length === 0) {
      console.log("Row", index, "- No inputs found, skipping");
      return;
    }

    // Extract ID from any input in this row
    let id = null;
    allInputs.each(function() {
      let name = $(this).attr("name");
      console.log("Row", index, "- Checking name:", name);
      let match = name.match(/prices\[(\d+)\]/);
      if (match && match[1]) {
        id = match[1];
        console.log("Row", index, "- Extracted ID:", id);
        return false; // break the loop
      }
    });

    if (!id || id === "0") {
      console.log("Row", index, "- Invalid ID, skipping");
      return;
    }

    // Get values using the exact ID
    let name = $row.find("input[name='prices["+id+"][name]']").val() || "";
    let duration_hours = $row.find("input[name='prices["+id+"][duration_hours]']").val() || "";
    let duration = $row.find("input[name='prices["+id+"][duration]']").val() || "";
    let day_type = $row.find("select[name='prices["+id+"][day_type]']").val() || "";
    let price = $row.find("input[name='prices["+id+"][price]']").val() || "";
    let notes = $row.find("input[name='prices["+id+"][notes]']").val() || "";

    console.log("Row", index, "- Name:", name, "Price:", price);

    // Combine duration fields for display
    let durationDisplay = duration_hours || duration || "N/A";

    // Build row HTML for archive modal
    let rowHtml = `
      <tr>
        <td><input type="checkbox" name="archive_ids[]" value="${escapeHtml(id)}"></td>
        <td>${escapeHtml(name)}</td>
        <td>${escapeHtml(durationDisplay)}</td>
        <td>${escapeHtml(day_type) || "N/A"}</td>
        <td>${escapeHtml(price)}</td>
        <td>${escapeHtml(notes) || "N/A"}</td>
      </tr>
    `;
    $("#archiveTableBody").append(rowHtml);
  });

  // Check if any rows were added
  if ($("#archiveTableBody tr").length === 0) {
    $("#archiveMessages").html('<div class="alert alert-warning">Unable to load records for archiving.</div>');
  } else {
    console.log("Archive modal populated with", $("#archiveTableBody tr").length, "rows");
  }

  $("#archiveModal").modal("show");
});

// Confirm Archive Handler - FIXED with proper serialization
$("#confirmArchive").click(function(){
  $("#archiveMessages").html("");
  
  let checkedBoxes = $("#archiveTableBody input[name='archive_ids[]']:checked");
  
  console.log("Checked boxes found:", checkedBoxes.length); // Debug log
  
  if (checkedBoxes.length === 0) {
    $("#archiveMessages").html('<div class="alert alert-danger">⚠️ Please select at least one record to archive.</div>');
    return;
  }

  // Disable button to prevent double-click
  let $btn = $(this);
  $btn.prop('disabled', true);

  // Build form data as URL-encoded string
  let archiveIds = [];
  checkedBoxes.each(function() {
    let id = $(this).val();
    console.log("Adding ID to archive:", id); // Debug log
    archiveIds.push(id);
  });

  // Create proper URL-encoded data that PHP can read
  let formData = 'ajax=archive';
  $.each(archiveIds, function(index, id) {
    formData += '&archive_ids[]=' + encodeURIComponent(id);
  });

  console.log("Sending data:", formData); // Debug log

  $.ajax({
    url: 'prices.php',
    type: 'POST',
    data: formData,
    contentType: 'application/x-www-form-urlencoded',
    success: function(response) {

      let res;
      try {
        res = typeof response === 'string' ? JSON.parse(response) : response;
      } catch(e) {
        $("#archiveMessages").html('<div class="alert alert-danger">Invalid server response</div>');
        $btn.prop('disabled', false);
        return;
      }
      

      if (res && res.success && res.redirect) {
        $("#archiveModal").modal("hide");
        $(".modal-backdrop").remove();
        const timestamp = Date.now() + '-' + Math.random().toString(36).substr(2, 9);
        window.location.replace(`${window.location.pathname}?_=${timestamp}`);
      } else {
        $("#archiveMessages").html('<div class="alert alert-danger">'+(res.error || "Failed to archive records.")+'</div>');
        $btn.prop('disabled', false);
      }
    },
    error: function(xhr, status, error) {
      $("#archiveMessages").html('<div class="alert alert-danger">Network error: ' + error + '</div>');
      $btn.prop('disabled', false);
    }
  });
});

// Handle success messages - Auto-fade after 3 seconds
document.addEventListener("DOMContentLoaded", function () {
  const alerts = document.querySelectorAll(".warning");
  alerts.forEach(alert => {
    setTimeout(() => {
      alert.classList.add("fade");
      setTimeout(() => {
        if (alert && alert.parentNode) {
          alert.parentNode.removeChild(alert);
        }
      }, 500);
    }, 3000);
  });
});




</script>
</body>
</html>