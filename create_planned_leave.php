<?php 
set_time_limit(0);
include "includes/config.php";
//include 'includes/session_check.php';
extract($_POST);
//print_r($_POST);
if($_POST['enggname'] || $_POST['tlname'] || $_POST['manager'] || $_POST['from_date'] || $_POST['to_date']){
	$from_date    	=	date('Y-m-d',strtotime($_POST['from_date']));
	$to_date    	=	date('Y-m-d',strtotime($_POST['to_date']));
	$manager    	=	$_POST['manager'];
	$tlname   		=	$_POST['tlname'];
	$enggname   	=	$_POST['enggname'];
}else{
	$from_date    	=	date('Y-m-d');
	$to_date    	=	date('Y-m-d');
	$manager    	=	empty($manager)?"Overall":$manager;
	$tlname    		=	empty($tlname)?"Overall":$tlname;
	$enggname    	=	empty($enggname)?"Overall":$enggname;
} 
$QryCondition.= $manager!='Overall'?" and mana='".$manager."'":'';	
$QryCondition.= $tlname!='Overall'?" and team='".$tlname."'":'';	
$QryCondition.= $enggname!='Overall'?" and case_owner='".$enggname."'":'';

$sel_clwhole_data = $commonobj->getQry("select * from planned_leaves order by id asc");
	
if($_POST['create_leave'] != '')		
{
	$from_date1 = date('n/j/Y',strtotime($from_date));
	$to_date1 = date('n/j/Y',strtotime($to_date));
	/* $rowexist = $commonobj->getQry("SELECT * FROM aruba_avaya_raw WHERE case_owner = '$enggname'");
	$emp_no = $rowexist[0]['emp_no']; */
	$sel_last_data = $commonobj->getQry("select * from aruba_avaya_raw where case_owner = '$enggname' order by id desc limit 0,1");
	$sellArr = $sel_last_data[0];
	$email = $_SESSION['email'];
	$datetime = date("Y-m-d H:i:s");
	$insert_planned_leaves = $commonobj->getQry("INSERT INTO `planned_leaves`(`emp_no`, `emp_name`, `from_date`, `to_date`, `created_by`, `created_at`) VALUES ('".$sellArr['emp_no']."','$enggname','$from_date1','$to_date1','$email','$datetime')");
	$dates = range(strtotime($from_date1), strtotime($to_date1),86400);
	foreach($dates as $date){
		$datesArrrr[] = date('n/j/Y',$date);
	}
    
	foreach($datesArrrr as $date){
		$week_num = date("W", strtotime($date));
		$year = date("Y", strtotime($date));
		$month = date("m", strtotime($date));
		$week_format = $year."Week".$week_num;
		
		$month_first_date = date("1-M-y",strtotime($date));
		
		$sel_date_lookup1 = $commonobj->getQry("select * from aruba_date_lookup where calendar_week = '$week_format'");
		$sdlkupArr1 = $sel_date_lookup1[0];
		$sel_date_lookup2 = $commonobj->getQry("select * from aruba_date_lookup where date_monthly = '$month_first_date'");
		$sdlkupArr2 = $sel_date_lookup2[0];
		
		$insert_into_avaya = $commonobj->getQry("INSERT INTO `aruba_avaya_raw`(`calendar_year`, `fiscal_year`, `fiscal_quarter`, `calendar_quarter`, `calendar_month`, `fiscal_month`, `calendar_week`, `fiscal_week`, `date`, `merge`, `wlan_ns`, `product_group`, `que_new`, `case_owner`, `team`, `manager_name`, `emp_no`, `emp_name`, `manager`, `day`, `total_time`, `login_date`, `login_id`, `name`, `login_ext`, `skill`, `login_time`, `logout_time`, `logout_date`, `attendance_satus`, `attendance_comment`, `delay_reason`, `updated_by`, `updated_time`) VALUES ('".$sdlkupArr2['calendar_year']."','".$sdlkupArr2['fiscal_year']."',	'".$sdlkupArr2['fiscal_quarter']."','".$sdlkupArr2['calendar_quarter']."','".$sdlkupArr2['calendar_month']."','".$sdlkupArr2['fiscal_month']."','".$sdlkupArr1['calendar_week']."','".$sdlkupArr1['fiscal_week']."','".$date."','".$sellArr['merge']."','".$sellArr['wlan_ns']."','".$sellArr['product_group']."','".$sellArr['que_new']."','".$enggname."','".$tlname."','".$manager."','".$sellArr['emp_no']."','".$sellArr['emp_name']."','".$sellArr['manager']."',	'".$sellArr['day']."','00:00','".$date."','".$sellArr['login_id']."','".$sellArr['name']."','".$sellArr['login_ext']."','".$sellArr['skill']."','','','".$date."','OFF','','','".$email."','".$datetime."')");
	}
}
?>
<?php include("includes/header.php"); ?>   
<script type="text/javascript" src="js/plugins/jquery/jquery.min.js"></script>

