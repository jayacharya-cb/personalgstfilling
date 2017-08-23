<table class="table table-bordered table-hover" id="line_items_table" >
<thead>
	<tr >
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th class="text-center" colspan="2" > IGST</th>
		<th class="text-center" colspan="2"> CESS</th>
		<th></th>
		
	</tr>
	<tr>
		<th> HSN Code</th>
		<th class="item-description"> Description of Services </th>
		<th> Qty </th>
		<th> Rate<br/>(Rs.)</th>
		<th> Total<br/>(Rs.)</th>
		<th> Disc.<br/>(Rs.)</th>
		<th> Taxable<br/> Value </th>
		<th > %</th>
		<th >Amt (Rs.)</th>
		<th > %</th>
		<th >Amt (Rs.)</th>
		
		<th> Net Amt</th>
	</tr>
	
	
</thead>
<tbody class="line_items">
	
	
	<?php 
				$item_stotal=0;
				foreach($invoice_items as $key=>$item)
				{
					$item['item_stotal']=$item['item_qty']*$item['item_unit_price'];
					$item_stotal+=$item['item_stotal'];
				?>
				<tr>
					<td><?php echo $item['item_gst_code'] ?></td>
					<td class="item-description"><p class="m0"><?php echo $item['item_description'] ?></p></td>
					<td class="text-right"><?php echo $item['item_qty'] ?></td>
					<td class="text-right"><?php echo $item['item_unit_price'] ?></td>
					<td class="text-right"><?php echo $item['item_stotal'] ?>  </td>
					<td class="text-right"><?php echo $item['item_discount'] ?>  </td>
					<td class="text-right"><?php echo $item['item_taxable_value'] ?> </td>
					<td class="text-right"> <?php echo $item['item_igst_per'] ?> </td>
					<td class="text-right"><?php echo $item['item_igst_amount'] ?> </td>
					<td class="text-right"> <?php echo $item['item_cess_per'] ?> </td>
					<td class="text-right"><?php echo $item['item_cess_amount'] ?> </td>
					<td class="text-right" ><?php echo $item['item_subtotal'] ?> </td>
					
				</tr>
			  
				<?php }?>
</tbody>
<tfoot >
	<tr>
		<th colspan="1"></th>
		<th class="item-discount">Total</th>
		<th class=""> </th>
		<th class=""> </th>
		<th class=""> <?php echo $item_stotal; ?></th>
		<th class="text-right"> <?php echo $invoice_total_discount; ?></th>
		<th class="total-taxable text-right"> <?php echo $invoice_total_taxable_value; ?></th>
		<th></th>
		<th class="total-cgst text-right">  <?php echo $invoice_total_igst; ?></th>
		<th></th>
		<th class="total-cess text-right"> <?php echo $invoice_total_cess; ?></th>
		<th class="grand-total text-right"> <?php echo $invoice_grand_total; ?></th>
		
	</tr>
</tfoot>
</table>
