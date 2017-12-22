<?php
include "includes/config.php";
if(isset($_POST['reporttype'])){
	if($_POST['reporttype']=="Monthly"){
		if($_POST['calendertype']=='Normal'){
			$typecalender='calendar_month';
		}else{
			$typecalender='fiscal_month';
		}
		$query = $conn->prepare("SELECT DISTINCT $typecalender FROM aruba_avaya_raw order by id desc");
		$query->execute();
		$weeklist = $query->fetchAll(PDO::FETCH_ASSOC);
		$weekdata[]='<option>-- Select --</option>';		
	    foreach($weeklist as $weeknamearr){
	       $weekdata[]='<option value="'.$weeknamearr[$typecalender].'">'.$weeknamearr[$typecalender].'</option>'; 
	    }
	    echo json_encode($weekdata);
	} else if($_POST['reporttype']=="Quarterly"){
		if($_POST['calendertype']=='Normal'){
			$typecalender='calendar_quarter';
		}else{
			$typecalender='fiscal_quarter';
		}
		$query = $conn->prepare("SELECT DISTINCT $typecalender FROM aruba_avaya_raw order by id desc");
		$query->execute();
		$weeklist = $query->fetchAll(PDO::FETCH_ASSOC);
		$weekdata[]='<option>-- Select --</option>';
	    foreach($weeklist as $weeknamearr){
	       $weekdata[]='<option value="'.$weeknamearr[$typecalender].'">'.$weeknamearr[$typecalender].'</option>'; 
	    }
	    echo json_encode($weekdata);
	}elseif($_POST['reporttype']=="Weekly"){
		if($_POST['calendertype']=='Normal'){
			$typecalender='calendar_week';
		}else{
			$typecalender='fiscal_week';
		}
		$query = $conn->prepare("SELECT DISTINCT $typecalender FROM aruba_avaya_raw order by id desc");
		$query->execute();
		$weeklist = $query->fetchAll(PDO::FETCH_ASSOC);
		$weekdata[]='<option>-- Select --</option>';		
	    foreach($weeklist as $weeknamearr){
	       $weekdata[]='<option value="'.$weeknamearr[$typecalender].'">'.$weeknamearr[$typecalender].'</option>'; 
	    }	
	    echo json_encode($weekdata);
	}else{
		$date='date';
		$query = $conn->prepare("SELECT DISTINCT $date FROM aruba_avaya_raw order by id desc");
		$query->execute();
		$weeklist = $query->fetchAll(PDO::FETCH_ASSOC);
		$weekdata[]='<option>-- Select --</option>';		
	    foreach($weeklist as $weeknamearr){
	       $weekdata[]='<option value="'.$weeknamearr['date'].'">'.$weeknamearr['date'].'</option>'; 
	    }
	    echo json_encode($weekdata);
	}	
}

if(isset($_POST['selectList']) && $_POST['comefrom'] == 'backlog' ){
	$explodeArr = explode("_",$_POST['selectList']);
	$Qry= empty($explodeArr[0])?'':" where team = '$explodeArr[0]'";
	if(empty($Qry)){
		$Qry.= empty($explodeArr[1])?"":" where case_owner = '$explodeArr[1]' ";
	}else{
		$Qry.= empty($explodeArr[1])?"":" and case_owner = '$explodeArr[1]' ";
	}
	$query = $commonobj->getQry("SELECT DISTINCT case_number FROM backlog_rawdata  $Qry and closed_status='1' order by case_number asc");
	$casenumber[]='<option>-- Select --</option>';		
    foreach($query as $weeknamearr){
       $casenumber[]='<option value="'.$weeknamearr['case_number'].'">'.$weeknamearr['case_number'].'</option>'; 
    }
    if($explodeArr[0] != ''){
	    $query1 = $commonobj->getQry("SELECT DISTINCT case_owner FROM backlog_rawdata  where team = '$explodeArr[0]' and closed_status='1' order by case_number asc");
	    $teamArr[] ='<option>-- Select --</option>';
	    foreach($query1 as $teamnane){
	       $teamArr[]='<option value="'.$teamnane['case_owner'].'">'.$teamnane['case_owner'].'</option>'; 
	    }
	}
    echo json_encode(array('0'=>$casenumber,'1'=>$teamArr));
}
if(isset($_POST['caseid']) && $_POST['comefrom'] == 'backlog_form' ){
	$case_num = $_POST['caseid'];
	//echo $case_num = '5315407679';
	$query = $commonobj->getQry("SELECT team_manager,team,case_owner,queue,project,product_group,region,case_origin,case_number,product_number,status,opened_date,severity,subject,calendar_week,calendar_month,calendar_quarter,calendar_year,`date` FROM backlog_rawdata where case_number = '$case_num' and closed_status='1'");
	
	$query1 = $commonobj->getQry("SELECT id,updated_to,main_catgry,sub_catgry,status,update_status FROM backlog_daily_status where case_number = '$case_num'");

	if(count($query1) > 0){
		$subtable.= '<table class="table table-bordeGreen"><thead><tr><th>id</th><th>Main Category</th><th>Sub Category</th><th>Comments</th><th>Date</th><th>Action</th></tr></thead><tbody><tr>';
        $i = 1; foreach ($query1 as $key => $bck_value) {
        $subtable.= '<td>'.$i.'</td>';
        //$subtable.= '<td>'.$bck_value['status'].'</td>';
        $subtable.= '<td>'.$bck_value['main_catgry'].'</td>';
        $subtable.='<td>'.$bck_value['sub_catgry'].'</td>';
        $subtable.= '<td>'.$bck_value['update_status'].'</td>';
        $subtable.='<td>'.date("d-m-Y",strtotime($bck_value['updated_to'])).'</td>';
        $subtable.='<td class=text-center><span class="btn btn-info btn-sm"  data-toggle="modal" data-target="#modal_basic" onclick=edit_fun('.$bck_value['id'].',"'.date("d-m-Y",strtotime($bck_value['updated_to'])).'")>Edit</button>
            </td><tr>';
        $i++; } 
        $subtable.='</tbody></table>';
	}
    echo json_encode(array('0'=>$query,'1'=>$subtable));
}

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