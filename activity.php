<?php 
error_reporting(0);
include('includes/config.php');

$qry= "SELECT Name from login where name!='' and project like '%aruba%' order by Name ASC";
$prepareqry=$pdo->prepare($qry);
$prepareqry->execute();
$namelist=$commonobj->arrayColumn($prepareqry->fetchAll(PDO::FETCH_ASSOC),'','Name');

$Tid = base64_decode($_GET['Tid']);
    if(!empty($Tid)){
	   $getreturnArr = $commonobj->getQry("SELECT * from task where task_id =$Tid ");
       $resultArr = $getreturnArr[0];
       $presentpersonlist = explode(',',$resultArr['invited_persion']);
	   $absentpersonlist = explode(',',$resultArr['absent']);
	   $getActivityArr = $commonobj->getQry("SELECT * from activity where task_id = $Tid ");
	  // print_r($getActivityArr);
	}
	
$id = base64_decode($_GET['id']);
    if(!empty($id)){
		
       $getreturnArr = $commonobj->getQry("SELECT * from task where task_id =$id ");
       $resultArr = $getreturnArr[0];
       $presentpersonlist = explode(',',$resultArr['invited_persion']);
	   $absentpersonlist = explode(',',$resultArr['absent']);
	   //echo "SELECT * from activity where task_id = $id ";
       $getActivityArr = $commonobj->getQry("SELECT * from activity where task_id = $id ");
	   //print_r($getActivityArr);
    }
   
	if(isset($_POST['hidden_id'])){
        for ($i=1; $i <= $_POST['hidden_id'] ; $i++) { 
			$act_id = $_POST["activity".$i."_id"];
			$title = $_POST["ID".$i."_title"];
			$comments = $_POST["ID".$i."_comments"]; 
			$startdate = date('Y-m-d',strtotime($_POST["ID".$i."_start_date"]));
			$enddate = date('Y-m-d',strtotime($_POST["ID".$i."_end_date"]));
			$respective_person = implode(",",$_POST["ID".$i."_person"]);
			$priority = $_POST["ID".$i."_priority"];
			$status = $_POST["ID".$i."_status"];
			
			if($act_id == ""){
				$insertId = $commonobj->InsertRecord('activity',array('task_id','activity_title', 'comments', 'start_date', 'end_date', 'respective_person', 'priority', 'status','create_by','create_at','update_at'),array($Tid,$title,$comments,$startdate,$enddate,$respective_person,$priority,$status,$_SESSION['email'],$dbdatetime,$dbdatetime),'');
			}else{
				//echo "UPDATE `activity` SET `activity_title`='$title',`comments`='$comments',`start_date`='$startdate',`end_date`='$enddate',`respective_person`='$respective_person',`priority`='$priority',`status`='$status',`update_at`='$dbdatetime' WHERE activity_id = '$act_id'";
				$updateId = $commonobj->getQry("UPDATE `activity` SET `activity_title`='$title',`comments`='$comments',`start_date`='$startdate',`end_date`='$enddate',`respective_person`='$respective_person',`priority`='$priority',`status`='$status',`update_at`='$dbdatetime' WHERE activity_id = '$act_id'");
			}
        }
		if($_POST['submit_button'] == 'saveform'){
			header("location:activity.php?Tid=".base64_encode($Tid));
		}elseif($_POST['submit_button'] == 'saveclose'){
			header("location:activity_list.php");
		}
		//header("location:activity_list.php?id=".base64_encode($id));
		//header("location:activity_list.php");
    }
	
include("includes/header.php");
?>
<style>
    .form-group {
        margin-bottom: 40px;
    }
	a.list-group-item.person-list {
		display: inline-block;
	}
	.input-group .bootstrap-select.form-control {
        z-index: inherit;
    }
    .form-control[disabled], .form-control[readonly] {
        color: #0c0c0c;
    }
