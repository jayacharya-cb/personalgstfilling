<?php
$page_id="507";
include("connect.php");

// PAGE DECLARATION
$main_page 	= "dashboard";
$page 		= "quick_stock_adjustment";
$page_title	= "Item Stock Adjustment";

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
         <link href="../assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
       	
        <!-- END PAGE LEVEL PLUGINS -->
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
                                        <h1><a href="<?php echo "dashboard.php" ?>" class="btn btn-default active"><i class="fa  fa-arrow-circle-o-left"></i>&nbsp;Back</a> &nbsp;<?php echo $page_title; ?></h1>
                                    </div>
                                    <!-- END PAGE TITLE -->                                   
                                </div>
                            </div>
                            <!-- END PAGE HEAD-->
                            <!-- BEGIN PAGE CONTENT BODY -->
                            <div class="page-content">
                                <div class="container-fluid">
                                    <!-- BEGIN PAGE CONTENT INNER -->
                                    <div class="page-content-inner">
									<div class="row">
									<div class="col-sm-12">
										<?php $system->getMessageBlock(); ?>	
									</div>
									</div>
                                    <div class="portlet light portlet-fit bordered">
										<div class="portlet-body">
											
										   <div id="quick_stock_adjustment_container">	
										  </div>
										</div>
									</div>
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
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
		
<script src="js/quick_stock_adjustment_datatable.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->

</body>
</html>	