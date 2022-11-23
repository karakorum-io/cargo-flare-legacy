<?php

/* * ************************************************************************************************
 * AccountManager class
 * Class for work with Carriers, Locations, Shippers
 *
 * Client:		FreightDragon
 * Version:		1.0
 * Date:			2011-11-02
 * Author:		C.A.W., Inc. dba INTECHCENTER
 * Address:	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * E-mail:		techsupport@intechcenter.com
 * CopyRight 2011 FreightDragon. - All Rights Reserved
 * ************************************************************************************************* */

class AccountManager extends FdObjectManager {

    const TABLE = Account::TABLE;

    public function get($order = null, $per_page = 100, $where = '') {
        $rows = parent::get($order, $per_page, $where);
        $accounts = array();
        foreach ($rows as $row) {
            $account = new Account($this->db);
            $account->load($row['id']);

            $accounts[] = $account;
        }
        return $accounts;
    }

public function getActive( $where = '')
    {
       // if (!array_key_exists($type, Entity::$type_name)) throw new FDException("Invalid entity type");
	   
	   $tables = " `app_accounts` ";
       
        $sql = "SELECT count(status) as number , status FROM
						{$tables}
					    WHERE {$where} group by status";
						
		//print $sql;
		//exit;
        $result = $this->db->query($sql);
        if ($this->db->isError)
            throw new FDException("MySQL query error");
			
		
			$accountsActive = array();
        if ($this->db->num_rows() > 0) {
            while ($row = $this->db->fetch_row($result)) {
				
			
                $tempArr  = array();
				$tempArr['number'] = $row['number'];
				$tempArr['status'] = $row['status'];
				
				$accountsActive[] = $tempArr;
            }
        }
		
        return $accountsActive;
    }
public function getActiveShippers( $where = '')
    {
       // if (!array_key_exists($type, Entity::$type_name)) throw new FDException("Invalid entity type");
	   
	   $tables = "
app_accounts AS A 
						LEFT OUTER JOIN app_commision AS B ON A.id=B.shipper_id  
						LEFT OUTER JOIN members M ON M.id = B.members_id";
       
        $sql = "SELECT count(A.status) as number , A.status FROM
						{$tables}
					    WHERE {$where} group by A.status";
						
		//print $sql;
		//exit;
        $result = $this->db->query($sql);
        if ($this->db->isError)
            throw new FDException("MySQL query error");
			
		
			$accountsActive = array();
        if ($this->db->num_rows() > 0) {
            while ($row = $this->db->fetch_row($result)) {
				
			
                $tempArr  = array();
				$tempArr['number'] = $row['number'];
				$tempArr['status'] = $row['status'];
				
				$accountsActive[] = $tempArr;
            }
        }
		
        return $accountsActive;
    }
public function getShippersAccount($order = null, $per_page = 100, $where = '',$pagerWhere='')
    {
       // if (!array_key_exists($type, Entity::$type_name)) throw new FDException("Invalid entity type");
	   
	   /*$tables = "
app_accounts AS A 
						LEFT OUTER JOIN app_commision AS B ON A.id=B.shipper_id  
						LEFT OUTER JOIN members M ON M.id = B.members_id";*/
						
		$tables = "
app_accounts AS A 
						INNER JOIN members M ON M.id = A.owner_id";
        $where_s = $where." ";
/*
        if ($order instanceof OrderRewrite) {
            $where_s .= " " . $order->getOrder() . " ";
        }
		*/
		if($order!=null)
		  $where_s .= " " . $order . " ";
		//print  $where_s;
        if (!is_null($per_page) && ctype_digit((string)$per_page)) {
			
            $this->pager = new PagerRewrite($this->db);
            $this->pager->UrlStart = getLink();
            $this->pager->RecordsOnPage = $per_page;
            //$this->pager->init(' `app_accounts` ', "id", "WHERE " . $pagerWhere);
			
			$this->pager->init($tables, "A.id", "  " . $where_s);
            $where_s .= " " . $this->pager->getLimit() . " ";
        }
        $sql = "SELECT M.contactname as contactname,A.id as ShipperID
					FROM
						{$tables}
					WHERE
						{$where_s} ";
						
					/*	
			$sql = "SELECT M.contactname as contactname,A.id as ShipperID
					FROM
						app_accounts AS A 
						LEFT OUTER JOIN app_commision AS B ON A.id=B.shipper_id  
						LEFT OUTER JOIN members M ON M.id = B.members_id
					WHERE A.is_shipper =1
AND B.primary_entry=1 AND A.owner_id=1";
*/


	//print $sql."<br><br>";
		//exit;
        $result = $this->db->query($sql);
        if ($this->db->isError)
            throw new FDException("MySQL query error");
			
		
			$accounts = array();
        if ($this->db->num_rows() > 0) {
            while ($row = $this->db->fetch_row($result)) {
				
				$account = new Account($this->db);
                $account->load($row['ShipperID']);
					
				$tempArr  = array();
				$tempArr['assigned_id'] = $row['AssignedID'];
				$tempArr['contactname'] = $row['AssignedName'];
				$tempArr['Orders'] = $row['contactname'];
				
				$tempArr['accounts'] = $account;
				
				$accounts[] = $tempArr;
            }
        }
		/*print "<pre>";
		print_r($accounts);
		print "</pre>";
		*/
        return $accounts;
    }

public function getAccount($order = null, $per_page = 100, $where = '',$pagerWhere='')
    {
       // if (!array_key_exists($type, Entity::$type_name)) throw new FDException("Invalid entity type");
	   
	   $tables = "
 `app_entities` A,  `app_accounts` B, `members` C ";
        $where_s = " A.Account_id = B.ID
and A.Assigned_id = C.id  ".$where."
GROUP BY B.ID, B.Company_name, B.first_name,B.last_name, B.city,B.state, A.Assigned_id, C.contactname ";
        if ($order instanceof OrderRewrite) {
            $where_s .= " " . $order->getOrder() . " ";
        }
		//print  $where_s;
        if (!is_null($per_page) && ctype_digit((string)$per_page)) {
			
            $this->pager = new PagerRewrite($this->db);
            $this->pager->UrlStart = getLink();
            $this->pager->RecordsOnPage = $per_page;
            $this->pager->init(' `app_accounts` ', "id", "WHERE " . $pagerWhere);
            $where_s .= " " . $this->pager->getLimit() . " ";
        }
        $sql = "SELECT
						 B.id as ShipperID, B.Company_name as shipperCompany,B.first_name,B.last_name, B.city,B.state,  A.Assigned_id as AssignedID, C.contactname AssignedName,  COUNT( * ) AS Orders
					FROM
						{$tables}
					WHERE
						{$where_s} ";
						
	print $sql."<br><br>";
		//exit;
        $result = $this->db->query($sql);
        if ($this->db->isError)
            throw new FDException("MySQL query error");
			
		
			$accounts = array();
        if ($this->db->num_rows() > 0) {
            while ($row = $this->db->fetch_row($result)) {
				
				$account = new Account($this->db);
                $account->load($row['ShipperID']);
				
			
                $tempArr  = array();
				$tempArr['assigned_id'] = $row['AssignedID'];
				$tempArr['contactname'] = $row['AssignedName'];
				$tempArr['Orders'] = $row['Orders'];
				
				$tempArr['accounts'] = $account;
				
				$accounts[] = $tempArr;
            }
        }
		/*print "<pre>";
		print_r($accounts);
		print "</pre>";
		*/
        return $accounts;
    }

