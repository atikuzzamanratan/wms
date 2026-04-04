<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../vendor/autoload.php';
include "../../Config/config.php";
include "../../Lib/lib.php";

$app = new Solvers\Dsql\Application();
$cn = ConnectDB();

$request = $_REQUEST;
$DataUserID = $request["DataUserID"] ?? '';
$DataChkAll = $request["DataChkAll"] ?? '';

$columns = [
    'id',
    'DIVISION_NAME',
    'DISTRICT_NAME',
    'BSIC_CODE',
    'BSIC_DETAIL',
    'Q4A',
    'ADDRESS',
    'MOBILE_NO',
    'UserID'
];

// Base query
$qry = "SELECT * FROM InstituteInfo";

$where = [];

// Filter by User
if (!empty($DataUserID) && $DataUserID != '0') {
    $where[] = "UserID = " . $app->getDBConnection()->quote($DataUserID);
}

// Global Search
$searchValue = $request['search']['value'] ?? '';
if (!empty($searchValue)) {
    $search = $app->getDBConnection()->quote('%' . $searchValue . '%');
    $where[] = "(DIVISION_NAME LIKE $search 
                 OR DISTRICT_NAME LIKE $search 
                 OR BSIC_CODE LIKE $search 
                 OR Q4A LIKE $search 
                 OR ADDRESS LIKE $search 
                 OR UserID LIKE $search 
                 OR MOBILE_NO LIKE $search)";
}

if (!empty($where)) {
    $qry .= " WHERE " . implode(" AND ", $where);
}

// Get Total Records (without LIMIT)
$totalQry = "SELECT COUNT(*) as total FROM InstituteInfo";
if (!empty($where)) {
    $totalQry .= " WHERE " . implode(" AND ", $where);
}
$totalResult = $app->getDBConnection()->query($totalQry)->fetch();
$recordsTotal = $totalResult ? $totalResult->total : 0;

// Get Filtered Records (same as total for now, unless you add more filters)
$recordsFiltered = $recordsTotal;

// Ordering
$orderColumnIndex = $request['order'][0]['column'] ?? 0;
$orderDirection = strtoupper($request['order'][0]['dir'] ?? 'ASC');

// Map column index to actual column name (skip "Actions" column)
$orderableColumns = $columns; // index 0 in DataTable = Actions, so real columns start from index 1
$orderByColumn = $columns[$orderColumnIndex - 1] ?? 'id'; // -1 because first column is Actions

$qry .= " ORDER BY " . $orderByColumn . " " . ($orderDirection === 'DESC' ? 'DESC' : 'ASC');

// Pagination
$start = (int)($request['start'] ?? 0);
$length = (int)($request['length'] ?? 10);

if ($length > 0) {
    $qry .= " OFFSET " . $start . " ROWS FETCH NEXT " . $length . " ROWS ONLY";
}

// Execute final query
$resQry = $app->getDBConnection()->query($qry);

$data = [];

foreach ($resQry as $row) {

    $id = $row->id;
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

    $actions = "<div style= \"display: flex; align-items: center; justify-content: center;\">
                   <button title=\"$btnViewDetailTitle\" type=\"button\" class=\"simple-ajax-modal btn btn-outline-primary\" style=\"display: inline-block;margin: 0 1px;\" data-bs-toggle=\"modal\" data-bs-target=\"#viewDataModal$id\"><i class=\"fas fa-eye\"></i></button>
                </div>
                               
                <!-- View Data Modal-->
                <div class='modal fade' id='viewDataModal$id' tabindex='-1' aria-labelledby='editDataModalLabel' aria-hidden='true'>
                  <div class='modal-dialog'>
                    <div class='modal-content'>
                      <div class='modal-header'>
                        <h5 class='modal-title' id='editDataModalLabel'>Institute Detail</h5>
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
                            <input type='text' class='form-control bg-light' value=\"$DIVISION_NAME ($DIVISION_CODE)\" readonly>
                          </div>
                          
                          <div class='form-group'>
                            <label>District</label>
                            <input type='text' class='form-control bg-light' value=\"$DISTRICT_NAME ($DISTRICT_CODE)\" readonly>
                          </div>
                          
                          <div class='form-group'>
                            <label>City Corporation</label>
                            <input type='text' class='form-control bg-light' value=\"$CITY_CORPORATION_NAME ($CITY_CORPORATION_CODE)\" readonly>
                          </div>
                          
                          <div class='form-group'>
                            <label>Upazila</label>
                            <input type='text' class='form-control bg-light' value=\"$UPAZILA_NAME ($UPAZILA_CODE)\" readonly>
                          </div>
                          
                          <div class='form-group'>
                            <label>Municipal</label>
                            <input type='text' class='form-control bg-light' value=\"$MUNICIPAL_NAME ($MUNICIPAL_CODE)\" readonly>
                          </div>
                          
                          <div class='form-group'>
                            <label>Union</label>
                            <input type='text' class='form-control bg-light' value=\"$UNION_NAME ($UNION_CODE)\" readonly>
                          </div>
                          
                          <div class='form-group'>
                            <label>Mouza</label>
                            <input type='text' class='form-control bg-light' value=\"$MOUZA_NAME ($MOUZA_CODE)\" readonly>
                          </div>
                          
                          <div class='form-group'>
                            <label>Village</label>
                            <input type='text' class='form-control bg-light' value=\"$VILLAGE_NAME ($VILLAGE_CODE)\" readonly>
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
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>";

    $UserName = getValue('userinfo', 'UserName', "id = $UserID");
    $UserFullName = getValue('userinfo', 'FullName', "id = $UserID");
    $UserData = "$UserFullName ($UserName/$UserID)";

    $subData = [
        $actions,
        $id,
        $DIVISION_NAME,
        $DISTRICT_NAME,
        $BSIC_CODE,
        $BSIC_DETAIL,
        $Q4A,
        $ADDRESS,
        $MOBILE_NO,
        $UserData
    ];

    $data[] = $subData;
}

// Response for DataTables
$json_data = [
    "draw"            => intval($request['draw'] ?? 0),
    "recordsTotal"    => (int)$recordsTotal,
    "recordsFiltered" => (int)$recordsFiltered,
    "data"            => $data
];

echo json_encode($json_data);