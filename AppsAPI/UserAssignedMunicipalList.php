<?php
require '../vendor/autoload.php';

use Solvers\Dsql\Application;

$app = new Application();

include '../Config/config.php';

//https://scempbbs.com/AppsAPI/UserAssignedMunicipalList.php?userid=69

$UserId = $_REQUEST['userid'];
$formId = $formIdMainData;

$query = "
IF object_id('tempdb..##TempMunData') is not null
	BEGIN
		drop table ##TempMunData;
	END
CREATE TABLE ##TempMunData(ID INT, Division VARCHAR(50), District VARCHAR(50), InstName NVARCHAR(MAX));

INSERT ##TempMunData (ID, Division, District, InstName)
SELECT id, DIVISION_NAME, DISTRICT_NAME, RTRIM(LTRIM(Q4A)) InstName FROM InstituteInfo WHERE UserID = '$UserId' AND Type = 'Municipal'
AND id NOT IN (SELECT SampleHHNo FROM xformrecord WHERE FormId='$formId' AND UserID = '$UserId') ORDER BY id ASC;";
$app->getDBConnection()->Query($query);

/*$query2 = "Declare @MunList AS nvarchar(max);
SELECT @MunList = COALESCE(@MunList + '~~~', '') + CONCAT('ID_', ID, '_DIV_', Division, '_DIST_', District, '_NAME_', InstName) 
FROM ##TempMunData ORDER BY ID ASC; 
select @MunList as MunNumbers";*/

$query2 = "Declare @MunList AS nvarchar(max);
SELECT @MunList = COALESCE(@MunList + '~~~', '') + CONCAT('NAME_', InstName, '_DIV_', Division, '_DIST_', District, '_ID_', ID) 
FROM ##TempMunData ORDER BY ID ASC; 
select @MunList as MunNumbers";
$MunList = $app->getDBConnection()->query($query2);

foreach ($MunList as $row) {
    $UserAssignedMun = $row->MunNumbers;
    echo $UserAssignedMun;
}

