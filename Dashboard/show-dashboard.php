<?php
$FormID = $_SESSION["FORMID"];

//echo "hhhh: $FormID<br>";

$qrySupervisor = "SELECT id FROM assignsupervisor WHERE SupervisorID = ?";
$rsSupervisor = $app->getDBConnection()->fetch($qrySupervisor, $loggedUserID);
$SuperID = $rsSupervisor->id;

if (strpos($loggedUserName, 'dist') !== false) {
    $divQuery = "SELECT DISTINCT p.Division_Name as DivisionName, p.Division_Code as DivisionCode FROM InstituteInfo AS p 
    JOIN assignsupervisor AS a ON p.UserID = a.UserID 
    WHERE a.DistCoordinatorID = $loggedUserID";
    $rsDivQuery = $app->getDBConnection()->fetchAll($divQuery);
} elseif (strpos($loggedUserName, 'div') !== false) {
    $divQuery = "SELECT DISTINCT p.Division_Name as DivisionName, p.Division_Code as DivisionCode FROM InstituteInfo AS p 
    JOIN assignsupervisor AS a ON p.UserID = a.UserID 
    WHERE a.UserID>0 AND a.DivCoordinatorID = ?";
    $rsDivQuery = $app->getDBConnection()->fetchAll($divQuery, $loggedUserID);
}  else {
    $divQuery = "SELECT DISTINCT Division_Name as DivisionName, Division_Code as DivisionCode FROM InstituteInfo ORDER BY Division_Name ASC";
    $rsDivQuery = $app->getDBConnection()->fetchAll($divQuery);
}

if($_REQUEST['show'] === 'Show'){
    $DivisionCode = $_REQUEST['DivisionCode'];
    $DistrictCode = $_REQUEST['DistrictCode'];
    $UpazilaCode = $_REQUEST['UpazilaCode'];
    $UnionWardCode = $_REQUEST['UnionWardCode'];
    $MauzaCode = $_REQUEST['MauzaCode'];
    $VillageCode = $_REQUEST['VillageCode'];
}
?>

