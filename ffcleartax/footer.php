<?php 
$states_r=$db->rp_getData("state","*","1=1","");
if($states_r)
while($s=mysql_fetch_assoc($states_r)){$states[]=$s;}
$tax_variants_for_cgst_r=$db->rp_getData("tax_variant","*","variant_for_cgst=1");
$tax_variants_for_sgst_r=$db->rp_getData("tax_variant","*","variant_for_sgst=1");
$tax_variants_for_igst_r=$db->rp_getData("tax_variant","*","variant_for_igst=1");
$tax_variants_for_cess_r=$db->rp_getData("tax_variant","*","variant_for_cess=1");
$tax_variants_for_cgst=array();
$tax_variants_for_sgst=array();
$tax_variants_for_igst=array();
$tax_variants_for_cess=array();
while($tax_variant=mysql_fetch_assoc($tax_variants_for_cgst_r)){$tax_variants_for_cgst[]=$tax_variant;};
while($tax_variant=mysql_fetch_assoc($tax_variants_for_sgst_r)){$tax_variants_for_sgst[]=$tax_variant;};
while($tax_variant=mysql_fetch_assoc($tax_variants_for_igst_r)){$tax_variants_for_igst[]=$tax_variant;};
while($tax_variant=mysql_fetch_assoc($tax_variants_for_cess_r)){$tax_variants_for_cess[]=$tax_variant;};
?>
<!-- Comman Modal -->
<div class="modal  fade in" id="item-modal" tabindex="-1" role="item-modal" aria-hidden="true">
<div class="modal-dialog modal-md">
<div class="modal-content">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">Add New Item</h4>
	</div>
	<form role="form" name="item-save-form" id="item-save-form" type="POST" >
	<div class="modal-body modal-md">
		
			<div class="form-body">
				<div class="form-group">
					<label>Item description(required)</label>
					<input autocomplete="off" id="s_item_description" name="description" value="" placeholder="Enter Item Description" class="form-control" type="text" data-validation="required" data-validation-error-msg="Item description required">
				</div>
			</div>
			<div class="row">
			<div class="col-sm-6">
			<div class="form-body">
				<div class="form-group">
					<label>Item type(required)</label>
					<select id="s_item_gst_type" name="s_item_gst_type" class="form-control" data-validation="required" data-validation-error-msg="Item type required"><option value="GOODS">Goods</option><option value="SERVICES">Services</option></select>
				</div>
			</div>
			</div>
			<div class="col-sm-6">
			<div class="form-body">
				<div class="form-group">
					<label>HSN/SAC code(optional)</label>
					<input id="s_item_gst_code" name="gst_code" value="" placeholder="Enter 1 digit to search" class="form-control auto_suggest__input" autocomplete="off" type="search">
				</div>
			</div>
			</div>
			</div>
			<div class="row">
			<div class="col-sm-6">
			<div class="form-body">
				<div class="form-group">
					<label>Item/SKU code(optional)</label>
					<input autocomplete="off" id="s_item_item_code" name="item_code" value="" placeholder="Enter Item Code" class="form-control" type="text">
				</div>
			</div>
			</div>
			<div class="col-sm-6">
			<div class="form-body">
				<div class="form-group">
					<label>Selling price(optional)</label>
					<input autocomplete="off" id="s_item_unit_price" name="unit_price" value="" placeholder="Enter Price" class="form-control" type="text">
				</div>
			</div>
			</div>
			</div>
			<div class="row">
			<div class="col-sm-6">
			
			<div class="form-body">
				<div class="form-group">
					<label>Purchase price(optional)</label>
					<input autocomplete="off" id="s_item_unit_cost" name="unit_cost" value="" placeholder="Enter Price" class="form-control" type="text">
				</div>
			</div>
			</div>
			<div class="col-sm-6">
			<div class="form-body">
				<div class="form-group">
					<label>Unit of measurement(optional)</label>
					<select id="s_item_unit_of_measurement" name="unit_of_measurement" class="form-control"><option value="">None</option><option value="boxes">boxes</option><option value="cm">cm</option><option value="crates">crates</option><option value="cu mtr">cu mtr</option><option value="gm">gm</option><option value="kg">kg</option><option value="ltr">ltr</option><option value="metric ton">metric ton</option><option value="ml">ml</option><option value="mm">mm</option><option value="mtr">mtr</option><option value="pallets">pallets</option><option value="pieces">pieces</option><option value="pkts">pkts</option><option value="sheets">sheets</option><option value="sq.cm">sq.cm</option><option value="sq.m">sq.m</option></select> 
				</div>
			</div>
			</div>
			</div>
			
			<div class="row">
			<div class="col-sm-6">
			<div class="form-body">
				<div class="form-group">
					<label>Discount(%)(optional)</label>
					<input autocomplete="off" id="s_item_discount" name="discount" value="" placeholder="Enter Discount" class="form-control" type="text">
				</div>
			</div>
			</div>
			<div class="col-sm-6">
			<div class="form-body">
				<div class="form-group">
					<label>Opening Qty(required)</label>
					<input autocomplete="off" id="s_item_opening_qty" name="opening_qty" value="" placeholder="Enter Opening" data-validation="required" data-validation-error-msg="Opening Qty Required" class="form-control" type="text">
				</div>
			</div>
			</div>
			
			</div>
			<div class="row">
			<div class="col-sm-12">
			<div class="form-body">
				<div class="form-group">
					<label>Item notes(optional)</label>
					<textarea autocomplete="off" id="s_item_notes" name="notes" placeholder="Enter Note" class="form-control"></textarea>
				</div>
			</div>
			</div>
			</div>
			<div class="row">
			<div class="col-sm-12">
				<span class="h4">Taxs</span>
				<hr/>
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					<label>CGST (required)</label>
					<select id="s_item_item_cgst_rate" name="s_item_item_cgst_rate" class="form-control" data-validation="required" >
						<?php 
						if($tax_variants_for_cgst)
						{
							foreach($tax_variants_for_cgst as $tax_variant)
							{
								?>
								'<option value="<?php echo $tax_variant['variant_value']; ?>"><?php echo $tax_variant['variant_name']; ?></option>'+
								<?php 
							}
						}
						?>
					</select>
			
				</div>
		    </div>
			<div class="col-sm-3">
				<div class="form-group">
					<label>SGST (required)</label>
					<select id="s_item_item_sgst_rate" name="s_item_item_sgst_rate" class="form-control" data-validation="required" >
						<?php 
						if($tax_variants_for_sgst)
						{
							foreach($tax_variants_for_sgst as $tax_variant)
							{
								?>
								'<option value="<?php echo $tax_variant['variant_value']; ?>"><?php echo $tax_variant['variant_name']; ?></option>'+
								<?php 
							}
						}
						?>
					</select>
			
				</div>
		    </div>
			<div class="col-sm-3">
				<div class="form-group">
					<label>IGST (required)</label>
					<select id="s_item_item_igst_rate" name="s_item_item_igst_rate" class="form-control" data-validation="required" >
						<?php 
						if($tax_variants_for_igst)
						{
							foreach($tax_variants_for_igst as $tax_variant)
							{
								?>
								'<option value="<?php echo $tax_variant['variant_value']; ?>"><?php echo $tax_variant['variant_name']; ?></option>'+
								<?php 
							}
						}
						?>
					</select>
			
				</div>
		    </div>
			<div class="col-sm-3">
				<div class="form-group">
					<label>Cess (required)</label>
					<select id="s_item_item_cess_rate" name="s_item_item_cess_rate" class="form-control" data-validation="required" >
						<?php 
						if($tax_variants_for_cess)
						{
							foreach($tax_variants_for_cess as $tax_variant)
							{
								?>
								'<option value="<?php echo $tax_variant['variant_value']; ?>"><?php echo $tax_variant['variant_name']; ?></option>'+
								<?php 
							}
						}
						?>
					</select>
			
				</div>
		    </div>
			</div>
		
	</div>
	<div class="modal-footer">
		<input type="hidden" id="item_form_id" value="" >
		<input type="hidden" id="item_save_mode" value="" >
		
		<button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
		<button type="submit" class="btn green" id="submit-item">Save</button>
	</div>
	</form>
