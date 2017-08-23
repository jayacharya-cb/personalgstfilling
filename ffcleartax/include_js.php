<!-- BEGIN CORE PLUGINS -->
<script src="../assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/parsley/parsley.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/jquery-form-validator/form-validator/jquery.form-validator.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/moment.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->        

<script type="text/javascript" src="js/friendfill-handy-library.js"></script>
<?php 
// Create JAVASCRIPT Object For User
$my_id=$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'];
$user_detail=$db->getAdminDetail($my_id);
if($user_detail['ack']==1)
{
	$user_detail=$user_detail['result'];
	$stringy[]="'".$user_detail['id']."'";
	$stringy[]="'0'";
	$stringy[]="'".$user_detail['name']."'";
	$stringy[]="'".$user_detail['email']."'";
	$stringy[]="'".$user_detail['image_path']."'";
	$stringy=implode(",",$stringy);	
	
	?>
	<script>
	jQuery(document).ready(function() {    
		var user_obj=User.createUser(<?php echo $stringy; ?>);// init metronic core componets
		User.chat_users=<?php echo json_encode($chat_users);?>;	
		User.chat_rooms=<?php echo json_encode($chat_rooms);?>;	
		console.log(User.chat_rooms);
	});
	</script>
	<?php
}
?>

<!-- BEGIN THEME GLOBAL SCRIPTS -->
<script src="../assets/global/scripts/app.js" type="text/javascript"></script>
<!-- END THEME GLOBAL SCRIPTS -->
<!-- BEGIN THEME LAYOUT SCRIPTS -->
<script src="../assets/layouts/layout3/scripts/layout.min.js" type="text/javascript"></script>
<script src="../assets/layouts/layout3/scripts/demo.min.js" type="text/javascript"></script>
<script src="../assets/layouts/global/scripts/quick-sidebar.js" type="text/javascript"></script>
<!-- END THEME LAYOUT SCRIPTS -->

<script type="text/javascript" src="js/toastr.js"></script>
<script type="text/javascript" src="js/jquery-aj.js"></script>
<script type="text/javascript" src="js/jquery.general.js"></script>
<script type="text/javascript" src="js/jquery.numeric.min.js"></script>