</style>
        <!-- PAGE CONTENT WRAPPER -->
        <div class="page-content-wrap">
        
            <div class="row">
                <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title" style='text-transform: capitalize;'><strong><?=$resultArr['title']?></strong></h3>
                                <ul class="panel-controls">
									<?php if($id){ ?>
										<li><a href="activity_list.php?list_type=MOM"><span class="fa fa-arrow-left"></span></a></li>
									<?php } ?>
                                </ul>
                            </div>
                            <!-- <div class="panel-body">Add Task Details for TL</p></div> -->
                            <div class="panel-body ">

                                <div class="row">
                                    <div class="col-md-3">
										<!--<div class="panel panel-default">
                                            <div class="panel-body list-group">-->
										        <a class="list-group-item"><span class="fa fa-calendar"></span> <?=date('d-m-Y',strtotime($resultArr['date']))?><span class="badge badge-danger"></span></a>
											<!--</div>
                                        </div>-->
                                    </div>
									
                                    <div class="col-md-3">
                                        <a class="list-group-item"><span class="fa fa-clock-o"></span><?=$resultArr['time']?></a>                                
                                    </div>
									<div class="col-md-3">
                                        <a class="list-group-item"><span class="fa fa-map-marker"></span> <?=$resultArr['meeting_place']?><span class="badge badge-danger"></span></a>
                                    </div>
									<div class="col-md-3">
                                        <a class="list-group-item"><span class="fa fa-phone"></span> <?=$resultArr['type_of_meeting']?><span class="badge badge-default"></span></a>
                                    </div>
                                    <div class="col-md-12" style="padding:10px;">
									<h5>Present List</h5>
                                            <?php foreach ($presentpersonlist as $key => $value) { ?>
                                                <a class="list-group-item person-list"><span class="fa fa-user"></span> <?=$value?><span class="badge badge-danger"></span></a>
                                            <?php }?>
                                        
                                    </div>
									<div class="col-md-12" style="padding:10px;">
									<h5>Absent List</h5>
                                            <?php foreach ($absentpersonlist as $key => $value) { ?>
                                                <a class="list-group-item person-list"><span class="fa fa-user"></span> <?=$value?><span class="badge badge-danger"></span></a>
                                            <?php }?>
                                        
                                    </div>

                                </div>
                                <hr>
                                <!-- id="sign-up_area"  -->
                                <form action="#" method="post" role="form">
									<input type="hidden" name="_token" value="<?php echo $token; ?>">
                                    <input type="hidden" id='hidden_id' name="hidden_id" value="1">
									  
                                    <?php if(!$id){ ?>
									<div class="panel-heading ui-draggable-handle">
                                        <ul class="panel-controls">
                                            <i></i>
                                            <button type="button" id="btnAdd" name="btnAdd" class="btn btn-info"><i class="fa fa-plus"></i>Add section</button>
                                            <button type="button" id="btnDel" name="btnDel" class="btn btn-danger"><i class="fa fa-minus"></i>Remove Last Section</button>
											<button id="submit_button" name="submit_button" value="saveform" id='saveform' class="btn btn-info"><i class="fa fa-save"></i>Save Form</button>
                                            <button id="submit_button" type="submit" name="submit_button" value="saveclose" id='save' class="btn btn-info" onclick="return confirm('Are you sure to Save & Close?');"><i class="fa fa-save"></i>Save & Close</button>
                                        </ul>                                
                                    </div>
									<?php } ?>
									<?php if(count($getActivityArr) <= 0){ ?>
                                    <div id="entry1" class="clonedInput">
                                        <p><h4 id="reference" name="reference" class="heading-reference">Task #1</h4></p>
                                        <fieldset>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label label_ttl">Text Tittle</label>
                                                        <div class="col-md-8 input_controls">                                            
                                                            <div class="input-group">
                                                              <input type="text" class="form-control select_ttl" name="ID1_title" id="ID1_title" placeholder="Activity Tittle" required>
                                                              <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                            </div>                                            
                                                            <span class="help-block"></span>
                                                        </div>
                                                    </div>
												</div>
												<div class="col-md-3">
													<div class="form-group">                                        
                                                        <label class="col-md-5 control-label label_str date">Start Date</label>
                                                        <div class="col-md-7 input_controls">
                                                            <div class="input-group date" data-provide="datepicker">
                                                                <input type="text" class="form-control input_str datepicker" value="<?php echo date('m/d/Y')?>" data-date-format="mm/dd/yyyy" data-date-viewmode="years" id='ID1_start_date' name='ID1_start_date' required>
                                                                <div class="input-group-addon">
                                                                    <span class="glyphicon glyphicon-th"></span>
                                                                </div>
                                                            </div>
                                                            <span class="help-block font-red"</span>
                                                        </div>
                                                    </div>
												</div>
												<div class="col-md-3">
                                                    <div class="form-group">                                        
                                                        <label class="col-md-5 control-label label_ed date">End Date</label>
                                                        <div class="col-md-7 input_controls">
                                                            <div class="input-group date" data-provide="datepicker">
                                                                <input type="text" class="form-control input_ed datepicker" value="<?php echo date('m/d/Y')?>" data-date-format="mm/dd/yyyy" data-date-viewmode="years" id='ID1_end_date' name='ID1_end_date' required>
                                                                <div class="input-group-addon">
                                                                    <span class="glyphicon glyphicon-th"></span>
                                                                </div>
                                                            </div>
                                                            <span class="help-block font-red"</span>
                                                        </div>
                                                    </div>
												</div>
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label label_pr">Priority</label>
                                                        <div class="col-md-9 input_controls">
                                                            <div class="input-group ">  
                                                               <select class="form-control select input_pr" id='ID1_priority' name='ID1_priority'>
                                                                    <option>P1</option>
                                                                    <option>P2</option>
                                                                    <option>P3</option>
                                                                    <option>P4</option>
                                                                </select>
                                                                <span class="input-group-addon"><span class="fa fa-users"></span></span>
                                                            </div>
                                                        </div>
                                                    </div>
												</div>
											</div>
											<div class="row" style="margin-bottom:5px;">
												<div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label label_st">Status</label>
                                                        <div class="col-md-9 input_controls">
                                                            <div class="input-group">  
                                                               <select class="form-control select input_st " id='ID1_status' name='ID1_status'>
                                                                    <option>Yet to Start</option>
                                                                    <option>Process</option>
                                                                    <option>Pending</option>
                                                                    <option>On going</option>
                                                                    <option>Complete</option>
                                                                </select>
                                                                <span class="input-group-addon"><span class="fa fa-users"></span></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
												<div class="col-md-8">
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label label_per">Respective Person</label>
                                                        <div class="col-md-9 input_controls">  
                                                            <div class="input-group "> 
                                                                <select class="form-control select input_per delet-div" multiple id='ID1_person' name='ID1_person[]'>
                                                                    <option value=''>All</option>
                                                                    <?php
																	foreach ($namelist as $value) { ?>
																		<option value="<?=$value?>"><?=$value?></option>
																	<?php }
																	?>
                                                                </select>
                                                                <span class="input-group-addon"><span class="fa fa-users"></span></span> 
                                                                <span class="help-block error" id="person-error" ></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
											<div class="row">
												<div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="col-md-1 control-label label_cmt">Comments</label>
                                                        <div class="col-md-11 input_controls">  
                                                            <textarea class="form-control input_cmt" placeholder="Do you want to add a message?" required name="ID1_comments" id="ID1_comments"></textarea >            
                                                            <span class="help-block"></span>
                                                        </div>
                                                    </div>
                                                </div>
											</div>
                                            <hr>
                                        </fieldset>
                                    </div>
									<?php }else{ ?>
									<?php foreach($getActivityArr as $key => $arrayval){
										$disabled = (!empty($id))?'disabled':'';
										$resArr = explode(",",$arrayval['respective_person']);
										$getActivitycmdsArr = $commonobj->getQry("SELECT * from activity_comments where activity_id = '".$arrayval['activity_id']."' AND task_id = '".$arrayval['task_id']."' ");
										?>
										<div id="entry<?php echo $key+1; ?>" class="clonedInput">
											<p><h4 id="reference" name="reference" class="heading-reference">Task #<?php echo $key+1; ?></h4></p>
											<fieldset>
												<div class="row">
													<div class="col-md-3">
														<div class="form-group">
															<label class="col-md-4 control-label label_ttl">Text Tittle</label>
															<div class="col-md-8">                                            
																<div class="input-group">
																  <input type="hidden" class="form-control act_id" name="activity<?php echo $key+1;?>_id" value="<?php echo $arrayval['activity_id'];?>" id="activity<?php echo $key+1;?>_id">
																  <input type="text" class="form-control select_ttl" <?php echo $disabled; ?> name="ID<?php echo $key+1;?>_title" value="<?php echo $arrayval['activity_title'];?>" id="ID<?php echo $key+1;?>_title" placeholder="Activity Tittle" required>
																  <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
																</div>                                            
																<span class="help-block"></span>
															</div>
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">                                        
															<label class="col-md-4 control-label label_str date">Start Date</label>
															<div class="col-md-8">
																<div class="input-group date">
																	<input type="text" class="form-control input_str datepicker" <?php echo $disabled; ?> value="<?php echo date('m/d/Y',strtotime($arrayval['start_date']))?>" data-date-format="mm/dd/yyyy" data-date-viewmode="years" id='ID<?php echo $key+1;?>_start_date' name='ID<?php echo $key+1;?>_start_date' required>
																	<div class="input-group-addon">
																		<span class="glyphicon glyphicon-th"></span>
																	</div>
																</div>
																<span class="help-block font-red"</span>
															</div>
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">                                        
															<label class="col-md-4 control-label label_ed date">End Date</label>
															<div class="col-md-8">
																<div class="input-group date">
																	<input type="text" class="form-control input_ed datepicker" <?php echo $disabled; ?> value="<?php echo date('m/d/Y',strtotime($arrayval['end_date']))?>" data-date-format="mm/dd/yyyy" data-date-viewmode="years" id='ID<?php echo $key+1;?>_end_date' name='ID<?php echo $key+1;?>_end_date' required>
																	<div class="input-group-addon">
																		<span class="glyphicon glyphicon-th"></span>
																	</div>
																</div>
																<span class="help-block font-red"</span>
															</div>
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label class="col-md-4 control-label label_pr">Priority</label>
															<div class="col-md-8">
																<div class="input-group">  
																   <select class="form-control select input_pr" <?php echo $disabled; ?> id='ID<?php echo $key+1;?>_priority' name='ID<?php echo $key+1;?>_priority'>
																		<option <?php if($arrayval['priority']== 'P1'){echo "selected"; } ?>>P1</option>
																		<option <?php if($arrayval['priority']== 'P2'){echo "selected"; } ?>>P2</option>
																		<option <?php if($arrayval['priority']== 'P3'){echo "selected"; } ?>>P3</option>
																		<option <?php if($arrayval['priority']== 'P4'){echo "selected"; } ?>>P4</option>
																	</select>
																	<span class="input-group-addon"><span class="fa fa-users"></span></span>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class="row" style="margin-bottom:5px;">
													<div class="col-md-4">
														<div class="form-group">
															<label class="col-md-3 control-label label_st">Status</label>
															<div class="col-md-9">
																<div class="input-group">  
																   <select class="form-control select input_st" <?php echo $disabled; ?> id='ID<?php echo $key+1;?>_status' name='ID<?php echo $key+1;?>_status'>
																		<option <?php if($arrayval['status']== 'Yet to Start'){echo "selected"; } ?>>Yet to Start</option>
																		<option <?php if($arrayval['status']== 'Process'){echo "selected"; } ?>>Process</option>
																		<option <?php if($arrayval['status']== 'Pending'){echo "selected"; } ?>>Pending</option>
																		<option <?php if($arrayval['status']== 'On going'){echo "selected"; } ?>>On going</option>
																		<option <?php if($arrayval['status']== 'Complete'){echo "selected"; } ?>>Complete</option>
																	</select>
																	<span class="input-group-addon"><span class="fa fa-users"></span></span>
																</div>
															</div>
														</div>
													</div>
													<div class="col-md-8">
														<div class="form-group">
															<label class="col-md-3 control-label label_per">Respective Person</label>
															<div class="col-md-9">  
																<div class="input-group "> 
																	<select class="form-control select input_per delet-div" multiple <?php echo $disabled; ?> id='ID<?php echo $key+1;?>_person' name='ID<?php echo $key+1;?>_person[]'>
																		<option value='' <?php if(in_array('',$resArr)){echo "selected"; } ?>>All</option>
																		<?php
																		foreach ($namelist as $value) { ?>
																			<option value="<?=$value?>" <?php if(in_array($value,$resArr)){echo "selected"; } ?>><?=$value?></option>
																		<?php }
																		?>
																	</select>
																	<span class="input-group-addon"><span class="fa fa-users"></span></span> 
																	<span class="help-block error" id="person-error" ></span>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-md-12">
														<div class="form-group">
															<label class="col-md-1 control-label label_cmt">Comments</label>
															<div class="col-md-11">  
																   <textarea class="form-control input_cmt" <?php echo $disabled; ?> rows='<?php echo substr_count($arrayval['comments'], "\n")+1?>' placeholder="Do you want to add a message?" required name="ID<?php echo $key+1;?>_comments" id="ID<?php echo $key+1;?>_comments"><?php echo $arrayval['comments'];?></textarea >            
																<span class="help-block"></span>
															</div>
														</div>
													</div>
												</div>
												<?php if(count($getActivitycmdsArr) > 0 && $id != '') { ?>
												<h5 style="font-weight:bold;">Sub Comments:</h5>
												<div class="row" style="margin-top:10px;">
													
													<?php foreach($getActivitycmdsArr as $cmds){ ?>
														<div class="col-md-8">
															<div class="form-group">
																<label class="col-md-2 control-label label_cmt">Comments</label>
																<div class="col-md-10">  
																	<textarea class="form-control input_cmt" <?php echo $disabled; ?> required name="comments" id="comments"><?php echo $cmds['comments'];?></textarea >            
																	<span class="help-block"></span>
																</div>
															</div>
														</div>
														<div class="col-md-4">
															<div class="form-group">
																<label class="col-md-1 control-label label_cmt">Date</label>
																<div class="col-md-11">  
																	<input type="text" name="cmd_date" id="cmd_date" class="form-control" <?php echo $disabled; ?> value="<?php echo date('Y-m-d',strtotime($cmds['create_at']));?>">
																</div>
															</div>
														</div>
													<?php } ?>
												</div>
												<?php } ?>
												<hr>
												<br>
											</fieldset>
										</div>
										
									<?php } 
									}?>
                                </form>
                                <div id="attribution"></div>
                            </div>
                        </div>
                </div>
            </div> 
        </div>
        <!-- END PAGE CONTENT WRAPPER -->  
<?php include("includes/footer.php"); ?>
 <script type="text/javascript" src="clone-path.js"></script>

<script type="text/javascript">
<?php if($id == ''){ ?>
    $('#hidden_id').val(1);
<?php }else{ ?>
	$('#hidden_id').val('<?php echo count($getActivityArr); ?>');
<?php } ?>
   $('.datepicker').datepicker({
        format: 'dd-mm-yyyy',
        startDate: '-2d'
    });
   $("#add_task").validate({
        ignore: [],
        rules: {                                            
                title: "required",
                date: "required",
                time:'required',
                place:'required',
                'person[]':'required',
                type_meeting: 'required',
            }                                
        }); 

    
</script>