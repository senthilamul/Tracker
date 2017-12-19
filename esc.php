<?php 
include('includes/config.php');
//include('includes/session_check.php');
$caseNumber = base64_decode($_GET['id']);
$type = $_GET['type'];
$msg = $_GET['msg'];
if(!empty($caseNumber)){
    $escalationArr = $commonobj->getQry("SELECT `date1`,`case`,team,case_owner,product,queue,wlan_ns,region,tier_1,tier_2,tier_3,tier_4,tier_5,rca_comments,product_group,manager_name,role,tl_exception,mgr_tier_1,mgr_tier_2,mgr_tier_3,mgr_tier_4,mgr_tier_5,mgr_rca_comments,mgr_exception from aruba_esc where `case` = '$caseNumber'");
}
$returnArr = $returnArr[0];
$escreturnArr = $escalationArr[0];
if(isset($_POST['tlname'])){
    extract($_POST);
    if($userType == 'TL'){
        $UpdateQry = "UPDATE aruba_esc set tier_1='$esc_tier1',tier_2='$esc_tier2',tier_3='$esc_tier3',tier_4='$esc_tier4',tier_5='$esc_tier5',rca_comments='$esc_tl_cmds',tl_exception='$iradio',tl_status='1',tl_update_date='$dbdatetime' where `case` = '$esc_case_number'";
        $upsubtable = $conn->prepare($UpdateQry);
        $upsubtable->execute();
    }else{       
        $UpdateQry = "UPDATE aruba_esc set mgr_tier_1='$esc_tier1',mgr_tier_2='$esc_tier2',mgr_tier_3='$esc_tier3',mgr_tier_4='$esc_tier4',mgr_tier_5='$esc_tier5',mgr_rca_comments='$esc_tl_cmds',mgr_exception='$iradio',mgr_status='1',mgr_update_date='$dbdatetime' where `case` = '$esc_case_number'";
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
            <li><a href="dsat_form.php">OE</a></li>
            <li><a href="csat_nps.php">NPS</a></li>
            <li><a href="#">Escalation</a></li>
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