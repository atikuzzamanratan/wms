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
                    $SelectedUserID = $_REQUEST['SelectedUserID'];
                    $SelectedLocationType = $_REQUEST['SelectedLocationType'];
                    $checkAll = $_REQUEST['chkAll'];

                    //echo "$SelectedUserID ~ $SelectedLocationType ~ $checkAll";
                    //exit();

                if (!$checkAll && $SelectedUserID == "") {
                    ?>
                    <script type="text/javascript">
                        alert("❌ Please select both User and Location Type!");

                        // Redirect to same page WITHOUT 'show' parameter → stops loop
                        window.location.href = "<?php echo strtok($baseURL . '?parent=ViewUserLocation'); ?>";
                    </script>
                <?php
                exit();
                }

                if ($checkAll) {
                    $UserData = "All users";
                    $selQuery = "SELECT Userid, Location, DateTime FROM (SELECT Userid, Location, DateTime, ROW_NUMBER() OVER (PARTITION BY Userid ORDER BY DateTime DESC) AS rn FROM UserLiveLocation) t WHERE rn = 1 ORDER BY DateTime DESC";
                } else {
                    $SelectedUserName = getValue('userinfo', 'UserName', "id = $SelectedUserID");
                    $SelectedFullName = getValue('userinfo', 'FullName', "id = $SelectedUserID");
                    $UserData = "$SelectedFullName ($SelectedUserName)";

                    if ($SelectedLocationType === "lastLocation") {
                        $selQuery = "SELECT TOP 1 * FROM UserLiveLocation WHERE UserId = $SelectedUserID ORDER BY DateTime DESC";
                    } else {
                        $selQuery = "SELECT * FROM UserLiveLocation WHERE UserId = $SelectedUserID ORDER BY DateTime DESC";
                    }
                }
                $resQry = $app->getDBConnection()->fetchAll($selQuery);

                $locationArray = [];
                foreach ($resQry as $row) {
                    if (!empty($row->Location)) {
                        $geo = explode(",", $row->Location);
                        if (count($geo) >= 2) {
                            $lat = trim($geo[0]);
                            $lon = trim($geo[1]);
                            if (is_numeric($lat) && is_numeric($lon)) {
                                $locationArray[] = [(float)$lat, (float)$lon];
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
                                // Global variable to track current map
                                var currentMap = null;

                                function initUserMap() {
                                    var locations = <?php echo $javascriptData; ?>;

                                    // Clear previous map if exists
                                    if (currentMap) {
                                        currentMap = null;
                                    }

                                    var mapDiv = document.getElementById('map');
                                    mapDiv.innerHTML = ''; // Clear previous content

                                    if (!locations || locations.length === 0) {
                                        mapDiv.innerHTML = `
                            <div style="text-align:center; padding:100px; color:#666;">
                                <h4>No location data found for this user.</h4>
                            </div>`;
                                        return;
                                    }

                                    var initLat = locations[0][0];
                                    var initLon = locations[0][1];

                                    currentMap = new google.maps.Map(mapDiv, {
                                        zoom: 10,
                                        center: new google.maps.LatLng(initLat, initLon),
                                        mapTypeId: google.maps.MapTypeId.ROADMAP
                                    });

                                    var infowindow = new google.maps.InfoWindow();

                                    for (var i = 0; i < locations.length; i++) {
                                        var marker = new google.maps.Marker({
                                            position: new google.maps.LatLng(locations[i][0], locations[i][1]),
                                            map: currentMap,
                                            title: 'Location ' + (i + 1) +
                                                '\nLat: ' + locations[i][0] +
                                                '\nLon: ' + locations[i][1],
                                            animation: google.maps.Animation.DROP
                                        });
                                        //animation: google.maps.Animation.BOUNCE
                                        marker.addListener('click', function () {
                                            infowindow.setContent(this.title.replace(/\n/g, '<br>'));
                                            infowindow.open(currentMap, this);
                                        });
                                    }
                                }

                                // Initialize map when script loads
                                window.onload = initUserMap;
                                // Also run immediately in case window is already loaded
                                if (document.readyState === 'complete') {
                                    initUserMap();
                                }
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

