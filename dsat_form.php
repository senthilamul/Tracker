<?php 
include('includes/config.php');
//include('includes/session_check.php');
$caseNumber = base64_decode($_GET['id']);
$type = $_GET['type'];
$msg = $_GET['msg'];
if(!empty($caseNumber)){
    if($type == 'csat'){
        $returnArr = $commonobj->getQry("SELECT alert_type,que_new,product_group,region,comments,overall_experience,nps,datetime_closed,cq3_ease_of_access,cq7_technical_ability,cq8_non_technical_performance,cq9_kept_informed,cq10_solution_time,engineer_email_id,tl_tier1,tl_tier2,tl_tier3,tl_tier4,tl_tier5,tl_comments,manager_name,team,case_owner,case_number from aruba_csat where case_number = '$caseNumber'");
    } else if($type == 'esc') {
        $escalationArr = $commonobj->getQry("SELECT `date1`,`case`,team,case_owner,product,queue,wlan_ns,region,tier_1,tier_2,tier_3,tier_4,tier_5,rca_comments,product_group,manager_name,role,tl_exception,mgr_tier_1,mgr_tier_2,mgr_tier_3,mgr_tier_4,mgr_tier_5,mgr_rca_comments,mgr_exception from aruba_esc where `case` = '$caseNumber'");
    }
}
$returnArr = $returnArr[0];
$escreturnArr = $escalationArr[0];
//print_r($escreturnArr);
if(isset($_POST['tlname'])){
    extract($_POST);
    if( $form_name == 'csat'){
        if($userType1 == 'TL'){
            $UpdateQry = "UPDATE aruba_csat set tl_tier1='$tier1',tl_tier2='$tier2',tl_tier3='$tier3',tl_tier4='$tier4',tl_tier5='$tier5',tl_comments='$tl_cmds',tl_status='1',tl_update_date='$dbdatetime' where case_number = '$case_number'";
            $upsubtable = $conn->prepare($UpdateQry);
            $upsubtable->execute();
        }else{       
            $UpdateQry = "UPDATE aruba_csat set mgr_tier1='$tier1',mgr_tier2='$tier2',mgr_tier3='$tier3',mgr_tier4='$tier4',mgr_tier5='$tier5',mgr_comments='$tl_cmds',mgr_status='1',mgr_update_date='$dbdatetime' where case_number = '$case_number'";
            $upsubtable = $conn->prepare($UpdateQry);
            $upsubtable->execute();
        }
    } else {
        if($userType == 'TL'){
            $UpdateQry = "UPDATE aruba_esc set tier_1='$esc_tier1',tier_2='$esc_tier2',tier_3='$esc_tier3',tier_4='$esc_tier4',tier_5='$esc_tier5',rca_comments='$esc_tl_cmds',tl_exception='$iradio',tl_status='1',tl_update_date='$dbdatetime' where `case` = '$esc_case_number'";
            $upsubtable = $conn->prepare($UpdateQry);
            $upsubtable->execute();
        }else{       
            $UpdateQry = "UPDATE aruba_esc set mgr_tier_1='$esc_tier1',mgr_tier_2='$esc_tier2',mgr_tier_3='$esc_tier3',mgr_tier_4='$esc_tier4',mgr_tier_5='$esc_tier5',mgr_rca_comments='$esc_tl_cmds',mgr_exception='$iradio',mgr_status='1',mgr_update_date='$dbdatetime' where `case` = '$esc_case_number'";
            $upsubtable = $conn->prepare($UpdateQry);
            $upsubtable->execute();
        }

    }
    echo $UpdateQry;
    exit;
    // $msg = 'DSAT Survey Updated';
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
        <div class="col-md-12">
            <form class="form-horizontal" method="POST" id='add_form'>
            <input type="hidden" id='form_name' name='form_name'>
                <div class="page-content-wrap">
                    <div class="panel-heading ui-draggable-handle">
                        <h4 class="panel-title"><strong>CSAT/Escalation</strong> Survey </h4>
                        <ul class="panel-controls">
                            <li><a href="#" class="panel-remove"><span class="fa fa-times"></span></a></li>
                        </ul>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                        <?php if($msg!=''){?>
                            <div class="alert alert-success">
                                <div><?php echo "Survey Completed for the Ticket"?></div>
                            </div>
                        <?php }?>                          
                            <div class="panel panel-default tabs">                            
                                <ul class="nav nav-tabs" role="tablist">
                                <?php 
                                    if(!empty($caseNumber)){
                                        $csatclass = $type == 'csat' ?'active':'';
                                        $escclass = $type == 'esc' ?'active':'';
                                        $csatnps = $type == 'esc' ?'active':'';

                                        $tabcsatclass = $type == 'csat' ?'tab-pane active':'tab-pane';
                                        $tabescclass = $type == 'esc' ?'tab-pane active':'tab-pane';
                                        $tabnpsclass = $type == 'esc' ?'tab-pane active':'tab-pane';

                                    }
                                ?>
                                    <li class="<?php echo empty($caseNumber)?'active':$csatclass ?>"><a href="#tab-first" role="tab" data-toggle="tab" name='csat' onclick="getfun(this.name)">CSAT</a></li>
                                    <li class="<?php echo empty($caseNumber)?'active':$csatnps ?>"><a href="#tab-second" role="tab" data-toggle="tab" name='csat' onclick="getfun(this.name)">CSAT</a></li>

                                    <li class="<?php echo empty($caseNumber)?'':$escclass ?>"><a href="#tab-third" role="tab" data-toggle="tab" name='esc' onclick="getfun(this.name)">Escalation</a></li>
                                </ul>
                                <div class="panel-body tab-content">
                                    <div class="<?php echo empty($caseNumber)?'tab-pane active':$tabcsatclass ?>" id="tab-first">
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
                                                    <label class="col-md-3 control-label">Tier 4</label>
                                                    <div class="col-md-9">     
                                                        <select class="form-control" onchange="tireselect()" name='tier4' id="tier4" required>
                                                            <option value="">-- Select --</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Tier 5</label>
                                                    <div class="col-md-9">     
                                                        <select class="form-control" onchange="tireselect()" name='tier5' id="tier5" required>
                                                            <option value="">-- Select --</option>
                                                           
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-md-3 col-xs-12 control-label">Comment</label>
                                                    <div class="col-md-9 col-xs-12">                                            
                                                        <textarea class="form-control" name='tl_cmds' rows="5"></textarea>
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
                                               
                                                <!-- <h5 class="panel-title"><strong>Manager's</strong> Review </h5>
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
                                                </table>    -->
                                                
                                            </div>


                                        </div>
                                    </div>
                                    <div class="<?php echo empty($caseNumber)?'tab-pane active':$tabnpsclass ?>" id="tab-second">
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
                                                    <label class="col-md-3 control-label">Tier 4</label>
                                                    <div class="col-md-9">     
                                                        <select class="form-control" onchange="tireselect()" name='tier4' id="tier4" required>
                                                            <option value="">-- Select --</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Tier 5</label>
                                                    <div class="col-md-9">     
                                                        <select class="form-control" onchange="tireselect()" name='tier5' id="tier5" required>
                                                            <option value="">-- Select --</option>
                                                           
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-md-3 col-xs-12 control-label">Comment</label>
                                                    <div class="col-md-9 col-xs-12">                                            
                                                        <textarea class="form-control" name='tl_cmds' rows="5"></textarea>
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
                                               
                                                <!-- <h5 class="panel-title"><strong>Manager's</strong> Review </h5>
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
                                                </table>    -->
                                                
                                            </div>


                                        </div>
                                    </div>
                                    <div class="<?php echo empty($caseNumber)?'tab-pane':$tabescclass ?>" id="tab-third">
                                       <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="col-md-3 col-xs-12 control-label">Team Leader</label>
                                                    <div class="col-md-9 col-xs-12">     
                                                       <select class="form-control select" onchange="esc_drpdown()" name='esc_tlname' id="esc_tlname"  data-live-search="true" required>
                                                        <option value="">-- Select --</option>
                                                           <?php
                                                             $TlList = $commonobj->getQry("SELECT distinct team from aruba_esc where LENGTH (`case`) > 7 $filterQry  order by team asc");
                                                                foreach ($TlList as $key => $value) {
                                                                    $select = $escreturnArr['team'] == $value['team'] ?'selected ':'';
                                                                 echo'<option value="'.$value['team'].'"'.$select.'>'.$value['team'].'</option>';
                                                                }
                                                            ?> 
                                                        </select>

                                                    </div>
                                                </div>
                                                

                                                <div class="form-group">
                                                    <label class="col-md-3 col-xs-12 control-label">Case Owner</label>
                                                    <div class="col-md-9 col-xs-12">     
                                                       <select class="form-control" onchange="esc_drpdown()" name='esc_case_owner' id="esc_case_owner" required>
                                                            <option value="">-- Select --</option>
                                                           <?php
                                                              $caseowner = $commonobj->getQry("SELECT distinct case_owner from aruba_esc where LENGTH (`case`) > 7 $filterQry  order by case_owner asc");;
                                                              foreach ($caseowner as $key => $value) {
                                                               echo'<option value="'.$value['case_owner'].'">'.$value['case_owner'].'</option>';
                                                              }
                                                          ?> 
                                                      </select>
                                                        <script>$('#esc_case_owner').val("<?php echo $escreturnArr['case_owner']?>")</script>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Ticket No.</label>
                                                    <div class="col-md-9" >     
                                                        <select class="form-control" name='esc_case_number' id="esc_case_number" onchange="esc_selectCase(this.value)" required>
                                                            <option value="">-- Select --</option>
                                                           <?php
                                                                $caseowner = $commonobj->getQry("SELECT distinct `case` from aruba_esc where LENGTH (`case`) > 7 $filterQry order by `case` asc");
                                                                foreach ($caseowner as $key => $value) {
                                                                 echo'<option value="'.$value['case'].'">'.$value['case'].'</option>';
                                                                }
                                                            ?> 
                                                        </select>
                                                        <script>$('#esc_case_number').val("<?php echo $escreturnArr['case']?>")</script>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Tier 1</label>
                                                    <div class="col-md-9">     
                                                        <select class="form-control" onchange="esc_tireselect()" name='esc_tier1' id="esc_tier1" required>
                                                            <option value="" class="">-- Select --</option>
                                                            <option value="Controllable">Controllable</option>
                                                            <option value="Uncontrollable">Uncontrollable</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Tier 2</label>
                                                    <div class="col-md-9">     
                                                        <select class="form-control" onchange="esc_tireselect()" name='esc_tier2' id="esc_tier2" required>
                                                            <option value="">-- Select --</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Tier 3</label>
                                                    <div class="col-md-9">     
                                                        <select class="form-control" onchange="esc_tireselect()" name='esc_tier3' id="esc_tier3" required>
                                                            <option value="">-- Select --</option>
                                                           
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Tier 4</label>
                                                    <div class="col-md-9">     
                                                        <select class="form-control" onchange="esc_tireselect()" name='esc_tier4' id="esc_tier4" required>
                                                            <option value="">-- Select --</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Tier 5</label>
                                                    <div class="col-md-9">     
                                                        <select class="form-control" onchange="esc_tireselect()" name='esc_tier5' id="esc_tier5" required>
                                                            <option value="">-- Select --</option>
                                                           
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-md-3 col-xs-12 control-label">Comment</label>
                                                    <div class="col-md-9 col-xs-12" required>
                                                    <?php if($val){?>                                        
                                                        <textarea class="form-control" name='esc_tl_cmds' rows="10"></textarea>
                                                    <?php }else{ ?>
                                                        <textarea class="form-control" name='esc_tl_cmds' rows="8"></textarea>
                                                    <?php    } ?>
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
                                                            <th>Project</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td id="esc_que"><?php echo empty($escreturnArr['queue'])?'-':$escreturnArr['queue'] ?></td>
                                                            <td id='esc_pro_group'><?php echo empty($escreturnArr['product'])?'-':$escreturnArr['product'] ?></td>
                                                            <td id='esc_region'><?php echo empty($escreturnArr['region'])?'-':$escreturnArr['region'] ?></td>
                                                            <td id='esc_project'><?php echo empty($escreturnArr['wlan_ns'])?'-':$escreturnArr['wlan_ns'] ?></td>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                                <table class="table table-bordeGreen table-striped table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Escalation Date</th>
                                                            <th>Manger Name</th>
                                                            <th>Role</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td id='esc_clsd_date'><?php echo empty($escreturnArr['date1'])?'-':date('d-M-Y',strtotime($escreturnArr['date1'])) ?></td>
                                                            <td id='esc_mgr_name'><?php echo empty($escreturnArr['manager_name'])?'-':$escreturnArr['manager_name'] ?></td>
                                                            <td id='esc_role'><?php echo empty($escreturnArr['role'])?'-':$escreturnArr['role'] ?></td>
                                                            
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
                                                            <th>Tier 4</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr class="success">
                                                            <td id="esc_lead_tier1"><?php echo empty($escreturnArr['tier_1'])?'-':$escreturnArr['tier_1'] ?></td>
                                                            <td id="esc_lead_tier2"><?php echo empty($escreturnArr['tier_2'])?'-':$escreturnArr['tier_2'] ?></td>
                                                            <td id="esc_lead_tier3"><?php echo empty($escreturnArr['tier_3'])?'-':$escreturnArr['tier_3'] ?></td>
                                                            <td id="esc_lead_tier4"><?php echo empty($escreturnArr['tier_4'])?'-':$escreturnArr['tier_4'] ?></td>
                                                        </tr>
                                                    </tbody>
                                                    <thead>
                                                        <tr class="active">
                                                            <th>Tier 5</th>
                                                            <th colspan="2">Comments</th>
                                                            <th>Exception</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr class="success">
                                                            
                                                            <td id="esc_lead_tier5"><?php echo empty($escreturnArr['tier_5'])?'-':$escreturnArr['tier_5'] ?></td>
                                                            <td colspan="2" id="esc_lead_cmds"><?php echo empty($escreturnArr['rca_comments'])?'-':$escreturnArr['rca_comments'] ?></td>
                                                            <td id="esc_lead_excep"><?php echo empty($escreturnArr['tl_exception'])?'-':$escreturnArr['tl_exception'] ?></td>
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
                                                            <th>Tier 4</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr class="success">
                                                            <td id="esc_mgr_tier1"><?php echo empty($escreturnArr['mgr_tier_1'])?'-':$escreturnArr['mgr_tier_1'] ?></td>
                                                            <td id="esc_mgr_tier2"><?php echo empty($escreturnArr['mgr_tier_2'])?'-':$escreturnArr['mgr_tier_2'] ?></td>
                                                            <td id="esc_mgr_tier3"><?php echo empty($escreturnArr['mgr_tier_3'])?'-':$escreturnArr['mgr_tier_3'] ?></td>
                                                            <td id="esc_mgr_tier4"><?php echo empty($escreturnArr['mgr_tier_4'])?'-':$escreturnArr['mgr_tier_4'] ?></td>
                                                        </tr>
                                                    </tbody>
                                                    <thead>
                                                        <tr class="active">
                                                            <th>Tier 5</th>
                                                            <th colspan="2">Comments</th>
                                                            <th>Exception</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr class="success">
                                                            <td id="esc_mgr_tier5"><?php echo empty($escreturnArr['mgr_tier_5'])?'-':$escreturnArr['mgr_tier_5'] ?></td>
                                                            <td colspan="2" id="esc_mgr_cmds"><?php echo empty($escreturnArr['mgr_rca_comments'])?'-':$escreturnArr['mgr_rca_comments'] ?></td>
                                                            <td id="esc_mgr_excep"><?php echo empty($escreturnArr['mgr_exception'])?'-':$escreturnArr['mgr_exception'] ?></td>
                                                        </tr>
                                                    </tbody>
                                                </table>  
                                            </div>
                                        </div>
                                    </div>                                        
                                </div>
                                <div class="panel-footer">     
                                    <a href="dsat_esc_pending.php"><input type='button' class="btn btn-danger" id= '' value='Pending List'></a>                                                                   
                                    <input type='button' class="btn btn-primary pull-right" id= 'sve-btn' value='Save Changes'>
                                </div>
                            </div>   
                        
                        </div>
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