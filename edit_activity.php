<?php 
error_reporting(0);
include('includes/config.php');
$_SESSION['email'] = 'senthil@csscorp.com';
$Aid = base64_decode($_GET['Aid']);
$Tid = base64_decode($_GET['Tid']);
    if(isset($_POST['hidden_id'])){
        $end_date = date('Y-m-d',strtotime($_POST['ID1_end_date']));
        $sql = "UPDATE activity SET status = '$_POST[ID1_status]',end_date='$end_date',update_at = '$dbdatetime'  WHERE activity_id = '$Aid'";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        //exit;
        for ($i=2; $i <= $_POST['hidden_id'] ; $i++) { 
            $comments = $_POST["ID".$i."_comments"];
			if($_POST["ID".$i."_comments"] != ""){
				$insertId = $commonobj->InsertRecord('activity_comments',array('activity_id','task_id','comments', 'create_by', 'create_at', 'update_at'),array($_POST['activity_id'],$_POST['task_id'],$comments,$_SESSION['email'],$dbdatetime,$dbdatetime),'');
			}
            header("location:edit_activity.php?Tid=".base64_encode($Tid)."&Aid=".base64_encode($Aid));
        }
    }
    if(!empty($Tid)){
       // echo "<pre>";
        //echo "SELECT * from task where task_id ='$Tid' ";
       $getreturnArr = $commonobj->getQry("SELECT * from task where task_id ='$Tid' ");
       $resultArr = $getreturnArr[0];
       $personlist = explode(',',$resultArr['invited_persion']);
       //print_r($resultArr);
      // echo "SELECT * from activity where task_id = '$Tid' and activity_id='$Aid'";
       $getActivityArr = $commonobj->getQry("SELECT * from activity where task_id = '$Tid' and activity_id='$Aid'");
       $activityArr = $getActivityArr[0];
      // print_r($activityArr);
       $getActivitycmntsArr = $commonobj->getQry("SELECT * from activity_comments where task_id = '$Tid' and activity_id='$Aid'");
       //$activitycmntsArr = $getActivitycmntsArr[0];
    }
