<?php
	require_once '../includes/validate.php';
	require '../includes/name.php';
	require '../includes/connect.php';
?>
				<div class = "alert alert-info">Re-Scheduled Reservations</div>
				<table id = "table" class = "table table-bordered">
					<thead>
						<tr>
							<th>Name</th>
							<th>Contact No</th>
							<th>Reservation Type</th>
							<th>Original Check-in Date</th>
							<th>New Check-in Date</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$query = $conn->query("SELECT * FROM `reservations` WHERE `status` = 'rescheduled' ORDER BY `created_at` DESC");
							while($fetch = $query->fetch_array()){
						?>
						<tr>
							<td><?php echo $fetch['full_name']?></td>
							<td><?php echo $fetch['phone']?></td>
							<td><?php echo $fetch['reservation_type']?></td>
							<td><?php echo date("M d, Y", strtotime($fetch['original_checkin_date']))?></td>
							<td><?php echo date("M d, Y", strtotime($fetch['checkin_date']))?></td>
							<td><?php echo $fetch['status']?></td>
							<td><center><a class = "btn btn-info" href = "confirm_reserve.php?id=<?php echo $fetch['id']?>"><i class = "glyphicon glyphicon-check"></i> Confirm</a></center></td>
						</tr>
						<?php
							}
						?>
					</tbody>
				</table>
<script src = "../admin/js/jquery.js"></script>
<script src = "../admin/js/bootstrap.js"></script>	
<script src = "../admin/js/jquery.dataTables.js"></script>
<script src = "../admin/js/dataTables.bootstrap.js"></script>	

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>

<script type = "text/javascript">
	$(document).ready(function(){
		$("#table").DataTable();
	});
</script>
