<?php 
set_time_limit(0);
include('includes/config.php');
include('includes/session_check.php');
if(isset($_POST['submit'])){
    $date = date("d-M-y",strtotime($_POST['date']));
    $count = $commonobj->arrayColumn($commonobj->getQry("SELECT count(*) as count From backlog_rawdata where `date` = '$date'"),'','count');
    if($count[0] == 0 ){
        if(count($_FILES['file_import']['name']) > 0){
            //$TableNameArr = 'backlog_rawdata';
            $tmpFilePath = $_FILES['file_import']['tmp_name'];
            if($tmpFilePath != ""){
                $shortname = explode(".",$_FILES['file_import']['name']);
                $filename=$shortname[0];
                $filePath = CSV_ROOT_PATH."csv/" . $_FILES['file_import']['name'];
                $TmpTableName = "dbr_temp".time();
                if(move_uploaded_file($tmpFilePath,$filePath)) {
                    $conn = new PDO("mysql:host=".SERVER.";dbname=".DATABASE, DBUSER, DBPASS);
                    $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
                    $sql="CREATE TABLE IF NOT EXISTS `$TmpTableName` ($backlog_temp_table)";
                    $conn->exec($sql);
                    try {
                        $conn = new PDO("mysql:host=".SERVER.";dbname=".DATABASE, DBUSER, DBPASS);
                        $conn->exec('LOAD DATA '.$localkeyword.' INFILE "'.$filePath.'" INTO TABLE  '. $TmpTableName. ' FIELDS TERMINATED BY ","   OPTIONALLY ENCLOSED BY """" LINES TERMINATED BY "\n" IGNORE 1 LINES');
                        $TempData = $commonobj->getQry("SELECT * From $TmpTableName");
                        foreach ($TempData as $key => $value) { 
                           $backlogArr = $commonobj->getQry("SELECT * From backlog_rawdata where case_number= '".trim($value[case_number])."'");
                            if(count($backlogArr) == 0){
                                $value0to5 = $value['0_to_5'];
                                $value6to10 = $value['6_to_10'];
                                $value11to19 = $value['11_to_19'];
                                $compliance3 = $value['compliance_>_3'];
                                $compliance30 = $value['compliance_>_30'];
                                $compliance60 = $value['compliance_>_60'];
                                $desc = preg_replace('/[^A-Za-z0-9\-]/', ' ', $value['description']);
                                $subject = preg_replace('/[^A-Za-z0-9\-]/', ' ', $value['subject']);
                                $acc_name = preg_replace('/[^A-Za-z0-9\-]/', ' ', $value['account_name']);

                                $case_owner1 = preg_replace('/[^A-Za-z0-9\-]/', ' ', $value['case_owner_scrubbing']);
                                $case_owner2 = preg_replace('/[^A-Za-z0-9\-]/', ' ', $value['case_owner_scrubbing1']);
                                $manger_name = preg_replace('/[^A-Za-z0-9\-]/', ' ', $value['team_manager']);

                                $alias = preg_replace('/[^A-Za-z0-9\-]/', ' ', $value['case_owner_alias']);
                                $cr_alias = preg_replace('/[^A-Za-z0-9\-]/', ' ', $value['created_alias']);
                                $case_modi_by = preg_replace('/[^A-Za-z0-9\-]/', ' ', $value['case_last_modified_by']);
                                $case_manger = preg_replace('/[^A-Za-z0-9\-]/', ' ', $value['case_owner_manager']);
                                $case_lst_modi_by = preg_replace('/[^A-Za-z0-9\-]/', ' ', $value['case_last_modified_alias']);
                                $created_by = preg_replace('/[^A-Za-z0-9\-]/', ' ', $value['created_by']);

                                $Qry = "INSERT INTO `backlog_rawdata` (`case_owner_scrubbing`, `case_owner_scrubbing1`, `team_manager`, `team`, `queue`, `project`, `product_group`, `region`, `case_origin`, `day`, `weekday`, `calendar_week`, `fiscal_week`, `calendar_month`, `fiscal_month`, `fiscal_quarter`, `calendar_quarter`, `calendar_year`, `fiscal_year`, `age`, `0_to_5`, `6_to_10`, `11_to_19`, `gra20`, `last_update_age`, `compliance_>_3`, `compliance_>_30`, `compliance_>_60`, `last_update_split_ups`, `date`, `case_number`, `support_type`, `milestone_status`, `asset_country_name`, `status`, `account_page_level`, `product_category`, `product_number`, `opened_date`, `case_owner_alias`, `case_owner_role`, `created_alias`, `case_last_modified_by`, `case_last_modified_alias`, `parent_case_number`, `parent_case_id`, `type`, `case_record_type`, `case_reason`, `description`, `case_last_modified_date`, `date_time_closed`, `closed_date`, `age_days`, `open`, `closed`, `case_currency`, `selfservice_commented`, `new_selfservice_comment`, `case_id`, `business_hours`, `business_hours1`, `entitlement_process_start_time`, `entitlement_process_end_time`, `customer_email`, `created_by`, `case_origin1`, `region1`, `case_country_timezone`, `physical_country`, `update_hour`, `case_age_business_days`, `case_owner`, `case_owner_manager`, `escalated`, `severity`, `account_name`, `case_date_time_last_modified`, `product_line`, `subject`, `entitlement_summary`, `entitlement_exception_process`) VALUES ('','',".'"'.$manger_name.'"'.",".'"'.$value['team'].'"'.",'$value[queue]','$value[project]','$value[product_group]','$value[region]','$value[case_origin]','$value[day]','$value[weekday]','$value[calendar_week]','$value[fiscal_week]','$value[calendar_month]','$value[fiscal_month]','$value[fiscal_quarter]','$value[calendar_quarter]','$value[calendar_year]','$value[fiscal_year]','$value[age]','$value0to5','$value6to10','$value11to19','$value[gra20]','$value[last_update_age]','$compliance3','$compliance30','$compliance60','$value[last_update_split_ups]','$value[date]','$value[case_number]','$value[support_type]','$value[milestone_status]','$value[asset_country_name]','$value[status]','$value[account_page_level]','$value[product_category]','$value[product_number]','$value[opened_date]','$alias','$value[case_owner_role]','$cr_alias','$case_modi_by','$case_lst_modi_by','$value[parent_case_number]','$value[parent_case_id]','$value[type]','$value[case_record_type]','$value[case_reason]','$desc','$value[case_last_modified_date]','$value[date_time_closed]','$value[closed_date]','$value[age_days]','$value[open]','$value[closed]','$value[case_currency]','$value[selfservice_commented]','$value[new_selfservice_comment]','$value[case_id]','$value[business_hours]','$value[business_hours1]','$value[entitlement_process_start_time]','$value[entitlement_process_end_time]','$value[customer_email]','$created_by','$value[case_origin1]','$value[region1]','$value[case_country_timezone]','$value[physical_country]','$value[update_hour]','$value[case_age_business_days]',".'"'.$value['case_owner'].'"'.",'$case_manger','$value[escalated]','$value[severity]','$acc_name','$value[case_date_time_last_modified]','$value[product_line]','$subject','$value[entitlement_summary]','$value[entitlement_exception_process]')";
                            }else{
                                 
                                 $UpdateStatus = $commonobj->getQry('SELECT id From backlog_rawdata where case_number= "'.$value['case_number'].'" and team_manager= "'.$value['team_manager'].'" and team = "'.$value['team'].'" and queue="'.$value['queue'].'" and project="'.$value['project'].'" and product_group = "'.$value['product_group'].'" and region = "'.$value['region'].'"');

                                if(count($UpdateStatus) == 0 ){

                                   $Qry = 'UPDATE `backlog_rawdata` SET `team_manager`="'.$value['team_manager'].'",`team`="'.$value['team'].'",`queue`="'.$value['queue'].'",`project`="'.$value['project'].'",`product_group`="'.$value['product_group'].'",`region`="'.$value['region'].'" where case_number = "'.$value['case_number'].'"';
                                }
                            }
                            $qry = $conn->prepare($Qry);
                            $qry->execute();
                        }

                    }catch(PDOException $e){  
                        echo $e->getMessage(); 
                    }
                    $dropTable = $conn->exec("DROP TABLE $TmpTableName");
                    unlink($filePath);
                }else{
                    echo $_FILES['file_import']['name']." - not upload";
                }
            }
        }
        unset($_FILES['file_import']);
    }else{
        $ErrorMsg = $count[0];
    }
    // $_POST['date']);
    
}
include("includes/header.php");
?>          
  
        <!-- PAGE CONTENT WRAPPER -->
        <div class="page-content-wrap">
            <div class="row">
                <div class="col-md-12">
                    <div class="content"></div>
                    <form class="form-horizontal" method="POST" id="upload"  enctype="multipart/form-data">
                    <input type="hidden" value='submit' name='submit'>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><strong>Backlog Raw Data Upload</strong></h3>
                                <ul class="panel-controls">
                                    <li><a href="#" class="panel-remove"><span class="fa fa-times"></span></a></li>
                                </ul>
                            </div>
                            <div class="panel-body"></p></div>
                             <!-- form-group-separated -->
                            <div class="panel-body">                                                                        
                                
                                <div class="row">
                                    <div class="col-md-4">

                                        <div class="widget widget-primary">
                                            <div class="widget-title">TOTAL Backlog Upto</div>
                                            <div class="widget-subtitle"><?php 
                                            $date = $commonobj->getQry("SELECT count(*) as count,`date` From backlog_rawdata order by id ASC limit 0,1");
                                            ?></div>
                                            <div class="widget-int"><span data-toggle="counter" data-to="<?=$date[0][count]?>"><?=$date[0]['count']?></span></div>
                                            <div class="widget-controls">
                                                <a href="#" class="widget-control-left"><span class="fa fa-upload"></span></a>
                                                <a href="#" class="widget-control-right"><span class="fa fa-times"></span></a>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-4">
                                        <div class="widget widget-success widget-no-subtitle">
                                            <div class="widget-big-int"><span class="num-count"><?php echo $ErrorMsg ==''?'-':$ErrorMsg ?></span></div>                            
                                            <div class="widget-subtitle">Total Records</div>
                                            <div class="widget-controls">
                                                <a href="#" class="widget-control-left"><span class="fa fa-cloud"></span></a>
                                                <a href="#" class="widget-control-right"><span class="fa fa-times"></span></a>
                                            </div>                            
                                        </div>                        

                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="widget widget-danger widget-no-subtitle">
                                            <div class="widget-big-int"><span class="num-count"><?php echo $ErrorMsg ==''?'-':$ErrorMsg ?></span></div>                            
                                            <div class="widget-subtitle">Already Upload Backlog Your Select Date</div>
                                            <div class="widget-controls">
                                                <a href="#" class="widget-control-left"><span class="fa fa-cloud"></span></a>
                                                <a href="#" class="widget-control-right"><span class="fa fa-times"></span></a>
                                            </div>                            
                                        </div>                        

                                    </div>

                                </div>
                                <!-- <div class="form-group">                                        
                                    <label class="col-md-6 control-label">Date</label>
                                    <div class="col-md-2">
                                        <div class="input-group">
                                            <input type="text" class="form-control datepicker" value="<?php echo date('m/d/Y')?>" data-date-format="mm/dd/yyyy" data-date-viewmode="years" id='date' name='date'>
                                            <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                        </div>
                                        <span class="help-block font-red"></span>
                                    </div>
                                </div> -->
                                <div class="form-group">
                                    <label class="col-md-6 col-xs-12 control-label">Backlog File Upload:</label>
                                    <div class="col-md-6 col-xs-12">                                             
                                        
                                            <input type="file" class="fileinput btn-primary" name="file_import" id="file_import">
                                       
                                        <!-- <label id="file_import-error" class="error" for="file_import"></label> -->
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer text-center" >
                                <a href="upload.php"><input type='button' class="btn btn-danger" formnovalidate value='Cancel'></a>
                                <button class="btn btn-info ">Upload</button>
                            </div>
                        </div>
                    </form>
                    
                </div>
            </div>                    
            
        </div>

<?php include("includes/footer.php"); ?>
<script type='text/javascript' src='js/plugins/jquery-validation/additional/additional-methods.min.js'></script> 
<script>
$( "#upload" ).validate({
    rules: {
        file_import: {
            required: true,
            extension: "csv"
        },
    }
});
$('.datepicker').datepicker({
    format: 'mm/dd/yyyy',
    //startDate: '-0d'
});

</script>

