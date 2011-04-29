<?php
/*
*   resolved tickets: #195 - javascript alert/confirm/prompt internationalization.
*/
?>
	<link rel="stylesheet" type="text/css" href="<?php echo site_prefix (); ?>/inc/app/siteinvoice/html/form.css" />

	<script language="javascript">
	<!--

function calcAmt (f, num) {
	qty = new Number (f.elements['qty' + num].value);
	price = new Number (f.elements['price' + num].value);
	f.elements['amt' + num].value = qty * price;
}

function calculate (f) {
	amt01 = new Number (f.elements.amt01.value);
	amt02 = new Number (f.elements.amt02.value);
	amt03 = new Number (f.elements.amt03.value);
	amt04 = new Number (f.elements.amt04.value);
	amt05 = new Number (f.elements.amt05.value);
	amt06 = new Number (f.elements.amt06.value);
	amt07 = new Number (f.elements.amt07.value);
	amt08 = new Number (f.elements.amt08.value);
	amt09 = new Number (f.elements.amt09.value);
	amt10 = new Number (f.elements.amt10.value);

	subtotal = new Number (amt01 + amt02 + amt03 + amt04 + amt05 + amt06 + amt07 + amt08 + amt09 + amt10);
	f.elements.subtotal.value = subtotal;

	gst = new Number (f.elements.subtotal.value * 0.07);
	pst = new Number (f.elements.subtotal.value * 0.07);
	f.elements.gst.value = gst;
	f.elements.pst.value = pst;

	f.elements.total.value = subtotal + gst + pst;

	//return false;
}

function copyDate (f, to) {
	f.elements[to].value = f.elements.date01.value;
}

var client_codes = [];
<?php

$default = false;

foreach (db_pairs ('select id, code from siteinvoice_client order by name asc') as $key => $value) {
	echo 'client_codes[' . $key . '] = \'' . $value . "';\n";
	if (! $default) {
		$default = $value;
	}
}

?>
var client_code = '<?php echo $default; ?>';
var item_no = 0;

function itemNo (item) {
	item_no++;
	if (item_no >= 10) {
		prefix = '0';
	} else {
		prefix = '00';
	}
	item.value = client_code + prefix + item_no;
}

function setClientCode (id) {
	client_code = client_codes[id];
}

	// -->
	</script>

<h1>SiteInvoice - New Invoice</h1>

<form action="<?php echo site_prefix (); ?>/index/siteinvoice-create-action">

<p><strong>Invoice Name</strong> &nbsp; <input type="text" name="name" style="border: 2px inset #ddd; width: 200px" /></p>

<p><strong>Client</strong> &nbsp;
<select name="client" onchange="setClientCode (this.options[this.selectedIndex].value)">
<?php

foreach (db_pairs ('select id, name from siteinvoice_client order by name asc') as $key => $value) {
	echo '<option value="' . $key . '">' . $value . "</option>\n";
}

?>
</select></p>

<p><input type="checkbox" name="send_invoice" value="yes" checked="checked" style="width: 15px" /> Send invoice (uncheck to importing existing invoices).</p>

<table border="0" cellpadding="1" cellspacing="0" width="800">
	<tr>
		<td class="black">