    public function searchAccount($search_string, $ownerId, $type = null) {
        if (!ctype_digit((string) $ownerId))
            throw new FDException("Invalid onwer ID");
        if (!is_null($type) && !array_key_exists($type, Account::$type_name))
            throw new FDException("Invalid account type");
        $add_where = "";
        if (!is_null($type)) {
            switch ($type) {
                case Account::TYPE_CARRIER:
                    $add_where = "AND `is_carrier` = 1";
					$owner_id = "`owner_id` !='' ";
                    break;
                case Account::TYPE_SHIPPER:
                    $add_where = "AND `is_shipper` = 1";
					$owner_id = "`owner_id` IN (" . implode(', ', Member::getCompanyMembers($this->db, $_SESSION['member']['parent_id'])) . ")";
                    break;
                case Account::TYPE_TERMINAL:
                    $add_where = "AND `is_location` = 1";
					$owner_id = "`owner_id` IN (" . implode(', ', Member::getCompanyMembers($this->db, $_SESSION['member']['parent_id'])) . ")";
                    break;
            }
        }
        $search_string = mysqli_real_escape_string($this->db->connection_id, $search_string);
        //$rows = parent::get(null, null, "`owner_id` = {$ownerId} {$add_where} AND `status` = 1 AND `donot_dispatch` = 0 AND (`company_name` LIKE('{$search_string}%') OR `first_name` LIKE('{$search_string}%') OR `last_name` LIKE('{$search_string}'))");
		
		//AND `donot_dispatch` = 0
		
		//$owner_id = "`owner_id` = {$ownerId}";
		//if($ownerId==1)
			  //$owner_id = "`owner_id` != 0";
           //if($_SESSION['member']['access_shippers']==0)
		   //if($_SESSION['member_id'] != getParentId())
				  //$ownerID = " AND `owner_id` = ".$_SESSION['member_id'];
			  
			   //$owner_id = " (`owner_id` IN (" . implode(', ', Member::getCompanyMembers($this->db, $_SESSION['member']['parent_id'])) . ")". $ownerID.")";
			 // $owner_id = "`owner_id` IN (" . implode(', ', Member::getCompanyMembers($this->db, $_SESSION['member']['parent_id'])) . ")";  // working
			  
			  //AND `status` = 1 
		$rows = parent::get(null, null, $owner_id." {$add_where}  AND (`company_name` LIKE('%{$search_string}%') 
OR `first_name` LIKE('%{$search_string}%') 
OR `last_name` LIKE('%{$search_string}') 
OR `phone1` LIKE('{$search_string}')
OR `phone2` LIKE('{$search_string}')
OR `contact_name1` LIKE('{$search_string}')
OR `contact_name2` LIKE('{$search_string}')
)");
        $accounts = array();
        foreach ($rows as $row) {
            $account = new Account($this->db);
            $account->load($row['id']);
            $accounts[] = $account;
        }
        return $accounts;
    }
    
