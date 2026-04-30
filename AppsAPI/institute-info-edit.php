<?php
error_reporting(E_ALL);

require '../vendor/autoload.php';

use Solvers\Dsql\Application;

$app = new Application();

include "../Config/config.php";
include "../Lib/lib.php";

include "../Components/header-includes.php";

/*$userID = xss_clean($_REQUEST["userID"]);

$instName = xss_clean($_REQUEST["instName"]);
$instAddress = xss_clean($_REQUEST["instAddress"]);
$instMobileNo = xss_clean($_REQUEST["instMobileNo"]);*/

$InstID = xss_clean($_REQUEST["instID"]);
$UserID = xss_clean($_REQUEST["userID"]);


$isExist = getValue('InstituteInfo', 'COUNT(*)', "id = $InstID AND UserID = $UserID");
$isCollected = getValue('xformrecord', 'COUNT(*)', "SampleHHNo = $InstID");

if ($isCollected > 0) {
    MsgBox("Sorry, this institute`s information already colleced! You can`t update now!");
    exit();
} else if (!$isExist) {
    MsgBox('You are not permitted in this page!');
    exit();
}


//echo "I am here $instID";


/*$param = "Q4A=N'$instName', ADDRESS=N'$instAddress', MOBILE_NO=N'$instMobileNo'";
$cond = "id='$instID'";

$isCollected = getValue('xformrecord', 'COUNT(*)', "SampleHHNo = $instID");

if ($isCollected > 0) {
    echo "Sorry, this institute`s information already colleced! You can`t update now!";
    if (empty($instName) || empty($instAddress) || empty($instMobileNo)) {
        echo 'Sorry, some information are missing!';
    } else {
        if (Edit('InstituteInfo', $param, $cond)) {
            echo 'Successfully updated.';
        } else
            echo 'Failed to update!';
    }
}*/
?>

<div class="inner-wrapper">
    <section role="main" class="content-body">
        <div class="row">
            <div class="col-lg-2 mb-0"></div>
            <div class="col-lg-8 mb-0">
                <section class="card">
                    <header class="card-header">
                        <h2 class="card-title">Edit Information</h2>
                    </header>
                    <div class="card-body">
                        <form name="editForm" class="form-horizontal form-bordered" action="" method="post">
                            <?php
                            if ($isExist > 0) {
                                $qryInst = "SELECT * FROM InstituteInfo WHERE id = ? and UserID = ?";
                                $queryRS = $app->getDBConnection()->query($qryInst, $InstID, $UserID);

                                foreach ($queryRS as $row) {
                                    $InstID = $row->id;
                                    $InstName = $row->Q4A;
                                    $InstMobile = $row->MOBILE_NO;
                                    $InstAddress = $row->ADDRESS;

                                    $Division = $row->DIVISION_NAME;
                                    $District = $row->DISTRICT_NAME;
                                }
                                ?>
                                <div class="form-group row pb-4">
                                    <label class="col-lg-9 control-label text-lg-start pt-2"><b>Institute ID
                                            : <?php echo $InstID; ?></b></label>
                                </div>

                                <div class="form-group">
                                    <label for="Name">Institute Name<span class="required">*</span></label>
                                    <input type="text" class="form-control" name="name" id="name"
                                           value="<?php echo $InstName; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="mobile">Mobile No<span class="required">*</span></label>
                                    <input type="number" placeholder="01*********" minlength="11" maxlength="11"
                                           class="form-control" name="mobile" id="mobile"
                                           value="<?php echo $InstMobile; ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="address">Address<span class="required">*</span></label>
                                    <input type="text" class="form-control" name="address" id="address"
                                           value="<?php echo $InstName; ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="Division">Division</label>
                                    <input type="text" class="form-control" style='background-color: lightgrey'
                                           name="Division" id="Division" value="<?php echo $Division; ?>"
                                           readonly>
                                </div>

                                <div class="form-group">
                                    <label for="District">District</label>
                                    <input type="text" class="form-control" style='background-color: lightgrey'
                                           name="District" id="District" value="<?php echo $District; ?>"
                                           readonly>
                                </div>

                                <?php
                            }
                            ?>

                            <footer class="card-footer">
                                <div class="row justify-content-end">
                                    <div class="col-lg-9">
                                        <input class="btn btn-primary" name="update" type="submit" id="update"
                                               value="Update">
                                        <button type="button" class="btn btn-secondary"
                                                onclick="window.open('', '_self', ''); window.close();">Close
                                        </button>
                                    </div>
                                </div>
                            </footer>
                        </form>
                    </div>
                    <?php
                    if ($_REQUEST['update'] === 'Update') {
                        $Name = xss_clean($_REQUEST["name"]);
                        $InstName = strtoupper($Name);

                        $Address = xss_clean($_REQUEST["address"]);
                        $InstAddress = strtoupper($Address);

                        $Mobile = xss_clean($_REQUEST["mobile"]);
                        $InstMobile = "0" . substr($Mobile, -10);

                        $param = "Q4A=N'$InstName', ADDRESS=N'$InstAddress', MOBILE_NO=N'$InstMobile'";
                        $cond = "id='$InstID'";

                        //exit();

                        if (empty($Name) || empty($Mobile) || empty($Address)) {
                            echo 'Sorry, some information are missing!';
                        } else {
                            if (Edit('InstituteInfo', $param, $cond)) {
                                MsgBox2('Data updated successfully.');
                            } else
                                MsgBox2('Failed to update data!');

                            //echo 'Readay';
                        }
                    }
                    ?>
                </section>
            </div>
            <div class="col-lg-2 mb-0"></div>
        </div>
        <!-- end: page -->
    </section>
</div>