<script>
$(function(){
	$("#s_item_opening_qty").numeric();
    $("#s_item_unit_price").numeric();
	$("#s_item_unit_cost").numeric();
	$("#customer_mobile_no").numeric();
	$("#customer_zipcode").numeric();
	$("#customer_land_line_number").numeric();
	$.validate({
		form : '#customer-save-form', 
		onSuccess : function($form) {
		  saveCustomer();
		  return false; // Will stop the submission of the form
		},		
	 });
	$.validate({
		form : '#item-save-form', 
		onSuccess : function($form) {
		  saveItem();
		  return false; // Will stop the submission of the form
		},		
	 });	
	$("body").on("click",".view-customer-btn",function(){
		var cid=$(this).data("cid");
		viewCustomer(cid);
	})
	$("body").on("click",".edit-customer-btn",function(){
		var cid=$(this).data("cid");
		var callback=$(this).data("callback");
		callback=(callback!=undefined)?callback:"";
		
		$("#submit-customer").data("callback",callback);
		editCustomer(cid);
	})
	$("body").on("click",".delete-customer-btn",function(){
		var cid=$(this).data("cid");
		deleteCustomer(cid);
	})
	$("body").on("click",".customer-add-btn",function(){
		var callback=$(this).data("callback");
		callback=(callback!=undefined)?callback:"";
		$("#submit-customer").data("callback",callback);
		$("#customer-save-form")[0].reset();
		$("#customer_form_id").val(0);
		$("#customer_save_mode").val("add");
		$("#customer-modal").modal('show');		
	})
	
	
	$("body").on("click",".view-item-btn",function(){
		var cid=$(this).data("cid");
		viewItem(cid);
	})
	$("body").on("click",".edit-item-btn",function(){
		var cid=$(this).data("cid");
		var callback=$(this).data("callback");
		callback=(callback!=undefined)?callback:"";
		$("#s_item_opening_qty").attr("disabled","disabled");
		$("#submit-item").data("callback",callback);
		editItem(cid);
	})
	$("body").on("click",".delete-item-btn",function(){
		var cid=$(this).data("cid");
		deleteItem(cid);
	})
	$("body").on("click",".item-add-btn",function(){
		var callback=$(this).data("callback");
		$("#s_item_opening_qty").removeAttr("disabled");
		callback=(callback!=undefined)?callback:"";
		$("#submit-item").data("callback",callback);
		$("#item-save-form")[0].reset();
		$("#item_form_id").val(0);
		$("#item_save_mode").val("add");
		$("#item-modal").modal('show');		
	})
	
	 
})
 function saveItem(){
	 var cid=$("#item_form_id").val();
	 var mode=$("#item_save_mode").val();
	 var description=$("#s_item_description").val();
	 var gst_code=$("#s_item_gst_code").val();
	 var gst_type=$("#s_item_gst_type").val();
	 var item_code=$("#s_item_item_code").val();
	 var unit_price=$("#s_item_unit_price").val();
	 var unit_cost=$("#s_item_unit_cost").val();
	 var opening_qty=$("#s_item_opening_qty").val();
	 var unit_of_measurement=$("#s_item_unit_of_measurement").val();
	 var discount=$("#s_item_discount").val();
	 var notes=$("#s_item_notes").val();
	 var cgst_rate=$("#s_item_item_cgst_rate").val();
	 var sgst_rate=$("#s_item_item_sgst_rate").val();
	 var igst_rate=$("#s_item_item_igst_rate").val();
	 var cess_rate=$("#s_item_item_cess_rate").val();
	 if(description!="" && gst_type!="")
	 {
		 $.ajax({
			 url:"quick_invoice_ajax_function.php",
			 type:"POST",
			 data:{
				 m:"si",
				 description:description,
				 gst_code:gst_code,
				 gst_type:gst_type,
				 item_code:item_code,
				 unit_price:unit_price,
				 unit_cost:unit_cost,
				 opening_qty:opening_qty,
				 unit_of_measurement:unit_of_measurement,
				 discount:discount,
				 notes:notes,
				 cgst_rate:cgst_rate,
				 sgst_rate:sgst_rate,
				 igst_rate:igst_rate,
				 cess_rate:cess_rate,
				 cid:cid,
				 mode:mode
			 },
			 success:function(result){
				result=$.parseJSON(result);
				if(result.a==1)
				{
					toastr.success("Item Saved!!","Great!!");
					$("#item-save-form")[0].reset();					
					$("#item-modal").modal('hide');
					if(TableQuickInvoiceItemDatatablesAjax.init())
					TableQuickInvoiceItemDatatablesAjax.init();
				}
				else
				toastr.error("Item Couldn't Saved!!","Sorry!!"); 	
			 },
			 error:function(){
				 toastr.error("Item Could Not Saved!!","Sorry!!");
			 }
		 })
	 }
	 else
	 {
		 toast.error("Item Saved!!","Great!!");toast.success("Item Saved!!","Great!!");
	 }
	 
	
 } 
 function saveCustomer(){
	 var cid=$("#customer_form_id").val();
	 var mode=$("#customer_save_mode").val();
	 var nick_name=$("#customer_business_name").val();
	 var gstin=$("#customer_gstin").val();
	 var country=$("#customer_country").val();
	 var state=$("#customer_state").val();
	 var city=$("#customer_city").val();
	 var mobile_no=$("#customer_mobile_no").val();
	 var pan_number=$("#customer_pan_number").val();
	 var contact_person=$("#customer_contact_person").val();
	 var address=$("#customer_address").val();
	 var zipcode=$("#customer_zipcode").val();
	 var email=$("#customer_email").val();
	 var land_line_number=$("#customer_land_line_number").val();
	 if(nick_name!="" && state!="")
	 {
		 $.ajax({
			 url:"quick_invoice_ajax_function.php",
			 type:"POST",
			 data:{
				 m:"sc",
				 mode:mode,
				 cid:cid,
				 nick_name:nick_name,
				 gstin:gstin,
				 country:country,
				 state:state,
				 city:city,
				 mobile_no:mobile_no,
				 pan_number:pan_number,
				 address:address,
				 zipcode:zipcode,
				 email:email,
				 contact_person:contact_person,
				 land_line_number:land_line_number,
			 },
			 success:function(result){
				result=$.parseJSON(result);
				if(result.a==1)
				{
					$("#customer-save-form")[0].reset();
					toastr.success("Contact Saved!!","Great!!"); 
					$("#customer-modal").modal('hide');
					if(TableCustomerDatatablesAjax.init()!="")
					{
						TableCustomerDatatablesAjax.init()
					}
				}
				else
				toastr.error("Contact Couldn't Saved!!","Sorry!!"); 	
			 },
			 error:function(){
				 toastr.error("Contact Could Not Saved!!","Sorry!!");
			 }
		 })
	 }
	 else
	 {
		 toastr.error("Contact Could Not Saved!!","Sorry!!");
	 }
	 
	
 }
 function editCustomer(cid){
	 if(cid!="")
	 {
		$.ajax({
		  url: "quick_invoice_ajax_function.php",
		  dataType: "json",
		  type:"POST",
		  data: {
			cid:cid,
			m:"fci"
		  },
		  success: function( data ) {
			  var result=data.result;
			$("#customer-save-form")[0].reset();
			 $("#customer_business_name").val(result.nick_name);
			 $("#customer_gstin").val(result.gstin);
			 $("#customer_country").val(result.country);
			 $("#customer_state").val(result.state);
			 $("#customer_city").val(result.city);
			 $("#customer_contact_person").val(result.contact_person);
			 $("#customer_mobile_no").val(result.mobile_no);
			 $("#customer_pan_number").val(result.pan_number);
			 $("#customer_address").val(result.address);
			 $("#customer_zipcode").val(result.zipcode);
			 $("#customer_email").val(result.email);
			 $("#customer_land_line_number").val(result.land_line_number);
			 $("#customer_form_id").val(cid);
			 $("#customer_save_mode").val("edit");
			$("#customer-modal").modal('show');
		  }
		} );
	 }
	 else
	 {
		 toastr.error("Customer detail not found");
	 }
 }
 function deleteCustomer(cid){
	 if(cid!="")
	 {
		$.ajax({
		  url: "quick_invoice_ajax_function.php",
		  dataType: "json",
		  type:"POST",
		  data: {
			cid:cid,
			m:"dc"
		  },
		  success: function( data ) {
			if(data.a==1)
			{
				TableCustomerDatatablesAjax.init();
				toastr.success("Customer deleted !!");
			}
			else
			{
				toastr.error("Customer could not deleted !!");
			}
		  }
		} );
	 }
	 else
	 {
		 toastr.error("Customer detail not found");
	 }
 }
 function viewCustomer(cid){
	 if(cid!="")
	 {
	 $.ajax({
		  url: "quick_invoice_ajax_function.php",
		  dataType: "json",
		  type:"POST",
		  data: {
			cid:cid,
			m:"fci"
		  },
		  success: function( data ) {
			  var result=data.result;
			 $("#view-customer-form")[0].reset();
			 $("#view_customer_business_name").val(result.nick_name);
			 $("#view_customer_gstin").val(result.gstin);
			 $("#view_customer_country").val(result.country);
			 $("#view_customer_state").val(result.state);
			 $("#view_customer_city").val(result.city);
			 $("#view_customer_contact_person").val(result.contact_person);
			 $("#view_customer_mobile_no").val(result.mobile_no);
			 $("#view_customer_pan_number").val(result.pan_number);
			 $("#view_customer_address").val(result.address);
			 $("#view_customer_zipcode").val(result.zipcode);
			 $("#view_customer_email").val(result.email);
			 $("#view_customer_land_line_number").val(result.land_line_number);
			$("#view-customer-modal").modal('show');
		  }
		} );
	 }
	 else
	 {
		 toastr.error("Customer detail not found");
	 }
 }
