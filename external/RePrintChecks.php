<?php
@session_start();
ob_start();
require_once '../libs/mpdf/mpdf.php';
require_once "init.php";
$_SESSION['iamcron'] = true; // Says I am cron for Full Access

$InvoiceData = GetInvoiceData($daffny);

// create folder if not exists
if (!file_exists(ROOT_PATH."uploads/Invoices/")) {
    mkdir(ROOT_PATH."uploads/Invoices/", 0777, true);
}

// create file
$fileName = "Check-Recipts-v2-".date('Y-m-d his').".html";
$fullPath = ROOT_PATH."uploads/Invoices/Checks/".$fileName;

$content = '';
ob_start();

for($i=0;$i<count($InvoiceData);$i++){
    //$mpdf->AddPage();
    $accountID = $InvoiceData[$i]['AccountID'];
    
    $content .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">';
    $content .= '<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Untitled Document</title>
        <style>
        *{
            box-sizing:border-box;
        }
        .container {
            width: 670px;
            margin: auto;
            padding: 3px;
        }
        .row {
            clear: left;
            width: 100%;
            display:inline-block;
        }
        input {
            border: none;
            height: 20px;
            width: 100%;
        }
        </style>
    </head>';

    $content .= '<body onload="window.print()">';
    $content .= '<div class="container">';

    // outer content
    $content .= '<div class="row" style="margin-top:70px;">
        <div style="float:right; padding-right:2px;">'.date('m/d/Y').'</div>
    </div>';

    // inner loop for orders
    foreach($InvoiceData[$i]['EntityData'] as $key => $value){
        $InvoiceID = $value['InvoiceID'];
        $EntityID = $value['EntityID'];

        $sql = "SELECT * FROM app_entities WHERE `id` = ".$EntityID;
        $respo = $daffny->DB->query($sql);
        $entityD = mysqli_fetch_assoc($respo);
        
        $sql = "SELECT * FROM app_accounts WHERE `id` = ".$entityD['carrier_id'];
        $respo = $daffny->DB->query($sql);
        $carrierD = mysqli_fetch_assoc($respo);

        $Amount = $daffny->DB->query("SELECT Amount FROM Invoices WHERE ID = ".$InvoiceID); 
        $Amount = mysqli_fetch_assoc($Amount)['Amount'];

        if ($carrierD['print_check'] == 1) {
            $printName = ucfirst($carrierD['print_name']);
            $company_name = $printName;
            $address1 = $carrierD['print_address1'];
            $address2 = $carrierD['print_address2'];
            $city = $carrierD['print_city'];
            $state = $carrierD['print_state'];
            $zip_code = $carrierD['print_zip_code'];
        } else {
            $printName = ucfirst($carrierD['company_name']);
            $company_name = $printName;
            $address1 = $carrierD['address1'];
            $address2 = $carrierD['address2'];
            $city = $carrierD['city'];
            $state = $carrierD['state'];
            $zip_code = $carrierD['zip_code'];
        }

        $sql = "SELECT count(*) as `Number` FROM app_payments WHERE `entity_id` = ".$EntityID;
        $PaymentNumber = $daffny->DB->query($sql);
        $PaymentNumber = mysqli_fetch_assoc($PaymentNumber)['Number'];

        $sql = "INSERT INTO app_payments (entity_id,number,date_received,fromid,toid,amount,method,entered_by)";
        $sql .= "VALUES( '".$EntityID."', '".($PaymentNumber+1)."', '".date('Y-m-d')."', '1', '3', '".$Amount."','2','".$_SESSION['member']['id']."' )";

        $res = $daffny->DB->query($sql);

        $content .= '<div class="row" style="margin-top:25px;">
            <div style="float:left; padding-left:35px;width:530px;">
                '.$company_name.'
            </div>
            <div style="float:right;padding-right:30px;">
            &nbsp;&nbsp;&nbsp;$'.number_format((float) $Amount, 2, ".", ",").'
            </div>
        </div>';

        $obj = new toWords(number_format((float) $Amount, 2, ".", ""), 'dollars', 'c');
        $content .= '<div class="row" style="margin-top:10px;">
            <div style="float:left;width:475px; padding-left:10px;">
            '.ucwords($obj->words).'
            </div>
        </div>';

        $content .= '<div class="row" style="margin-top:20px;">
            <div style="float:left; padding-left:50px;width:310px;">
            '.$company_name.'
            </div>
        </div>';

        $content .= '<div class="row" style="">
            <div style="float:left; padding-left:50px;width:310px;">
            '.$address1.' '.$address2.'
            </div>
        </div>';

        $content .= '<div class="row" style="">
            <div style="float:left; padding-left:50px;width:310px;">
            '.$city.', '.$state.' '.$zip_code.'
            </div>
        </div>';

        $content .= '<div class="row" style="margin-top:10px;">
            <div style="float:left; padding-left:30px;width:260px;">
            Dispatch ID '.$entityD['number'].'
            </div>
        </div>';

        $content .= '<div class="row" style="margin-top:100px;">
            <div style="float:left; padding-left:5px;width:380px;">
            '.$company_name.'
            </div>
            <div style="float:right; padding-right:5px;width:120px;">
            '.date('m/d/Y').'
            </div>
        </div>';

        $content .= '<div class="row" style="margin-top:10px;">
            <table width="100%" cellpadding="1" cellspacing="1">
            <tr>
                <td width="33%">Date</td>
                <td  width="33%" align="center">Reference</td>
                <td  width="34%"  align="right">Payment</td>
            </tr>
            <tr>
                <td>'.date('m/d/Y').'</td>
                <td align="center">#'.$entityD['number'].'</td>
                <td  align="right">'.number_format((float) $Amount, 2, ".", ",").'</td>
            </tr>
            <tr><td colspan="7">&nbsp;</td></tr>
            </table>
        </div>';

        $content .= '<div class="row" style="margin-top:100px;"></div>';

        $content .= '<div class="row" style="margin-top:155px;">
            <div style="float:left; padding-left:5px;width:295px;">
            '.$company_name.'
            </div>
            <div style="float:right; padding-right:5px;width:120px;">
            '.date('m/d/Y').'
            </div>
        </div>';

        $content .= '<div class="row" style="margin-top:10px;">
            <table width="100%" cellpadding="1" cellspacing="1">
            <tr>
                <td  width="33%">Date</td>
                <td  width="33%" align="center">Reference</td>
        
                <td  width="34%"  align="right">Payment</td>
            </tr>
            <tr>
                <td>'.date('m/d/Y').'</td>
        
                <td align="center">#'.$entityD['number'].'</td>
        
                <td align="right">'.number_format((float) $Amount, 2, ".", ",").'</td>
            </tr>
            <tr><td colspan="7">&nbsp;</td></tr>
            </table>
        </div>';

        $content .= '<div class="row" style="margin-top:110px;"></div>';
    }
    $content .= '</div>';
    $content .= '</body>';
    
}

