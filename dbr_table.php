<?php
set_time_limit(0);
include "includes/config.php";
//include 'includes/session_check.php';
if($_POST['projectwise'] || $_POST['productwise'] || $_POST['productgroup'] || $_POST['selectrange'] || $_POST['regionwise'] || $_POST['manager'] || $_POST['tlname'] || $_POST['enggname']){
	$productgroup 	= 	$_POST['productgroup'];
	$calendartype	= 	$_POST['calendartype'];
	$selecttype	    = 	$_POST['selecttype'];
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
	$productgroup 	=  !empty($productgroup)?$productgroup:"Overall";
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
	
	// $headertotalArr = $commonobj->arrayColumn($commonobj->getQry("select distinct ".$type.$selectQry." from aruba_avaya_raw order by id asc"),'',$type.$selectQry);
	// foreach ($headertotalArr as $masterkey => $mastervalue) {
	// 	$totalArrval[$masterkey]=$mastervalue;
	//        if($mastervalue=="$selectrange"){break;}
	// }
	// $arrayval=array_reverse($totalArrval);
	// $tablehead=array_reverse(array_slice($arrayval, 0, 6, true));
	
	$backlogArr = $commonobj->getQry("SELECT team_manager,team,product_group,case_origin,case_number,opened_date,case_owner,age,severity,description,`date` from backlog_rawdata where closed_status ='1'");
	$updated_backlogArr = $commonobj->getQry("SELECT a.team_manager,a.team,a.product_group,a.case_origin,a.case_number,a.opened_date,a.case_owner,a.age,a.severity,a.description,a.`date`,b.updated_to from backlog_rawdata a inner join backlog_daily_status b on a.case_number = b.case_number where b.updated_to = '2017-12-01' group by case_number order by b.id desc");
	include "includes/header.php";
	?>
	<script type="text/javascript" src="js/plugins/jquery/jquery.min.js"></script>
	<form method="POST" id="frmsrch">               
                <!-- START BREADCRUMB -->
                <ul class="breadcrumb">
                    <li><a href="dashboard.php">Home</a></li>
                    <li><a href="dbr_form.php">Form</a></li>
                </ul>
                <!-- END BREADCRUMB -->
                
                
                <!-- END PAGE TITLE -->                
                
                <!-- PAGE CONTENT WRAPPER -->
                <div class="page-content-wrap">
                    <div class="row">
                        <div class="col-md-12">
                            
                            <!-- START DATATABLE EXPORT -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><b>Pending</b> Backlog</h3>
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
												<th>Case Number</th>
												<th>Opened Date</th>
												<th>Product Group</th>
												<th>Age</th>
												<th>Severity</th>
												<th>Case Origin</th>
												<th>Case Owner</th>
												<th>Case TL</th>
												<th>Case Manager</th>
												<th>Updated On</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php 
											foreach ($backlogArr as $key => $value) { 
											$backlogStatus = $commonobj->getQry("SELECT updated_to FROM backlog_daily_status where case_number = '$value[case_number]'");
											if(date('Y-m-d',strtotime($backlogStatus[0]['updated_to'])) != date('Y-m-d')){
											?>
												<tr>
													<td>
														<a onclick="show_model(<?=$value['case_number']?>)">
															<?=$value['case_number']?>
														</a>
													</td>
													<td><?=$value['opened_date']?></td>
													<td><?=$value['product_group']?></td>
													<td><?=$value['age']?></td>
													<td><?=$value['severity']?></td>
													<td><?=$value['case_origin']?></td>
													<td><?=$value['case_owner']?></td>
													<td><?=$value['team']?></td>
													<td><?=$value['team_manager']?></td>
													<td><?=$backlogStatus[0]['updated_to']?></td>
													<td><a href="dbr_form.php?id=<?=base64_encode($value['case_number'])?>"><i class="fa fa-pencil"></a></td>
												</tr>
											<?php } } ?>
										</tbody>
									</table>    
								</div>
								<div class="modal fade" id="myModal" role="dialog">
									<div class="modal-dialog">
										<!-- Modal content-->
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal">&times;</button>
												<h4 class="modal-title">Case Number: <?=$value['case_number']?></h4>
											</div>
											<div class="modal-body">
												   
															
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
											</div>
										</div>
									</div>
								</div>
                            </div>
                            <!-- END DATATABLE EXPORT -->
                        </div>
                    </div>
					<div class="row">
                        <div class="col-md-12">
                            
                            <!-- START DATATABLE EXPORT -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><b>Updated</b> Backlog</h3>
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
												<th>Case Number</th>
												<th>Opened Date</th>
												<th>Product Group</th>
												<th>Age</th>
												<th>Severity</th>
												<th>Case Origin</th>
												<th>Case Owner</th>
												<th>Case TL</th>
												<th>Case Manager</th>
												<th>Updated On</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($updated_backlogArr as $key => $value) { ?>
											<tr>
												<td>
													<a onclick="show_model(<?=$value['case_number']?>)">
														<?=$value['case_number']?>
													</a>
												</td>
												<td><?=$value['opened_date']?></td>
												<td><?=$value['product_group']?></td>
												<td><?=$value['age']?></td>
												<td><?=$value['severity']?></td>
												<td><?=$value['case_origin']?></td>
												<td><?=$value['case_owner']?></td>
												<td><?=$value['team']?></td>
												<td><?=$value['team_manager']?></td>
												<td><?=$value['updated_to']?></td>
												<td><a href="dbr_form.php?id=<?=base64_encode($value[case_number])?>"><i class="fa fa-pencil"> </a></td>
											</tr>
											<?php } ?>
										</tbody>
									</table>    
								</div>
                            </div>
                            <!-- END DATATABLE EXPORT -->
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
	function show_model(case_number){
		//alert(case_number);
		$.ajax({
			  url: 'ajax_tbr_tabel.php',
			  type: 'POST',
			  data: {'case_number':case_number},
			  success: function(output) {
				  //alert(output);
				$(".modal-body").html("");
				$(".modal-body").html(output);
				$("#myModal").modal('show');
			  }
			}); 
	}
	$("#manager").change(function(){
		$("#tlname").val("Overall");
		$("#enggname").val("Overall");
	});
	function reload(){
		document.getElementById("frmsrch").action = 'dbr_table.php'; 
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