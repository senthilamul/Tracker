<?php 
include('includes/config.php');
include('includes/session_check.php');
if($_POST['reporttype'] || $_POST['selectrange']){
    $reporttype     =   $_POST['reporttype'];
    $selectrange    =   $_POST['selectrange'];
}else{
    $crentweek = $commonobj->getQry('SELECT calendar_week from aruba_csat order by id desc limit 0,1');
    $selectrange    =   !empty($selectrange)?$selectrange:$crentweek[0]['calendar_week'] ;
    $reporttype     =   !empty($reporttype)?$reporttype:"calendar_week";
}
if($selectrange != ''){

}
include("includes/header.php");
?>          
<style>
    .form-control[disabled], .form-control[readonly] {
        color: #0a0000;
    }
    .form-horizontal .form-group {
        margin-right: 0px;
        margin-left: 0px;
    }
</style>

        <!-- PAGE CONTENT WRAPPER -->
<div class="page-content-wrap">
<div id="wait" style="display:none;width:49px;height:69px;position:absolute;top:30%;left:50%;padding:2px;z-index: 99999999"><img src='img/demo_wait.gif' width="64" height="64" /></div>
    <div class="row">
        <div class="col-md-12">
            <form class="form-horizontal" method="POST" id='add_form'>
                <div class="page-title">                    
                    <!-- <h3><span class="fa fa-bar-chart-o"></span> CSAT </h3> -->
                    <div class="row">
                        <div class="form-group col-md-6"></div>
                        <div class="form-group col-md-3 col-sm-6 col-xs-6">
                            Report Type
                            <select class="form-control" name= 'reporttype' id='reporttype'>
                                <option value=''>-- Select -- </option>
                                <option value='calendar_week'>Weekly</option>
                                <option value='calendar_month'>Monthly</option>
                                <option value='calendar_quarter'>Quarterly</option>
                            </select>
                            <script>$('#reporttype').val("<?php echo $reporttype?>")</script>  
                        </div>
                        <div class="form-group col-md-3 col-sm-6 col-xs-6">
                            Break Up
                            <select class="form-control" name='selectrange' id='selectrange'>
                                <option value="">-- Select -- </option>
                                <?php 
                                echo "SELECT $reporttype from aruba_csat ";
                                $currentArr = $commonobj->arrayColumn($returnArr = $commonobj->getQry("SELECT distinct $reporttype from aruba_csat order by id desc"),'',$reporttype);
                                foreach ($currentArr as $key => $value) { 
                                 echo "<option value='$value'>$value</option>";
                                }  ?>
                            </select>
                            <script>$('#selectrange').val('')</script>
                        </div>    

                    </div>
                </div>
                <div class="panel panel-warning">
                    <div class="panel-heading ui-draggable-handle">
                        <h3 class="panel-title">Panel control classes</h3>
                        <ul class="panel-controls">
                            <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                            <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
                            <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                            <li><a href="#" class="panel-remove"><span class="fa fa-times"></span></a></li>
                        </ul>                                
                    </div>
                    <div class="panel-body">                    
                        <div class="row">
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <div id="container"></div>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div id="container1"></div>
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <div id="container2"></div>
                            </div>
                        </div>
                    </div>      
                    <div class="panel-footer">                                
                        <button class="btn btn-primary pull-right">Button</button>
                    </div>                            
                </div>
            
            </form>
            
        </div>
    </div>                

</div>
<link rel="stylesheet" href="css/jquery-confirm.min.css">
<?php include("includes/footer.php"); ?>
<script src="js/jquery-confirm.min.js" type="text/javascript"></script>
<!-- <script src="assets/highcharts/highcharts.js" type="text/javascript"></script>
<script src="assets/highcharts/exporting.js" type="text/javascript"></script>
<script src="assets/highcharts/data.js" type="text/javascript"></script>
<script src="assets/highcharts/drilldown.js" type="text/javascript"></script> -->

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/drilldown.js"></script>
<script src="assets/highcharts/exporting.js" type="text/javascript"></script>
<script src="dropdown_ajax.js" type="text/javascript"></script>
