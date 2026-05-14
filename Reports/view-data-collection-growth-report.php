<?php
$qryFormName = "SELECT id, FormName FROM datacollectionform WHERE CompanyID = ? AND Status = '$formActiveStatus' ORDER BY id";
$rsQryFormName = $app->getDBConnection()->fetchAll($qryFormName, $loggedUserCompanyID);

$divQuery = "SELECT DISTINCT Division_Code as DivisionCode, Division_Name as DivisionName FROM InstituteInfo ORDER BY Division_Name ASC";
$rsDivQuery = $app->getDBConnection()->fetchAll($divQuery);

if (strpos($loggedUserName, 'dist') !== false) {
    $divQuery = "SELECT DISTINCT p.Division_Name as DivisionName, p.Division_Code as DivisionCode FROM InstituteInfo AS p 
    JOIN assignsupervisor AS a ON p.UserID = a.UserID 
    WHERE a.DistCoordinatorID = ?";
    $rsDivQuery = $app->getDBConnection()->fetchAll($divQuery, $loggedUserID);
} elseif (strpos($loggedUserName, 'val') !== false) {
    $divQuery = "SELECT DISTINCT p.Division_Name as DivisionName, p.Division_Code as DivisionCode FROM InstituteInfo AS p 
    JOIN assignsupervisor AS a ON p.UserID = a.UserID 
    WHERE a.ValidatorID = ?";
    $rsDivQuery = $app->getDBConnection()->fetchAll($divQuery, $loggedUserID);
} else {
    $divQuery = "SELECT DISTINCT Division_Name as DivisionName, Division_Code as DivisionCode FROM InstituteInfo ORDER BY DivisionName ASC";
    $rsDivQuery = $app->getDBConnection()->fetchAll($divQuery);
}

if (strpos($loggedUserName, 'cval') !== false) {
    $divQuery = "SELECT DISTINCT p.Division_Name as DivisionName, p.Division_Code as DivisionCode FROM InstituteInfo AS p 
    JOIN assignsupervisor AS a ON p.PSUUserID = a.UserID";
    $rsDivQuery = $app->getDBConnection()->fetchAll($divQuery);
}

if ($_REQUEST['show'] === 'Show') {

    $FormID = xss_clean($_REQUEST['FormID']);

    $DivisionCode = xss_clean($_REQUEST['DivisionCode']);
    $DistrictCode = xss_clean($_REQUEST['DistrictCode']);
    $UpazilaCode = xss_clean($_REQUEST['UpazilaCode']);
    $UnionWardCode = xss_clean($_REQUEST['UnionWardCode']);
    $MauzaCode = xss_clean($_REQUEST['MauzaCode']);
    $VillageCode = xss_clean($_REQUEST['VillageCode']);
}
?>