    /**
     * --Reports--
     * Get data for Shippers report
     * 
     * @param type $order
     * @param int $per_page
     * @param String $cust_name
     * @param int $parent_id
     * @return Shippers data
     */


	   public function getShippersReports($order = "", $per_page, $cust_name,$report_arr = array(), $parent_id) {

	   
	   // print "11";
	    //print $cust_name = mysqli_real_escape_string($this->daffny->DB->connection_id, trim($report_arr["customers_name"]));


	    $where_query = "";
		$where = " WHERE A.`parentid` = ". $parent_id." AND A.STATUS <>3 AND A.`type` =3 AND A.account_id <>0 AND `created` >= '" . $report_arr["start_date"] . "' AND `created` <= '" . $report_arr["end_date"] . "'";
		if (trim($cust_name) != "") {

            $where_query ="
                           AND (
                            B.first_name LIKE '%" . $cust_name . "%'
                            OR B.last_name LIKE '%" . $cust_name . "%'
                            OR B.company_name LIKE '%" . $cust_name . "%'                           
                            OR CONCAT_WS(' ',B.first_name, B.last_name) LIKE '%" . $cust_name . "%'
                        )
                        ";   //OR CONCAT_WS(' ',shipperfname, shipperlname) LIKE '%" . $cust_name . "%'
						
            $where .= "
            		AND (
                            B.first_name LIKE '%" . $cust_name . "%'
                            OR B.last_name LIKE '%" . $cust_name . "%'
                            OR B.company_name LIKE '%" . $cust_name . "%'                           
                            OR CONCAT_WS(' ',B.first_name, B.last_name) LIKE '%" . $cust_name . "%'                       
                            
                        )					
            ";	
			
			//OR CONCAT_WS(' ',shipperfname, shipperlname) LIKE '%" . $cust_name . "%'
        }
		
		$where_x= "  GROUP BY A.account_id, A.assigned_id, B.first_name, B.last_name, B.company_name, B.email, B.phone1, B.phone1_ext, B.phone2, B.phone2_ext, case when A.source_name is null then A.referred_by else A.source_name end, A.AssignedName";
		 $where_s = $where.$where_x;				
		
	    $table = '(SELECT COUNT( * ) FROM app_order_header as A inner join app_accounts as B on A.account_id = B.id WHERE  A.`parentid` ='.$parent_id.' AND A.STATUS <>3 AND A.`type` =3 AND A.account_id <>0 AND `created` >= "' . $report_arr["start_date"] . '" AND `created` <= "' . $report_arr["end_date"] . '"'.$where_query.' GROUP BY A.account_id, A.assigned_id, B.first_name, B.last_name, B.company_name, B.email, B.phone1, B.phone1_ext, B.phone2, B.phone2_ext, case when A.source_name is null then A.referred_by else A.source_name end, A.AssignedName) as a';
		if (!is_null($per_page) && ctype_digit((string)$per_page)) {
			
            $this->pager = new PagerRewrite($this->db);
            $this->pager->UrlStart = getLink();
            $this->pager->RecordsOnPage = $per_page;
            $this->pager->init($table, "", trim());
            $where_s .= " " . $this->pager->getLimit() . " ";
        }
		
		if (!is_null($order)) $where .= " ".$where_s;
	    $sql = "Select A.account_id, A.created, A.assigned_id, B.first_name as shipperfname, B.last_name as shipperlname, B.company_name as shippercompany, B.email as shipperemail, B.phone1 as shipperphone1, B.phone1_ext as shipperphone1_ext, B.phone2 as shipperphone2, B.phone2_ext as shipperphone2_ext, case when A.source_name is null then A.referred_by else A.source_name end as `source_name`, A.AssignedName, count( * ) AS orders, sum( A.`total_tariff` ) AS tariff, sum( A.`total_carrier_pay` ) AS carrier, sum( A.`total_tariff` - `total_carrier_pay`
) AS deposit, sum(CASE WHEN A.`FlagTarrif` = 2 THEN 1 ELSE 0 END ) AS invoices, sum(CASE WHEN A.`FlagTarrif` !=2 THEN 1 ELSE 0 END ) AS payments FROM app_order_header A inner join app_accounts B on A.account_id = B.id".$where_s . "   ";



		             
	      $rows = $this->db->selectRows($sql);         
        if (!is_array($rows))
            throw new FDException("MySQL query error");        					
		    $shippers = array();
            foreach ($rows as $row) {			
                $shippers[] = $row;				
            }
        return $shippers;
    }

    public function getShippersReport($order = "", $per_page, $cust_name, $parent_id) {

        $where = " `owner_id`= '" . $parent_id . "' 
                    AND `is_shipper` = 1
                    ";

        if (trim($cust_name) != "") {
            $where .= " 
            		AND (
                            first_name LIKE '%" . $cust_name . "%'
                            OR last_name LIKE '%" . $cust_name . "%'
                            OR company_name LIKE '%" . $cust_name . "%'
                            OR contact_name1 LIKE '%" . $cust_name . "%'
                            OR contact_name2 LIKE '%" . $cust_name . "%'
                            OR CONCAT_WS(' ', first_name, last_name) LIKE '%" . $cust_name . "%'
                        )
            ";
        }

        $rows = parent::get($order, $per_page, $where);
        $shippers = array();
        foreach ($rows as $row) {
            $shipper = new Account($this->db);
            $shipper->load($row['id']);
            $shippers[] = $shipper;
        }
        return $shippers;
    }

    
    /**
     * --Reports--
     * Get data for Carriers report
     * 
     * @param type $order
     * @param int $per_page
     * @param String $comp_name
     * @param int $parent_id
     * @return Carriers data
     */

    public function getCarriersReport( $order = "", $per_page, $comp_name, $parent_id ) {

        $where = " `owner_id`= '" . $parent_id . "' 
                    AND `is_carrier` = 1
                    ";

        if (trim($comp_name) != "") {
            $where .= " 
            		AND (
                            first_name LIKE '%" . $comp_name . "%'
                            OR last_name LIKE '%" . $comp_name . "%'
                            OR company_name LIKE '%" . $comp_name . "%'
                            OR contact_name1 LIKE '%" . $comp_name . "%'
                            OR contact_name2 LIKE '%" . $comp_name . "%'
                            OR CONCAT_WS(' ', first_name, last_name) LIKE '%" . $comp_name . "%'
                        )
            ";
        }

        $rows = parent::get($order, $per_page, $where);
        $carriers = array();
        foreach ($rows as $row) {
            $carrier = new Account($this->db);
            $carrier->load($row['id']);
            $carriers[] = $carrier;
        }
        return $carriers;
    }
	
	
		
    public function getCarriersReports($order = "", $per_page, $cust_name, $parent_id) {
		
        $inner_query = "SELECT COUNT( * ) , carrier_id FROM  `app_order_header` WHERE TYPE =3 AND parentid =". $parent_id."
AND STATUS <>3";
	     
		$where = " WHERE a.`owner_id` = ". $parent_id." AND a.is_carrier =1 and e.status <>3 AND e.`type` =3";		
		if (trim($cust_name) != "") {
			$where_query ="
                            a.first_name LIKE '%" . $cust_name . "%'
                            OR a.last_name LIKE '%" . $cust_name . "%'
                            OR a.company_name LIKE '%" . $cust_name . "%'                           
                            OR CONCAT_WS(' ',a.first_name, a.last_name) LIKE '%" . $cust_name . "%'
                        ";
            $where .= " 
            		AND (
                            a.first_name LIKE '%" . $cust_name . "%'
                            OR a.last_name LIKE '%" . $cust_name . "%'
                            OR a.company_name LIKE '%" . $cust_name . "%'                           
                            OR CONCAT_WS(' ',a.first_name, a.last_name) LIKE '%" . $cust_name . "%'
                        )
            ";
        }
		$where_x = "  GROUP BY `carrier_id` order by carrier_id desc";
		$where_s = $where.$where_x;
		
	    $table = 'app_accounts AS a INNER JOIN ('.$inner_query.' GROUP BY  `carrier_id`  ORDER BY carrier_id DESC) AS e ON a.id = e.carrier_id';
		//var $on_page = 10;
		if (!is_null($per_page) && ctype_digit((string)$per_page)) {
			
            $this->pager = new PagerRewrite($this->db);
            $this->pager->UrlStart = getLink();
            $this->pager->RecordsOnPage = $per_page;
            //$this->pager->init('`app_accounts` AS a INNER JOIN app_order_header AS e ON a.id = e.carrier_id ', "", trim($where));
			$this->pager->init($table, "", trim($where_query));
		    
            $where_s .= " " . $this->pager->getLimit() . " ";
			
        }
		
		if (!is_null($order)) $where .= " ".$where_s;
		
       $sql = "SELECT a.id, a.first_name,a.company_name, a.last_name, a.print_name, a.tax_id_num, a.insurance_iccmcnumber, a.contact_name1, a.contact_name2, a.phone1, a.phone2, a.cell, a.fax, a.email,count( * ) AS orders, sum(
CASE WHEN e.`status` >=6
THEN 1
ELSE 0
END ) AS dispatches, sum( e.`total_tariff` ) AS tariff, sum(
CASE WHEN e.`FlagTarrif` =2
THEN 1
ELSE 0
END ) AS invoices, sum(
CASE WHEN e.`FlagTarrif` !=2
THEN 1
ELSE 0
END ) AS payments
FROM `app_accounts` AS a
INNER JOIN app_order_header AS e ON a.id = e.carrier_id ".$where_s . "   ";

       
	      $rows = $this->db->selectRows($sql);         
        if (!is_array($rows))
            throw new FDException("MySQL query error");
        
			
		
		$shippers = array();
        foreach ($rows as $row) {
			
            $shippers[] = $row;
			
			
        }
        return $shippers;
    }

