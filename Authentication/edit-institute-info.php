<?php
error_reporting(E_ALL);

require '../vendor/autoload.php';

use Solvers\Dsql\Application;

$app = new Application();

include "../Config/config.php";
include "../Lib/lib.php";

$Id = xss_clean($_REQUEST["id"]);
$instName = xss_clean($_REQUEST["instName"]);
$instAddress = xss_clean($_REQUEST["instAddress"]);
$mobileNo = xss_clean($_REQUEST["mobileNo"]);

$cond = "id=$Id";

$type = getValue('InstituteInfo', 'Type', "$cond");

$param = "Q4A=N'$instName', ADDRESS=N'$instAddress', MOBILE_NO='$mobileNo'";

if ($type == "Municipal") {
    if (empty($instName)) {
        echo 'Sorry, name missing!';
    } elseif (!preg_match('/^[A-Za-z0-9\s\.,!?\'"-]+$/u', trim($instName))) {
        echo 'Failed, name contains non-English characters';
    } else {
        if (Update('InstituteInfo', $param, $cond)) {
            echo 'Successfully updated.';
        } else
            echo 'Failed to update!';
    }
} else {
    if (empty($instName) || empty($instAddress) || empty($mobileNo)) {
        echo 'Sorry, some information are missing!';
    } elseif (!preg_match('/^[A-Za-z0-9\s\.,!?\'"-]+$/u', trim($instName))) {
        echo 'Failed, name contains non-English characters';
    } elseif (!preg_match('/^\d{5,11}$/', trim($mobileNo))) {
        echo 'Failed, mobile number must be 5-11 digits only';
    } elseif (preg_match('/[^A-Za-z0-9\s\.,\/#\-()\'":;]+/u', $instAddress)) {
        echo 'Failed, Address contains special or invalid characters';
    } else {
        if (Update('InstituteInfo', $param, $cond)) {
            echo 'Successfully updated.';
        } else
            echo 'Failed to update!';
    }
}
