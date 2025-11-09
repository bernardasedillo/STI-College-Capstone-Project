<table id = "table" class = "table table-bordered">
	<thead>
		<tr>
			<th>Name</th>
			<th>Reservation Type</th>
			<th>Reservation Date</th>
			<th>Checkout Date</th>
			<th>Status</th>
			<th>Total Amount</th>
			<th>Billing Status</th>
		</tr>
	</thead>
	<tbody>
		<?php
			require '../includes/connect.php';
			$query = $conn->query("SELECT r.*, r.checkout_date, b.total_amount, b.down_payment, b.balance, b.status AS billing_status FROM `reservations` r LEFT JOIN `billing` b ON r.id = b.reservation_id WHERE r.status = 'checked-out' ORDER BY `created_at` DESC");
			while($fetch = $query->fetch_array()){
		?>
		<tr>
			<td><?php echo $fetch['full_name']?></td>
			<td><?php echo $fetch['reservation_type']?></td>
			<td><strong><?php if($fetch['checkin_date'] <= date("Y-m-d", strtotime("+8 HOURS"))){echo "<label style = 'color:#ff0000;'>".date("M d, Y", strtotime($fetch['checkin_date']))."</label>";}else{echo "<label style = 'color:#00ff00;'>".date("M d, Y", strtotime($fetch['checkin_date']))."</label>";}?></strong></td>
			<td><?php echo date("M d, Y", strtotime($fetch['checkout_date']))?></td>
			<td><?php echo $fetch['status']?></td>
			<td><?php echo $fetch['total_amount'] ? '₱ ' . number_format($fetch['total_amount'], 2) : '' ?></td>
			<td><?php echo $fetch['billing_status']?></td>
		</tr>
		<?php
			}
		?>
	</tbody>
</table>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>