<?php

require __DIR__ . '/vendor/autoload.php';

use Mpdf\HTMLParserMode;


function getExcerpt($str, $startPos = 0, $maxLength = 100)
{
    if (strlen($str) > $maxLength) {
        $excerpt = substr($str, $startPos, $maxLength - 3);
        $excerpt .= '...';
    } else {
        $excerpt = $str;
    }

    return $excerpt;
}
function getDetailedAddress($address) {
    $matches = [];
    preg_match('/(.+)\s(\w+)\s(\w{2,2})\s(\d{5,5})/', $address, $matches);
    return $matches;
}


// if (isset($_POST['generate_pdf'])) {

    // Reading data from spreadsheet.
    try{
        $res = [];
        $client = new \Google_Client();

        $client->setApplicationName('Google Sheets and PHP');
    
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS, Google_Service_Drive::DRIVE_FILE]);
    
        $client->setAccessType('offline');
    
        $client->setAuthConfig(__DIR__ . '/credentials.json');
    
        $service = new Google_Service_Sheets($client);
    
        $spreadsheetId = "1_AONbruTz7v03z3pLL8-JlvohtwCMBVdA8SbuyDrShI"; //It is present in your URL
    
        $get_range = "A2:P701";
    
        //Request to get data from spreadsheet.
    
        $response = $service->spreadsheets_values->get($spreadsheetId, $get_range);
    
        $data = $response->getValues();

        foreach ($data as $key => $row) {
            $now = new DateTime();
            ob_start();      
            $row = array_map('trim', $row);  

            list(
                $business_name, 
                $SSN,
                $first_name,
                $last_name,
                $DOB,
                $phone, 
                $email,
                $turnover,
                $bank_statements,
                $street,
                $city,
                $state,
                $zipcode,
                $owner_address,
                $business_start_date,
                $EIN
            ) = $row;
        
            if (empty($business_name) || $business_name == "") break;
            if (empty($name)) $name = $first_name . " " . $last_name;

            $business_address = $street . " " . $city . " " . $state . " " . $zipcode;
            $state_incorporated = $state;

            $detailed_homeaddress = getDetailedAddress($owner_address);
        
            if ( count($detailed_homeaddress) == 5 ) {
                $personal_address = $detailed_homeaddress[1];
                $personal_city = $detailed_homeaddress[2];
                $personal_state = $detailed_homeaddress[3];
                $personal_zipcode = $detailed_homeaddress[4];
            }
            else {
                $personal_address = "";
                $personal_city = "";
                $personal_state = "";
                $personal_zipcode = "";
            }
        
            if (empty($phone )) {
                $phone = '<p style="color:white;">I</p>';
            }
            if (empty($email)) {
                $email = '<p style="color:white;">I</p>';
            }
            $stylesheet = file_get_contents('mpdf.css'); // Get css content
            $todaydate = $now->format('m/d/Y');
            $birthdate = date('m/d/Y', strtotime($DOB));
        
            $html = <<<EOD
            <div class="pdfpage">
                <div style="text-align:center;"><img src="https://nytribecagroup.com/wp-content/uploads/2020/10/pdflogo2.png" /></div>
                <div class="pdfhead">
                    <div class="head1">
                        <p style="font-size: 11px;"><a href="https://nytribecagroup.com/">www.NYTribecaGroup.com</a></p>
                        <p style="font-size: 11px;">Main-866-315-7715</p>
                    </div>
                    <div class="head2">
                        <p style="font-size: 11px;">40 Wall Street, 43rd Floor</p>
                        <p style="font-size: 11px;">New York, N.Y.10005</p>
                    </div>
                    <div class="head3">
                        <p style="font-size: 11px;">info@NYTribecaGroup.com</p>
                        <p style="font-size: 11px;">Fax-866-331-6040</p>
                    </div>
                </div>
                <div class="pdfbody">
                    <table class="mytable" style="width:100%">
                        <tr style="background:#53b0bf;">
                            <td colspan="4" style="font-size:11px;">
                                <center><strong>BUSINESS INFORMATION</strong></center>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><strong>Legal Business Name:</strong><br>$business_name</td>
                            <td colspan="2"><strong>DBA Name:</strong><br>$business_name</td>
                        </tr>
                        <tr>
                            <td style="width:25%;"><strong>Business Phone Number:</strong><br>
                                <p style="color:white;">I</p>
                            </td>
                            <td style="width:25%;"><strong>Business Start Date:</strong><br>$business_start_date</td>
                            <td style="width:25%;"><strong>Nature of Business:</strong><br><p style="color:white;">I</p></td>
                            <td style="width:25%;"><strong>Advance Amount Requested:</strong><br><p style="color:white;">I</p></td>
                        </tr>
                        <tr>
                            <td colspan="4"><strong>Business Address: </strong><br>$business_address</td>
                        </tr>
                        <tr>
                            <td style="width:25%;"><strong>Style of Business:</strong><br><p style="color:white;">I</p></td>
                            <td style="width:25%;"><strong>Tax ID Number:</strong><br>$EIN</td>
                            <td style="width:25%;"><strong>State Incorporated:</strong><br>$state_incorporated </td>
                            <td style="width:25%;"><strong>Reason for Funding:</strong><br>Working Capital</td>
                        </tr>
                    </table>
                    <table class="mytable" style="width:100%; margin-top:6px;">
                        <tr style="background:#53b0bf;">
                            <td colspan="4" style="font-size:11px;">
                                <center><strong>QUALIFYING QUESTIONS</strong></center>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <table style="width: 100%;border:none;" class="myinnertable">
                                    <tr>
                                        <td style="width:20%;border:none;"><strong>Any open Liens or Judgments?</strong><br><p style="color:white;">I</p></td>
                                        <td style="width:28%;border:none;"><strong>Did you ever default on a cash
                                                advance?</strong><br><p style="color:white;">I</p></td>
                                        <td style="width:18%;border:none;"><strong>Ever file for bankruptcy?</strong><br><p style="color:white;">I</p></td>
                                        <td style="width:18%;border:none;"><strong>If yes, What is the
                                                status?</strong><br><p style="color:white;">I</p></td>
                                        <td style="width:16%;border:none;"><strong>Bankruptcy year:</strong><br><p style="color:white;">I</p></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <table class="mytable" style="width:100%; margin-top:6px;border-bottom: 1px solid black;">
                        <tr style="text-align:center;background:#53b0bf;">
                            <td colspan="2" style="font-size:11px;">
                                <center><strong>EQUITY ADVANCE INFO</strong></center>
                            </td>
                            <td colspan="2" style="font-size:11px;">
                                <center><strong>MERCHANT CASH ADVANCE INFO</strong></center>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><strong>Do you or your business own any commercial or residential
                                    property?</strong><br>No</td>
                            <td colspan="2"><strong>Do you currently have a cash advance?</strong><br>No</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="border-bottom:none;width:50%;">
                                <table style="width: 100%;border:none;border-bottom: 1px solid black;" class="myinnertable">
                                    <tr>
                                        <td colspan="3" style="border:none;"><strong>(If yes) Property address</strong><br><p style="color:white;">I</p></td>
                                    </tr>
                                    <tr>
                                        <td style="border-left:none;"><strong>Year
                                                Acquired</strong><br><p style="color:white;">I</p></td>
                                        <td><strong>Purchase
                                                Price</strong><br><p style="color:white;">I</p></td>
                                        <td style="border-right:none;"><strong>Current
                                                Value</strong><br><p style="color:white;">I</p></td>
                                    </tr>
                                    <tr>
                                        <td style="border-left:none;"><strong>Current Loan
                                                Balance</strong><br><p style="color:white;">I</p></td>
                                        <td><strong>Lender</strong><br><p style="color:white;">I</p></td>
                                        <td style="border-right:none;"><strong>Title Holder</strong><br><p style="color:white;">I</p></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="border-left:none;border-right:none;border-top: none;">
                                            <strong>Property address (2)</strong><br><p style="color:white;">I</p></td>
                                    </tr>
                                    <tr>
                                        <td style="border-left:none;"><strong>Year  Acquired</strong><br><p style="color:white;">I</p></td>
                                        <td><strong>Purchase
                                                Price</strong><br><p style="color:white;">I</p></td>
                                        <td style="border-right:none;"><strong>Current
                                                Value</strong><br><p style="color:white;">I</p></td>
                                    </tr>
                                    <tr>
                                        <td style="border-left:none;border-bottom: none;"><strong>Current Loan
                                                Balance</strong><br><p style="color:white;">I</p></td>
                                        <td style="border-bottom: none;">
                                            <strong>Lender</strong><br><p style="color:white;">I</p></td>
                                        <td style="border-right:none;border-bottom: none;"><strong>Title
                                                Holder</strong><br><p style="color:white;">I</p></td>
                                    </tr>
                                </table>
                            </td>
                            <td colspan="2" style="border-bottom:none;">
                                <table style="width: 100%;border:none;" class="myinnertable">
                                    <tr>
                                        <td style="border:none;"><strong>If yes, what is your current balance? </strong><br>
                                            <p style="color:white;">I</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border:none; text-align:center;"><label
                                                style="margin-right:50px;"><strong>Company</strong></label><strong
                                                style="color:white;">This is
                                                testing</strong><label><strong>Balance</strong></label><br>
                                            <p style="color:white;">I</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border:none;border-top:1px solid black;"><strong>1.
                                            </strong><p style="color:white;">I</p></td>
                                    </tr>
                                    <tr>
                                        <td style="border-left:none; border-right: none;"><strong>2.
                                            </strong><p style="color:white;">I</p></td>
                                    </tr>
                                    <tr>
                                        <td style="border-left:none; border-top:none; border-right: none;"><strong>3.
                                            </strong><p style="color:white;">I</p></td>
                                    </tr>
                                    <tr>
                                        <td style="border-left:none; border-top:none; border-right: none;"><strong>4.
                                            </strong><p style="color:white;">I</p></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <table class="mytable" style="width:100%; margin-top:6px;">
                        <tr style="background:#53b0bf;">
                            <td colspan="4" style="font-size:11px;">
                                <center><strong>PERSONAL INFORMATION ON OWNERS, PARTNERS, PROPRIETORS, OR GUARANTOR</strong>
                                </center>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="width:50%;">
                                <table style="width: 100%;border:none;" class="myinnertable">
                                    <tr>
                                        <td colspan="2" style="border-left:none;border-top: none;"><strong>Name</strong><br>$name</td>
                                        <td style="border-right:none;border-top: none;">
                                            <strong>FICO</strong><br><p style="color:white;">I</p></td>
                                    </tr>
                                    <tr>
                                        <td style="border-left:none;width:33%"><strong>% of Ownership</strong><br><p style="color:white;">I</p>
                                        </td>
                                        <td style="width:33%"><strong>Social Security No.</strong><br>$SSN
                                        </td>
                                        <td style="border-right:none;width:33%"><strong>DOB</strong><br>$birthdate</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="border-left:none;border-right:none;border-top: none;">
                                            <strong>Address</strong><br>$personal_address</td>
                                    </tr>
                                    <tr>
                                        <td style="border-left:none;"><strong>City/Country</strong><br>$personal_city</td>
                                        <td><strong>State</strong><br>$personal_state</td>
                                        <td style="border-right:none;"><strong>Zip</strong><br>$personal_zipcode</td>
                                    </tr>
                                    <tr>
                                        <td style="border-left:none;border-bottom: none;"><strong>Phone Number</strong><br>
                                            $phone
                                        </td>
                                        <td colspan="2" style="border-right:none;border-bottom: none;"><strong>Email
                                                Address</strong><br>
                                            $email
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td colspan="2" style="width:50%;">
                                <table style="width: 100%;border:none;" class="myinnertable">
                                    <tr>
                                        <td colspan="2" style="border-left:none;border-top: none;">
                                            <strong>Name</strong><br><p style="color:white;">I</p></td>
                                        <td style="border-right:none;border-top: none;">
                                            <strong>FICO</strong><br><p style="color:white;">I</p></td>
                                    </tr>
                                    <tr>
                                        <td style="border-left:none; width:33%"><strong>% of
                                                Ownership</strong><br><p style="color:white;">I</p></td>
                                        <td style="width:33%"><strong>Social Security
                                                No.</strong><br><p style="color:white;">I</p></td>
                                        <td style="border-right:none; width:33%">
                                            <strong>DOB</strong><br><p style="color:white;">I</p></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="border-left:none;border-right:none;border-top: none;">
                                            <strong>Address</strong><br><p style="color:white;">I</p></td>
                                    </tr>
                                    <tr>
                                        <td style="border-left:none;">
                                            <strong>City/Country</strong><br><p style="color:white;">I</p></td>
                                        <td><strong>State</strong><br><p style="color:white;">I</p></td>
                                        <td style="border-right:none;"><strong>Zip</strong><br><p style="color:white;">I</p></td>
                                    </tr>
                                    <tr>
                                        <td style="border-left:none;border-bottom: none;"><strong>Phone Number</strong><br>
                                            <p style="color:white;">I</p>
                                        </td>
                                        <td colspan="2" style="border-right:none;border-bottom: none;"><strong>Email
                                                Address</strong><br>
                                            <p style="color:white;">I</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <table class="mytable" style="width:100%; margin-top:6px;">
                        <tr style="background:#53b0bf;">
                            <td colspan="4" style="font-size:11px;">
                                <center><strong>AUTHORIZATION</strong></center>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="font-size:9px;">Each of the undersigned, who is either a Principal, Sole
                                Proprietor or Personal Guarantor of the above-named business, recognizes that his or her individual
                                credit history may be a factor in the evaluation of this application of the above named business for
                                funding. Each of the undersigned hereby authorizes NYTG and its assigns and/or affiliate partners of
                                NYTG to obtain his or her credit report (and any updates to his or her credit report) in connection
                                with NYTG consideration of this application and any affiliate partners of NYTG in connection with
                                any subsequent review of the account of the abovenamed business. Each of the undersigned hereby
                                authorizes NYTG to utilize information including but not limited to calls, emails, texts and direct
                                mail for marketing efforts from NYTG.</td>
                        </tr>
                        <tr>
                            <td colspan="2"><strong>Partner, Proprietor or Guarantor</strong><br>
                                <p style="color:white;">I</p><br>
                                <p style="color:white;">I</p><br>
                                <p style="color:white;">I</p>
                            </td>
                            <td colspan="2"><strong>Partner, Proprietor or Guarantor </strong><br>
                                <p style="color:white;">I</p><br>
                                <p style="color:white;">I</p><br>
                                <p style="color:white;">I</p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="font-style: italic;"><strong>DATE</strong><br>
                                <p style="font-style: normal;">$todaydate</p>
                            </td>
                            <td colspan="2" style="font-style: italic;">
                                <strong>DATE</strong><br><p style="color:white;">I</p></td>
                        </tr>
                    </table>
                    <table class="mytable" style="width:100%; margin-top: 6px;">
                        <tr style="background:#53b0bf;">
                            <td colspan="2" style="font-size:11px;">
                                <center><strong>ACCOUNT MANAGERS INFORMATION</strong></center>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 50%;text-align: center; font-size:11px; font-family:'Times New Roman', Times, serif;">
                                <p><strong>Please return this application with your past three months of business bank statements to
                                        your account manager to complete your pre-approval process.</strong></p>
                                <p style="margin-top:20px;"><strong>Thank you for choosing New York Tribeca Group!</p></strong>
                            </td>
                            <td>
                                <table style="width: 100%;border:none;" class="myinnertable">
                                    <tr>
                                        <td style="border-left:none;border-top: none;border-right: 0px;"><strong>Account Manager:
                                            </strong></td>
                                        <td style="border-top: none; border-bottom: none; border-right: none;">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style="border-left:none;border-top: none;border-right: 0px;"><strong>Office Number:
                                            </strong></td>
                                        <td style="border-bottom: none; border-right: none;">(866) 315-7715</td>
                                    </tr>
                                    <tr>
                                        <td style="border-left:none;border-top: none;border-right: 0px;"><strong>Fax Number:
                                            </strong></td>
                                        <td style="border-bottom: none; border-right: none;">(646) 859-2801</td>
                                    </tr>
                                    <tr>
                                        <td style="border:none;"><strong>Email: </strong></td>
                                        <td style="border-bottom: none; border-right: none;">&nbsp;</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            EOD;
       
            $mpdf = new \Mpdf\Mpdf(['utf-8', 'A4-P']); // New PDF object with encoding & page size
            $mpdf->setAutoTopMargin = 'stretch'; // Set pdf top margin to stretch to avoid content overlapping
            $mpdf->setAutoBottomMargin = 'stretch'; // Set pdf bottom margin to stretch to avoid content overlapping
            // PDF header content
            $mpdf->SetHTMLHeader('<div class="pdf-header">
                                        </div>');
            // PDF footer content
            $mpdf->normalLineheight = 0.5;
            $mpdf->SetHTMLFooter('<div class="pdf-footer">
                                    </div>');
        
            $mpdf->WriteHTML($stylesheet, HTMLParserMode::HEADER_CSS); // Writing style to pdf
        
            $mpdf->WriteHTML($html); // Writing html to pdf
        
            // $content = $mpdf->Output();
        
            $mypdfname = $name. " " .  $now->format('Y-m-d His') . '.pdf';
            $mpdf->Output($mypdfname, \Mpdf\Output\Destination::FILE);
            $service = new Google_Service_Drive($client);
            $file = new Google_Service_Drive_DriveFile();
            $file->setName( $mypdfname) ;
            $file->setDescription('A test document');
            $file->setMimeType('application/pdf');
            $file->setParents(['18UMcI6hNP4coi41xntvrCfq1-Pd2VSTY']);
            $createdFile = $service->files->create($file, array(
                'data' => file_get_contents($mypdfname),
                'mimeType' => 'application/pdf',
                'uploadType' => 'multipart'
            ));          
            ob_end_flush();

            $options = array(
                'cluster' => 'mt1',
                'useTLS' => true
            );
            $pusher = new Pusher\Pusher(
                'bbd0bd2d8a4213686105',
                '6f635c112ae367907ab5',
                '1473657',
                $options
            );
            $message['message'] = $createdFile;
            $pusher->trigger('google-channel', 'google-finish-event', $message);


        }


    }catch(Exception $e) {
        echo $e->getMessage();
    }

// }