include("includes/header.php");
?>
<style>
    .form-group {
        margin-bottom: 40px;
    }
    .form-control[disabled]{
        background-color:#f7f7f7
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
                                    <li><a href="#" class="panel-remove"><span class="fa fa-times"></span></a></li>
                                </ul>
                            </div>
                            <!-- <div class="panel-body">Add Task Details for TL</p></div> -->
                            <div class="panel-body ">

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="panel panel-default">
                                           <!--  <div class="panel-heading ui-draggable-handle">
                                                <h3 class="panel-title " style='text-transform: capitalize;'></h3>                                      
                                            </div> -->
                                            <div class="panel-body list-group">
                                                <a href="#" class="list-group-item"><span class="fa fa-calendar"></span> <?=date('d-m-Y',strtotime($resultArr['date']))?><span class="badge badge-danger"></span></a>
                                                <a href="#" class="list-group-item"><span class="fa fa-clock-o"></span><?=$resultArr['time']?></a>                                
                                                <a href="#" class="list-group-item"><span class="fa fa-phone"></span> <?=$resultArr['type_of_meeting']?><span class="badge badge-default"></span></a>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-4">
                                        <div class="panel panel-default">
                                            <div class="panel-body list-group">
                                                <a href="#" class="list-group-item"><span class="fa fa-map-marker"></span> <?=$resultArr['meeting_place']?><span class="badge badge-danger"></span></a>
                                                <a href="#" class="list-group-item"><span class="fa fa-pencil"></span><?=$resultArr['create_by']?></a>                                
                                                <a href="#" class="list-group-item"><span class="fa fa-envelope-o"></span> - <span class="badge badge-default"></span></a>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-md-4">
                                        <div class="panel panel-default">
                                            <div class="panel-body list-group">
                                            <?php foreach ($personlist as $key => $value) { ?>
                                                <a href="#" class="list-group-item"><span class="fa fa-user"></span> <?=$value?><span class="badge badge-danger"></span></a>
                                            <?php }?>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                                <hr>
                                <!-- id="sign-up_area"  -->
                                <form action="#" method="post" role="form">
                                    <input type="hidden" id='hidden_id' name="hidden_id">
                                    <div class="panel-heading ui-draggable-handle">
                                        <ul class="panel-controls">
                                            <i></i>
                                            <button type="button" id="btnAdd" name="btnAdd" <?php echo $activityArr['status']; ?> class="btn btn-info" value="comment"><i class="fa fa-plus"></i>Add section</button>
                                            <button type="button" id="btnDel" name="btnDel" class="btn btn-danger"><i class="fa fa-minus"></i>Remove Last Section</button>
                                            <button id="submit_button" name="submit_button" id='save' class="btn btn-info" onclick="ConfirmDialog('Are you sure');"><i class="fa fa-save"></i>Save</button>
                                        </ul>                                
                                    </div>  
                                    <div id="entry1" class="clonedInput">
                                        <p><h4 id="reference" name="reference" class="heading-reference">Task #1</h4></p>
                                        <fieldset>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label label_ttl">Text Tittle</label>
                                                        <div class="col-md-8">                                            
                                                            <div class="input-group">
															  <input type="hidden" class="form-control" name="task_id" value="<?=$resultArr['task_id']?>">
															  <input type="hidden" class="form-control" name="activity_id" value="<?=$activityArr['activity_id']?>">
                                                              <input type="text" disabled class="form-control select_ttl" name="ID1_title" value="<?=$activityArr['activity_title']?>" id="ID1_title" placeholder="Activity Tittle" required>
                                                              <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                            </div>                                            
                                                            <span class="help-block"></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label label_cmt">Comments</label>
                                                        <div class="col-md-8">  
                                                               <textarea class="form-control input_cmt" disabled placeholder="Do you want to add a message?" rows="5" required name="ID1_comments" id="ID1_comments"><?=$activityArr['comments']?></textarea >            
                                                            <span class="help-block"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    
                                                    <div class="form-group">                                        
                                                        <label class="col-md-5 control-label label_str date" data-provide="datepicker">Start Date</label>
                                                        <div class="col-md-7">
                                                            <div class="input-group date" data-provide="datepicker">
                                                                <input type="text" disabled class="form-control input_str datepicker" value="<?php echo empty($activityArr['start_date'])?date('m/d/Y'):date('m/d/Y',strtotime($activityArr['start_date']))?>" data-date-format="mm/dd/yyyy" data-date-viewmode="years" id='ID1_start_date' name='ID1_start_date' required>
                                                                <div class="input-group-addon" >
                                                                    <span class="glyphicon glyphicon-th"></span>
                                                                </div>
                                                            </div>
                                                            <span class="help-block font-red"</span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">                                        
                                                        <label class="col-md-5 control-label label_ed date" data-provide="datepicker">End Date</label>
                                                        <div class="col-md-7">
														    <div class="input-group date" data-provide="datepicker">
                                                                <input type="text" class="form-control input_ed datepicker" value="<?php echo empty($activityArr['end_date'])?date('m/d/Y'):date('m/d/Y',strtotime($activityArr['end_date']))?>" data-date-format="mm/dd/yyyy" data-date-viewmode="years" id='ID1_end_date' name='ID1_end_date' required>
                                                                <div class="input-group-addon" >
                                                                    <span class="glyphicon glyphicon-th"></span>
                                                                </div>
                                                            </div>
                                                            <span class="help-block font-red"</span>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="col-md-5 control-label label_per">Respective Person</label>
                                                        <div class="col-md-7">  
                                                            <div class="input-group "> 
                                                                <select disabled class="form-control select input_per delet-div" readonly multiple id='ID1_person' name='ID1_person[]'>
                                                                    <option value=''>All</option>
																	<?php
																	$respective_personArr = explode(",",$activityArr['respective_person']);
																	$personArr = array("Option 1","Option 2","Option 3","Option 4","Option 5");
																	foreach($personArr as $per_key => $per_val){
																	if(in_array($per_val,$respective_personArr)){ $selected = "selected"; }else{ $selected = ""; }
																	?>
																		<option value="<?php echo $selected; ?>" <?php echo $selected; ?>><?php echo $per_val; ?></option>
																	<?php } ?>
                                                                </select>
                                                                <span class="input-group-addon"><span class="fa fa-users"></span></span> 
                                                                <span class="help-block error" id="person-error" ></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label label_pr">Priority</label>
                                                        <div class="col-md-9">
                                                            <div class="input-group">  
                                                               <select class="form-control select input_pr" disabled id='ID1_priority' name='ID1_priority'>
                                                                    <option value="P1" <?PHP if($activityArr['priority'] == "P1"){ echo "selected"; }else{ echo ""; } ?>>P1</option>
                                                                    <option value="P2" <?PHP if($activityArr['priority'] == "P2"){ echo "selected"; }else{ echo ""; } ?>>P2</option>
                                                                    <option value="P3" <?PHP if($activityArr['priority'] == "P3"){ echo "selected"; }else{ echo ""; } ?>>P3</option>
                                                                    <option value="P4" <?PHP if($activityArr['priority'] == "P4"){ echo "selected"; }else{ echo ""; } ?>>P4</option>
                                                                </select>
                                                                <span class="input-group-addon"><span class="fa fa-users"></span></span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label label_st">Status</label>
                                                        <div class="col-md-9">
                                                            <div class="input-group">  
                                                               <select class="form-control select input_st " id='ID1_status' name='ID1_status'>
                                                                    <option value="Yet to Start" <?PHP if($activityArr['status'] == "Yet to Start"){ echo "selected"; }else{ echo ""; } ?>>Yet to Start</option>
                                                                    <option value="Process" <?PHP if($activityArr['status'] == "Process"){ echo "selected"; }else{ echo ""; } ?>>Process</option>
                                                                    <option value="Pending" <?PHP if($activityArr['status'] == "Pending"){ echo "selected"; }else{ echo ""; } ?>>Pending</option>
                                                                    <option value="On going" <?PHP if($activityArr['status'] == "On going"){ echo "selected"; }else{ echo ""; } ?>>On going</option>
                                                                    <option value="Complete" <?PHP if($activityArr['status'] == "Complete"){ echo "selected"; }else{ echo ""; } ?>>Complete</option>
                                                                </select>
                                                                <span class="input-group-addon"><span class="fa fa-users"></span></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                        </fieldset>
                                    </div>
									<?php foreach($getActivitycmntsArr as $key => $arrayval){?>
                                    <div id="entry<?=$key+2?>" class="clonedInput">
                                        <p><h4 id="reference" name="reference" class="heading-reference">Comment #<?=$key+1?></h4></p>
                                        <fieldset>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label label_cmt">Comments</label>
                                                        <div class="col-md-8">  
                                                               <textarea class="form-control input_cmt" disabled placeholder="Do you want to add a message?" rows="3" required name="ID<?=$key+2?>_comments" id="ID<?=$key+2?>_comments"><?=$arrayval['comments']?></textarea >            
                                                            <span class="help-block"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    
                                                    <div class="form-group">                                        
                                                        <label class="col-md-2 control-label label_str date" data-provide="datepicker">Date</label>
                                                        <div class="col-md-7">
                                                            <div class="input-group date" data-provide="datepicker">
                                                                <input type="text" disabled class="form-control input_str datepicker" value="<?php echo empty($arrayval['start_date'])?date('m/d/Y'):date('m/d/Y',strtotime($arrayval['start_date']))?>" data-date-format="mm/dd/yyyy" data-date-viewmode="years" id='ID<?=$key+2?>_start_date' name='ID<?=$key+2?>_start_date' required>
                                                            </div>
                                                            <span class="help-block font-red"</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                        </fieldset>
                                    </div>
									<?php } ?>
                                </form>
                                <div id="attribution"></div>
                            </div>
                        </div>
                </div>
            </div> 
        </div>
        <!-- END PAGE CONTENT WRAPPER -->  
<?php include("includes/footer.php"); ?>
 <script type="text/javascript" src="clone-path-activity.js"></script>

<script type="text/javascript">
    $('#hidden_id').val(1);
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