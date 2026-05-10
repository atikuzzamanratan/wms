<?php
session_start();
error_reporting(E_ALL);

require '../vendor/autoload.php';

use Solvers\Dsql\Application;

$app = new Application();

include '../Components/header-includes.php';

require_once "../Config/config.php";

require_once "../Lib/lib.php";

$loggedUserID = $_SESSION['UserID'];
$loggedUserName = $app->cleanInput($_SESSION['User']);
$loggedUserCompanyID = $_SESSION['loggedUserCompanyID'];

$ShowFunction = $_REQUEST['ShowFunction'];
$RequestingValue = $_REQUEST['RequestingValue'];
$NextCallFunction = $_REQUEST['NextCallFunction'];

if ($ShowFunction == "DistrictUser")
    ShowDistrictUser($app, $RequestingValue, $loggedUserID);
else if ($ShowFunction == "ShowUser")
    ShowUser($app, $loggedUserName, $loggedUserID);
else if ($ShowFunction == "ShowUpazilaUser")
    ShowUpazilaUser($app, $RequestingValue);
else if ($ShowFunction == "ShowUnionWardUser")
    ShowUnionWardUser($app, $RequestingValue);
else if ($ShowFunction == "ShowMauzaUser")
    ShowMauzaUser($app, $RequestingValue);
else if ($ShowFunction == "ShowVillageUser")
    ShowVillageUser($app, $RequestingValue);

function ShowUser($app, $loggedUserName, $loggedUserID)
{
    $DivisionCode = $_REQUEST['DivisionCode'] ?? null;
    $DistrictCode = $_REQUEST['DistrictCode'] ?? null;
    $UpazilaCode = $_REQUEST['UpazilaCode'] ?? null;
    $UnionWardCode = $_REQUEST['UnionWardCode'] ?? null;
    $MauzaCode = $_REQUEST['MauzaCode'] ?? null;
    $VillageCode = $_REQUEST['VillageCode'] ?? null;
	$RequiredUser = $_REQUEST['RequiredUser'] ?? null;
	
    $query = "SELECT DISTINCT u.id, 
				u.UserName, 
				u.FullName 
			FROM userinfo AS u 
				JOIN InstituteInfo AS p ON u.id = p.UserID ";
	if (strpos($loggedUserName, 'val') !== false) {
		if (strpos($loggedUserName, 'cval') === false) {
			$query .= " JOIN assignsupervisor ap ON ap.UserID = u.id AND ap.ValidatorID = $loggedUserID ";
		}
	}
	$query .= " WHERE 1=1 ";
	if ($DivisionCode !== null) {
		$query .= " AND p.Division_Code = '$DivisionCode' ";
	}
	if ($DistrictCode !== null) {
		$query .= " AND p.District_Code = '$DistrictCode' ";
	}
	if ($UpazilaCode !== null) {
		$query .= " AND p.Upazila_Code= '$UpazilaCode' ";
	}
	if ($UnionWardCode !== null) {
		$query .= " AND p.Union_Code = '$UnionWardCode' ";
	}
	if ($MauzaCode !== null) {
		$query .= " AND p.Mouza_Code = '$MauzaCode' ";
	}
	if ($VillageCode !== null) {
		$query .= " AND p.Village_Code = '$VillageCode' ";
	}
//echo $query;
	$rsQuery = $app->getDBConnection()->fetchAll($query);


?>
    <label class="col-lg-3 control-label text-lg-end pt-2"><?=(!$RequiredUser ? "or " : "")?>User Select<?php if ($RequiredUser) { ?><span class="required">*</span><?php } else { ?><span class="required"></span><?php } ?></label>
    <div class="col-lg-6">
        <select data-plugin-selectTwo class="form-control populate" name="SelectedUserID" id="SelectedUserID" <?=($RequiredUser ? " required " : "")?>
            title="Please select a user">
            <option value="">Choose a User</option>
            <?php
            foreach ($rsQuery as $row) {
                echo '<option value="' . $row->id . '">' . $row->UserName . ' | ' . substr($row->FullName, 0, 102) . '</option>';
            }
            ?>
        </select>
    </div>
<?php
}

