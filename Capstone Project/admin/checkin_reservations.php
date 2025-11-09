<div class="table-responsive">
<table id="table" class="table table-bordered"> 
	<thead>
		<tr>
			<th>Name</th>
			<th>Contact No</th>
			<th>Reservation Type</th>
			<th>Booking Date</th>
			<th>Check-in Date</th>			
			<th>Status</th>
			<th>Total Amount</th>
			<th>Downpayment</th>
			<th>Balance</th>
			<th>Payment</th> <!-- NEW COLUMN -->
			<th>Billing Status</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		<?php
			require '../includes/connect.php';
			$query = $conn->query("SELECT r.*, 
										 b.total_amount, 
										 b.down_payment, 
										 b.balance, 
										 b.payment_method,   
										 b.status AS billing_status 
								   FROM `reservations` r 
								   LEFT JOIN `billing` b ON r.id = b.reservation_id 
								   WHERE r.status = 'confirmed' 
								   ORDER BY `created_at` DESC");
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
								}else{
									echo "<label style='color:#00ff00;'>".date("M d, Y", strtotime($fetch['checkin_date']))."</label>";
								}
							?>
							</strong>
						</td>
			<!-- Reservation Status -->
			<td>
				<?php 
					echo ($fetch['status'] == 'confirmed') ? 'Confirmed' : ucfirst($fetch['status']); 
				?>
			</td>

			<td><?php echo $fetch['total_amount'] ? '₱ ' . number_format($fetch['total_amount'], 2) : '' ?></td>
			<td><?php echo $fetch['down_payment'] ? '₱ ' . number_format($fetch['down_payment'], 2) : '' ?></td>
			<td><?php echo $fetch['balance'] ? '₱ ' . number_format($fetch['balance'], 2) : '' ?></td>
			
			<!-- Payment Method -->
			<td><?php echo $fetch['payment_method'] ? $fetch['payment_method'] : 'N/A'; ?></td>
			
			<!-- Billing Status -->
			<td>
				<?php 
					if (is_null($fetch['balance'])) {
						echo "Pending"; 
					} elseif ($fetch['balance'] == 0) {
						echo "Paid";
					} elseif ($fetch['balance'] > 0) {
						echo "Partially Paid";
					}
				?>
			</td>
			
	        <td>
				<center>
		            <div class="action-buttons d-flex gap-2">
						<a class="btn btn-primary btn-sm" style="min-width: 100px;" data-toggle="modal" data-target="#checkoutModal<?php echo $fetch['id']?>">
							<i class="glyphicon glyphicon-check"></i> Checkout
						</a>
						<button class="btn btn-info btn-sm" style="min-width: 100px;" data-toggle="modal" data-target="#rescheduleModal<?php echo $fetch['id']?>">
							<i class="glyphicon glyphicon-calendar"></i> Re-schedule
						</button>
					</div>
				</center>
			</td>
		</tr>					<!-- Checkout Modal -->
		<div class="modal fade" id="checkoutModal<?php echo $fetch['id']?>" tabindex="-1" role="dialog" aria-labelledby="checkoutModalLabel<?php echo $fetch['id']?>">
		  <div class="modal-dialog" role="document">
			<div class="modal-content">
			  <div class="modal-header">
				<h4 class="modal-title" id="checkoutModalLabel<?php echo $fetch['id']?>">Checkout for Reservation #<?php echo $fetch['id']?></h4>
			  </div>
			  <div class="modal-body">
				<form method="POST" action="process_checkout.php">
					<input type="hidden" name="reservation_id" value="<?php echo $fetch['id']?>"/>
					<input type="hidden" name="total_amount_hidden" id="total_amount_hidden_<?php echo $fetch['id']?>" value="<?php echo $fetch['total_amount']?>"/>
					<input type="hidden" name="down_payment_hidden" id="down_payment_hidden_<?php echo $fetch['id']?>" value="<?php echo $fetch['down_payment']?>"/>

					<div class="form-group">
						<label>Total Amount</label>
						<input type="text" class="form-control" value="₱ <?php echo number_format($fetch['total_amount'], 2)?>" readonly/>
					</div>
					<div class="form-group">
						<label>Amount Already Paid (Downpayment)</label>
						<input type="text" class="form-control" value="₱ <?php echo number_format($fetch['down_payment'], 2)?>" readonly/>
					</div>
					<div class="form-group">
						<label>Remaining Balance</label>
						<input type="text" class="form-control" id="remaining_balance_<?php echo $fetch['id']?>" value="₱ <?php echo number_format($fetch['balance'], 2)?>" readonly/>
					</div>
					<div class="form-group">
						<label for="amount_paid_at_checkout_<?php echo $fetch['id']?>">Amount Paid at Checkout</label>
						<input type="number" class="form-control amount-paid" data-reservation-id="<?php echo $fetch['id']?>" name="amount_paid_at_checkout" id="amount_paid_at_checkout_<?php echo $fetch['id']?>" step="0.01" value="0" min="0"/>
					</div>
					
					<!-- SELECT PAYMENT METHOD -->
					<div class="form-group">
						<label for="payment_method_<?php echo $fetch['id']?>">Payment Method</label>
						<select class="form-control" name="payment_method" id="payment_method_<?php echo $fetch['id']?>" required>
							<option value="">-- Select Payment Method --</option>
							<option value="Cash">Cash</option>
							<option value="GCash">GCash</option>
							<option value="BDO">BDO</option>
						</select>
					</div>
					
					<div class="form-group">
						<label>New Balance</label>
						<input type="text" class="form-control" id="new_balance_<?php echo $fetch['id']?>" value="₱ <?php echo number_format($fetch['balance'], 2)?>" readonly/>
					</div>
					<button type="submit" class="btn btn-primary">Complete Checkout</button>
				</form>
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
	 <?php } ?>
	</tbody>
</table>
</div>
<!-- jQuery + Bootstrap JS (needed for modal) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius: 10px;">
      <div class="modal-header bg-success text-white">
        <h4 class="modal-title" id="successModalLabel">
          <i class="glyphicon glyphicon-ok-circle"></i> Success
        </h4>
      </div>
      <div class="modal-body text-center">
        <p>Reservation re-scheduled successfully!</p>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
	const urlParams = new URLSearchParams(window.location.search);
	if (urlParams.get('reschedule') === 'success') {
		$('#successModal').modal('show');
		setTimeout(() => {
			$('#successModal').modal('hide');
		}, 2500);
	}
});
</script>
<script>
$(document).ready(function() {
	$(".amount-paid").on("input", function() {
		var reservationId = $(this).data("reservation-id");
		var totalAmount = parseFloat($("#total_amount_hidden_" + reservationId).val());
		var downPayment = parseFloat($("#down_payment_hidden_" + reservationId).val());
		var initialBalance = totalAmount - downPayment;

		var amountPaidAtCheckout = parseFloat($(this).val()) || 0;
		var newBalance = initialBalance - amountPaidAtCheckout;

		$("#new_balance_" + reservationId).val('₱ ' + newBalance.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
	});
});
</script>
