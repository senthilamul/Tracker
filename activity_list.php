<?php 
error_reporting(0);
include('includes/config.php');
//include 'includes/session_check.php';
$_REQUEST['list_type'] = !empty($_REQUEST['list_type'])?$_REQUEST['list_type']:"MOM";
if(isset($_POST['filter_btn'])){
	if($_REQUEST['list_type'] == "Task"){
		$startdate = date('Y-m-d',strtotime($_POST['start_date']));
		$enddate = date('Y-m-d',strtotime($_POST['end_date']));
		if($_POST['end_date'] == ''){
			$qry.= $_POST['start_date'] != ''?" AND `start_date` = '$startdate' ":'';
		}else{
			$qry.= $_POST['start_date'] != ''?" AND `start_date` between '$startdate' ":'';
		}
		
		$qry.= $_POST['end_date'] !='' ? " AND '$enddate'":'';	
		$qry.= $_POST['type_meeting'] != ''?" AND status = '$_POST[type_meeting]'":'';	
		
	}elseif($_REQUEST['list_type'] == "MOM"){
		$startdate = date('Y-m-d',strtotime($_POST['start_date']));
		$enddate = date('Y-m-d',strtotime($_POST['end_date']));
		if($_POST['end_date'] == ''){
			$qry.= $_POST['start_date'] != ''?" AND `date` = '$startdate' ":'';
		}else{
			$qry.= $_POST['start_date'] != ''?" AND `date` between '$startdate' ":'';
		}
		
		$qry.= $_POST['end_date'] !='' ? " AND '$enddate'":'';	
	}
	
}

//print_r($_POST);
// echo "SELECT * FROM activity where $qry order by task_id desc";
// $returnArr = $commonobj->getQry("SELECT * FROM activity where $qry order by task_id desc");
// print_r($returnArr);
//echo $qry;
// echo "SELECT a.title,a.task_id,a.date FROM task a join activity b on a.task_id = b.task_id where $qry group by a.task_id order by a.task_id desc";
// $returnArr = $commonobj->getQry("SELECT a.title,a.task_id,a.date FROM task a join activity b on a.task_id = b.task_id where $qry group by a.task_id order by a.task_id desc");
	

include("includes/header.php");
?> 
<style>
	.panel .panel-title {
		font-size: 15px;
		line-height: 16px;
	}

