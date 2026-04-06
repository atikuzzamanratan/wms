<?PHP

//https://scempbbs.com/AppsAPI/ViewPSUDetail.php?UserID=69&authToken=Mwgq0LcFGSHvYdVzb1Ifq3L9lhWmi4IXBDWcQZR9hUt1q7UboELrUFVJZO244Ujo

//error_reporting(E_ALL);

require '../vendor/autoload.php';

use Solvers\Dsql\Application;

$app = new Application();

include "../Config/config.php";
include "../Lib/lib.php";

$UserID = $app->cleanInput($_REQUEST['UserID']);

$AuthToken = $_GET['authToken'];
if ($AuthToken != $AuthTokenValue) {
    echo $unAuthorizedMsg;
    exit();
}

$dataURL = $baseURL . "AppsAPI/ajax-data/institute-list-ajax-data.php?ui=$UserID";

include_once '../Components/header-includes.php';
?>

<div class="inner-wrapper">
    <section role="main" class="content-body">
        <section class="card">
            <div class="card-body">
                <table class="table table-bordered table-striped" id="datatable-ajax"
                       data-url="<?php echo $dataURL; ?>">
                    <thead>
                    <tr>
                        <th>Actions</th>
                        <th>ID</th>
                        <th>Division</th>
                        <th>Disctict</th>
                        <th>BSIC Code</th>
                        <th>BSIC Detail</th>
                        <th>Institute Name</th>
                        <th>Address</th>
                        <th>Mobile No</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </section>
    </section>
</div>

<?php
include_once '../Components/footer-includes.php';
?>