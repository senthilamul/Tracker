<?php 
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

                                $Qry = "INSERT INTO backlog_rawdata SELECT * FROM $TmpTableName WHERE NOT EXISTS(SELECT * FROM backlog_rawdata WHERE ($TmpTableName.`case_number`= backlog_rawdata.case_number))";

                                $qry = $conn->prepare($Qry);
                                $qry->execute();

                    }catch(PDOException $e){  
                        echo $e->getMessage(); 
                    }
                   //unlink($filePath);
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