<?php 
include('includes/config.php');
//include('includes/session_check.php');
$caseNumber = base64_decode($_GET['id']);
$type = $_GET['type'];
$msg = $_GET['msg'];
if(!empty($caseNumber)){
    $returnArr = $commonobj->getQry("SELECT alert_type,que_new,product_group,region,comments,overall_experience,nps,datetime_closed,cq3_ease_of_access,cq7_technical_ability,cq8_non_technical_performance,cq9_kept_informed,cq10_solution_time,engineer_email_id,tl_tier1,tl_tier2,tl_tier3,tl_tier4,tl_tier5,tl_comments,manager_name,team,case_owner,case_number from aruba_csat where case_number = '$caseNumber'");
}
$returnArr = $returnArr[0];
$escreturnArr = $escalationArr[0];
//print_r($escreturnArr);
if(isset($_POST['tlname'])){
    extract($_POST);
    if($userType1 == 'TL'){
        $UpdateQry = "UPDATE aruba_csat set tl_tier1='$tier1',tl_tier2='$tier2',tl_tier3='$tier3',tl_tier4='$tier4',tl_tier5='$tier5',tl_comments='$tl_cmds',tl_status='1',tl_update_date='$dbdatetime' where case_number = '$case_number'";
        $upsubtable = $conn->prepare($UpdateQry);
        $upsubtable->execute();
    }else{       
        $UpdateQry = "UPDATE aruba_csat set mgr_tier1='$tier1',mgr_tier2='$tier2',mgr_tier3='$tier3',mgr_tier4='$tier4',mgr_tier5='$tier5',mgr_comments='$tl_cmds',mgr_status='1',mgr_update_date='$dbdatetime' where case_number = '$case_number'";
        $upsubtable = $conn->prepare($UpdateQry);
        $upsubtable->execute();
    }
    header("Location:dsat_form.php?msg=1");
}

include("includes/header.php");
?>          
<style>
    .form-control[disabled], .form-control[readonly] {
        color: #0a0000;
    }
</style>

