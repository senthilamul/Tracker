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
	
	$query = $commonobj->getQry("SELECT DISTINCT case_number FROM aruba_csat  $Qry and  alert_type !='Green' and LENGTH (case_number) > 7 $filterQry order by case_number asc");
	$casenumber[]='<option>-- Select --</option>';		
    foreach($query as $weeknamearr){
       $casenumber[]='<option value="'.$weeknamearr['case_number'].'">'.$weeknamearr['case_number'].'</option>'; 
    }
    if($explodeArr[0] != ''){
	    $query1 = $commonobj->getQry("SELECT DISTINCT case_owner FROM aruba_csat where team = '$explodeArr[0]' and LENGTH (case_number) > 7 and alert_type !='Green' $filterQry order by case_owner asc");
	    $teamArr[] ='<option>-- Select --</option>';
	    foreach($query1 as $teamnane){
	       $teamArr[]='<option value="'.$teamnane['case_owner'].'">'.$teamnane['case_owner'].'</option>'; 
	    }
	}

    echo json_encode(array('0'=>$casenumber,'1'=>$teamArr));
}
if(isset($_POST['caseid']) && $_POST['comefrom'] == 'csat_form' ){
	$case_num = $_POST['caseid'];
	//$case_num = '5314647099';
	$query = $commonobj->getQry("SELECT alert_type,que_new,product_group,region,comments,overall_experience,nps,datetime_closed,cq3_ease_of_access,cq7_technical_ability,cq8_non_technical_performance,cq9_kept_informed,cq10_solution_time,engineer_email_id,tl_tier1,tl_tier2,tl_tier3,tl_tier4,tl_tier5,tl_comments FROM aruba_csat where case_number = '$case_num'  and LENGTH (case_number) > 7 and alert_type !='Green' $filterQry ");
    echo json_encode(array('0'=>$query));
}

