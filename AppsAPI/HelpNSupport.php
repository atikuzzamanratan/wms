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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <section class="card">
        <header class="card-header">
            <!--<div class="card-title"><h4>Help & Support</h4></div>-->
            <div class="card-title"><h4> </h4></div>
        </header>
        <div class="card-body">
            <table class="table table-striped table-bordered">
                <tr>
                    <th style="color: firebrick"><b>ROBI Help Line</b></th>
                </tr>
                <tr>
                    <td>
                        <b>ROBI Govt. Project Hotline Number:</b><br>
                        <a href="tel:+8801880004420">
                            <i class="bi bi-phone"></i> <b> +8801880004420</b>
                        </a>
                        <br/>[যখন ফোন করবেন তখন বলবেন `আপনি বি বি এস থেকে ফোন করছেন` বলে সমস্যার কথা জানাবেন]
                    </td>
                </tr>
                <tr>
                    <td>ডাটা (MB) চেক *3#</td>
                </tr>
                <tr>
                    <td>ব্যলান্স চেক/বকেয়া বিল *1#</td>
                </tr>
            </table>
            <table class="table table-striped table-bordered">
                <tr>
                    <th style="color: blue"><b>Project Head quarter</b></th>
                </tr>
                <tr>
                    <td>
                        <b>Md. Rafiqul Islam</b><br>
                        Director (In Charge)<br>
                        Director General Office,<br>
                        Bangladesh Bureau of Statistics<br>
                        (Additional Charge: SCEMP Project, BBS)<br>
                        <a href="tel:+8801712141750">
                            <i class="bi bi-phone"></i> <b> Mobile: +8801712141750</b>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Surangit Kumar Ghosh</b><br>
                        Deputy Director<br>
                        National Accounting Wing<br>
                        Bangladesh Bureau of Statistics<br>
                        <a href="tel:+8801727301205">
                            <i class="bi bi-phone"></i> <b> Mobile: +8801727301205</b>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Ahshan Habib</b><br>
                        Statistical Officer, FA & MIS<br>
                        Bangladesh Bureau of Statistics<br>
                        <a href="tel:+8801310724965">
                            <i class="bi bi-phone"></i><b> Mobile: +8801310724965</b>
                        </a>
                    </td>
                </tr>
            </table>
            <table class="table table-striped table-bordered">
                <tr>
                    <th style="color: rebeccapurple">Solvers Support (9:15 A.M to 5:00 P.M)</th>
                </tr>
                <tr>
                    <td>
                        1st Line Support:
                        <a href="tel:+8801720540859">
                            <i class="bi bi-phone"></i> +8801720540859
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>2nd Line Support:
                        <a href="tel:+8801810096285">
                            <i class="bi bi-phone"></i> +8801810096285
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>3rd Line Support:
                        <a href="tel:+8801329684382">
                            <i class="bi bi-phone"></i> +8801329684382
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>4th Line Support:
                        <a href="tel:+8801841190330">
                            <i class="bi bi-phone"></i> +8801841190330
                        </a>
                    </td>
                </tr>
            </table>
        </div>
    </section>


<?php
include_once "../Components/footer-includes.php";
?>