<table cellpadding="0" cellspacing="1" width="100%">
	<tr>
		<th width="10%" class="head">DATE</th>
		<th width="10%" class="head">ITEM NO.</th>
		<th width="50%" class="head">DESCRIPTION</th>
		<th width="10%" class="head">QUANTITY</th>
		<th width="10%" class="head">PRICE</th>
		<th width="10%" class="head">AMOUNT</th>
	</tr>
	<tr>
		<td><input type="text" name="date01" value="<?php echo date ('M d/y'); ?>" /></td>
		<td><input type="text" name="item01" onfocus="itemNo (this)" /></td>
		<td><input type="text" name="desc01" class="desc" /></td>
		<td><input type="text" name="qty01" /></td>
		<td><input type="text" name="price01" /></td>
		<td><input type="text" name="amt01" onfocus="calcAmt (this.form, '01')" /></td>
	</tr>
	<tr>
		<td><input type="text" name="date02" onfocus="copyDate (this.form, 'date02')" /></td>
		<td><input type="text" name="item02" onfocus="itemNo (this)" /></td>
		<td><input type="text" name="desc02" class="desc" /></td>
		<td><input type="text" name="qty02" /></td>
		<td><input type="text" name="price02" /></td>
		<td><input type="text" name="amt02" onfocus="calcAmt (this.form, '02')" /></td>
	</tr>
	<tr>
		<td><input type="text" name="date03" onfocus="copyDate (this.form, 'date03')" /></td>
		<td><input type="text" name="item03" onfocus="itemNo (this)" /></td>
		<td><input type="text" name="desc03" class="desc" /></td>
		<td><input type="text" name="qty03" /></td>
		<td><input type="text" name="price03" /></td>
		<td><input type="text" name="amt03" onfocus="calcAmt (this.form, '03')" /></td>
	</tr>
	<tr>
		<td><input type="text" name="date04" onfocus="copyDate (this.form, 'date04')" /></td>
		<td><input type="text" name="item04" onfocus="itemNo (this)" /></td>
		<td><input type="text" name="desc04" class="desc" /></td>
		<td><input type="text" name="qty04" /></td>
		<td><input type="text" name="price04" /></td>
		<td><input type="text" name="amt04" onfocus="calcAmt (this.form, '04')" /></td>
	</tr>
	<tr>
		<td><input type="text" name="date05" onfocus="copyDate (this.form, 'date05')" /></td>
		<td><input type="text" name="item05" onfocus="itemNo (this)" /></td>
		<td><input type="text" name="desc05" class="desc" /></td>
		<td><input type="text" name="qty05" /></td>
		<td><input type="text" name="price05" /></td>
		<td><input type="text" name="amt05" onfocus="calcAmt (this.form, '05')" /></td>
	</tr>
	<tr>
		<td><input type="text" name="date06" onfocus="copyDate (this.form, 'date06')" /></td>
		<td><input type="text" name="item06" onfocus="itemNo (this)" /></td>
		<td><input type="text" name="desc06" class="desc" /></td>
		<td><input type="text" name="qty06" /></td>
		<td><input type="text" name="price06" /></td>
		<td><input type="text" name="amt06" onfocus="calcAmt (this.form, '06')" /></td>
	</tr>
	<tr>
		<td><input type="text" name="date07" onfocus="copyDate (this.form, 'date07')" /></td>
		<td><input type="text" name="item07" onfocus="itemNo (this)" /></td>
		<td><input type="text" name="desc07" class="desc" /></td>
		<td><input type="text" name="qty07" /></td>
		<td><input type="text" name="price07" /></td>
		<td><input type="text" name="amt07" onfocus="calcAmt (this.form, '07')" /></td>
	</tr>
	<tr>
		<td><input type="text" name="date08" onfocus="copyDate (this.form, 'date08')" /></td>
		<td><input type="text" name="item08" onfocus="itemNo (this)" /></td>
		<td><input type="text" name="desc08" class="desc" /></td>
		<td><input type="text" name="qty08" /></td>
		<td><input type="text" name="price08" /></td>
		<td><input type="text" name="amt08" onfocus="calcAmt (this.form, '08')" /></td>
	</tr>
	<tr>
		<td><input type="text" name="date09" onfocus="copyDate (this.form, 'date09')" /></td>
		<td><input type="text" name="item09" onfocus="itemNo (this)" /></td>
		<td><input type="text" name="desc09" class="desc" /></td>
		<td><input type="text" name="qty09" /></td>
		<td><input type="text" name="price09" /></td>
		<td><input type="text" name="amt09" onfocus="calcAmt (this.form, '09')" /></td>
	</tr>
	<tr>
		<td><input type="text" name="date10" onfocus="copyDate (this.form, 'date10')" /></td>
		<td><input type="text" name="item10" onfocus="itemNo (this)" /></td>
		<td><input type="text" name="desc10" class="desc" /></td>
		<td><input type="text" name="qty10" /></td>
		<td><input type="text" name="price10" /></td>
		<td><input type="text" name="amt10" onfocus="calcAmt (this.form, '10')" /></td>
	</tr>
	<tr>
		<td colspan="5" align="right">Currency</td>
		<td><select name="currency">
<?php

foreach (appconf ('currencies') as $currency) {
	if ($currency == appconf ('default_currency')) {
		$sel = ' selected="selected"';
	} else {
		$sel = '';
	}
	echo TABx3 . sprintf (
		'<option value="%s"%s>%s</option>',
		$currency,
		$sel,
		$currency
	);
}

?>
		</select></td>
	</tr>
<?php

foreach (appconf ('taxes') as $tax => $percent) {

?>
	<tr>
		<th colspan="5" align="right"><?php echo $tax; ?></th>
		<td><select name="<?php echo $tax; ?>">
			<option value="yes">Yes</option>
			<option value="no">No</option>
		</select></td>
	</tr>
<?php

}

?>
</table></td>
	</tr>
</table>

<p align="right">
<input type="submit" value="Create Invoice" class="submit" />
<?php
#Start: SEMIAS. #195 - javascript alert/confirm/prompt internationalization.
?>
<input type="reset" value="Clear Form" class="submit" onclick="return confirm ('<?php echo intl_get('Are you sure?') ?>')" />
</p>
<?php
#END: SEMIAS. 
?>
</form>

