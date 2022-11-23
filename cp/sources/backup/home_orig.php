<?php
/***************************************************************************************************
* Home CP Class                                                                 *
*                                                                              					   *
*                                                                                                  *
* Client: 	FreightDragon                                                                          *
* Version: 	1.0                                                                                    *
* Date:    	2011-09-28                                                                             *
* Author:  	C.A.W., Inc. dba INTECHCENTER                                                          *
* Address: 	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076                             *
* E-mail:	techsupport@intechcenter.com                                                           *
* CopyRight 2011 FreightDragon. - All Rights Reserved                                              *
****************************************************************************************************/

class CpHome extends CpAction {

	public $title = "Sales Dashboard";
	public $tplname = "home";

	public function idx() {
		$date = date('Y-m-d', (isset($_POST['week']))?strtotime($_POST['week']):time());
		$this->input['week'] = date('m/d/Y', strtotime($date));
		$res = $this->daffny->DB->query("SELECT
		DATE_FORMAT(DATE_ADD('".$date."', INTERVAL 2 - DAYOFWEEK('".$date."') DAY), '%m/%d/%Y') as `2`,
		DATE_FORMAT(DATE_ADD('".$date."', INTERVAL 3 - DAYOFWEEK('".$date."') DAY), '%m/%d/%Y') as `3`,
		DATE_FORMAT(DATE_ADD('".$date."', INTERVAL 4 - DAYOFWEEK('".$date."') DAY), '%m/%d/%Y') as `4`,
		DATE_FORMAT(DATE_ADD('".$date."', INTERVAL 5 - DAYOFWEEK('".$date."') DAY), '%m/%d/%Y') as `5`,
		DATE_FORMAT(DATE_ADD('".$date."', INTERVAL 6 - DAYOFWEEK('".$date."') DAY), '%m/%d/%Y') as `6`,
		DATE_FORMAT(DATE_ADD('".$date."', INTERVAL 7 - DAYOFWEEK('".$date."') DAY), '%m/%d/%Y') as `7`,
		DATE_FORMAT(DATE_ADD('".$date."', INTERVAL 1 - DAYOFWEEK('".$date."') DAY), '%m/%d/%Y') as `1`
		");
		$weekData = $this->daffny->DB->fetch_row($res);
		foreach ($weekData as $key => $wd) {
			$weekData[$key] = array('date' => $wd);
		}

		$products = $this->daffny->DB->selectRows('id, name', Product::TABLE, "WHERE `is_online` = 1 AND `is_delete` = 0");
		$data = array();
		$dashboard = array();
		$monthlyTotal = array();
		foreach ($products as $prod) {
			$select = "IFNULL(SUM(od.total), 0) as amount, SUM(od.quantity) as cnt, DAYOFWEEK(o.`register_date`) as `day`, DATE_FORMAT(o.`register_date`, '%m/%d/%Y') as `date`";
			$from = Product::TABLE." p, ".Orders::TABLE." o, `orders_details` od";
			$where = "WHERE WEEK(o.`register_date`) = WEEK('".$date."') AND p.`id` = od.`product_id` AND od.`order_id` = o.`id` AND o.`status` = ".Orders::STATUS_PROCESSED." AND p.`id` = ".(int)$prod['id']." GROUP BY `day`";
			$data[$prod['name']] = $this->daffny->DB->selectRows($select, $from, $where);
			$dashboard[$prod['name']] = $weekData;
			$select = "IFNULL(SUM(od.total), 0) as amount, SUM(od.quantity) as cnt";
			$from = Product::TABLE." p, ".Orders::TABLE." o, `orders_details` od";
			$where = "WHERE MONTH(o.`register_date`) = MONTH('".$date."') AND p.`id` = od.`product_id` AND od.`order_id` = o.`id` AND o.`status` = ".Orders::STATUS_PROCESSED." AND p.`id` = ".(int)$prod['id'];
			$monthlyTotal[$prod['name']] = $this->daffny->DB->selectRow($select, $from, $where);
		}
		foreach ($dashboard as $pn => $dd) {
			$weeklySummary = array(
				'cnt' => 0,
				'amount' => 0,
			);
			foreach ($dd as $wn => $wd) {
				foreach ($data[$pn] as $pd) {
					if ($pd['day'] == $wn) {
						unset($pd['day']);
						$dashboard[$pn][$wn] = array_merge($pd, $wd);
					}
				}
				if (!isset($dashboard[$pn][$wn]['amount'])) {
					$dashboard[$pn][$wn]['amount'] = '0.00';
					$dashboard[$pn][$wn]['cnt'] = 0;
				}
				$weeklySummary['cnt'] += (int)$dashboard[$pn][$wn]['cnt'];
				$weeklySummary['amount'] += (float)$dashboard[$pn][$wn]['amount'];
			}
			$dashboard[$pn]['week'] = $weeklySummary;
			$dashboard[$pn]['month'] = $monthlyTotal[$pn];
		}
		$this->daffny->tpl->weekData = $weekData;
		$this->daffny->tpl->dashboard = $dashboard;
		$this->form->DateField('week', '10', array(), 'Week', '&nbsp;');
	}
}
