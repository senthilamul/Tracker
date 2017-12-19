<?php 
set_time_limit(0);
include "includes/config.php";
extract($_POST);
if($_POST['edit_attedance'] == "edit"){
	$email = $_SESSION['email'];//'Rajalakshmi.Ramasamy@csscorp.com';
	$datetime = date("Y-m-d H:i:s");
	//$delay_reason = implode(",",$delay_reason);
	$update = $commonobj->getQry("UPDATE `aruba_avaya_raw` SET `login_time`='$login_time',`logout_time`='$logout_time',`total_time`='$total_time',`attendance_satus`='$attendance_type',`attendance_comment`='$attendance_comment',`delay_reason`='$delay_reason',`updated_by`='$email',`updated_time`='$datetime' WHERE id = '$id'");
	echo "success";
}
if($_POST['add_attedance'] == "add"){
	//print_r($_POST);
	$email = $_SESSION['email'];//'Rajalakshmi.Ramasamy@csscorp.com';
	$datetime = date("Y-m-d H:i:s");
	$prev_date = date('n/j/Y', strtotime($login_date .' -1 day'));
	$next_date = date('n/j/Y', strtotime($record_date .' +1 day'));
	//$delay_reason = implode(",",$delay_reason);
	//echo "SELECT * FROM aruba_avaya_raw WHERE `login_date` = '$prev_date' AND case_owner = '$add_emp_name'";
	$lastArr = $commonobj->getQry("SELECT * FROM aruba_avaya_raw WHERE `login_date` = '$prev_date' AND case_owner = '$add_emp_name'");
	
	if(count($lastArr) <= 0){
		//echo "SELECT * FROM aruba_avaya_raw WHERE `login_date` = '$next_date' AND case_owner = '$add_emp_name'";
		$lastArr = $commonobj->getQry("SELECT * FROM aruba_avaya_raw WHERE `login_date` = '$next_date' AND case_owner = '$add_emp_name'");
	}
	//print_r($lastArr);echo count($lastArr);
	//echo "INSERT INTO `aruba_avaya_raw`(`calendar_year`, `fiscal_year`, `fiscal_quarter`, `calendar_quarter`, `calendar_month`, `fiscal_month`, `calendar_week`, `fiscal_week`, `date`, `merge`, `wlan_ns`, `product`, `queue`, `case_owner`, `team`, `mana`, `emp_no`, `emp_name`, `manager_name`, `day`, `total_time`, `login_date`, `login_id`, `name`, `login_ext`, `skill`, `login_time`, `logout_time`, `logout_date`, `attendance_satus`, `attendance_comment`, `updated_by`, `updated_time`) VALUES ('".$lastArr[0]['calendar_year']."', '".$lastArr[0]['fiscal_year']."', '".$lastArr[0]['fiscal_quarter']."', '".$lastArr[0]['calendar_quarter']."', '".$lastArr[0]['calendar_month']."', '".$lastArr[0]['fiscal_month']."', '".$lastArr[0]['calendar_week']."', '".$lastArr[0]['fiscal_week']."', '".$lastArr[0]['date']."', '".$lastArr[0]['merge']."', '".$lastArr[0]['wlan_ns']."', '".$lastArr[0]['product']."', '".$lastArr[0]['queue']."', '".$lastArr[0]['case_owner']."', '".$lastArr[0]['team']."', '".$lastArr[0]['mana']."', '".$lastArr[0]['emp_no']."', '".$lastArr[0]['emp_name']."', '".$lastArr[0]['manager_name']."', '".$lastArr[0]['day']."', '".$total_time."', '".$login_date."', '".$lastArr[0]['login_id']."', '".$lastArr[0]['name']."', '".$lastArr[0]['login_ext']."', '".$lastArr[0]['skill']."', '".$login_time."', '".$logout_time."', '".$logout_date."', '".$attendance_type."', '".$attendance_comment."', '".$email."', '".$datetime."')";
	if($login_time == $logout_time && $attendance_type != 'P'){
		$total_time = "00:00";
	}
	echo "INSERT INTO `aruba_avaya_raw`(`calendar_year`, `fiscal_year`, `fiscal_quarter`, `calendar_quarter`, `calendar_month`, `fiscal_month`, `calendar_week`, `fiscal_week`, `date`, `merge`, `wlan_ns`, `product`, `queue`, `case_owner`, `team`, `mana`, `emp_no`, `emp_name`, `manager_name`, `day`, `total_time`, `login_date`, `login_id`, `name`, `login_ext`, `skill`, `login_time`, `logout_time`, `logout_date`, `attendance_satus`, `attendance_comment`, `delay_reason`,`updated_by`, `updated_time`) VALUES ('".$lastArr[0]['calendar_year']."', '".$lastArr[0]['fiscal_year']."', '".$lastArr[0]['fiscal_quarter']."', '".$lastArr[0]['calendar_quarter']."', '".$lastArr[0]['calendar_month']."', '".$lastArr[0]['fiscal_month']."', '".$lastArr[0]['calendar_week']."', '".$lastArr[0]['fiscal_week']."', '".$login_date."', '".$lastArr[0]['merge']."', '".$lastArr[0]['wlan_ns']."', '".$lastArr[0]['product']."', '".$lastArr[0]['queue']."', '".$lastArr[0]['case_owner']."', '".$lastArr[0]['team']."', '".$lastArr[0]['mana']."', '".$lastArr[0]['emp_no']."', '".$lastArr[0]['emp_name']."', '".$lastArr[0]['manager_name']."', '".$lastArr[0]['day']."', '".$total_time."', '".$login_date."', '".$lastArr[0]['login_id']."', '".$lastArr[0]['name']."', '".$lastArr[0]['login_ext']."', '".$lastArr[0]['skill']."', '".$login_time."', '".$logout_time."', '".$logout_date."', '".$attendance_type."', '".$attendance_comment."', '".$delay_reason."','".$email."', '".$datetime."')";
	$update = $commonobj->getQry("INSERT INTO `aruba_avaya_raw`(`calendar_year`, `fiscal_year`, `fiscal_quarter`, `calendar_quarter`, `calendar_month`, `fiscal_month`, `calendar_week`, `fiscal_week`, `date`, `merge`, `wlan_ns`, `product`, `queue`, `case_owner`, `team`, `mana`, `emp_no`, `emp_name`, `manager_name`, `day`, `total_time`, `login_date`, `login_id`, `name`, `login_ext`, `skill`, `login_time`, `logout_time`, `logout_date`, `attendance_satus`, `attendance_comment`, `delay_reason`,`updated_by`, `updated_time`) VALUES ('".$lastArr[0]['calendar_year']."', '".$lastArr[0]['fiscal_year']."', '".$lastArr[0]['fiscal_quarter']."', '".$lastArr[0]['calendar_quarter']."', '".$lastArr[0]['calendar_month']."', '".$lastArr[0]['fiscal_month']."', '".$lastArr[0]['calendar_week']."', '".$lastArr[0]['fiscal_week']."', '".$login_date."', '".$lastArr[0]['merge']."', '".$lastArr[0]['wlan_ns']."', '".$lastArr[0]['product']."', '".$lastArr[0]['queue']."', '".$lastArr[0]['case_owner']."', '".$lastArr[0]['team']."', '".$lastArr[0]['mana']."', '".$lastArr[0]['emp_no']."', '".$lastArr[0]['emp_name']."', '".$lastArr[0]['manager_name']."', '".$lastArr[0]['day']."', '".$total_time."', '".$login_date."', '".$lastArr[0]['login_id']."', '".$lastArr[0]['name']."', '".$lastArr[0]['login_ext']."', '".$lastArr[0]['skill']."', '".$login_time."', '".$logout_time."', '".$logout_date."', '".$attendance_type."', '".$attendance_comment."', '".$delay_reason."','".$email."', '".$datetime."')");
	echo "success";
}
if($_POST['cal_hours'] == "hours"){
	$t1 =strtotime(date("H:i:s",strtotime($login_time)));
    $t2=strtotime(date("H:i:s",strtotime($logout_time))); 

	$diff = $t2- $t1; 
	echo gmdate("h:i", $diff);
}
?>