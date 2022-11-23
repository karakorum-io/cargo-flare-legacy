<?php

/**
 * Helper class for Excel Export
 * Client: FreightDragon
 * Address 11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * Email techsupport@intechcenter.com
 * Date: 2012-02-08
 * @name Excel Export
 * @version 1.0
 * @author C.A.W., Inc. dba INTECHCENTER
 * @copyright 2012 FreightDragon. - All Rights Reserved
 * 
 */
require_once(ROOT_PATH . "libs/excel/PHPExcel.php");
require_once(ROOT_PATH . "libs/excel/PHPExcel/Writer/Excel5.php");

class ExportExcel {

    private static $lineFont = array(
        'borders' => array(
            'outline' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('argb' => '000000'),
            )
        )
    );
    private static $titleFont = array(
        'font' => array(
            'name' => 'Arial Cyr',
            'size' => '16',
            'bold' => true,
            'color' => array('rgb' => '3E8AB0'),
        )
    );
    private static $headFont = array(
        'font' => array(
            'name' => 'Arial Cyr',
            'size' => '10',
            'italic' => true,
            'color' => array('rgb' => 'ffffff'),
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => '2996cc'),
        ),
        'borders' => array(
            'outline' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('argb' => '000000'),
            )
        )
    );
    private static $totalFont = array(
        'font' => array(
            'name' => 'Arial Cyr',
            'size' => '10',
            'bold' => true
        ),
        'borders' => array(
            'outline' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('argb' => '000000'),
            )
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'e0e0e0'),
        )
    );
    private static $smallFont = array(
        'font' => array('size' => 9)
    );
    
    
    /**
     * Print Logo
     * 
     * @param object $sheet - Active Sheet
     */
    final private function printLogo($sheet) {
        $iDrowing = new PHPExcel_Worksheet_Drawing();
        $iDrowing->setPath(ROOT_PATH . 'images/logo_excel.png');
        $iDrowing->setCoordinates('K2');
        $iDrowing->setOffsetY(-10);
        $iDrowing->setOffsetX(-20);
        $iDrowing->setWorksheet($sheet);
    }

    /**
     * Output Excel file
     * @param object $excl - Excel O
     * @param String $name - Report name
     */
    final private function outputExcel($excl, $name) {
        $objWriter = new PHPExcel_Writer_Excel5($excl);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $name . '.' . 'xls"');
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
        $excl->disconnectWorksheets();
        unset($excl);
        unset($objWriter);
        exit();
    }

}

?>