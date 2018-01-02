<?php
include('includes/config.php');
require_once('csat_esc_support.php');
if(isset($_POST['selectList']) && $_POST['comefrom'] == 'csat' ){
	$explodeArr = explode("_",$_POST['selectList']);
    //$explodeArr = explode("_",'Abigail Alba_');
	$Qry= empty($explodeArr[0])?'':" where team = '$explodeArr[0]'";
	if(empty($Qry)){
		$Qry.= empty($explodeArr[1])?"":" where case_owner = '$explodeArr[1]' ";
	}else{
		$Qry.= empty($explodeArr[1])?"":" and case_owner = '$explodeArr[1]' ";
	}
	
	$query = $commonobj->getQry("SELECT DISTINCT case_number FROM aruba_csat $Qry and  (alert_type in ('Normal','Red') or nps in ('Passive','Red')) and LENGTH (case_number) > 7 $filterQry1 order by case_number asc");
	$casenumber[]='<option>-- Select --</option>';		
    foreach($query as $weeknamearr){
       $casenumber[]='<option value="'.$weeknamearr['case_number'].'">'.$weeknamearr['case_number'].'</option>'; 
    }
    if($explodeArr[0] != ''){
	    $query1 = $commonobj->getQry("SELECT DISTINCT case_owner FROM aruba_csat where team = '$explodeArr[0]' and LENGTH (case_number) > 7 and (alert_type in ('Normal','Red') or nps in ('Passive','Red')) $filterQry1 order by case_owner asc");
	    $teamArr[] ='<option>-- Select --</option>';
	    foreach($query1 as $teamnane){
	       $teamArr[]='<option value="'.$teamnane['case_owner'].'">'.$teamnane['case_owner'].'</option>'; 
	    }
	}
    echo json_encode(array('0'=>$casenumber,'1'=>$teamArr));
}
if(isset($_POST['caseid']) && $_POST['comefrom'] == 'csat_form' ){
	$case_num = $_POST['caseid'];
	//$case_num = '5325042654';
	$query = $commonobj->getQry("SELECT alert_type,que_new,product_group,region,comments,overall_experience,nps,datetime_closed,cq3_ease_of_access,cq7_technical_ability,cq8_non_technical_performance,cq9_kept_informed,cq10_solution_time,engineer_email_id,tl_tier1,tl_tier2,tl_tier3,tl_comments,tl_exception,mgr_tier1,mgr_tier2,mgr_tier3,mgr_comments,mgr_exception,nps_tl_tier1,nps_tl_tier2,nps_tl_tier3,nps_tl_comments,nps_tl_exception,nps_mgr_tier1,nps_mgr_tier2,nps_mgr_tier3,nps_mgr_comments,nps_mgr_exception FROM aruba_csat where case_number = '$case_num' $filterQry1 ");
    echo json_encode(array('0'=>$query));

}

if (isset($_POST['selectdropdown']) && $_POST['comefrom'] == 'csat_esc') {

    $tier = explode("_", $_POST['selectdropdown']);
    // $val = 'Controllable__';
    // $tier = explode("_",$val);
    $drp2 = $commonobj->arrayColumn($commonobj->getQry("SELECT distinct tier2 From csat_tier where tier1= '$tier[0]'"),'','tier2');
    $drp3 = $commonobj->arrayColumn($commonobj->getQry("SELECT distinct tier3 From csat_tier where tier1= '$tier[0]' and tier2= '$tier[1]'"),'','tier3');
    echo json_encode(array('2' => $drp2, '3' => $drp3));
}

if (isset($_POST['report']) && $_POST['comefrom'] == 'dsat_trend') {
   $report = $_POST['report'];
   $returnArr = $commonobj->arrayColumn($commonobj->getQry("SELECT distinct $report from aruba_csat order by id desc"),'',$report);
   echo json_encode($returnArr);
}

if(isset($_POST['selectList']) && $_POST['comefrom'] == 'escalation' ){
    $explodeArr = explode("_",$_POST['selectList']);
    //$explodeArr = explode("_",'Abigail Alba_');
    $Qry= empty($explodeArr[0])?'':" where team = '$explodeArr[0]'";
    if(empty($Qry)){
        $Qry.= empty($explodeArr[1])?"":" where case_owner = '$explodeArr[1]' ";
    }else{
        $Qry.= empty($explodeArr[1])?"":" and case_owner = '$explodeArr[1]' ";
    }
    
    $query = $commonobj->getQry("SELECT DISTINCT `case` FROM aruba_esc  $Qry and LENGTH (`case`) > 7 $filterQry order by `case` asc");
    $casenumber[]='<option>-- Select --</option>';      
    foreach($query as $weeknamearr){
       $casenumber[]='<option value="'.$weeknamearr['case'].'">'.$weeknamearr['case'].'</option>'; 
    }
    if($explodeArr[0] != ''){
        $query1 = $commonobj->getQry("SELECT DISTINCT case_owner FROM aruba_esc where team = '$explodeArr[0]' and LENGTH (`case`) > 7 $filterQry order by case_owner asc");
        $teamArr[] ='<option>-- Select --</option>';
        foreach($query1 as $teamnane){
           $teamArr[]='<option value="'.$teamnane['case_owner'].'">'.$teamnane['case_owner'].'</option>'; 
        }
    }
    echo json_encode(array('0'=>$casenumber,'1'=>$teamArr));
}
if(isset($_POST['caseid']) && $_POST['comefrom'] == 'escalation' ){
    $case_num = $_POST['caseid'];
    //$case_num = '5314647099';
    $query = $commonobj->getQry("SELECT wlan_ns,product,queue,region,tier_1,tier_2,tier_3,tier_4,tier_5,rca_comments,`date`,manager_name,role,tl_exception,mgr_tier_1,mgr_tier_2,mgr_tier_3,mgr_tier_4,mgr_tier_5,mgr_rca_comments,mgr_exception FROM aruba_esc where `case` = '$case_num'  and LENGTH (`case`) > 7 $filterQry ");
    echo json_encode(array('0'=>$query));
}
if (isset($_POST['selectdropdown']) && $_POST['comefrom'] == 'esc_tier_ajax') {

    $tier = explode("_", $_POST['selectdropdown']);
    //$tier = explode("_",'Controllable_Aruba_Product_Bad Scan_');
    $drp2 = $commonobj->arrayColumn($commonobj->getQry("SELECT distinct tier2 From esc_tier where tier1= '$tier[0]'"),'','tier2');
    $drp3 = $commonobj->arrayColumn($commonobj->getQry("SELECT distinct tier3 From esc_tier where tier1= '$tier[0]' and tier2= '$tier[1]'"),'','tier3');
    $drp4 = $commonobj->arrayColumn($commonobj->getQry("SELECT distinct tier4 From esc_tier where tier1= '$tier[0]' and tier2= '$tier[1]' and tier3= '$tier[2]'"),'','tier4');
    
    $drp5 = $commonobj->arrayColumn($commonobj->getQry("SELECT distinct tier5 From esc_tier where tier1= '$tier[0]' and tier2= '$tier[1]' and tier3= '$tier[2]' and tier4 = '$tier[3]'"),'','tier5');
    
    echo json_encode(array('2' => $drp2, '3' => $drp3, '4' => $drp4, '5' => $drp5));
}
?>