<?php 
set_time_limit(0);
include "includes/config.php";
//include 'includes/session_check.php';
if($_POST['case_owner'] || $_POST['from_date'] || $_POST['to_date']){
	$from_date    	=	date('Y-m-d',strtotime($_POST['from_date']));
	$to_date    	=	date('Y-m-d',strtotime($_POST['to_date']));
	$case_owner   		=	$_POST['case_owner'];
}else{
	$from_date    	=	date('Y-m-d');
	$to_date    	=	date('Y-m-d');
	$case_owner    		=	empty($case_owner)?"Overall":$case_owner;
} 
//print_r($_POST); echo $from_date.$to_date;
		
	$QryCondition.= $case_owner!='Overall'?" and case_owner='".$case_owner."'":'';	
	$dates = range(strtotime($from_date), strtotime($to_date),86400);
    $dateArr = array_map("toDate", $dates);
    function toDate($x){return date('n/j/Y', $x);}
	//TL list
	$cowner_list = $commonobj->arrayColumn($commonobj->getQry("SELECT distinct case_owner from aruba_avaya_raw order by case_owner asc"),'','case_owner');
	array_unshift($cowner_list,"Overall");
	if(!in_array($case_owner,$cowner_list)){
		$case_owner = current($cowner_list);
	}
	if($case_owner == "Overall"){
		$cownerArr = $cowner_list;
		foreach($cowner_list as $csonamesel){
			if($csonamesel != "Overall"){
				//echo "SELECT id,case_owner,login_date,attendance_satus,TIME_TO_SEC(total_time) as total_login_secs,team,login_time,logout_time,total_time FROM aruba_avaya_raw WHERE `login_date` in ('".implode("','", $dateArr)."') AND case_owner = '$csonamesel'";
				$attendancewholeArr[] = $commonobj->getQry("SELECT id,case_owner,login_date,attendance_satus,TIME_TO_SEC(total_time) as total_login_secs,team,login_time,logout_time,total_time FROM aruba_avaya_raw WHERE `login_date` in ('".implode("','", $dateArr)."') AND case_owner = '$csonamesel'");
			}
		}
		foreach($attendancewholeArr as $resArr){
			foreach($resArr as $resinArr){
				//$resArrrr[$resinArr['team']][$resinArr['case_owner']][$resinArr['login_date']][] = $resinArr['total_login_secs'];
				$resArrrr[$resinArr['case_owner']][$resinArr['login_date']]['id'] = $resinArr['id'];
				$resArrrr[$resinArr['case_owner']][$resinArr['login_date']]['case_owner'] = $resinArr['case_owner'];
				$resArrrr[$resinArr['case_owner']][$resinArr['login_date']]['login_date'] = $resinArr['login_date'];
				$resArrrr[$resinArr['case_owner']][$resinArr['login_date']]['total_login_secs'] = $resinArr['total_login_secs'];
				$resArrrr[$resinArr['case_owner']][$resinArr['login_date']]['login_time'] = $resinArr['login_time'];
				$resArrrr[$resinArr['case_owner']][$resinArr['login_date']]['logout_time'] = $resinArr['logout_time'];
				$resArrrr[$resinArr['case_owner']][$resinArr['login_date']]['total_time'] = $resinArr['total_time'];
				$resArrrr[$resinArr['case_owner']][$resinArr['login_date']]['attendance_satus'] = $resinArr['attendance_satus'];
			}
		}
	}else{
		$cownerArr[] = $case_owner;
		//echo "SELECT id,case_owner,login_date,attendance_satus,TIME_TO_SEC(total_time) as total_login_secs,team,login_time,logout_time,total_time FROM aruba_avaya_raw WHERE `login_date` in ('".implode("','", $dateArr)."') AND case_owner = '$case_owner'";
		$attendancewholeArr[] = $commonobj->getQry("SELECT id,case_owner,login_date,attendance_satus,TIME_TO_SEC(total_time) as total_login_secs,team,login_time,logout_time,total_time FROM aruba_avaya_raw WHERE `login_date` in ('".implode("','", $dateArr)."') AND case_owner = '$case_owner'");
		//print_r($attendancewholeArr);
		foreach($attendancewholeArr as $resArr){
			foreach($resArr as $resinArr){
				//$resArrrr[$resinArr['team']][$resinArr['case_owner']][$resinArr['login_date']][] = $resinArr['total_login_secs'];
				$resArrrr[$resinArr['case_owner']][$resinArr['login_date']]['id'] = $resinArr['id'];
				$resArrrr[$resinArr['case_owner']][$resinArr['login_date']]['case_owner'] = $resinArr['case_owner'];
				$resArrrr[$resinArr['case_owner']][$resinArr['login_date']]['login_date'] = $resinArr['login_date'];
				$resArrrr[$resinArr['case_owner']][$resinArr['login_date']]['total_login_secs'] = $resinArr['total_login_secs'];
				$resArrrr[$resinArr['case_owner']][$resinArr['login_date']]['login_time'] = $resinArr['login_time'];
				$resArrrr[$resinArr['case_owner']][$resinArr['login_date']]['logout_time'] = $resinArr['logout_time'];
				$resArrrr[$resinArr['case_owner']][$resinArr['login_date']]['total_time'] = $resinArr['total_time'];
				$resArrrr[$resinArr['case_owner']][$resinArr['login_date']]['attendance_satus'] = $resinArr['attendance_satus'];
			}
		}
	}
	//print_r($resArrrr);
	$headerArr = array("P","O","OD","LOP","SL","T","SE","LIL");
