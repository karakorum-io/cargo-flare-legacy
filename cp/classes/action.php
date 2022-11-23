<?php

/* * *************************************************************************************************
 * Action CP Class                                                                 				   *
 *                                                                              					   *
 *                                                                                                  *
 * Client: 	FreightDragon                                                                          *
 * Version: 	1.0                                                                                    *
 * Date:    	2011-09-29                                                                             *
 * Author:  	C.A.W., Inc. dba INTECHCENTER                                                          *
 * Address: 	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076                             *
 * E-mail:	techsupport@intechcenter.com                                                           *
 * CopyRight 2011 FreightDragon. - All Rights Reserved                                              *
 * ************************************************************************************************** */
#require_once ROOT_PATH . 'libs/excel/PHPExcel.php';
#require_once ROOT_PATH . 'libs/excel/PHPExcel/Writer/Excel2007.php';

class CpAction extends AppAction {

    public static $actionLinks = null;

    /**
     * put your comment there...
     *
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * put your comment there...
     *
     */
    public function construct() {
        $cont = "";

        if ($this->tplname != "") {
            $this->showMessage();

            $cont .= $this->daffny->tpl->build($this->tplname, $this->input);
        } else if (!is_array($this->input)) {
            $cont .= $this->input;
        }

        if ($this->title != "") {
            $arr_cont = array(
                'title' => $this->title
                , 'content' => $cont
            );

            $this->out .= $this->daffny->tpl->build("action", $arr_cont);
        } else {
            $this->out .= $cont;
        }

        return $this->out;
    }

    /**
     * put your comment there...
     *
     */
    protected function applyGridAction() {
        $this->input['grid_action'] = $this->daffny->tpl->build("grid_action");
        return $this->input['grid_action'];
    }

    private function __getFilePostfix() {
        return "_" . date('Ymd');
    }

    public function exportall() {
        $files = array("feedback", "contactus", "members", "subscribers");

        $tmpFolder = ROOT_PATH . "uploads/temp/" . md5(uniqid());
        $tmpZip = tempnam($tmpFolder, "archive");
        mkdir($tmpFolder);

        $zip = new ZipArchive();
        $zip->open($tmpZip, ZipArchive::OVERWRITE);
        foreach ($files as $file) {
            $excelFile = $this->_exportOne($file, false, $tmpFolder);
            $zip->addFile($excelFile, basename($excelFile));
        }
        $zip->close();

        header('Content-Type: application/zip');
        header('Content-Length: ' . filesize($tmpZip));
        header('Content-Disposition: attachment; filename="forms' . $this->__getFilePostfix() . '.zip"');
        readfile($tmpZip);
        $this->__rmdir($tmpFolder);
        exit();
    }

