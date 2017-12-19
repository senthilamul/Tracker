<?php
include "includes/config.php";
if(isset($_POST['case_number'])){
	/* $query = $conn->prepare("SELECT * FROM backlog_daily_status where case_number = '$_POST[case_number]'");
	$query->execute();
	$backlogStatus = $query->fetchAll(PDO::FETCH_ASSOC); */
	$backlogStatus = $commonobj->getQry("SELECT * FROM backlog_daily_status where case_number = '$_POST[case_number]'");
	$res = '<table class="table table-bordered">
				<thead>
					<tr>
						<th>id</th>
						<th>Status</th>
						<th>Main Category</th>
						<th>Sub Category</th>
						<th>Date</th>
					</tr>
				</thead>
				<tbody>';
				if(count($backlogStatus) > 0){
				$i = 1;
				foreach ($backlogStatus as $key => $bck_value) {
					$res .= '<tr>
						<td>'.$i.'</td>
						<td>'.$bck_value['status'].'</td>
						<td>'.$bck_value['main_catgry'].'</td>
						<td>'.$bck_value['sub_catgry'].'</td>
						<td>'.date("d-m-Y",strtotime($bck_value['updated_to'])).'</td>
					</tr>';  
					$i++;
				}
				}else{
					$res .= '<tr>
						<td colspan="5">No Records Found</td>
					</tr>';  
				}
				$res .= '</tbody>
			</table>';
	echo $res;
}
?>