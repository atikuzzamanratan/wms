<?php
error_reporting(1);

require '../../vendor/autoload.php';
include "../../Config/config.php";
include "../../Lib/lib.php";

$app = new Solvers\Dsql\Application();

$cn = ConnectDB();

if (!empty($_POST)) {
    $request = $_REQUEST;
    $DataFromID = xss_clean($_REQUEST["DataFromID"]);
    $DataUserID = xss_clean($_REQUEST["DataUserID"]);
    $DataChkAll = xss_clean($_REQUEST["DataChkAll"]);
    $DataCompanyID = xss_clean($_REQUEST["DataCompanyID"]);
    $LoggedUserName = xss_clean($_REQUEST["LoggedUserName"]);
    $LoggedUserID = xss_clean($_REQUEST["LoggedUserID"]);
    $DataStartDate = xss_clean($_REQUEST["DataStartDate"]);
    $DataEndDate = xss_clean($_REQUEST["DataEndDate"]);

    $col[] = "id";
	$col[] = "id";
	$col[] = "SampleHHNo";
	$col[] = "PSU";
	$col[] = "DivisionName";
	$col[] = "DistrictName";
	$col[] = "userid";
	$col[] = "MobileNumber";
	$col[] = "DataName";
	$col[] = "EntryDate";
	$col[] = "IsApproved";
	$col[] = "DeviceID";
	
	$qry = "SELECT xfr.id, 
					xfr.SampleHHNo, 
					xfr.PSU, 
					ui.UserName, 
					ui.id as userid, 
					ui.FullName, 
					ui.MobileNumber, 
					xfr.DataName, 
					xfr.DeviceID, 
					xfr.EntryDate, 
					xfr.FormGroupId, 
					xfr.IsApproved, 
					xfr.XFormsFilePath, 
					pl.Division_Name, 
					pl.District_Name,
					(
						SELECT mdp.ColumnValue 
						FROM masterdatarecord_Approved mdp 
						WHERE mdp.XFormRecordId = xfr.id 
							AND mdp.FormId = xfr.FormId 
							AND mdp.UserID = xfr.UserID 
							AND mdp.CompanyId = xfr.CompanyId 
							AND mdp.SampleHHNo = xfr.SampleHHNo 
							AND mdp.ColumnName = N'surveyEndDate'
					) AS StartTime,
					(
						SELECT mdp.ColumnValue 
						FROM masterdatarecord_Approved mdp 
						WHERE mdp.XFormRecordId = xfr.id 
							AND mdp.FormId = xfr.FormId 
							AND mdp.UserID = xfr.UserID 
							AND mdp.CompanyId = xfr.CompanyId 
							AND mdp.SampleHHNo = xfr.SampleHHNo 
							AND mdp.ColumnName = N'surveyStartDate'
					) AS EndTime 
			FROM xformrecord xfr 
				JOIN userinfo ui ON xfr.UserID = ui.id 
				JOIN InstituteInfo pl ON pl.UserID = ui.id AND xfr.SampleHHNo = pl.id ";
	if (strpos($LoggedUserName, 'cs') !== false) {
		$qry .= " JOIN assignsupervisor a ON a.UserID = ui.id AND a.SupervisorID = $LoggedUserID ";
	}
	if (strpos($LoggedUserName, 'val') !== false) {
		if (strpos($LoggedUserName, 'cval') === false) {
			$qry .= " JOIN assignsupervisor a ON a.UserID = ui.id AND a.ValidatorID = $LoggedUserID ";
		}
	}
	$qry .= "WHERE xfr.IsApproved = 1  
				AND xfr.IsChecked = 1 
				AND xfr.FormId = $DataFromID 
				AND xfr.CompanyId = $DataCompanyID";

	if (!empty($DataStartDate) && !empty($DataEndDate)) {
		$qry .= " AND (xfr.EntryDate BETWEEN '$DataStartDate' AND '$DataEndDate')";
	}
	if (!empty($DataUserID)) {
		$qry .= " AND xfr.UserID = $DataUserID";
	}
	
	$DivisionCode = xss_clean($_REQUEST['DivisionCode']);
	$DistrictCode = xss_clean($_REQUEST['DistrictCode']);
	$UpazilaCode = xss_clean($_REQUEST['UpazilaCode']);
	$UnionWardCode = xss_clean($_REQUEST['UnionWardCode']);
	$MauzaCode = xss_clean($_REQUEST['MauzaCode']);
	$VillageCode = xss_clean($_REQUEST['VillageCode']);
	
	if (!empty($DivisionCode)) {
		$qry .= " AND ( pl.Division_Code = '" . $DivisionCode . "') ";
	}
	if (!empty($DistrictCode)) {
		$qry .= " AND ( pl.District_Code = '" . $DistrictCode . "') ";
	}
	if (!empty($UpazilaCode)) {
		$qry .= " AND ( pl.Upazila_Code = '" . $UpazilaCode . "') ";
	}
	if (!empty($UnionWardCode)) {
		$qry .= " AND ( pl.Union_Code = '" . $UnionWardCode . "') ";
	}
	if (!empty($MauzaCode)) {
		$qry .= " AND ( pl.Mouza_Code = '" . $MauzaCode . "') ";
	}
	if (!empty($VillageCode)) {
		$qry .= " AND ( pl.Village_Code = '" . $VillageCode . "') ";
	}

    if (!empty($request['search']['value'])) {
        $qry .= " AND (xfr.id like'" . $request['search']['value'] . "%'";
        $qry .= " OR ui.id like'%" . $request['search']['value'] . "%'";
        $qry .= " OR ui.UserName like'%" . $request['search']['value'] . "%'";
        $qry .= " OR ui.FullName like'%" . $request['search']['value'] . "%'";
        $qry .= " OR ui.MobileNumber like'%" . $request['search']['value'] . "%'";
        $qry .= " OR xfr.DataName like'%" . $request['search']['value'] . "%'";
        $qry .= " OR xfr.SampleHHNo like'%" . $request['search']['value'] . "%'";
        $qry .= " OR xfr.DeviceID like'%" . $request['search']['value'] . "%'";
        $qry .= " OR xfr.EntryDate like'%" . $request['search']['value'] . "%'";
		$qry .= " OR pl.Division_Name like'%" . $request['search']['value'] . "%'";
		$qry .= " OR pl.District_Name like'%" . $request['search']['value'] . "%')";
    }

    $rs = db_query($qry, $cn);
    $TotalData = db_num_rows($rs);
    $totalFilter = $TotalData;

    if ($col[$request['order'][0]['column']]=="SampleHHNo") {
		$col[$request['order'][0]['column']] = "CAST(".$col[$request['order'][0]['column']]." AS INT)";
	}

    if ($request['length'] < 0) {
        $qry .= " ORDER BY " . $col[$request['order'][0]['column']] . " " . $request['order'][0]['dir'];
    } else {
        $qry .= " ORDER BY " . $col[$request['order'][0]['column']] . " " . $request['order'][0]['dir'] . " OFFSET " . $request['start'] . " ROWS FETCH NEXT " . $request['length'] . " ROWS ONLY";
    }

    $resQry = $app->getDBConnection()->fetchAll($qry);

    $data = array();

    foreach ($resQry as $row) {
        $RecordID = $row->id;
        $HhNo = $row->SampleHHNo;
        $PSU = $row->PSU;

        $UserMobileNo = $row->MobileNumber;
        $UserMobileNo = whatsAppLink($UserMobileNo);

        $UserID = $row->userid;
        $UserName = $row->UserName;
        $UserFullName = $row->FullName;
        $UserData = "$UserFullName<br>($UserName)<br>$UserMobileNo";

        $DataName = $row->DataName;
        $XFormsFilePath = $row->XFormsFilePath;
        $DeviceID = $row->DeviceID;
        $EntryDate = date_format($row->EntryDate, 'd-m-Y H:i:s');

        $IsApproved = $row->IsApproved;

        $DataStatus = "Checked"; //GetDataStatus($IsApproved);

		$XFormsFilePath = $row->XFormsFilePath;
		
		$DivisionName = $row->DivisionName;
		$DistrictName = $row->DistrictName;

        $Duration = 'N/A';
		
		if ($row->StartTime != NULL) {
			$start = strtotime($row->StartTime);
			$end = strtotime($row->EndTime);
			try {
				$start_date = new DateTime($row->StartTime);
			} catch (Exception $e) {
			}
			try {
				$since_start = $start_date->diff(new DateTime($row->EndTime));
			} catch (Exception $e) {
			}

			$Duration = '';
			if ($since_start->d) {
				$Duration = $since_start->d . ' Days ';
			}elseif ($since_start->h) {
				$Duration = $since_start->h . ' hours ';
			}elseif ($since_start->i) {
				$Duration = $since_start->i . ' minutes ';
			}elseif ($since_start->s) {
				$Duration = $since_start->s . ' seconds ';
			}else {
				$Duration = '0 seconds ';
			}
		}
		
		$SubData = array();

        $actions = "<div style= \"display: flex; flex-wrap: wrap; gap: 5px; align-items: center; justify-content: center;\">
                    <button title=\"$btnTitleView\" type=\"button\" class=\"simple-ajax-modal btn btn-outline-primary\" style=\"width: 100%; display: inline-block;margin: 0 1px;\" data-bs-toggle=\"modal\" data-bs-target=\"#viewDataModal\" 
                    onclick=\"ShowDataDetail('$DataFromID', '$RecordID', '0', '$PSU', '$LoggedUserID', '$UserID', '$XFormsFilePath', 'check')\"><i class=\"fas fa-eye\"></i></button>
                    
                    <button title=\"$btnTitleNotice\" type=\"button\" class=\"btn btn-outline-secondary\" style=\"width: 100%; display: inline-block;margin: 0 1px;\" data-bs-toggle=\"modal\" data-bs-target=\"#sendNoticeModal$RecordID\"><i class=\"fas fa-bell\"></i></button>
                </div>
                <script type=\"text/javascript\">
                    function ShowDataDetail(dataFromID, recordID, isAproved, psu, loggedUserID, agentID, XFormsFilePath, data) {
                            $.ajax({
                                url: 'ViewData/ajax-data/data-detail-view.php',
                                method: 'GET',
                                datatype: 'json',
                                data: {
                                    dataFromID: dataFromID,
                                    id: recordID,
                                    status: isAproved,
                                    psu: psu,
                                    loggedUserID: loggedUserID,
                                    agentID: agentID,
                                    XFormsFilePath: XFormsFilePath,
									data: data
                                },
                                success: function (response) {
                                    //alert(response);
                                    $('#dataViewDiv').html(response);
                                }
                            }); 
                        return false;
                    }
                </script>
                
                <!-- View Data Modal-->
                <div class=\"modal fade bd-example-modal-lg\" id=\"viewDataModal\" tabindex=\"-1\" aria-labelledby=\"editDataModalLabel\" aria-hidden=\"true\">
                  <div class=\"modal-dialog modal-lg\">
                    <div id=\"dataViewDiv\" class=\"modal-content\">
                      
                    </div>
                  </div>
                </div>";

        $actions .= " 
                 <!-- Send Notification Modal-->
                <div class=\"modal fade\" id=\"sendNoticeModal$RecordID\" tabindex=\"-1\" aria-labelledby=\"editDataModalLabel\" aria-hidden=\"true\">
                  <div class=\"modal-dialog\">
                    <div class=\"modal-content\">
                      <div class=\"modal-header\">
                      <h5 class=\"modal-title\" id=\"editDataModalLabel\">Send Message</h5>
                        <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\" aria-label=\"Close\"></button>
                      </div>
                      <div class=\"modal-body\">
                        <form id=\"editForm\" method=\"POST\" action=\"\">
                            <div class=\"form-group\">
                                <label for=\"UserName\">Recipient</label>
                                <input type=\"text\" class=\"form-control\" name=\"UserName\" id=\"UserName$RecordID\" value=\"$UserData\" readonly>
                                <input type=\"hidden\" class=\"form-control\" name=\"Userid\" id=\"Userid$RecordID\" value=\"$UserID\">
                            </div>
                            <div class=\"form-group\">
                                <label for=\"UserPass\">Message<span class=\"required\">*</span></label>
                                <textarea class=\"form-control\" rows=\"3\" id=\"message$RecordID\" data-plugin-textarea-autosize placeholder='write message here' required></textarea>
                            </div>
                            
                            <div class=\"modal-footer\">
                                <button type=\"button\" class=\"btn btn-secondary\" data-bs-dismiss=\"modal\">Close</button>
                                <button type=\"button\" class=\"btn btn-primary\" name=\"Save\" id=\"Save\" value=\"Send\" 
                                onclick= \"
                                var toID = document.getElementById('Userid$RecordID').value;
                                var uMessage = document.getElementById('message$RecordID').value;

                                SendNotification('$LoggedUserID', toID, uMessage, '$DataCompanyID');
                                \">
                                Send Message
                                </button>
                             </div>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>";

        $SubData[] = $actions;
		
		$SubData[] = $RecordID;
        $SubData[] = $HhNo;
		$SubData[] = $DivisionName;
		$SubData[] = $DistrictName;
        $SubData[] = $UserData;

		$SubData[] = $DataName;
        $SubData[] = $EntryDate;
        $SubData[] = $DataStatus;
		$SubData[] = $Duration;
        $SubData[] = $DeviceID;


        $data[] = $SubData;
    }

    $json_data = array(
        "draw" => intval($request['draw']),
        "recordsTotal" => $TotalData,
        "recordsFiltered" => $totalFilter,
        "data" => $data
    );

    echo json_encode($json_data);
}

