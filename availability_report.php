<?php
set_time_limit(0);
include "includes/config.php";
//include 'includes/session_check.php';
if($_POST['projectwise'] || $_POST['productwise'] || $_POST['productgroup'] || $_POST['selectrange'] || $_POST['regionwise'] || $_POST['manager'] || $_POST['tlname'] || $_POST['enggname']){
	//$projectwise 	= 	$_POST['projectwise'];
	//$productwise   	=	$_POST['productwise'];
	$productgroup 	= 	$_POST['productgroup'];
	$calendartype	= 	$_POST['calendartype'];
	$selecttype	    = 	$_POST['selecttype'];
	//$regionwise 	= 	$_POST['regionwise'];
	if($selecttype == 'Daily'){
		$selectrange    =	date('m/j/Y',strtotime($_POST['picked_date']));
	}else{
		$selectrange    =	$_POST['selectrange'];
	}
	$manager    	=	$_POST['manager'];
	$tlname   		=	$_POST['tlname'];
	$enggname   	=	$_POST['enggname'];
}else{
	$selectrange    =  !empty($selectrange)?$selectrange:current($currentweek);
	//$projectwise 	=  !empty($projectwise)?$projectwise:"Overall";
	//$productwise 	=  !empty($productwise)?$productwise:"Overall";
	$productgroup 	=  !empty($productgroup)?$productgroup:"Overall";
	//$regionwise 	=  !empty($regionwise)?$regionwise:"Overall";
	$calendartype 	=  !empty($calendartype)?$calendartype:"Normal";
	$selecttype  	=  !empty($selecttype)?$selecttype:"Weekly";
	
	$manager    	=	empty($manager)?"Overall":$manager;
	$tlname    		=	empty($tlname)?"Overall":$tlname;
	$enggname    	=	empty($enggname)?"Overall":$enggname;
}