function ShowDistrictUser($app, $RequestingValue, $UserID)
{
    $loggedUserName = getValue('userinfo', 'UserName', "id = $UserID");

    if (strpos($loggedUserName, 'dist') !== false) {
        $query = "SELECT DISTINCT TRIM(p.District_Name) AS DistrictName, TRY_CAST(p.District_Code AS INT) AS DistrictCode FROM InstituteInfo as p JOIN assignsupervisor as a ON p.UserID = a.UserID WHERE a.DistCoordinatorID = ? AND p.Division_Code = '$RequestingValue'";
        $rsQuery = $app->getDBConnection()->fetchAll($query, $UserID);
    } else if (strpos($loggedUserName, 'cs') !== false) {
        $query = "SELECT DISTINCT TRIM(p.District_Name) AS DistrictName, TRY_CAST(p.District_Code AS INT) AS DistrictCode FROM InstituteInfo as p JOIN assignsupervisor as a ON p.UserID = a.UserID WHERE a.SupervisorID = ?";
        $rsQuery = $app->getDBConnection()->fetchAll($query, $UserID);
    } else if (strpos($loggedUserName, 'val') !== false) {
		if (strpos($loggedUserName, 'cval') === false) {
			$query = "SELECT DISTINCT TRIM(p.District_Name) AS DistrictName, TRY_CAST(p.District_Code AS INT) AS DistrictCode FROM InstituteInfo as p JOIN assignsupervisor as a ON p.UserID = a.UserID WHERE a.ValidatorID = ? AND p.DivisionCode = '$RequestingValue'";
			$rsQuery = $app->getDBConnection()->fetchAll($query, $UserID);
		} else {
			$query = "SELECT DISTINCT TRIM(p.District_Name) AS DistrictName, TRY_CAST(p.District_Code AS INT) AS DistrictCode FROM InstituteInfo as p JOIN assignsupervisor as a ON p.UserID = a.UserID WHERE p.DivisionCode = '$RequestingValue'";
			$rsQuery = $app->getDBConnection()->fetchAll($query);
		}
    } else {
        //$query = "SELECT DISTINCT District_Name as DistrictName, District_Code as DistrictCode FROM InstituteInfo WHERE DivisionCode = '$RequestingValue' order by DistrictName asc";
        $query = "SELECT DISTINCT TRIM(p.District_Name) AS DistrictName, TRY_CAST(p.District_Code AS INT) AS DistrictCode FROM InstituteInfo p WHERE p.Division_Code = $RequestingValue ORDER BY DistrictCode ASC";
        $rsQuery = $app->getDBConnection()->fetchAll($query);
    }
    //echo $query;
    //echo $loggedUserName;
	$RequiredUser = $_REQUEST['RequiredUser'] ?? 0;
    $NextCallFunction = "ShowDropDown4('DistrictCode','UpazilaDiv','userDiv','ShowUpazilaUser',['DivisionCode','DistrictCode'], {'RequiredUser':'$RequiredUser'})";
?>

    <label class="col-lg-3 control-label text-sm-end pt-2">District Select
        <?php if (strpos($loggedUserName, 'admin') === false) { ?>
            <span class="required">*</span>
        <?php } ?>
    </label>
    <div class="col-lg-6">
        <select data-plugin-selectTwo class="form-control populate" name="DistrictCode" id="DistrictCode"
            onchange="<?php echo $NextCallFunction; ?>"
            <?php if (strpos($loggedUserName, 'admin') === false) { ?>
            required
            <?php } ?>>
            <?php
            echo "<option selected value=\"\">Select district</option>";
            foreach ($rsQuery as $row) {
                echo "<option value=\"" . $row->DistrictCode . "\">" . $row->DistrictName . "</option>";
            }
            ?>
        </select>
    </div>
<?php
}
function ShowUpazilaUser($app, $RequestingValue)
{
    //$query = "SELECT DISTINCT UpazilaName, UpazilaCode FROM PSUList WHERE DistrictCode = ? order by UpazilaName asc";
    $query = "SELECT DISTINCT TRIM(Upazila_Name) AS UpazilaName, TRY_CAST(Upazila_Code AS INT) AS UpazilaCode FROM InstituteInfo WHERE District_Code = '$RequestingValue' AND Upazila_Name IS NOT NULL AND TRIM(Upazila_Name) <> '' AND TRIM(Upazila_Name) <> 'NULL' AND Upazila_Code IS NOT NULL ORDER BY UpazilaName ASC;";
    $rsQuery = $app->getDBConnection()->fetchAll($query);

    $RequiredUser = $_REQUEST['RequiredUser'] ?? 0;
    $NextCallFunction = "ShowDropDown4('UpazilaCode','UnionWardDiv','userDiv','ShowUnionWardUser',['DivisionCode','DistrictCode','UpazilaCode'], {'RequiredUser':'$RequiredUser'})";
?>

    <label class="col-lg-3 control-label text-sm-end pt-2">Upazila Select</label>
    <div class="col-lg-6">
        <select data-plugin-selectTwo class="form-control populate" name="UpazilaCode" id="UpazilaCode"
            onchange="<?php echo $NextCallFunction; ?>">
            <?php
            echo "<option selected value=\"\">Select upazila</option>";
            foreach ($rsQuery as $row) {
                echo "<option value=\"" . $row->UpazilaCode . "\">" . $row->UpazilaName . "</option>";
            }
            ?>
        </select>
    </div>
<?php
}
function ShowUnionWardUser($app, $RequestingValue)
{
    $DistrictCode = $_REQUEST['DistrictCode'];
    //$query = "SELECT DISTINCT UnionWardName, UnionWardCode FROM PSUList WHERE DistrictCode = ? AND UpazilaCode = ? order by UnionWardName asc";

    $query = "SELECT DISTINCT UNION_NAME as UnionWardName, UNION_CODE as UnionWardCode FROM InstituteInfo WHERE District_Code = '$DistrictCode' AND Upazila_Code = '$RequestingValue' order by UnionWardName asc";
    $rsQuery = $app->getDBConnection()->fetchAll($query);

    $RequiredUser = $_REQUEST['RequiredUser'] ?? 0;
    $NextCallFunction = "ShowDropDown4('UnionWardCode','MauzaDiv','userDiv','ShowMauzaUser',['DivisionCode','DistrictCode','UpazilaCode','UnionWardCode'], {'RequiredUser':'$RequiredUser'})";
?>

    <label class="col-lg-3 control-label text-sm-end pt-2">Union/Ward Select</label>
    <div class="col-lg-6">
        <select data-plugin-selectTwo class="form-control populate" name="UnionWardCode" id="UnionWardCode"
            onchange="<?php echo $NextCallFunction; ?>">
            <?php
            echo "<option selected value=\"\">Select Ward</option>";
            foreach ($rsQuery as $row) {
                echo "<option value=\"" . $row->UnionWardCode . "\">" . $row->UnionWardName . "</option>";
            }
            ?>
        </select>
    </div>
<?php
}
function ShowMauzaUser($app, $RequestingValue)
{
    $DistrictCode = $_REQUEST['DistrictCode'];
    $UpazilaCode = $_REQUEST['UpazilaCode'];
    //$query = "SELECT DISTINCT MauzaName, MauzaCode FROM PSUList WHERE DistrictCode = ? AND UpazilaCode = ? AND UnionWardCode = ? order by MauzaName asc";

    $query = "SELECT DISTINCT Mouza_Name as MauzaName, Mouza_Code as MauzaCode FROM InstituteInfo WHERE District_Code = '$DistrictCode' AND Upazila_Code = '$UpazilaCode' AND Union_Code = '$RequestingValue' order by MauzaName asc";

    $rsQuery = $app->getDBConnection()->fetchAll($query);

    $RequiredUser = $_REQUEST['RequiredUser'] ?? 0;
    $NextCallFunction = "ShowDropDown4('MauzaCode','VillageDiv','userDiv','ShowVillageUser',['DivisionCode','DistrictCode','UpazilaCode','UnionWardCode','MauzaCode'], {'RequiredUser':'$RequiredUser'})";
?>

    <label class="col-lg-3 control-label text-sm-end pt-2">Mauza Name Select</label>
    <div class="col-lg-6">
        <select data-plugin-selectTwo class="form-control populate" name="MauzaCode" id="MauzaCode"
            onchange="<?php echo $NextCallFunction; ?>">
            <?php
            echo "<option selected value=\"\">Select Mauza</option>";
            foreach ($rsQuery as $row) {
                echo "<option value=\"" . $row->MauzaCode . "\">" . $row->MauzaName . "</option>";
            }
            ?>
        </select>
    </div>
<?php
}