public function getDuplicateAccount($order = null, $per_page = 100, $where = '',$pagerWhere='',$accType=0)
    {
       // if (!array_key_exists($type, Entity::$type_name)) throw new FDException("Invalid entity type");
	  
	   $select = '';
	   //if($accType==1) 
	      //$select .= " , A.`contact_name1` , A.`contact_name2` ";
	   //else
	   if($accType==2)
	     $select .= " , A.first_name, A.last_name ";
		
	   $tables = "
`app_accounts` A, members B ";

        $where_s = " A.`owner_id` = B.`id` 
			 ".$where." and  B.parent_id  = ".getParentId()." 
			 GROUP BY A.`company_name` ".$select.", A.`city` , A.`state` , A.`zip_code`
			 HAVING COUNT( * ) > 1
			 ORDER BY COUNT( * ) DESC , A.`company_name` ";

         $sql = "SELECT A.id as ID, A.`company_name`".$select." , A.`city` , A.`state` , A.`zip_code`, COUNT( * ) as number_of_count
					FROM
						{$tables}
					WHERE
						{$where_s} ";
						

	
        $result = $this->db->query($sql);
        if ($this->db->isError)
            throw new FDException("MySQL query error");
			
		
			$accounts = array();
        if ($this->db->num_rows() > 0) {
            while ($row = $this->db->fetch_row($result)) {
				
				$account = new Account($this->db);
                $account->load($row['ID']);
					
				$tempArr  = array();
				$tempArr['ID'] = $row['ID'];
				$tempArr['number_of_count'] = $row['number_of_count'];
				$tempArr['company_name'] = $row['company_name'];
				if($accType==1){ 
	              $tempArr['contact_name1'] = $row['contact_name1'];
				  $tempArr['contact_name2'] = $row['contact_name2'];
				}
	            elseif($accType==2){
					 $tempArr['first_name'] = $row['first_name'];
				     $tempArr['last_name'] = $row['last_name'];  
				}
				$tempArr['city'] = $row['city'];
				$tempArr['state'] = $row['state'];
				$tempArr['zip_code'] = $row['zip_code'];
				
				
				$tempArr['accounts'] = $account;
				
				$accounts[] = $tempArr;
            }
        }
	
        return $accounts;
    }

   public function getDuplicateAccountDetails($order = null, $per_page = 100, $where = '',$pagerWhere='',$acctype=0)
    {
       $select = '';
	   $tables = " `app_accounts` A Inner join members B on A.`owner_id` = B.`id` LEFT OUTER JOIN `app_entities` C ";
	   if($acctype==1) {
	      $tables .= " on  A.`id` = C.`carrier_id` ";
		  $select .= " , A.`contact_name1` , A.`contact_name2` ";
	   }
	   elseif($acctype==2){
	     $tables .= " on  A.`id` = C.`account_id` ";
		  $select .= " , A.first_name, A.last_name ";
	   }
		   
        $where_s = $where." and  B.parent_id  = ".getParentId()."
			 group by A.`id` , A.`owner_id` , A.`company_name` ".$select." , A.`phone1` , A.`phone2` , A.`phone1_ext` ,
A.`phone2_ext` , A.Address1, A.address2, A.`city` , A.`state` ,
A.`zip_code`";

       $sql = "SELECT A.id as ID, COUNT( C.id ) as number_of_count
					FROM
						{$tables}
					WHERE
						{$where_s} ";


        $result = $this->db->query($sql);
        if ($this->db->isError)
            throw new FDException("MySQL query error");
			
		
			$accounts = array();
        if ($this->db->num_rows() > 0) {
            while ($row = $this->db->fetch_row($result)) {
				
				$account = new Account($this->db);
                $account->load($row['ID']);
					
				$tempArr  = array();
				$tempArr['ID'] = $row['ID'];
				$tempArr['number_of_count'] = $row['number_of_count'];
				
				$tempArr['accounts'] = $account;
				
				$accounts[] = $tempArr;
            }
        }
	
        return $accounts;
    }
    
	