</style>
    	<!-- PAGE CONTENT WRAPPER -->
    <div class="page-content-wrap">

		<div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><strong>MOM List</strong></h3>
                <ul class="panel-controls">
                   <!--  <i></i>
                    <button type="button" id="btnAdd" name="btnAdd" class="btn btn-info"><i class="fa fa-plus"></i>Add section</button>
                    <button type="button" id="btnDel" name="btnDel" class="btn btn-danger"><i class="fa fa-minus"></i>Remove Last Section</button>
                    <button id="submit_button" name="submit_button" id='save' class="btn btn-info" onclick="ConfirmDialog('Are you sure');"><i class="fa fa-save"></i>Save</button> -->
                </ul>
            </div>
            <div class="panel-body">
            		<div class="row">
					<form action="activity_list.php" method="POST" id="frmsrch">
						<input type="hidden" name="_token" value="<?php echo $token; ?>">
						<div class="col-md-2 col-sm-2">
                        	<div class="form-group">
                                <label class="col-md-3 control-label">Type</label>
                                <div class="col-md-8">                                            
                                    <div class="input-group">
                                        <select class="form-control select" id="list_type" name="list_type" onchange="reload()">
                                            <option value="MOM" <?php if($_REQUEST['list_type'] == "MOM"){ echo "selected"; } ?>>MOM</option>
                                            <option value="Task" <?php if($_REQUEST['list_type'] == "Task"){ echo "selected"; } ?>>Task</option>
                                        </select>
                                    </div>                                            
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
					
						<input type="hidden"  name="filter_btn" >
                    	<div class="col-md-3 col-sm-3">
                        	<div class="form-group">
	                            <label class="col-md-4 control-label">Start Date</label>
	                            <div class="col-md-8">                                            
	                                <div class="input-group">
	                                  <input type="text" class="form-control input_str datepicker" value="<?php echo !empty($_POST['start_date'])?$_POST['start_date']:""?>" data-date-format="mm/dd/yyyy" data-date-viewmode="years" id='start_date' name='start_date' >
	                                  <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
	                                </div>                                            
	                                <span class="help-block"></span>
	                            </div>
	                        </div>
                        </div>
                        <div class="col-md-3 col-sm-3">
                        	<div class="form-group">
	                            <label class="col-md-4 control-label">End Date</label>
	                            <div class="col-md-8">                                            
	                                <div class="input-group">
	                                  <input type="text" class="form-control input_str datepicker" value="<?php echo !empty($_POST['end_date'])?$_POST['end_date']:""?>" data-date-format="mm/dd/yyyy" data-date-viewmode="years" id='end_date' name='end_date' >
	                                  <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
	                                </div>                                            
	                                <span class="help-block"></span>
	                            </div>
	                        </div>
                       </div>
					   <?php if($_REQUEST['list_type'] == "Task"){ ?>
                        <div class="col-md-3  col-sm-4">
                        	<div class="form-group">
                                <label class="col-md-3 control-label">Status</label>
                                <div class="col-md-8">                                            
                                    <div class="input-group">
                                        <!-- <input type="text" class="form-control"/ id='type_meeting' name="type_meeting"> -->
                                        <select class="form-control select" id="type_meeting" name="type_meeting" style="display: none;">
                                            <option value="">All</option>
                                            <option value="Yet to Start" <?php echo ($_POST['type_meeting'] == "Yet to Start")?"selected":""; ?>>Yet to Start</option>
                                            <option value="Process" <?php echo ($_POST['type_meeting'] == "Process")?"selected":""; ?>>Process</option>
                                            <option value="Pending" <?php echo ($_POST['type_meeting'] == "Pending")?"selected":""; ?>>Pending</option>
                                            <option value="On going" <?php echo ($_POST['type_meeting'] == "On going")?"selected":""; ?>>On going</option>
                                            <option value="Complete" <?php echo ($_POST['type_meeting'] == "Complete")?"selected":""; ?>>Complete</option>
                                        </select>
                                        <span class="input-group-addon"><span class="fa fa-user"></span></span>
                                    </div>                                            
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
					   <?php } ?>
                        <div class="col-md-1  col-sm-1">
                        	<button type="submit" value="filter" class="btn btn-info btn-block">Submit</button>
                        </div>
					</form>
                    </div>
            </div>
            <div class="panel-body ">                                                                        
                <div class="row">
                    <!-- START ACCORDION -->
                    <div class="panel-group accordion">
                    
						
		<!-- 					<div class="panel panel-success">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a href="#accOneCol<?=$key?>" class="accOneCol<?=$key?>" value="show" onclick='show_content_div("accOneCol<?=$key?>")'>
											<?php echo $j.".".$taskvalue['title']?>
										</a>
									</h4>
								</div>                                
								<div class="panel-body" id="accOneCol<?=$key?>" data-spy="accOneCol<?=$key?>" data-target="#accOneCol<?=$key?>"> -->
									<div id="DataTables_Table_1_wrapper" class="dataTables_wrapper no-footer">
									<?php if($_REQUEST['list_type'] == "MOM"){ ?>
										<div class="table-responsive">
										<table class="table table-bordered datatable" role="grid"'>
											<thead>
												<tr role="row">
													<th>S.no</th>
													<th>Task</th>
													<th style='width: 100px;'>Date</th>
													<th style='width: 100px;'>Time</th>
													<th>Invited person</th>
													<th>Absent</th>
													<th>Action</th>
												</tr>
											</thead>
											<tbody>
											<?php $j=1;$s=1;
												$activityArr = $commonobj->getQry("SELECT * FROM `task` where task_id!='' $qry order by task_id desc");
												if(count($activityArr)>0){
													foreach ($activityArr as $key => $activityvalue) { 
														$titlename=$commonobj->getQry("SELECT task_id,activity_title,comments,start_date,end_date,priority,status,activity_id from activity where task_id = '$activityvalue[task_id]'"); ?>
														<tr>
														<td><?=$s?></td>
														<td><?php echo $activityvalue['title']?></td>
														<td><?php echo $activityvalue['date']?></td>
														<td><?php echo $activityvalue['time']?></td>
														<td><?php echo $activityvalue['invited_persion']?></td>
														<td><?php echo $activityvalue['absent']?></td>
														<td>
															<a href="activity.php?id=<?php echo base64_encode($activityvalue['task_id'])?>" class="control-danger"><span class="fa fa-pencil"></span></a></td>
														</tr>
													<?php $s++;}
													$j++;
												} else{ ?>
												
												<td> No Task Found</td>
												<?php } ?>
											</tbody>
										</table>
										</div>
									<?php }elseif($_REQUEST['list_type']=='Task'){ ?>
									<div class="table-responsive">
										<table class="table table-bordered datatable" role="grid">
											<thead>
												<tr role="row">
													<th>S.no</th>
													<th>Activity</th>
													<th>Comments</th>
													<th style='width: 100px;'>Start Date</th>
													<th style='width: 100px;'>End Date</th>
													<th>Respective Person</th>
													<th>Priority</th>
													<th>Status</th>
													<th>Action</th>
												</tr>
											</thead>
											<tbody>
											<?php $j=1;$s=1;
												$activityArr = $commonobj->getQry("SELECT * FROM `activity` where task_id!='' $qry order by task_id desc");
												if(count($activityArr)>0){
													foreach ($activityArr as $key => $activityvalue) { 
														$titlename=$commonobj->getQry("SELECT title from task where task_id = '$activityvalue[task_id]'"); ?>
														<tr>
														<td><?=$s?></td>
														<td><?=ucwords($activityvalue['activity_title'])?></td>
														<td title="<?=$activityvalue['comments']?>"><?=$activityvalue["comments"]?></td> 
														<td><?=$activityvalue['start_date']?></td>
														<td><?=$activityvalue['end_date']?></td>
														<td><?=$activityvalue['respective_person']?></td>
														<td><?=$activityvalue['priority']?></td>
														<td><?=$activityvalue['status']?></td>
														<td><a href="edit_activity.php?Tid=<?php echo base64_encode($activityvalue['task_id'])?>&Aid=<?php echo base64_encode($activityvalue['activity_id'])?>" class="control-danger"><span class="fa fa-pencil"></span></a></td>
														</tr>
													<?php $s++;}
													$j++;
												} else{ ?>
												
												<td> No Task Found</td>
												<?php } ?>
											</tbody>
										</table>
									</div>
									<?php } ?>
									</div>
								<!-- </div>   -->                              
							<!-- </div> -->
						<!--<style>#DataTables_Table_1_paginate{display: none}</style>-->
						

                    </div>
                    <!-- END ACCORDION -->                        
                </div>
                
				
            </div>
            <div class="panel-footer text-center">
                                                   
               
            </div>
        </div>
		
	</div>

<?php include("includes/footer.php"); ?>
<script>
function show_content_div(idval){
	for($i=0;$i<<?php echo count($returnArr); ?>; $i++)
	{
		var formid = "accOneCol"+$i; 
		if(formid == idval){
			var show_hide = $("."+idval).attr('value');
			if(show_hide == "show"){
				$("."+idval).attr("value","hide");
				$("#"+idval).css('display','inline-block');
			}else{
				$("."+idval).attr("value","show");
				$("#"+idval).css('display','none');
			}
		}else{
			$("#"+formid).css('display','none');
			var show_hide = $("."+formid).attr('value');
			$("."+formid).attr("value","show");
		}
	}
}
function reload(){
	document.getElementById("frmsrch").action = 'activity_list.php'; 
	document.getElementById("frmsrch").submit();
	return false;
}
// $('.datepicker').on('changeDate', function(ev){
//     $(this).datepicker('hide');
// });
</script>
