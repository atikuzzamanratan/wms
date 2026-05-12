<?php
error_reporting(1);

require '../../vendor/autoload.php';
include "../../Config/config.php";
include "../../Lib/lib.php";

$app = new Solvers\Dsql\Application();

if ($_REQUEST['frmID'] != '') {
    $FormID = $frmID = $app->cleanInput($_REQUEST['frmID']);
}

if ($_REQUEST['dataCollectorNamePrefix'] != '') {
    $dataCollectorNamePrefix = $app->cleanInput($_REQUEST['dataCollectorNamePrefix']);
}

if ($_REQUEST['DivisionCode'] != '') {
    $DivisionCode = $app->cleanInput($_REQUEST['DivisionCode']);
}

if ($_REQUEST['DistrictCode'] != '') {
    $DistrictCode = $app->cleanInput($_REQUEST['DistrictCode']);
}

if ($_REQUEST['UpazilaCode'] != '') {
    $UpazilaCode = $app->cleanInput($_REQUEST['UpazilaCode']);
}

if ($_REQUEST['UnionWardCode'] != '') {
    $UnionWardCode = $app->cleanInput($_REQUEST['UnionWardCode']);
}

if ($_REQUEST['MauzaCode'] != '') {
    $MauzaCode = $app->cleanInput($_REQUEST['MauzaCode']);
}

if ($_REQUEST['VillageCode'] != '') {
    $VillageCode = $app->cleanInput($_REQUEST['VillageCode']);
}

$qryCreate = "SELECT ui.id, 
				ui.UserName, 
				ui.FullName, 
				ui.MobileNumber, 
				sup.UserName AS SupUserName,
				sup.FullName AS SupFullName,
				sup.MobileNumber AS SupMobileNumber,
				ISNULL(COUNT(xfr.id), 0) AS Number, ";
if ($FormID==$formIdMainData) {
	//$qryCreate .= " (SELECT DISTINCT STRING_AGG(pl.District_Name, ', ') WITHIN GROUP (ORDER BY pl.District_Name) FROM InstituteInfo pl WHERE pl.UserID = ui.id) AS DistrictName, ";
	$qryCreate .= " (SELECT STRING_AGG(District_Name, ', ') WITHIN GROUP (ORDER BY District_Name) FROM (SELECT DISTINCT TRIM(District_Name) AS District_Name FROM InstituteInfo pl WHERE pl.UserID = ui.id AND pl.District_Name IS NOT NULL AND TRIM(pl.District_Name) <> '') AS DistinctDistricts) AS DistrictName, ";
	$qryCreate .= " (SELECT ISNULL(COUNT(pli.id),0) FROM InstituteInfo pli WHERE pli.UserID = ui.id and pli.Type='Municipal') AS FormTarget";
} elseif ($FormID ==$formIdSamplingData) {
    //$qryCreate .= " (SELECT DISTINCT STRING_AGG(pl.District_Name, ', ') WITHIN GROUP (ORDER BY pl.District_Name) FROM InstituteInfo pl WHERE pl.UserID = ui.id) AS DistrictName, ";
    $qryCreate .= " (SELECT STRING_AGG(District_Name, ', ') WITHIN GROUP (ORDER BY District_Name) FROM (SELECT DISTINCT TRIM(District_Name) AS District_Name FROM InstituteInfo pl WHERE pl.UserID = ui.id AND pl.District_Name IS NOT NULL AND TRIM(pl.District_Name) <> '') AS DistinctDistricts) AS DistrictName, ";

    $qryCreate .= " (SELECT ISNULL(COUNT(pli.id),0) FROM InstituteInfo pli WHERE pli.UserID = ui.id and pli.Type='Establishment') AS FormTarget";
}

$qryCreate .= " FROM userinfo ui 
				LEFT JOIN assignsupervisor asup ON asup.UserID = ui.id
				LEFT JOIN userinfo sup ON sup.id = asup.SupervisorID 
				LEFT JOIN xformrecord xfr ON ui.id = xfr.UserID AND xfr.FormID = $FormID
			WHERE ui.UserName LIKE '$dataCollectorNamePrefix%' 
				AND ui.IsActive = 1 
			";

if (!empty($DivisionCode)) {
	$qryCreate .= " AND ui.id IN (SELECT UserID FROM InstituteInfo WHERE Division_Code = '$DivisionCode'";
	if (!empty($DistrictCode)) {
		$qryCreate .= " AND District_Code = '$DistrictCode'";
	}
	if (!empty($UpazilaCode)) {
		$qryCreate .= " AND Upazila_Code = '$UpazilaCode'";
	}
	if (!empty($UnionWardCode)) {
		$qryCreate .= " AND Union_Code = '$UnionWardCode'";
	}
	if (!empty($MauzaCode)) {
		$qryCreate .= " AND Mouza_Code = '$MauzaCode'";
	}
	if (!empty($VillageCode)) {
		$qryCreate .= " AND Village_Code = '$VillageCode'";
	}
	$qryCreate .= ")";
}

$qryCreate .= "GROUP BY ui.id, 
				ui.UserName, 
				ui.FullName, 
				ui.MobileNumber, 
				sup.UserName, 
				sup.FullName, 
				sup.MobileNumber
			ORDER BY Number DESC;";
			
//die($qryCreate);

$resQry = $app->getDBConnection()->fetchAll($qryCreate);

$data = array();

foreach ($resQry as $row) {
    $UserDBID = $row->id;

    $UserName = $row->UserName;
    $UserFullName = $row->FullName;
    $UserMobileNo = $row->MobileNumber;
    $UserMobileNo = whatsAppLink($UserMobileNo);

    $TotalDataSent = $row->Number;
	$DistrictName = $row->DistrictName;
	$SupFullName = $row->SupFullName;
	$SupMobileNumber = whatsAppLink($row->SupMobileNumber);
	$TotalTarget = $row->FormTarget;

    $DataCollectionPercentage = Ratio($TotalDataSent, $TotalTarget);

    $SubData = array();

    $SubData[] = $UserName;
    $SubData[] = $UserFullName;
    $SubData[] = $UserMobileNo;
	$SubData[] = $DistrictName;
	$SubData[] = $SupFullName;
	$SubData[] = $SupMobileNumber;
    $SubData[] = $TotalTarget;
    $SubData[] = $TotalDataSent;
    $SubData[] = $DataCollectionPercentage;

    $data[] = $SubData;
}

$jsonData = json_encode($data);

echo '{"aaData":' . $jsonData . '}';