</div>
<!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->
</div><!-- Comman Modal -->
<div class="modal  fade in" id="view-item-modal" tabindex="-1" role="view-item-modal" aria-hidden="true">
<div class="modal-dialog modal-md">
<div class="modal-content">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">View Item</h4>
	</div>
	<form role="form" name="view-item-form" id="view-item-form" >
	<div class="modal-body modal-md">
		
			<div class="form-body">
				<div class="form-group">
					<label>Item description(required)</label>
					<input autocomplete="off" id="view_item_description" name="description" value=""  class="form-control" readonly type="text">
				</div>
			</div>
			<div class="row">
			<div class="col-sm-6">
			<div class="form-body">
				<div class="form-group">
					<label>Item type(required)</label>
					<select id="view_item_gst_type" name="gst_type" class="form-control" readonly ><option value="GOODS">Goods</option><option value="SERVICES">Services</option></select>
				</div>
			</div>
			</div>
			<div class="col-sm-6">
			<div class="form-body">
				<div class="form-group">
					<label>HSN/SAC code(optional)</label>
					<input id="view_item_gst_code" name="gst_code" value="" class="form-control auto_suggest__input" readonly autocomplete="off" type="search">
				</div>
			</div>
			</div>
			</div>
			<div class="row">
			<div class="col-sm-6">
			<div class="form-body">
				<div class="form-group">
					<label>Item/SKU code(optional)</label>
					<input autocomplete="off" id="view_item_item_code" name="item_code" value=""  class="form-control" readonly type="text">
				</div>
			</div>
			</div>
			<div class="col-sm-6">
			<div class="form-body">
				<div class="form-group">
					<label>Selling price(optional)</label>
					<input autocomplete="off" id="view_item_unit_price" name="unit_price" value="" class="form-control" readonly type="text">
				</div>
			</div>
			</div>
			</div>
			<div class="row">
			<div class="col-sm-6">
			
			<div class="form-body">
				<div class="form-group">
					<label>Purchase price(optional)</label>
					<input autocomplete="off" id="view_item_unit_cost" name="unit_cost" value="" class="form-control" readonly type="text">
				</div>
			</div>
			</div>
			<div class="col-sm-6">
			<div class="form-body">
				<div class="form-group">
					<label>Unit of measurement(optional)</label>
					<select id="view_item_unit_of_measurement" name="unit_of_measurement" class="form-control" readonly><option value="">None</option><option value="boxes">boxes</option><option value="cm">cm</option><option value="crates">crates</option><option value="cu mtr">cu mtr</option><option value="gm">gm</option><option value="kg">kg</option><option value="ltr">ltr</option><option value="metric ton">metric ton</option><option value="ml">ml</option><option value="mm">mm</option><option value="mtr">mtr</option><option value="pallets">pallets</option><option value="pieces">pieces</option><option value="pkts">pkts</option><option value="sheets">sheets</option><option value="sq.cm">sq.cm</option><option value="sq.m">sq.m</option></select> 
				</div>
			</div>
			</div>
			</div>
			
			<div class="row">
			<div class="col-sm-6">
			<div class="form-body">
				<div class="form-group">
					<label>Discount(%)(optional)</label>
					<input autocomplete="off" id="view_item_discount" name="discount" value="" class="form-control" readonly type="text">
				</div>
			</div>
			</div>
			<div class="col-sm-6">
			<div class="form-body">
				<div class="form-group">
					<label>Opening Qty(required)</label>
					<input autocomplete="off" id="view_item_opening_qty" name="opening_qty" value="" data-validation="required" data-validation-error-msg="Opening Qty Required" class="form-control" readonly type="text">
				</div>
			</div>
			</div>
			
			</div>
			<div class="row">
			<div class="col-sm-12">
			<div class="form-body">
				<div class="form-group">
					<label>Item notes(optional)</label>
					<textarea autocomplete="off" id="view_item_notes" name="notes" class="form-control" readonly></textarea>
				</div>
			</div>
			</div>
			</div>
			<div class="row">
			<div class="col-sm-12">
				<span class="h4">Taxs</span>
				<hr/>
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					<label>CGST (required)</label>
					<select id="view_item_item_cgst_rate" name="item_cgst_rate" class="form-control" readonly >
						<?php 
						if($tax_variants_for_cgst)
						{
							foreach($tax_variants_for_cgst as $tax_variant)
							{
								?>
								'<option value="<?php echo $tax_variant['variant_value']; ?>"><?php echo $tax_variant['variant_name']; ?></option>'+
								<?php 
							}
						}
						?>
					</select>
			
				</div>
		    </div>
			<div class="col-sm-3">
				<div class="form-group">
					<label>SGST (required)</label>
					<select id="view_item_item_sgst_rate" name="item_sgst_rate" class="form-control" readonly >
						<?php 
						if($tax_variants_for_sgst)
						{
							foreach($tax_variants_for_sgst as $tax_variant)
							{
								?>
								'<option value="<?php echo $tax_variant['variant_value']; ?>"><?php echo $tax_variant['variant_name']; ?></option>'+
								<?php 
							}
						}
						?>
					</select>
			
				</div>
		    </div>
			<div class="col-sm-3">
				<div class="form-group">
					<label>IGST (required)</label>
					<select id="view_item_item_igst_rate" name="item_igst_rate" class="form-control" readonly  >
						<?php 
						if($tax_variants_for_igst)
						{
							foreach($tax_variants_for_igst as $tax_variant)
							{
								?>
								'<option value="<?php echo $tax_variant['variant_value']; ?>"><?php echo $tax_variant['variant_name']; ?></option>'+
								<?php 
							}
						}
						?>
					</select>
			
				</div>
		    </div>
			<div class="col-sm-3">
				<div class="form-group">
					<label>Cess (required)</label>
					<select id="view_item_item_cess_rate" name="item_cess_rate" class="form-control" readonly >
						<?php 
						if($tax_variants_for_cess)
						{
							foreach($tax_variants_for_cess as $tax_variant)
							{
								?>
								'<option value="<?php echo $tax_variant['variant_value']; ?>"><?php echo $tax_variant['variant_name']; ?></option>'+
								<?php 
							}
						}
						?>
					</select>
			
				</div>
		    </div>
			</div>
		
	</div>
	<div class="modal-footer">
		<button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>		
	</div>
	</form>