<div class="inner-wrapper">
    <section role="main" class="content-body">
        <header class="page-header">
            <h2><?php echo $MenuLebel; ?></h2>

            <?php include_once 'Components/header-home-button.php'; ?>
        </header>

        <!-- start: page -->
        <div class="row">
            <div class="col-lg-12 mb-0">
                <section class="card">
                    <div class="card-body">
                        <form class="form-horizontal form-bordered" action="" method="post">
                            <div class="form-group row pb-3">
                                <label class="col-lg-3 control-label text-sm-end pt-2">Form Select<span
                                            class="required">*</span></label>
                                <div class="col-lg-6">
                                    <select data-plugin-selectTwo class="form-control populate" name="FormID"
                                            id="FormID" required>
                                        <option value="">Select Form</option>
                                            <?PHP
                                            foreach ($rsQryFormName as $row) {
                                                echo '<option value="' . $row->id . '"' . (isset($FormID) && !empty($FormID) && $row->id == $FormID ? ' selected' : '') . '>' . $row->FormName . '</option>';
                                            }
                                            ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row pb-3">
                                <label class="col-lg-3 control-label text-sm-end pt-2">Division Select
                                    <?php if (strpos($loggedUserName, 'admin') === false) { ?>
                                        <span class="required">*</span>
                                    <?php } ?>
                                </label>
                                <div class="col-lg-6">
                                    <select data-plugin-selectTwo class="form-control populate" name="DivisionCode"
                                            id="DivisionCode"
                                        <?php if (strpos($loggedUserName, 'admin') === false) { ?>
                                            required
                                        <?php } ?>
                                            onchange="ShowDropDown('DivisionCode', 'DistrictDiv', 'ShowDistrict', 'ShowUpazila')">
                                        <option value="">Choose division</option>
                                        <?PHP
                                        foreach ($rsDivQuery as $row) {
                                            echo '<option value="' . $row->DivisionCode . '"' . (isset($DivisionCode) && !empty($DivisionCode) && $row->DivisionCode == $DivisionCode ? ' selected' : '') . '>' . $row->DivisionName . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div id="geoDiv" style="display: none">
                                <div class="form-group row pb-3" id="DistrictDiv"></div>
                                <div class="form-group row pb-3" id="UpazilaDiv"></div>
                                <!--<div class="form-group row pb-3" id="UnionWardDiv"></div>
                                <div class="form-group row pb-3" id="MauzaDiv"></div>
                                <div class="form-group row pb-3" id="VillageDiv"></div>-->
                            </div>

                            <footer class="card-footer">
                                <div class="row justify-content-end">
                                    <div class="col-lg-9">
                                        <input class="btn btn-primary" name="show" type="submit" id="show"
                                               value="Show">

                                        <button type="button" class="btn btn-secondary ms-4" id="clearForm">Clear
                                        </button>
                                    </div>
                                </div>
                            </footer>
                        </form>
                    </div>
                </section>
                <?php
                if ($_REQUEST['show'] === 'Show') {

                    $FormName = getValue('datacollectionform', 'FormName', "id = $FormID");

                    $ReportCondition = "";
                    $getFieldValue = 'Division_Name';

                    if (!empty($DivisionCode)) {
                        $DivisionName = getValue('InstituteInfo', 'DISTINCT(Division_Name)', "Division_Code = '$DivisionCode'");
                        $getFieldValue = 'District_Name';
                        $ReportCondition .= " AND ( ps.Division_Code = '" . $DivisionCode . "') ";
                        //echo $DivisionName;
                    }

                    if (!empty($DistrictCode)) {
                        $DistrictName = getValue('InstituteInfo', 'DISTINCT(District_Name)', "Division_Code = '$DivisionCode' AND District_Code = '$DistrictCode'");
                        $DistrictName = ' > ' . $DistrictName;
                        //echo $DistrictName;

                        $getFieldValue = 'Upazila_Name';
                        $ReportCondition .= " AND ( ps.District_Code = '" . $DistrictCode . "') ";
                    }

                    if (!empty($UpazilaCode)) {
                        $UpazilaName = getValue(
                            'InstituteInfo',
                            'DISTINCT(Upazila_Name)',
                            "Division_Code = '$DivisionCode' AND District_Code = '$DistrictCode' AND Upazila_Code = '$UpazilaCode'"
                        );
                        $UpazilaName = ' > ' . $UpazilaName;
                        //echo $UpazilaName;

                        $getFieldValue = 'Union_Name';
                        $ReportCondition .= " AND ( ps.Upazila_Code = '" . $UpazilaCode . "') ";
                    }

                    if (!empty($UnionWardCode)) {
                        $UnionWardName = getValue(
                            'InstituteInfo',
                            'DISTINCT(Union_Name)',
                            "Division_Code = '$DivisionCode' AND District_Code = '$DistrictCode' AND Upazila_Code = '$UpazilaCode' AND Union_Code = '$UnionWardCode'"
                        );
                        $UnionWardName = ' > ' . $UnionWardName;

                        $getFieldValue = 'Mouza_Name';
                        $ReportCondition .= " AND ( ps.Union_Code = '" . $UnionWardCode . "') ";
                    }

                    if (!empty($MauzaCode)) {
                        $MauzaName = getValue(
                            'InstituteInfo',
                            'DISTINCT(Mouza_Name)',
                            "Division_Code = '$DivisionCode' AND District_Code = '$DistrictCode' AND Upazila_Code = '$UpazilaCode' AND Union_Code = '$UnionWardCode' AND Mouza_Code = '$MauzaCode'"
                        );
                        $MauzaName = ' > ' . $MauzaName;

                        $getFieldValue = 'Village_Name';
                        $ReportCondition .= " AND ( ps.Mouza_Code = '" . $MauzaCode . "') ";
                    }

                    if (!empty($VillageCode)) {
                        $VillageName = getValue(
                            'InstituteInfo',
                            'DISTINCT(Village_Name)',
                            "Division_Code = '$DivisionCode' AND District_Code = '$DistrictCode' AND Upazila_Code = '$UpazilaCode' AND Union_Code = '$UnionWardCode' AND Mouza_Code = '$MauzaCode' AND Village_Code = '$VillageCode'"
                        );
                        $VillageName = ' > ' . $VillageName;
                        //echo $VillageName;
                    }

                    if ($FormID == $formIdSamplingData) {
                        $QueryTarget = "SELECT COUNT(SQ.Target) as Target FROM (SELECT DISTINCT id as Target FROM InstituteInfo WHERE Type='$InstType'";
                    } elseif ($FormID == $formIdMainData) {
                        $QueryTarget = "SELECT COUNT(SQ.Target) as Target FROM (SELECT DISTINCT id as Target FROM InstituteInfo WHERE Type='$MunType'";
                    }

                    $QueryCollected = "SELECT COUNT(id) as Collected FROM xformrecord WHERE CompanyID = ? AND FormId=$FormID AND SampleHHNo 
                    IN(SELECT DISTINCT id FROM InstituteInfo WHERE 1=1 ";

                    if (!empty($DivisionCode)) {
                        $QueryTarget .= " AND ( Division_Code = '" . $DivisionCode . "') ";
                        $QueryCollected .= " AND ( Division_Code = '" . $DivisionCode . "') ";
                    }

                    if (!empty($DistrictCode)) {
                        $QueryTarget .= " AND ( District_Code = '" . $DistrictCode . "') ";
                        $QueryCollected .= " AND ( District_Code = '" . $DistrictCode . "') ";
                    }

                    if (!empty($UpazilaCode)) {
                        $QueryTarget .= " AND ( Upazila_Code = '" . $UpazilaCode . "') ";
                        $QueryCollected .= " AND ( Upazila_Code = '" . $UpazilaCode . "') ";
                    }

                    if (!empty($UnionWardCode)) {
                        $QueryTarget .= " AND ( Union_Code = '" . $UnionWardCode . "') ";
                        $QueryCollected .= " AND ( Union_Code = '" . $UnionWardCode . "') ";
                    }

                    if (!empty($MauzaCode)) {
                        $QueryTarget .= " AND ( Mouza_Code = '" . $MauzaCode . "') ";
                        $QueryCollected .= " AND ( Mouza_Code = '" . $MauzaCode . "') ";
                    }

                    if (!empty($VillageCode)) {
                        $QueryTarget .= " AND ( Village_Code = '" . $VillageCode . "') ";
                        $QueryCollected .= " AND ( Village_Code = '" . $VillageCode . "') ";
                    }

                    $QueryTarget .= ") SQ";
                    $QueryCollected .= ")";


                    /*echo $QueryTarget;
                    echo '<br>';
                    echo $QueryCollected;
					exit;*/

                    if ($FormID == $formIdSamplingData) {
                        $locationReportQuery = "SELECT ps.$getFieldValue as Name, 
												COUNT(ps.id) as Target, 
												COUNT(CASE WHEN xfr.FormId = $FormID THEN xfr.id END) as Collected 
											FROM InstituteInfo ps 
												LEFT JOIN xformrecord xfr ON xfr.SampleHHNo = ps.id 
											WHERE ps.Type='$InstType' $ReportCondition 
											GROUP BY ps.$getFieldValue";
                    } elseif ($FormID == $formIdMainData) {
                        $locationReportQuery = "SELECT ps.$getFieldValue as Name, 
												COUNT(ps.id) as Target, 
												COUNT(CASE WHEN xfr.FormId = $FormID THEN xfr.id END) as Collected 
											FROM InstituteInfo ps 
												LEFT JOIN xformrecord xfr ON xfr.SampleHHNo = ps.id 
											WHERE ps.Type='$MunType' $ReportCondition 
											GROUP BY ps.$getFieldValue";
                    }

                    //echo $locationReportQuery;
                    //exit();

                    if (empty($DivisionCode)) {
                        if ($FormID == $formIdMainData) {
                            //$locationReportQuery = "";
                            $targetSQL = "SELECT ps.Division_Name, 
											ISNULL(COUNT(ps.id),0) AS Target
										FROM InstituteInfo ps 
										WHERE ps.Type='$MunType'
										GROUP BY ps.Division_Name";
                        } elseif ($FormID == $formIdSamplingData) {
                            $targetSQL = "SELECT ps.Division_Name, 
											ISNULL(COUNT(ps.id),0) AS Target
										FROM InstituteInfo ps 
										WHERE ps.Type='$InstType'
										GROUP BY ps.Division_Name";
                        }
                        //echo $targetSQL;
                        //exit();
                        $rsDistrictTarget = $app->getDBConnection()->fetchAll($targetSQL);
                        $DistTargetArray = array();
                        foreach ($rsDistrictTarget as $rowTarget) {
                            $DistTargetArray[strtolower($rowTarget->Division_Name)] = $rowTarget->Target;
                        }
                    }
                    if (!empty($DivisionCode)) {
                        if ($FormID == $formIdMainData) {
                            //$locationReportQuery = "";
                            $targetSQL = "SELECT ps.District_Name, 
											ISNULL(COUNT(ps.id),0) AS Target
										FROM InstituteInfo ps 
										WHERE ps.Type='$MunType' AND ps.Division_Code = ?
										GROUP BY ps.District_Name";
                        } elseif ($FormID == $formIdSamplingData) {
                            $targetSQL = "SELECT ps.District_Name, 
											ISNULL(COUNT(ps.id),0) AS Target
										FROM InstituteInfo ps 
										WHERE ps.Type='$InstType' AND ps.Division_Code = ?
										GROUP BY ps.District_Name";
                        }
                        $rsDistrictTarget = $app->getDBConnection()->fetchAll($targetSQL, $DivisionCode);
                        $DistTargetArray = array();
                        foreach ($rsDistrictTarget as $rowTarget) {
                            $DistTargetArray[strtolower($rowTarget->District_Name)] = $rowTarget->Target;
                        }
                    }
                    if (!empty($DistrictCode)) {
                        if ($FormID == $formIdMainData) {
                            //$locationReportQuery = "";
                            $targetSQL = "SELECT ps.Upazila_Name, 
											ISNULL(COUNT(ps.id),0) AS Target
										FROM InstituteInfo ps 
										WHERE ps.Type='$MunType' AND ps.Division_Code = ? AND ps.District_Code = ?
										GROUP BY ps.Upazila_Name";
                        } elseif ($FormID == $formIdSamplingData) {
                            $targetSQL = "SELECT ps.Upazila_Name, 
											ISNULL(COUNT(ps.id),0) AS Target
										FROM InstituteInfo ps 
										WHERE ps.Type='$InstType' AND ps.Division_Code = ? AND ps.District_Code = ?
										GROUP BY ps.Upazila_Name";
                        }

                        $rsDistrictTarget = $app->getDBConnection()->fetchAll($targetSQL, $DivisionCode, $DistrictCode);
                        $DistTargetArray = array();
                        foreach ($rsDistrictTarget as $rowTarget) {
                            $DistTargetArray[strtolower($rowTarget->Upazila_Name)] = $rowTarget->Target;
                        }
                    }
                    if (!empty($UpazilaCode)) {
                        if ($FormID == $formIdMainData) {
                            //$locationReportQuery = "";
                            $targetSQL = "SELECT ps.Union_Name, 
											ISNULL(COUNT(ps.id),0) AS Target
										FROM InstituteInfo ps 
										WHERE ps.Type='$MunType' AND ps.Division_Code = ? AND ps.District_Code = ? AND ps.Upazila_Code = ?
										GROUP BY ps.Union_Name";
                        } elseif ($FormID == $formIdSamplingData) {
                            $targetSQL = "SELECT ps.Union_Name, 
											ISNULL(COUNT(ps.id),0) AS Target
										FROM InstituteInfo ps 
										WHERE ps.Type='$InstType' AND ps.Division_Code = ? AND ps.District_Code = ? AND ps.Upazila_Code = ?
										GROUP BY ps.Union_Name";
                        }

                        $rsDistrictTarget = $app->getDBConnection()->fetchAll($targetSQL, $DivisionCode, $DistrictCode, $UpazilaCode);
                        $DistTargetArray = array();
                        foreach ($rsDistrictTarget as $rowTarget) {
                            $DistTargetArray[strtolower($rowTarget->Union_Name)] = $rowTarget->Target;
                        }
                    }


                    $rsTarget = $app->getDBConnection()->fetch($QueryTarget);
                    $Target = $rsTarget->Target;

                    $rsCollected = $app->getDBConnection()->fetch($QueryCollected, $loggedUserCompanyID);
                    $Collected = $rsCollected->Collected;

                    $CountData = $Target + $Collected;

                    $DataCollectionPercentage = Ratio($Collected, $Target);

                    $rsLocationReport = $app->getDBConnection()->fetchAll($locationReportQuery);

                    ?>
                    <div class="card">
                        <div class="row">
                            <div class="col-lg-12 mb-3">
                                <section class="card">
                                    <div class="card-header">
                                        <div class="card-title">Data Status : <?php echo $FormName; ?></div>
                                        <div class="card-subtitle"><?php echo $DivisionName . $DistrictName . $UpazilaName . $UnionWardName . $MauzaName . $VillageName; ?></div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-12 text-center">
                                                <div class="liquid-meter-wrapper liquid-meter-lg mt-3">
                                                    <div class="liquid-meter">
                                                        <meter min="0" max="100"
                                                               value="<?php echo $DataCollectionPercentage; ?>"
                                                               id="meterSales"></meter>
                                                    </div>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-responsive-lg table-bordered table-striped table-sm mb-0">
                                                        <thead>
                                                        <tr>
                                                            <th>Target</th>
                                                            <th>Collected</th>
                                                            <th>Remaining</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td><?php echo $Target; ?></td>
                                                            <td><?php echo $Collected; ?></td>
                                                            <td><?php echo $Target - $Collected; ?></td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                <section class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-12 text-center">
                                                <div class="table-responsive">
                                                    <table class="table table-responsive-lg table-bordered table-striped table-sm mb-0">
                                                        <thead>
                                                        <tr>
                                                            <th>Name</th>
                                                            <th>Target</th>
                                                            <th>Collected</th>
                                                            <th>Remaining</th>
                                                            <th>Progress</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php foreach ($rsLocationReport as $row) { ?>
                                                            <tr>
                                                                <td><?php echo $row->Name; ?></td>
                                                                <td><?php echo $DistTargetArray[strtolower($row->Name)]; ?></td>
                                                                <td><?php echo $row->Collected; ?></td>
                                                                <td><?php echo ($DistTargetArray[strtolower($row->Name)] - $row->Collected) > 0 ? $DistTargetArray[strtolower($row->Name)] - $row->Collected : 0; ?></td>
                                                                <td><?php echo $DistTargetArray[strtolower($row->Name)] > 0 ? Ratio($row->Collected, $DistTargetArray[strtolower($row->Name)]) : '0.00%'; ?></td>
                                                            </tr>
                                                        <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
                <!-- end: page -->
            </div>
        </div>
        <!-- end: page -->
    </section>
</div>

<script>
    $(document).ready(function () {
        populateDropdowns(
            <?php echo isset($DivisionCode) && $DivisionCode !== '' ? $DivisionCode : 'null'; ?>,
            <?php echo isset($DistrictCode) && $DistrictCode !== '' ? $DistrictCode : 'null'; ?>,
            <?php echo isset($UpazilaCode) && $UpazilaCode !== '' ? $UpazilaCode : 'null'; ?>,
            <?php echo isset($UnionWardCode) && $UnionWardCode !== '' ? $UnionWardCode : 'null'; ?>,
            <?php echo isset($MauzaCode) && $MauzaCode !== '' ? $MauzaCode : 'null'; ?>,
            <?php echo isset($VillageCode) && $VillageCode !== '' ? $VillageCode : 'null'; ?>
        );
    });
</script>