<?php
$page_id                     ="514";
$page_title                     ="View Reverse Sales Invoice";
include("connect.php");
include("Numbers_Words_Locale_en_IN.php");
$ctableQuickInvoiceInfo="quick_invoice_info";
$ctableQuickInvoiceItem="quick_invoice_item";
$invoice=array();
$invoice_items=array();
$classname = "Numbers_Words_Locale_en_IN" ;
$obj = new $classname; 

try
{
	if(isset($_REQUEST['iid']) && $_REQUEST['iid']!="")
	{
		$invoice_id=$_REQUEST['iid'];
		$invoice=$db->rp_getData($ctableQuickInvoiceInfo,"*","id='".$invoice_id."'");
		if($invoice)
		{
			$invoice=mysql_fetch_assoc($invoice);			
			$invoice['customer_billing_state']=$db->rp_getValue("state","name","slug='".$invoice['customer_billing_state']."'");
			extract($invoice);
			$invoice_items_r=$db->rp_getData($ctableQuickInvoiceItem,"*","invoice_id='".$invoice_id."'");
			if($invoice_items_r)
			{
					while($item=mysql_fetch_assoc($invoice_items_r))
					{
						$invoice_items[]=$item;
					}
			}
		}
		else
		{
			throw new Exception("No Invoice Found!!");
		}
	}
	else
	{
		throw new Exception("No Invoice Found!!");
	}
}
catch(Exception $e)
{
	$system->addMessage($e->getMessage(),'error');
	$db->rp_location("quick_invoice_manage.php");
}	
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->
	
    <head>
        <meta charset="utf-8" />
        <title>Dashboard | <?php echo SITETITLE; ?></title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="" name="description" />
        <meta content="" name="author" />
		<?php include("include_css.php");?>
       <!-- BEGIN PAGE LEVEL PLUGINS -->  	
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		 <link href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
       <link href="../assets/global/plugins/datatables/plugins/bootstrap/fixedColumns.dataTables.min.css" rel="stylesheet" type="text/css" />
       	<style>
		</style>
		<!-- END PAGE LEVEL STYLES -->
    </head>
    <!-- END HEAD -->

   <body class="page-container-bg-solid">
        <div class="page-wrapper">
			<?php include("top.php");?>
			<!-- BEGIN CONTAINER -->
		   <div class="page-wrapper-row full-height">
                <div class="page-wrapper-middle">
                    <!-- BEGIN CONTAINER -->
                    <div class="page-container">
                        <!-- BEGIN CONTENT -->
                        <div class="page-content-wrapper">
                            <!-- BEGIN CONTENT BODY -->
                            <!-- BEGIN PAGE HEAD-->
                            <div class="page-head">
                                <div class="container-fluid">
                                    <!-- BEGIN PAGE TITLE -->
                                    <div class="page-title">
                                        <h1><a href="<?php echo "quick_invoice_reverse_manage.php" ?>" class="btn btn-default active"><i class="fa  fa-arrow-circle-o-left"></i>&nbsp;Back</a> &nbsp;<?php echo $page_title; ?></h1>
										
                                    </div>
									<a class="btn btn-sm blue hidden-print margin-bottom-5 mt3 pull-right" onclick="javascript:window.print();"> Print
										<i class="fa fa-print"></i>
									</a>
									<a class="btn btn-sm blue hidden-print margin-bottom-5 mt3 pull-right mr1" href="quick_invoice_reverse_crud.php?mode=edit&iid=<?php echo $id; ?>"> Edit
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                    <!-- END PAGE TITLE -->                                   
                                </div>
                            </div>
                            <!-- END PAGE HEAD-->
                            <!-- BEGIN PAGE CONTENT BODY -->
                            <div class="page-content">
                                <div class="container-fluid p0 m0">
                                    <!-- BEGIN PAGE CONTENT INNER -->
                                    <div class="page-content-inner">
									<div class="row">
									<div class="col-sm-12 hidden-print">
										<?php $system->getMessageBlock(); ?>	
									</div>
									</div>
                                    <!-- END PAGE HEADER-->
									<!-- BEGIN PAGE CONTENT INNER -->
                                    <div class="page-content-inner bg-white" style="width:210mm;margin:0 auto;padding:10px;">
                                        <div class="invoice" >
                                            <div class="row invoice-logo">
                                                <div class="col-xs-3 pull-right text-right">
                                                    
													
                                                   
                                                </div>
                                            </div>
											<div class="row">
                                                <div class="col-xs-12 text-center">
                                                    <div class="h2 bold mt0">Tax Invoice</div>
                                                </div>
												<div class="col-xs-12">
												<div class="row">
                                                    <div class="col-xs-12">
														<p class="mt0 mb0"><b>Supplier:&nbsp;<?php echo strtoupper(CLIENT_NAME); ?></b> &nbsp;Address:&nbsp;<?php echo strtoupper(CLIENT_ADDRESS)?> GSTIN No.<?php echo GST_NO?></p>
													</div>
                                                </div>
                                                </div>
												
												<div class="col-xs-12">
												<hr class="mt1 mb1" style="background-color:#000;height:1px"/>
												<div class="row">
                                                    <div class="col-xs-12">
														<p class="mt0 mb0"><b>Recipient:&nbsp;&nbsp;<?php echo strtoupper($invoice['customer_name'])."(".$invoice['customer_gstin'].")"; ?></b>&nbsp;Address:&nbsp; <?php echo $invoice['customer_billing_address']; ?>&nbsp;<?php echo $invoice['customer_billing_city']; ?><?php echo (($invoice['customer_billing_city']!="")?"-":"").$invoice['customer_billing_pincode']; ?><?php echo ",".$invoice['customer_billing_state']; ?> </p>
													</div>
                                                </div>
												<hr class="mt1 mb1 bg-black" style="background-color:#000;height:1px"/>
												<div class="row">
                                                    <div class="col-xs-12">
														<p class="mt0 mb0">Invoice No-Date:<b>&nbsp;&nbsp;<?php echo strtoupper($invoice['invoice_serial_no']); ?>-<?php echo date("d-M-y",strtotime($invoice['invoice_date'])); ?></b></p>
													</div>
                                                </div>
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="row">
                                                <div class="col-xs-12">
												<div class="table-scrollable table-list table-list-view">
													<?php 
													if($invoice['customer_place_of_supply']=="GUJARAT")
													{
														include('quick_invoice_format_intra.php');
													}
													else
													{
														include('quick_invoice_format_inter.php'); 
													}
													?>	
												</div>
												</div>
                              
                                                 </div>
												<div class="row">
												<div class="col-xs-12">
													<div class="row">
													<div class="col-xs-4">
													Total Invoice Value (In Rupees)
													</div>
													<div class="col-xs-2">
													<?php echo $invoice['invoice_grand_total'] ?>
													</div>
													
													</div>
												</div>
												<div class="col-xs-12">
													<div class="row">
													<div class="col-xs-4">
													Total Invoice Value (In Words)
													</div>
													<div class="col-xs-8">
													
													<?php
														try {
															$ret = $obj->toCurrency( round($invoice['invoice_grand_total'],2));
															echo ucwords($ret." Only");
														  } catch (Numbers_Words_Exception $nwe) {
															echo (string)$nwe . "\n";
														  }
													?>
													</div>
													</div>
												</div>
												<div class="col-xs-12 ">
													<div class="row">
													<div class="col-xs-3">
													Name of signtory
													</div>
													<div class="col-xs-3">
													</div>
													<div class="col-xs-3">
													Designation/Status
													</div>
													<div class="col-xs-3">
													</div>
													</div>
												</div>
													
												<div class="col-xs-12 text-center mt4">
													* No Coupans can be redeemed against this line as they already carry a spot discount.<br>
													<hr class="mt1 mb0" style="border:1px dashed #000"/>
												</div>
												</div>
                                            </div>
										</div>
                                    </div>
                                    <!-- END PAGE CONTENT INNER -->
                       
									<!-- END PAGE CONTENT INNER -->
									</div>
                                </div>
                            </div>
                            <!-- END PAGE CONTENT BODY -->
                            <!-- END CONTENT BODY -->
                        </div>
                        <!-- END CONTENT -->
                       <?php include('rightbar.php'); ?>
                    </div>
                    <!-- END CONTAINER -->
                </div>
            </div>
            <div class="page-wrapper-row">
				<?php include('footer.php'); ?>
		    </div>
        </div>
        <!--[if lt IE 9]>
<script src="../assets/global/plugins/respond.min.js"></script>
<script src="../assets/global/plugins/excanvas.min.js"></script> 
<![endif]-->
	  <?php include('include_js.php');?>
<!-- START PAGELEVEL JS -->
  <!-- <script src="js/jquery.autocomplete.js"></script>-->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>
<script type="text/javascript" src="js/jquery.numeric.min.js"></script>
<script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/datatables/plugins/bootstrap/dataTables.fixedColumns.min.js" type="text/javascript"></script>        
<script src="../assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js" type="text/javascript"></script>
        

 <!-- END PAGELEVEL JS -->

</body>
</html>	