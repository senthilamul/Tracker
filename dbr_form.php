<?php 
include('includes/config.php');
include('includes/session_check.php');
$casenumber = base64_decode($_GET['id']);
$msgid = $_GET['msg_id'];
if($casenumber != ''){
    $resultArr = $commonobj->getQry("SELECT * FROM backlog_rawdata where case_number = '$casenumber' ");
    $resultArr = $resultArr[0];
    $backlogStatus= $commonobj->getQry("SELECT * FROM backlog_daily_status where case_number = '$casenumber'");
}
if(isset($_POST['project'])){

    if(isset($_POST['sub_id']) && $_POST['sub_id'] != ''){
        if($_POST['reason'] == 'Escalated to GEC' || $_POST['reason'] == 'Closure'){
            $updedQru = ", status='0'";
            $Qry1 ="UPDATE `backlog_rawdata` SET `closed_status`='0' where case_number= '$_POST[case_number]'";
            $update = $conn->prepare($Qry1);
            $update->execute();
        }
        $sub_Qry ="UPDATE `backlog_daily_status` SET main_catgry = '$_POST[reason]',sub_catgry ='$_POST[cmd_sub]', update_status='$_POST[cmd]' $updedQru  where id= '$_POST[sub_id]'";
        $upsubtable = $conn->prepare($sub_Qry);
        $upsubtable->execute();
        $msg= '1';
    }else{
        $checkstatus= $commonobj->getQry("SELECT * FROM backlog_daily_status where case_number = '$casenumber' and updated_to='".date('Y-m-d',strtotime($_POST['rec_date']))."' ");

        if(count($checkstatus) == 0){
        
            $Qry = 'INSERT INTO `backlog_daily_status` (`manager_name`, `team`,`case_owner`, `project`, `que_new`,`product_group`, `region`, `case_origin`,`case_open_date` ,`date`,`calendar_week`, `calendar_month`,`calendar_quarter`,`calendar_year`,`case_number`,`main_catgry`,`sub_catgry`,`status`,`update_status`,`updated_by`,`updated_to`) VALUES ("'.$_POST['team_manager'].'","'.$_POST['tlname'].'","'.$_POST['case_owner'].'","'.$_POST['project'].'","'.$_POST['que'].'","'.$_POST['product_group'].'","'.$_POST['region'].'","'.$_POST['case_origin'].'","'.$_POST['open_date'].'","'.$_POST['date'].'","'.$_POST['weekname'].'","'.$_POST['cal_month'].'","'.$_POST['cal_qtr'].'","'.$_POST['cal_year'].'","'.$_POST['case_number'].'","'.$_POST['reason'].'","'.$_POST['cmd_sub'].'","'.$_POST['case_status'].'","'.$_POST['cmd'].'","'.$_SESSION['email'].'","'.date('Y-m-d',strtotime($_POST['rec_date'])).'")';
            $qry = $conn->prepare($Qry);
            $insert = $qry->execute();
            if($_POST['reason'] == 'Escalated to GEC' || $_POST['reason'] == 'Closure'){
                $Qry1 ="UPDATE `backlog_rawdata` SET `closed_status`='0' where case_number= '$_POST[case_number]'";
                $update = $conn->prepare($Qry1);
                $update->execute();
            }
            
            $msg= '1';
        }else{
            $msg= '2';
        }
        
    }
    header('Location:dbr_form.php?id='.base64_decode($_POST['case_number']));

} 
include("includes/header.php");
?>          
<style>
    .form-control[disabled], .form-control[readonly] {
        color: #0a0000;
    }
