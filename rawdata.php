<?php
set_time_limit(0);
include "includes/config.php";
include 'includes/session_check.php';
$month = $_GET['month'];
$table = $_GET['data'];
if(count ($month) >0 ){
	$CsatArr = $commonobj->getQry("SELECT * from $table where calendar_month ='$month'");
}
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$table.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<form method="POST" id="frmsrch">    
<input type="hidden" name="_token" value="<?php echo $token; ?>">
<input type="hidden" name="frm_submit"> 
<div class="page-content-wrap">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <table id="customers2" class="table datatable" border='1' style='white-space: nowrap;'>
						<thead>
							<tr>
								<?php 
								$getColumnName = $commonobj->arrayColumn($commonobj->getQry("SHOW COLUMNS FROM $table"),'','Field');
								foreach ($getColumnName as $key => $value) {
									echo "<td style='background-color:#ED7D31;color:white'>".str_replace('_',' ',$value)."</td>";
								}
								?>
							</tr>
						</thead>
						<tbody>
							<tr>
							<?php 
								foreach ($CsatArr as $key => $values) { 
									foreach ($getColumnName as $key => $val) {
										echo '<td>'.$values[$val].'</td>';
									} ?>
								</tr>
							<?php  } ?>
						</tbody>
					</table>    
				</div>
            </div>
        </div>
    </div>
	
</div>         
</form> 