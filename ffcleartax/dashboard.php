<?php
$page_id=400;
$page_slug="dashboard";
$main_page = "home";
$page_hierarchy=array(array("link"=>"dashboard.php","title"=>"Dashboard"));
include("connect.php");

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
        <link href="../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />
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
                           <!-- BEGIN PAGE CONTENT BODY -->
                            <div class="page-content">
                                <div class="container">
                                    <!-- BEGIN PAGE CONTENT INNER -->
                                    <div class="page-content-inner">
                                    <?php include('dashboard_tile.php') ?>
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
	   <!-- BEGIN PAGE LEVEL PLUGINS -->
        
        <script src="../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/counterup/jquery.waypoints.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/counterup/jquery.counterup.min.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL PLUGINS -->
		<!-- BEGIN PAGE LEVEL SCRIPTS -->
		<script src="../assets/pages/scripts/dashboard.min.js" type="text/javascript"></script>
		<!-- END PAGE LEVEL SCRIPTS -->

    </body>