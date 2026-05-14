<?php
$qrySupervisor = "SELECT id FROM assignsupervisor WHERE SupervisorID = ?";
$rsSupervisor = $app->getDBConnection()->fetch($qrySupervisor, $loggedUserID);
$SuperID = $rsSupervisor->id;

$baseURL = get_base_url();

?>

<div class="inner-wrapper">
    <section role="main" class="content-body">
        <header class="page-header">
            <h2><?php echo $MenuLebel; ?></h2>

            <?php include_once 'Components/header-home-button.php'; ?>
            <!--<script async src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCh0aR_e-GOQpWjDUSwxOMTCyRtAdaHrYI"></script>-->
        </header>

        <!-- start: page -->
        <div class="row">
            <div class="col-lg-12 mb-0">
                <section class="card">
                    <div class="card-body">
                        <form class="form-horizontal form-bordered" action="" method="post">
                            <div class="form-group row pb-3">
                                <label class="col-lg-3 control-label text-sm-end pt-2">User Select</label>
                                <div class="col-lg-6">
                                    <?php if (strpos($loggedUserName, 'admin') === false) { ?>
                                    <select data-plugin-selectTwo class="form-control populate"
                                            name="SelectedUserID"
                                            id="SelectedUserID" title="Please select user">
                                        <?php } else { ?>
                                        <select data-plugin-selectTwo class="form-control populate"
                                                name="SelectedUserID"
                                                id="SelectedUserID" title="Please select user">
                                            <?php } ?>
                                            <option value="">Choose user</option>
                                            <?PHP
                                            if ($loggedUserName == 'admin') {
                                                $qryDistUser = "SELECT id, UserName, FullName FROM userinfo WHERE IsActive = 1 AND UserName LIKE '$dataCollectorNamePrefix%' ORDER BY UserName ASC";
                                                $resQryDistUser = $app->getDBConnection()->fetchAll($qryDistUser);
                                            } else if (strpos($loggedUserName, 'admin') !== false) {
                                                $qryDistUser = "SELECT id, UserName, FullName FROM userinfo WHERE IsActive = 1 AND UserName LIKE '$dataCollectorNamePrefix%' AND CompanyID = ? $assignedIDFilter ORDER BY UserName ASC";
                                                $resQryDistUser = $app->getDBConnection()->fetchAll($qryDistUser, $loggedUserCompanyID);
                                            } else if ($SuperID) {
                                                $qryDistUser = "SELECT u.id, u.UserName, u.FullName FROM assignsupervisor as a JOIN userinfo as u ON a.UserID = u.id WHERE u.IsActive = 1 AND u.UserName LIKE '$dataCollectorNamePrefix%' AND a.SupervisorID = ?";
                                                $resQryDistUser = $app->getDBConnection()->fetchAll($qryDistUser, $loggedUserID);
                                            } else if (strpos($loggedUserName, 'dist') !== false) {
                                                $qryDistUser = "SELECT u.id, u.UserName, u.FullName FROM assignsupervisor as a JOIN userinfo as u ON a.UserID = u.id WHERE u.IsActive = 1 AND u.UserName LIKE '$dataCollectorNamePrefix%' AND a.DistCoordinatorID = ?";
                                                $resQryDistUser = $app->getDBConnection()->fetchAll($qryDistUser, $loggedUserID);
                                            } else if (strpos($loggedUserName, 'div') !== false) {
                                                $qryDistUser = "SELECT u.id, u.UserName, u.FullName FROM assignsupervisor as a JOIN userinfo as u ON a.UserID = u.id WHERE u.IsActive = 1 AND u.UserName LIKE '$dataCollectorNamePrefix%' AND a.DivCoordinatorID = ?";
                                                $resQryDistUser = $app->getDBConnection()->fetchAll($qryDistUser, $loggedUserID);
                                            } else {
                                                $qryDistUser = "SELECT id, UserName, FullName FROM userinfo WHERE IsActive = 1 AND UserName LIKE '$dataCollectorNamePrefix%' AND CompanyID = ? and UserName = ? ORDER BY UserName ASC";
                                                $resQryDistUser = $app->getDBConnection()->fetchAll($qryDistUser, $loggedUserCompanyID, $loggedUserName);
                                            }

                                            foreach ($resQryDistUser as $row) {
                                                echo '<option value="' . $row->id . '"' . (isset($SelectedUserID) && !empty($SelectedUserID) && $row->id == $SelectedUserID ? ' selected' : '') . '>' . $row->UserName . ' | ' . substr($row->FullName, 0, 102) . '</option>';
                                            }
                                            ?>
                                        </select>
                                </div>
                            </div>

                            <div class="form-group row pb-3">
                                <label class="col-lg-3 control-label text-sm-end pt-2">Location Type</label>
                                <div class="col-lg-6">
                                    <select data-plugin-selectTwo id="SelectedLocationType" name="SelectedLocationType"
                                            class="form-control populate">
                                        <option value="lastLocation" selected>Last live location</option>
                                        <option value="allLocation">All locations</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row pb-3">
                                <label class="col-lg-3 control-label text-sm-end pt-2"></label>
                                <div class="col-lg-6">
                                    <div class="checkbox-custom checkbox-primary">
                                        <input id="chkAll" value="1" type="checkbox" name="chkAll"/>
                                        <label for="chkAll">All user's last location</label>
                                    </div>
                                </div>
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
                    $SelectedUserID       = $_REQUEST['SelectedUserID'] ?? '';
                    $SelectedLocationType = $_REQUEST['SelectedLocationType'] ?? '';
                    $checkAll             = $_REQUEST['chkAll'] ?? '';

                if (!$checkAll && empty($SelectedUserID)) {
                    ?>
                    <script>
                        alert("❌ Please select User!");
                        window.location.href = "<?php echo strtok($baseURL . '?parent=ViewUserLocation', '?'); ?>";
                    </script>
                <?php
                exit();
                }

                // ==================== QUERY ====================
                if ($checkAll) {
                    $UserData = "All Users";
                    $selQuery = "SELECT t.Userid, t.Location, t.DateTime, ui.UserName, ui.FullName, ui.MobileNumber 
                     FROM (SELECT Userid, Location, DateTime, ROW_NUMBER() OVER (PARTITION BY Userid ORDER BY DateTime DESC) AS rn 
                           FROM UserLiveLocation) t 
                     JOIN userinfo ui ON t.UserId = ui.id 
                     WHERE rn = 1 ORDER BY t.DateTime DESC";
                } else {
                    $SelectedUserName = getValue('userinfo', 'UserName', "id = $SelectedUserID");
                    $SelectedFullName = getValue('userinfo', 'FullName', "id = $SelectedUserID");
                    $UserData = "$SelectedFullName ($SelectedUserName)";

                    if ($SelectedLocationType === "lastLocation") {
                        $selQuery = "SELECT TOP 1 t.Userid, t.Location, t.DateTime, ui.UserName, ui.FullName, ui.MobileNumber 
                         FROM UserLiveLocation t JOIN userinfo ui ON t.UserId = ui.id 
                         WHERE t.UserId = ? ORDER BY t.DateTime DESC";
                    } else {
                        $selQuery = "SELECT t.Userid, t.Location, t.DateTime, ui.UserName, ui.FullName, ui.MobileNumber 
                         FROM UserLiveLocation t JOIN userinfo ui ON t.UserId = ui.id 
                         WHERE t.UserId = ? ORDER BY t.DateTime DESC";
                    }
                }

                $resQry = $app->getDBConnection()->fetchAll($selQuery, $checkAll ? [] : [$SelectedUserID]);

                $locationArray = [];
                foreach ($resQry as $row) {
                    if (!empty($row->Location)) {
                        $geo = explode(",", $row->Location);
                        if (count($geo) >= 2) {
                            $lat = trim($geo[0]);
                            $lon = trim($geo[1]);
                            if (is_numeric($lat) && is_numeric($lon)) {

                                $fullName = htmlspecialchars($row->FullName ?? 'N/A');
                                $userName = htmlspecialchars($row->UserName ?? '');
                                $mobile = htmlspecialchars($row->MobileNumber ?? 'N/A');
                                $mobile = "+880".substr($mobile,-10);
                                $dateTime = htmlspecialchars($row->DateTime ?? '');
                                $dateTime = date('d-m-Y H:i:s', strtotime($dateTime));

                                $tooltip = "👤 $fullName ($userName)\n📱 $mobile";

                                $locationArray[] = [
                                    (float)$lat,
                                    (float)$lon,
                                    $fullName,
                                    $userName,
                                    $mobile,
                                    $dateTime
                                ];
                            }
                        }
                    }
                }

                $javascriptData = json_encode($locationArray);
                ?>

                    <section class="card">
                        <div class="card-header">
                            <div class="card-title">User: <?php echo htmlspecialchars($UserData); ?></div>
                        </div>
                        <div class="card-body">
                            <div id="map" style="width:100%; height:700px;"></div>

                            <script type="text/javascript">
                                function initUserMap() {
                                    var locations = <?php echo $javascriptData; ?>;

                                    var mapDiv = document.getElementById('map');
                                    mapDiv.innerHTML = '';

                                    if (!locations || locations.length === 0) {
                                        mapDiv.innerHTML = `<div style="text-align:center; padding:120px; color:#666;"><h4>No location data found.</h4></div>`;
                                        return;
                                    }

                                    var map = new google.maps.Map(mapDiv, {
                                        zoom: 9,
                                        center: new google.maps.LatLng(locations[0][0], locations[0][1]),
                                        mapTypeId: google.maps.MapTypeId.ROADMAP
                                    });

                                    var infowindow = new google.maps.InfoWindow();

                                    for (let i = 0; i < locations.length; i++) {
                                        let loc = locations[i];     // Important: use 'let' here

                                        var marker = new google.maps.Marker({
                                            position: new google.maps.LatLng(loc[0], loc[1]),
                                            map: map,
                                            title: loc[2] + " (" + loc[4] + ")",   // Hover shows correct data
                                            animation: google.maps.Animation.DROP
                                        });

                                        // Click listener - Now shows correct user data
                                        marker.addListener('click', function() {
                                            var contentString = `
                                <div style="font-family:Arial,sans-serif; min-width:280px; padding:8px;">
                                    <h5 style="margin:5px 0 8px 0; color:#333;">👤 ${loc[2]}</h5>
                                    <p style="margin:4px 0;"><strong>Username:</strong> ${loc[3]}</p>
                                    <p style="margin:8px 0 12px 0;"><strong>📱 Mobile:</strong> ${loc[4]}</p>

                                    <a href="https://wa.me/${loc[4]}" target="_blank"
                                       style="background:#25D366; color:white; padding:10px 16px;
                                              text-decoration:none; border-radius:6px; display:inline-flex;
                                              align-items:center; font-weight:bold;">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/6b/WhatsApp.svg/20px-WhatsApp.svg.png"
                                             style="margin-right:8px;" alt="WA">
                                        Chat on WhatsApp
                                    </a>

                                    ${loc[5] ? `<p style="margin-top:12px; font-size:0.9em; color:#555;">
                                        <strong>Location Time:</strong> ${loc[5]}</p>` : ''}
                                </div>
                            `;
                                            infowindow.setContent(contentString);
                                            infowindow.open(map, this);
                                        });
                                    }
                                }

                                window.onload = initUserMap;
                            </script>
                        </div>
                    </section>
                    <?php
                }
                ?>
            </div>
        </div>
        <!-- end: page -->
    </section>
</div>