if (isset($_POST['selectdropdown']) && $_POST['comefrom'] == 'csat_esc') {

    $tier = explode("_", $_POST['selectdropdown']);
    //$val = 'Controllable____';
    //$tier = explode("_",$val);
    if ($tier[0] == 'Controllable') {
        $drp2 = array('Aruba', 'GEC-TAC', 'GSC-TAC', 'GSC-WC');
        if ($tier[1] == 'Aruba') {
            $drp3 = array('Product', 'RMA/Shipping', 'Sales Team', 'Tool/Database Issues');

            if ($tier[2] == 'Product') {
                $drp4 = array('Bad Scan', 'Delay in Bug Fix', 'Delay in Code Release', 'Delay in Path Release', 'Delay in QA Replication', 'Inadequate Resolution', 'issue Resurfaced - Post Upgrade', 'New Feature Request');
                $drp5 = $tier[3] != '' ? array('Others') : '';
            }
            elseif($tier[2] == 'RMA/Shipping') {
                $drp4 = array('Database Related', "Delay in Shipment (RTF RMA's)", 'Delay In Shipment', 'DOA - Initial', 'DOA - Replacement', 'Out of stock', 'Replacement Unit - Defective', 'Shipping Label', 'Wrong unit shipped');
                $drp5 = $tier[3] != '' ? array('Others') : '';

            } else if ($tier[2] == 'Sales Team') {
                $drp4 = array('Delpoyment Issue');
                $drp5 = $tier[3] == 'Delpoyment Issue' ? array('Incorrect Design', 'No Site Survey Done', 'Wrong Product Sold') : '';

            } else
            if ($tier[2] == 'Tool/Database Issues') {
                $drp4 = array('Customer record missing', 'Delay in Response - DL Entitlement', 'Entitlement Delay', 'Incorrect entitlement status', 'Licence/serialnumber/certificate missing', 'SFDC/IT Related Issue');
                $drp5 = $tier[3] != '' ? array('Others') : '';
            }
        } else if ($tier[1] == 'GSC-TAC') {
            $drp3 = array('Communication', 'People', 'Process', 'Technical');

            if ($tier[2] == 'Communication') {
                $drp4 = array('Effective communication', 'Rate of Speech/Voice & Accent');
                $drp5 = $tier[3] != '' ? array('Others') : '';
            }
            elseif($tier[2] == 'People') {
                $drp4 = array('Behaviour', 'Management');
                if ($tier[3] == 'Behaviour') {
                    $drp5 = array('Carelessness', 'Fake NAD');
                } else if ($tier[3] == 'Management') {
                    $drp5 = array('LIL', 'Out of Office', 'Out of Shift', 'Timely Case Assignment');
                }
            } else if ($tier[2] == 'Process') {
                $drp4 = array('Availability', 'Case documentation', 'Delay in Response', 'Ownership', 'Process Awareness');
                if ($tier[3] == 'Availability') {
                    $drp5 = array('Availability - Existing Case', 'Availability - New Case');
                } else if ($tier[3] == 'Case documentation') {
                    $drp5 = array('Failed to document', 'In accurate/In adequate Documentation');
                } else if ($tier[3] == 'Delay in Response') {
                    $drp5 = array('Bug Filing', 'Case Handover', 'Engineer on a different call', 'Engineer out of office', 'Failed To Response', 'Follow-up miss', 'Initial Respose', 'Log Analysis', 'Out of Office', 'Out of shift response', 'Replication', 'RMA Initiation', 'Timely Escalation', 'Timely Response', 'Timezone Related');
                } else if ($tier[3] == 'Ownership') {
                    $drp5 = array('Failed Commitment', 'Failed to Own', 'Incorrect RMA', 'Missed Schedule');
                } else if ($tier[3] == 'Process Awareness') {
                    $drp5 = array('Sense of Urgency');
                }
            } else
            if ($tier[2] == 'Technical') {
                $drp4 = array('Technical Competency');
                $drp5 = $tier[3] != 'Technical Competency' ? array('Delay in Resolution', 'Inappropriate Response', 'Inccurate/Inadequate Resolution', 'Issue Resurfaced', 'Problem Understanding', 'Product Knowledge Gap', 'Request for New Engineer', 'Suggestion/Steps did not help', 'Technical Competency', 'Timely Assistance', 'Timely Escalation', 'Wrong Solution Provided') : '';

            }
        } else if ($tier[1] == 'GEC-TAC') {
            $drp3 = array('Communication', 'People', 'Process', 'Technical');
            if ($tier[2] == 'Communication') {
                $drp4 = array('Effective communication', 'Rate of Speech/Voice & Accent');
                $drp5 = $tier[3] != '' ? array('Others') : '';
            }
            elseif($tier[2] == 'People') {
                $drp4 = array('Availability');
                if ($tier[3] == 'Availability')
                    $drp5 = array('GEC Denied');

            } else if ($tier[2] == 'Process') {
                $drp4 = array('Availability', 'Case documentation', 'Delay in Response', 'Ownership');
                if ($tier[3] == 'Availability') {
                    $drp5 = array('Availability - Existing Case', 'Availability - New Case', 'GEC Unavailable', 'TimeZone Related');
                } else if ($tier[3] == 'Case documentation') {
                    $drp5 = array('Failed to document', 'In accurate Response', 'In accurate/In adequate Documentation');
                } else if ($tier[3] == 'Delay in Response') {
                    $drp5 = array('Delay in Response', 'Replication', 'RMA Initiation');
                } else if ($tier[3] == 'Ownership') {
                    $drp5 = array('In accurate RMA');
                }
            } else
            if ($tier[2] == 'Technical') {
                $drp4 = array('Technical Competency');
                $drp5 = $tier[3] != 'Technical Competency' ? array('Issue Resurfaced', 'Problem Understanding', 'Suggestion/Steps did not help', 'Technical Competency') : '';

            }
        } else if ($tier[1] == 'GSC-WC') {
            $drp3 = array('Communication', 'Process');
            if ($tier[2] == 'Communication') {
                $drp4 = array('Effective communication', 'Rate of Speech/Voice & Accent');
                $drp5 = $tier[3] != '' ? array('Others') : '';
            }
            elseif($tier[2] == 'Process') {
                $drp4 = array('Case documentation', 'Delay in Response', 'Ownership', 'Process Awareness');
                if ($tier[3] == 'Case documentation') {
                    $drp5 = array('Delay in Documentation', 'Failed to Document/Update', 'Inccurate/Inadequate case Documentation', 'Inccurate/Inadequate info given to Customer');
                } else if ($tier[3] == 'Delay in Response') {
                    $drp5 = array('Case Handover', 'Delay in case assignment', 'Delay in Case Transfer', 'Delay in RMA Processing', 'Follow-up miss', 'Timely Response');
                } else if ($tier[3] == 'Ownership') {
                    $drp5 = array('Inccurate/Inadequate RMA processed', 'Shipped to Wrong/Incomplete Address', 'Wrong unit shipped');
                } else if ($tier[3] == 'Process Awareness') {
                    $drp5 = array('Activation Related Issues', 'Case Closed - Inproper resolution', 'Denied Access - Support Expired', 'Denied Access - Valid Support', 'Denied TAC Support - Support Expired', 'Denied TAC Support - Valid Support', 'FAR - Missed', 'Inappropriate Response', 'Legacy License Issues', 'License Consolidation Issues', 'License Transfer Issues', 'Login Issues');
                }
            }
        } else {
            $drp3 = array('');
        }

    } else if ($tier[0] == 'Uncontrollable') {
        $drp2 = array('Aruba', 'GSC-TAC', 'GSC-WC');
        if ($tier[1] == 'Aruba') {
            $drp3 = array('RMA/Shipping');

            if ($tier[2] == 'RMA/Shipping') {
                $drp4 = array('Customs Delay', 'Missed NDB');
                $drp5 = $tier[3] != 'Missed NDB' ? array('Others') : array('UPS Delay');
            }
        } else if ($tier[1] == 'GSC-TAC') {
            $drp3 = array('Customer', 'Political Escalation', 'Sales Team');

            if ($tier[2] == 'Customer') {
                $drp4 = array('3rd Party Issue', 'Customer Related', 'Environment Related', 'New Case', 'No Support / Support Expired', 'Out of Office');
                $drp5 = $tier[3] != '' ? array('Others') : '';
            }
            elseif($tier[2] == 'Political Escalation') {
                $drp4 = array('As per Aruba Mgmt', 'As per SE', 'Customer Sensitive', 'Deal Breaker/POC', 'Due to Past experience', 'Production/Network Impact');
                $drp5 = $tier[3] != '' ? array('Others') : '';
            } else if ($tier[2] == 'Sales Team') {
                $drp4 = array('ACE Request', 'Case follow-up', 'Expedite request from customer', 'Maintenance Window', 'New Case', 'Out of Contract', 'POC', 'Possible outage', 'Production Deployment', 'Request for case review', 'SE approved for support');
                $drp5 = $tier[3] != '' ? array('Others') : '';
            }
        } else if ($tier[1] == 'GSC-WC') {
            $drp3 = array('Customer');

            if ($tier[2] == 'Customer') {
                $drp4 = array('Out of Office');
                $drp5 = $tier[3] != '' ? array('Others') : '';
            }
        }

    } else {
        $drp2 = array('');
    }
    echo json_encode(array('2' => $drp2, '3' => $drp3, '4' => $drp4, '5' => $drp5));
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
    $query = $commonobj->getQry("SELECT wlan_ns,product,queue,region,tier_1,tier_2,tier_3,tier_4,tier_5,rca_comments,`date`,manager_name,role FROM aruba_esc where `case` = '$case_num'  and LENGTH (`case`) > 7 $filterQry ");
    echo json_encode(array('0'=>$query));
}
if (isset($_POST['selectdropdown']) && $_POST['comefrom'] == 'esc_tier_ajax') {

    $tier = explode("_", $_POST['selectdropdown']);
    //$val = 'Controllable____';
    //$tier = explode("_",$val);
    if ($tier[0] == 'Controllable') {
        $drp2 = array('Aruba','GEC-TAC','GSC-TAC','GSC-WC');
        if ($tier[1] == 'Aruba') {
            $drp3 = array('Product','RMA/Shipping','Sales Team','Tool/Database Issues');

            if ($tier[2] == 'Product') {
                $drp4 = array('Bad Scan','Delay in Bug Fix','Delay in Code Release','Delay in Patch Release','Delay in QA Replication','Inadequate Resolution','Issue resurfaced - Post Upgrade','New Feature request');
                $drp5 = $tier[3] != '' ? array('Others') : '';
            }
            elseif($tier[2] == 'RMA/Shipping') {
                $drp4 = array('Database related',"Delay in Shipment (RTF RMA's)",'Delay is shipment','DOA - Initial','DOA - Replacement','Out of stock','Replacement Unit - Defective','Shipping Label','Wrong unit shipped');
                $drp5 = $tier[3] != '' ? array('Others') : '';

            } else if ($tier[2] == 'Sales Team') {
                $drp4 = array('Delpoyment Issue');
                $drp5 = $tier[3] == 'Delpoyment Issue' ? array('Incorrect Design', 'No Site Survey Done', 'Wrong Product Sold') : '';

            } else
            if ($tier[2] == 'Tool/Database Issues') {
                $drp4 = array('Delay in Response - DL Entitlement','Entitlement Delay','Incorrect entitlement status','Licence/serialnumber/certificate missing','SFDC/IT Related Issue','Customer record missing');
                $drp5 = $tier[3] != '' ? array('Others') : '';
            }
        } else if ($tier[1] == 'GSC-TAC') {
            $drp3 = array('Communication', 'People', 'Process', 'Technical');

            if ($tier[2] == 'Communication') {
                $drp4 = array('Effective communication', 'Rate of Speech/Voice & Accent');
                $drp5 = $tier[3] != '' ? array('Others') : '';
            }
            elseif($tier[2] == 'People') {
                $drp4 = array('Behaviour', 'Management');
                if ($tier[3] == 'Behaviour') {
                    $drp5 = array('Carelessness', 'Fake NAD');
                } else if ($tier[3] == 'Management') {
                    $drp5 = array('LIL', 'Out of Office', 'Out of Shift', 'Timely Case Assignment');


                }
            } else if ($tier[2] == 'Process') {
                $drp4 = array('Availability', 'Case documentation', 'Delay in Response', 'Ownership', 'Process Awareness');

                if ($tier[3] == 'Availability') {
                    $drp5 = array('Availability - Existing Case', 'Availability - New Case');
                } else if ($tier[3] == 'Case documentation') {
                    $drp5 = array('Failed to document', 'In accurate/In adequate Documentation');
                } else if ($tier[3] == 'Delay in Response') {
                    $drp5 = array('Bug Filing','Engineer on a different call','Engineer out of office','Follow-up miss','Initial Respose','Log Analysis','Out of office','Out of shift response','Replication','RMA Initiation','Timely Escalation','Timely Response','Timezone Related','Case Handover','Failed To Response');
                } else if ($tier[3] == 'Ownership') {
                    $drp5 = array('Failed Commitment', 'Failed to Own', 'Incorrect RMA', 'Missed Schedule');
                } else if ($tier[3] == 'Process Awareness') {
                    $drp5 = array('Sense of Urgency');
                }
            } else
            if ($tier[2] == 'Technical') {
                $drp4 = array('Technical Competency');
                $drp5 = $tier[3] != 'Technical Competency' ? array('Delay in Resolution','Inappropriate Response','Issue Resurfaced','Problem Understanding','Request for New Engineer','Technical Competency','Timely Escalation','Wrong Solution Provided','Suggestion/Steps did not help','Product Knowledge Gap','Inccurate/Inadequate Resolution','Timely Assistance') : '';

            }
        } else if ($tier[1] == 'GEC-TAC') {
            $drp3 = array('Communication', 'People', 'Process', 'Technical');
            if ($tier[2] == 'Communication') {
                $drp4 = array('Effective communication', 'Rate of Speech/Voice & Accent');
                $drp5 = $tier[3] != '' ? array('Others') : '';
            }
            elseif($tier[2] == 'People') {
                $drp4 = array('Availability');
                if ($tier[3] == 'Availability')
                    $drp5 = array('GEC Denied');

            } else if ($tier[2] == 'Process') {
                $drp4 = array('Availability','Case documentation','Delay in Response','Ownership');
                if ($tier[3] == 'Availability') {
                    $drp5 = array('Availability - Existing Case', 'Availability - New Case', 'GEC Unavailable', 'TimeZone Related');
                } else if ($tier[3] == 'Case documentation') {
                    $drp5 = array('Failed to document', 'In accurate Response', 'In accurate/In adequate Documentation');
                } else if ($tier[3] == 'Delay in Response') {
                    $drp5 = array('Delay in Response', 'Replication', 'RMA Initiation');
                } else if ($tier[3] == 'Ownership') {
                    $drp5 = array('In accurate RMA');
                }
            } else
            if ($tier[2] == 'Technical') {
                $drp4 = array('Technical Competency');
                $drp5 = $tier[3] != 'Technical Competency' ? array('Issue Resurfaced', 'Problem Understanding', 'Suggestion/Steps did not help', 'Technical Competency') : '';

            }
        } else if ($tier[1] == 'GSC-WC') {
            $drp3 = array('Communication', 'Process');
            if ($tier[2] == 'Communication') {
                $drp4 = array('Effective communication', 'Rate of Speech/Voice & Accent');
                $drp5 = $tier[3] != '' ? array('Others') : '';
            }
            elseif($tier[2] == 'Process') {
                $drp4 = array('Case documentation','Delay in Response','Ownership','Process Awareness');
                if ($tier[3] == 'Case documentation') {
                    $drp5 = array('Delay in Documentation','Failed to Document/Update','Inccurate/Inadequate case Documentation','Inccurate/Inadequate info given to Customer','Incorrect Priority');
                } else if ($tier[3] == 'Delay in Response') {
                    $drp5 = array('Follow-up miss','Delay in case assignment','Delay in RMA Processing','Delay in Case Transfer','Case Handover','Timely Response');
                } else if ($tier[3] == 'Ownership') {
                    $drp5 = array('Inccurate/Inadequate RMA processed','Wrong unit shipped','Shipped to Wrong/Incomplete Address');
                } else if ($tier[3] == 'Process Awareness') {
                    $drp5 = array('Denied TAC Support - Support Expired','Denied TAC Support - Valid Support','Case Closed - Inproper resolution','Login Issues','Denied Access - Support Expired','Denied Access - Valid Support','Activation Related Issues','License Transfer Issues','Legacy License Issues','License Consolidation Issues','FAR - Missed','Inappropriate Response');
                }
            }
        } else {
            $drp3 = array('');
        }

    } else if ($tier[0] == 'Uncontrollable') {
        $drp2 = array('Aruba', 'GSC-TAC', 'GSC-WC');
        if ($tier[1] == 'Aruba') {
            $drp3 = array('RMA/Shipping');

            if ($tier[2] == 'RMA/Shipping') {
                $drp4 = array('Customs Delay', 'Missed NDB');
                $drp5 = $tier[3] != 'Missed NDB' ? array('Others') : array('UPS Delay');
            }
        } else if ($tier[1] == 'GSC-TAC') {
            $drp3 = array('Customer', 'Political Escalation', 'Sales Team');

            if ($tier[2] == 'Customer') {
                $drp4 = array('New Case','No Support / Support Expired','Out of Office','Environment Related','3rd Party Issue');
                $drp5 = $tier[3] != '' ? array('Others') : '';
            }
            elseif($tier[2] == 'Political Escalation') {
                $drp4 = array('Customer Sensitive','As per SE','As per Aruba Mgmt','Due to Past experience','Production/Network Impact','Deal Breaker/POC');
                $drp5 = $tier[3] != '' ? array('Others') : '';
            } else if ($tier[2] == 'Sales Team') {
                $drp4 = array('ACE Request','Case follow-up','Production Deployment','Possible outage','Request for case review','Expedite request from customer','SE approved for support','Maintenance Window','POC','Out of Contract','New Case');
                $drp5 = $tier[3] != '' ? array('Others') : '';
            }
        } else if ($tier[1] == 'GSC-WC') {
            $drp3 = array('Customer');

            if ($tier[2] == 'Customer') {
                $drp4 = array('Out of Office');
                $drp5 = $tier[3] != '' ? array('Others') : '';
            }
        }

    } else {
        $drp2 = array('Others');
        $drp3 = array('Others');
        $drp4 = array('Others');
        $drp5 = array('Others');
    }
    echo json_encode(array('2' => $drp2, '3' => $drp3, '4' => $drp4, '5' => $drp5));
}
?>