function editItem(cid){
	 if(cid!="")
	 {
	 $.ajax({
		  url: "quick_invoice_ajax_function.php",
		  dataType: "json",
		  type:"POST",
		  data: {
			cid:cid,
			m:"fii"
		  },
		  success: function( data ) {
			  var result=data.result;
			 $("#item-save-form")[0].reset();
			 $("#s_item_description").val(result.description);
			 $("#s_item_gst_code").val(result.gst_code);
			 $("#s_item_gst_type").val(result.gst_type);
			 $("#s_item_item_code").val(result.item_code);
			 $("#s_item_unit_price").val(result.unit_price);
			 $("#s_item_unit_cost").val(result.unit_cost);
			 $("#s_item_opening_qty").val(result.opening_qty);
			 $("#s_item_unit_of_measurement").val(result.unit_of_measurement);
			 $("#s_item_discount").val(result.discount);
			 $("#s_item_notes").val(result.notes);
			 $("#s_item_item_cgst_rate").val(result.cgst_rate);
			 $("#s_item_item_sgst_rate").val(result.sgst_rate);
			 $("#s_item_item_igst_rate").val(result.igst_rate);
			 $("#s_item_item_cess_rate").val(result.cess_rate);
			 $("#item_form_id").val(cid);
			 $("#item_save_mode").val("edit");
			 $("#item-modal").modal('show');
		  }
		} );
	 }
	 else
	 {
		 toastr.error("Item detail not found");
	 }
 }

 function viewItem(cid){
	 if(cid!="")
	 {
	 $.ajax({
		  url: "quick_invoice_ajax_function.php",
		  dataType: "json",
		  type:"POST",
		  data: {
			cid:cid,
			m:"fii"
		  },
		  success: function( data ) {
			  var result=data.result;
			 $("#view-item-form")[0].reset();
			 $("#view_item_description").val(result.description);
			 $("#view_item_gst_code").val(result.gst_code);
			 $("#view_item_gst_type").val(result.gst_type);
			 $("#view_item_item_code").val(result.item_code);
			 $("#view_item_unit_price").val(result.unit_price);
			 $("#view_item_unit_cost").val(result.unit_cost);
			 $("#view_item_opening_qty").val(result.opening_qty);
			 $("#view_item_unit_of_measurement").val(result.unit_of_measurement);
			 $("#view_item_discount").val(result.discount);
			 $("#view_item_notes").val(result.notes);
			 $("#view_item_item_cgst_rate").val(result.cgst_rate);
			 $("#view_item_item_sgst_rate").val(result.sgst_rate);
			 $("#view_item_item_igst_rate").val(result.igst_rate);
			 $("#view_item_item_cess_rate").val(result.cess_rate);
			 $("#view-item-modal").modal('show');
		  }
		} );
	 }
	 else
	 {
		 toastr.error("Item detail not found");
	 }
 }
</script>