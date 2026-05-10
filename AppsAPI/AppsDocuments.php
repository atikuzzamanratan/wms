<?php
require '../vendor/autoload.php';

use Solvers\Dsql\Application;

$app = new Application();

include "../Config/config.php";

$AuthToken = $_GET['authToken'];

if ($AuthToken != $AuthTokenValue) {
    echo $unAuthorizedMsg;
    exit();
}

include_once '../Components/header-includes.php';
?>

    <section class="card">
        <header class="card-header">
            <div class="card-title"><h4>Useful Documents</h4></div>
        </header>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <tbody>
                <tr>
                    <td><a href="../Documents/WMS-Mobile-Application-Manual-v3"><i class="bi bi-phone"></i>মোবাইল এপ্লিকেশন ম্যানুয়াল</a></td>
                </tr>
                <tr>
                    <td><a href="../Documents/EPER-WM-Survey-2026_Questionnaire.pdf"><i class="bi bi-phone"></i>প্রশ্নপত্র: পরিবেশ সংরক্ষণ ব্যয়, সম্পদ এবং বর্জ্য ব্যবস্থাপনা (প্রতিষ্ঠান) জরিপ ২০২৬ (EPER & WM Survey)</a>
                    </td>
                </tr>
                <tr>
                    <td><a href="../Documents/MWM-Survey-2026-Questionnarie.pdf"><i class="bi bi-phone"></i>প্রশ্নপত্র: বর্জ্য ব্যবস্থাপনা জরিপ (সিটি কর্পোরেশন ও পৌরসভা) ২০২৬ (MWM Survey)</a>
                    </td>
                </tr>

                </tbody>
            </table>
        </div>
    </section>


<?php
include_once "../Components/footer-includes.php";
?>