</style>
        <!-- PAGE CONTENT WRAPPER -->
        <div class="page-content-wrap">
            <div id="prog"></div>
            <div class="row">
                <div class="col-md-12">
                <form class="form-horizontal" method="POST" id='add_form'>
                <input type="hidden" name="_token" value="<?php echo $token; ?>">
                    <input type="hidden" id="weekname" name="weekname" value="<?php echo $resultArr[calendar_week]?>">
                    <input type="hidden" id="cal_month" name="cal_month" value="<?php echo $resultArr[calendar_month]?>">
                    <input type="hidden" id="cal_qtr" name="cal_qtr" value="<?php echo $resultArr[calendar_quarter]?>">
                    <input type="hidden" id="cal_year" name="cal_year" value="<?php echo $resultArr[calendar_year]?>">
                    <input type="hidden" id="team_manager" name="team_manager" value="<?php echo $resultArr[team_manager]?>">
                    <input type="hidden" id="sub_id" name="sub_id" value="<?php echo $resultArr[id]?>">

                    <input type="hidden" id="date" name="date" value="<?php echo $resultArr[date]?>">

                    <div class="panel panel-default">
                        <div class="panel-heading ui-draggable-handle">
                            <h3 class="panel-title"><strong>Backlog</strong> Survey</h3>
                            <ul class="panel-controls">
                                <li><a href="#" class="panel-remove"><span class="fa fa-times"></span></a></li>
                            </ul>
                        </div>
                        <div class="panel-body">
                        <?php if(!empty($msgid)){?>
                           <div class="alert <?php echo $msgid=='1' ? 'alert-success':'alert-danger' ?> "><?php if($msgid =='1'){echo "Backlog Reason Updated"; }else if($msgid =='2'){ echo "Already Updated Selected Date"; } ?></div>
                        <?php }?>
                        </div>
                        <div class="panel-body form-group-separated">                                                                        
                            
                            <div class="row">
                                
                                <div class="col-md-4">
                                    
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Select TL</label>
                                        <div class="col-md-8">                                                                                            
                                            <select class="form-control" onchange="drpdown()" name='tlname' id="tlname">
                                            <option value="">-- Select --</option>
                                               <?php
                                                 $TlList = $commonobj->getQry("SELECT distinct team from backlog_rawdata order by team asc");
                                                    foreach ($TlList as $key => $value) {
                                                     echo'<option value="'.$value['team'].'">'.$value['team'].'</option>';
                                                    }
                                                ?> 
                                            </select>
                                            <script>$('#tlname').val("<?php echo $resultArr['team']?>")</script>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Case Owner</label>
                                        <div class="col-md-8">                                                                                            
                                            <select class="form-control " onchange="drpdown()" name='case_owner' id="case_owner">
                                                <option value="">-- Select --</option>
                                               <?php
                                                 $caseowner = $commonobj->getQry("SELECT distinct case_owner from backlog_rawdata order by case_owner asc");
                                                    foreach ($caseowner as $key => $value) {
                                                     echo'<option value="'.$value['case_owner'].'">'.$value['case_owner'].'</option>';
                                                    }
                                                ?> 
                                            </select>
                                            <script>$('#case_owner').val("<?php echo $resultArr['case_owner']?>")</script>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Case Number</label>
                                        <div class="col-md-8">                                                                                            
                                            <select class="form-control " name='case_number' id="case_number" onchange="selectCase(this.value)">
                                                <option value="">-- Select --</option>
                                               <?php
                                                 $case_numberArr = $commonobj->getQry("SELECT distinct case_number from backlog_rawdata where closed_status = '1' order by case_number asc");
                                                    foreach ($case_numberArr as $key => $value) {
                                                     echo '<option value="'.$value['case_number'].'">'.$value['case_number'].'</option>';
                                                    }
                                                ?> 
                                            </select>
                                            <script>$('#case_number').val("<?php echo $resultArr['case_number']?>")</script>
                                        </div>
                                    </div>
                                    <div class="form-group">                                        
                                        <label class="col-md-3 control-label">Status</label>
                                        <div class="col-md-8 col-xs-12">
                                            <select class="form-control " name='case_status' id="case_status">
                                                <option value="">-- Select --</option>
                                               <?php
                                                 $statusArr = $commonobj->getQry("SELECT distinct status from backlog_rawdata ");
                                                    foreach ($statusArr as $key => $value) {
                                                     echo'<option value="'.$value['status'].'">'.$value['status'].'</option>';
                                                    }
                                                ?> 
                                            </select>           
                                           <script>$('#case_status').val("<?php echo $resultArr['status']?>")</script>
                                        </div>
                                    </div>
                                    <div class="form-group">                                        
                                        <label class="col-md-3 control-label">Region</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name='region' id='region' readonly value="<?=$resultArr[region]?>"> 
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    
                                    <div class="form-group">                                        
                                        <label class="col-md-4 control-label">Case No.</label>
                                        <div class="col-md-7">
                                           <input type="text" class="form-control" name='casenum' id='casenum' disabled value="<?=$resultArr[case_number]?>">
                                        </div>
                                    </div>

                                    <div class="form-group">                                        
                                        <label class="col-md-4 control-label">Opened Date</label>
                                        <div class="col-md-7">
                                           <input type="text" class="form-control" name='open_date' id='open_date' readonly value="<?=$resultArr[opened_date]?>">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">                                        
                                        <label class="col-md-4 control-label">Project</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" name='project' id='project' readonly value="<?=$resultArr[project]?>">  
                                        </div>
                                    </div>
                                    
                                     <div class="form-group">                                        
                                        <label class="col-md-4 control-label">Que</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" name='que' id='que' readonly value="<?=$resultArr[queue]?>">  
                                        </div>
                                    </div>

                                    <div class="form-group">                                        
                                        <label class="col-md-4 control-label">Product Group</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" name='product_group' id='product_group' readonly value="<?=$resultArr[product_group]?>"> 
                                        </div>
                                    </div>
                                    
                                    
                                    
                                </div>


                                <div class="col-md-4">

                                    <div class="form-group">                                        
                                        <label class="col-md-3 control-label">Case Origin</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name='case_origin' id='case_origin' readonly value="<?=$resultArr[case_origin]?>"> 
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">                                        
                                        <label class="col-md-3 control-label">Severity</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name='severity' id='severity' disabled  value="<?=$resultArr[severity]?>"> 
                                        </div>
                                    </div>

                                    <div class="form-group">                                        
                                        <label class="col-md-3 control-label">Product No.</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name='prod_num' id='prod_num' disabled  value="<?=$resultArr[product_number]?>"> 
                                        </div>
                                    </div>
                                    <div class="form-group">                                        
                                        <label class="col-md-3 control-label">Subject</label>
                                        <div class="col-md-8">
                                            <textarea type="text" class="form-control" name='desc' id='desc' disabled rows='4'><?=$resultArr[subject]?> </textarea>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>

                        </div>
                        <div class="panel-footer text-center">
                            <a href="dbr_table.php"><input type='button' class="btn btn-default" value="Listing"></a>
                           <!--  <input type='button' class="btn btn-success" id="clickcmd" value='Add Comments'> -->
                           <span class="btn btn-info" data-toggle="modal" data-target="#modal_basic"><i class='fa fa-pencil'>Add Comment</i></span>
                           <!--  <button class="btn btn-primary ">Submit</button> -->
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading ui-draggable-handle">
                            <h3 class="panel-title">Backlog Reason</h3>
                        </div>
                        <div class="panel-body panel-body-table" id='remove_div'>                                
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>id</th>
                                        <!-- <th>Status</th> -->
                                        <th>Main Category</th>
                                        <th>Sub Category</th>
                                        <th>Comments</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                    <?php $i = 1; foreach ($backlogStatus as $key => $bck_value) { ?>
                                        <td><?=$i?></td>
                                        <!-- <td><?=$bck_value['status']?></td> -->
                                        <td><?=$bck_value['main_catgry']?></td>
                                        <td><?=$bck_value['sub_catgry']?></td>
                                        <td><?=$bck_value['update_status']?></td>
                                        <td><?=date("d-m-Y",strtotime($bck_value['updated_to']))?></td>
                                        <td class=text-center><span class="btn btn-info btn-sm"  data-toggle="modal" data-target="#modal_basic" onclick="edit_fun(<?php echo $bck_value['id'].",'".date("d-m-Y",strtotime($bck_value['updated_to']))."'" ?>)">Edit</button>
                                        </td>
                                      </tr>   
                                   <?php $i++;} ?>
                                </tbody>
                            </table>                                
                        </div>
                    </div>
                    <div class="modal " id="modal_basic" tabindex="-1" role="dialog" aria-labelledby="defModalHead" aria-hidden="true"  data-easein="bounceIn"  >
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                    <h4 class="modal-title" id="defModalHead">Backlog Reason</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="row" id="addcommets">
                                            <div class="form-group">                                        
                                                <label class="col-md-4 control-label">Update Date</label>
                                                <div class="col-md-7">
                                                        <input type="text" class="form-control datepicker" value="<?php echo date('m/d/Y')?>" data-date-format="mm/dd/yyyy" data-date-viewmode="years" id='rec_date' name='rec_date'>
                                                   
                                                </div>
                                            </div>
                                            <div class="form-group">                                        
                                                <label class="col-md-4 control-label">Main Category</label>
                                                <div class="col-md-7 col-xs-12">
                                                    <select class="form-control select" name='reason' id="reason" onchange="LoadCatgry(this.value)">
                                                        <option value="">-- Select --</option>
                                                       <option value="Bug">Bug</option>
                                                       <option value="Closure">Closure</option>
                                                       <option value="Customer">Customer</option>
                                                       <option value="Escalated to GEC">Escalated to GEC</option>
                                                       <option value="Monitoring">Monitoring</option>
                                                       <option value="RMA">RMA</option>
                                                       <option value="TAC">TAC</option>
                                                    </select>
                                                    <span class="help-block " id="error_msg_cat" style='display:none;color:red'>Please Select Category</span>
                                                </div>
                                            </div>                                            
                                            <div class="form-group">                                        
                                                <label class="col-md-4 control-label">Sub Category</label>
                                                <div class="col-md-7 col-xs-12">
                                                    <select class="form-control select" name='cmd_sub' id="cmd_sub" >
                                                    </select>           
                                                    <span class="help-block" id="error_msg" style='display:none;color:red'>Please Select Sub Category</span>
                                                </div>
                                            </div>

                                            <div class="form-group">                                        
                                                <label class="col-md-4 control-label">Comments</label>
                                                <div class="col-md-7">
                                                    <textarea type="text" class="form-control" name='cmd' id='cmd' rows='4'></textarea>
                                                </div>
                                            </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary" id="submit">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>
                    
                </div>
            </div>                
        
        </div>


