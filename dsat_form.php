<?php 
include('includes/config.php');
include('includes/session_check.php');
require_once('csat_esc_support.php');
$caseNumber = base64_decode($_GET['id']);
$msg = $_GET['msg'];
if(!empty($caseNumber)){
    $returnArr = $commonobj->getQry("SELECT alert_type,que_new,product_group,region,comments,overall_experience,nps,datetime_closed,cq3_ease_of_access,cq7_technical_ability,cq8_non_technical_performance,cq9_kept_informed,cq10_solution_time,engineer_email_id,tl_tier1,tl_tier2,tl_tier3,tl_comments,manager_name,team,case_owner,case_number,tl_exception,mgr_tier1,mgr_tier2,mgr_tier3,mgr_comments,mgr_exception,nps_tl_tier1,nps_tl_tier2,nps_tl_tier3,nps_tl_comments,nps_tl_exception,nps_mgr_tier1,nps_mgr_tier2,nps_mgr_tier3,nps_mgr_comments,nps_mgr_exception from aruba_csat where case_number = '$caseNumber'");
}
$returnArr = $returnArr[0];
$escreturnArr = $escalationArr[0];
if(isset($_POST['tlname'])){
    extract($_POST);
    if($userType1 == 'TL'){
		if($oe_iradio == "No"){
			$UpdateQry1 = "UPDATE aruba_csat set mgr_exception='$oe_iradio',mgr_status='1',mgr_update_date='$dbdatetime',client_exception='$oe_iradio',client_status='1' where case_number = '$case_number'";
			$upsubtable1 = $conn->prepare($UpdateQry1);
			$insertQey1= $upsubtable1->execute();
		}
        $addslas_cmd = addslashes($nps_tl_cmds);
        $UpdateQry = "UPDATE aruba_csat set tl_tier1='$tier1',tl_tier2='$tier2',tl_tier3='$tier3',tl_comments=".'"'.$tl_cmds.'"'.",tl_exception='$oe_iradio',tl_status='1',tl_update_date='$dbdatetime',nps_tl_tier1='$nps_tier1',nps_tl_tier2='$nps_tier2',nps_tl_tier3='$nps_tier3',nps_tl_comments='$addslas_cmd',nps_tl_exception='$nps_iradio' where case_number = '$case_number'";
        $upsubtable = $conn->prepare($UpdateQry);
        $insertQey= $upsubtable->execute();
    }else if($userType1 == 'Manager'){       
        $UpdateQry = "UPDATE aruba_csat set mgr_tier1='$tier1',mgr_tier2='$tier2',mgr_tier3='$tier3',mgr_comments=".'"'.$tl_cmds.'"'.",mgr_exception='$oe_iradio',mgr_status='1',mgr_update_date='$dbdatetime',nps_mgr_tier1='$nps_tier1',nps_mgr_tier2='$nps_tier2',nps_mgr_tier3='$nps_tier3',nps_mgr_comments='$addslas_cmd',nps_mgr_exception='$nps_iradio' where case_number = '$case_number'";
        $upsubtable = $conn->prepare($UpdateQry);
        $insertQey= $upsubtable->execute();
    }else{
        $exception = empty($oe_iradio)?$nps_iradio :$oe_iradio;
        $cmds = empty($nps_tl_cmds)?$tl_cmds :$nps_tl_cmds;
        $UpdateQry = "UPDATE aruba_csat set client_exception='$exception',client_comments='$addslas_cmd',client_status='1' where case_number = '$case_number'";
        $upsubtable = $conn->prepare($UpdateQry);
        $insertQey = $upsubtable->execute();
    }
    if($insertQey){
        header("Location:dsat_form.php?msg=1");
        exit;
    }else{
        header("Location:dsat_form.php?msg=2");
        exit;
    }
}

