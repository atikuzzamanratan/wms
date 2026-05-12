<?php
$FormID = 2;

$cn = ConnectDB();

$qryFormName = "SELECT id, FormName FROM datacollectionform WHERE CompanyID = ? AND Status = '$formActiveStatus' ORDER BY id ASC";
$rsQryFormName = $app->getDBConnection()->fetchAll($qryFormName, $loggedUserCompanyID);

$qrySupervisor = "SELECT id FROM assignsupervisor WHERE SupervisorID = ?";
$rsSupervisor = $app->getDBConnection()->fetch($qrySupervisor, $loggedUserID);
$SuperID = $rsSupervisor->id;

if (strpos($loggedUserName, 'dist') !== false) {
    $divQuery = "SELECT DISTINCT p.Division_Name, p.Division_Code FROM InstituteInfo AS p 
    JOIN assignsupervisor AS a ON p.UserID = a.UserID 
    WHERE a.DistCoordinatorID = $loggedUserID";
} else {
    $divQuery = "SELECT DISTINCT Division_Name, Division_Code FROM InstituteInfo ORDER BY Division_Name ASC";
}
$rsDivQuery = $app->getDBConnection()->fetchAll($divQuery);

$boxFontColor = "black";

if ($_REQUEST['show'] === 'Show') {
    $FormID = xss_clean($_REQUEST['FormID']);
}
?>

