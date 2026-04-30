<?php
/*error_reporting(E_ALL);
error_reporting(1);
error_reporting(0);
ini_set('display_errors', 0);*/

require '../../vendor/autoload.php';
include "../../Config/config.php";
include "../../Lib/lib.php";

$app = new Solvers\Dsql\Application();

if ($_REQUEST['ui'] != '') {
    $UserID = $app->cleanInput($_REQUEST['ui']);
}

$qry = "SELECT * FROM InstituteInfo WHERE UserID = $UserID";
$resQry = $app->getDBConnection()->fetchAll($qry);

$data = [];

foreach ($resQry as $row) {
    $id = $row->id;

    $isCollected = "";
    $hasInXformRecord = getValue('xformrecord', 'COUNT(*)', "SampleHHNo = $id");
    if($hasInXformRecord > 0){
        $isCollected = "<b style='color: green'>Collected</b>";
    }else{
        $isCollected = "<b style='color: red'>Not Collected</b>";
    }

    $DIVISION_CODE = $row->DIVISION_CODE;
    $DIVISION_NAME = $row->DIVISION_NAME;
    $DISTRICT_CODE = $row->DISTRICT_CODE;
    $DISTRICT_NAME = $row->DISTRICT_NAME;
    $CITY_CORPORATION_CODE = $row->CITY_CORPORATION_CODE;
    $CITY_CORPORATION_NAME = $row->CITY_CORPORATION_NAME;
    $UPAZILA_CODE = $row->UPAZILA_CODE;
    $UPAZILA_NAME = $row->UPAZILA_NAME;
    $MUNICIPAL_CODE = $row->MUNICIPAL_CODE;
    $MUNICIPAL_NAME = $row->MUNICIPAL_NAME;
    $UNION_CODE = $row->UNION_CODE;
    $UNION_NAME = $row->UNION_NAME;
    $MOUZA_CODE = $row->MOUZA_CODE;
    $MOUZA_NAME = $row->MOUZA_NAME;
    $VILLAGE_CODE = $row->VILLAGE_CODE;
    $VILLAGE_NAME = $row->VILLAGE_NAME;
    $RMC = $row->RMC;
    $Q2B = $row->Q2B;
    $SECTION = $row->SECTION;
    $BSIC_CODE = $row->BSIC_CODE;
    $BSIC_DETAIL = $row->BSIC_DETAIL;
    $Q4A = $row->Q4A;
    $ADDRESS = $row->ADDRESS;
    $MOBILE_NO = $row->MOBILE_NO;
    $Q7AT = $row->Q7AT;
    $UNIT_CAT = $row->UNIT_CAT;
    $UserID = $row->UserID;
    $STATUS = $row->STATUS;

    $actions = $actions = "<div style= \"display: flex; align-items: center; justify-content: center;\">
                               
                                    <button title=\"$btnViewDetailTitle\" type=\"button\" class=\"simple-ajax-modal btn btn-outline-primary\" style=\"display: inline-block;margin: 0 1px;\" data-bs-toggle=\"modal\" data-bs-target=\"#viewDataModal$id\"><i class=\"fas fa-eye\"></i></button>
                            </div>         
                            <!-- View Data Modal-->
                            <div class='modal fade' id='viewDataModal$id' tabindex='-1' aria-labelledby='viewDataModalLabel' aria-hidden='true'>
                              <div class='modal-dialog'>
                                <div class='modal-content'>
                                  <div class='modal-header'>
                                    <h5 class='modal-title' id='viewDataModalLabel'>View Institute Detail</h5>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                  </div>
                                  <div class='modal-body'>
                                    <form>
                                      <div class='form-group'>
                                        <label>ID</label>
                                        <input type='text' class='form-control bg-light' value='$id' readonly>
                                      </div>
                                      
                                      <div class='form-group'>
                                        <label>Division</label>
                                        <input type='text' class='form-control bg-light' value='$DIVISION_NAME ($DIVISION_CODE)' readonly>
                                      </div>
                                      
                                      <div class='form-group'>
                                        <label>District</label>
                                        <input type='text' class='form-control bg-light' value='$DISTRICT_NAME ($DISTRICT_CODE)' readonly>
                                      </div>
                                      
                                      <div class='form-group'>
                                        <label>City Corporation</label>
                                        <input type='text' class='form-control bg-light' value='$CITY_CORPORATION_NAME ($CITY_CORPORATION_CODE)' readonly>
                                      </div>
                                      
                                      <div class='form-group'>
                                        <label>Upazila</label>
                                        <input type='text' class='form-control bg-light' value='$UPAZILA_NAME ($UPAZILA_CODE)' readonly>
                                      </div>
                                      
                                      <div class='form-group'>
                                        <label>Municipal</label>
                                        <input type='text' class='form-control bg-light' value='$MUNICIPAL_NAME ($MUNICIPAL_CODE)' readonly>
                                      </div>
                                      
                                      <div class='form-group'>
                                        <label>Union</label>
                                        <input type='text' class='form-control bg-light' value='$UNION_NAME ($UNION_CODE)' readonly>
                                      </div>
                                      
                                      <div class='form-group'>
                                        <label>Mouza</label>
                                        <input type='text' class='form-control bg-light' value='$MOUZA_NAME ($MOUZA_CODE)' readonly>
                                      </div>
                                      
                                      <div class='form-group'>
                                        <label>Village</label>
                                        <input type='text' class='form-control bg-light' value='$VILLAGE_NAME ($VILLAGE_CODE)' readonly>
                                      </div>
                                      
                                      <div class='form-group'>
                                        <label>RMC</label>
                                        <input type='text' class='form-control bg-light' value='$RMC' readonly>
                                      </div>
                                      
                                      <div class='form-group'>
                                        <label>Q2B</label>
                                        <input type='text' class='form-control bg-light' value='$Q2B' readonly>
                                      </div>
                                      
                                      <div class='form-group'>
                                        <label>SECTION</label>
                                        <input type='text' class='form-control bg-light' value='$SECTION' readonly>
                                      </div>
                                      
                                      <div class='form-group'>
                                        <label>BSIC Code</label>
                                        <input type='text' class='form-control bg-light' value='$BSIC_CODE' readonly>
                                      </div>
                                      
                                      <div class='form-group'>
                                        <label>BSIC Detail</label>
                                        <input type='text' class='form-control bg-light' value='$BSIC_DETAIL' readonly>
                                      </div>
                                      
                                      <div class='form-group'>
                                        <label>Institute Name (Q4A)</label>
                                        <input type='text' class='form-control bg-light' value='$Q4A' readonly>
                                      </div>
                                      
                                      <div class='form-group'>
                                        <label>Address</label>
                                        <input type='text' class='form-control bg-light' value='$ADDRESS' readonly>
                                      </div>
                                      
                                      <div class='form-group'>
                                        <label>Mobile No</label>
                                        <input type='text' class='form-control bg-light' value='$MOBILE_NO' readonly>
                                      </div>
                                      
                                      <div class='form-group'>
                                        <label>Q7AT</label>
                                        <input type='text' class='form-control bg-light' value='$Q7AT' readonly>
                                      </div>
                                      
                                      <div class='form-group'>
                                        <label>Unit Cat</label>
                                        <input type='text' class='form-control bg-light' value='$UNIT_CAT' readonly>
                                      </div>
                                     
                                      <div class='modal-footer'>
                                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                                        <button type=\"button\" class=\"btn btn-primary\" name=\"Save\" id=\"Save\" value=\"Update\" data-bs-dismiss=\"modal\" 
                                        onClick='NewWindow(\"institute-info-edit.php?instID=$id&userID=$UserID\", \"name\", \"600\", \"600\", \"Yes\"); return false;'>
                                        Edit
                                        </button>
                                      </div>
                                    </form>
                                  </div>
                                </div>
                              </div>
                            </div>
                            
                            <div class='modal fade' id='editDataModal$id' tabindex='-1' aria-labelledby='editDataModalLabel' aria-hidden='true'>
                                <div class='modal-dialog'>
                                    <div class='modal-content'>
                                      <div class='modal-header'>
                                        <h5 class='modal-title' id='viewDataModalLabel'>Update Institute Detail</h5>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                      </div>
                                        <div class='modal-body'>
                                        <form name='frmInstEdit' id='frmInstEdit'>
                                            <div class='form-group'>
                                                <label>ID</label>
                                                <input id='instID$id' type='text' class='form-control bg-light' value='$id' readonly>
                                            </div>
                                            <div class='form-group'>
                                                <label>Division</label>
                                                <input type='text' class='form-control bg-light' value='$DIVISION_NAME ($DIVISION_CODE)' readonly>
                                            </div>
                                            <div class='form-group'>
                                                <label>District</label>
                                                <input type='text' class='form-control bg-light' value='$DISTRICT_NAME ($DISTRICT_CODE)' readonly>
                                            </div>
                                            
                                            <div class='form-group'>
                                                <label style='color:red'>Institute Name (Q4A)</label>
                                                <input id='instName$id' type='text' class='form-control bg-light' value='$Q4A' required>
                                            </div>
                                              
                                            <div class='form-group'>
                                                <label style='color:red'>Address</label>
                                                <input id='instAddress$id' type='text' class='form-control bg-light' value='$ADDRESS' required>
                                            </div>
                                              
                                            <div class='form-group'>
                                                <label style='color:red'>Mobile No</label>
                                                <input id='instMobileNo$id' type='number' pattern='01[0-9]{9}' title='Number must start with 01 and be exactly 11 digits long' maxlength='11' minlength='11' class='form-control bg-light' value='$MOBILE_NO' required>
                                            </div>
                                            <div class='modal-footer'>
                                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                                            <button type='button' class='btn btn-primary' onclick=\"
                                            const nameInput = document.getElementById('instName$id').value;
                                            const phoneInput = document.getElementById('instMobileNo$id').value;
                                            const addressInput = document.getElementById('instAddress$id').value;
                                            
                                            if(!nameInput || !phoneInput || !addressInput){
                                                alert('All required fields must be filled.');
                                            }else if (phoneInput.length !== 11) {
                                                alert('Mobile number must be exactly 11 digits.');
                                             }
                                             else if (!phoneInput.startsWith('01')) {
                                                alert('Mobile number must be starting with 01.');
                                             }else{
                                             //alert('ok to go');
                                             const editParam = 'userID=$UserID' + '&instID=$id' + '&instName=' + nameInput + '&instAddress=' + addressInput + '&instMobileNo=' + phoneInput
                                             alert(editParam)
                                           
                                              //EditItem('$UserID', '$id', nameInput, addressInput, phoneInput);
                                              }
                                              
                                            \">Save changes</button>
                                          </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            ";

    $SubData = array();
    $SubData[] = $actions;
    $SubData[] = $isCollected;
    $SubData[] = $id;
    $SubData[] = $Q4A;
    $SubData[] = $MOBILE_NO;
    $SubData[] = $ADDRESS;
    $SubData[] = $BSIC_CODE;
    $SubData[] = $BSIC_DETAIL;
    $SubData[] = $DIVISION_NAME;
    $SubData[] = $DISTRICT_NAME;

    $data[] = $SubData;
}

$jsonData = json_encode($data);

echo '{"aaData":' . $jsonData . '}';