include("includes/header.php");
?>          
<style>
    .form-control[disabled], .form-control[readonly] {color: #0a0000;}
    td{background: #b1d6d1 !important;}
</style>
<div class="page-content-wrap">
    <div id="wait" style="display:none;width:49px;height:69px;position:absolute;top:30%;left:50%;padding:2px;z-index: 99999999;"><img src='img/demo_wait.gif' width="64" height="64" /></div>
    <div class="row">
        <ul class="breadcrumb">
            <li><a href="#">OE</a></li>
            <li><a href="esc.php">Escalation</a></li>
        </ul>
        <?php if(!empty($msg)){
            if($msg =='1' ){
            ?>
            <div class="alert alert-success alert-dismissable">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Alert !</strong> Case Survey Completed
            </div>
        <?php }else{ ?>
            <div class="alert alert-error alert-dismissable">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Alert !</strong> Case Not Updated 
            </div>
        <?php } } ?>
        <div class="col-md-12">
            <form class="form-horizontal" method="POST" id='add_form'>
            <input type="hidden" name="_token" value="<?php echo $token; ?>">
            <input type="hidden" id='form_name' name='form_name'>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><strong>CSAT OE/NPS</strong></h3>
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
                                       <select class="form-control select" onchange="drpdown()" name='tlname' id="tlname"  data-live-search="true" >
                                        <option value="">-- Select --</option>
                                           <?php
                                             $TlList = $commonobj->getDistinctQry('team',$filterQry1);
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
                                       <select class="form-control" onchange="drpdown()" name='case_owner' id="case_owner" >
                                            <option value="">-- Select --</option>
                                           <?php
                                              $caseowner = $commonobj->getDistinctQry('case_owner',$filterQry1);
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
                                               $caseowner = $commonobj->getDistinctQry('case_number',$filterQry1);
                                                foreach ($caseowner as $key => $value) {
                                                 echo'<option value="'.$value['case_number'].'">'.$value['case_number'].'</option>';
                                                }
                                            ?> 
                                        </select>
                                        <script>$('#case_number').val("<?php echo $returnArr['case_number']?>")</script>
                                    </div>
                                </div>
                                <div class="row control-oe">
                                    OE:
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Tier 1</label>
                                        <div class="col-md-9">     
                                            <select class="form-control" onchange="tireselect('oe')" name='tier1' id="tier1" required>
                                                <option value="" class="">-- Select --</option>
                                                <option value="Controllable">Controllable</option>
                                                <option value="Un-Controllable">UnControllable</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Tier 2</label>
                                        <div class="col-md-9">     
                                            <select class="form-control" onchange="tireselect('oe')" name='tier2' id="tier2" required>
                                                <option value="">-- Select --</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Tier 3</label>
                                        <div class="col-md-9">     
                                            <select class="form-control"  name='tier3' id="tier3" required>
                                                <option value="">-- Select --</option>
                                               
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Comment</label>
                                        <div class="col-md-9 col-xs-12" required>                                            
                                            <textarea class="form-control" name='tl_cmds' rows="15" required></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 col-xs-12 control-label changelabel">Exception</label>
                                        <div class="col-md-8 col-xs-12">
                                            <div class="col-md-6">                                    
                                                <label class="check"><div class="iradio_minimal-grey checked" style="position: relative;"><input type="radio" class="radio" name="oe_iradio" value="Yes" required></div> Yes</label>
                                            </div>
                                            <div class="col-md-6">                                    
                                                <label class="check"><div class="iradio_minimal-grey checked" style="position: relative;"><input type="radio" class="radio" name="oe_iradio" value="No" required></div> No</label>
                                            </div>
                                            <label id="oe_iradio-error" class="error" for="oe_iradio"></label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row control-nps" >
                                    NPS:
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Tier 1</label>
                                        <div class="col-md-9">     
                                            <select class="form-control" onchange="tireselect('nps')" name='nps_tier1' id="nps_tier1" required>
                                                <option value="" class="">-- Select --</option>
                                                <option value="Controllable">Controllable</option>
                                                <option value="Un-Controllable">Uncontrollable</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Tier 2</label>
                                        <div class="col-md-9">     
                                            <select class="form-control" onchange="tireselect('nps')" name='nps_tier2' id="nps_tier2" required>
                                                <option value="">-- Select --</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Tier 3</label>
                                        <div class="col-md-9">     
                                            <select class="form-control" name='nps_tier3' id="nps_tier3" required>
                                                <option value="">-- Select --</option>
                                               
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Comment</label>
                                        <div class="col-md-9 col-xs-12" required>                                            
                                            <textarea class="form-control" name='nps_tl_cmds' rows="15" required></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                    <label class="col-md-4 col-xs-12 control-label changelabel">Exception</label>
                                    <div class="col-md-8 col-xs-12">
                                        <div class="col-md-6">                                    
                                            <label class="check"><div class="iradio_minimal-grey checked" style="position: relative;"><input type="radio" class="radio" name="nps_iradio" value="Yes" required></div> Yes</label>
                                        </div>
                                        <div class="col-md-6">                                    
                                            <label class="check"><div class="iradio_minimal-grey checked" style="position: relative;"><input type="radio" class="radio" name="nps_iradio" value="No" required></div> No</label>
                                        </div>
                                        <label id="nps_iradio-error" class="error" for="nps_iradio"></label>
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
                                            <th style='font-weight: 900'>Alert Type</th>
                                            <th>Overall Exp</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td id="csat_que"><?php echo empty($returnArr['que_new'])?'-':$returnArr['que_new'] ?></td>
                                            <td id='csat_pro_group'><?php echo empty($returnArr['product_group'])?'-':$returnArr['product_group'] ?></td>
                                            <td id='csat_region'><?php echo empty($returnArr['region'])?'-':$returnArr['region'] ?></td>
                                            <td style='font-weight: 900' class='text-red' id='csat_alert'><?php echo empty($returnArr['alert_type'])?'-':$returnArr['alert_type'] ?></td>
                                            <td id='csat_ovr_exp'><?php echo empty($returnArr['overall_experience'])?'-':$returnArr['overall_experience'] ?></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <table class="table table-bordeGreen table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Closed Date</th>
                                            <th style='font-weight: 900'>NPS</th>
                                            <th>Engineer Email</th>
                                            <th>Comments</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td id='csat_clsd_date'><?php echo empty($returnArr['datetime_closed'])?'-':date('d-M-Y',strtotime($returnArr['datetime_closed'])) ?></td>
                                            <td style='font-weight: 900' id='csat_nps'><?php echo empty($returnArr['nps'])?'-':$returnArr['nps'] ?></td>
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
                                <div class="row control-oe">
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
                                                <th colspan="2">Comments</th>
                                                <th>Exception</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="success">
                                                <td colspan="2" id="csat_lead_cmds"><?php echo empty($returnArr['tl_comments'])?'-':$returnArr['tl_comments'] ?></td>
                                                <td id="csat_lead_exception"><?php echo empty($returnArr['tl_exception'])?'-':$returnArr['tl_exception'] ?></td>
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
                                                 <td id="csat_mgr_tier1"><?php echo empty($returnArr['mgr_tier1'])?'-':$returnArr['mgr_tier1'] ?></td>
                                                <td id="csat_mgr_tier2"><?php echo empty($returnArr['mgr_tier2'])?'-':$returnArr['mgr_tier2'] ?></td>
                                                <td id="csat_mgr_tier3"><?php echo empty($returnArr['mgr_tier3'])?'-':$returnArr['mgr_tier3'] ?></td>
                                            </tr>
                                        </tbody>
                                        <thead>
                                            <tr class="active">
                                                <th colspan="2">Comments</th>
                                                <th>Exception</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="success">
                                                <td colspan="2" id="csat_mgr_cmds"><?php echo empty($returnArr['mgr_comments'])?'-':$returnArr['mgr_comments'] ?></td>
                                                <td id="csat_mgr_exception"><?php echo empty($returnArr['mgr_exception'])?'-':$returnArr['mgr_exception'] ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <hr>
                                <div class="row control-nps">
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
                                                <td id="nps_tl_tier1"><?php echo empty($returnArr['nps_tl_tier1'])?'-':$returnArr['nps_tl_tier1'] ?></td>
                                                <td id="nps_tl_tier2"><?php echo empty($returnArr['nps_tl_tier2'])?'-':$returnArr['nps_tl_tier2'] ?></td>
                                                <td id="nps_tl_tier3"><?php echo empty($returnArr['nps_tl_tier3'])?'-':$returnArr['nps_tl_tier3'] ?></td>
                                            </tr>
                                        </tbody>
                                        <thead>
                                            <tr class="active">
                                                <th colspan="2">Comments</th>
                                                <th>Exception</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="success">
                                                <td colspan="2" id="nps_tl_cmds"><?php echo empty($returnArr['nps_tl_comments'])?'-':$returnArr['nps_tl_comments'] ?></td>
                                                <td id="nps_tl_exception"><?php echo empty($returnArr['nps_tl_exception'])?'-':$returnArr['nps_tl_exception'] ?></td>
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
                                                 <td id="nps_mgr_tier1"><?php echo empty($returnArr['nps_mgr_tier1'])?'-':$returnArr['nps_mgr_tier1'] ?></td>
                                                <td id="nps_mgr_tier2"><?php echo empty($returnArr['nps_mgr_tier2'])?'-':$returnArr['nps_mgr_tier2'] ?></td>
                                                <td id="nps_mgr_tier3"><?php echo empty($returnArr['nps_mgr_tier3'])?'-':$returnArr['nps_mgr_tier3'] ?></td>
                                            </tr>
                                        </tbody>
                                        <thead>
                                            <tr class="active">
                                                <th colspan="2">Comments</th>
                                                <th>Exception</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="success">
                                                <td colspan="2" id="nps_mgr_cmds"><?php echo empty($returnArr['nps_mgr_comments'])?'-':$returnArr['nps_mgr_comments'] ?></td>
                                                <td id="nps_mgr_exception"><?php echo empty($returnArr['nps_mgr_exception'])?'-':$returnArr['nps_mgr_exception'] ?></td>
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
            </form>
        </div>
    </div>                

</div>
<link rel="stylesheet" href="css/jquery-confirm.min.css">
<?php include("includes/footer.php"); ?>
<script src="js/jquery-confirm.min.js" type="text/javascript"></script>
<script src="dropdown_ajax.js" type="text/javascript"></script>
<script>
   var usertype = "<?php echo $userType?>";
    //console.log(usertype);
    if(usertype == 'client'){
        $('#tier1').attr('disabled', 'disabled');
        $('#tier2').attr('disabled', 'disabled');
        $('#tier3').attr('disabled', 'disabled');

        $('#nps_tier1').attr('disabled', 'disabled');
        $('#nps_tier2').attr('disabled', 'disabled');
        $('#nps_tier3').attr('disabled', 'disabled');

        $('.changelabel').text('Accept Exception');
    }

    var AlertType = "<?php echo $returnArr[alert_type] ?>";
    AlertType == 'Green'?$('.control-oe').css('display','none'):'';
    var NpsType = "<?php echo $returnArr[nps] ?>";
    NpsType == 'Promoter'?$('.control-nps').css('display','none'):'';
</script>