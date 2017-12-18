<?php 
include('includes/config.php');
//include('includes/session_check.php');
if(isset($_POST['file'])){
    extract($_POST);
    if($_FILES['file_import']['name'] != ''){
        $tmpFilePath = $_FILES['file_import']['tmp_name'];
        if($tmpFilePath != ""){
            $shortname = explode(".",$_FILES['file_import']['name']);
            $filename=$shortname[0];
            $filePath = CSV_ROOT_PATH."csv/" . $_FILES['file_import']['name'];
            $TmpTableName = "csat_esc_temp".time();
            if(move_uploaded_file($tmpFilePath,$filePath)) {
                $conn = new PDO("mysql:host=".SERVER.";dbname=".DATABASE, DBUSER, DBPASS);
                $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
                if($file == 'CSAT'){
                    $sql="CREATE TABLE IF NOT EXISTS `$TmpTableName` ($TempCSAT)";
                }else if($file == 'Escalation'){
                    $sql="CREATE TABLE IF NOT EXISTS `$TmpTableName` ($TempEsc)";
                }
                $conn->exec($sql);
                try {
                    $conn = new PDO("mysql:host=".SERVER.";dbname=".DATABASE, DBUSER, DBPASS);
                   $conn->exec('LOAD DATA '.$localkeyword.' INFILE "'.$filePath.'" INTO TABLE  '. $TmpTableName. ' FIELDS TERMINATED BY ","   OPTIONALLY ENCLOSED BY """"  LINES TERMINATED BY "\r\n" IGNORE 1 LINES');

                     //$conn->exec('LOAD DATA '.$localkeyword.' INFILE "'.$filePath.'" INTO TABLE  '. $TmpTableName. ' FIELDS TERMINATED BY  ',' ENCLOSED BY  '.'"'.' ESCAPED BY  '"\\"' LINES TERMINATED BY  '\r\n' IGNORE 1 LINES');

                    if($file == 'CSAT'){
                        $sql1 = "INSERT INTO aruba_csat SELECT * FROM $TmpTableName WHERE NOT EXISTS(SELECT * 
                            FROM aruba_csat WHERE ($TmpTableName.`case_number`= aruba_csat.case_number))";
                    }else if($file == 'Escalation'){
                        $sql1 = "INSERT INTO aruba_esc1 SELECT * FROM $TmpTableName WHERE NOT EXISTS(SELECT * 
                            FROM aruba_esc1 WHERE ($TmpTableName.`case`= aruba_esc1.case))";
                    }
                    $conn->exec($sql1);
                }catch(PDOException $e){  
                    echo $e->getMessage(); 
                }
                //33802
//"LOAD DATA LOCAL INFILE  '/tmp/phphLtP3h' INTO TABLE  `aruba_csat` FIELDS TERMINATED BY  ',' ENCLOSED BY  '".".' ESCAPED BY  '\\' LINES TERMINATED BY  '\r\n'".

                // $dropTable = $conn->exec("DROP TABLE $TmpTableName");
                // unlink($filePath);
            }else{
                echo $_FILES['file_import']['name']." - not upload";
            }
        }
        unset($_FILES['file_import']);
    }else{
        echo "file not Upload";
        exit;
    }
    
}
include("includes/header.php");
?>          
  
        <!-- PAGE CONTENT WRAPPER -->
        <div class="page-content-wrap">
            <div class="row">
                <div class="col-md-12">
                    <div class="content"></div>
                    <form class="form-horizontal" method="POST" id="upload"  enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="<?php echo $token; ?>">
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
                                            $date = $commonobj->getQry("SELECT count(*) as count,`date` From aruba_csat order by id ASC limit 0,1");
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
                                <div class="form-group">
                                    <label class="col-md-6 col-xs-12 control-label">Upload File</label>
                                    <div class="col-md-3 col-xs-12">                                             
                                        <select name="file" id="file" class="form-control select" >
                                            <option value="">-- Select--</option>
                                            <option value="CSAT">CSAT</option>
                                            <option value="Escalation">Escalation</option>
                                        </select>
                                    </div>
                                </div>

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

