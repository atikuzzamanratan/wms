<?php
error_reporting(1);

require '../../vendor/autoload.php';
include "../../Config/config.php";
include "../../Lib/lib.php";

$app = new Solvers\Dsql\Application();

$baseURL = get_base_url();

if ($_REQUEST['selFormID'] != '') {
    $SelectedFormID = $app->cleanInput($_REQUEST['selFormID']);
}

if ($_REQUEST['selUserID'] != '') {
    $SelectedUserID = $app->cleanInput($_REQUEST['selUserID']);
}

if ($_REQUEST['selColName'] != '') {
    $SelectedColumnName = $app->cleanInput($_REQUEST['selColName']);
}

if ($_REQUEST['selDataStatus'] != '') {
    $SelectedDataStatus = $app->cleanInput($_REQUEST['selDataStatus']);
}

if ($_REQUEST['lun'] != '') {
    $LoggedUserName = $app->cleanInput($_REQUEST['lun']);
}

if ($_REQUEST['lui'] != '') {
    $LoggedUserID = $app->cleanInput($_REQUEST['lui']);
}

if ($_REQUEST['ci'] != '') {
    $SelectedCompanyID = $app->cleanInput($_REQUEST['ci']);
}

if ($SelectedDataStatus == 0) {
    $tbl = 'masterdatarecord_Pending';
} elseif ($SelectedDataStatus == 1) {
    $tbl = 'masterdatarecord_Approved';
} elseif ($SelectedDataStatus == 2) {
    $tbl = 'masterdatarecord_UnApproved';
}

$qry = "
SELECT 
    mdr.XFormRecordId,
    mdr.DataName,
    ui.UserName,
    ui.FullName,
    CAST(mdr.ColumnName AS NVARCHAR(MAX)) AS ColumnName,
    CAST(mdr.ColumnValue AS NVARCHAR(MAX)) AS ColumnValue,
    mdr.EntryDate,
    mdr.id,
    pl.Division_Name,
    pl.District_Name
FROM $tbl AS mdr
JOIN userinfo AS ui ON mdr.UserID = ui.id
LEFT JOIN InstituteInfo AS pl ON TRY_CAST(mdr.SampleHHNo AS BIGINT) = pl.id
WHERE ui.id = ?
  AND mdr.IsApproved = ?
  AND mdr.FormId = ?
  AND CAST(mdr.ColumnName AS NVARCHAR(MAX)) = ?
ORDER BY mdr.XFormRecordId ASC";
$resQry = $app->getDBConnection()->fetchAll($qry, $SelectedUserID, $SelectedDataStatus, $SelectedFormID, $SelectedColumnName);

$data = array();
$il = 1;

foreach ($resQry as $row) {
    $RecordID = $row->XFormRecordId;
    $XFormsFilePath = getValue('xformrecord', 'XFormsFilePath', "id = $RecordID");
    $DataName = $row->DataName;

    $UserName = $row->UserName;
    $UserFullName = $row->FullName;
    $UserData = "$UserFullName ($UserName)";

    $ColumnName = $row->ColumnName;
    $ColumnValue = $row->ColumnValue;
    $EntryDate = date_format($row->EntryDate, 'd-m-Y H:i:s');
		
	$DivisionName = $row->Division_Name;
	$DistrictName = $row->District_Name;

    $SubData = array();

    $SubData[] = $RecordID;
	$SubData[] = $DivisionName;
	$SubData[] = $DistrictName;
    $SubData[] = $UserData;
    $SubData[] = $DataName;
    $SubData[] = $ColumnValue;
    $SubData[] = $EntryDate;


    $actions = '<div style="display:flex;align-items:center;justify-content:center;">
                    <button type="button"
                            class="btn btn-outline-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#viewDataModal"
                            onclick="ShowDataDetail('
                                . "'$SelectedFormID','$RecordID','$SelectedDataStatus','$LoggedUserID','$SelectedUserID','$XFormsFilePath'" .
                            ')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>';

    $SubData[] = $actions;

    $il++;

    $data[] = $SubData;
}

$jsonData = json_encode($data);

echo '{"aaData":' . $jsonData . '}';

