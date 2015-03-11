<html>
<head>
</head>
<body>
<table cellpadding="2" cellspacing="2" border="1" width="100%">



	<tr>
	<td align="center">
	<h1>Order Form</h1>
	 <div style="float:right"><a href="<?php echo base_url('index.php/welcome'); ?>">Go to Welcome</a></h1>
	</td>
	</tr>
	<tr>
	<td>
	 
	
<?php echo validation_errors(); 
?>
   <?php echo form_open('order'); ?>
	 


   <table  align="center" class="form_table" width="100%">

	
	   <tr>

		<td colspan="2"> 
		Order Details
		</td>

	</tr>


   <tr>

		<td width="50%" align="right"> <label for="companyname" style="padding-left:5px;"> Price</label> </td>
		<td width="50%" align="left">

		  <input type="text" name="price" id="price"   value="<?php echo set_value('price',''); ?>">

		</td>

	</tr>

	

    <tr><td width="100%" colspan="2"></td></tr>

               <tr>
                <td align="right"><label for="currency" style="padding-left:5px;">Currency</label></td>
                <td>
			
				<select name="currency" id="currency">
				<option value="">Select Currency</option>
				<option value="USD" <?php echo set_select('currency','USD');  ?>>USD</option>
				<option value="EUR" <?php echo set_select('currency','EUR');  ?>>EUR</option>
				<option value="THB" <?php echo set_select('currency','THB');  ?>>THB</option>
				<option value="HKD" <?php echo set_select('currency','HKD');  ?>>HKD</option>
				<option value="SGD" <?php echo set_select('currency','SGD');  ?>>SGD</option>
				<option value="AUD" <?php echo set_select('currency','AUD');  ?>>AUD</option>
				
					
				</select>
				</td>
            </tr>

   
   <tr>

		<td align="right"> <label for="customername"> Customer Name</label> </td><td>

		  <input type="text" name="customername" id="customername"   value="<?php echo set_value('customername',''); ?>">

		</td>

	</tr>

	 <tr>

		<td colspan="2"> 
		Payment Details
		</td>

	</tr>
	

	<tr>
		<td align="right"> <label for="ccholdername">Credit card holder name</label> </td><td>

		  <input type="text" name="ccholdername" id="ccholdername"   value="<?php echo set_value('ccholdername',''); ?>">

		</td>

	</tr>

	
	<tr>
		<td align="right"> <label for="ccnumber">Credit card number</label> </td><td>

		  <input type="text" name="ccnumber" id="ccnumber"   value="<?php echo set_value('ccnumber',''); ?>">

		</td>

	</tr>
   
		<tr>
		<td align="right"> <label for="ccexpiration">Credit card expiration</label> </td><td>
			
				<select name="ccexpmonth" id="ccexpmonth">
				<option value="">Select Month</option>
				<?php foreach($this->config->item('ccmonth') as $ccmkey=>$ccmval) { ?> 
				<option value="<?php echo $ccmkey; ?>" <?php echo set_select('ccexpmonth',$ccmkey); ?>>
				<?php echo $ccmval; ?>
				</option>
				<?php } ?>
				</select>

				<select name="ccexpyear" id="ccexpyear">
				<option value="">Select Year</option>
				<?php for($expyear=date('Y');$expyear<=$this->config->item('ccendyear');$expyear++) { ?> 
				<option value="<?php echo $expyear; ?>" <?php echo set_select('ccexpyear',$expyear); ?>>
				<?php echo $expyear; ?>
				</option>
				<?php } ?>
				</select>
		</td>

	</tr>
	
		<tr>
		<td align="right"> <label for="ccnumber">CVV</label> </td><td>

		  <input type="text" name="cvvnumber" id="cvvnumber"   value="<?php echo set_value('cvvnumber',''); ?>">

		</td>

	</tr>
	
	 
     <tr> <td colspan="2" align="center"> <div class="button">

			<span><input type="submit" name="submit" id="submit" value="Add"/></span>

			<span><input type="reset" value="Reset"/></span>

			 </tr>

	</table>
			
	</form>		
			
</td>
</tr>
</table>
          
</body>
</html>		  