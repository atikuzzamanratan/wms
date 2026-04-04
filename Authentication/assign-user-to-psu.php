<?php
$qrySelectUser = "SELECT id, UserName, FullName FROM userinfo WHERE IsActive = 1 AND UserName LIKE '$dataCollectorNamePrefix%' AND CompanyID = ? ORDER BY UserName ASC";
$resQrySelectUser = $app->getDBConnection()->fetchAll($qrySelectUser, $loggedUserCompanyID);

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
                                <label class="col-lg-3 control-label text-sm-end pt-2">User Select<span
                                            class="required">*</span></label>
                                <div class="col-lg-6">
                                    <select data-plugin-selectTwo class="form-control populate"
                                            name="SelectedUser" id="SelectedUser" required>
                                        <option value="">Choose user</option>
                                        <?PHP
                                        foreach ($resQrySelectUser as $row) {
                                            echo '<option value="' . $row->id . '">' . $row->UserName . ' | ' . substr($row->FullName, 0, 102) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row pb-3">
                                <label class="col-lg-3 control-label text-lg-end pt-2"
                                       for="NumberOfData">Institute IDs<span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <textarea class="form-control" id="NumberOfData" name="InstituteId"
                                              placeholder="comma seperated institute id (1, 2, 3,.....)"
                                              required></textarea>
                                </div>
                            </div>

                            <footer class="card-footer">
                                <div class="row justify-content-end">
                                    <div class="col-lg-9">
                                        <input class="btn btn-primary" name="show" type="submit" id="show"
                                               value="Assign">
                                    </div>
                                </div>
                            </footer>
                        </form>
                    </div>
                </section>
                <?php

                if ($_REQUEST['show'] === 'Assign') {

                    $AssignUserNameID = xss_clean($_REQUEST['SelectedUser']);
                    $NumberOfRecord = xss_clean($_REQUEST['InstituteId']);

                    if ($AssignUserNameID == null || $NumberOfRecord == null) {
                        MsgBox('Required information missing!');
                    } else {
                        $arrayInstID = explode(',', trim($NumberOfRecord));

                        foreach ($arrayInstID as $value) {
                            $Query = "UPDATE InstituteInfo SET UserID = $AssignUserNameID WHERE id = $value";
                            $resQuery = $app->getDBConnection()->query($Query);
                        }
                        MsgBox('Assigned Successfully');
                    }
                }
                ?>
            </div>
        </div>
        <!-- end: page -->
    </section>
</div>