<style>
.btn{
	padding: 1px 5px;
}
.dropdown-menu
{
	z-index:10000000;
}
td,tr{
	font-size:12px;
}
</style>                
<form method="POST" id="frmsrch">               
                <!-- START BREADCRUMB -->
                <ul class="breadcrumb">
                    <li><a href="outage_tracker.php">Home</a></li>
                    <li><a href="create_planned_leave.php">Create Planned Leave</a></li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE TITLE -->
                
                <!-- END PAGE TITLE -->                
                
                <!-- PAGE CONTENT WRAPPER -->
                <div class="page-content-wrap">
                
                    
                    
                    <div class="row">
                        <div class="col-md-12">
                            <form action="create_planned_leave.php" method="POST">
                            <!-- START DATATABLE EXPORT -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Create Planned Leave</h3>
                                    <!--<div class="btn-group pull-right">
                                        <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
                                        <ul class="dropdown-menu">
                                            <li><a href="#" onClick ="$('#customers2').tableExport({type:'excel',escape:'false'});"><img src='img/icons/xls.png' width="24"/> XLS</a></li>
                                        </ul>
                                    </div>-->                                 
                                    
                                </div>
                                <div class="panel-body">
                                    	<div class="form-group col-sm-3">
											<select class="form-control" id="manager"  name="manager" onchange="reload()">
												<option value="Overall">Overall Manager</option>';
												<?php
													//$Qry.= $projectwise == 'Overall' ? '':" and wlan_ns = '$projectwise'";
													//$Qry.= $productwise == 'Overall' ? '':" and que_new = '$productwise'";
													//$Qry.= $productgroup == 'Overall' ? '':" and product_group = '$productgroup'";
													//$Qry.= $regionwise == 'Overall' ? '':" and region = '$regionwise'";
													$managerList = $commonobj->getQry("SELECT distinct manager_name from aruba_avaya_raw order by manager_name asc");
													foreach ($managerList as $key => $value) {
														echo'<option value="'.$value['manager_name'].'">'.$value['manager_name'].'</option>';
													}
												?> 
											</select>
											<script> 
												 jQuery("#manager").val("<?php echo $manager ?>");
											</script>
										</div>
										<div class="form-group col-sm-3">
											<select class="form-control" id="tlname"  name="tlname" onchange="reload()">
												<option value="Overall">Overall TL</option>
												<?php
													$tlQry = '1';
													$tlQry .= ($manager == 'Overall')? '' : " and manager_name ='$manager'";
													$tllist = $commonobj->getQry("SELECT distinct team from aruba_avaya_raw where $tlQry  order by team asc");
													foreach ($tllist as $key => $value) {
														echo'<option value="'.$value['team'].'">'.$value['team'].'</option>';
													}
												?> 
											</select>
											<script> 
												 jQuery("#tlname").val("<?php echo $tlname ?>");
											</script>
										</div>
										<div class="form-group col-sm-3">
										<?php
										$engQry = '1';
													if ( $manager != 'Overall' && $tlname == 'Overall') {
														$engQry .= " and mana = '$manager' ";
													} else if($manager != 'Overall' && $tlname != 'Overall') {
														$engQry .= " and team = '$tlname' ";
													}else if($manager == 'Overall' && $tlname != 'Overall'){
														$engQry .= " and team = '$tlname' ";
													}
													//echo "SELECT distinct case_owner from aruba_avaya_raw where $engQry $Qry order by case_owner asc";
													$tllist = $commonobj->getQry("SELECT distinct case_owner from aruba_avaya_raw where $engQry $Qry order by case_owner asc");
													?>
											<select class="form-control" id="enggname"  name="enggname" onchange="reload()">
												<option value="Overall">Overall Engineer</option>
												<?php
													
													foreach ($tllist as $key => $value) {
														echo "<option value='".$value['case_owner']."'>".$value['case_owner']."</option>";
													}
												?> 
											</select>
											<script> 
												 jQuery("#enggname").val("<?php echo $enggname ?>");
											</script>
										</div>
										<div class="col-md-4">
											<div class="form-group">                                        
												<label class="col-md-3 control-label">From</label>
												<div class="col-md-9">
													<div class="input-group">
														<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
														<input type="text" class="form-control datepicker" name="from_date" id="from_date" value="<?php echo $from_date; ?>">                                            
													</div>
												</div>
												
											</div>
										</div>
										<script> 
											jQuery("#from_date").val("<?php echo $from_date ?>");
										</script>
										<div class="col-md-4">
											<div class="form-group">                                        
												<label class="col-md-3 control-label">To</label>
												<div class="col-md-9">
													<div class="input-group">
														<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
														<input type="text" class="form-control datepicker" name="to_date" id="to_date" value="<?php echo $to_date; ?>">                                            
													</div>
												</div>
												
											</div>
										</div>
										<script> 
											jQuery("#to_date").val("<?php echo $to_date ?>");
										</script>
										<button type="submit" value="Submit" name="create_leave" class="btn btn-primary pull-right">Submit</button>
									 
								</div>
							</div>
							</form>
                        </div>
                        <!-- END DATATABLE EXPORT -->                            
                    </div>
					
					<div class="row">
                        <div class="col-md-12">
                            <!-- START DATATABLE EXPORT -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Created Planned Leaves</h3>
                                    <!--<div class="btn-group pull-right">
                                        <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
                                        <ul class="dropdown-menu">
                                            <li><a href="#" onClick ="$('#customers2').tableExport({type:'excel',escape:'false'});"><img src='img/icons/xls.png' width="24"/> XLS</a></li>
                                        </ul>
                                    </div>-->                                 
                                    
                                </div>
                                <div class="panel-body">
                                   	<div class="form-group col-md-12">
										<table class="table dataTable">
										<thead>
											<tr>
												<th>Emp No.</th>
												<th>Emp Name</th>
												<th>From Date</th>
												<th>To Date</th>
												<th>Created By</th>
												<th>Created At</th>
											</tr>
										</thead>
										<tbody>
											<?php
											foreach($sel_clwhole_data as $cl){
												echo "<tr><td>".$cl['emp_no']."</td>";
												echo "<td>".$cl['emp_name']."</td>";
												echo "<td>".$cl['from_date']."</td>";
												echo "<td>".$cl['to_date']."</td>";
												echo "<td>".$cl['created_by']."</td>";
												echo "<td>".$cl['created_at']."</td></tr>";
											}
											?>
										</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
                        <!-- END DATATABLE EXPORT -->                            
                    </div>
                </div>
			</div>         
                <!-- END PAGE CONTENT WRAPPER -->
            </div>            
            <!-- END PAGE CONTENT -->
        </div>
        <!-- END PAGE CONTAINER -->   
	</form> 
		
        <?php include("includes/footer.php"); ?>
		
		<script type="text/javascript" src="js/plugins/datatables/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="js/plugins/tableexport/tableExport.js"></script>
		<script type="text/javascript" src="js/plugins/tableexport/jquery.base64.js"></script>
		<script type="text/javascript" src="js/plugins/tableexport/html2canvas.js"></script>
		<script type="text/javascript" src="js/plugins/tableexport/jspdf/libs/sprintf.js"></script>
		<script type="text/javascript" src="js/plugins/tableexport/jspdf/jspdf.js"></script>
		<script type="text/javascript" src="js/plugins/tableexport/jspdf/libs/base64.js"></script> 
		
		<script>
		$('.timepicker').timepicker();
		
		$('.table').dataTable( {
		  "pageLength": 50
		} );
		
		function reload(){
			document.getElementById("frmsrch").action = 'create_planned_leave.php'; 
			document.getElementById("frmsrch").submit();
			return false;
		}
		</script>
		
		
		