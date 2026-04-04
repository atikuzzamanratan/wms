<script src="https://cdn.jsdelivr.net/gh/StephanWagner/jBox@v1.3.2/dist/jBox.all.min.js"></script>
<link href="https://cdn.jsdelivr.net/gh/StephanWagner/jBox@v1.3.2/dist/jBox.all.min.css" rel="stylesheet">

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
                            <div class="form-group row pb-3" id="userDiv">
                                <label class="col-lg-3 control-label text-sm-end pt-2">User Select
                                    <?php if (strpos($loggedUserName, 'cs') !== false) { ?><span
                                            class="required">*</span><?php } ?></label>
                                <div class="col-lg-6">
                                    <select data-plugin-selectTwo class="form-control populate"
                                            name="SelectedUserID"
                                        <?php if (strpos($loggedUserName, 'cs') !== false) { ?>
                                            required
                                        <?php } ?>
                                            id="SelectedUserID" title="Please select user">
                                        <option value="">Choose user</option>
                                        <?PHP
                                        if ($loggedUserName == 'admin') {
                                            $qryDistUser = "SELECT id, UserName, FullName FROM userinfo WHERE IsActive = 1 AND UserName LIKE '$dataCollectorNamePrefix%' ORDER BY UserName ASC";
                                            $resQryDistUser = $app->getDBConnection()->fetchAll($qryDistUser);
                                        } else if (strpos($loggedUserName, 'admin') !== false) {
                                            $qryDistUser = "SELECT id, UserName, FullName FROM userinfo WHERE IsActive = 1 AND UserName LIKE '$dataCollectorNamePrefix%' AND CompanyID = ? ORDER BY UserName ASC";
                                            $resQryDistUser = $app->getDBConnection()->fetchAll($qryDistUser, $loggedUserCompanyID);
                                        } else if ($SuperID) {
                                            $qryDistUser = "SELECT u.id, u.UserName, u.FullName FROM assignsupervisor as a JOIN userinfo as u ON a.UserID = u.id WHERE u.IsActive = 1 AND u.UserName LIKE '$dataCollectorNamePrefix%' AND a.SupervisorID = ?";
                                            $resQryDistUser = $app->getDBConnection()->fetchAll($qryDistUser, $loggedUserID);
                                        } else if (strpos($loggedUserName, 'dist') !== false) {
                                            $qryDistUser = "SELECT u.id, u.UserName, u.FullName FROM assignsupervisor as a JOIN userinfo as u ON a.UserID = u.id WHERE u.IsActive = 1 AND u.UserName LIKE '$dataCollectorNamePrefix%' AND a.DistCoordinatorID = ?";
                                            $resQryDistUser = $app->getDBConnection()->fetchAll($qryDistUser, $loggedUserID);
                                        } else if (strpos($loggedUserName, 'cs') !== false) {
                                            $qryDistUser = "SELECT u.id, u.UserName, u.FullName FROM assignsupervisor as a JOIN userinfo as u ON a.UserID = u.id WHERE u.IsActive = 1 AND u.UserName LIKE '$dataCollectorNamePrefix%' AND a.SupervisorID = ?";
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


                            <?php
                            if (strpos($loggedUserName, 'admin')) {
                                ?>

                                <div class="form-group row pb-3">
                                    <label class="col-lg-3 control-label text-sm-end pt-2"></label>
                                    <div class="col-lg-6">
                                        <div class="checkbox-custom checkbox-warning">
                                            <input id="chkAll" value="chkAll" type="checkbox"
                                                   name="chkAll" <?php echo isset($checkAll) && $checkAll == 'chkAll' ? 'checked' : ''; ?> />
                                            <label for="chkAll">All Users</label>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>

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
                <?php
                if ($_REQUEST['show'] === 'Show') {
                    $SelectedUserID = $_REQUEST['SelectedUserID'];
                    $checkAll = $_REQUEST['chkAll'];

                    if ($checkAll == 'chkAll') {
                        $SelectedCheckAll = 1;
                    } else {
                        $SelectedCheckAll = 0;
                    }

                    //echo "$SelectedUserID | $SelectedCheckAll";
                    //exit();

                    if (empty($SelectedUserID) && empty($checkAll)) {
                        MsgBox('Please select All Users or a specific User.');
                        ReloadPage();
                    } else {
                        ?>
                        <input type="hidden" id="DataUserID" value="<?php echo $SelectedUserID; ?>">
                        <input type="hidden" id="DataChkAll" value="<?php echo $SelectedCheckAll; ?>">

                        <br>
                        <section class="card">
                            <div class="card-body">
                                <table class="table table-bordered table-striped" id="tblViewData">
                                    <thead>
                                    <tr>
                                        <th>Actions</th>
                                        <th>ID</th>
                                        <th>Division Name</th>
                                        <th>District Name</th>
                                        <th>BSIC Code</th>
                                        <th>BSIC Detail</th>
                                        <th>Institute Name</th>
                                        <th>Address</th>
                                        <th>Mobile No</th>
                                        <th>Assigned User</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </section>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var body = $('body');

        var DataUserID = body.find('#DataUserID').val(),
            DataChkAll = body.find('#DataChkAll').val();

        var dataTable = $('#tblViewData').DataTable({
            "aLengthMenu": [
                [20, 10, 50, 100, 500, 1000, 5000, 100000000],
                [20, 10, 50, 100, 500, 1000, 5000, "All"]
            ],
            "processing": true,
            "serverSide": true,
            "ajax": {
                data: {
                    DataUserID: DataUserID,
                    DataChkAll: DataChkAll
                },
                url: "<?php echo $dataURL = $baseURL . "Authentication/ajax-data/view-psu-data-ajax-data.php"; ?>",
                type: "POST"
            },
            "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                if (aData[13] > 0) {
                    $('td', nRow).css('background-color', '#FBC6C2');
                }
            }
        });
    });
</script>