<div class="inner-wrapper">
    <section role="main" class="content-body">
        <header class="page-header">
            <h2><?php echo $MenuLebel; ?></h2>

            <?php include_once 'Components/header-home-button.php'; ?>
        </header>

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
                                            id="FormID" title="Please select a form" required>
                                        <optgroup label="Select form">
                                            <?PHP
                                            foreach ($rsQryFormName as $row) {
                                                echo '<option value="' . $row->id . '"' . (isset($FormID) && !empty($FormID) && $FormID == $row->id ? 'selected' : '') . '>' . $row->FormName . '</option>';
                                            }
                                            ?>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>

                            <footer class="card-footer">
                                <div class="row justify-content-end">
                                    <div class="col-lg-9">
                                        <input class="btn btn-primary" name="show" type="submit" id="show"
                                               value="Show">
                                    </div>
                                </div>
                            </footer>
                        </form>
                    </div>
                </section>
            </div>
        </div>
        <br>

        <?php

        $FormName = getValue('datacollectionform', 'FormName', "id = $FormID");

        //die("Form Name: ".$FormName);

        $DHKDivCode = " Division_Code = 30";
        $CTGDivCode = " Division_Code = 20";
        $RAJDivCode = " Division_Code = 50";
        $KHLDivCode = " Division_Code = 40";
        $BARDivCode = " Division_Code = 10";
        $SYLDivCode = " Division_Code = 60";
        $RANDivCode = " Division_Code = 55";
        $MYMDivCode = " Division_Code = 45";

        $DHKUserCond = " IN(SELECT id FROM InstituteInfo WHERE Division_Code = 30)";
        $CTGUserCond = " IN(SELECT id FROM InstituteInfo WHERE Division_Code = 20)";
        $RAJUserCond = " IN(SELECT id FROM InstituteInfo WHERE Division_Code = 50)";
        $KHLUserCond = " IN(SELECT id FROM InstituteInfo WHERE Division_Code = 40)";
        $BARUserCond = " IN(SELECT id FROM InstituteInfo WHERE Division_Code = 10)";
        $SYLUserCond = " IN(SELECT id FROM InstituteInfo WHERE Division_Code = 60)";
        $RANUserCond = " IN(SELECT id FROM InstituteInfo WHERE Division_Code = 55)";
        $MYMUserCond = " IN(SELECT id FROM InstituteInfo WHERE Division_Code = 45)";

        $TotalQueryDHK = "SELECT COUNT(id) AS Number FROM xformrecord WHERE CompanyId = ? AND FormId = ? AND SampleHHNo $DHKUserCond";
        $RsTotalQueryDHK = $app->getDBConnection()->fetch($TotalQueryDHK, $loggedUserCompanyID, $FormID);
        $NumberOfRecordDHK = $RsTotalQueryDHK->Number;

        $TotalQueryCTG = "SELECT COUNT(id) AS Number FROM xformrecord WHERE CompanyId = ? AND FormId = ? AND SampleHHNo $CTGUserCond";
        $RsTotalQueryCTG = $app->getDBConnection()->fetch($TotalQueryCTG, $loggedUserCompanyID, $FormID);
        $NumberOfRecordCTG = $RsTotalQueryCTG->Number;

        $TotalQueryRAJ = "SELECT COUNT(id) AS Number FROM xformrecord WHERE CompanyId = ? AND FormId = ? AND SampleHHNo $RAJUserCond";
        $RsTotalQueryRAJ = $app->getDBConnection()->fetch($TotalQueryRAJ, $loggedUserCompanyID, $FormID);
        $NumberOfRecordRAJ = $RsTotalQueryRAJ->Number;

        $TotalQueryKHL = "SELECT COUNT(id) AS Number FROM xformrecord WHERE CompanyId = ? AND FormId = ? AND SampleHHNo $KHLUserCond";
        $RsTotalQueryKHL = $app->getDBConnection()->fetch($TotalQueryKHL, $loggedUserCompanyID, $FormID);
        $NumberOfRecordKHL = $RsTotalQueryKHL->Number;

        $TotalQueryBAR = "SELECT COUNT(id) AS Number FROM xformrecord WHERE CompanyId = ? AND FormId = ? AND SampleHHNo $BARUserCond";
        $RsTotalQueryBAR = $app->getDBConnection()->fetch($TotalQueryBAR, $loggedUserCompanyID, $FormID);
        $NumberOfRecordBAR = $RsTotalQueryBAR->Number;

        $TotalQuerySYL = "SELECT COUNT(id) AS Number FROM xformrecord WHERE CompanyId = ? AND FormId = ? AND SampleHHNo $SYLUserCond";
        $RsTotalQuerySYL = $app->getDBConnection()->fetch($TotalQuerySYL, $loggedUserCompanyID, $FormID);
        $NumberOfRecordSYL = $RsTotalQuerySYL->Number;

        $TotalQueryRAN = "SELECT COUNT(id) AS Number FROM xformrecord WHERE CompanyId = ? AND FormId = ? AND SampleHHNo $RANUserCond";
        $RsTotalQueryRAN = $app->getDBConnection()->fetch($TotalQueryRAN, $loggedUserCompanyID, $FormID);
        $NumberOfRecordRAN = $RsTotalQueryRAN->Number;

        $TotalQueryMYM = "SELECT COUNT(id) AS Number FROM xformrecord WHERE CompanyId = ? AND FormId = ? AND SampleHHNo $MYMUserCond";
        $RsTotalQueryMYM = $app->getDBConnection()->fetch($TotalQueryMYM, $loggedUserCompanyID, $FormID);
        $NumberOfRecordMYM = $RsTotalQueryMYM->Number;

        if ($FormID == $formIdSamplingData) { //For Establishment
            $TotalTergetQryDHK = "SELECT COUNT(id) as TotalTerget FROM InstituteInfo where UserID > 0 AND Type = '$InstType' AND $DHKDivCode";
            $TotalTergetQryCTG = "SELECT COUNT(id) as TotalTerget FROM InstituteInfo where UserID > 0 AND Type = '$InstType' AND $CTGDivCode";
            $TotalTergetQryRAJ = "SELECT COUNT(id) as TotalTerget FROM InstituteInfo where UserID > 0 AND Type = '$InstType' AND $RAJDivCode";
            $TotalTergetQryKHL = "SELECT COUNT(id) as TotalTerget FROM InstituteInfo where UserID > 0 AND Type = '$InstType' AND $KHLDivCode";
            $TotalTergetQryBAR = "SELECT COUNT(id) as TotalTerget FROM InstituteInfo where UserID > 0 AND Type = '$InstType' AND $BARDivCode";
            $TotalTergetQrySYL = "SELECT COUNT(id) as TotalTerget FROM InstituteInfo where UserID > 0 AND Type = '$InstType' AND $SYLDivCode";
            $TotalTergetQryRAN = "SELECT COUNT(id) as TotalTerget FROM InstituteInfo where UserID > 0 AND Type = '$InstType' AND $RANDivCode";
            $TotalTergetQryMYM = "SELECT COUNT(id) as TotalTerget FROM InstituteInfo where UserID > 0 AND Type = '$InstType' AND $MYMDivCode";
        } else if ($FormID == $formIdMainData) {//For Municipal
            $TotalTergetQryDHK = "SELECT COUNT(id) as TotalTerget FROM InstituteInfo where UserID > 0 AND Type = '$MunType' AND $DHKDivCode";
            $TotalTergetQryCTG = "SELECT COUNT(id) as TotalTerget FROM InstituteInfo where UserID > 0 AND Type = '$MunType' AND $CTGDivCode";
            $TotalTergetQryRAJ = "SELECT COUNT(id) as TotalTerget FROM InstituteInfo where UserID > 0 AND Type = '$MunType' AND $RAJDivCode";
            $TotalTergetQryKHL = "SELECT COUNT(id) as TotalTerget FROM InstituteInfo where UserID > 0 AND Type = '$MunType' AND $KHLDivCode";
            $TotalTergetQryBAR = "SELECT COUNT(id) as TotalTerget FROM InstituteInfo where UserID > 0 AND Type = '$MunType' AND $BARDivCode";
            $TotalTergetQrySYL = "SELECT COUNT(id) as TotalTerget FROM InstituteInfo where UserID > 0 AND Type = '$MunType' AND $SYLDivCode";
            $TotalTergetQryRAN = "SELECT COUNT(id) as TotalTerget FROM InstituteInfo where UserID > 0 AND Type = '$MunType' AND $RANDivCode";
            $TotalTergetQryMYM = "SELECT COUNT(id) as TotalTerget FROM InstituteInfo where UserID > 0 AND Type = '$MunType' AND $MYMDivCode";
        }
        //die("Total Target: ".$TotalTergetQryDHK);
        //echo $TotalTergetQryRAJ;

        $result_TotalTergetQryDHK = $app->getDBConnection()->fetch($TotalTergetQryDHK);
        $TotalTergetDHK = $result_TotalTergetQryDHK->TotalTerget;

        $result_TotalTergetQryCTG = $app->getDBConnection()->fetch($TotalTergetQryCTG);
        $TotalTergetCTG = $result_TotalTergetQryCTG->TotalTerget;

        $result_TotalTergetQryRAJ = $app->getDBConnection()->fetch($TotalTergetQryRAJ);
        $TotalTergetRAJ = $result_TotalTergetQryRAJ->TotalTerget;

        $result_TotalTergetQryKHL = $app->getDBConnection()->fetch($TotalTergetQryKHL);
        $TotalTergetKHL = $result_TotalTergetQryKHL->TotalTerget;

        $result_TotalTergetQryBAR = $app->getDBConnection()->fetch($TotalTergetQryBAR);
        $TotalTergetBAR = $result_TotalTergetQryBAR->TotalTerget;

        $result_TotalTergetQrySYL = $app->getDBConnection()->fetch($TotalTergetQrySYL);
        $TotalTergetSYL = $result_TotalTergetQrySYL->TotalTerget;

        $result_TotalTergetQryRAN = $app->getDBConnection()->fetch($TotalTergetQryRAN);
        $TotalTergetRAN = $result_TotalTergetQryRAN->TotalTerget;

        $result_TotalTergetQryMYM = $app->getDBConnection()->fetch($TotalTergetQryMYM);
        $TotalTergetMYM = $result_TotalTergetQryMYM->TotalTerget;

        $DataCollectionRatioDHK = Ratio($NumberOfRecordDHK, $TotalTergetDHK);
        $DataCollectionRatioCTG = Ratio($NumberOfRecordCTG, $TotalTergetCTG);
        $DataCollectionRatioRAJ = Ratio($NumberOfRecordRAJ, $TotalTergetRAJ);
        $DataCollectionRatioKHL = Ratio($NumberOfRecordKHL, $TotalTergetKHL);
        $DataCollectionRatioBAR = Ratio($NumberOfRecordBAR, $TotalTergetBAR);
        $DataCollectionRatioSYL = Ratio($NumberOfRecordSYL, $TotalTergetSYL);
        $DataCollectionRatioRAN = Ratio($NumberOfRecordRAN, $TotalTergetRAN);
        $DataCollectionRatioMYM = Ratio($NumberOfRecordMYM, $TotalTergetMYM);
        //die("Ratio: ".$DataCollectionRatioMYM);
        $DataSendingDateQuery = " Select CONVERT(date, EntryDate) as DataDate, count(*) as Number from xformrecord where FormId = ?";
        $DataSendingDateQuery .= " group by CONVERT(date, EntryDate) order by DataDate desc";
        $DataSendingDateRS = $app->getDBConnection()->fetchAll($DataSendingDateQuery, $FormID);

        if ($FormID == $formIdSamplingData) {
            $QueryDistLavel = "SELECT DISTINCT p.District_Name, p.District_Code, ( SELECT COUNT(SQ.Target) 
            FROM ( SELECT DISTINCT id as Target FROM InstituteInfo WHERE Type='$InstType' and District_Code = p.District_Code ) SQ ) as Target, 
            ( SELECT COUNT(id) FROM xformrecord WHERE xformrecord.FormId = $formIdSamplingData AND xformrecord.SampleHHNo IN ( SELECT id FROM InstituteInfo WHERE District_Code = p.District_Code and Type='$InstType') ) as Collected 
            FROM InstituteInfo as p WHERE p.UserID > 0 GROUP BY p.District_Code,p.District_Name ORDER BY p.District_Name asc";
        } else if ($FormID == $formIdMainData) {
            $QueryDistLavel = "SELECT DISTINCT p.District_Name, p.District_Code, ( SELECT COUNT(SQ.Target) 
            FROM ( SELECT DISTINCT id as Target FROM InstituteInfo WHERE Type='$MunType' and District_Code = p.District_Code ) SQ ) as Target, 
            ( SELECT COUNT(id) FROM xformrecord WHERE xformrecord.FormId = $formIdMainData AND xformrecord.SampleHHNo IN ( SELECT id FROM InstituteInfo WHERE District_Code = p.District_Code and Type='$MunType') ) as Collected 
            FROM InstituteInfo as p WHERE p.UserID > 0 GROUP BY p.District_Code,p.District_Name ORDER BY p.District_Name asc";
        }

        //die("SQL: ".$QueryDistLavel);
        $QueryDistLavelRS = $app->getDBConnection()->fetchAll($QueryDistLavel);
        ?>
        <section class="card">
            <div class="card-header">
                <div class="card-title">Form: <?php echo $FormName; ?></div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-3 mb-3">
                        <section class="card">
                            <div class="card-body" style="background-color: #abc4ab">
                                <div class="row">
                                    <div class="col-xl-12 text-center">
                                        <h2 class="card-title mt-3" style="color: <?php echo $boxFontColor; ?>">
                                            DHAKA</h2>
                                        <div class="liquid-meter-wrapper liquid-meter-md mt-3">
                                            <div class="liquid-meter">
                                                <meter min="0" max="100" value="<?php echo $DataCollectionRatioDHK; ?>"
                                                       id="meterSales"></meter>
                                            </div>
                                        </div>
                                        <h2 class="card-subtitle mt-3" style="color: <?php echo $boxFontColor; ?>">
                                            Target
                                            : <?php echo $TotalTergetDHK; ?></h2>
                                        <h2 class="card-subtitle mt-3" style="color: <?php echo $boxFontColor; ?>">
                                            Collected
                                            : <?php echo $NumberOfRecordDHK; ?></h2>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="col-lg-3 mb-3">
                        <section class="card">
                            <div class="card-body" style="background-color: #a39171">
                                <div class="row">
                                    <div class="col-xl-12 text-center">
                                        <h2 class="card-title mt-3" style="color: <?php echo $boxFontColor; ?>">
                                            CHATTOGRAM</h2>
                                        <div class="liquid-meter-wrapper liquid-meter-md mt-3">
                                            <div class="liquid-meter">
                                                <meter min="0" max="100" value="<?php echo $DataCollectionRatioCTG; ?>"
                                                       id="meterSales2"></meter>
                                            </div>
                                        </div>
                                        <h2 class="card-subtitle mt-3" style="color: <?php echo $boxFontColor; ?>">
                                            Target
                                            : <?php echo $TotalTergetCTG; ?></h2>
                                        <h2 class="card-subtitle mt-3" style="color: <?php echo $boxFontColor; ?>">
                                            Collected
                                            : <?php echo $NumberOfRecordCTG; ?></h2>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="col-lg-3 mb-3">
                        <section class="card">
                            <div class="card-body" style="background-color: #dcc9b6">
                                <div class="row">
                                    <div class="col-xl-12 text-center">
                                        <h2 class="card-title mt-3" style="color: <?php echo $boxFontColor; ?>">
                                            RAJSHAHI</h2>
                                        <div class="liquid-meter-wrapper liquid-meter-md mt-3">
                                            <div class="liquid-meter">
                                                <meter min="0" max="100" value="<?php echo $DataCollectionRatioRAJ; ?>"
                                                       id="meterSales3"></meter>
                                            </div>
                                        </div>
                                        <h2 class="card-subtitle mt-3" style="color: <?php echo $boxFontColor; ?>">
                                            Target
                                            : <?php echo $TotalTergetRAJ; ?></h2>
                                        <h2 class="card-subtitle mt-3" style="color: <?php echo $boxFontColor; ?>">
                                            Collected
                                            : <?php echo $NumberOfRecordRAJ; ?></h2>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="col-lg-3 mb-3">
                        <section class="card">
                            <div class="card-body" style="background-color: #727d71">
                                <div class="row">
                                    <div class="col-xl-12 text-center">
                                        <h2 class="card-title mt-3" style="color: <?php echo $boxFontColor; ?>">
                                            KHULNA</h2>
                                        <div class="liquid-meter-wrapper liquid-meter-md mt-3">
                                            <div class="liquid-meter">
                                                <meter min="0" max="100" value="<?php echo $DataCollectionRatioKHL; ?>"
                                                       id="meterSales4"></meter>
                                            </div>
                                        </div>
                                        <h2 class="card-subtitle mt-3" style="color: <?php echo $boxFontColor; ?>">
                                            Target
                                            : <?php echo $TotalTergetKHL; ?></h2>
                                        <h2 class="card-subtitle mt-3" style="color: <?php echo $boxFontColor; ?>">
                                            Collected
                                            : <?php echo $NumberOfRecordKHL; ?></h2>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 mb-3">
                        <section class="card">
                            <div class="card-body" style="background-color: #c0c0c0">
                                <div class="row">
                                    <div class="col-xl-12 text-center">
                                        <h2 class="card-title mt-3" style="color: <?php echo $boxFontColor; ?>">
                                            BARISHAL</h2>
                                        <div class="liquid-meter-wrapper liquid-meter-md mt-3">
                                            <div class="liquid-meter">
                                                <meter min="0" max="100" value="<?php echo $DataCollectionRatioBAR; ?>"
                                                       id="meterSales5"></meter>
                                            </div>
                                        </div>
                                        <h2 class="card-subtitle mt-3" style="color: <?php echo $boxFontColor; ?>">
                                            Target
                                            : <?php echo $TotalTergetBAR; ?></h2>
                                        <h2 class="card-subtitle mt-3" style="color: <?php echo $boxFontColor; ?>">
                                            Collected
                                            : <?php echo $NumberOfRecordBAR; ?></h2>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="col-lg-3 mb-3">
                        <section class="card">
                            <div class="card-body" style="background-color: #c6c5b9">
                                <div class="row">
                                    <div class="col-xl-12 text-center">
                                        <h2 class="card-title mt-3" style="color: <?php echo $boxFontColor; ?>">
                                            SYLHET</h2>
                                        <div class="liquid-meter-wrapper liquid-meter-md mt-3">
                                            <div class="liquid-meter">
                                                <meter min="0" max="100" value="<?php echo $DataCollectionRatioSYL; ?>"
                                                       id="meterSales6"></meter>
                                            </div>
                                        </div>
                                        <h2 class="card-subtitle mt-3" style="color: <?php echo $boxFontColor; ?>">
                                            Target
                                            : <?php echo $TotalTergetSYL; ?></h2>
                                        <h2 class="card-subtitle mt-3" style="color: <?php echo $boxFontColor; ?>">
                                            Collected
                                            : <?php echo $NumberOfRecordSYL; ?></h2>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="col-lg-3 mb-3">
                        <section class="card">
                            <div class="card-body" style="background-color: #a4ac86">
                                <div class="row">
                                    <div class="col-xl-12 text-center">
                                        <h2 class="card-title mt-3" style="color: <?php echo $boxFontColor; ?>">
                                            RANGPUR</h2>
                                        <div class="liquid-meter-wrapper liquid-meter-md mt-3">
                                            <div class="liquid-meter">
                                                <meter min="0" max="100" value="<?php echo $DataCollectionRatioRAN; ?>"
                                                       id="meterSales7"></meter>
                                            </div>
                                        </div>
                                        <h2 class="card-subtitle mt-3" style="color: <?php echo $boxFontColor; ?>">
                                            Target
                                            : <?php echo $TotalTergetRAN; ?></h2>
                                        <h2 class="card-subtitle mt-3" style="color: <?php echo $boxFontColor; ?>">
                                            Collected
                                            : <?php echo $NumberOfRecordRAN; ?></h2>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="col-lg-3 mb-3">
                        <section class="card">
                            <div class="card-body" style="background-color: #aeb4a9">
                                <div class="row">
                                    <div class="col-xl-12 text-center">
                                        <h2 class="card-title mt-3" style="color: <?php echo $boxFontColor; ?>">
                                            MYMENSINGH</h2>
                                        <div class="liquid-meter-wrapper liquid-meter-md mt-3">
                                            <div class="liquid-meter">
                                                <meter min="0" max="100" value="<?php echo $DataCollectionRatioMYM; ?>"
                                                       id="meterSales8"></meter>
                                            </div>
                                        </div>
                                        <h2 class="card-subtitle mt-3" style="color: <?php echo $boxFontColor; ?>">
                                            Target
                                            : <?php echo $TotalTergetMYM; ?></h2>
                                        <h2 class="card-subtitle mt-3" style="color: <?php echo $boxFontColor; ?>">
                                            Collected
                                            : <?php echo $NumberOfRecordMYM; ?></h2>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <section class="card">
                            <header class="card-header">
                                <h2 class="card-title">District Level Bar Chart</h2>
                            </header>
                            <div class="card-body">
                                <div class="chart chart-lg" id="morrisBar"></div>
                                <script type="text/javascript">
                                    // Build chart data safely
                                    var morrisBarData = [
                                        <?php
                                        foreach ($QueryDistLavelRS as $row) {
                                            echo json_encode([
                                                    'y' => $row->District_Name,
                                                    'a' => (int)($row->Target ?: 0),
                                                    'b' => (int)($row->Collected ?: 0)
                                                ]) . ",";
                                        }
                                        ?>
                                    ];

                                    // Initialize Morris Bar Chart
                                    Morris.Bar({
                                        element: 'morrisBar',
                                        data: morrisBarData,
                                        xkey: 'y',
                                        ykeys: ['a', 'b'],
                                        labels: ['Target', 'Collected'],
                                        barColors: ['#1e88e5', '#43a047'],
                                        hideHover: 'auto',
                                        resize: true
                                    });
                                </script>


                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </section>
    </section>
</div>
