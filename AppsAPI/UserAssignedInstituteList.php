<?php
require '../vendor/autoload.php';

use Solvers\Dsql\Application;

$app = new Application();

include '../Config/config.php';

//https://scempbbs.com/AppsAPI/UserAssignedInstituteList.php?userid=69

$UserId = $_REQUEST['userid'];
$formId = $formIdSamplingData;

$query = "
IF object_id('tempdb..##TempSMData') is not null
	BEGIN
		drop table ##TempInstData;
	END
CREATE TABLE ##TempInstData(ID INT, Bsic_Code VARCHAR(50), MobileNo VARCHAR(50), InstName NVARCHAR(MAX));

INSERT ##TempInstData (ID, Bsic_Code, MobileNo, InstName)
SELECT id, BSIC_CODE, MOBILE_NO, RTRIM(LTRIM(Q4A)) InstName FROM InstituteInfo WHERE UserID = '$UserId' AND Type = 'Establishment'
AND id NOT IN (SELECT SampleHHNo FROM xformrecord WHERE FormId='$formId' AND UserID = '$UserId') ORDER BY id ASC;";
$app->getDBConnection()->Query($query);

/*$query2 = "Declare @InstList AS nvarchar(max);
SELECT @InstList = COALESCE(@InstList + '~~~', '') + CONCAT('ID_', ID, '_BSICCODE_', Bsic_Code, '_MOBILE_', MobileNo, '_NAME_', InstName) 
FROM ##TempInstData ORDER BY ID ASC; 
select @InstList as InstNumbers";*/

$query2 = "Declare @InstList AS nvarchar(max);
SELECT @InstList = COALESCE(@InstList + '~~~', '') + CONCAT('NAME_', InstName, '_MOBILE_', MobileNo, '_BSICCODE_', Bsic_Code, '_ID_', ID) 
FROM ##TempInstData ORDER BY ID ASC; 
select @InstList as InstNumbers";

$InstList = $app->getDBConnection()->query($query2);

foreach ($InstList as $row) {
    $UserAssignedInst = $row->InstNumbers;
    echo $UserAssignedInst;
}