public function getShipperPaymentHistoryReports($order = "", $per_page, $cust_name,$report_arr = array(), $parent_id) {

	  
	    //print $cust_name = mysqli_real_escape_string($this->daffny->DB->connection_id, trim($report_arr["customers_name"]));
	    $where_query = "";
		$where = " WHERE A.`parentid` = ". $parent_id." AND A.STATUS =9 AND A.`type` =3 AND A.account_id <>0 AND `created` >= '" . $report_arr["start_date"] . "' AND `created` <= '" . $report_arr["end_date"] . "' group by A.`account_id`,
	A.`shippercompany`,
	A.`shipperfname`,
	A.`shipperlname`
order by orderCount desc";

		 $where_s = $where.$where_x;				
		
	     $table = "(
    SELECT COUNT( * ) AS orderCount
    FROM `app_order_header` A INNER JOIN 
	(SELECT 
			P.`entityid`,
			max(Q.`created`) as paidDate,
			sum(Q.`amount`) as ShipperAmount	 
		FROM `app_order_header` P LEFT OUTER JOIN `app_payments` Q
		ON P.`entityid` = Q.`entity_id`
		and  Q.`fromid` =2
		AND  Q.`toid` =1
		AND  Q.`deleted` =0
		WHERE 	P.`parentid` =  ". $parent_id."
		and 	P.`type` = 3
		and 	P.`status` in (9)
		
		group by 
			P.`entityid`) B
	ON A.entityid = B.entityid INNER JOIN  
	(SELECT  `entity_id` , MAX(  `created` ) AS invoiceDate
		FROM  `app_notes` 
		WHERE  `text` LIKE  '%Invoice Attached%'
		AND  `deleted` =0
		GROUP BY  `entity_id` ) as C
	on A.entityid  = C.`entity_id` ";
	
	
		if (!is_null($per_page) && ctype_digit((string)$per_page)) {
			
            $this->pager = new PagerRewrite($this->db);
            $this->pager->UrlStart = getLink();
            $this->pager->RecordsOnPage = $per_page;
            $this->pager->init($table, "X.orderCount", " WHERE A.`parentid` = ". $parent_id." AND A.STATUS =9 AND A.`type` =3 AND A.account_id <>0 AND `created` >= '" . $report_arr["start_date"] . "' AND `created` <= '" . $report_arr["end_date"] . "' GROUP BY A.`account_id` , A.`shippercompany` , A.`shipperfname` , A.`shipperlname` 
    ORDER BY orderCount DESC 
) as X");
            $where_s .= " " . $this->pager->getLimit() . " ";
        }
		
		if (!is_null($order)) $where .= " ".$where_s;
	      $sql = "SELECT 
	A.`account_id` as account_id,
	A.`shippercompany`,
	A.`shipperfname`,
	A.`shipperlname`,
	sum(A.`total_tariff`) as total_tariff, 
	sum(A.`total_carrier_pay`) as total_carrier_pay, 
	sum(A.`total_deposite`) as total_deposite,
	sum(B.ShipperAmount) as PaidShipperAmount,	
	max(A.`created`) as LastOrderDate, 
	max(B.paidDate) as LastPaidDate,
	Max(C.invoiceDate) as LastInvoiceDate,	
	count(A.entityid) as orderCount,
	Sum(DATEDIFF(B.paidDate,C.invoiceDate)) as TotalofDaysToPay,
	Sum(DATEDIFF(B.paidDate,C.invoiceDate))/count(A.entityid) as AvgDayToPay
FROM `app_order_header` A INNER JOIN 
	(SELECT 
			P.`entityid`,
			max(Q.`created`) as paidDate,
			sum(Q.`amount`) as ShipperAmount	 
		FROM `app_order_header` P LEFT OUTER JOIN `app_payments` Q
		ON P.`entityid` = Q.`entity_id`
		and  Q.`fromid` =2
		AND  Q.`toid` =1
		AND  Q.`deleted` =0
		WHERE 	P.`parentid` = ". $parent_id."
		and 	P.`type` = 3
		and 	P.`status` in (9)
		group by 
			P.`entityid`) B
	ON A.entityid = B.entityid INNER JOIN  
	(SELECT  `entity_id` , MAX(  `created` ) AS invoiceDate
		FROM  `app_notes` 
		WHERE  `text` LIKE  '%Invoice Attached%'
		AND  `deleted` =0
		GROUP BY  `entity_id` ) as C
	on A.entityid  = C.`entity_id`".$where_s . "   ";	             
	    $rows = $this->db->selectRows($sql);         
        if (!is_array($rows))
            throw new FDException("MySQL query error");        					
		    $shippers = array();
            foreach ($rows as $row) {			
                $shippers[] = $row;				
            }
        return $shippers;
    }

    public function searchDuplicateAccounts($order = null, $per_page = 100, $where = '',$acctype=0, $search_type, $value)
    {
       
	   $SearchStr = "";
 		$SearchArr = $value;

        $sizeString = sizeof($SearchArr);
		if($value!="" && $sizeString>=1)
		{
			$where .=" and (";
		   for($i=0;$i<$sizeString;$i++)
		   {
			//$SearchStr .= "'".trim($SearchArr[$i])."',";
			$where .= "  A.`company_name` LIKE('%".mysqli_real_escape_string($this->db->connection_id, trim($SearchArr[$i]))."%') OR A.`id` LIKE('%".mysqli_real_escape_string($this->db->connection_id, trim($SearchArr[$i]))."%') ";
			
			if($i < $sizeString-1)
			   $where .= " OR ";
		   }
		   
		   $where .=" ) ";
			//$SearchStr = substr($SearchStr,0,-1);
			//$where .= " and A.id  in (".$SearchStr.")  "; 
		}
		elseif($search_type !=""){
		   $where .= " and A.`company_name` ='".$search_type."'  "; 
		}
	   
	   $tables = " `app_accounts` A Inner join members B on A.`owner_id` = B.`id` LEFT OUTER JOIN `app_entities` C ";
	   if($acctype==1) 
	      $tables .= " on  A.`id` = C.`account_id` ";
	   elseif($acctype==2)
	     $tables .= " on  A.`id` = C.`carrier_id` ";
	     
        $where_s = $where." and  B.parent_id  = ".getParentId()."
			 group by A.`id` , A.`owner_id` , A.`company_name` , A.`contact_name1` ,
A.`contact_name2` , A.`phone1` , A.`phone2` , A.`phone1_ext` ,
A.`phone2_ext` , A.Address1, A.address2, A.`city` , A.`state` ,
A.`zip_code`";

       $sql = "SELECT A.id as ID, COUNT( C.id ) as number_of_count
					FROM
						{$tables}
					WHERE
						{$where_s} ";

        $result = $this->db->query($sql);
        if ($this->db->isError)
            throw new FDException("MySQL query error");
			
		
			$accounts = array();
        if ($this->db->num_rows() > 0) {
            while ($row = $this->db->fetch_row($result)) {
				
				$account = new Account($this->db);
                $account->load($row['ID']);
					
				$tempArr  = array();
				$tempArr['ID'] = $row['ID'];
				$tempArr['number_of_count'] = $row['number_of_count'];
				
				$tempArr['accounts'] = $account;
				
				$accounts[] = $tempArr;
            }
        }
	
        return $accounts;
    } 
}

?>