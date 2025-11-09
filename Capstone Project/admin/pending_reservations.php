<table id="table" class="table table-bordered">
    <thead>
        <tr>
            <th>Name</th>
            <th>Contact No</th>
            <th>Reservation Type</th>
            <th>Reservation Date</th>
            <th>Check-in Date</th>
            <th>Status</th>
            <th>Payment Method</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
            require '../includes/connect.php';
            $query = $conn->query("SELECT * FROM `reservations` WHERE `status` = 'pending' ORDER BY `created_at` DESC");
            while($fetch = $query->fetch_array()){
        ?>
        <tr>
            <td><?php echo $fetch['full_name']?></td>
            <td><?php echo $fetch['phone']?></td>
            <td><?php echo $fetch['reservation_type']?></td>
            <td><?php echo date("M d, Y", strtotime($fetch['created_at']))?></td>
            <td>
                <strong>
                    <?php 
                        if($fetch['checkin_date'] <= date("Y-m-d", strtotime("+8 HOURS"))){
                            echo "<label style='color:#ff0000;'>".date("M d, Y", strtotime($fetch['checkin_date']))."</label>";
                        } else {
                            echo "<label style='color:#00ff00;'>".date("M d, Y", strtotime($fetch['checkin_date']))."</label>";
                        }
                    ?>
                </strong>
            </td>
            <td><?php echo $fetch['status']?></td>
            <td><?php echo $fetch['payment_method']?></td>
            <td>
                <center>
                    <!-- Confirm Button -->
                    <button class="btn btn-success" data-toggle="modal" data-target="#confirmModal<?php echo $fetch['id']?>">
                        <i class="glyphicon glyphicon-check"></i> Confirm
                    </button> 
                    
                    <!-- Discard Button -->
                    <a class="btn btn-danger" onclick="return showToastrConfirm(this, 'Are you sure you want to discard this reservation?');" href="delete_reserve.php?id=<?php echo $fetch['id']?>">
                        <i class="glyphicon glyphicon-trash"></i> Discard
                    </a>
                </center>
            </td>
        </tr>



        <!-- Confirm Modal -->
        <div class="modal fade" id="confirmModal<?php echo $fetch['id']?>" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel<?php echo $fetch['id']?>" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="confirmModalLabel<?php echo $fetch['id']?>">Confirm Reservation</h4>
              </div>
              <div class="modal-body">
                <p>Are you sure you want to confirm this reservation?</p>
                <p><strong>Name:</strong> <?php echo $fetch['full_name']?></p>
                <p><strong>Gmail:</strong> <?php echo $fetch['email']?></p>
                <p><strong>Notes:</strong> <?php echo $fetch['special_requests']?></p>
                <p><strong>Type:</strong> <?php echo $fetch['reservation_type']?></p>
                <p><strong>Date:</strong> <?php echo date("M d, Y", strtotime($fetch['checkin_date']))?></p>
                <p><strong>Time:</strong> <?php echo date("h:i A", strtotime($fetch['checkin_time']))?></p>
                <hr>
                <h5>Proof of Payment</h5>
                <?php if($fetch['proof_of_payment']): ?>
                    <a href="../<?php echo $fetch['proof_of_payment']?>" target="_blank">
                        <img src="../<?php echo $fetch['proof_of_payment']?>" alt="Proof of Payment" style="max-width: 50%; height: auto;">
                    </a>
                <?php else: ?>
                    <p>No proof of payment uploaded.</p>
                <?php endif; ?>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <a href="confirm_reserve.php?id=<?php echo $fetch['id']?>" class="btn btn-success">
                    Confirm
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Re-schedule Modal -->
        <div class="modal fade" id="rescheduleModal<?php echo $fetch['id']?>" tabindex="-1" role="dialog" aria-labelledby="rescheduleModalLabel<?php echo $fetch['id']?>" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="rescheduleModalLabel<?php echo $fetch['id']?>">Re-schedule Reservation</h4>
                    </div>
                    <div class="modal-body">
                        <form action="process_reschedule.php" method="POST">
                            <input type="hidden" name="reschedule_id" value="<?php echo $fetch['id']?>">
                            <div class="form-group">
                                <label for="new_checkin_date<?php echo $fetch['id']?>">Select New Check-in Date:</label>
                                <input type="date" class="form-control" id="new_checkin_date<?php echo $fetch['id']?>" name="new_checkin_date" required>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Re-schedule</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php
            }
        ?>
    </tbody>
</table>

<!-- Bootstrap JS + jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>