<div class="page-content-wrap">
    <div id="wait" style="display:none;width:49px;height:69px;position:absolute;top:30%;left:50%;padding:2px;z-index: 99999999;"><img src='img/demo_wait.gif' width="64" height="64" /></div>
    <div class="row">
    <ul class="breadcrumb">
        <li><a href="#">OE</a></li>
        <li><a href="csat_nps.php">NPS</a></li>
        <li><a href="esc.php">Escalation</a></li>
    </ul>
        <div class="col-md-12">
            <form class="form-horizontal" method="POST" id='add_form'>
            <input type="hidden" id='form_name' name='form_name'>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><strong>CSAT OE</strong></h3>
                        <ul class="panel-controls">
                            <li><a href="#" class="panel-remove"><span class="fa fa-times"></span></a></li>
                        </ul>
                    </div>
                    <div class="panel-body">                                                                        
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-md-3 col-xs-12 control-label">Team Leader</label>
                                    <div class="col-md-9 col-xs-12">     
                                       <select class="form-control select" onchange="drpdown()" name='tlname' id="tlname"  data-live-search="true"  required>
                                        <option value="">-- Select --</option>
                                           <?php

                                             $TlList = $commonobj->getQry("SELECT distinct team from aruba_csat where alert_type !='Green' and LENGTH (case_number) > 7 $filterQry  order by team asc");
                                                foreach ($TlList as $key => $value) {
                                                  $select = $value['team'] == $returnArr['team'] ? 'selected':'';
                                                       
                                                 echo'<option value="'.$value['team'].'"'." $select ".' >'.$value['team'].'</option>';
                                                }
                                            ?> 
                                        </select>
                                        <script>$('#tlname').val("<?php echo $returnArr['team']?>")</script>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-3 col-xs-12 control-label">Case Owner</label>
                                    <div class="col-md-9 col-xs-12">     
                                       <select class="form-control" onchange="drpdown()" name='case_owner' id="case_owner" required>
                                            <option value="">-- Select --</option>
                                           <?php

                                              $caseowner = $commonobj->getQry("SELECT distinct case_owner from aruba_csat where alert_type !='Green' and LENGTH (case_number) > 7 $filterQry  order by case_owner asc");
                                              foreach ($caseowner as $key => $value) {
                                               echo '<option value="'. $value['case_owner'].'"'." $select ".'>'.$value['case_owner'].'</option>';
                                              }
                                          ?> 
                                      </select>
                                        <script>$('#case_owner').val("<?php echo $returnArr['case_owner']?>")</script>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-3 control-label">Ticket No.</label>
                                    <div class="col-md-9">     
                                        <select class="form-control" name='case_number' id="case_number" onchange="selectCase(this.value)" required>
                                            <option value="">-- Select --</option>
                                           <?php
                                                $caseowner = $commonobj->getQry("SELECT distinct case_number from aruba_csat where alert_type !='Green' and  LENGTH (case_number) > 7 $filterQry order by case_number asc");
                                                foreach ($caseowner as $key => $value) {
                                                 echo'<option value="'.$value['case_number'].'">'.$value['case_number'].'</option>';
                                                }
                                            ?> 
                                        </select>
                                        <script>$('#case_number').val("<?php echo $returnArr['case_number']?>")</script>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-3 control-label">Tier 1</label>
                                    <div class="col-md-9">     
                                        <select class="form-control" onchange="tireselect()" name='tier1' id="tier1" required>
                                            <option value="" class="">-- Select --</option>
                                            <option value="Controllable">Controllable</option>
                                            <option value="Uncontrollable">Uncontrollable</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-3 control-label">Tier 2</label>
                                    <div class="col-md-9">     
                                        <select class="form-control" onchange="tireselect()" name='tier2' id="tier2" required>
                                            <option value="">-- Select --</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-3 control-label">Tier 3</label>
                                    <div class="col-md-9">     
                                        <select class="form-control" onchange="tireselect()" name='tier3' id="tier3" required>
                                            <option value="">-- Select --</option>
                                           
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-3 col-xs-12 control-label">Comment</label>
                                    <div class="col-md-9 col-xs-12">                                            
                                        <textarea class="form-control" name='tl_cmds' rows="12"></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 col-xs-12 control-label">Exception</label>
                                    <div class="col-md-9 col-xs-12">
                                        <div class="col-md-6">                                    
                                            <label class="check"><div class="iradio_minimal-grey checked" style="position: relative;"><input type="radio" class="radio" name="iradio" value="Yes"></div> Yes</label>
                                        </div>
                                        <div class="col-md-6">                                    
                                            <label class="check"><div class="iradio_minimal-grey checked" style="position: relative;"><input type="radio" class="radio" name="iradio" value="No"></div> No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h5 class="panel-title"><strong>Case</strong> Details </h5>
                                <table class="table table-bordeGreen table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Queue</th>
                                            <th>Product Group</th>
                                            <th>Region</th>
                                            <th>Alert Type</th>
                                            <th>Overall Exp</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td id="csat_que"><?php echo empty($returnArr['que_new'])?'-':$returnArr['que_new'] ?></td>
                                            <td id='csat_pro_group'><?php echo empty($returnArr['product_group'])?'-':$returnArr['product_group'] ?></td>
                                            <td id='csat_region'><?php echo empty($returnArr['region'])?'-':$returnArr['region'] ?></td>
                                            <td id='csat_alert'><?php echo empty($returnArr['alert_type'])?'-':$returnArr['alert_type'] ?></td>
                                            <td id='csat_ovr_exp'><?php echo empty($returnArr['overall_experience'])?'-':$returnArr['overall_experience'] ?></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <table class="table table-bordeGreen table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Closed Date</th>
                                            <th>NPS</th>
                                            <th>Engineer Email</th>
                                            <th>Comments</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td id='csat_clsd_date'><?php echo empty($returnArr['datetime_closed'])?'-':date('d-M-Y',strtotime($returnArr['datetime_closed'])) ?></td>
                                            <td id='csat_nps'><?php echo empty($returnArr['nps'])?'-':$returnArr['nps'] ?></td>
                                            <td id='csat_email'><?php echo empty($returnArr['engineer_email_id'])?'-':$returnArr['engineer_email_id'] ?></td>
                                            <td id='csat_cmd'><?php echo empty($returnArr['comments'])?'-':$returnArr['comments'] ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="table table-bordeGreen table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Ease of Access</th>
                                            <th>Technical Ability</th>
                                            <th>Non Tech Performance</th>
                                            <th>Kept Informed</th>
                                            <th>Solution Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td id='csat_esy_use'><?php echo empty($returnArr['cq3_ease_of_access'])?'-':$returnArr['cq3_ease_of_access'] ?></td>
                                            <td id='csat_tech_abl'><?php echo empty($returnArr['cq7_technical_ability'])?'-':$returnArr['cq7_technical_ability'] ?></td>
                                            <td id='csat_non_tec_per'><?php echo empty($returnArr['cq8_non_technical_performance'])?'-':$returnArr['cq8_non_technical_performance'] ?></td>
                                            <td id='csat_kept_info'><?php echo empty($returnArr['cq9_kept_informed'])?'-':$returnArr['cq9_kept_informed'] ?></td>
                                            <td id='csat_solution_time'><?php echo empty($returnArr['cq10_solution_time'])?'-':$returnArr['cq10_solution_time'] ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                                
                                <h5 class="panel-title"><strong>Lead's</strong> Review </h5>
                                   
                                <table class="table table-bordeGreen table-striped table-bordered">
                                    <thead>
                                        <tr class="active">
                                            <th>Tier 1</th>
                                            <th>Tier 2</th>
                                            <th>Tier 3</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="success">
                                            <td id="csat_lead_tier1"><?php echo empty($returnArr['tl_tier1'])?'-':$returnArr['tl_tier1'] ?></td>
                                            <td id="csat_lead_tier2"><?php echo empty($returnArr['tl_tier2'])?'-':$returnArr['tl_tier2'] ?></td>
                                            <td id="csat_lead_tier3"><?php echo empty($returnArr['tl_tier3'])?'-':$returnArr['tl_tier3'] ?></td>
                                        </tr>
                                    </tbody>
                                    <thead>
                                        <tr class="active">
                                            <th>Tier 4</th>
                                            <th>Tier 5</th>
                                            <th>Comments</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="success">
                                            <td id="csat_lead_tier4"><?php echo empty($returnArr['tl_tier4'])?'-':$returnArr['tl_tier4'] ?></td>
                                            <td id="csat_lead_tier5"><?php echo empty($returnArr['tl_tier5'])?'-':$returnArr['tl_tier5'] ?></td>
                                            <td id="csat_lead_cmds"><?php echo empty($returnArr['tl_comments'])?'-':$returnArr['tl_comments'] ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                               
                                <h5 class="panel-title"><strong>Manager's</strong> Review </h5>
                                <table class="table table-bordeGreen table-striped table-bordered">
                                    <thead>
                                        <tr class="active">
                                            <th>Tier 1</th>
                                            <th>Tier 2</th>
                                            <th>Tier 3</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="success">
                                            <td id="csat_lead_tier1">-</td>
                                            <td id="csat_lead_tier2">-</td>
                                            <td id="csat_lead_tier3">-</td>
                                        </tr>
                                    </tbody>
                                    <thead>
                                        <tr class="active">
                                            <th>Tier 4</th>
                                            <th>Tier 5</th>
                                            <th>Comments</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="success">
                                            <td id="csat_lead_tier4">-</td>
                                            <td id="csat_lead_tier5">-</td>
                                            <td id="csat_lead_cmds">-</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                        <div class="panel-footer">     
                            <a href="dsat_esc_pending.php"><input type='button' class="btn btn-danger" id= '' value='Pending List'></a>                                                                   
                            <input type='button' class="btn btn-primary pull-right" id= 'sve-btn' value='Save Changes'>
                        </div>
                </div>
            </form>
        </div>
    </div>                

</div>
<link rel="stylesheet" href="css/jquery-confirm.min.css">
<?php include("includes/footer.php"); ?>
<script src="js/jquery-confirm.min.js" type="text/javascript"></script>
<script src="dropdown_ajax.js" type="text/javascript"></script>