if($calendartype=='Normal'){
		$type="calendar_";
	}else{
		$type="fiscal_";
	}
	if($selecttype=='Quarterly'){	
		$selectQry= 'quarter';
	}else if($selecttype=='Monthly'){
		$selectQry= 'month';		
	}else if($selecttype=='Weekly'){
		$selectQry= 'week';
	}else{
		$selectQry= 'date';
	}
	
	$QryCondition.= $manager!='Overall'?" and manager_name='".$manager."'":'';	
	$QryCondition.= $tlname!='Overall'?" and team='".$tlname."'":'';	
	$QryCondition.= $enggname!='Overall'?" and case_owner='".$enggname."'":'';	
	$QryCondition.= $productgroup!='Overall'?" and product_group='".$productgroup."'":'';	
	
	$headertotalArr = $commonobj->arrayColumn($commonobj->getQry("select distinct ".$type.$selectQry." from aruba_avaya_raw order by id asc"),'',$type.$selectQry);
	foreach ($headertotalArr as $masterkey => $mastervalue) {
		$totalArrval[$masterkey]=$mastervalue;
	       if($mastervalue=="$selectrange"){break;}
	}
	$arrayval=array_reverse($totalArrval);
	$tablehead=array_reverse(array_slice($arrayval, 0, 6, true));
	$noofdaysArr = $commonobj->arrayColumn($commonobj->getQry("select $type$selectQry,working_days from aruba_headcount where ".$type.$selectQry." in ('".implode("','", $tablehead)."') group by $type$selectQry"),$type.$selectQry,'working_days');
	//echo "SELECT count(*) as cnt,mana,$type$selectQry from aruba_avaya_raw where ".$type.$selectQry." in ('".implode("','", $tablehead)."') $QryCondition group by $type$selectQry, mana order by mana asc";
	$present_managerArr = $commonobj->getQry("SELECT count(*) as cnt,manager_name,$type$selectQry from aruba_avaya_raw where ".$type.$selectQry." in ('".implode("','", $tablehead)."') AND TIME_TO_SEC(total_time) >= '28800' $QryCondition group by $type$selectQry, manager_name order by manager_name asc");
	foreach($present_managerArr as $resArr1)
	{
		$manager_countArr[$resArr1['manager_name']][$resArr1[$type.$selectQry]] =  $resArr1['cnt'];
	}
	$present_managercntArr = $commonobj->getQry("SELECT count(distinct case_owner) as cnt,manager_name,$type$selectQry from aruba_avaya_raw where ".$type.$selectQry." in ('".implode("','", $tablehead)."') $QryCondition group by $type$selectQry, manager_name order by manager_name asc");
	foreach($present_managercntArr as $resArr1)
	{
		$manager_empcountArr[$resArr1['manager_name']][$resArr1[$type.$selectQry]] =  $resArr1['cnt'];
	}
	//print_r($manager_empcountArr);
	$present_teamArr = $commonobj->getQry("SELECT count(*) as cnt,team,$type$selectQry from aruba_avaya_raw where ".$type.$selectQry." in ('".implode("','", $tablehead)."') AND TIME_TO_SEC(total_time) >= '28800' $QryCondition group by $type$selectQry, team order by team asc");
	foreach($present_teamArr as $resArr1)
	{
		$team_countArr[$resArr1['team']][$resArr1[$type.$selectQry]] =  $resArr1['cnt'];
	}
	$present_teamcntArr = $commonobj->getQry("SELECT count(distinct case_owner) as cnt,team,$type$selectQry from aruba_avaya_raw where ".$type.$selectQry." in ('".implode("','", $tablehead)."') $QryCondition group by $type$selectQry, team order by team asc");
	foreach($present_teamcntArr as $resArr1)
	{
		$team_empcountArr[$resArr1['team']][$resArr1[$type.$selectQry]] =  $resArr1['cnt'];
	}
	$present_enggArr = $commonobj->getQry("SELECT count(*) as cnt,case_owner,$type$selectQry from aruba_avaya_raw where ".$type.$selectQry." in ('".implode("','", $tablehead)."') AND TIME_TO_SEC(total_time) >= '28800' $QryCondition group by $type$selectQry, case_owner order by case_owner asc");
	foreach($present_enggArr as $resArr1)
	{
		$engg_countArr[$resArr1['case_owner']][$resArr1[$type.$selectQry]] =  $resArr1['cnt'];
	}
	include "includes/header.php";
	?>
	<script type="text/javascript" src="js/plugins/jquery/jquery.min.js"></script>
	<form method="POST" id="frmsrch">               
                <!-- START BREADCRUMB -->
                <ul class="breadcrumb">
                    <li><a href="dashboard.php">Home</a></li>
                    <li><a href="outage_tracker.php">Outage Tracker</a></li>
					<li><a href="availability_report.php">Availability Report</a></li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                    <div class="portlet">
		        <div class="portlet-body">
					<div class="form-group col-sm-2 col-md-3 col-xs-6">
						<select class="form-control" id="manager"  name="manager" onchange="reload()">
		                	<option value="Overall">Overall Manager</option>';
		                	<?php
								//$Qry.= $projectwise == 'Overall' ? '':" and wlan_ns = '$projectwise'";
		                		//$Qry.= $productwise == 'Overall' ? '':" and que_new = '$productwise'";
		                		$Qry.= $productgroup == 'Overall' ? '':" and product_group = '$productgroup'";
		                		//$Qry.= $regionwise == 'Overall' ? '':" and region = '$regionwise'";
								
		                		$managerList = $commonobj->getQry("SELECT distinct manager_name from aruba_avaya_raw where ".$type.$selectQry." in ('".implode("','", $tablehead)."') $Qry order by manager_name asc");
								foreach ($managerList as $key => $value) {
									echo'<option value="'.$value['manager_name'].'">'.$value['manager_name'].'</option>';
								}
	                		?> 
		                </select>
		                <script> 
		                     jQuery("#manager").val("<?php echo $manager ?>");
		                </script>
		            </div>
					<div class="form-group col-sm-2 col-md-3 col-xs-6">
					    <select class="form-control" id="tlname"  name="tlname" onchange="reload()">
							<option value="Overall">Overall TL</option>
							<?php
								$tlQry = $manager == 'Overall' ? '' : " and manager_name ='$manager'";
								$tllist = $commonobj->getQry("SELECT distinct team from aruba_avaya_raw where ".$type.$selectQry." in ('".implode("','", $tablehead)."') $Qry $tlQry  order by team asc");
								foreach ($tllist as $key => $value) {
									echo'<option value="'.$value['team'].'">'.$value['team'].'</option>';
								}
							?> 
		                </select>
		                <script> 
		                     jQuery("#tlname").val("<?php echo $tlname ?>");
		                </script>
		            </div>
					<div class="form-group col-sm-2 col-md-3 col-xs-6">
						<select class="form-control" id="enggname"  name="enggname" onchange="reload()">
		                	<option value="Overall">Overall Engineer</option>
							<?php
								if ( $manager != 'Overall' && $tlname == 'Overall') {
		                			$engQry = " and manager_name = '$manager' ";
		                		} else if($manager != 'Overall' && $tlname != 'Overall') {
		                			$engQry = " and team = '$tlname' ";
		                		}else if($manager == 'Overall' && $tlname != 'Overall'){
		                			$engQry = " and team = '$tlname' ";
		                		}
								
								$tllist = $commonobj->getQry("SELECT distinct case_owner from aruba_avaya_raw where ".$type.$selectQry." in ('".implode("','", $tablehead)."')  $engQry $Qry order by case_owner asc");
								foreach ($tllist as $key => $value) {
									echo "<option value='".$value['case_owner']."'>".$value['case_owner']."</option>";
								}
							?> 
		                </select>
		                <script> 
		                     jQuery("#enggname").val("<?php echo $enggname ?>");
		                </script>
		            </div>
					<div class="form-group col-md-3 col-sm-3 col-xs-6">
					    <select class="form-control" id="drop4"  name="productgroup" onchange="reload()">
		                	<?php
		                		$productName = $commonobj->getQry("SELECT distinct product_group from aruba_avaya_raw");
								echo '<option value="Overall">Overall Product</option>';
		                		foreach ($productName as $key => $value) {
		                			echo '<option value="'.$value['product_group'].'">'.$value['product_group'].'</option>';
		                		} 
		                	?>
		                </select>
		                <script> 
		                     jQuery("#drop4").val("<?php echo $productgroup ?>");
		                </script>
		            </div>
		            <div class="form-group col-sm-2 col-md-3 col-xs-6">
		                <select class="form-control selectweek" id="drop5" name="calendartype">
		                	<option value="Normal">Calendar</option>
		                	<option value="Fiscal">Fiscal</option>
		                </select>
		                <script> 
		                     jQuery("#drop5").val("<?php echo $calendartype ?>");
		                </script>
		            </div>
		            
					<div class="form-group col-sm-2 col-md-3 col-xs-6">
		                <select class="form-control selectweek" id="drop6" name="selecttype" >
							<option value="Weekly">Weekly</option>
		                    <option value="Monthly">Monthly</option>
		                    <option value="Quarterly">Quarterly</option>
		                </select>
		                <script> 
		                     jQuery("#drop6").val("<?php echo $selecttype ?>");
		                </script>
		            </div>
		            <div class="form-group col-sm-2 col-md-3 col-xs-6">
						<select class="form-control" id="drop7" name="selectrange" onchange="reload()">
		                	<option value="">--- Select ---</option>';
		                	<?php
								$drowpdownArr = $commonobj->getQry("select distinct ".$type.$selectQry." from aruba_avaya_raw order by id desc");
								foreach ($drowpdownArr as $key => $value) {
									echo '<option value="'.$value[$type.$selectQry].'">'.$value[$type.$selectQry].'</option>';
								}
	                		?> 
		                </select>
						<script> 
		                     jQuery("#drop7").val("<?php echo $selectrange ?>");
		                </script>
						
					 </div>
		        </div>
		    </div>
					<!--<button class="btn btn-primary pull-right">Submit</button>-->
				</div>
                <!-- END PAGE TITLE -->                
                
                <!-- PAGE CONTENT WRAPPER -->
                <div class="page-content-wrap">
                    <div class="row">
                        <div class="col-md-12">
                            
                            <!-- START DATATABLE EXPORT -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Manager Wise Availability List</h3>
                                    <div class="btn-group pull-right">
                                        <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
                                        <ul class="dropdown-menu">
                                            <li><a href="#" onClick ="$('#customers2').tableExport({type:'excel',escape:'false'});"><img src='img/icons/xls.png' width="24"/> XLS</a></li>
                                        </ul>
                                    </div>                                    
                                    
                                </div>
                                <div class="panel-body" style="overflow:scroll;">
                                    <table id="customers2" class="table datatable">
										<thead>
											<tr>
												<th>Manger_name</th>
												<?php foreach($tablehead as $tbl){ ?>
													<th><?php echo $tbl; ?></th>
												<?php } ?>
											</tr>
										</thead>
                                        <tbody>
											<?php foreach($manager_countArr as $key => $resArr){
												echo "<tr><td>$key</td>";
												foreach($tablehead as $tbl){
													//$val = $resArr[$tbl]."/".$noofdaysArr[$tbl]."*".$manager_empcountArr[$key][$tbl];
													$val = empty($resArr[$tbl])?0:round(($resArr[$tbl]/($noofdaysArr[$tbl]*$manager_empcountArr[$key][$tbl]))*100,1);
													if($val > 100){
														$subval = '100%';
													}else{
														$subval = $val.'%';
													}
													echo "<td>$subval</td>";
												}
												echo "</tr>";
											}?>
										</tbody>
									</table>    
								</div>
                            </div>
                            <!-- END DATATABLE EXPORT -->                            
							<div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Team Wise Availability List</h3>
                                    <div class="btn-group pull-right">
                                        <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
                                        <ul class="dropdown-menu">
                                            <li><a href="#" onClick ="$('#customers2').tableExport({type:'excel',escape:'false'});"><img src='img/icons/xls.png' width="24"/> XLS</a></li>
                                        </ul>
                                    </div>                                    
                                    
                                </div>
                                <div class="panel-body" style="overflow:scroll;">
                                    <table id="customers2" class="table datatable">
										<thead>
											<tr>
												<th>Team Lead Name</th>
												<?php foreach($tablehead as $tbl){ ?>
													<th><?php echo $tbl; ?></th>
												<?php } ?>
											</tr>
										</thead>
                                        <tbody>
											<?php foreach($team_countArr as $key => $resArr){
												echo "<tr><td>$key</td>";
												foreach($tablehead as $tbl){
													$val = empty($resArr[$tbl])?0:round(($resArr[$tbl]/($noofdaysArr[$tbl]*$team_empcountArr[$key][$tbl]))*100,1);
													if($val > 100){
														$subval = '100%';
													}else{
														$subval = $val.'%';
													}
													echo "<td>$subval</td>";
												}
												echo "</tr>";
											}?>
										</tbody>
									</table>    
								</div>
                            </div>
							<div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Engineer Wise Availability List</h3>
                                    <div class="btn-group pull-right">
                                        <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
                                        <ul class="dropdown-menu">
                                            <li><a href="#" onClick ="$('#customers2').tableExport({type:'excel',escape:'false'});"><img src='img/icons/xls.png' width="24"/> XLS</a></li>
                                        </ul>
                                    </div>                                    
                                    
                                </div>
                                <div class="panel-body" style="overflow:scroll;">
                                    <table id="customers2" class="table datatable">
										<thead>
											<tr>
												<th>Engineer Name</th>
												<?php foreach($tablehead as $tbl){ ?>
													<th><?php echo $tbl; ?></th>
												<?php } ?>
											</tr>
										</thead>
                                        <tbody>
											<?php foreach($engg_countArr as $key => $resArr){
												echo "<tr><td>$key</td>";
												foreach($tablehead as $tbl){
													$val = empty($resArr[$tbl])?0:round(($resArr[$tbl]/$noofdaysArr[$tbl])*100,1);
													if($val > 100){
														$subval = '100%';
													}else{
														$subval = $val.'%';
													}
													echo "<td>$subval</td>";
												}
												echo "</tr>";
											}?>
										</tbody>
									</table>    
								</div>
                            </div>
                        </div>
                    </div>

                </div>         
                <!-- END PAGE CONTENT WRAPPER -->
            </div>            
            <!-- END PAGE CONTENT -->
        </div>
        <!-- END PAGE CONTAINER -->   
	</form> 
	<?php 
	include("includes/footer.php");
	?>
	<script type="text/javascript" src="js/plugins/datatables/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="js/plugins/tableexport/tableExport.js"></script>
	<script type="text/javascript" src="js/plugins/tableexport/jquery.base64.js"></script>
	<script type="text/javascript" src="js/plugins/tableexport/html2canvas.js"></script>
	<script type="text/javascript" src="js/plugins/tableexport/jspdf/libs/sprintf.js"></script>
	<script type="text/javascript" src="js/plugins/tableexport/jspdf/jspdf.js"></script>
	<script type="text/javascript" src="js/plugins/tableexport/jspdf/libs/base64.js"></script> 
	
	<script>
	$("#manager").change(function(){
		$("#tlname").val("Overall");
		$("#enggname").val("Overall");
	});
	function reload(){
		document.getElementById("frmsrch").action = 'availability_report.php'; 
		document.getElementById("frmsrch").submit();
		return false;
	}
	$(".selectweek").change(function(){
	    var selectdate= $("#drop6").val();
		if(selectdate == 'Daily'){
			$("#drop7").css("display","none");
		    $("#datepicker").css("display","inline-block");
	    }else{
			$("#drop7").css("display","inline-block");
			$("#datepicker").css("display","none");
			var calendertype= $("#drop5").val();
			$.ajax({
			  url: 'ajax_outage.php',
			  type: 'POST',
			  data: {'reporttype':selectdate,'calendertype':calendertype},
			  success: function(output) {
				var obj = jQuery.parseJSON( output);
				$("#drop7").html("");
				$("#drop7").html(obj);
			  }
			}); 
		}
	});
	</script>