<div class="inner-wrapper">
    <section role="main" class="content-body">
        <header class="page-header">
            <h2>Dashboard : <?php echo getName('FormName', 'datacollectionform', $FormID); ?></h2>

            <?php include_once 'Components/header-home-button.php'; ?>
        </header>

        <div class="row">
            <div class="col-lg-12 mb-0">
                <section class="card">
                    <div class="card-body">
                        <form class="form-horizontal form-bordered" action="" method="post">
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
                                            onchange="ShowDropDown4('DivisionCode', 'DistrictDiv', 'ShowDistrict', 'ShowUpazila')">
                                        <option value="">Choose division</option>
                                        <?PHP
                                        foreach ($rsDivQuery as $row) {
                                            echo '<option value="' . $row->DivisionCode . '" ' . (isset($DivisionCode) && $DivisionCode == $row->DivisionCode ? 'selected' : '') . '>' . $row->DivisionName . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div id="geoDiv" style="display: none">
                                <div class="form-group row pb-3" id="DistrictDiv"></div>
                                <div class="form-group row pb-3" id="UpazilaDiv"></div>
                                <div class="form-group row pb-3" id="UnionWardDiv"></div>
                                <div class="form-group row pb-3" id="MauzaDiv"></div>
                                <div class="form-group row pb-3" id="VillageDiv"></div>

                                <footer class="card-footer">
                                    <div class="row justify-content-end">
                                        <div class="col-lg-9">
                                            <input class="btn btn-primary" name="show" type="submit" id="show"
                                                   value="Show">

                                            <button type="button" class="btn btn-secondary ms-4" id="clearForm">Clear</button>
                                        </div>
                                    </div>
                                </footer>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </div>

        <?php
        $qryCreate = "";
        $qryCreate2 = "";
        $qryCreate3 = "";
        $qryCreate4 = "";
        $qryCreate5 = "";
        if ($_REQUEST['show'] === 'Show') {
            $DivisionCode = xss_clean($_REQUEST['DivisionCode']);
            $DistrictCode = xss_clean($_REQUEST['DistrictCode']);
            $UpazilaCode = xss_clean($_REQUEST['UpazilaCode']);
            $UnionWardCode = xss_clean($_REQUEST['UnionWardCode']);
            $MauzaCode = xss_clean($_REQUEST['MauzaCode']);
            $VillageCode = xss_clean($_REQUEST['VillageCode']);


            if (!empty($DivisionCode)) {
                $DivisionName = getValue('InstituteInfo', 'Division_Name', "Division_Code = '$DivisionCode'");
            }

            if (!empty($DistrictCode)) {
                $DistrictName = getValue('InstituteInfo', 'District_Name', "Division_Code = '$DivisionCode' AND District_Code = '$DistrictCode'");
                $DistrictName = ' > ' . $DistrictName;
            }

            if (!empty($UpazilaCode)) {
                $UpazilaName = getValue('InstituteInfo', 'Upazila_Name',
                    "Division_Code = '$DivisionCode' AND District_Code = '$DistrictCode' AND Upazila_Code = '$UpazilaCode'");
                $UpazilaName = ' > ' . $UpazilaName;
            }

            if (!empty($UnionWardCode)) {
                $UnionWardName = getValue('InstituteInfo', 'Union_Name',
                    "Division_Code = '$DivisionCode' AND District_Code = '$DistrictCode' AND Upazila_Code = '$UpazilaCode' AND Union_Code = '$UnionWardCode'");
                $UnionWardName = ' > ' . $UnionWardName;
            }

            if (!empty($MauzaCode)) {
                $MauzaName = getValue('InstituteInfo', 'Mouza_Name',
                    "Division_Code = '$DivisionCode' AND District_Code = '$DistrictCode' AND Upazila_Code = '$UpazilaCode' AND Union_Code = '$UnionWardCode' AND Mouza_Code = '$MauzaCode'");
                $MauzaName = ' > ' . $MauzaName;
            }

            if (!empty($VillageCode)) {
                $VillageName = getValue('InstituteInfo', 'Village_Name',
                    "Division_Code = '$DivisionCode' AND District_Code = '$DistrictCode' AND Upazila_Code = '$UpazilaCode' AND Union_Code = '$UnionWardCode' AND Mouza_Code = '$MauzaCode' AND Village_Code = '$VillageCode'");
                $VillageName = ' > ' . $VillageName;
            }

            //echo "$DivisionName $DistrictName $UpazilaName $UnionWardName $MauzaName $VillageName";
            //exit();

            function getQryCreate($userParam): string
            {
                if (!empty($DivisionCode)) {
                    $qryCreate .= " AND $userParam IN (SELECT UserID FROM InstituteInfo WHERE Division_Code = $DivisionCode";
                    if (!empty($DistrictCode)) {
                        $qryCreate .= " AND District_Code = $DistrictCode";
                    }
                    if (!empty($UpazilaCode)) {
                        $qryCreate .= " AND Upazila_Code = $UpazilaCode";
                    }
                    if (!empty($UnionWardCode)) {
                        $qryCreate .= " AND Union_Code = $UnionWardCode";
                    }
                    if (!empty($MauzaCode)) {
                        $qryCreate .= " AND Mouza_Code = $MauzaCode";
                    }
                    if (!empty($VillageCode)) {
                        $qryCreate .= " AND Village_Code = $VillageCode";
                    }
                    $qryCreate .= ")";
                }
                return $qryCreate;
            }

            //qryCreate
            if (!empty($DivisionCode)) {
                $qryCreate .= " AND UserID IN (SELECT UserID FROM InstituteInfo WHERE Division_Code = '$DivisionCode'";
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

            //qryCreate2
            if (!empty($DivisionCode)) {
                $qryCreate2 .= " AND UserID IN (SELECT UserID FROM InstituteInfo WHERE Division_Code = '$DivisionCode'";
                if (!empty($DistrictCode)) {
                    $qryCreate2 .= " AND District_Code = '$DistrictCode'";
                }
                if (!empty($UpazilaCode)) {
                    $qryCreate2 .= " AND Upazila_Code = '$UpazilaCode'";
                }
                if (!empty($UnionWardCode)) {
                    $qryCreate2 .= " AND Union_Code = '$UnionWardCode'";
                }
                if (!empty($MauzaCode)) {
                    $qryCreate2 .= " AND Mouza_Code = '$MauzaCode'";
                }
                if (!empty($VillageCode)) {
                    $qryCreate2 .= " AND Village_Code = '$VillageCode'";
                }
                $qryCreate2 .= ")";
            }

            //qryCreate3
            if (!empty($DivisionCode)) {
                $qryCreate3 .= " AND id IN (SELECT UserID FROM InstituteInfo WHERE Division_Code = $DivisionCode";
                if (!empty($DistrictCode)) {
                    $qryCreate3 .= " AND District_Code = '$DistrictCode'";
                }
                if (!empty($UpazilaCode)) {
                    $qryCreate3 .= " AND Upazila_Code = '$UpazilaCode'";
                }
                if (!empty($UnionWardCode)) {
                    $qryCreate3 .= " AND Union_Code = '$UnionWardCode'";
                }
                if (!empty($MauzaCode)) {
                    $qryCreate3 .= " AND Mouza_Code = '$MauzaCode'";
                }
                if (!empty($VillageCode)) {
                    $qryCreate3 .= " AND Village_Code = '$VillageCode'";
                }
                $qryCreate3 .= ")";
            }

            //qryCreate4
            if (!empty($DivisionCode)) {
                $qryCreate4 .= " AND userinfo.id IN (SELECT UserID FROM InstituteInfo WHERE Division_Code = $DivisionCode";
                if (!empty($DistrictCode)) {
                    $qryCreate4 .= " AND District_Code = '$DistrictCode'";
                }
                if (!empty($UpazilaCode)) {
                    $qryCreate4 .= " AND Upazila_Code = '$UpazilaCode'";
                }
                if (!empty($UnionWardCode)) {
                    $qryCreate4 .= " AND Union_Code = '$UnionWardCode'";
                }
                if (!empty($MauzaCode)) {
                    $qryCreate4 .= " AND Mouza_Code = '$MauzaCode'";
                }
                if (!empty($VillageCode)) {
                    $qryCreate4 .= " AND Village_Code = '$VillageCode'";
                }
                $qryCreate4 .= ")";
            }

            //qryCreate5
            if (!empty($DivisionCode)) {
                $qryCreate5 .= " AND ui.id IN (SELECT UserID FROM InstituteInfo WHERE Division_Code = $DivisionCode";
                if (!empty($DistrictCode)) {
                    $qryCreate5 .= " AND District_Code = '$DistrictCode'";
                }
                if (!empty($UpazilaCode)) {
                    $qryCreate5 .= " AND Upazila_Code = '$UpazilaCode'";
                }
                if (!empty($UnionWardCode)) {
                    $qryCreate5 .= " AND Union_Code = '$UnionWardCode'";
                }
                if (!empty($MauzaCode)) {
                    $qryCreate5 .= " AND Mouza_Code = '$MauzaCode'";
                }
                if (!empty($VillageCode)) {
                    $qryCreate5 .= " AND Village_Code = '$VillageCode'";
                }
                $qryCreate5 .= ")";
            }
        }


        if (strpos($loggedUserName, 'dist') !== false) {
            $distUserIdSelectCodition = " IN(SELECT UserID FROM assignsupervisor WHERE DistCoordinatorID = $loggedUserID AND UserID > 0) ";
            $distUserIdSelectCodition2 = " IN(SELECT UserID FROM assignsupervisor WHERE DistCoordinatorID = $loggedUserID  AND UserID > 0) ";

            $xFormsQuery = "SELECT COUNT(id) AS Number FROM xformrecord WHERE UserID NOT IN $testingUserIDs AND UserID $distUserIdSelectCodition AND CompanyId=? AND FormId = ?";
            $xFormsQuery .= $qryCreate;
            $result_LastExpence = $app->getDBConnection()->fetch($xFormsQuery, $loggedUserCompanyID, $FormID);
            $NumberOfRecord = $result_LastExpence->Number;

            $TotalDataQry = "SELECT count(id) as TotalHouseHold, 
								SUM(CASE WHEN IsApproved= 1 AND (IsChecked = 0 OR IsChecked IS NULL) THEN 1 ELSE 0 END) as ApprovedData, 
								SUM(CASE WHEN IsApproved= 1 AND IsChecked=1 THEN 1 ELSE 0 END) as CheckedData, 
								SUM(CASE WHEN IsApproved = 0 THEN 1 ELSE 0 END) as Pending, 
								SUM(CASE WHEN IsApproved = 2 THEN 1 ELSE 0 END) as UnApprovedData 
							FROM xformrecord 
							WHERE UserID NOT IN $testingUserIDs AND UserID $distUserIdSelectCodition 
									AND CompanyID = ?  
									AND FormId = ?";
            $TotalDataQry .= $qryCreate;
            $result_TotalDataQry = $app->getDBConnection()->fetch($TotalDataQry, $loggedUserCompanyID, $FormID);
            $TotalData = $result_TotalDataQry->TotalHouseHold;
            $ApprovedData = $result_TotalDataQry->ApprovedData;
            $CheckedData = $result_TotalDataQry->CheckedData;
            $UnApprovedData = $result_TotalDataQry->UnApprovedData;
            $PendingData = $result_TotalDataQry->Pending;

            if ($FormID == $formIdSamplingData) {
                $TotalTergetQry = "SELECT COUNT(id) as TotalTerget FROM InstituteInfo where UserID $distUserIdSelectCodition AND UserID <>'' and UserID>0  and Type = '$InstType'";
                $TotalTergetQry .= $qryCreate2;

                $TotalDataTodayQry = "SELECT COUNT(*) AS TotalData FROM xformrecord WHERE UserID $distUserIdSelectCodition AND FormId = ? AND CompanyId = ? AND (EntryDate BETWEEN '$todayDate 00:00:00' AND '$todayDate 23:59:59')";
                $TotalDataTodayQry .= $qryCreate;
                $result_TotalDataTodayQry = $app->getDBConnection()->fetch($TotalDataTodayQry, $formIdSamplingData, $loggedUserCompanyID);

                $TotalDataLast7DaysQry = "SELECT COUNT(*) AS TotalData FROM xformrecord WHERE UserID $distUserIdSelectCodition AND FormId = ? AND CompanyId = ? AND (EntryDate BETWEEN DATEADD(day, -7,'$todayDate 00:00:00') AND '$todayDate 23:59:59')";
                $TotalDataLast7DaysQry .= $qryCreate;
                $result_TotalDataLast7DaysQry = $app->getDBConnection()->fetch($TotalDataLast7DaysQry, $formIdSamplingData, $loggedUserCompanyID);

            } else if ($FormID == $formIdMainData) {
                $TotalTergetQry = "SELECT COUNT(id) as TotalTerget FROM InstituteInfo where UserID $distUserIdSelectCodition AND UserID <>'' and UserID>0  and Type = '$MunType'";
                $TotalTergetQry .= $qryCreate2;

                $TotalDataTodayQry = "SELECT COUNT(*) AS TotalData FROM xformrecord WHERE UserID $distUserIdSelectCodition AND FormId = ? AND CompanyId = ? AND (EntryDate BETWEEN '$todayDate 00:00:00' AND '$todayDate 23:59:59')";
                $TotalDataTodayQry .= $qryCreate;
                $result_TotalDataTodayQry = $app->getDBConnection()->fetch($TotalDataTodayQry, $formIdMainData, $loggedUserCompanyID);

                $TotalDataLast7DaysQry = "SELECT COUNT(*) AS TotalData FROM xformrecord WHERE UserID $distUserIdSelectCodition AND FormId = ? AND CompanyId = ? AND (EntryDate BETWEEN DATEADD(day, -7,'$todayDate 00:00:00') AND '$todayDate 23:59:59')";
                $TotalDataLast7DaysQry .= $qryCreate;
                $result_TotalDataLast7DaysQry = $app->getDBConnection()->fetch($TotalDataLast7DaysQry, $formIdMainData, $loggedUserCompanyID);
            }

            $TotalUserTodayQry = "SELECT count(distinct(UserID)) as TotalUser FROM UserLogStatus WHERE UserId in (SELECT id FROM userinfo where UserName like 'cd%' and IsActive=1)  and [DateTime] BETWEEN '$todayDate 00:00:00' AND '$todayDate 23:59:59'";
            $TotalUserTodayQry .= $qryCreate;
            $result_TotalUserTodayQry = $app->getDBConnection()->fetch($TotalUserTodayQry);
            $TotalUserToday = $result_TotalUserToday->TotalUser;

            $TotalDataToday = $result_TotalDataTodayQry->TotalData;
            $TotalDataLast7Days = $result_TotalDataLast7DaysQry->TotalData;

            $result_TotalTergetQry = $app->getDBConnection()->fetch($TotalTergetQry);
            $TotalTerget = $result_TotalTergetQry->TotalTerget;

            $DataCollectionPercentage = Ratio($NumberOfRecord, $TotalTerget);

            $TotalRejectQry = "SELECT COUNT(id) as TotalReject FROM deletedxformrecord where UserID $distUserIdSelectCodition AND CompanyID = ? AND FormId = ?";
            $TotalRejectQry .= $qryCreate;
            $result_TotalRejectQry = $app->getDBConnection()->fetch($TotalRejectQry, $loggedUserCompanyID, $FormID);
            $TotalReject = $result_TotalRejectQry->TotalReject;

            $TotalDataCollectorQry = "SELECT COUNT(id) AS TotalUser FROM userinfo WHERE id $distUserIdSelectCodition AND IsActive = ? AND CompanyID = ? AND UserName LIKE '%$dataCollectorNamePrefix%' ";
            $TotalDataCollectorQry .= $qryCreate3;
            $result_TotalDataCollectorQry = $app->getDBConnection()->fetch($TotalDataCollectorQry, 1, $loggedUserCompanyID);
            $TotalUser = $result_TotalDataCollectorQry->TotalUser;

            $TotalDataCollectorOnlineQry = "SELECT COUNT(id) AS TotalUser FROM userinfo WHERE id $distUserIdSelectCodition AND IsActive = ? AND IsOnline = ? AND CompanyID = ? AND UserName LIKE '%$dataCollectorNamePrefix%' ";
            $TotalDataCollectorOnlineQry .= $qryCreate3;
            $result_TotalDataCollectorOnlineQry = $app->getDBConnection()->fetch($TotalDataCollectorOnlineQry, 1, 1, $loggedUserCompanyID);
            $TotalUserOnline = $result_TotalDataCollectorOnlineQry->TotalUser;

            $TotalSupervisorQry = "SELECT COUNT( DISTINCT SupervisorID) Supervisor FROM assignsupervisor WHERE UserID $distUserIdSelectCodition2 AND CompanyID = ?";
            $TotalSupervisorQry .= $qryCreate;
            //$TotalSupervisorQry;
            $result_TotalSupervisorQry = $app->getDBConnection()->fetch($TotalSupervisorQry, $loggedUserCompanyID);
            $TotalSupervisor = $result_TotalSupervisorQry->Supervisor;

            $TotalSupervisorOnlineQry = "SELECT COUNT( DISTINCT assignsupervisor.SupervisorID) SupervisorOnline FROM assignsupervisor join userinfo on assignsupervisor.SupervisorID = userinfo.id WHERE UserID $distUserIdSelectCodition AND assignsupervisor.CompanyID = ? AND userinfo.IsOnline = ?";
            $TotalSupervisorOnlineQry .= $qryCreate4;
            $result_TotalSupervisorOnlineQry = $app->getDBConnection()->fetch($TotalSupervisorOnlineQry, $loggedUserCompanyID, 1);
            $TotalSupervisorOnline = $result_TotalSupervisorOnlineQry->SupervisorOnline;


            $TypeString = "";
            if ($FormID == $formIdMainData) {
                $TypeString = "Type='$MunType'";
            } elseif ($FormID == $formIdSamplingData) {
                $TypeString = "Type='$InstType'";
            }

            $LastSenderQuery = "SELECT TOP 10
                                xfr.UserID,
                                ui.UserName,
                                COUNT(*) AS Collected,
                                ISNULL(pl.SumTarget, 0) AS Target,
                                CAST(
                                    ROUND(
                                        100.0 * COUNT(*) / NULLIF(pl.SumTarget, 0),
                                        2
                                    ) AS DECIMAL(5,2)
                                ) AS Ratio
                            FROM xformrecord xfr
                            JOIN userinfo ui ON xfr.UserID = ui.id
                            LEFT JOIN (
                                SELECT 
                                    UserID,
                                    COUNT(id) AS SumTarget
                                FROM InstituteInfo WHERE $TypeString
                                GROUP BY UserID
                            ) pl ON xfr.UserID = pl.UserID
                            WHERE xfr.CompanyId = ?
                              AND xfr.UserID $distUserIdSelectCodition
                              AND xfr.FormId = ? $qryCreate5
                              AND ui.id NOT IN $testingUserIDs
                            GROUP BY 
                                xfr.UserID,
                                ui.UserName,
                                pl.SumTarget
                            ORDER BY 
                                Ratio ASC;";

            $result_LastSender = $app->getDBConnection()->fetchAll($LastSenderQuery, $loggedUserCompanyID, $FormID);

            $TopSenderQuery = "SELECT top 10 xfr.UserID, ui.UserName, COUNT(*) AS Number FROM xformrecord xfr JOIN userinfo ui ON xfr.UserID = ui.id WHERE xfr.UserID $distUserIdSelectCodition AND  xfr.CompanyId = ?  AND xfr.FormId = ? AND ui.id NOT IN $testingUserIDs";
            $TopSenderQuery .= $qryCreate;
            //$TopSenderQuery .= " GROUP BY xfr.UserID,ui.UserName ORDER BY Number DESC";
            $TopSenderQuery .= " GROUP BY xfr.UserID,ui.UserName";
            //$TopSenderQuery .= " HAVING COUNT(*)<49";
            $TopSenderQuery .= " ORDER BY Number DESC";
            $result_TopSender = $app->getDBConnection()->fetchAll($TopSenderQuery, $loggedUserCompanyID, $FormID);

            /*$LastSenderQuery = "SELECT top 10 xfr.UserID, ui.UserName, COUNT(*) AS Number FROM xformrecord xfr JOIN userinfo ui ON xfr.UserID = ui.id WHERE xfr.UserID $distUserIdSelectCodition AND  xfr.CompanyId = ?  AND xfr.FormId = ? AND ui.id NOT IN(68, 69)";
            $LastSenderQuery .= $qryCreate;
            $LastSenderQuery .= " GROUP BY xfr.UserID,ui.UserName ORDER BY Number ASC";
            $result_LastSender = $app->getDBConnection()->fetchAll($LastSenderQuery, $loggedUserCompanyID, $FormID);*/

            $DataSendingDateQuery = " Select CONVERT(date, EntryDate) as DataDate, count(*) as Number from xformrecord where UserID $distUserIdSelectCodition AND  CompanyId = ? AND FormId = ?";
            $DataSendingDateQuery .= $qryCreate;
            $DataSendingDateQuery .= " group by CONVERT(date, EntryDate) order by DataDate desc";
            $DataSendingDateRS = $app->getDBConnection()->fetchAll($DataSendingDateQuery, $loggedUserCompanyID, $FormID);

            $QueryDistLavel = "SELECT DISTINCT p.DistrictName, p.DistrictCode, (SELECT SUM(SQ.Target)
            FROM (SELECT DISTINCT PSU, NumberOfRecordForMainSurvey as Target FROM PSUList WHERE CompanyID = ? and DistrictCode = p.DistrictCode) SQ) as Target ,
            (SELECT COUNT(id) FROM xformrecord WHERE FormId = ? and xformrecord.UserID IN(SELECT PSUUserID FROM PSUList WHERE CompanyID = ?
            and DistrictCode = p.DistrictCode)) as Collected FROM PSUList as  p WHERE p.PSUUserID $distUserIdSelectCodition AND p.PSUUserID IS NOT NULL
            GROUP BY p.DistrictCode,p.DistrictName ORDER BY p.DistrictName asc;";
            $QueryDistLavelRS = $app->getDBConnection()->fetchAll($QueryDistLavel, $loggedUserCompanyID, $FormID, $loggedUserCompanyID);
        }elseif (strpos($loggedUserName, 'div') !== false) {
            $distUserIdSelectCodition = " IN(SELECT UserID FROM assignsupervisor WHERE DivCoordinatorID = $loggedUserID) ";
            $distUserIdSelectCodition2 = " IN(SELECT UserID FROM assignsupervisor WHERE DivCoordinatorID = $loggedUserID  AND UserID <> 0) ";

            $xFormsQuery = "SELECT COUNT(id) AS Number FROM xformrecord WHERE UserID NOT IN $testingUserIDs AND UserID $distUserIdSelectCodition AND CompanyId=? AND FormId = ?";
            $xFormsQuery .= $qryCreate;
            $result_LastExpence = $app->getDBConnection()->fetch($xFormsQuery, $loggedUserCompanyID, $FormID);
            $NumberOfRecord = $result_LastExpence->Number;

            $TotalDataQry = "SELECT count(id) as TotalHouseHold, 
								SUM(CASE WHEN IsApproved= 1 AND (IsChecked = 0 OR IsChecked IS NULL) THEN 1 ELSE 0 END) as ApprovedData, 
								SUM(CASE WHEN IsApproved= 1 AND IsChecked=1 THEN 1 ELSE 0 END) as CheckedData, 
								SUM(CASE WHEN IsApproved = 0 THEN 1 ELSE 0 END) as Pending, 
								SUM(CASE WHEN IsApproved = 2 THEN 1 ELSE 0 END) as UnApprovedData 
							FROM xformrecord 
							WHERE UserID NOT IN $testingUserIDs AND UserID $distUserIdSelectCodition 
									AND CompanyID = ?  
									AND FormId = ?";
            $TotalDataQry .= $qryCreate;
            $result_TotalDataQry = $app->getDBConnection()->fetch($TotalDataQry, $loggedUserCompanyID, $FormID);
            $TotalData = $result_TotalDataQry->TotalHouseHold;
            $ApprovedData = $result_TotalDataQry->ApprovedData;
            $CheckedData = $result_TotalDataQry->CheckedData;
            $UnApprovedData = $result_TotalDataQry->UnApprovedData;
            $PendingData = $result_TotalDataQry->Pending;

            if ($FormID == $formIdSamplingData) {
                $TotalTergetQry = "SELECT COUNT(id) as TotalTerget FROM InstituteInfo where UserID $distUserIdSelectCodition AND UserID <>'' and UserID>0  and Type = '$InstType'";
                $TotalTergetQry .= $qryCreate2;

                $TotalDataTodayQry = "SELECT COUNT(*) AS TotalData FROM xformrecord WHERE UserID $distUserIdSelectCodition AND FormId = ? AND CompanyId = ? AND (EntryDate BETWEEN '$todayDate 00:00:00' AND '$todayDate 23:59:59')";
                $TotalDataTodayQry .= $qryCreate;
                $result_TotalDataTodayQry = $app->getDBConnection()->fetch($TotalDataTodayQry, $formIdSamplingData, $loggedUserCompanyID);

                $TotalDataLast7DaysQry = "SELECT COUNT(*) AS TotalData FROM xformrecord WHERE UserID $distUserIdSelectCodition AND FormId = ? AND CompanyId = ? AND (EntryDate BETWEEN DATEADD(day, -7,'$todayDate 00:00:00') AND '$todayDate 23:59:59')";
                $TotalDataLast7DaysQry .= $qryCreate;
                $result_TotalDataLast7DaysQry = $app->getDBConnection()->fetch($TotalDataLast7DaysQry, $formIdSamplingData, $loggedUserCompanyID);

            } else if ($FormID == $formIdMainData) {
                $TotalTergetQry = "SELECT COUNT(id) as TotalTerget FROM InstituteInfo where UserID $distUserIdSelectCodition AND UserID <>'' and UserID>0  and Type = '$MunType'";
                $TotalTergetQry .= $qryCreate2;

                $TotalDataTodayQry = "SELECT COUNT(*) AS TotalData FROM xformrecord WHERE UserID $distUserIdSelectCodition AND FormId = ? AND CompanyId = ? AND (EntryDate BETWEEN '$todayDate 00:00:00' AND '$todayDate 23:59:59')";
                $TotalDataTodayQry .= $qryCreate;
                $result_TotalDataTodayQry = $app->getDBConnection()->fetch($TotalDataTodayQry, $formIdMainData, $loggedUserCompanyID);

                $TotalDataLast7DaysQry = "SELECT COUNT(*) AS TotalData FROM xformrecord WHERE UserID $distUserIdSelectCodition AND FormId = ? AND CompanyId = ? AND (EntryDate BETWEEN DATEADD(day, -7,'$todayDate 00:00:00') AND '$todayDate 23:59:59')";
                $TotalDataLast7DaysQry .= $qryCreate;
                $result_TotalDataLast7DaysQry = $app->getDBConnection()->fetch($TotalDataLast7DaysQry, $formIdMainData, $loggedUserCompanyID);
            }

            $TotalUserTodayQry = "SELECT count(distinct(UserID)) as TotalUser FROM UserLogStatus WHERE UserId in (SELECT id FROM userinfo where UserName like 'cd%' and IsActive=1)  and [DateTime] BETWEEN '$todayDate 00:00:00' AND '$todayDate 23:59:59'";
            $TotalUserTodayQry .= $qryCreate;
            $result_TotalUserTodayQry = $app->getDBConnection()->fetch($TotalUserTodayQry);
            $TotalUserToday = $result_TotalUserToday->TotalUser;

            $TotalDataToday = $result_TotalDataTodayQry->TotalData;
            $TotalDataLast7Days = $result_TotalDataLast7DaysQry->TotalData;

            $result_TotalTergetQry = $app->getDBConnection()->fetch($TotalTergetQry);
            $TotalTerget = $result_TotalTergetQry->TotalTerget;

            $DataCollectionPercentage = Ratio($NumberOfRecord, $TotalTerget);

            $TotalRejectQry = "SELECT COUNT(id) as TotalReject FROM deletedxformrecord where UserID $distUserIdSelectCodition AND CompanyID = ? AND FormId = ?";
            $TotalRejectQry .= $qryCreate;
            $result_TotalRejectQry = $app->getDBConnection()->fetch($TotalRejectQry, $loggedUserCompanyID, $FormID);
            $TotalReject = $result_TotalRejectQry->TotalReject;

            $TotalDataCollectorQry = "SELECT COUNT(id) AS TotalUser FROM userinfo WHERE id $distUserIdSelectCodition AND IsActive = ? AND CompanyID = ? AND UserName LIKE '%$dataCollectorNamePrefix%' ";
            $TotalDataCollectorQry .= $qryCreate3;
            $result_TotalDataCollectorQry = $app->getDBConnection()->fetch($TotalDataCollectorQry, 1, $loggedUserCompanyID);
            $TotalUser = $result_TotalDataCollectorQry->TotalUser;

            $TotalDataCollectorOnlineQry = "SELECT COUNT(id) AS TotalUser FROM userinfo WHERE id $distUserIdSelectCodition AND IsActive = ? AND IsOnline = ? AND CompanyID = ? AND UserName LIKE '%$dataCollectorNamePrefix%' ";
            $TotalDataCollectorOnlineQry .= $qryCreate3;
            $result_TotalDataCollectorOnlineQry = $app->getDBConnection()->fetch($TotalDataCollectorOnlineQry, 1, 1, $loggedUserCompanyID);
            $TotalUserOnline = $result_TotalDataCollectorOnlineQry->TotalUser;

            $TotalSupervisorQry = "SELECT COUNT( DISTINCT SupervisorID) Supervisor FROM assignsupervisor WHERE UserID $distUserIdSelectCodition2 AND CompanyID = ?";
            $TotalSupervisorQry .= $qryCreate;
            //$TotalSupervisorQry;
            $result_TotalSupervisorQry = $app->getDBConnection()->fetch($TotalSupervisorQry, $loggedUserCompanyID);
            $TotalSupervisor = $result_TotalSupervisorQry->Supervisor;

            $TotalSupervisorOnlineQry = "SELECT COUNT( DISTINCT assignsupervisor.SupervisorID) SupervisorOnline FROM assignsupervisor join userinfo on assignsupervisor.SupervisorID = userinfo.id WHERE UserID $distUserIdSelectCodition AND assignsupervisor.CompanyID = ? AND userinfo.IsOnline = ?";
            $TotalSupervisorOnlineQry .= $qryCreate4;
            $result_TotalSupervisorOnlineQry = $app->getDBConnection()->fetch($TotalSupervisorOnlineQry, $loggedUserCompanyID, 1);
            $TotalSupervisorOnline = $result_TotalSupervisorOnlineQry->SupervisorOnline;


            $TypeString = "";
            if ($FormID == $formIdMainData) {
                $TypeString = "Type='$MunType'";
            } elseif ($FormID == $formIdSamplingData) {
                $TypeString = "Type='$InstType'";
            }

            $LastSenderQuery = "SELECT TOP 10
                                xfr.UserID,
                                ui.UserName,
                                COUNT(*) AS Collected,
                                ISNULL(pl.SumTarget, 0) AS Target,
                                CAST(
                                    ROUND(
                                        100.0 * COUNT(*) / NULLIF(pl.SumTarget, 0),
                                        2
                                    ) AS DECIMAL(5,2)
                                ) AS Ratio
                            FROM xformrecord xfr
                            JOIN userinfo ui ON xfr.UserID = ui.id
                            LEFT JOIN (
                                SELECT 
                                    UserID,
                                    COUNT(id) AS SumTarget
                                FROM InstituteInfo WHERE $TypeString
                                GROUP BY UserID
                            ) pl ON xfr.UserID = pl.UserID
                            WHERE xfr.CompanyId = ?
                              AND xfr.UserID $distUserIdSelectCodition
                              AND xfr.FormId = ? $qryCreate5
                              AND ui.id NOT IN $testingUserIDs
                            GROUP BY 
                                xfr.UserID,
                                ui.UserName,
                                pl.SumTarget
                            ORDER BY 
                                Ratio ASC;";

            $result_LastSender = $app->getDBConnection()->fetchAll($LastSenderQuery, $loggedUserCompanyID, $FormID);

            $TopSenderQuery = "SELECT top 10 xfr.UserID, ui.UserName, COUNT(*) AS Number FROM xformrecord xfr JOIN userinfo ui ON xfr.UserID = ui.id WHERE xfr.UserID $distUserIdSelectCodition AND  xfr.CompanyId = ?  AND xfr.FormId = ? AND ui.id NOT IN $testingUserIDs";
            $TopSenderQuery .= $qryCreate;
            //$TopSenderQuery .= " GROUP BY xfr.UserID,ui.UserName ORDER BY Number DESC";
            $TopSenderQuery .= " GROUP BY xfr.UserID,ui.UserName";
            //$TopSenderQuery .= " HAVING COUNT(*)<49";
            $TopSenderQuery .= " ORDER BY Number DESC";
            $result_TopSender = $app->getDBConnection()->fetchAll($TopSenderQuery, $loggedUserCompanyID, $FormID);

            /*$LastSenderQuery = "SELECT top 10 xfr.UserID, ui.UserName, COUNT(*) AS Number FROM xformrecord xfr JOIN userinfo ui ON xfr.UserID = ui.id WHERE xfr.UserID $distUserIdSelectCodition AND  xfr.CompanyId = ?  AND xfr.FormId = ? AND ui.id NOT IN(68, 69)";
            $LastSenderQuery .= $qryCreate;
            $LastSenderQuery .= " GROUP BY xfr.UserID,ui.UserName ORDER BY Number ASC";
            $result_LastSender = $app->getDBConnection()->fetchAll($LastSenderQuery, $loggedUserCompanyID, $FormID);*/

            $DataSendingDateQuery = " Select CONVERT(date, EntryDate) as DataDate, count(*) as Number from xformrecord where UserID $distUserIdSelectCodition AND  CompanyId = ? AND FormId = ?";
            $DataSendingDateQuery .= $qryCreate;
            $DataSendingDateQuery .= " group by CONVERT(date, EntryDate) order by DataDate desc";
            $DataSendingDateRS = $app->getDBConnection()->fetchAll($DataSendingDateQuery, $loggedUserCompanyID, $FormID);

            $QueryDistLavel = "SELECT DISTINCT p.DistrictName, p.DistrictCode, (SELECT SUM(SQ.Target)
            FROM (SELECT DISTINCT PSU, NumberOfRecordForMainSurvey as Target FROM PSUList WHERE CompanyID = ? and DistrictCode = p.DistrictCode) SQ) as Target ,
            (SELECT COUNT(id) FROM xformrecord WHERE FormId = ? and xformrecord.UserID IN(SELECT PSUUserID FROM PSUList WHERE CompanyID = ?
            and DistrictCode = p.DistrictCode)) as Collected FROM PSUList as  p WHERE p.PSUUserID $distUserIdSelectCodition AND p.PSUUserID IS NOT NULL
            GROUP BY p.DistrictCode,p.DistrictName ORDER BY p.DistrictName asc;";
            $QueryDistLavelRS = $app->getDBConnection()->fetchAll($QueryDistLavel, $loggedUserCompanyID, $FormID, $loggedUserCompanyID);
        } else {
            $xFormsQuery = "SELECT COUNT(id) AS Number FROM xformrecord WHERE CompanyId=? AND UserID NOT IN $testingUserIDs AND FormId = ?";
            $xFormsQuery .= $qryCreate;
            $result_LastExpence = $app->getDBConnection()->fetch($xFormsQuery, $loggedUserCompanyID, $FormID);
            $NumberOfRecord = $result_LastExpence->Number;

            $TotalDataQry = "SELECT count(id) as TotalHouseHold, 
								SUM(CASE WHEN IsApproved= 1 AND (IsChecked = 0 OR IsChecked IS NULL) THEN 1 ELSE 0 END) as ApprovedData, 
								SUM(CASE WHEN IsApproved= 1 AND IsChecked=1 THEN 1 ELSE 0 END) as CheckedData, 
								SUM(CASE WHEN IsApproved = 0 THEN 1 ELSE 0 END) as Pending, 
								SUM(CASE WHEN IsApproved = 2 THEN 1 ELSE 0 END) as UnApprovedData 
							FROM xformrecord 
							WHERE UserID NOT IN $testingUserIDs AND CompanyID = ?  
								AND FormId = ?";
            $TotalDataQry .= $qryCreate;
            $result_TotalDataQry = $app->getDBConnection()->fetch($TotalDataQry, $loggedUserCompanyID, $FormID);
            $TotalData = $result_TotalDataQry->TotalHouseHold;
            $ApprovedData = $result_TotalDataQry->ApprovedData;
            $CheckedData = $result_TotalDataQry->CheckedData;
            $UnApprovedData = $result_TotalDataQry->UnApprovedData;
            $PendingData = $result_TotalDataQry->Pending;

            if ($FormID == $formIdSamplingData) {
                $TotalTergetQry = "SELECT COUNT(ID) as TotalTerget FROM InstituteInfo where UserID NOT IN $testingUserIDs AND UserID <>'' and UserID>0 and Type = '$InstType'";
                $TotalTergetQry .= $qryCreate2;
                //echo $TotalTergetQry;

                $TotalDataTodayQry = "SELECT COUNT(*) AS TotalData FROM xformrecord WHERE UserID NOT IN $testingUserIDs AND FormId = ? AND CompanyId = ? AND (EntryDate BETWEEN '$todayDate 00:00:00' AND '$todayDate 23:59:59')";
                $TotalDataTodayQry .= $qryCreate;
                $result_TotalDataTodayQry = $app->getDBConnection()->fetch($TotalDataTodayQry, $formIdSamplingData, $loggedUserCompanyID);

                $TotalDataLast7DaysQry = "SELECT COUNT(*) AS TotalData FROM xformrecord WHERE UserID NOT IN $testingUserIDs AND FormId = ? AND CompanyId = ? AND (EntryDate BETWEEN DATEADD(day, -7,'$todayDate 00:00:00') AND '$todayDate 23:59:59')";
                $TotalDataLast7DaysQry .= $qryCreate;
                $result_TotalDataLast7DaysQry = $app->getDBConnection()->fetch($TotalDataLast7DaysQry, $formIdSamplingData, $loggedUserCompanyID);

            } else if ($FormID == $formIdMainData) {
                $TotalTergetQry = "SELECT COUNT(ID) as TotalTerget FROM InstituteInfo where UserID NOT IN $testingUserIDs AND UserID <>'' and UserID>0 and Type = '$MunType'";
                $TotalTergetQry .= $qryCreate2;

                $TotalDataTodayQry = "SELECT COUNT(*) AS TotalData FROM xformrecord WHERE UserID NOT IN $testingUserIDs AND FormId = ? AND CompanyId = ? AND (EntryDate BETWEEN '$todayDate 00:00:00' AND '$todayDate 23:59:59')";
                $TotalDataTodayQry .= $qryCreate;
                $result_TotalDataTodayQry = $app->getDBConnection()->fetch($TotalDataTodayQry, $formIdMainData, $loggedUserCompanyID);

                $TotalDataLast7DaysQry = "SELECT COUNT(*) AS TotalData FROM xformrecord WHERE UserID NOT IN $testingUserIDs AND FormId = ? AND CompanyId = ? AND (EntryDate BETWEEN DATEADD(day, -7,'$todayDate 00:00:00') AND '$todayDate 23:59:59')";
                $TotalDataLast7DaysQry .= $qryCreate;
                $result_TotalDataLast7DaysQry = $app->getDBConnection()->fetch($TotalDataLast7DaysQry, $formIdMainData, $loggedUserCompanyID);
            }

            $TotalUserTodayQry = "SELECT count(distinct(UserID)) as TotalUser FROM UserLogStatus WHERE UserId in (SELECT id FROM userinfo where UserName like 'cd%' and IsActive=1)  and [DateTime] BETWEEN '$todayDate 00:00:00' AND '$todayDate 23:59:59'";
            $TotalUserTodayQry .= $qryCreate;
            $result_TotalUserTodayQry = $app->getDBConnection()->fetch($TotalUserTodayQry);
            $TotalUserToday = $result_TotalUserTodayQry->TotalUser;


            $TotalDataToday = $result_TotalDataTodayQry->TotalData;
            $TotalDataLast7Days = $result_TotalDataLast7DaysQry->TotalData;

            $result_TotalTergetQry = $app->getDBConnection()->fetch($TotalTergetQry);
            $TotalTerget = $result_TotalTergetQry->TotalTerget;

            $DataCollectionPercentage = Ratio($NumberOfRecord, $TotalTerget);

            $TotalRejectQry = "SELECT COUNT(id) as TotalReject FROM deletedxformrecord where CompanyID = ? AND FormId = ?";
            $TotalRejectQry .= $qryCreate;
            $result_TotalRejectQry = $app->getDBConnection()->fetch($TotalRejectQry, $loggedUserCompanyID, $FormID);
            $TotalReject = $result_TotalRejectQry->TotalReject;

            //$TotalDataCollectorQry = "SELECT COUNT(id) AS TotalUser FROM userinfo WHERE IsActive = ? AND CompanyID = ? AND UserName LIKE '%$dataCollectorNamePrefix%' ";
            $TotalDataCollectorQry = "SELECT COUNT(Distinct(UserID)) AS TotalUser FROM InstituteInfo WHERE UserID NOT IN $testingUserIDs ";
            $TotalDataCollectorQry .= $qryCreate3;
            $result_TotalDataCollectorQry = $app->getDBConnection()->fetch($TotalDataCollectorQry);
            $TotalUser = $result_TotalDataCollectorQry->TotalUser;

            $TotalDataCollectorOnlineQry = "SELECT COUNT(id) AS TotalUser FROM userinfo WHERE IsActive = ? AND IsOnline = ? AND CompanyID = ? AND UserName LIKE '%$dataCollectorNamePrefix%' ";
            $TotalDataCollectorOnlineQry .= $qryCreate3;
            $result_TotalDataCollectorOnlineQry = $app->getDBConnection()->fetch($TotalDataCollectorOnlineQry, 1, 1, $loggedUserCompanyID);
            $TotalUserOnline = $result_TotalDataCollectorOnlineQry->TotalUser;

            //$TotalSupervisorQry = "SELECT COUNT( DISTINCT SupervisorID) Supervisor FROM assignsupervisor WHERE CompanyID = ?";
            $TotalSupervisorQry = "SELECT COUNT(DISTINCT(SupervisorID)) 'Supervisor' FROM assignsupervisor WHERE UserID IN (SELECT DISTINCT(UserID) FROM InstituteInfo WHERE UserID NOT IN $testingUserIDs)";

            $TotalSupervisorQry .= $qryCreate;
            $result_TotalSupervisorQry = $app->getDBConnection()->fetch($TotalSupervisorQry);
            $TotalSupervisor = $result_TotalSupervisorQry->Supervisor;

            $TotalSupervisorOnlineQry = "SELECT COUNT( DISTINCT assignsupervisor.SupervisorID) SupervisorOnline FROM assignsupervisor join userinfo on assignsupervisor.SupervisorID = userinfo.id WHERE assignsupervisor.CompanyID = ? AND userinfo.IsOnline = ?";
            $TotalSupervisorOnlineQry .= $qryCreate4;
            $result_TotalSupervisorOnlineQry = $app->getDBConnection()->fetch($TotalSupervisorOnlineQry, $loggedUserCompanyID, 1);
            $TotalSupervisorOnline = $result_TotalSupervisorOnlineQry->SupervisorOnline;

            $TopSenderQuery = "SELECT top 10 xfr.UserID, ui.UserName, COUNT(*) AS Number FROM xformrecord xfr JOIN userinfo ui ON xfr.UserID = ui.id WHERE xfr.CompanyId = ?  AND xfr.FormId = ? AND ui.id NOT IN $testingUserIDs";
            $TopSenderQuery .= $qryCreate;
            $TopSenderQuery .= " GROUP BY xfr.UserID,ui.UserName";
            //$TopSenderQuery .= " HAVING COUNT(*)<49";
            $TopSenderQuery .= " ORDER BY Number DESC";
            //echo $TopSenderQuery;
            //echo $FormID;
            $result_TopSender = $app->getDBConnection()->fetchAll($TopSenderQuery, $loggedUserCompanyID, $FormID);

            /*$LastSenderQuery = "SELECT top 7 xfr.UserID, ui.UserName, COUNT(*) AS Number FROM xformrecord xfr JOIN userinfo ui ON xfr.UserID = ui.id WHERE xfr.CompanyId = ?  AND xfr.FormId = ?  AND ui.id NOT IN(68, 69)";
            $LastSenderQuery .= $qryCreate;
            $LastSenderQuery .= " GROUP BY xfr.UserID,ui.UserName ORDER BY Number ASC";
            $result_LastSender = $app->getDBConnection()->fetchAll($LastSenderQuery, $loggedUserCompanyID, $FormID);
            */

            $TypeString = "";
            if ($FormID == $formIdMainData) {
                $TypeString = "Type='$MunType'";
            } elseif ($FormID == $formIdSamplingData) {
                $TypeString = "Type='$InstType'";
            }



            $LastSenderQuery = "SELECT TOP 10
                                xfr.UserID,
                                ui.UserName,
                                COUNT(*) AS Collected,
                                ISNULL(pl.SumTarget, 0) AS Target,
                                CAST(
                                    ROUND(
                                        100.0 * COUNT(*) / NULLIF(pl.SumTarget, 0),
                                        2
                                    ) AS DECIMAL(5,2)
                                ) AS Ratio
                            FROM xformrecord xfr
                            JOIN userinfo ui ON xfr.UserID = ui.id
                            LEFT JOIN (
                                SELECT 
                                    UserID,
                                    COUNT(id) AS SumTarget
                                FROM InstituteInfo WHERE $TypeString
                                GROUP BY UserID
                            ) pl ON xfr.UserID = pl.UserID
                            WHERE xfr.CompanyId = ?
                              AND xfr.FormId = ?
                              AND ui.id NOT IN $testingUserIDs
                            $qryCreate5
                            GROUP BY 
                                xfr.UserID,
                                ui.UserName,
                                pl.SumTarget
                            ORDER BY 
                                Ratio ASC;";


            $result_LastSender = $app->getDBConnection()->fetchAll($LastSenderQuery, $loggedUserCompanyID, $FormID);

            $DataSendingDateQuery = " Select CONVERT(date, EntryDate) as DataDate, count(*) as Number from xformrecord where CompanyId = ? AND FormId = ?";
            $DataSendingDateQuery .= $qryCreate;
            $DataSendingDateQuery .= " group by CONVERT(date, EntryDate) order by DataDate desc";
            $DataSendingDateRS = $app->getDBConnection()->fetchAll($DataSendingDateQuery, $loggedUserCompanyID, $FormID);

            $QueryDistLavel = "SELECT DISTINCT p.DistrictName, p.DistrictCode, (SELECT SUM(SQ.Target)
            FROM (SELECT DISTINCT PSU, NumberOfRecordForMainSurvey as Target FROM PSUList WHERE CompanyID = ? and DistrictCode = p.DistrictCode) SQ) as Target ,
            (SELECT COUNT(id) FROM xformrecord WHERE FormId = ? and xformrecord.UserID IN(SELECT PSUUserID FROM PSUList WHERE CompanyID = ?
            and DistrictCode = p.DistrictCode)) as Collected FROM PSUList as  p WHERE p.PSUUserID IS NOT NULL
            GROUP BY p.DistrictCode,p.DistrictName ORDER BY p.DistrictName asc;";
            $QueryDistLavelRS = $app->getDBConnection()->fetchAll($QueryDistLavel, $loggedUserCompanyID, $FormID, $loggedUserCompanyID);
        }
        ?>

        <?php
        if (!empty($DivisionCode)) {
            ?>
            <br>
            <section class="card">
                <div class="card-header">
                    <div class="card-subtitle"><?php echo $DivisionName . $DistrictName . $UpazilaName . $UnionWardName . $MauzaName . $VillageName; ?></div>
                </div>
            </section>
            <br>

            <?php
        }
        ?>

        <div class="row">
            <div class="col-lg-6 mb-3">
                <section class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 text-center">
                                <div class="liquid-meter-wrapper liquid-meter-sm mt-3">
                                    <div class="liquid-meter">
                                        <meter min="0" max="100" value="<?php echo $DataCollectionPercentage; ?>"
                                               id="meterSales"></meter>
                                    </div>
                                </div>
                                <h2 class="card-title mt-3">Target : <?php echo $TotalTerget; ?></h2>
                            </div>
                            <div class="col-xl-8 text-center">
                                <div class="chart-data-selector" id="salesSelectorWrapper">
                                    <div id="salesSelectorItems" class="chart-data-selector-items mt-3">
                                        <div class="summary">
                                            <h4 class="title text-success">Data Collected
                                                : <?php echo $NumberOfRecord; ?></h4>
                                            <h5 class="text-danger">Not Collected
                                                : <?php echo $TotalTerget - $NumberOfRecord; ?></h5><br/>
                                            <div class="title">
                                                <i class="fas fa-caret-right"></i> Approved : <strong
                                                        class="amount"><?php echo $ApprovedData; ?></strong><br/>
                                                <i class="fas fa-caret-right"></i> Checked : <strong
                                                        class="amount"><?php echo $CheckedData; ?></strong><br/>
                                                <i class="fas fa-caret-right"></i> Pending : <strong
                                                        class="amount"><?php echo $PendingData; ?></strong><br/>
                                                <i class="fas fa-caret-right"></i> Unapproved : <strong
                                                        class="amount"><?php echo $UnApprovedData; ?></strong><!--<br/>
                                                <i class="fas fa-caret-right"></i> Deleted : <strong
                                                        class="amount"><?php /*echo $TotalReject; */ ?></strong>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <div class="col-lg-6">
                <div class="row mb-3">
                    <div class="col-xl-6">
                        <section class="card card-featured-left card-featured-primary mb-3">
                            <div class="card-body">
                                <div class="widget-summary">
                                    <div class="widget-summary-col widget-summary-col-icon">
                                        <div class="summary-icon bg-primary">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    </div>
                                    <div class="widget-summary-col">
                                        <div class="summary">
                                            <h4 class="title">Total Data Collector</h4>
                                            <div class="info">
                                                <strong class="amount"><?php echo $TotalUser; ?></strong>
                                                <!--<span class="text-primary">(<?php /*echo $TotalUserOnline; */ ?> online)</span>-->
                                            </div>
                                        </div>
                                        <div class="summary-footer">
                                            <a class="text-muted text-uppercase"
                                               href="<?php echo $baseURL ?>index.php?parent=ShowUserInfo">(View
                                                All)</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="col-xl-6">
                        <section class="card card-featured-left card-featured-secondary">
                            <div class="card-body">
                                <div class="widget-summary">
                                    <div class="widget-summary-col widget-summary-col-icon">
                                        <div class="summary-icon bg-secondary">
                                            <i class="fas fa-user-alt"></i>
                                        </div>
                                    </div>
                                    <div class="widget-summary-col">
                                        <div class="summary">
                                            <h4 class="title">Total Supervisor</h4>
                                            <div class="info">
                                                <strong class="amount"><?php echo $TotalSupervisor; ?></strong>
                                                <!--<span class="text-primary">(<?php /*echo $TotalSupervisorOnline; */ ?> online)</span>-->
                                            </div>
                                        </div>
                                        <div class="summary-footer">
                                            <a class="text-muted text-uppercase"
                                               href="<?php echo $baseURL ?>index.php?parent=ViewSupervisor">(View
                                                All)</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6">
                        <section class="card card-featured-left card-featured-tertiary mb-3">
                            <div class="card-body">
                                <div class="widget-summary">
                                    <div class="widget-summary-col widget-summary-col-icon">
                                        <div class="summary-icon bg-tertiary">
                                            <i class="fas fa-calendar-day"></i>
                                        </div>
                                    </div>
                                    <div class="widget-summary-col">
                                        <div class="summary">
                                            <h4 class="title">Today's Collected Data</h4>
                                            <div class="info">
                                                <strong class="amount"><?php echo $TotalDataToday; ?></strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="col-xl-6">
                        <section class="card card-featured-left card-featured-quaternary">
                            <div class="card-body">
                                <div class="widget-summary">
                                    <div class="widget-summary-col widget-summary-col-icon">
                                        <div class="summary-icon bg-success">
                                            <i class="fas fa-calendar-days"></i>
                                        </div>
                                    </div>
                                    <div class="widget-summary-col">
                                        <div class="summary">
                                            <h4 class="title">Enumerator Login Today</h4>
                                            <div class="success">
                                                <strong class="amount"><?php echo $TotalUserToday; ?></strong>
                                            </div>
                                        </div>
                                        <!--<div class="summary">
                                            <h4 class="title">Last 7 Day's Data</h4>
                                            <div class="info">
                                                <strong class="amount"><?php /*echo $TotalDataLast7Days; */ ?></strong>
                                            </div>
                                        </div>-->
                                        <!--<div class="summary-footer">
                                            <a class="text-muted text-uppercase" href="#">(view growth)</a>
                                        </div>-->
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>

        <div class="row pt-4 mt-1">
            <div class="col-xl-6">
                <section class="card">
                    <header class="card-header">
                        <h2 class="card-title">Highest 10 Data Senders</h2>
                    </header>
                    <div class="card-body">
                        <table class="table table-responsive-md table-striped mb-0">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Target</th>
                                <th>Sent</th>
                                <th>Ratio</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $il = 1;
                            foreach ($result_TopSender as $row) {
                                $UserIDVal = $row->UserID;
                                $UserName = $row->UserName;
                                $UserFullName = getName('FullName', 'userinfo', $UserIDVal);
                                $NumberOfTopData = $row->Number;

                                if ($FormID == $formIdMainData) {
                                    $NumberOfTargetData = getValue("InstituteInfo", "COUNT(id)", "UserID = $UserIDVal AND Type = '$MunType'");
                                } elseif ($FormID == $formIdSamplingData) {
                                    $NumberOfTargetData = getValue("InstituteInfo", "COUNT(id)", "UserID = $UserIDVal AND Type = '$InstType'");
                                }

                                $UserDataCollectionRatio = Ratio($NumberOfTopData, $NumberOfTargetData);
                                ?>
                                <tr>
                                    <td><?php echo $il; ?></td>
                                    <td><?php echo $UserFullName . ' (<b>' . $UserName . '</b>)'; ?></td>
                                    <td><span class="badge badge-primary"><?php echo $NumberOfTargetData; ?></span>
                                    </td>
                                    <td><span class="badge badge-primary"><?php echo $NumberOfTopData; ?></span>
                                    </td>
                                    <td><span class="badge badge-primary"><?php echo $UserDataCollectionRatio; ?></span>
                                    </td>
                                </tr>
                                <?php
                                $il++;
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
            <div class="col-xl-6">
                <section class="card">
                    <header class="card-header">
                        <h2 class="card-title">Lowest 10 Data Senders</h2>
                    </header>
                    <div class="card-body">
                        <table class="table table-responsive-md table-striped mb-0">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Target</th>
                                <th>Sent</th>
                                <th>Ratio</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $il = 1;
                            foreach ($result_LastSender as $row) {
                                /*$UserIDVal = $row->UserID;
                                $UserName = $row->UserName;
                                $UserFullName = getName('FullName', 'userinfo', $UserIDVal);
                                $NumberOfTopData = $row->Number;
                                if ($FormID == $formIdMainData) {
                                    $NumberOfTargetData = getValue("PSUList", "SUM(NumberOfRecordForMainSurvey)", "FarmName='' AND PSUUserID = $UserIDVal");
                                } elseif ($FormID == $formIdSamplingData) {
                                    $NumberOfTargetData = getValue("PSUList", "SUM(NumberOfRecord)", "FarmName='' AND PSUUserID = $UserIDVal");
                                } elseif ($FormID == $formIdFarmData) {
                                    $NumberOfTargetData = getValue("PSUList", "SUM(NumberOfRecord)", "FarmName<>'' AND PSUUserID = $UserIDVal");
                                }

                                $UserDataCollectionRatio = Ratio($NumberOfTopData, $NumberOfTargetData);*/

                                $UserIDVal = $row->UserID;
                                $UserName = $row->UserName;
                                $UserFullName = getName('FullName', 'userinfo', $UserIDVal);
                                $UserCollectedData = $row->Collected;
                                $UserTargetData = $row->Target;
                                $UserRatioData = $row->Ratio;
                                ?>
                                <tr>
                                    <td><?php echo $il; ?></td>
                                    <td><?php echo $UserFullName . ' (<b>' . $UserName . '</b>)'; ?></td>
                                    <td><span class="badge badge-primary"><?php echo $UserTargetData; ?></span>
                                    </td>
                                    <td><span class="badge badge-primary"><?php echo $UserCollectedData; ?></span>
                                    </td>
                                    <td><span class="badge badge-primary"><?php echo $UserRatioData.'%'; ?></span>
                                    </td>
                                </tr>
                                <?php
                                $il++;
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <section class="card">
                    <header class="card-header">
                        <div class="card-actions">
                            <a href="#" class="card-action card-action-toggle" data-card-toggle></a>
                        </div>
                        <h2 class="card-title">Day-wise Data Sending Report</h2>
                    </header>
                    <div class="card-body">
                        <!-- Flot: Bars -->
                        <div class="chart chart-lg" id="flotBars"></div>
                        <script type="text/javascript">
                            var flotBarsData =
                                [
                                    <?php
                                    foreach ($DataSendingDateRS as $row) {
                                    ?>
                                    ['<?php echo date_format($row->DataDate, "d/m"); ?>', <?php echo $row->Number; ?>],
                                    <?php
                                    }
                                    ?>
                                ];
                        </script>
                    </div>
                </section>
            </div>
        </div>

        <!--<div class="row">
            <div class="col-lg-12">
                <section class="card">
                    <header class="card-header">
                        <div class="card-actions">
                            <a href="#" class="card-action card-action-toggle" data-card-toggle></a>
                        </div>
                        <h2 class="card-title">Data Map</h2>
                    </header>
                    <div class="card-body">
                        <div id="gmap-basic" style="height: 500px; width: 100%;"></div>
                    </div>
                </section>
            </div>
        </div>-->
        <!-- end: page -->
    </section>
</div>

<script>
    $(document).ready(function() {
        // Initial population on page load
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