<?php include("includes/footer.php"); ?>
<script>
$('.datepicker').datepicker({
    format: 'mm/dd/yyyy',
    startDate: '-2d'
});
function drpdown(){
    var tlname = $('#tlname').val()=='' || $('#tlname').val()=='-- Select --'  ? '' : $('#tlname').val() ;
    var caseowner = $('#case_owner').val()=='' || $('#case_owner').val()== '-- Select --' ? '' : $('#case_owner').val() ;
    tlName_caseowner = tlname +'_' + caseowner;
    $.ajax({
        url: 'ajax_outage.php',
        type: 'POST',
        data: {selectList: tlName_caseowner,comefrom:'backlog'},
    }).done(function(output) {

        outputArr = JSON.parse(output);
        $('#case_number').html('');
        $('#case_number').html(outputArr[0]);

        if(outputArr[1] != null && outputArr[1] !=''){
            // console.log(caseowner);
            //  caseowner =='-- Select --' || caseowner=='' ?'':
             $('#case_owner').html(outputArr[1]);
             $('#case_owner').val(caseowner)
        }
    }).fail(function() {
        console.log("error");
    });
}

function selectCase(value) {
  $.ajax({
        url: 'ajax_outage.php',
        type: 'POST',
        data: {caseid: value,comefrom:'backlog_form'},
    })
    .done(function(output) {
       
       output = JSON.parse(output);
       tableArr  = output[1];
       getoutput = output[0];

       if(getoutput.length > 0){
           for (var i = 0; i < getoutput.length; i++) {
                casenum.value = getoutput[i]['case_number'];
                open_date.value = getoutput[i]['opened_date'];
                project.value = getoutput[i]['project'];
                que.value = getoutput[i]['queue'];
                region.value = getoutput[i]['region'];
                case_origin.value = getoutput[i]['case_origin'];
                prod_num.value = getoutput[i]['product_number'];
                product_group.value = getoutput[i]['product_group'];
                desc.value = getoutput[i]['subject'];
                case_status.value = getoutput[i]['status'];
                tlname.value = getoutput[i]['team'];
                case_owner.value = getoutput[i]['case_owner'];
                severity.value = getoutput[i]['severity'];

                cal_month.value = getoutput[i]['calendar_month'];
                cal_qtr.value = getoutput[i]['calendar_quarter'];
                weekname.value = getoutput[i]['calendar_week'];
                cal_year.value = getoutput[i]['calendar_year'];
                team_manager.value = getoutput[i]['team_manager'];
                date.value = getoutput[i]['date'];
           };

           if(tableArr != null && tableArr != ''){
                $('#remove_div').html('');
                $('#remove_div').html(tableArr);
           }else{
                $('#remove_div').html('');
           }
        }else{
               casenum.value = '';
               open_date.value = '';
               project.value = '';
               que.value = '';
               region.value = '';
               case_origin.value = '';
               prod_num.value = '';
               product_group.value = '';
               desc.value = '';
               severity.value='';
               cal_month.value ='';
               cal_qtr.value ='';
               weekname.value ='';
               cal_year.value ='';
        }
    }).fail(function() {
        console.log("error");
    });
}
 // $('#clickcmd').click(function(event) {
 //    if(clickcmd.value == 'Add Comments'){
 //        $('#addcommets').css('display','block');
 //        $('#clickcmd').val('Delete Comments').addClass('btn-danger').removeClass('btn-success');
 //    }else{
 //        $('#addcommets').css('display','none');
 //        $('#clickcmd').val('Add Comments').addClass('btn-success').removeClass('btn-danger');
 //    }
 // });
 function LoadCatgry (value) {
    var Sub_cat;
    if(value == 'Customer'){
        Sub_cat = Array('Awaiting Customer Response','No Response','Awaiting log/Output','Customer OOO','First Follow-up','Second Follow-up','Third Follow-up');
    }else if(value == 'Bug'){
        Sub_cat = Array('Pending Customer','Pending Developer');
    }else if(value == 'Closure'){
        Sub_cat = Array('Case Closed');
    }else if(value == 'Escalated to GEC'){
        Sub_cat = Array('Case Escalated');
    }else if(value == 'Monitoring'){
        Sub_cat = Array('Awaiting Customer Response','Issue Resolved','Awaiting for Issue reoccurrence');
    }else if(value == 'RMA'){
        Sub_cat = Array('Part not available','Dead on Arrival','Incorrect Part','Incorrect Address','Pending WC');
    }else{
        Sub_cat = Array('GSC - Replication','GSC - Log Review','Awaiting TAC Response','L2 Assistance','L3 Assistance','Bug Filing','RMA Initiation','Pending Escalation');
    }
    var optionNew = [];
    Sub_cat = Sub_cat.sort();
    optionNew = "<option value=''>Nothing Selected</option>";
    for(var i=0;i<Sub_cat.length; i++){
        optionNew +="<option value='"+Sub_cat[i]+"'>"+Sub_cat[i]+"</option>";
    }
    $('#cmd_sub').html(optionNew);
    $("#cmd_sub").selectpicker('refresh');
 }
 $('#submit').on('click',function(){
    if(cmd_sub.value =='' || reason.value ==''){
        cmd_sub.value == '' ? $('#error_msg').css('display','block'):$('#error_msg').css('display','none');
        reason.value == '' ? $('#error_msg_cat').css('display','block') :$('#error_msg_cat').css('display','none');
        return false; 
    }
 });
 setTimeout(function() {
    $('.alert').fadeOut('slow');
}, 5000);

 $('.datepicker').datepicker({
        format: 'mm/dd/yyyy',
        // startDate: '+0d'
       maxDate: '0'
    });
 function edit_fun (id,date) {
    sub_id.value = id;
    $('#rec_date').val(date).attr("disabled", 'disabled');
 }
</script>

<script>
    (function($){
        $(window).on("load",function(){
            $(".content").mCustomScrollbar();
        });
    })(jQuery);
</script>