</div>
<!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->
</div>
<div class="modal  fade in" id="customer-modal" tabindex="-1" role="customer-modal" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
				<h4 class="modal-title">Add Customer Or Vendor</h4>
			</div>
			<form role="form" name="customer-save-form" id="customer-save-form" type="POST" >
			<div class="modal-body modal-md">
				
					<div class="row">
						<div class="col-sm-12">
							<div class="form-body">
								<div class="form-group">
									<label>Customer Or Vendor name (required)</label>
									<input autocomplete="off" id="customer_business_name" name="business_name" value="" placeholder="Enter Customer Or Vendor name" class="form-control" type="text" data-validation="required" data-validation-error-msg="Customer or vendor name required">
								</div>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-body">
								<div class="form-group">
									<label for="gstin" class="">GSTIN &nbsp;<span class="inline_link"><a href="https://services.gst.gov.in/services/track-provisional-id-status" rel="noopener noreferrer" target="_blank">Find your contact's GSTIN</a></span></label>
									<input autocomplete="off" id="customer_gstin" name="gstin" value="" placeholder="Enter GST identification no." class="form-control" type="text">
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-body">
								<div class="form-group">
									<label for="country" class="">Country&nbsp;<span class="input_condition"></span></label><select id="customer_country" name="country" class="form-control"><option value="">Select Country</option><option value="AFGHANISTAN">AFGHANISTAN</option><option value="ALBANIA">ALBANIA</option><option value="ALGERIA">ALGERIA</option><option value="AMERICAN SAMOA">AMERICAN SAMOA</option><option value="ANDORRA">ANDORRA</option><option value="ANGOLA">ANGOLA</option><option value="ANGUILLA">ANGUILLA</option><option value="ANTARCTICA">ANTARCTICA</option><option value="ANTIGUA AND BARBUDA">ANTIGUA AND BARBUDA</option><option value="ARGENTINA">ARGENTINA</option><option value="ARMENIA">ARMENIA</option><option value="ARUBA">ARUBA</option><option value="AUSTRALIA">AUSTRALIA</option><option value="AUSTRIA">AUSTRIA</option><option value="AZERBAIJAN">AZERBAIJAN</option><option value="BAHAMAS">BAHAMAS</option><option value="BAHRAIN">BAHRAIN</option><option value="BANGLADESH">BANGLADESH</option><option value="BARBADOS">BARBADOS</option><option value="BELARUS">BELARUS</option><option value="BELGIUM">BELGIUM</option><option value="BELIZE">BELIZE</option><option value="BENIN">BENIN</option><option value="BERMUDA">BERMUDA</option><option value="BHUTAN">BHUTAN</option><option value="BOLIVIA">BOLIVIA</option><option value="BONAIRE">BONAIRE</option><option value="BOSNIA AND HERZEGOVINA">BOSNIA AND HERZEGOVINA</option><option value="BOTSWANA">BOTSWANA</option><option value="BOUVET ISLAND">BOUVET ISLAND</option><option value="BRAZIL">BRAZIL</option><option value="BRUNEI">BRUNEI</option><option value="BULGARIA">BULGARIA</option><option value="BURKINA FASO">BURKINA FASO</option><option value="BURUNDI">BURUNDI</option><option value="CABO VERDE">CABO VERDE</option><option value="CAMBODIA">CAMBODIA</option><option value="CAMEROON">CAMEROON</option><option value="CANADA">CANADA</option><option value="CENTRAL AFRICAN REPUBLIC">CENTRAL AFRICAN REPUBLIC</option><option value="CHAD">CHAD</option><option value="CHILE">CHILE</option><option value="CHINA">CHINA</option><option value="COLOMBIA">COLOMBIA</option><option value="COMOROS">COMOROS</option><option value="CONGO">CONGO</option><option value="COSTA RICA">COSTA RICA</option><option value="CROATIA">CROATIA</option><option value="CUBA">CUBA</option><option value="CURAÇAO">CURAÇAO</option><option value="CYPRUS">CYPRUS</option><option value="CZECHIA">CZECHIA</option><option value="CÔTE DIVOIRE">CÔTE DIVOIRE</option><option value="DEMOCRATIC REPUBLIC OF THE CONGO">DEMOCRATIC REPUBLIC OF THE CONGO</option><option value="DENMARK">DENMARK</option><option value="DJIBOUTI">DJIBOUTI</option><option value="DOMINICA">DOMINICA</option><option value="DOMINICAN REPUBLIC">DOMINICAN REPUBLIC</option><option value="ECUADOR">ECUADOR</option><option value="EGYPT">EGYPT</option><option value="EL SALVADOR">EL SALVADOR</option><option value="EQUATORIAL GUINEA">EQUATORIAL GUINEA</option><option value="ERITREA">ERITREA</option><option value="ESTONIA">ESTONIA</option><option value="ETHIOPIA">ETHIOPIA</option><option value="FAROE ISLANDS">FAROE ISLANDS</option><option value="FIJI">FIJI</option><option value="FINLAND">FINLAND</option><option value="FRANCE">FRANCE</option><option value="FRENCH GUIANA">FRENCH GUIANA</option><option value="FRENCH POLYNESIA">FRENCH POLYNESIA</option><option value="GABON">GABON</option><option value="GAMBIA">GAMBIA</option><option value="GEORGIA">GEORGIA</option><option value="GERMANY">GERMANY</option><option value="GHANA">GHANA</option><option value="GIBRALTAR">GIBRALTAR</option><option value="GREECE">GREECE</option><option value="GREENLAND">GREENLAND</option><option value="GRENADA">GRENADA</option><option value="GUADELOUPE">GUADELOUPE</option><option value="GUAM">GUAM</option><option value="GUATEMALA">GUATEMALA</option><option value="GUERNSEY">GUERNSEY</option><option value="GUINEA">GUINEA</option><option value="GUINEA-BISSAU">GUINEA-BISSAU</option><option value="GUYANA">GUYANA</option><option value="HAITI">HAITI</option><option value="HOLY SEE">HOLY SEE</option><option value="HONDURAS">HONDURAS</option><option value="HONG KONG">HONG KONG</option><option value="HUNGARY">HUNGARY</option><option value="ICELAND">ICELAND</option><option value="INDIA">INDIA</option><option value="INDONESIA">INDONESIA</option><option value="IRAN">IRAN</option><option value="IRAQ">IRAQ</option><option value="IRELAND">IRELAND</option><option value="ISRAEL">ISRAEL</option><option value="ITALY">ITALY</option><option value="JAMAICA">JAMAICA</option><option value="JAPAN">JAPAN</option><option value="JERSEY">JERSEY</option><option value="JORDAN">JORDAN</option><option value="KAZAKHSTAN">KAZAKHSTAN</option><option value="KENYA">KENYA</option><option value="KIRIBATI">KIRIBATI</option><option value="KUWAIT">KUWAIT</option><option value="KYRGYZSTAN">KYRGYZSTAN</option><option value="LAOS">LAOS</option><option value="LATVIA">LATVIA</option><option value="LEBANON">LEBANON</option><option value="LESOTHO">LESOTHO</option><option value="LIBERIA">LIBERIA</option><option value="LIBYA">LIBYA</option><option value="LIECHTENSTEIN">LIECHTENSTEIN</option><option value="LITHUANIA">LITHUANIA</option><option value="LUXEMBOURG">LUXEMBOURG</option><option value="MACAO">MACAO</option><option value="MACEDONIA">MACEDONIA</option><option value="MADAGASCAR">MADAGASCAR</option><option value="MALAWI">MALAWI</option><option value="MALAYSIA">MALAYSIA</option><option value="MALDIVES">MALDIVES</option><option value="MALI">MALI</option><option value="MALTA">MALTA</option><option value="MARSHALL ISLANDS">MARSHALL ISLANDS</option><option value="MARTINIQUE">MARTINIQUE</option><option value="MAURITANIA">MAURITANIA</option><option value="MAURITIUS">MAURITIUS</option><option value="MAYOTTE">MAYOTTE</option><option value="MEXICO">MEXICO</option><option value="MICRONESIA">MICRONESIA</option><option value="MOLDOVA">MOLDOVA</option><option value="MONACO">MONACO</option><option value="MONGOLIA">MONGOLIA</option><option value="MONTENEGRO">MONTENEGRO</option><option value="MONTSERRAT">MONTSERRAT</option><option value="MOROCCO">MOROCCO</option><option value="MOZAMBIQUE">MOZAMBIQUE</option><option value="MYANMAR">MYANMAR</option><option value="NAMIBIA">NAMIBIA</option><option value="NAURU">NAURU</option><option value="NEPAL">NEPAL</option><option value="NETHERLANDS">NETHERLANDS</option><option value="NEW CALEDONIA">NEW CALEDONIA</option><option value="NEW ZEALAND">NEW ZEALAND</option><option value="NICARAGUA">NICARAGUA</option><option value="NIGER">NIGER</option><option value="NIGERIA">NIGERIA</option><option value="NIUE">NIUE</option><option value="NORFOLK ISLAND">NORFOLK ISLAND</option><option value="NORTH KOREA">NORTH KOREA</option><option value="NORTHERN MARIANA ISLANDS">NORTHERN MARIANA ISLANDS</option><option value="NORWAY">NORWAY</option><option value="OMAN">OMAN</option><option value="PAKISTAN">PAKISTAN</option><option value="PALAU">PALAU</option><option value="PALESTINE">PALESTINE</option><option value="PANAMA">PANAMA</option><option value="PAPUA NEW GUINEA">PAPUA NEW GUINEA</option><option value="PARAGUAY">PARAGUAY</option><option value="PERU">PERU</option><option value="PHILIPPINES">PHILIPPINES</option><option value="POLAND">POLAND</option><option value="PORTUGAL">PORTUGAL</option><option value="PUERTO RICO">PUERTO RICO</option><option value="QATAR">QATAR</option><option value="ROMANIA">ROMANIA</option><option value="RUSSIA">RUSSIA</option><option value="RWANDA">RWANDA</option><option value="RÉUNION">RÉUNION</option><option value="SAINT KITTS AND NEVIS">SAINT KITTS AND NEVIS</option><option value="SAINT LUCIA">SAINT LUCIA</option><option value="SAINT VINCENT AND THE GRENADINES">SAINT VINCENT AND THE GRENADINES</option><option value="SAMOA">SAMOA</option><option value="SAN MARINO">SAN MARINO</option><option value="SAO TOME AND PRINCIPE">SAO TOME AND PRINCIPE</option><option value="SAUDI ARABIA">SAUDI ARABIA</option><option value="SENEGAL">SENEGAL</option><option value="SERBIA">SERBIA</option><option value="SEYCHELLES">SEYCHELLES</option><option value="SIERRA LEONE">SIERRA LEONE</option><option value="SINGAPORE">SINGAPORE</option><option value="SINT MAARTEN">SINT MAARTEN</option><option value="SLOVAKIA">SLOVAKIA</option><option value="SLOVENIA">SLOVENIA</option><option value="SOLOMON ISLANDS">SOLOMON ISLANDS</option><option value="SOMALIA">SOMALIA</option><option value="SOUTH AFRICA">SOUTH AFRICA</option><option value="SOUTH KOREA">SOUTH KOREA</option><option value="SOUTH SUDAN">SOUTH SUDAN</option><option value="SPAIN">SPAIN</option><option value="SRI LANKA">SRI LANKA</option><option value="SUDAN">SUDAN</option><option value="SURINAME">SURINAME</option><option value="SWAZILAND">SWAZILAND</option><option value="SWEDEN">SWEDEN</option><option value="SWITZERLAND">SWITZERLAND</option><option value="SYRIA">SYRIA</option><option value="TAIWAN">TAIWAN</option><option value="TAJIKISTAN">TAJIKISTAN</option><option value="TANZANIA">TANZANIA</option><option value="THAILAND">THAILAND</option><option value="TIMOR-LESTE">TIMOR-LESTE</option><option value="TOGO">TOGO</option><option value="TOKELAU">TOKELAU</option><option value="TONGA">TONGA</option><option value="TRINIDAD AND TOBAGO">TRINIDAD AND TOBAGO</option><option value="TUNISIA">TUNISIA</option><option value="TURKEY">TURKEY</option><option value="TURKMENISTAN">TURKMENISTAN</option><option value="TUVALU">TUVALU</option><option value="UGANDA">UGANDA</option><option value="UKRAINE">UKRAINE</option><option value="UNITED ARAB EMIRATES">UNITED ARAB EMIRATES</option><option value="UNITED KINGDOM">UNITED KINGDOM</option><option value="UNITED STATES OF AMERICA">UNITED STATES OF AMERICA</option><option value="URUGUAY">URUGUAY</option><option value="UZBEKISTAN">UZBEKISTAN</option><option value="VANUATU">VANUATU</option><option value="VENEZUELA">VENEZUELA</option><option value="VIETNAM">VIETNAM</option><option value="YEMEN">YEMEN</option><option value="ZAMBIA">ZAMBIA</option><option value="ZIMBABWE">ZIMBABWE</option><option value="ÅLAND ISLANDS">ÅLAND ISLANDS</option></select>
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-body">
								<div class="form-group">
									<label for="state" class="">State&nbsp;<span class="input_condition">(<!-- /react-text --><!-- react-text: 3383 -->required<!-- /react-text --><!-- react-text: 3384 -->)<!-- /react-text --></span></label>
									<select id="customer_state" name="state" class="form-control" data-validation="required" data-validation-error-msg="State required">
									<option value="">Select State</option>
									<?php 
										if($states)
										{
											foreach($states as $state)
											{
												?>
												<option  value="<?php echo $state['slug'] ?>">
												<?php echo $state['name']; ?>
												</option>
												<?php 
											}
										}
									?>
									</select>
								</div>
							</div>
						</div>	
					</div>	
					<div class="row">
						<div class="col-sm-6">
							<div class="form-body">
								<div class="form-group">
									<label for="customer_contact_person" class=""><!-- react-text: 3427 -->Contact person &nbsp;<!-- /react-text --><span class="input_condition"><!-- react-text: 3429 -->(<!-- /react-text --><!-- react-text: 3430 -->optional<!-- /react-text --><!-- react-text: 3431 -->)<!-- /react-text --></span></label><input autocomplete="off" id="customer_contact_person" name="contact_person_name" value="" placeholder="Enter Name" class="form-control" type="text">
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-body">
								<div class="form-group">
									<label for="mobile_number" class=""><!-- react-text: 3436 -->Mobile no.<!-- /react-text -->&nbsp;<span class="input_condition"><!-- react-text: 3438 -->(<!-- /react-text --><!-- react-text: 3439 -->optional<!-- /react-text --><!-- react-text: 3440 -->)<!-- /react-text --></span></label><input autocomplete="off" id="customer_mobile_no" name="mobile_number" value="" placeholder="Enter Mobile No." class="form-control" type="text">
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						
						<div class="col-sm-6">
							<div class="form-body">
								<div class="form-group">
									<label for="pan_number" class=""><!-- react-text: 3445 -->PAN&nbsp;<!-- /react-text --><span class="input_condition"><!-- react-text: 3447 -->(<!-- /react-text --><!-- react-text: 3448 -->optional<!-- /react-text --><!-- react-text: 3449 -->)<!-- /react-text --></span></label><input autocomplete="off" id="customer_pan_number" name="pan_number" value="" placeholder="Enter PAN no." class="form-control" type="text">
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div class="form-body">
								<div class="form-group">
									<label for="address" class=""><!-- react-text: 3455 -->Address&nbsp;<!-- /react-text --><span class="input_condition"><!-- react-text: 3457 -->(<!-- /react-text --><!-- react-text: 3458 -->optional<!-- /react-text --><!-- react-text: 3459 -->)<!-- /react-text --></span></label><textarea autocomplete="off" id="customer_address" name="address" placeholder="Enter full address" class="form-control"></textarea>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-body">
								<div class="form-group">
									<label for="zip_code" class=""><!-- react-text: 3464 -->Pincode&nbsp;<!-- /react-text --><span class="input_condition"><!-- react-text: 3466 -->(<!-- /react-text --><!-- react-text: 3467 -->optional<!-- /react-text --><!-- react-text: 3468 -->)<!-- /react-text --></span></label><input autocomplete="off" id="customer_zipcode" name="zip_code" value="" placeholder="Enter Pincode" class="form-control" type="text">
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-body">
								<div class="form-group">
									<label for="city" class=""><!-- react-text: 3473 -->City&nbsp;<!-- /react-text --><span class="input_condition"><!-- react-text: 3475 -->(<!-- /react-text --><!-- react-text: 3476 -->optional<!-- /react-text --><!-- react-text: 3477 -->)<!-- /react-text --></span></label><input autocomplete="off" id="customer_city" name="city" value="" placeholder="Enter City" class="form-control" type="text">
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-body">
								<div class="form-group">
									<label for="email" class=""><!-- react-text: 3482 -->Email id&nbsp;<!-- /react-text --><span class="input_condition"><!-- react-text: 3484 -->(<!-- /react-text --><!-- react-text: 3485 -->optional<!-- /react-text --><!-- react-text: 3486 -->)<!-- /react-text --></span></label><input autocomplete="off" id="customer_email" name="email" value="" placeholder="Enter Email Id" class="form-control" type="text">
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-body">
								<div class="form-group">
									<label for="land_line_number" class=""><!-- react-text: 3491 -->Landline no.&nbsp;<!-- /react-text --><span class="input_condition"><!-- react-text: 3493 -->(<!-- /react-text --><!-- react-text: 3494 -->optional<!-- /react-text --><!-- react-text: 3495 -->)<!-- /react-text --></span></label><input autocomplete="off" id="customer_land_line_number" name="land_line_number" value="" placeholder="Enter Landline No." class="form-control" type="text">
								</div>
							</div>
						</div>
					</div>
							
				
			</div>
			<div class="modal-footer">
				<input type="hidden" id="customer_form_id" value="" >
				<input type="hidden" id="customer_save_mode" value="" >
				<button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
				<button type="submit" class="btn green" id="submit-customer" >Save changes</button>
			</div>
			</form>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<div class="modal  fade in" id="view-customer-modal" tabindex="-1" role="view-customer-modal" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
				<h4 class="modal-title">Customer Or Vendor Details</h4>
			</div>
			<div class="modal-body modal-md">
				<form id="view-customer-form">
						<div class="row">
						<div class="col-sm-12">
							<div class="form-body">
								<div class="form-group">
									<label>Customer Or Vendor name</label>
									<input autocomplete="off" readonly id="view_customer_business_name" name="business_name" value="" class="form-control" type="text">
								</div>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-body">
								<div class="form-group">
									<label for="gstin" class="">GSTIN &nbsp;</label>
									<input autocomplete="off" readonly id="view_customer_gstin" name="gstin" value="" class="form-control" type="text">
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-body">
								<div class="form-group">
									<label for="country" autocomplete="off"  class="">Country&nbsp;</label><select id="view_customer_country" readonly name="country" class="form-control"><option value="">Not Selected</option><option value="AFGHANISTAN">AFGHANISTAN</option><option value="ALBANIA">ALBANIA</option><option value="ALGERIA">ALGERIA</option><option value="AMERICAN SAMOA">AMERICAN SAMOA</option><option value="ANDORRA">ANDORRA</option><option value="ANGOLA">ANGOLA</option><option value="ANGUILLA">ANGUILLA</option><option value="ANTARCTICA">ANTARCTICA</option><option value="ANTIGUA AND BARBUDA">ANTIGUA AND BARBUDA</option><option value="ARGENTINA">ARGENTINA</option><option value="ARMENIA">ARMENIA</option><option value="ARUBA">ARUBA</option><option value="AUSTRALIA">AUSTRALIA</option><option value="AUSTRIA">AUSTRIA</option><option value="AZERBAIJAN">AZERBAIJAN</option><option value="BAHAMAS">BAHAMAS</option><option value="BAHRAIN">BAHRAIN</option><option value="BANGLADESH">BANGLADESH</option><option value="BARBADOS">BARBADOS</option><option value="BELARUS">BELARUS</option><option value="BELGIUM">BELGIUM</option><option value="BELIZE">BELIZE</option><option value="BENIN">BENIN</option><option value="BERMUDA">BERMUDA</option><option value="BHUTAN">BHUTAN</option><option value="BOLIVIA">BOLIVIA</option><option value="BONAIRE">BONAIRE</option><option value="BOSNIA AND HERZEGOVINA">BOSNIA AND HERZEGOVINA</option><option value="BOTSWANA">BOTSWANA</option><option value="BOUVET ISLAND">BOUVET ISLAND</option><option value="BRAZIL">BRAZIL</option><option value="BRUNEI">BRUNEI</option><option value="BULGARIA">BULGARIA</option><option value="BURKINA FASO">BURKINA FASO</option><option value="BURUNDI">BURUNDI</option><option value="CABO VERDE">CABO VERDE</option><option value="CAMBODIA">CAMBODIA</option><option value="CAMEROON">CAMEROON</option><option value="CANADA">CANADA</option><option value="CENTRAL AFRICAN REPUBLIC">CENTRAL AFRICAN REPUBLIC</option><option value="CHAD">CHAD</option><option value="CHILE">CHILE</option><option value="CHINA">CHINA</option><option value="COLOMBIA">COLOMBIA</option><option value="COMOROS">COMOROS</option><option value="CONGO">CONGO</option><option value="COSTA RICA">COSTA RICA</option><option value="CROATIA">CROATIA</option><option value="CUBA">CUBA</option><option value="CURAÇAO">CURAÇAO</option><option value="CYPRUS">CYPRUS</option><option value="CZECHIA">CZECHIA</option><option value="CÔTE DIVOIRE">CÔTE DIVOIRE</option><option value="DEMOCRATIC REPUBLIC OF THE CONGO">DEMOCRATIC REPUBLIC OF THE CONGO</option><option value="DENMARK">DENMARK</option><option value="DJIBOUTI">DJIBOUTI</option><option value="DOMINICA">DOMINICA</option><option value="DOMINICAN REPUBLIC">DOMINICAN REPUBLIC</option><option value="ECUADOR">ECUADOR</option><option value="EGYPT">EGYPT</option><option value="EL SALVADOR">EL SALVADOR</option><option value="EQUATORIAL GUINEA">EQUATORIAL GUINEA</option><option value="ERITREA">ERITREA</option><option value="ESTONIA">ESTONIA</option><option value="ETHIOPIA">ETHIOPIA</option><option value="FAROE ISLANDS">FAROE ISLANDS</option><option value="FIJI">FIJI</option><option value="FINLAND">FINLAND</option><option value="FRANCE">FRANCE</option><option value="FRENCH GUIANA">FRENCH GUIANA</option><option value="FRENCH POLYNESIA">FRENCH POLYNESIA</option><option value="GABON">GABON</option><option value="GAMBIA">GAMBIA</option><option value="GEORGIA">GEORGIA</option><option value="GERMANY">GERMANY</option><option value="GHANA">GHANA</option><option value="GIBRALTAR">GIBRALTAR</option><option value="GREECE">GREECE</option><option value="GREENLAND">GREENLAND</option><option value="GRENADA">GRENADA</option><option value="GUADELOUPE">GUADELOUPE</option><option value="GUAM">GUAM</option><option value="GUATEMALA">GUATEMALA</option><option value="GUERNSEY">GUERNSEY</option><option value="GUINEA">GUINEA</option><option value="GUINEA-BISSAU">GUINEA-BISSAU</option><option value="GUYANA">GUYANA</option><option value="HAITI">HAITI</option><option value="HOLY SEE">HOLY SEE</option><option value="HONDURAS">HONDURAS</option><option value="HONG KONG">HONG KONG</option><option value="HUNGARY">HUNGARY</option><option value="ICELAND">ICELAND</option><option value="INDIA">INDIA</option><option value="INDONESIA">INDONESIA</option><option value="IRAN">IRAN</option><option value="IRAQ">IRAQ</option><option value="IRELAND">IRELAND</option><option value="ISRAEL">ISRAEL</option><option value="ITALY">ITALY</option><option value="JAMAICA">JAMAICA</option><option value="JAPAN">JAPAN</option><option value="JERSEY">JERSEY</option><option value="JORDAN">JORDAN</option><option value="KAZAKHSTAN">KAZAKHSTAN</option><option value="KENYA">KENYA</option><option value="KIRIBATI">KIRIBATI</option><option value="KUWAIT">KUWAIT</option><option value="KYRGYZSTAN">KYRGYZSTAN</option><option value="LAOS">LAOS</option><option value="LATVIA">LATVIA</option><option value="LEBANON">LEBANON</option><option value="LESOTHO">LESOTHO</option><option value="LIBERIA">LIBERIA</option><option value="LIBYA">LIBYA</option><option value="LIECHTENSTEIN">LIECHTENSTEIN</option><option value="LITHUANIA">LITHUANIA</option><option value="LUXEMBOURG">LUXEMBOURG</option><option value="MACAO">MACAO</option><option value="MACEDONIA">MACEDONIA</option><option value="MADAGASCAR">MADAGASCAR</option><option value="MALAWI">MALAWI</option><option value="MALAYSIA">MALAYSIA</option><option value="MALDIVES">MALDIVES</option><option value="MALI">MALI</option><option value="MALTA">MALTA</option><option value="MARSHALL ISLANDS">MARSHALL ISLANDS</option><option value="MARTINIQUE">MARTINIQUE</option><option value="MAURITANIA">MAURITANIA</option><option value="MAURITIUS">MAURITIUS</option><option value="MAYOTTE">MAYOTTE</option><option value="MEXICO">MEXICO</option><option value="MICRONESIA">MICRONESIA</option><option value="MOLDOVA">MOLDOVA</option><option value="MONACO">MONACO</option><option value="MONGOLIA">MONGOLIA</option><option value="MONTENEGRO">MONTENEGRO</option><option value="MONTSERRAT">MONTSERRAT</option><option value="MOROCCO">MOROCCO</option><option value="MOZAMBIQUE">MOZAMBIQUE</option><option value="MYANMAR">MYANMAR</option><option value="NAMIBIA">NAMIBIA</option><option value="NAURU">NAURU</option><option value="NEPAL">NEPAL</option><option value="NETHERLANDS">NETHERLANDS</option><option value="NEW CALEDONIA">NEW CALEDONIA</option><option value="NEW ZEALAND">NEW ZEALAND</option><option value="NICARAGUA">NICARAGUA</option><option value="NIGER">NIGER</option><option value="NIGERIA">NIGERIA</option><option value="NIUE">NIUE</option><option value="NORFOLK ISLAND">NORFOLK ISLAND</option><option value="NORTH KOREA">NORTH KOREA</option><option value="NORTHERN MARIANA ISLANDS">NORTHERN MARIANA ISLANDS</option><option value="NORWAY">NORWAY</option><option value="OMAN">OMAN</option><option value="PAKISTAN">PAKISTAN</option><option value="PALAU">PALAU</option><option value="PALESTINE">PALESTINE</option><option value="PANAMA">PANAMA</option><option value="PAPUA NEW GUINEA">PAPUA NEW GUINEA</option><option value="PARAGUAY">PARAGUAY</option><option value="PERU">PERU</option><option value="PHILIPPINES">PHILIPPINES</option><option value="POLAND">POLAND</option><option value="PORTUGAL">PORTUGAL</option><option value="PUERTO RICO">PUERTO RICO</option><option value="QATAR">QATAR</option><option value="ROMANIA">ROMANIA</option><option value="RUSSIA">RUSSIA</option><option value="RWANDA">RWANDA</option><option value="RÉUNION">RÉUNION</option><option value="SAINT KITTS AND NEVIS">SAINT KITTS AND NEVIS</option><option value="SAINT LUCIA">SAINT LUCIA</option><option value="SAINT VINCENT AND THE GRENADINES">SAINT VINCENT AND THE GRENADINES</option><option value="SAMOA">SAMOA</option><option value="SAN MARINO">SAN MARINO</option><option value="SAO TOME AND PRINCIPE">SAO TOME AND PRINCIPE</option><option value="SAUDI ARABIA">SAUDI ARABIA</option><option value="SENEGAL">SENEGAL</option><option value="SERBIA">SERBIA</option><option value="SEYCHELLES">SEYCHELLES</option><option value="SIERRA LEONE">SIERRA LEONE</option><option value="SINGAPORE">SINGAPORE</option><option value="SINT MAARTEN">SINT MAARTEN</option><option value="SLOVAKIA">SLOVAKIA</option><option value="SLOVENIA">SLOVENIA</option><option value="SOLOMON ISLANDS">SOLOMON ISLANDS</option><option value="SOMALIA">SOMALIA</option><option value="SOUTH AFRICA">SOUTH AFRICA</option><option value="SOUTH KOREA">SOUTH KOREA</option><option value="SOUTH SUDAN">SOUTH SUDAN</option><option value="SPAIN">SPAIN</option><option value="SRI LANKA">SRI LANKA</option><option value="SUDAN">SUDAN</option><option value="SURINAME">SURINAME</option><option value="SWAZILAND">SWAZILAND</option><option value="SWEDEN">SWEDEN</option><option value="SWITZERLAND">SWITZERLAND</option><option value="SYRIA">SYRIA</option><option value="TAIWAN">TAIWAN</option><option value="TAJIKISTAN">TAJIKISTAN</option><option value="TANZANIA">TANZANIA</option><option value="THAILAND">THAILAND</option><option value="TIMOR-LESTE">TIMOR-LESTE</option><option value="TOGO">TOGO</option><option value="TOKELAU">TOKELAU</option><option value="TONGA">TONGA</option><option value="TRINIDAD AND TOBAGO">TRINIDAD AND TOBAGO</option><option value="TUNISIA">TUNISIA</option><option value="TURKEY">TURKEY</option><option value="TURKMENISTAN">TURKMENISTAN</option><option value="TUVALU">TUVALU</option><option value="UGANDA">UGANDA</option><option value="UKRAINE">UKRAINE</option><option value="UNITED ARAB EMIRATES">UNITED ARAB EMIRATES</option><option value="UNITED KINGDOM">UNITED KINGDOM</option><option value="UNITED STATES OF AMERICA">UNITED STATES OF AMERICA</option><option value="URUGUAY">URUGUAY</option><option value="UZBEKISTAN">UZBEKISTAN</option><option value="VANUATU">VANUATU</option><option value="VENEZUELA">VENEZUELA</option><option value="VIETNAM">VIETNAM</option><option value="YEMEN">YEMEN</option><option value="ZAMBIA">ZAMBIA</option><option value="ZIMBABWE">ZIMBABWE</option><option value="ÅLAND ISLANDS">ÅLAND ISLANDS</option></select>
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-body">
								<div class="form-group">
									<label for="state" class="">State</label>
									<select id="view_customer_state" readonly autocomplete="off" name="state" class="form-control">
									<option value="">Not Selected</option>
									<?php 
										if($states)
										{
											foreach($states as $state)
											{
												?>
												<option  value="<?php echo $state['slug'] ?>">
												<?php echo $state['name']; ?>
												</option>
												<?php 
											}
										}
									?>
									</select>
								</div>
							</div>
						</div>	
					</div>	
					<div class="row">
						<div class="col-sm-6">
							<div class="form-body">
								<div class="form-group">
									<label for="contact_person_name" class=""><!-- react-text: 3427 -->Contact person &nbsp;<!-- /react-text --></span></label><input autocomplete="off" readonly id="view_customer_contact_person" name="contact_person_name" value="" class="form-control" type="text">
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-body">
								<div class="form-group">
									<label for="mobile_number" class=""><!-- react-text: 3436 -->Mobile no.</label><input autocomplete="off" readonly id="view_customer_mobile_no" name="mobile_number" value="" class="form-control" type="text">
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						
						<div class="col-sm-6">
							<div class="form-body">
								<div class="form-group">
									<label for="pan_number" class=""><!-- react-text: 3445 -->PAN&nbsp;<!-- /react-text --></label><input autocomplete="off" readonly id="view_customer_pan_number" name="pan_number" value=""  class="form-control" type="text">
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div class="form-body">
								<div class="form-group">
									<label for="address" class=""><!-- react-text: 3455 -->Address&nbsp;<!-- /react-text --></label><textarea autocomplete="off" readonly id="view_customer_address" name="address" class="form-control"></textarea>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-body">
								<div class="form-group">
									<label for="zip_code" class=""><!-- react-text: 3464 -->Pincode&nbsp;<!-- /react-text --><span class="input_condition"><!-- react-text: 3466 --></span></label><input autocomplete="off" readonly id="view_customer_zipcode" name="zip_code" value="" class="form-control" type="text">
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-body">
								<div class="form-group">
									<label for="city" class=""><!-- react-text: 3473 -->City&nbsp;<!-- /react-text --></label><input autocomplete="off" readonly id="view_customer_city" name="city" value="" class="form-control" type="text">
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-body">
								<div class="form-group">
									<label for="email" class=""><!-- react-text: 3482 -->Email id&nbsp;<!-- /react-text --></label><input autocomplete="off" readonly id="view_customer_email" name="email" value="" class="form-control" type="text">
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-body">
								<div class="form-group">
									<label for="land_line_number" class=""><!-- react-text: 3491 -->Landline no.&nbsp;<!-- /react-text --></label><input autocomplete="off" readonly id="view_customer_land_line_number" name="land_line_number" value=""  class="form-control" type="text">
								</div>
							</div>
						</div>
					</div>
							
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
			</div>			
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>

<!-- Comman Modal -->

<!-- BEGIN FOOTER -->
<div class="page-footer">
	<div class="page-footer-inner"> <?php echo date("Y");?> &copy; <?php echo SITETITLE; ?> By 	
	<a target="_blank" href="<?php echo SITEORIGINURL;?>"><?php echo SITEORIGIN;?></a>
	</div>
	<div class="scroll-to-top">
		<i class="icon-arrow-up"></i>
	</div>
</div>
<!-- END FOOTER -->