?>
<?php include("includes/header.php"); ?>   
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
                    <li><a href="dashboard.php">Home</a></li>
                    <li><a href="summary.php">Summary</a></li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                    <div class="form-group col-md-3 col-xs-6">
					    <select class="form-control select" id="case_owner" name="case_owner" onchange="reload()">
							<?php
								foreach ($cowner_list as $key => $value) {
									$selected = ($case_owner == $value)?"selected":"";
									echo '<option value="'.$value.'" '.$selected.'>'.$value.'</option>';
								}
							?> 
		                </select>
		                <script> 
							jQuery("#case_owner").val("<?php echo $case_owner ?>");
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
					<button class="btn btn-primary pull-right">Submit</button>
				</div>
                <!-- END PAGE TITLE -->                
                
                <!-- PAGE CONTENT WRAPPER -->
                <div class="page-content-wrap">
                
                    
                    
                    <div class="row">
                        <div class="col-md-12">
                            
                            <!-- START DATATABLE EXPORT -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Engineer Attendance List</h3>
                                    <!--<div class="btn-group pull-right">
                                        <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
                                        <ul class="dropdown-menu">
                                            <li><a href="#" onClick ="$('#customers2').tableExport({type:'excel',escape:'false'});"><img src='img/icons/xls.png' width="24"/> XLS</a></li>
                                        </ul>
                                    </div>-->                                 
                                    
                                </div>
                                <div class="panel-body" class="wrap" style="overflow:scroll;">
                                        <?php 
										//print_r($resArrrr);
										foreach($cownerArr as $case_owner){
											$p_count = 0; $o_count = 0; $od_count = 0; $lop_count = 0; $sl_count = 0; $t_count = 0; $se_count = 0; $lil_count = 0;
											//if($resArrrr[$case_owner] != ""){
												$i = 0;
												foreach($dateArr as $date){
													if($resArrrr[$case_owner][$date] != ""){
														if(round($resArrrr[$case_owner][$date]['total_login_secs']) >= '28800'){
															$csocntArr[$date]['P'][] = "p";
														}else{
															if($resArrrr[$case_owner][$date]['attendance_satus'] != ''){
																if($resArrrr[$case_owner][$date]['attendance_satus'] == "O")
																{
																	$csocntArr[$date]['O'][] = "O";
																}elseif($resArrrr[$case_owner][$date]['attendance_satus'] == "OD")
																{
																	$csocntArr[$date]['OD'][] = "OD";
																}elseif($resArrrr[$case_owner][$date]['attendance_satus'] == "LOP")
																{
																	$csocntArr[$date]['LOP'][] = "LOP";
																}elseif($resArrrr[$case_owner][$date]['attendance_satus'] == "Sick Leave")
																{
																	$csocntArr[$date]['SL'][] = "SL";
																}elseif($resArrrr[$case_owner][$date]['attendance_satus'] == "Training")
																{
																	$csocntArr[$date]['T'][] = "T";
																}elseif($resArrrr[$case_owner][$date]['attendance_satus'] == "System Exit")
																{
																	$csocntArr[$date]['SE'][] = "SE";
																}elseif($resArrrr[$case_owner][$date]['attendance_satus'] == "LIL")
																{
																	$csocntArr[$date]['LIL'][] = "LIL";
																}
															}else{
																//echo "<button type='button' class='btn btn-info' data-toggle='modal' onclick='show_model(this.value)' id='".$valArr1[$date]['id']."' value='".$valArr1[$date]['id'].'-'.$valArr1[$date]['case_owner'].'-'.$valArr1[$date]['login_date'].'-'.$valArr1[$date]['login_time'].'-'.$valArr1[$date]['logout_time'].'-'.$valArr1[$date]['total_time']."' data-target='#myModal'>AB</button>";
																$csocntArr[$date]['LOP'][] = "LOP";
															}
														}
													}else{
														$csocntArr[$date]['LOP'][] = "LOP";
													}
												$i++;
												} 
														
											/* }else{
												$csocntArr[$date]['YTS'][] = "YTS";
											} */
										}
										//print_r($csocntArr);
										?>	
										
									<table class="table datatable">
										<thead>
											<tr>
												<th>Date</th>
												<?php
												foreach($headerArr as $head){
													echo "<th>$head</th>";
												} ?>
											</tr>
										</thead>
									
											<tbody>
											<?php 
											foreach($csocntArr as $key => $resAr){
												echo "<tr><td>$key</td>"; ?>
												<td> <?php echo empty($resAr['P'])?0:count($resAr['P']); ?></td>
												<td> <?php echo empty($resAr['O'])?0:count($resAr['O']); ?></td>
												<td> <?php echo empty($resAr['OD'])?0:count($resAr['OD']); ?></td>
												<td> <?php echo empty($resAr['LOP'])?0:count($resAr['LOP']); ?></td>
												<td> <?php echo empty($resAr['SL'])?0:count($resAr['SL']); ?></td>
												<td> <?php echo empty($resAr['T'])?0:count($resAr['T']); ?></td>
												<td> <?php echo empty($resAr['SE'])?0:count($resAr['SE']); ?></td>
												<td> <?php echo empty($resAr['LIL'])?0:count($resAr['LIL']); ?></td></tr>
											<?php } ?>
											</tbody>
									</table>    
								   </div>
								   <?php //} ?>
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
		<div class="modal fade" id="myModal" role="dialog">
			<div class="modal-dialog">
			
			  <!-- Modal content-->
			  <div class="modal-content">
				
					<div class="modal-header">
					  <button type="button" class="close" data-dismiss="modal">&times;</button>
					  <h4 class="modal-title">Edit Attendance for <span id="emp_name"></span> on <span id="login_date"></span></h4>
					</div>
					<div class="modal-body">
						<input type="hidden" id="emp_id" name="emp_id">
						<!--<input type="hidden" id="having_record" name="having_record">-->
						<div class="form-group">                                        
							<label class="control-label col-md-3">Login Time</label>
							<div class="input-group col-md-3 bootstrap-timepicker">
								<input type="text" class="form-control timepicker" name="login_time" onchange="calculate_hours()" id="login_time">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
							</div>
						</div>
						<div class="form-group">                                        
							<label class="control-label col-md-3">Logout Time</label>
							<div class="input-group col-md-3 bootstrap-timepicker">
								<input type="text" class="form-control timepicker" name="logout_time" onchange="calculate_hours()" id="logout_time">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
							</div>
						</div>
						<div class="form-group">                                        
							<label class="control-label col-md-3">Total Time</label>
							<div class="input-group col-md-3">
								<input type="text" class="form-control" name="total_time" readonly id="total_time">
                            </div>
						</div>
						<div class="form-group">                                        
							<label class="control-label col-md-3">Delay Reason</label>
							<div class="input-group col-md-3">
								<input type="checkbox" name="delay_reason[]" class="delay_reason" value="Cab Delay" id="delay_reason1"> Cab Delay
                            </div>
							<div class="input-group col-md-3">
								<input type="checkbox" name="delay_reason[]" class="delay_reason" value="Permission" id="delay_reason2"> Permission
                            </div>
						</div>
						<div class="form-group">                                        
							<label class="control-label col-md-3">Attendance Type</label>
							<div class="input-group col-md-9">
								<select name="attendance_type" id="attendance_type" class="form-control">
									<option value="P">P</option>
									<option value="O">O</option>
									<option value="OD">OD</option>
									<option value="LOP">LOP</option>
									<option value="Sick Leave">Sick Leave</option>
									<option value="Training">Training</option>
									<option value="System Exit">System Exit</option>
									<option value="LIL">LIL</option>
								</select>
							</div>
						</div>
						<div class="form-group">                                        
							<label class="control-label col-md-3">Comment</label>
							<div class="input-group col-md-9">
								<textarea name="attendance_comment" id="attendance_comment" class="form-control"></textarea>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" id="submit_edit_attedance" class="btn btn-info">Submit</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				
			  </div>
			  
			</div>
		</div>
		
		<div class="modal fade" id="myModal_new" role="dialog">
			<div class="modal-dialog">
			
			  <!-- Modal content-->
			  <div class="modal-content">
				
					<div class="modal-header">
					  <button type="button" class="close" data-dismiss="modal">&times;</button>
					  <h4 class="modal-title">Edit Attendance for <span id="emp_name"></span> on <span id="login_date"></span></h4>
					</div>
					<div class="modal-body">
						<input type="hidden" id="add_emp_name" name="add_emp_name">
						<input type="hidden" id="record_date" name="record_date">
						<div class="form-group">                                        
							<label class="control-label col-md-3">Login Date</label>
							<div class="input-group col-md-3">
								<input type="text" class="form-control datepicker" name="add_login_date" id="add_login_date">
								<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                            </div>
						</div>
						<div class="form-group">                                        
							<label class="control-label col-md-3">Logout Date</label>
							<div class="input-group col-md-3">
								<input type="text" class="form-control datepicker" name="add_logout_date" id="add_logout_date">
								<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                            </div>
						</div>
						<div class="form-group">                                        
							<label class="control-label col-md-3">Login Time</label>
							<div class="input-group col-md-3 bootstrap-timepicker">
								<input type="text" class="form-control timepicker" name="add_login_time" onchange="add_calculate_hours()" id="add_login_time">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
							</div>
						</div>
						<div class="form-group">                                        
							<label class="control-label col-md-3">Logout Time</label>
							<div class="input-group col-md-3 bootstrap-timepicker">
								<input type="text" class="form-control timepicker" name="add_logout_time" onchange="add_calculate_hours()" id="add_logout_time">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
							</div>
						</div>
						<div class="form-group">                                        
							<label class="control-label col-md-3">Total Time</label>
							<div class="input-group col-md-3">
								<input type="text" class="form-control" name="add_total_time" readonly id="add_total_time">
                            </div>
						</div>
						<div class="form-group">                                        
							<label class="control-label col-md-3">Delay Reason</label>
							<div class="input-group col-md-3">
								<input type="checkbox" name="delay_reason[]" class="delay_reason" id="Cab Delay" id="delay_reason1"> Cab Delay
                            </div>
							<div class="input-group col-md-3">
								<input type="checkbox" name="delay_reason[]" class="delay_reason" id="Permission" id="delay_reason2"> Permission
                            </div>
						</div>
						<div class="form-group">                                        
							<label class="control-label col-md-3">Attendance Type</label>
							<div class="input-group col-md-9">
								<select name="add_attendance_type" id="add_attendance_type" class="form-control">
									<option value="P">P</option>
									<option value="O">O</option>
									<option value="OD">OD</option>
									<option value="LOP">LOP</option>
									<option value="Sick Leave">Sick Leave</option>
									<option value="Training">Training</option>
									<option value="System Exit">System Exit</option>
									<option value="LIL">LIL</option>
								</select>
							</div>
						</div>
						<div class="form-group">                                        
							<label class="control-label col-md-3">Comment</label>
							<div class="input-group col-md-9">
								<textarea name="add_attendance_comment" id="add_attendance_comment" class="form-control"></textarea>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" id="submit_add_attedance" class="btn btn-info">Submit</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				
			  </div>
			  
			</div>
		</div>

        
        <?php include("includes/footer.php"); ?>
		<script type="text/javascript" src="js/plugins/bootstrap/bootstrap-timepicker.min.js"></script>
		<script type="text/javascript" src="js/plugins/datatables/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="js/plugins/tableexport/tableExport.js"></script>
		<script type="text/javascript" src="js/plugins/tableexport/jquery.base64.js"></script>
		<script type="text/javascript" src="js/plugins/tableexport/html2canvas.js"></script>
		<script type="text/javascript" src="js/plugins/tableexport/jspdf/libs/sprintf.js"></script>
		<script type="text/javascript" src="js/plugins/tableexport/jspdf/jspdf.js"></script>
		<script type="text/javascript" src="js/plugins/tableexport/jspdf/libs/base64.js"></script> 
		
		<script>
		$('.timepicker').timepicker();
		
		function show_model(getval){
			var valArr = getval.split('-');
			$("#emp_id").val(valArr[0]);
			$("#emp_name").html(valArr[1]);
			$("#login_date").html(valArr[2]);
			$("#login_time").val(valArr[3]);
			jQuery("#login_time").timepicker({defaultTime: valArr[3]});
			$("#logout_time").val(valArr[4]);
			jQuery("#logout_time").timepicker({defaultTime: valArr[4]});
			$("#total_time").val(valArr[5]);
			//$("#having_record").val(valArr[6]);
		}
		function show_model_new(getval){
			//alert(getval);
			var valArr = getval.split('-'); //alert(valArr[0]); alert(valArr[1]);
			$("#emp_name").html(valArr[0]);
			$("#login_date").html(valArr[1]);
			$("#add_emp_name").val(valArr[0]);
			$("#add_login_date").val(valArr[1]);
			$("#add_logout_date").val(valArr[1]);
			$("#record_date").val(valArr[1]);
		}
		$("#submit_edit_attedance").click(function(){
			var id = $("#emp_id").val();
			var attendance_type = $("#attendance_type").val(); //alert(attendance_type);
			var attendance_comment = $("#attendance_comment").val();
			var login_time = $("#login_time").val();
			var logout_time = $("#logout_time").val();
			var total_time = $("#total_time").val();
			var total_res_time = total_time.split(":");
			var delay_reasonArr = [];
			$("input[name='delay_reason[]']:checked").each(function ()
			{
				delay_reasonArr.push($(this).val());
			});
			if(attendance_type == "P"){
				if(parseInt(total_res_time[0]) < 8){
					alert("Total Time should more than 8 Hours!");
					return false;
				}else if(login_time == logout_time){
					alert("Login Time Should not equal to Logout Time");
					$("#logout_time").focus();
					return false;
				}else{
					$.ajax({
						url:"ajax_edit_attendance.php",
						data:"edit_attedance=edit&id="+id+"&attendance_type="+attendance_type+"&attendance_comment="+attendance_comment+"&login_time="+login_time+"&logout_time="+logout_time+"&total_time="+total_time+"&delay_reason="+delay_reasonArr,
						type:"POST",
						success:function(html){
							if(html == "success"){
								location.reload();
							}else{
								alert("Failed to upload");
							}
						}
					});
				}
			}else{
				$.ajax({
					url:"ajax_edit_attendance.php",
					data:"edit_attedance=edit&id="+id+"&attendance_type="+attendance_type+"&attendance_comment="+attendance_comment+"&login_time="+login_time+"&logout_time="+logout_time+"&total_time="+total_time+"&delay_reason="+delay_reasonArr,
					type:"POST",
					success:function(html){
						alert(html);
						if(html == "success"){
							location.reload();
						}else{
							alert("Failed to upload");
						}
					}
				});
			}			
		});
		$("#submit_add_attedance").click(function(){
			var attendance_type = $("#add_attendance_type").val(); 
			var attendance_comment = $("#add_attendance_comment").val();
			var login_date = $("#add_login_date").val();
			var logout_date = $("#add_logout_date").val();
			var login_time = $("#add_login_time").val();
			var logout_time = $("#add_logout_time").val();
			var total_time = $("#add_total_time").val();
			var record_date = $("#record_date").val();
			var add_emp_name = $("#add_emp_name").val();
			var total_res_time = total_time.split(":");
			var delay_reasonArr = []
			$("input[name='delay_reason[]']:checked").each(function ()
			{
				delay_reasonArr.push($(this).val());
			});
			if(attendance_type == "P"){
				if(parseInt(total_res_time[0]) < 8){
					alert("Total Time should more than 8 Hours!");
					return false;
				}else if(login_time == logout_time){
					alert("Login Time Should not equal to Logout Time");
					$("#logout_time").focus();
					return false;
				}else{
					$.ajax({
						url:"ajax_edit_attendance.php",
						data:"add_attedance=add&add_emp_name="+add_emp_name+"&record_date="+record_date+"&attendance_type="+attendance_type+"&attendance_comment="+attendance_comment+"&login_time="+login_time+"&logout_time="+logout_time+"&total_time="+total_time+"&login_date="+login_date+"&logout_date="+logout_date+"&record_date="+record_date+"&add_emp_name="+add_emp_name+"&delay_reason="+delay_reasonArr,
						type:"POST",
						success:function(html){
							if(html == "success"){
								location.reload();
								
							}else{
								//$("#res").html(html);
								alert("Failed to upload");
							}
						}
					});
				}
			}else{
				$.ajax({
					url:"ajax_edit_attendance.php",
					data:"add_attedance=add&add_emp_name="+add_emp_name+"&record_date="+record_date+"&attendance_type="+attendance_type+"&attendance_comment="+attendance_comment+"&login_time="+login_time+"&logout_time="+logout_time+"&total_time="+total_time+"&login_date="+login_date+"&logout_date="+logout_date+"&record_date="+record_date+"&add_emp_name="+add_emp_name+"&delay_reason="+delay_reasonArr,
					type:"POST",
					success:function(html){
						alert(html);
						if(html == "success"){
							location.reload();
							
						}else{
							//$("#res").html(html);
							alert("Failed to upload");
						}
					}
				});
			}			
		});
		function add_calculate_hours(){
			var login_time = $("#add_login_time").val(); 
			var logout_time = $("#add_logout_time").val(); 
			var attendance_type = $("#attendance_type").val();
			$.ajax({
				url:"ajax_edit_attendance.php",
				data:"cal_hours=hours&login_time="+login_time+"&logout_time="+logout_time,
				type:"POST",
				success:function(html){
					$("#add_total_time").val(html);
				}
			});
		}
		function calculate_hours(){
			var login_time = $("#login_time").val(); 
			var logout_time = $("#logout_time").val(); 
			var attendance_type = $("#attendance_type").val();
			$.ajax({
				url:"ajax_edit_attendance.php",
				data:"cal_hours=hours&login_time="+login_time+"&logout_time="+logout_time,
				type:"POST",
				success:function(html){
					//alert(html);
					$("#total_time").val(html);
				}
			});
		}
		$('.table').dataTable( {
		  "pageLength": 50
		} );
		</script>
		
		
		