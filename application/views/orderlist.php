<html>
<head>
</head>
<body>
<table cellpadding="2" cellspacing="2" border="1" width="100%">



	<tr>
	<td align="center">
	<h1>Order List</h1>
	 <div style="float:right;"><a href="<?php echo base_url('/index.php/welcome'); ?>">Go to Welcome</a></div>
	</td>
	</tr>
	<?php if(count($orders)>0) {	?>
	<tr>
	<td>
	 
		<table  width="100%">
		<tr>
			<td>
			Order id
			</td>
			<td>
			Customer Name
			</td>
			<td>
			Gateway
			</td>
			<td>
			Order Amount
			</td>
			<td>
			Order Currency
			</td>
			<td>
			Transaction Amount
			</td>
			<td>
			Transaction Currency
			</td>
			<td>
			Order Status
			</td>
			<td>
			Gateway Status
			</td>
			<td>
			Transacion id
			</td>
			<td>
			Failed Reason
			</td>
		</tr>
		
		<?php foreach($orders as $orderval) { ?>
			<tr>
			<td>
			<?php echo $orderval['orderid']; ?>
			</td>
			<td>
			<?php echo $orderval['customername']; ?>
			</td>
			<td>
			<?php echo $orderval['payment_type']; ?>
			</td>
			<td>
			<?php echo $orderval['price']; ?>
			</td>
			<td>
			<?php echo $orderval['currency']; ?>
			</td>
			<td>
			<?php echo $orderval['amount']; ?>
			</td>
			<td>
			<?php echo $orderval['trans_currency']; ?>
			</td>
			<td>
			<?php echo $orderval['order_status']; ?>
			</td>
			<td>
			<?php echo $orderval['trans_status']; ?>
			</td>
			<td>
			<?php echo $orderval['tans_id']; ?>
			</td>
			<td>
			<?php echo $orderval['fail_reason']; ?>
			</td>
			</tr>
		<?php } ?>
			
		</table>
			
	</td>
	</tr>
	<tr>
	<td>
	<?php echo $links; ?>
	</td>
	</tr>
	<?php }else{ ?>
	<tr>
	<td>
	No Records Found
	</td>
	</tr>
	<?php } ?>
	</table>
	</body>
	 </html>          