file_put_contents($fullPath,$content);
$out = array('URL' => $fileName);

$InvoiceIds = implode(",",$_POST['selectedInvoices']);
$sql = "UPDATE Invoices SET Deleted = 1 WHERE `ID` IN (".$InvoiceIds.") AND Hold =  0";
$daffny->DB->query($sql);

echo json_encode($out);
die;
?>

<?php
function GetInvoiceData($daffny){
    $InvoiceIds = $_POST['selectedInvoices'];
    $sql = "SELECT AccountID,EntityID,ID FROM Invoices WHERE `ID` IN (".$InvoiceIds.") AND Hold =  0";
    $res = $daffny->DB->query($sql);

    $invoiceData = array();
    while($r = mysqli_fetch_assoc($res)){
        $invoiceData[] = $r;
    }

    $dataArray = array();
    $accountIds = array();
    for($i=0;$i<count($invoiceData);$i++){
        $accountIds[]  =  $invoiceData[$i]['AccountID'];
    }

    $accountIds  = array_unique($accountIds);
    $accountIdsSorted = array();

    foreach($accountIds as $key => $value){
        $accountIdsSorted[] = $value;
    }

    $dataArray= array();
    $j = 0;

    for($i=0; $i<count($accountIdsSorted);$i++){
        $dataArray[$i]['AccountID'] = $accountIdsSorted[$i];

        for($j=0;$j<count($invoiceData);$j++){
            if($invoiceData[$j]['AccountID'] == $accountIdsSorted[$i]){
                $dataArray[$i]['EntityData'][$j] = array(
                    'InvoiceID' => $invoiceData[$j]['ID'],
                    'EntityID' => $invoiceData[$j]['EntityID'],
                );
            }
        }
    }

    return $dataArray;
}

define("MAJOR", 'pounds');
define("MINOR", 'p');
class toWords
{
    public $pounds;
    public $pence;
    public $major;
    public $minor;
    public $words = '';
    public $number;
    public $magind;
    public $units = array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine');
    public $teens = array('ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen');
    public $tens = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety');
    public $mag = array('', 'thousand', 'million', 'billion', 'trillion');
    
    public function toWords($amount, $major = MAJOR, $minor = MINOR)
    {
        $this->major = $major;
        $this->minor = $minor;
        $this->number = number_format($amount, 2);
        list($this->pounds, $this->pence) = explode('.', $this->number);
        $this->words = " $this->major $this->pence $this->minor";
        if ($this->pounds == 0) {
            $this->words = "Zero $this->words";
        } else {
            $groups = explode(',', $this->pounds);
            $groups = array_reverse($groups);
            for ($this->magind = 0; $this->magind < count($groups); $this->magind++) {
                if (($this->magind == 1) && (strpos($this->words, 'hundred') === false) && ($groups[0] != '000')) {
                    $this->words = ' and ' . $this->words;
                }

                $this->words = $this->_build($groups[$this->magind]) . $this->words;
            }
        }
    }
    
    public function _build($n)
    {
        $res = '';
        $na = str_pad("$n", 3, "0", STR_PAD_LEFT);
        if ($na == '000') {
            return '';
        }

        if ($na{0} != 0) {
            $res = ' ' . $this->units[$na{0}] . ' hundred';
        }

        if (($na{1} == '0') && ($na{2} == '0')) {
            return $res . ' ' . $this->mag[$this->magind];
        }

        $res .= $res == '' ? '' : ' and';
        $t = (int) $na{1};
        $u = (int) $na{2};
        switch ($t) {
            case 0:$res .= ' ' . $this->units[$u];
                break;
            case 1:$res .= ' ' . $this->teens[$u];
                break;
            default:$res .= ' ' . $this->tens[$t] . ' ' . $this->units[$u];
                break;
        }
        $res .= ' ' . $this->mag[$this->magind];
        return $res;
    }
}
?>