function ShowVillageUser($app, $RequestingValue)
{
    $DistrictCode = $_REQUEST['DistrictCode'];
    $UpazilaCode = $_REQUEST['UpazilaCode'];
    $UnionWardCode = $_REQUEST['UnionWardCode'];
    //$query = "SELECT DISTINCT VillageName, VillageCode FROM PSUList WHERE DistrictCode = ? AND UpazilaCode = ? AND UnionWardCode = ? AND MauzaCode = ? order by VillageName asc";
    $query = "SELECT DISTINCT Village_Name as VillageName, Village_Code as VillageCode FROM InstituteInfo WHERE District_Code = '$DistrictCode' AND Upazila_Code = '$UpazilaCode' AND Union_Code = '$UnionWardCode' AND Mouza_Code = '$RequestingValue' order by VillageName asc";
    $rsQuery = $app->getDBConnection()->fetchAll($query);

?>

    <label class="col-lg-3 control-label text-sm-end pt-2">Village Name Select</label>
    <div class="col-lg-6">
        <select data-plugin-selectTwo class="form-control populate" name="VillageCode" id="VillageCode">
            <?php
            echo "<option selected value=\"\">Select Village</option>";
            foreach ($rsQuery as $row) {
                echo "<option value=\"" . $row->VillageCode . "\">" . $row->VillageName . "</option>";
            }
            ?>
        </select>
    </div>
<?php
}