    protected function _exportOne($fileName, $download = true, $savePath = null) {
        switch ($fileName) {
            case "subscribers" :
                $where = "";
                if (isset($_SESSION['category_id']) && $_SESSION['category_id'] != "") {
                    $where = " WHERE f.category_id = '" . $_SESSION['category_id'] . "' ";
                }
                $sql = "SELECT f.*
        			 , DATE_FORMAT(reg_date, '%m/%d/%Y') AS reg_date
        			 , CONCAT_WS(' ', f.first_name, f.last_name) AS sname
                     , c.name AS catname
                     , (CASE WHEN f.unsubscribed = '1' THEN 'Yes' ELSE 'No' END) AS unsubscribed
                  FROM subscribers f
                       LEFT JOIN subscribers_categories c ON f.category_id = c.id"
                        . $where
                        . " ORDER BY id DESC";
                $fields = array(
                    'first_name' => "First Name",
                    'last_name' => "Last Name",
                    'email' => "E-mail",
                    'address' => "Street Address",
                    'city' => "City",
                    'state' => "State",
                    'zip' => "Zip",
                    'phone' => "Telephone",
                    'country' => "Country",
                    'reg_date' => "Date",
                    'unsubscribed' => "Unsubscribed"
                );
                break;



            case 'feedback':
                $sql = "SELECT *
							 , DATE_FORMAT(reg_date, '%m/%d/%Y') AS reg_date
						  FROM feedback
							   " . ch(get_var("f_state") != "", "WHERE state = '" . get_var("f_state") . "'") . "
						 ORDER BY id DESC";
                $fields = array(
                    'your_name' => "Name",
                    'email' => "E-Mail",
                    'company_name' => "Company",
                    'address' => "Street Address",
                    'city' => "City",
                    'state' => "State",
                    'zip' => "Zip",
                    'phone' => "Phone",
                    'comments' => "Comments",
                    'reg_date' => "Date"
                );
                break;

            case 'contact_us':
                $sql = "SELECT cu.*
							 , DATE_FORMAT(cu.reg_date, '%m/%d/%Y') AS reg_date
							 , c.name AS country
							 , IFNULL(cu.state, cu.state_other) AS state
						  FROM contact_us cu
							   LEFT JOIN states s ON s.code = cu.state
							   LEFT JOIN countries c ON c.code = cu.country
							   " . ch(get_var("f_state") != "", "WHERE cu.state = '" . get_var("f_state") . "'") . "
						 ORDER BY cu.id DESC";
                $fields = array(
                    'first' => "First Name",
                    'last' => "Last Name",
                    'email' => "E-mail",
                    'street_address' => "Street Address",
                    'city' => "City",
                    'state' => "State",
                    'zip' => "Zip",
                    'country' => "Country",
                    'phone_d' => "Phone (day)",
                    'phone_e' => "Phone (eve)",
                    'message' => "Case Name",
                    'message1' => "Name of the Corporation",
                    'message2' => "Comments",
                    'reg_date' => "Date"
                );
                break;

            default:
                die("Unknown type: " . $fileName);
        }

        $PHPExcel = new PHPExcel();
        $PHPExcel->getProperties()->setCreator($this->daffny->cfg['site_title']);
        $PHPExcel->getProperties()->setLastModifiedBy($this->daffny->cfg['site_title']);
        $PHPExcel->setActiveSheetIndex(0);

        $i = 65; // A
        foreach ($fields as $fieldName) {
            $PHPExcel->getActiveSheet()->SetCellValue(chr($i) . "1", $fieldName);
            $i++;
        }

        foreach ($this->daffny->DB->selectRows($sql) as $i => $row) {
            $j = 65;
            foreach ($fields as $fieldCode => $fieldName) {
                $PHPExcel->getActiveSheet()->SetCellValue(chr($j) . ($i + 2), str_replace("\n", "\r\n", $row[$fieldCode]));
                $j++;
            }
        }

        $Writer = new PHPExcel_Writer_Excel5($PHPExcel);

        $fName = $savePath . '/' . $fileName . $this->__getFilePostfix() . '.xls';
        if ($download) {
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . basename($fName) . '"');
            header('Cache-Control: max-age=0');
            $Writer->save('php://output');
            exit();
        } else {
            $Writer->save($fName);
            return $fName;
        }
    }

    protected function getCategoriesSub($empty = "") {
        $categories = array();
        if ($empty != "") {
            $categories[''] = $empty;
        }

        $rows = $this->daffny->DB->selectRows("id, name", "subscribers_categories", "ORDER BY name");
        foreach ($rows as $category) {
            $categories[$category['id']] = $category['name'];
        }

        return $categories;
    }

    protected function getTemplatesSub($empty) {
        $templates = array();
        if ($empty != "") {
            $templates[''] = $empty;
        }
        $this->daffny->DB->select("id, title", "subscribers_newsletters", "ORDER BY title");
        while ($row = $this->daffny->DB->fetch_row()) {
            $templates[$row['id']] = $row['title'];
        }
        return $templates;
    }

}

?>