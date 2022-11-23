<?php

/* * ************************************************************************************************
 * EntityManager class
 * Class for work with entities
 *
 * Client:		FreightDragon
 * Version:		1.0
 * Date:			2011-11-14
 * Author:		C.A.W., Inc. dba INTECHCENTER
 * Address:		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * E-mail:		techsupport@intechcenter.com
 * CopyRight 2011 FreightDragon. - All Rights Reserved
 * ************************************************************************************************* */

class EntityManager extends FdObjectManager
{

    const TABLE = Entity::TABLE;
	
 public function getCount($type)
  {

        if (!array_key_exists($type, Entity::$type_name)) 
             throw new FDException("Invalid entity type");

        $where = " WHERE `type` = " . $type . " AND `deleted` = 0 ";

        if (isset($_SESSION['view_id']) && ($_SESSION['view_id'] != -1)) {
            //$where .= " AND `assigned_id` = " . (int)$_SESSION['view_id'] . " ";
			$where .= " AND (`assigned_id` = '" . (int)$_SESSION['view_id'] . "' OR  `creator_id` ='".(int)$_SESSION['view_id'] . "')";
        }else {
           // $where .= " AND e.`assigned_id` IN (" . implode(', ', Member::getCompanyMembers($this->db, $_SESSION['member']['parent_id'])) . ")";
		   $where .= " AND (".$this->getPermissionCondition($type, false)." OR `creator_id` ='".$_SESSION['member_id'] . "')"; 
        }
      
	 
 	 // print $where;
	  
		if($type==1)
          $rows = $this->db->selectRows("`status`,lead_type, COUNT(`id`) as cnt", self::TABLE, "{$where} GROUP BY `status`,lead_type");
		else
		  $rows = $this->db->selectRows("`status`, COUNT(`id`) as cnt", self::TABLE, "{$where} GROUP BY `status`");
		  
        $result = array(
            0 => 0,
            Entity::STATUS_UNREADABLE => 0,
            Entity::STATUS_ACTIVE => 0,
            Entity::STATUS_ONHOLD => 0,
            Entity::STATUS_ARCHIVED => 0,
            Entity::STATUS_POSTED => 0,
            Entity::STATUS_NOTSIGNED => 0,
            Entity::STATUS_DISPATCHED => 0,
            Entity::STATUS_ISSUES => 0,
            Entity::STATUS_PICKEDUP => 0,
            Entity::STATUS_DELIVERED => 0,
			15 => 0 // For Leads created count
       );

  
		$archivedTotal = 0;
        foreach ($rows as $row) {

            if($row['status']==1 && $row['lead_type']==1)
			    $result[15] = $row['cnt'];
		    else
			    if(Entity::STATUS_ARCHIVED == $row['status'])
				   $archivedTotal = $archivedTotal + $row['cnt'];
				else   
                $result[$row['status']] = $row['cnt'];

        }

       $result[Entity::STATUS_ARCHIVED] = $archivedTotal;
	   
	   
        $row = $this->db->selectRow("COUNT(`id`) as followup", self::TABLE, "WHERE `deleted` = 0 AND `type` = " . $type . " AND " . $this->getPermissionCondition($type, false) . " AND `id` IN (SELECT `entity_id` FROM " . FollowUp::TABLE . " WHERE `followup` = '" . date("Y-m-d") . "')");

        $result[0] = $row['followup'];
        return $result;
 }
 
 
 public function getCountCreatedLead($type,$extra=0)
  {

        if (!array_key_exists($type, Entity::$type_name)) 
             throw new FDException("Invalid entity type");

        $where = " WHERE `type` = " . $type . " AND `deleted` = 0 ";

        if (isset($_SESSION['view_id']) && ($_SESSION['view_id'] != -1)) {
            //$where .= " AND `assigned_id` = " . (int)$_SESSION['view_id'] . " ";
			$where .= " AND (`assigned_id` = '" . (int)$_SESSION['view_id'] . "' ";
			if($extra==1 )
		    {
				$where .= " OR  (`creator_id` ='".(int)$_SESSION['view_id'] . "' AND creator_id = assigned_id ))";
			}
			else
			  $where .= " OR  `creator_id` ='".(int)$_SESSION['view_id'] . "' )";
        }else {
           // $where .= " AND e.`assigned_id` IN (" . implode(', ', Member::getCompanyMembers($this->db, $_SESSION['member']['parent_id'])) . ")";
		   $where .= " AND (".$this->getPermissionCondition($type, false)." "; 
		   if($extra==1 )
		    {
				$where .= " OR  (`creator_id` ='".(int)$_SESSION['view_id'] . "' AND creator_id = assigned_id ))";
			}
			else
			  $where .= " OR  `creator_id` ='".(int)$_SESSION['view_id'] . "' )";
        }
      
	 if($extra==2 )
		{
			$where .= " AND lead_type = 1  AND creator_id != assigned_id ";
		}
		elseif($extra==1 )
		{
			$where .= " AND lead_type = ".$extra."  AND creator_id = assigned_id ";
		}
		
 	 // print $where;
	  
		if($type==1)
          $rows = $this->db->selectRows("`status`,lead_type, COUNT(`id`) as cnt", self::TABLE, "{$where} GROUP BY `status`,lead_type");
		else
		  $rows = $this->db->selectRows("`status`, COUNT(`id`) as cnt", self::TABLE, "{$where} GROUP BY `status`");
		  
        $result = array(
            0 => 0,
            Entity::STATUS_UNREADABLE => 0,
            Entity::STATUS_ACTIVE => 0,
            Entity::STATUS_ONHOLD => 0,
            Entity::STATUS_ARCHIVED => 0,
            Entity::STATUS_POSTED => 0,
            Entity::STATUS_NOTSIGNED => 0,
            Entity::STATUS_DISPATCHED => 0,
            Entity::STATUS_ISSUES => 0,
            Entity::STATUS_PICKEDUP => 0,
            Entity::STATUS_DELIVERED => 0,
			15 => 0, // For Leads created count
			16 => 0 ,// For Leads Assigned count
			Entity::STATUS_PRIORITY => 0,
            Entity::STATUS_DEAD => 0
       );

  
		$archivedTotal = 0;
        foreach ($rows as $row) {

            if($row['status']==1 && $row['lead_type']==1)
			    $result[15] = $row['cnt'];
		    else
			    if(Entity::STATUS_ARCHIVED == $row['status'])
				   $archivedTotal = $archivedTotal + $row['cnt'];
				else   
                $result[$row['status']] = $row['cnt'];

        }

       $result[Entity::STATUS_ARCHIVED] = $archivedTotal;
	   
	   
        $row = $this->db->selectRow("COUNT(`id`) as followup", self::TABLE, "WHERE `deleted` = 0 AND `type` = " . $type . " AND " . $this->getPermissionCondition($type, false) . " AND `id` IN (SELECT `entity_id` FROM " . FollowUp::TABLE . " WHERE `followup` = '" . date("Y-m-d") . "')");

        $result[0] = $row['followup'];
        return $result;
 }
 
   public function getCountAssigned($type,$extra=0)
  {

        if (!array_key_exists($type, Entity::$type_name)) 
             throw new FDException("Invalid entity type");

        $where = " WHERE `type` = " . $type . " AND `deleted` = 0 ";

        if (isset($_SESSION['view_id']) && ($_SESSION['view_id'] != -1)) {
            //$where .= " AND `assigned_id` = " . (int)$_SESSION['view_id'] . " ";
			$where .= " AND (`assigned_id` = '" . (int)$_SESSION['view_id'] . "' ";
			if($extra ==2 )
		    {
				$where .= " OR  (`creator_id` ='".(int)$_SESSION['view_id'] . "' AND creator_id != assigned_id ))";
			}
			
        }else {
           // $where .= " AND e.`assigned_id` IN (" . implode(', ', Member::getCompanyMembers($this->db, $_SESSION['member']['parent_id'])) . ")";
		   $where .= " AND (".$this->getPermissionCondition($type, false)." "; 
		   if($extra ==2 )
		    {
				$where .= " OR  ( `creator_id` ='".$_SESSION['member_id'] . "' AND creator_id != assigned_id ))";
			}
			
        }
      
	 if($extra==2 )
		{
			$where .= " AND lead_type = 1  AND creator_id != assigned_id";
		}
 	 // print $where;
	  
		if($type==1)
          $rows = $this->db->selectRows("`status`,lead_type, COUNT(`id`) as cnt", self::TABLE, "{$where} GROUP BY `status`,lead_type");
		else
		  $rows = $this->db->selectRows("`status`, COUNT(`id`) as cnt", self::TABLE, "{$where} GROUP BY `status`");
		  
        $result = array(
           16 => 0 // For Leads Assigned count
       );

  
		foreach ($rows as $row) {

            if($extra==2 && $row['status']==1 && $row['lead_type']==1)
			{
				$result[16] = $row['cnt'];
			}
			
        }
        return $result;
 }
/*
    public function getCount($type)
    {
        if (!array_key_exists($type, Entity::$type_name))
            throw new FDException("Invalid entity type");
        $where = " WHERE `type` = " . $type . " AND `deleted` = 0 AND " . $this->getPermissionCondition($type, false) . " ";
        if (isset($_SESSION['view_id']) && ($_SESSION['view_id'] != -1)) {
            $where .= " AND `assigned_id` = " . (int)$_SESSION['view_id'] . " ";
        }
        $rows = $this->db->selectRows("`status`, COUNT(`id`) as cnt", self::TABLE, "{$where} GROUP BY `status`");
        $result = array(
            0 => 0,
            Entity::STATUS_UNREADABLE => 0,
            Entity::STATUS_ACTIVE => 0,
            Entity::STATUS_ONHOLD => 0,
            Entity::STATUS_ARCHIVED => 0,
            Entity::STATUS_POSTED => 0,
            Entity::STATUS_NOTSIGNED => 0,
            Entity::STATUS_DISPATCHED => 0,
            Entity::STATUS_ISSUES => 0,
            Entity::STATUS_PICKEDUP => 0,
            Entity::STATUS_DELIVERED => 0
        );
        foreach ($rows as $row) {
            $result[$row['status']] = $row['cnt'];
        }
        $row = $this->db->selectRow("COUNT(`id`) as followup", self::TABLE, "WHERE `deleted` = 0 AND `type` = " . $type . " AND " . $this->getPermissionCondition($type, false) . " AND `id` IN (SELECT `entity_id` FROM " . FollowUp::TABLE . " WHERE `followup` = '" . date("Y-m-d") . "')");
        $result[0] = $row['followup'];
        return $result;
    }
*/
    public function getDispatchedCount()
    {
        $where = "WHERE `type` = " . Entity::TYPE_ORDER . " AND `deleted` = 0 AND `carrier_id` IN (" . implode(",", Member::getCompanyMembers($this->db, $_SESSION['member']['parent_id'])) . ")";
        $rows = $this->db->selectRows("`status`, COUNT(`id`) as cnt", self::TABLE, "{$where} GROUP BY `status`");
        $result = array(
            Entity::STATUS_NOTSIGNED => 0,
            Entity::STATUS_DISPATCHED => 0,
            Entity::STATUS_PICKEDUP => 0,
            Entity::STATUS_DELIVERED => 0,
            Entity::STATUS_ARCHIVED => 0
        );
        foreach ($rows as $row) {
            $result[$row['status']] = $row['cnt'];
        }
        return $result;
    }

    public function getNewLeadsCount()
    {
        if (!($this->db instanceof mysql))
            throw new FDException("DB Helper not set");
        $count = $this->db->selectRow("COUNT(`id`) as cnt", self::TABLE, "WHERE `type` = " . Entity::TYPE_LEAD . " AND DATE_SUB(CURDATE(),INTERVAL 30 DAY) <= `created` AND (`assigned_id` = " . (int)$_SESSION['member_id'] . " OR `assigned_id` = " . (int)getParentId() . ") AND `deleted` = 0 AND `status`='".Entity::STATUS_ACTIVE."'");
        if ($this->db->isError)
            throw new FDException("LeadManager->getNewCount: MySQL query error");
        return $count['cnt'];
    }

    private function getPermissionCondition($type, $edit = false, $index = '')
    {
        if (!array_key_exists($type, Entity::$type_name))
            throw new FDException("Invalid entity type");
        if ( $index != '' ) {
            $index .= '.';
        }
        switch ($type) {
            case Entity::TYPE_LEAD:
                $access = $_SESSION['member']['access_leads'];
                break;
            case Entity::TYPE_QUOTE:
                $access = $_SESSION['member']['access_quotes'];
                break;
            case Entity::TYPE_ORDER:
                $access = $_SESSION['member']['access_orders'];
                break;
        }
        $where = false;
      
        switch ($access) {
            case 0:
                $where = " {$index}`assigned_id` = " . $_SESSION['member_id'] . " ";
                break;
            case 1:
                if ($edit) {
                    $where = " {$index}`assigned_id` = " . $_SESSION['member_id'] . " ";
                } else {
                    $where = " {$index}`assigned_id` IN (" . implode(",", Member::getCompanyMembers($this->db, $_SESSION['member']['parent_id'])) . ") ";
                }
                break;
            case 2:
                $where = " {$index}`assigned_id` IN (" . implode(",", Member::getCompanyMembers($this->db, $_SESSION['member']['parent_id'])) . ") ";
                break;
        }
        return ($where) ? $where : " 1";
    }

    public function changeStatus($entity_ids, $status)
    {
        if (!is_array($entity_ids) || !array_key_exists($status, Entity::$status_name))
            throw new FDException("Invalid input data");
        foreach ($entity_ids as $entity_id) {
            $entity = new Entity($this->db);
            $entity->load($entity_id);
            //if ($entity->readonly)
                //continue;
            if ($entity->status != $status) {
                $entity->setStatus($status);
            }
        }
    }

    public function search($type, $search_type, $value, $per_page = 50, $order = null)
    {
        if (!array_key_exists($type, Entity::$type_name)) throw new FDException("Invalid entity type");
        $entities = array();
        $value = mysqli_real_escape_string($this->db->connection_id, $value);
        switch ($search_type) {
            case "basic":
                $tables = self::TABLE . " e
									LEFT JOIN " . Shipper::TABLE . " s ON (e.`shipper_id` = s.`id`)
									LEFT JOIN " . Origin::TABLE . " o ON (e.`origin_id` = o.`id`)
									LEFT JOIN " . Destination::TABLE . " d ON (e.`destination_id` = d.`id`)
									LEFT JOIN " . Vehicle::TABLE . " v ON (e.`id` = v.`entity_id`)
									LEFT JOIN " . Note::TABLE . " n ON (e.`id` = n.`entity_id`)";
                $where = "e.`deleted` = 0
									AND (s.`fname` LIKE('%{$value}%')
										OR s.`lname` LIKE('%{$value}%')
										OR s.`phone1` LIKE('%{$value}%')
										OR s.`phone2` LIKE('%{$value}%')
										OR s.`mobile` LIKE('%{$value}%')
										OR s.`email` LIKE('%{$value}%')
										OR s.`company` LIKE('%{$value}%')
										OR o.`address1` LIKE('%{$value}%')
										OR o.`address2` LIKE('%{$value}%')
										OR o.`city` LIKE('%{$value}%')
										OR o.`state` LIKE('%{$value}%')
										OR o.`zip` LIKE('%{$value}%')
										OR o.`country` LIKE('%{$value}%')
										OR d.`address1` LIKE('%{$value}%')
										OR d.`address2` LIKE('%{$value}%')
										OR d.`city` LIKE('%{$value}%')
										OR d.`state` LIKE('%{$value}%')
										OR d.`zip` LIKE('%{$value}%')
										OR d.`country` LIKE('%{$value}%')
										OR v.`make` LIKE('%{$value}%')
										OR v.`model` LIKE('%{$value}%')
										OR v.`type` LIKE('%{$value}%')
										OR n.`text` LIKE('%{$value}%'))";
                break;
	        case 'carrier':
		        $tables = self::TABLE . " e
		        LEFT JOIN `app_dispatch_sheets` ds ON (ds.`entity_id` = e.`id`)";
				$where = "
				ds.`deleted` = 0
				AND ds.`cancelled` IS NULL
				AND ds.`rejected` IS NULL
				AND ds.`carrier_company_name` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
				";
		        break;
	        case 'route':
				$routeStates = explode('-', $value);
				if (count($routeStates) != 2) {
					$tables = self::TABLE . " e";
					$where = " 0 ";
				} else {
					$tables = self::TABLE . " e
									LEFT JOIN " . Origin::TABLE . " o ON (e.`origin_id` = o.`id`)
									LEFT JOIN " . Destination::TABLE . " d ON (e.`destination_id` = d.`id`)";
					$where = "e.`deleted` = 0
					AND o.`state` LIKE('".$routeStates[0]."')
					AND d.`state` LIKE('".$routeStates[1]."')";
				}
		        break;
            case "name":
                $tables = self::TABLE . " e
									LEFT JOIN " . Shipper::TABLE . " s ON (e.`shipper_id` = s.`id`)";
                $where = "e.`deleted` = 0
									AND (s.`fname` LIKE('%{$value}%')
										OR s.`lname` LIKE('%{$value}%'))";
                break;
            case "id":
                $tables = self::TABLE . " e";
                $where = "e.`number` = {$value} AND e.`deleted` = 0";
                break;
            case "origin":
                $tables = self::TABLE . " e
									LEFT JOIN " . Origin::TABLE . " o ON (e.`origin_id` = o.`id`)";
                $where = "e.`deleted` = 0
									AND (o.`address1` LIKE('%{$value}%')
										OR o.`address2` LIKE('%{$value}%')
										OR o.`city` LIKE('%{$value}%')
										OR o.`state` LIKE('%{$value}%')
										OR o.`zip` LIKE('%{$value}%')
										OR o.`country` LIKE('%{$value}%'))";
                break;
            case "destination":
                $tables = self::TABLE . " e
									LEFT JOIN " . Destination::TABLE . " d ON (e.`destination_id` = d.`id`)";
                $where = "e.`deleted` = 0
									AND (d.`address1` LIKE('%{$value}%')
										OR d.`address2` LIKE('%{$value}%')
										OR d.`city` LIKE('%{$value}%')
										OR d.`state` LIKE('%{$value}%')
										OR d.`zip` LIKE('%{$value}%')
										OR d.`country` LIKE('%{$value}%'))";
                break;
            case "phone":
                $tables = self::TABLE . " e
									LEFT JOIN " . Shipper::TABLE . " s ON (e.`shipper_id` = s.`id`)";
                $where = "e.`deleted` = 0
									AND (s.`phone1` LIKE('%{$value}%')
										OR s.`phone2` LIKE('%{$value}%')
										OR s.`mobile` LIKE('%{$value}%'))";
                break;
            case "email":
                $tables = self::TABLE . " e
									LEFT JOIN " . Shipper::TABLE . " s ON (e.`shipper_id` = s.`id`)";
                $where = "e.`deleted` = 0
									AND s.`email` LIKE('%{$value}%')";
                break;
            case "company":
                $tables = self::TABLE . " e
									LEFT JOIN " . Shipper::TABLE . " s ON (e.`shipper_id` = s.`id`)";
                $where = "e.`deleted` = 0
									AND s.`company` LIKE('%{$value}%')";
                break;
            case "vehicle":
                $tables = self::TABLE . " e
									LEFT JOIN " . Vehicle::TABLE . " v ON (e.`id` = v.`entity_id`)";
                $where = "e.`deleted` = 0
									AND (v.`make` LIKE('%{$value}%')
										OR v.`model` LIKE('%{$value}%')
										OR v.`type` LIKE('%{$value}%'))";
                break;
            case "notes":
                $tables = self::TABLE . " e
									LEFT JOIN " . Note::TABLE . " n ON (e.`id` = n.`entity_id`)";
                $where = "e.`deleted` = 0
									AND n.`text` LIKE('%{$value}%')";
                break;
            default:
                throw new FDException("Invalid search type");
                break;
        }
        $where .= " AND e.`type` = {$type} AND " . $this->getPermissionCondition($type, false, "e");
        $where_s = $where . " GROUP BY e.`id`";
        if ($order instanceof OrderRewrite) {
            $where_s .= " " . $order->getOrder() . " ";
        }
        if (!is_null($per_page) && ctype_digit((string)$per_page)) {
            $this->pager = new PagerRewrite($this->db);
            $this->pager->UrlStart = getLink();
            $this->pager->RecordsOnPage = $per_page;
            $this->pager->init($tables, "DISTINCT(e.`id`)", "WHERE " . $where);
            $where_s .= " " . $this->pager->getLimit() . " ";
        }
        $sql = "SELECT
						e.`id`
					FROM
						{$tables}
					WHERE
						{$where_s}";
        $result = $this->db->query($sql);
        if ($this->db->isError)
            throw new FDException("MySQL query error");
        if ($this->db->num_rows() > 0) {
            while ($row = $this->db->fetch_row($result)) {
                $entity = new Entity($this->db);
                $entity->load($row['id']);
                $entities[] = $entity;
            }
        }
        return $entities;
    }

	public function get($order = null, $per_page = 50, $where = null)  {
		$where_str = "";
		if (!is_null($where)) $where_str .= $where;

		if (trim($where_str) != "" && stripos(strtoupper($where), "WHERE") === false ){
			$where_str = "WHERE ".$where_str;
		}
		$where_str_limit = "";
		if (!is_null($per_page) && ctype_digit((string)$per_page)) {
			$this->pager = new PagerRewrite($this->db);
			$this->pager->UrlStart = getLink();
			$this->pager->RecordsOnPage = $per_page;
			$this->pager->init(static::TABLE.' e', "", trim($where_str));
			$where_str_limit = " ".$this->pager->getLimit()." ";
		}
		$table = self::TABLE.' e ';
		/*$table = self::TABLE.' e LEFT JOIN app_shippers s ON s.id = e.shipper_id';
		$table .= ' LEFT JOIN app_locations o ON o.id = e.origin_id';
		$table .= ' LEFT JOIN app_locations d ON d.id = e.destination_id';
		$table .= ' LEFT JOIN app_vehicles v ON v.entity_id = e.id';
		$where_str .= ' GROUP BY e.id ';
		*/
		if (!is_null($order)) $where_str .= " ".$order." ".$where_str_limit;
		
		//print $table." ".$where_str;
		//return $this->db->selectRows("e.`id`, o.city as origin, d.city as destination, s.lname as shipper, e.est_ship_date as avail, SUM(v.tariff) as tariff", $table, $where_str);
		return $this->db->selectRows("e.`id`", $table, $where_str);
	}

public function getEntities($type, $order = "", $status, $per_page, $followup = false,$extra = 0)
    {

        if (!array_key_exists((int)$type, Entity::$type_name))
            throw new FDException("Invalid entity type");

        if (!array_key_exists((int)$status, Entity::$status_name))
           throw new FDException("Invalid entity status");

        //$where = "e.`type` = {$type} AND e.`deleted` = 0 AND " . $this->getPermissionCondition($type, false, 'e') . " AND e.`status` = " . (int)$status;

        $where = "e.`type` = {$type} AND e.`deleted` = 0 " . " AND e.`status` = " . (int)$status;
		
		if($extra==2 && $status==1)
		{
			$where .= " AND e.lead_type = 1  AND e.creator_id != e.assigned_id";
		}
		elseif($extra==1 && $status==1)
		{
			$where .= " AND e.`lead_type` = ".$extra."  AND e.creator_id = e.assigned_id  ";
		}
		else
		  if($status==1 && $type!=3 && $type!=2 )
		      $where .= " AND e.`lead_type` = ".$extra;
			  
		/*
		if($extra==1)
		{
			$where .= " AND e.`lead_type` = ".$extra;
		}
		else
		  if($status!=3 && $status!=2 && $status!=-1 && $type!=3  && $type!=2)
		    $where .= " AND e.`lead_type` = ".$extra;
        */
        if ($followup) {
           $where .= " AND e.`id` IN (SELECT `entity_id` FROM " . FollowUp::TABLE . " WHERE `followup` = '" . date("Y-m-d") . "')";
        }

        if (isset($_SESSION['view_id']) && ($_SESSION['view_id'] != -1)) {
           $where .= " AND (e.`assigned_id` = '" . (int)$_SESSION['view_id'] . "' OR  e.`creator_id` ='".(int)$_SESSION['view_id'] . "')";
		   $where .= " AND ".$this->getPermissionCondition($type, false, 'e');
		   
        } else {
            //$where .= " AND e.`assigned_id` IN (" . implode(', ', Member::getCompanyMembers($this->db, $_SESSION['member']['parent_id'])) . ")";
			//$where .= " OR e.`creator_id` ='".$_SESSION['member_id'] . "'";
			$where .= " AND (".$this->getPermissionCondition($type, false, 'e')." OR e.`creator_id` ='".$_SESSION['member_id'] . "')"; 
        }
		
		
//print $where;
//exit;
        $rows = $this->get($order, $per_page, $where);
        $entities = array();
        foreach ($rows as $i => $row) {
           $entity = new Entity($this->db);
           $entity->load($row['id']);
           $entities[] = $entity;
        }
       return $entities;
    }

/*
    public function getEntities($type, $order = "", $status, $per_page, $followup = false)
    {
        if (!array_key_exists((int)$type, Entity::$type_name))
            throw new FDException("Invalid entity type");
        if (!array_key_exists((int)$status, Entity::$status_name))
            throw new FDException("Invalid entity status");
        //$where = "e.`type` = {$type} AND e.`deleted` = 0 AND " . $this->getPermissionCondition($type, false, 'e') . " AND e.`status` = " . (int)$status;
        $where = "e.`type` = {$type} AND e.`deleted` = 0 " . " AND e.`status` = " . (int)$status;
        if ($followup) {
            $where .= " AND e.`id` IN (SELECT `entity_id` FROM " . FollowUp::TABLE . " WHERE `followup` = '" . date("Y-m-d") . "')";
        }

        if (isset($_SESSION['view_id']) && ($_SESSION['view_id'] != -1)) {
            $where .= " AND e.`assigned_id` = '" . (int)$_SESSION['view_id'] . "' ";
        } else {
            $where .= " AND e.`assigned_id` IN (" . implode(', ', Member::getCompanyMembers($this->db, $_SESSION['member']['parent_id'])) . ")";
        }

        $where .= " AND ".$this->getPermissionCondition($type, false, 'e');


        $rows = $this->get($order, $per_page, $where);
        $entities = array();
        foreach ($rows as $i => $row) {
            $entity = new Entity($this->db);
            $entity->load($row['id']);
            $entities[] = $entity;
        }
        return $entities;
    }
*/
    public function getAllEntities($type, $order = "", $per_page)
    {
        if (!array_key_exists((int)$type, Entity::$type_name))
            throw new FDException("Invalid entity type");
        $where = "e.`type` = {$type} AND e.`deleted` = 0 AND e.`status` = " . (int)Entity::STATUS_POSTED;
        $where .= " AND e.assigned_id = m.id AND m.parent_id = d.owner_id AND d.hide_orders = 0";
        $where_str_limit = "";
        if (!is_null($per_page) && ctype_digit((string)$per_page)) {
            $this->pager = new PagerRewrite($this->db);
            $this->pager->UrlStart = getLink();
            $this->pager->RecordsOnPage = $per_page;
            $this->pager->init(Entity::TABLE . " e, " . DefaultSettings::TABLE . " d, " . Member::TABLE . " m", "", trim($where));
            $where_str_limit = " " . $this->pager->getLimit() . " ";
        }
        $where .= $order . $where_str_limit;
        $rows = $this->db->selectRows('e.id', Entity::TABLE . " e, " . DefaultSettings::TABLE . " d, " . Member::TABLE . " m", "WHERE " . $where);
        $entities = array();
        foreach ($rows as $row) {
            $entity = new Entity($this->db);
            $entity->loadForeignEntity($row['id']);
            $entities[] = $entity;
        }
        return $entities;
    }


public function getArrData($order = null, $per_page = 50, $where = null)  {

		$where_str = "";

		if (!is_null($where)) $where_str .= $where;



		if (trim($where_str) != "" && stripos(strtoupper($where), "WHERE") === false ){

			$where_str = "WHERE ".$where_str;

		}

		$where_str_limit = "";

		if (!is_null($per_page) && ctype_digit((string)$per_page)) {

			$this->pager = new PagerRewrite($this->db);

			$this->pager->UrlStart = getLink();

			$this->pager->RecordsOnPage = $per_page;

			$this->pager->init(static::TABLE.' e', "", trim($where_str));

			$where_str_limit = " ".$this->pager->getLimit()." ";

		}

		$table = self::TABLE.' e LEFT JOIN app_shippers s ON s.id = e.shipper_id';

		$table .= ' LEFT JOIN app_locations o ON o.id = e.origin_id';

		$table .= ' LEFT JOIN app_locations d ON d.id = e.destination_id';
		
		$table .= ' LEFT JOIN members m ON m.id = e.assigned_id';

		//$table .= ' LEFT JOIN app_vehicles v ON v.entity_id = e.id';

		$where_str .= ' GROUP BY e.id ';

		if (!is_null($order)) $where_str .= " ".$order." ".$where_str_limit;
     
	 /*print "select e.*,
  s.fname as s_fname,
  s.lname as s_lname,
  s.company as s_company,
  s.email as s_email,
  s.phone1 as s_phone1,
  s.city as s_city,
  s.state as s_state,
  s.zip as s_zip,
  s.country as s_country ,
 
  o.city as o_city,
  o.state as o_state,
  o.zip as o_zip,
  o.country as o_country, 
 
  d.city as d_city,
  d.state as d_state,
  d.zip as d_zip,
  d.country as d_country,
  m.contactname as contactname from ". $table." ".$where_str;
  */
		return $this->db->selectRows("e.*,
  s.fname as s_fname,
  s.lname as s_lname,
  s.company as s_company,
  s.email as s_email,
  s.phone1 as s_phone1,
  s.city as s_city,
  s.state as s_state,
  s.zip as s_zip,
  s.country as s_country ,
 
  o.city as o_city,
  o.state as o_state,
  o.zip as o_zip,
  o.country as o_country, 
 
  d.city as d_city,
  d.state as d_state,
  d.zip as d_zip,
  d.country as d_country,
  m.contactname as contactname
 
									 ", $table, $where_str);

	}



    public function getEntitiesArrData($type, $order = "", $status, $per_page, $followup = false,$extra = 0)
    {

        if (!array_key_exists((int)$type, Entity::$type_name))
            throw new FDException("Invalid entity type");

        if (!array_key_exists((int)$status, Entity::$status_name))
           throw new FDException("Invalid entity status");

       
        $where = "e.`type` = {$type} AND e.`deleted` = 0 " . " AND e.`status` = " . (int)$status;
		
		if($extra==1 && $status==1)
		{
			$where .= " AND e.`lead_type` = ".$extra;
		}
		else
		  if($status==1 && $type!=3 && $type!=2 )
		      $where .= " AND e.`lead_type` = ".$extra;

        if ($followup) {
           $where .= " AND e.`id` IN (SELECT `entity_id` FROM " . FollowUp::TABLE . " WHERE `followup` = '" . date("Y-m-d") . "')";
        }

        if (isset($_SESSION['view_id']) && ($_SESSION['view_id'] != -1)) {
           //$where .= " AND e.`assigned_id` = '" . (int)$_SESSION['view_id'] . "' ";
		   $where .= " AND (e.`assigned_id` = '" . (int)$_SESSION['view_id'] . "' OR  e.`creator_id` ='".(int)$_SESSION['view_id'] . "')";
		   $where .= " AND ".$this->getPermissionCondition($type, false, 'e');
        } else {
           // $where .= " AND e.`assigned_id` IN (" . implode(', ', Member::getCompanyMembers($this->db, $_SESSION['member']['parent_id'])) . ")";
		   $where .= " AND (".$this->getPermissionCondition($type, false, 'e')." OR e.`creator_id` ='".$_SESSION['member_id'] . "')"; 
        }
		
		 
		
		//$where .= " AND ".$this->getPermissionCondition($type, false, 'e');
       $rows = $this->getArrData($order, $per_page, $where);
		
        $entities = array();
        
       return $rows;
    }

    /**
     * Get not archived entities
     * @param $assigned_ids
     * @return array
     */
    public function getNotArchived($assigned_ids)
    {

        $where = " `deleted` = 0 AND `status` <> '" . Entity::STATUS_ARCHIVED . "' ";
        $where .= " AND `assigned_id` IN('" . implode("','", $assigned_ids) . "') ";

        $rows = parent::get(null, null, $where);
        $entities = array();

        foreach ($rows as $row) {
            $entity = new Entity($this->db);
            $entity->load($row['id']);
            $entities[] = $entity;
        }
        return $entities;
    }

    /**
     * Get not first followupped quotes
     * @param type $assigned_ids
     * @return \Entity
     */

    public function getNotFirstFollowUpped($assigned_ids)
    {

        $where = " `deleted` = 0 AND `status` <> '" . Entity::STATUS_ARCHIVED . "' ";
        $where .= " AND `type` = '" . Entity::TYPE_QUOTE . "' ";
        $where .= " AND `is_firstfollowup` <> '" . Entity::FIRSTFOLLOWUPPED_YES . "' ";
        $where .= " AND `assigned_id` IN('" . implode("','", $assigned_ids) . "') ";

        $rows = parent::get(null, null, $where);
        $entities = array();

        foreach ($rows as $row) {
            $entity = new Entity($this->db);
            $entity->load($row['id']);
            $entities[] = $entity;
        }
        return $entities;
    }


    /**
     * @param array $assigned_ids
     * @return array $entities
     */
    public function getAssumedDelivered($assigned_ids)
    {
        $where = "`deleted` = 0 AND `status` NOT IN (" . Entity::STATUS_DELIVERED . "," . Entity::STATUS_ARCHIVED . ")";
        $where .= " AND `assigned_id` IN (" . implode(",", $assigned_ids) . ")";

        $rows = parent::get(null, null, $where);
        $entities = array();

        foreach ($rows as $row) {
            $entity = new Entity($this - db);
            $entity->load($row['id']);
            $entities[] = $entity;
        }
        return $entities;
    }

    public function getNotQuotedLeads($assigned_ids)
    {

        $where = " `deleted` = 0 AND `status` <> '" . Entity::STATUS_ARCHIVED . "' AND `status` <> '" . Entity::STATUS_UNREADABLE . "' ";
        $where .= " AND `type` = '" . Entity::TYPE_LEAD . "' ";
        $where .= " AND `assigned_id` IN('" . implode("','", $assigned_ids) . "') ";

        $rows = parent::get(null, null, $where);
        $entities = array();

        foreach ($rows as $row) {
            $r = $this->db->selectRow("COUNT(*) as cnt", "app_vehicles", "WHERE `tariff` = 0 AND `entity_id` = " . $row['id'] . " AND `deleted` = 0");
            if ($r['cnt'] >= 0) {
                $entity = new Entity($this->db);
                $entity->load($row['id']);
                $entities[] = $entity;
            }
        }
        return $entities;
    }

    public function getDispatchedTo($order = "", $status, $per_page)
    {
        if (!array_key_exists((int)$status, Entity::$status_name)) throw new FDException("Invalid entity status");
        $where = "`type` = " . Entity::TYPE_ORDER . " AND `deleted` = 0 AND `carrier_id` IN (" . implode(",", Member::getCompanyMembers($this->db, $_SESSION['member']['parent_id'])) . ") AND `status` = " . (int)$status;
        if (isset($_SESSION['view_id']) && ($_SESSION['view_id'] != -1)) {
            $where .= " AND `carrier_id` = " . (int)$_SESSION['view_id'] . " ";
        }
        $rows = parent::get($order, $per_page, $where);
        $entities = array();
        foreach ($rows as $row) {
            $entity = new Entity($this->db);
            $entity->load($row['id']);
            $entities[] = $entity;
        }
        return $entities;
    }

    public function getFollowupQuotes($order = null, $per_page = 100)
    {
        $where = "WHERE `deleted` = 0 AND " . $this->getPermissionCondition(Entity::TYPE_QUOTE, false) . " AND `id` IN (SELECT `entity_id` FROM `app_followups` WHERE `followup` = '" . date("Y-m-d") . "')";
        if (isset($_SESSION['view_id'])) {
            if ($_SESSION['view_id'] != -1) {
                $where .= " AND `assigned_id` = " . (int)$_SESSION['view_id'] . " ";
            }
        } else {
            $where .= " AND `assigned_id` = " . (int)$_SESSION['member_id'] . " ";
        }
        $rows = parent::get($order, $per_page, $where);
        if (!is_array($rows))
            throw new FDException("MySQL query error");
        $entities = array();
        foreach ($rows as $row) {
            $entity = new Entity($this->db);
            $entity->load($row['id']);
            $entities[] = $entity;
        }
        return $entities;
    }

    /**
     * --Reports--
     * Quotes Report
     * Slide 35
     * Quotes (Quote ID, Assigned To, Before Quote (who it was assigned before), Quote date, Quote time, Pickup State, Pickup Zip code, Pickup City, Dropoff State, Dropoff Zip Code, Dropoff City, Estimated Ship date, Inop (Yes/No), Ship Via, First Name, Last Name, Company, Email, Phone, Alt. Phone, Cell, Fax, Address Line 1, Address Line 2, City, State, Zip, Country, Referrer, Lead Source, Vehicles Year, Make, Model, Year, Type, Tariff, Deposit Required).
     * Thisreport displays all quotes and can be additionally filtered by Quote ID, Assigned To, First + Last Name, Email or Phone Number.
     * @param string $order
     * @param int $per_page
     * @param array $report_arr
     * @param int $owner_id
     * @return array with Quotes objects
     */
    final public function getQuotesReport($order = null, $per_page = 100, $report_arr = array(), $owner_id)
    {

        // shipper query
        $shipper_where = array();
        if (trim($report_arr["ship_via"]) != "") {
            $ship_via = mysqli_real_escape_string($this->db->connection_id, trim($report_arr["ship_via"]));
            $shipper_where[] = " 
                (fname LIKE '%" . $ship_via . "%' OR
                lname LIKE '%" . $ship_via . "%' OR
                CONCAT_WS(' ', 'fname', 'lname') LIKE '%" . $ship_via . "%' OR
                company LIKE '%" . $ship_via . "%')";
        }
        if (trim($report_arr["email"]) != "") {
            $email = mysqli_real_escape_string($this->db->connection_id, trim($report_arr["email"]));
            $shipper_where[] = "(`email` LIKE '%" . $email . "%')";
        }
        if (trim($report_arr["phone"]) != "") {
            $phone = mysqli_real_escape_string($this->db->connection_id, trim($report_arr["phone"]));
            $shipper_where[] = "
                (`phone1` LIKE '%" . $phone . "%' OR
                `phone2` LIKE '%" . $phone . "%' OR
                `mobile` LIKE '%" . $phone . "%' OR
                `fax` LIKE '%" . $phone . "%')";
        }

        $users = array();
        $members = Member::getCompanyMembers($this->db, $owner_id);

        if (count($report_arr["users_ids"]) > 0) {
            foreach ($report_arr["users_ids"] as $value) {
                if (in_array($value, $members)) {
                    $users[] = $value;
                }
            }
        }

        $where = "`assigned_id` IN('" . implode(",", $users) . "')
                        AND `deleted` = 0
                        AND `created` >= '" . $report_arr["start_date"] . "'
                        AND `created` <= '" . $report_arr["end_date"] . "'
                        AND `type` = '" . Entity::TYPE_QUOTE . "'
        ";

        //Quote ID
        if (trim($report_arr["quote_id"]) != "") {
            $where .= " AND `id` LIKE '%" . mysqli_real_escape_string($this->db->connection_id, $report_arr["quote_id"]) . "%'";
        }

        //get shippers ids and add where string
        if (count($shipper_where) > 0) {
            $swhere = implode(" AND ", $shipper_where);
            $shippers = Shipper::getShippers($this->db, $swhere);
            $where .= " AND `shipper_id` IN('" . implode(",", $shippers) . "')";
        }

        $data = array();
        $rows = parent::get($order, $per_page, $where);
        foreach ($rows as $row) {
            $entity = new Entity($this->db);
            $entity->load($row['id']);
            $data[$row['id']] = $entity;
        }
        return $data;
    }

    /**
     * --Reports--
     * Orders Report
     * Slide 34
     * (Order ID, Order date, Delivery date, Description, Shipper, Carrier, Status (New, Posted to Board, Dispatched – Not Signed, Dispatched – Signed, Picked Up, Delivered, Assumed Delivered, Cancelled), Reimbursable (Yes/No), Assigned To, Cost (=Carrier Pay + Terminal Fee), Total Price (=Tariff).
     * This report can be additional filtered by Order ID and Shipper and also exclude all cancelled orders.
     * @param string $order
     * @param int $per_page
     * @param array $report_arr
     * @param int $owner_id
     * @return array with Orders objects
     */
    final public function getOrdersReport($order = null, $per_page = 100, $report_arr = array(), $owner_id = null)
    {

        // shipper query
        $shipper_where = array();
        //shippers where
        if (isset($report_arr["ship_via"]) && trim($report_arr["ship_via"]) != "") {
            $ship_via = mysqli_real_escape_string($this->db->connection_id, trim($report_arr["ship_via"]));
            $shipper_where[] = " 
                (fname LIKE '%" . $ship_via . "%' OR
                lname LIKE '%" . $ship_via . "%' OR
                CONCAT_WS(' ', 'fname', 'lname') LIKE '%" . $ship_via . "%' OR
                company LIKE '%" . $ship_via . "%')";
        }
        //carriers where
        $carrier_where = array();
        if (isset($report_arr["carrier_name"]) && trim($report_arr["carrier_name"]) != "") {
            $carrier_name = mysqli_real_escape_string($this->db->connection_id, trim($report_arr["carrier_name"]));
            $carrier_where[] = " 
                is_carrier = 1 AND 
                (first_name LIKE '%" . $carrier_name . "%' OR
                last_name LIKE '%" . $carrier_name . "%' OR
                CONCAT_WS(' ', 'first_name', 'last_name') LIKE '%" . $carrier_name . "%' OR
                company_name LIKE '%" . $carrier_name . "%')";
        }

        if (isset($report_arr["email"]) && trim($report_arr["email"]) != "") {
            $email = mysqli_real_escape_string($this->db->connection_id, trim($report_arr["email"]));
            $shipper_where[] = "(`email` LIKE '%" . $email . "%')";
        }
        if (isset($report_arr["phone"]) && trim($report_arr["phone"]) != "") {
            $phone = mysqli_real_escape_string($this->db->connection_id, trim($report_arr["phone"]));
            $shipper_where[] = "  
                (`phone1` LIKE '%" . $phone . "%' OR
                `phone2` LIKE '%" . $phone . "%' OR
                `mobile` LIKE '%" . $phone . "%' OR
                `fax` LIKE '%" . $phone . "%')";
        }

        $users = array();
        $members = Member::getCompanyMembers($this->db, $owner_id);

        if (count($report_arr["users_ids"]) > 0) {
            foreach ($report_arr["users_ids"] as $value) {
                if (in_array($value, $members)) {
                    $users[] = $value;
                }
            }
        }

        $where = "`assigned_id` IN('" . implode(",", $users) . "')
                        AND `deleted` = 0
                        AND `created` >= '" . $report_arr["start_date"] . "'
                        AND `created` <= '" . $report_arr["end_date"] . "'
                        AND `type` = '" . Entity::TYPE_ORDER . "'
        ";

        //Order ID
        if (trim($report_arr["order_id"]) != "") {
            $where .= " AND `id` LIKE '%" . mysqli_real_escape_string($this->db->connection_id, $report_arr["order_id"]) . "%'";
        }

        // order status
        if (isset($report_arr["status_id"]) && trim($report_arr["status_id"]) != "") {
            $where .= " AND `status` = '" . (int)$report_arr["status_id"] . "'";
        }

        //get shippers ids and add where string
        if (count($shipper_where) > 0) {
            $swhere = implode(" AND ", $shipper_where);
            $shippers = Shipper::getShippers($this->db, $swhere);
            $where .= " AND `shipper_id` IN('" . implode(",", $shippers) . "')";
        }

        //get carriers ids and add where string
        if (count($carrier_where) > 0) {
            $swhere = implode(" AND ", $carrier_where);
            $carriers = Account::getAccounts($this->db, $swhere);
            $where .= " AND `carrier_id` IN('" . implode(",", $carriers) . "')";
        }

        $data = array();
        $rows = parent::get($order, $per_page, $where);
        foreach ($rows as $row) {
            $entity = new Entity($this->db);
            $entity->load($row['id']);
            $data[$row['id']] = $entity;
        }
        return $data;
    }

    /**
     * --Reports--
     * Accounts Payable/Receivable
     * Slide 35
     * Order information (Order ID, Order date, Carrier, Shipper, Origin, Destination, ETD); Original Order Terms (Tariff, Carrier Pay, Terminal Fee (Pickup on top, Delivery on bottom), Deposit, COD, Profit); Accounts Receivable (From Shipper, From Broker (do not display this option for Brokers accounts), From Carrier, From Pickup Terminal, From Drop-off Terminal), Accounts Payable (To Shipper, To Broker (do not display this option for Brokers accounts), To Carrier, To Pickup Terminal, To Drop-off Terminal).
     * @param string $order
     * @param int $per_page
     * @param array $report_arr
     * @param int $owner_id
     * @return array with report data
     */
    final public function getAccountsReport($order = null, $per_page = 100, $report_arr = array(), $owner_id = null)
    {

        $users = array();
        $members = Member::getCompanyMembers($this->db, $owner_id);

        if (count($report_arr["users_ids"]) > 0) {
            foreach ($report_arr["users_ids"] as $value) {
                if (in_array($value, $members)) {
                    $users[] = $value;
                }
            }
        }

        $where = "`assigned_id` IN('" . implode(",", $users) . "')
                        AND `deleted` = 0
                        AND `created` >= '" . $report_arr["start_date"] . "'
                        AND `created` <= '" . $report_arr["end_date"] . "'
                        AND `type` = '" . Entity::TYPE_ORDER . "'
        ";

        //  Include orders that are not dispatched
        if (!isset($report_arr["include_orders"]) || trim($report_arr["include_orders"]) != "1") {
            $where .= " AND `status` = '" . Entity::STATUS_DISPATCHED . "'";
        }

        $data = array();
        $rows = parent::get($order, $per_page, $where);
        foreach ($rows as $row) {
            $entity = new Entity($this->db);
            $entity->load($row['id']);
            $data[$row['id']] = $entity;
        }
        return $data;
    }


    /**
     * --Reports--
     * Return QTY Leads, Quotes, Orders, Conversion Rate (Order # : Quote #)
     * by date intervals
     * for Reports -> DashBoard
     * Doc: Slide 34 (Dashboard should graphically interpret Sales report below by default for Current Month, and with ability to change time frames.)
     * If date range is Year or Quarter - print out a monthly basis
     * If a period of more than one year to display a years basis
     *
     * @param int $parent_id
     * @param datetime $start_date
     * @param datetime $end_date
     * @param array $users_ids
     * @param bool $define_as
     * @return array for Build Graph
     */
    final public function getDashBoardSales($parent_id, $start_date, $end_date, $users_ids, $define_as)
    {

        $leads = array(); /* Qty leads by period  */
        $quotes = array(); /* Qty quotes by period */
        $orders = array(); /* Qty orders by period */
        $ticks = array(); /* X-axis labels */
        /* Get ticks and range */
        $tr = $this->prepareDateRanges($start_date, $end_date);
        /* Get qty leads, quotes, orders for periods */
        foreach ($tr["periods"] as $key => $value) {
            $conv_rate = 0; /* Conversion Rate (Order # : Quote #) */
            $sales = $this->getSalesReportByPeriod($parent_id, $value["start"], $value["end"], $users_ids, $define_as);
            if ($sales["qty_q"] > 0) {
                $conv_rate = ($sales["qty_o"] / $sales["qty_q"]) * 100; /* Calc Conv. Rate */
            }
            /* Fill arrays */
            $leads[] = $sales["qty_l"];
            $quotes[] = $sales["qty_q"];
            $orders[] = $sales["qty_o"];
            $ticks[] = $tr["ticks"][$key] . "<br />" . $conv_rate . "%"; /* X-axis label */
        }
        /* Return Sales Data */
        return array(
            "leads" => $leads
        , "quotes" => $quotes
        , "orders" => $orders
        , "ticks" => $ticks
        , "range" => $tr["range"]
        );
    }

    /**
     * --Reports--
     * Show userfriendly Date period for Dashboard Graph X-axis
     * @param datetime $start_date
     * @param datetime $end_date
     * @return array
     * 1) string range  'Jan, 2011 - Feb, 2011', '2011', 'Jan, 2012'
     * 2) ticks - labels for X-axis
     * 3) datetime periods of ticks
     */
    private function prepareDateRanges($start_date, $end_date)
    {
        $out = ""; /* String range */
        $ticks = array(); /* Ranges for X-axis */
        $periods = array(); /* list of periods for ticks */
        /* Check monthes range */
        $d1 = explode("-", substr($start_date, 0, 10));
        $d2 = explode("-", substr($end_date, 0, 10));
        $range = ($d2[0] * 12 + $d2[1]) - ($d1[0] * 12 + $d1[1]);
        /* Revert to time */
        $date1 = mktime(0, 0, 0, $d1[1], $d1[2], $d1[0]);
        $date2 = mktime(23, 59, 59, $d2[1], $d2[2], $d2[0]);
        /* Generate arrays */
        if ($range == 0) { /* Several Days */
            if ((int)date("d", $date1) == (int)date("d", $date2)) { /* One date */
                $out = date("m/d/Y", $date1);
            } else {
                if ($d1[2] == "1" && date("d", mktime(0, 0, 0, $d2[1] + 1, 0, $d2[0])) == $d2[2]) { /* Full month */
                    $out = date("M, Y", $date1);
                } else {
                    $out = date("m/d/Y", $date1) . " - " . date("m/d/Y", $date2);
                }
            }
            $ticks = array($out);
            $periods[] = array(
                "start" => date("Y-m-d H:i:s", $date1)
            , "end" => date("Y-m-d H:i:s", $date2)
            );
        } elseif ($range <= 12) { /* Several Monthes */
            $out = date("M, Y", $date1) . " - " . date("M, Y", $date2);
            /* Get Ticks strings */
            for ($i = 0; $i <= $range; $i++) {
                $ticks[] = date("M,Y", mktime(0, 0, 0, $d1[1] + $i, $d1[2], $d1[0]));
                $periods[] = array(
                    "start" => date("Y-m-d H:i:s", mktime(0, 0, 0, $d1[1] + $i, $d1[2], $d1[0])) //1st day of month
                , "end" => date("Y-m-d H:i:s", mktime(23, 59, 59, $d1[1] + $i + 1, 0, $d1[0])) //last day of month
                );
            }
        } else { /* Several Years */
            $out = date("Y", $date1) . " - " . date("Y", $date2);
            /* Get Ticks strings */
            for ($i = date("Y", $date1); $i <= date("Y", $date2); $i++) {
                $ticks[] = date("Y", mktime(0, 0, 0, 1, 1, $i));
                $periods[] = array(
                    "start" => date("Y-m-d H:i:s", mktime(0, 0, 0, 1, 1, $i)) //1st day of year
                , "end" => date("Y-m-d H:i:s", mktime(23, 59, 59, 1, 0, $i + 1)) //last day of year
                );
            }
        }
        return array("ticks" => $ticks
        , "range" => $out
        , "periods" => $periods
        );
    }

    /**
     * --Reports--
     * Return array with leads, quotes, orders by Period
     * @param int $owner_id
     * @param string $start_date
     * @param string $end_date
     * @param array $users_ids
     * @param bool $define_as:
     * false - Orders that were created during selected time period and dispatched at any time.
     * true - Orders that were created during any time period and dispatched during selected time period.
     * @return array QTY leads, quotes, orders
     */
    private function getSalesReportByPeriod($owner_id, $start_date, $end_date, $users_ids, $define_as)
    {
        /* Set zero by Defaults */
        $ret = array(
            "qty_l" => 0
        , "qty_q" => 0
        , "qty_o" => 0
        );
        /* Revert Dates in time */
        $sd = explode("-", substr($start_date, 0, 10));
        $ed = explode("-", substr($end_date, 0, 10));
        $start_date_t = mktime(0, 0, 0, $sd[1], $sd[2], $sd[0]);
        $end_date_t = mktime(23, 59, 59, $ed[1], $ed[2], $ed[0]);

        /* Get members list */
        $member = new Member();
        $members = $member->getCompanyMembers($this->db, $owner_id);
        $users = array();
        /* Validate if $_POST user ID is in Company Members */
        foreach ($users_ids as $key => $value) {
            if (in_array($value, $members)) {
                $users[] = $value;
            }
        }
        /* Build where */
        $where = "`assigned_id` IN('" . implode("','", $users) . "')
				  AND e.`deleted` = 0
        ";

        /* Add where for type 'define_as' for orders */
        if ($define_as) {
            /* Orders that were created during any time period and dispatched during selected time period. */
            $where .= "
                AND (
                        (
                            e.`type` = '" . Entity::TYPE_ORDER . "'
                            AND e.status = '" . Entity::STATUS_DISPATCHED . "'
                            AND e.`dispatched` >= '" . $start_date . "'
                            AND e.`dispatched` <= '" . $end_date . "'
                        ) OR (
                                e.`created` >= '" . $start_date . "'
                            AND e.`created` <= '" . $end_date . "'
                        )
                    )";
        } else {
            /*  Orders that were created during selected time period and dispatched at any time. */
            $where .= " 
                        AND e.`created` >= '" . $start_date . "'
                        AND e.`created` <= '" . $end_date . "'
            ";
        }
        /* Get Entities */
        $em = new EntityManager($this->db);
        $rows = $em->get(NULL, NULL, $where);
        foreach ($rows as $row) {
            $entity = new Entity($this->db);
            $entity->load($row['id']);

            if ($entity->type == Entity::TYPE_LEAD) {
                $ret["qty_l"]++;
            }
            if ($entity->type == Entity::TYPE_QUOTE) {
                $ret["qty_q"]++;
                $ret["qty_l"]++;
            }
            if ($entity->type == Entity::TYPE_ORDER) {
                $ret["qty_o"]++;
                if ($entity->status == Entity::STATUS_DISPATCHED) {
                    /* Check Leads for Orders that were created during any time period and dispatched during selected time period. */
                    $ct = explode("-", substr($entity->created, 0, 10));
                    $created_t = mktime(0, 0, 0, $ct[1], $ct[2], $ct[0]);

                    if ($define_as == 1 && ($created_t < $start_date_t || $created_t > $end_date_t)) {
                        //Orders that were created during any time period and dispatched during selected time period.
                    } else {
                        //Orders that were created during selected time period and dispatched at any time.
                        $ret["qty_l"]++; /* Add to leads if created in selected periods */
                    }
                } else {
                    $ret["qty_l"]++;
                }
            }
        }
        return $ret;
    }

    /**
     * --Reports--
     * Return Data for Sales Report and Lead Sources Report
     * for each LeadSource / Member
     * Slide 34: Sales report (User, All Leads, All Quotes, All Orders, Conversion Rate (Oder # : Quote #), Dispatched Orders (quantity), Tariffs, Carrier Pay, Terminal Fee (Pickup on top, Delivery on bottom), Gross Profit (Tariff – Carrier Pay – Terminal Fee), Profit Margin % (Gross Profit : Tariffs),  Average Profit per Order (Gross Profit : Dispatched Orders).
     * @param int $owner_id
     * @param datetime $start_date
     * @param datetime $end_date
     * @param int $source_id Member ID / Lead Source ID
     * @param string $field 'assigned_id' - for Sales OR 'source_id' - for LeadSourses Report
     * @param bool $define_as
     * @return array  with Sales Data
     */
    final public function getSalesReport($owner_id, $start_date, $end_date, $source_id, $field, $define_as = false)
    {

        $lqo = array( // Set defauls by zero
            "leads" => 0,
            "quotes" => 0,
            "orders" => 0,
            "conv_rate" => 0,
            "dispatched" => 0,
            "tariffs" => 0,
            "carrier_pay" => 0,
            "terminal_feesP" => 0,
            "terminal_feesD" => 0,
            "gross_profit" => 0,
            "profit_margin" => 0,
            "average_profit" => 0,
        );

        $member = new Member();
        $members = $member->getCompanyMembers($this->db, $owner_id);
        $where = "`assigned_id` IN(" . implode(",", $members) . ")
				  AND `deleted` = 0
				  AND " . $field . "='" . (int)$source_id . "'
        ";

        /* Revert Dates in time */
        $sd = explode("-", substr($start_date, 0, 10));
        $ed = explode("-", substr($end_date, 0, 10));
        $start_date_t = mktime(0, 0, 0, $sd[1], $sd[2], $sd[0]);
        $end_date_t = mktime(23, 59, 59, $ed[1], $ed[2], $ed[0]);

        // Add where for type 'define_as' for orders
        if ($define_as) {
            // Orders that were created during any time period and dispatched during selected time period.
            $where .= "
                AND (
                        (
                            `type` = '" . Entity::TYPE_ORDER . "'
                            AND status = '" . Entity::STATUS_DISPATCHED . "'
                            AND `dispatched` >= '" . $start_date . "'
                            AND `dispatched` <= '" . $end_date . "'
                        ) OR (
                                `created` >= '" . $start_date . "'
                            AND `created` <= '" . $end_date . "'
                        )
                    )";
        } else {
            //  Orders that were created during selected time period and dispatched at any time.
            $where .= " 
                        AND `created` >= '" . $start_date . "'
                        AND `created` <= '" . $end_date . "'
            ";
        }

        //New query
        $rows = $this->db->selectRows("*", Entity::TABLE, "WHERE ".$where);

        $ret["qty_l"] = 0;
        foreach ($rows as $row) {
            $lqo["tariffs"] += (float)$row["total_tariff_stored"];
            if ($row["type"] == Entity::TYPE_LEAD) {
                $lqo["leads"]++;
            }
            if ($row["type"] == Entity::TYPE_QUOTE) {
                $lqo["quotes"]++;
                $lqo["leads"]++;
            }
            if ($row["type"] == Entity::TYPE_ORDER) {
                $lqo["orders"]++;
                $lqo["carrier_pay"] += (float)$row["carrier_pay_stored"];
                $lqo["terminal_feesP"] += (float)$row["pickup_terminal_fee"];
                $lqo["terminal_feesD"] += (float)$row["dropoff_terminal_fee"];

                if ($row["status"] == Entity::STATUS_DISPATCHED) {
                    // Check Leads for Orders that were created during any time period and dispatched during selected time period.
                    $ct = explode("-", substr($row["created"], 0, 10));
                    $created_t = mktime(0, 0, 0, $ct[1], $ct[2], $ct[0]);

                    if ($define_as == 1 && ($created_t < $start_date_t || $created_t > $end_date_t)) {
                        //Orders that were created during any time period and dispatched during selected time period.
                    } else {
                        //Orders that were created during selected time period and dispatched at any time.
                        $lqo["leads"]++; // Add to leads if created in selected periods 
                    }

                    if ($row["status"] == Entity::STATUS_DISPATCHED) {
                        $lqo["dispatched"]++;
                    }
                } else {
                    $ret["qty_l"]++;
                }
            }
        }

        //calculate report rows
        if ($lqo["quotes"] > 0) {
            $lqo["conv_rate"] = ($lqo["orders"] / $lqo["quotes"]) * 100;
        }
        $lqo["gross_profit"] = $lqo["tariffs"] - $lqo["carrier_pay"] - $lqo["terminal_feesP"] - $lqo["terminal_feesD"];

        if ($lqo["tariffs"] > 0) {
            $lqo["profit_margin"] = ceil(($lqo["gross_profit"] / $lqo["tariffs"])) * 100;
        }

        if ($lqo["dispatched"] > 0) {
            $lqo["average_profit"] = $lqo["gross_profit"] / $lqo["dispatched"];
        }
        return $lqo;
    }
	
	
public function searchAll($type, $search_type, $value, $per_page = 50, $order = null)
 {
	 
        if (!array_key_exists($type, Entity::$type_name)) throw new FDException("Invalid entity type");

        $entities = array();
      
	     $searchOther = 1;
		 
		 if(in_array("route",$search_type))
		 {
			 $value = mysqli_real_escape_string($this->db->connection_id, $value);
			 $routeStates = explode('-', $value);
			 if (count($routeStates) != 2) {
				 
				 $searchOther = 1;
			 
			 } else {
						$searchOther = 0;
						
						$tables .= self::TABLE . " e    LEFT JOIN " . Origin::TABLE . " o ON (e.`origin_id` = o.`id`)
	
										LEFT JOIN " . Destination::TABLE . " d ON (e.`destination_id` = d.`id`)";
	
						$where .= "e.`deleted` = 0
	
						AND ( o.`state` LIKE('".$routeStates[0]."')
	
						AND d.`state` LIKE('".$routeStates[1]."')) ";
	
					}	
		 }
		     if($searchOther == 1)
			 {
					$sizeType = sizeof($search_type);
					$tables = self::TABLE . " e ";
					$where = " e.`deleted` = 0 AND (  ";	
					$whereTemp = "";
					foreach($search_type as $valueType)
					{
						
						
					   if($valueType == "carrier")
					   {
						   $tables .= " LEFT JOIN `app_dispatch_sheets` ds ON (ds.`entity_id` = e.`id`) ";
						   $whereTemp .= " ds.`carrier_company_name` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
									   OR ds.`carrier_contact_name` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')		
									   OR ds.`c_icc_mc_number` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
									   OR ds.`carrier_email` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
									   OR ds.`carrier_phone_1` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
									   OR ds.`carrier_phone_2` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')";
					   }
					   
					   if($valueType == "origin")
					   {
						   $tables .= " LEFT JOIN " . Origin::TABLE . " o ON (e.`origin_id` = o.`id`) ";
						   if($whereTemp != "")
							 $whereTemp .= "  OR";
						   $whereTemp .= "  o.`address1` LIKE('%{$value}%')
										OR o.`address2` LIKE('%{$value}%')	
										OR o.`city` LIKE('%{$value}%')	
										OR o.`state` LIKE('%{$value}%')	
										OR o.`zip` LIKE('%{$value}%')	
										OR o.`country` LIKE('%{$value}%')";
					   }
					   
					   if($valueType == "destination")
					   {
						   $tables .= " LEFT JOIN " . Destination::TABLE . " d ON (e.`destination_id` = d.`id`) ";
						   if($whereTemp != "")
							 $whereTemp .= "  OR";
						   $whereTemp .= "  d.`address1` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
										OR d.`address2` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
										OR d.`city` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
										OR d.`state` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
										OR d.`zip` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
										OR d.`country` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')";
					   }
					   
					   if($valueType == "vehicle")
					   {
						   $tables .= " LEFT JOIN " . Vehicle::TABLE . " v ON (e.`id` = v.`entity_id`) ";
						   if($whereTemp != "")
							 $whereTemp .= "  OR";
						   $whereTemp .= "  v.`make` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
										OR v.`model` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
										OR v.`type` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')							
										OR v.`vin` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
										OR v.`lot` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
										OR v.`color` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')";
					   }
					   
					   if($valueType == "shippers")
					   {
					       $flnameSTR = "";
						   $flnameArr = explode(" ",$value);
						   if(is_array($flnameArr) && count($flnameArr)>1)
						    {
							  $flnameSTR = "  (s.`fname` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $flnameArr[0])."%')
										AND s.`lname` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $flnameArr[1])."%'))";	
							}
							else{
								$flnameSTR = " s.`fname` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
										OR s.`lname` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')";
							}
					   
						   $tables .= " LEFT JOIN " . Shipper::TABLE . " s ON (e.`shipper_id` = s.`id`) ";
						   if($whereTemp != "")
							 $whereTemp .= "  OR";
						   $whereTemp .= $flnameSTR."
										OR s.`phone1` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
										OR s.`phone2` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
										OR s.`mobile` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
										OR s.`email` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
										OR s.`company` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%') ";
					   }
					   
					   if($valueType == "orderid")
					   {
						   //$tables .= "";
							if($whereTemp != "")
							 $whereTemp .= "  OR";
						   $whereTemp .= " e.`number` = '".mysqli_real_escape_string($this->db->connection_id, $value)."' ";
						   $whereTemp .= " OR e.`id` = '".mysqli_real_escape_string($this->db->connection_id, $value)."' ";
						   
					   }
					   
					   if($valueType == "referred")
					   {
						   if($whereTemp != "")
							 $whereTemp .= "  OR";
						   $whereTemp .= " e.`referred_by` = '".mysqli_real_escape_string($this->db->connection_id, $value)."' ";
						   
					   }
					   
					   
					   if($valueType == "route")
					   {
						   $tables .= "";
						   //if($whereTemp != "")
							 //$whereTemp .= "  OR";
						   $whereTemp .= "";
					   }
					   if($valueType == "notes")
					   {
						   $tables .= " LEFT JOIN " . Note::TABLE . " n ON (e.`id` = n.`entity_id`) ";
						   if($whereTemp != "")
							 $whereTemp .= "  OR";
						   $whereTemp .= " n.`text` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%') ";
					   }
					   
					}
					
					$where .= $whereTemp." )";
					
			 }
					
		if (isset($_SESSION['view_id']) && ($_SESSION['view_id'] != -1)) {
           $where .= " AND e.`assigned_id` = '" . (int)$_SESSION['view_id'] . "' ";
        }

        $where .= " AND e.`type` = {$type} AND " . $this->getPermissionCondition($type, false, "e");

        $where_s = $where . " GROUP BY e.`id`";
		
		

        if ($order instanceof OrderRewrite) {

            $where_s .= " " . $order->getOrder() . " ";

        }

        if (!is_null($per_page) && ctype_digit((string)$per_page)) {

            $this->pager = new PagerRewrite($this->db);

            $this->pager->UrlStart = getLink();

            $this->pager->RecordsOnPage = $per_page;

            $this->pager->init($tables, "DISTINCT(e.`id`)", "WHERE " . $where);

            $where_s .= " " . $this->pager->getLimit() . " ";

        }

        $sql = "SELECT

						e.`id`

					FROM

						{$tables}

					WHERE

						{$where_s}";
//print $sql;
//exit;
        $result = $this->db->query($sql);

        if ($this->db->isError)

            throw new FDException("MySQL query error");

        if ($this->db->num_rows() > 0) {
             $searchData = array();
            while ($row = $this->db->fetch_row($result)) {

                $entity = new Entity($this->db);

                $entity->load($row['id']);

                $entities[] = $entity;
                 
				$searchData[] = $row['id'];
            }
			
			$searchCount = count($searchData);
			if($searchCount>0){
			   $_SESSION['searchData'] = $searchData;
			   $_SESSION['searchCount'] = $searchCount;
			   $_SESSION['searchShowCount'] = 0;
			}

        }

        return $entities;

    }

	
	
	
	/*
	public function searchAll($type, $search_type, $value, $per_page = 50, $order = null)

    {
          
        if (!array_key_exists($type, Entity::$type_name)) throw new FDException("Invalid entity type");

        $entities = array();

        $value = mysqli_real_escape_string($this->db->connection_id, $value);

       $routeStates = explode('-', $value);

				if (count($routeStates) != 2) {

					

                $tables = self::TABLE . " e

									LEFT JOIN " . Shipper::TABLE . " s ON (e.`shipper_id` = s.`id`)

									LEFT JOIN " . Origin::TABLE . " o ON (e.`origin_id` = o.`id`)

									LEFT JOIN " . Destination::TABLE . " d ON (e.`destination_id` = d.`id`)

									LEFT JOIN " . Vehicle::TABLE . " v ON (e.`id` = v.`entity_id`)

									LEFT JOIN " . Note::TABLE . " n ON (e.`id` = n.`entity_id`)
									
									LEFT JOIN `app_dispatch_sheets` ds ON (ds.`entity_id` = e.`id`)";
									;
									
									

				

                $where = "e.`deleted` = 0

									AND (s.`fname` LIKE('%{$value}%')

										OR s.`lname` LIKE('%{$value}%')

										OR s.`phone1` LIKE('%{$value}%')

										OR s.`phone2` LIKE('%{$value}%')

										OR s.`mobile` LIKE('%{$value}%')

										OR s.`email` LIKE('%{$value}%')

										OR s.`company` LIKE('%{$value}%')

										OR o.`address1` LIKE('%{$value}%')

										OR o.`address2` LIKE('%{$value}%')

										OR o.`city` LIKE('%{$value}%')

										OR o.`state` LIKE('%{$value}%')

										OR o.`zip` LIKE('%{$value}%')

										OR o.`country` LIKE('%{$value}%')

										OR d.`address1` LIKE('%{$value}%')

										OR d.`address2` LIKE('%{$value}%')

										OR d.`city` LIKE('%{$value}%')

										OR d.`state` LIKE('%{$value}%')

										OR d.`zip` LIKE('%{$value}%')

										OR d.`country` LIKE('%{$value}%')

										OR v.`make` LIKE('%{$value}%')

										OR v.`model` LIKE('%{$value}%')

										OR v.`type` LIKE('%{$value}%')
										
										OR v.`vin` LIKE('%{$value}%')
										
										OR v.`color` LIKE('%{$value}%')

										OR n.`text` LIKE('%{$value}%') 
										
										";

                // number
                $where .= " OR e.`number` = '{$value}' ";
				$where .= " OR e.`id` = '{$value}' ";

                

				$where .= " OR ds.`carrier_company_name` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
						OR ds.`carrier_contact_name` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')		
						OR ds.`c_icc_mc_number` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
						OR ds.`carrier_email` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
						OR ds.`carrier_phone_1` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
                        OR ds.`carrier_phone_2` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')";
			

                //ds.`deleted` = 0
                //AND ds.`cancelled` IS NULL
                //AND ds.`rejected` IS NULL
				
				
		  
		   $where .= " )";
									
		} else {
                    
					$tables .= self::TABLE . " e    LEFT JOIN " . Origin::TABLE . " o ON (e.`origin_id` = o.`id`)

									LEFT JOIN " . Destination::TABLE . " d ON (e.`destination_id` = d.`id`)";

					$where .= "e.`deleted` = 0

					AND ( o.`state` LIKE('".$routeStates[0]."')

					AND d.`state` LIKE('".$routeStates[1]."')) ";

				}							

        $where .= " AND e.`type` = {$type} AND " . $this->getPermissionCondition($type, false, "e");

        $where_s = $where . " GROUP BY e.`id`";

        if ($order instanceof OrderRewrite) {

            $where_s .= " " . $order->getOrder() . " ";

        }

        if (!is_null($per_page) && ctype_digit((string)$per_page)) {

            $this->pager = new PagerRewrite($this->db);

            $this->pager->UrlStart = getLink();

            $this->pager->RecordsOnPage = $per_page;

            $this->pager->init($tables, "DISTINCT(e.`id`)", "WHERE " . $where);

            $where_s .= " " . $this->pager->getLimit() . " ";

        }

        $sql = "SELECT

						e.`id`

					FROM

						{$tables}

					WHERE

						{$where_s}";

        $result = $this->db->query($sql);

        if ($this->db->isError)

            throw new FDException("MySQL query error");

        if ($this->db->num_rows() > 0) {

            while ($row = $this->db->fetch_row($result)) {

                $entity = new Entity($this->db);

                $entity->load($row['id']);

                $entities[] = $entity;

            }

        }

        return $entities;

    }
*/
	
	/*
	public function searchAll($type, $search_type, $value, $per_page = 50, $order = null)

    {
         
        if (!array_key_exists($type, Entity::$type_name)) throw new FDException("Invalid entity type");

        $entities = array();

        $value = mysqli_real_escape_string($this->db->connection_id, $value);

       

                $tables = self::TABLE . " e

									LEFT JOIN " . Shipper::TABLE . " s ON (e.`shipper_id` = s.`id`)

									LEFT JOIN " . Origin::TABLE . " o ON (e.`origin_id` = o.`id`)

									LEFT JOIN " . Destination::TABLE . " d ON (e.`destination_id` = d.`id`)

									LEFT JOIN " . Vehicle::TABLE . " v ON (e.`id` = v.`entity_id`)

									LEFT JOIN " . Note::TABLE . " n ON (e.`id` = n.`entity_id`)";

                $where = "e.`deleted` = 0

									AND (s.`fname` LIKE('%{$value}%')

										OR s.`lname` LIKE('%{$value}%')

										OR s.`phone1` LIKE('%{$value}%')

										OR s.`phone2` LIKE('%{$value}%')

										OR s.`mobile` LIKE('%{$value}%')

										OR s.`email` LIKE('%{$value}%')

										OR s.`company` LIKE('%{$value}%')

										OR o.`address1` LIKE('%{$value}%')

										OR o.`address2` LIKE('%{$value}%')

										OR o.`city` LIKE('%{$value}%')

										OR o.`state` LIKE('%{$value}%')

										OR o.`zip` LIKE('%{$value}%')

										OR o.`country` LIKE('%{$value}%')

										OR d.`address1` LIKE('%{$value}%')

										OR d.`address2` LIKE('%{$value}%')

										OR d.`city` LIKE('%{$value}%')

										OR d.`state` LIKE('%{$value}%')

										OR d.`zip` LIKE('%{$value}%')

										OR d.`country` LIKE('%{$value}%')

										OR v.`make` LIKE('%{$value}%')

										OR v.`model` LIKE('%{$value}%')

										OR v.`type` LIKE('%{$value}%')

										OR n.`text` LIKE('%{$value}%') ";

               

				$routeStates = explode('-', $value);

				if (count($routeStates) != 2) {

					//$tables = self::TABLE . " e";

					//$where = " 0 ";

				} else {

					$tables .= "    LEFT JOIN " . Origin::TABLE . " o ON (e.`origin_id` = o.`id`)

									LEFT JOIN " . Destination::TABLE . " d ON (e.`destination_id` = d.`id`)";

					$where .= "

					OR ( o.`state` LIKE('".$routeStates[0]."')

					AND d.`state` LIKE('".$routeStates[1]."')) ";

				}

		        

           
                // number
                $where .= " OR e.`number` = '{$value}' ";

                
                //case 'carrier':

		        $tables .= " LEFT JOIN `app_dispatch_sheets` ds ON (ds.`entity_id` = e.`id`) ";

				$where .= " OR ds.`carrier_company_name` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')";
				
                //ds.`deleted` = 0
                //AND ds.`cancelled` IS NULL
                //AND ds.`rejected` IS NULL
		  
		$where .= " )";

        $where .= " AND e.`type` = {$type} AND " . $this->getPermissionCondition($type, false, "e");

        $where_s = $where . " GROUP BY e.`id`";

        if ($order instanceof OrderRewrite) {

            $where_s .= " " . $order->getOrder() . " ";

        }

        if (!is_null($per_page) && ctype_digit((string)$per_page)) {

            $this->pager = new PagerRewrite($this->db);

            $this->pager->UrlStart = getLink();

            $this->pager->RecordsOnPage = $per_page;

            $this->pager->init($tables, "DISTINCT(e.`id`)", "WHERE " . $where);

            $where_s .= " " . $this->pager->getLimit() . " ";

        }

        $sql = "SELECT

						e.`id`

					FROM

						{$tables}

					WHERE

						{$where_s}";

        $result = $this->db->query($sql);

        if ($this->db->isError)

            throw new FDException("MySQL query error");

        if ($this->db->num_rows() > 0) {

            while ($row = $this->db->fetch_row($result)) {

                $entity = new Entity($this->db);

                $entity->load($row['id']);

                $entities[] = $entity;

            }

        }

        return $entities;

    }
*/
	
public function searchIssue($type, $search_type, $per_page = 50, $order = null)
 {
	   // if (!array_key_exists($type, Entity::$type_name)) throw new FDException("Invalid entity type");

         $entities = array();
        $where .= " e.status=7 AND e.`type` = {$type} AND " . $this->getPermissionCondition($type, false, "e");

        $where_s = $where . " GROUP BY e.`id`";
		
		

        if ($order instanceof OrderRewrite) {

            $where_s .= " " . $order->getOrder() . " ";

        }

        if (!is_null($per_page) && ctype_digit((string)$per_page)) {

            $this->pager = new PagerRewrite($this->db);

            $this->pager->UrlStart = getLink();

            $this->pager->RecordsOnPage = $per_page;

            $this->pager->init('app_entities as e', "DISTINCT(e.`id`)", "WHERE " . $where);

            $where_s .= " " . $this->pager->getLimit() . " ";

        }

        $sql = "SELECT

						e.`id`

					FROM

						app_entities AS e

					WHERE

						{$where_s}";
//print $sql;
//exit;
        $result = $this->db->query($sql);

        if ($this->db->isError)

            throw new FDException("MySQL query error");

        if ($this->db->num_rows() > 0) {

            while ($row = $this->db->fetch_row($result)) {

                $entity = new Entity($this->db);

                $entity->load($row['id']);

                $entities[] = $entity;

            }

        }

        return $entities;

    }
	
	final public function getCommissionReport($order = null, $per_page = 100, $report_arr = array(), $owner_id = null)

    {
   			       if($report_arr['reports']==0 && $report_arr['reports']==0)
					{
					   //Estimated Commission
					   $searchStatus = " AND status NOT IN (7,9,3)";
					}
					elseif($report_arr['reports']==1)
					{
					  //"Actual Commission"
					  $searchStatus = " AND status =9 ";
					}
					elseif($report_arr['reports']==2)
					{
					  //"Receivables"
					  $searchStatus = " AND status IN (7,8) ";
					}
					elseif($report_arr['reports']==3)
					{
					  //"Dispatched"
					  $searchStatus = " AND status =6 ";
					}
					elseif($report_arr['reports']==4)
					{
					  //"Schedule Pickup"
					  $searchStatus = " AND status =8 ";
					}
					elseif($report_arr['reports']==5)
					{
					   //"Schedule Delivery"
					   $searchStatus = " AND status =9 ";
					}
					
					if($report_arr['report_option'] == 1){
						/*$timeperiod  = $report_arr['time_period'];
						$timeperiod  = $timeperiod+1;
					    $optionQuery = " AND  MONTH(ae.created)='$timeperiod' ";
						*/
						
						$optionQuery = " AND `created` >= '" . $report_arr["start_date"] . "'

                        AND `created` <= '" . $report_arr["end_date"] . "'";
					  
					}
					elseif($report_arr['report_option'] == 2){
						
						$startDate = trim($report_arr["start_date"]);
						$endDate   = trim($report_arr['end_date']);
						if($startDate != ""){
						  $startDateArr = explode("/",$startDate);
						  $startDate = $startDateArr[2]."-".$startDateArr[0]."-".$startDateArr[1];
						}
						if($endDate != ""){  
						  $endDateArr = explode("/",$endDate);
						  $endDate = $endDateArr[2]."-".$endDateArr[0]."-".$endDateArr[1];
						  
						}
					  
					  //$optionQuery = " AND (date_format(ae.created,'%Y-%m-%d') between date_format('$startDate','%Y-%m-%d') and date_format('$endDate','%Y-%m-%d')) ";
					  
					  $optionQuery = " AND `created` >= '" . $report_arr["start_date"] . "'

                        AND `created` <= '" . $report_arr["end_date"] . "'";
					  
					}

       

        $users = array();

        $members = Member::getCompanyMembers($this->db, $owner_id);

        if (count($report_arr["users_ids"]) > 0) {

            foreach ($report_arr["users_ids"] as $value) {

                if (in_array($value, $members)) {

                    $users[] = $value;

                }

            }
        $created_query = " AND creator_id IN('" . implode(",", $users) . "')";
        $assigned_query = " AND assigned_id IN('" . implode(",", $users) . "')";
        }

     
			$sql = "SELECT *,1 as created_assigned
						FROM app_entity_commission
						WHERE id!=0  ".$creator_query." ".$searchStatus." ".$optionQuery." 
						UNION
						SELECT *,2 as created_assigned
						FROM app_entity_commission
						WHERE id!=0  ".$assigned_query." ".$searchStatus." ".$optionQuery." 
						ORDER BY id desc";
			//print $sql .= $where_str_limit;
			//print $sql;
        $rows = $this->db->selectRows($sql);
       // $rows = parent::get($order, $per_page, $where);
	     $data = array();
        foreach ($rows as $row) {
            $tempArr  = array();
			$tempArr['id'] = $row['id'];
			$tempArr['entity_id'] = $row['entity_id'];
			$tempArr['number'] = $row['number'];
			$tempArr['created'] = $row['created'];
			$tempArr['total_tariff_stored'] = $row['total_tariff_stored'];
			$tempArr['carrier_pay_stored'] = $row['carrier_pay_stored'];
			$tempArr['type'] = $row['type'];
			$tempArr['status'] = $row['status'];
			$tempArr['reffered_id'] = $row['reffered_id'];
			$tempArr['reffered_by'] = $row['reffered_by'];
			$tempArr['account_id'] = $row['account_id'];
			$tempArr['company_name'] = $row['company_name'];
			$tempArr['creator_id'] = $row['creator_id'];
			$tempArr['creator_name'] = $row['creator_name'];
			$tempArr['assigned_id'] = $row['assigned_id'];
			$tempArr['assign_name'] = $row['assign_name'];
			$tempArr['commission'] = $row['commission'];
			$tempArr['deposit'] = $row['total_tariff_stored'] - $row['carrier_pay_stored'];
			$tempArr['intial_percentage'] = $row['intial_percentage'];
			$tempArr['residual_percentage'] = $row['residual_percentage'];
			$tempArr['commission_payed'] = $row['commission_payed'];
			$tempArr['commission_payed_assigned'] = $row['commission_payed_assigned'];
			$tempArr['commission_got'] = $row['commission_got'];
			
			if($row['created_assigned']==1){  // created
			  $tempArr['commission_got_amount'] = (($row['total_tariff_stored'] - $row['carrier_pay_stored']) * $row['commission_got'])/100;
			}
			elseif($row['created_assigned']==2){  // assigned
			  $tempArr['commission_got_amount'] = (($row['total_tariff_stored'] - $row['carrier_pay_stored']) * $row['commission'])/100;
			}
			
			$tempArr['commission_type'] = $row['commission_type'];
			$tempArr['created_assigned'] = $row['created_assigned'];
            $data[] = $tempArr;

        }

        return $data;

    }

	
	
public function search_lead_quote_order($type, $search_type, $value, $per_page = 50, $order = null,$orders_search_combo = -1)
 {
	 
        if (!array_key_exists($type, Entity::$type_name)) throw new FDException("Invalid entity type");

        $entities = array();
      
	     $searchOther = 1;
		 
		 if(in_array("route",$search_type))
		 {
			 $value = mysqli_real_escape_string($this->db->connection_id, $value);
			 $routeStates = explode('-', $value);
			 if (count($routeStates) != 2) {
				 
				 $searchOther = 1;
			 
			 } else {
						$searchOther = 0;
						
						$tables .= self::TABLE . " e    LEFT JOIN " . Origin::TABLE . " o ON (e.`origin_id` = o.`id`)
	
										LEFT JOIN " . Destination::TABLE . " d ON (e.`destination_id` = d.`id`)";
	
						$where .= "e.`deleted` = 0
	
						AND ( o.`state` LIKE('".$routeStates[0]."')
	
						AND d.`state` LIKE('".$routeStates[1]."')) ";
	
					}	
		 }
		     if($searchOther == 1)
			 {
					$sizeType = sizeof($search_type);
					$tables = self::TABLE . " e ";
					$where = " e.`deleted` = 0 AND (  ";	
					$whereTemp = "";
					foreach($search_type as $valueType)
					{
						
						
					   if($valueType == "carrier")
					   {
						   $tables .= " LEFT JOIN `app_dispatch_sheets` ds ON (ds.`entity_id` = e.`id`) ";
						   $whereTemp .= " ds.`carrier_company_name` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
									   OR ds.`carrier_contact_name` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')		
									   OR ds.`c_icc_mc_number` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
									   OR ds.`carrier_email` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
									   OR ds.`carrier_phone_1` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
									   OR ds.`carrier_phone_2` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')";
					   }
					   
					   if($valueType == "origin")
					   {
						   $tables .= " LEFT JOIN " . Origin::TABLE . " o ON (e.`origin_id` = o.`id`) ";
						   if($whereTemp != "")
							 $whereTemp .= "  OR";
						   $whereTemp .= "  o.`address1` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
										OR o.`address2` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')	
										OR o.`city` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')	
										OR o.`state` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')	
										OR o.`zip` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')	
										OR o.`country` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')";
					   }
					   
					   if($valueType == "destination")
					   {
						   $tables .= " LEFT JOIN " . Destination::TABLE . " d ON (e.`destination_id` = d.`id`) ";
						   if($whereTemp != "")
							 $whereTemp .= "  OR";
						   $whereTemp .= "  d.`address1` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
										OR d.`address2` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
										OR d.`city` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
										OR d.`state` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
										OR d.`zip` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
										OR d.`country` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')";
					   }
					   
					   if($valueType == "vehicle")
					   {
						   $tables .= " LEFT JOIN " . Vehicle::TABLE . " v ON (e.`id` = v.`entity_id`) ";
						   if($whereTemp != "")
							 $whereTemp .= "  OR";
						   $whereTemp .= "  v.`make` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
										OR v.`model` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
										OR v.`type` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')							
										OR v.`vin` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')							
										OR v.`color` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')";
					   }
					   
					   if($valueType == "shippers")
					   {
						   $flnameSTR = "";
						   $flnameArr = explode(" ",$value);
						   if(is_array($flnameArr) && count($flnameArr)>1)
						    {
							  $flnameSTR = "  (s.`fname` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $flnameArr[0])."%')
										AND s.`lname` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $flnameArr[1])."%'))";	
							}
							else{
								$flnameSTR = " s.`fname` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
										OR s.`lname` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')";
							}
						   
						   $tables .= " LEFT JOIN " . Shipper::TABLE . " s ON (e.`shipper_id` = s.`id`) ";
						   if($whereTemp != "")
							 $whereTemp .= "  OR";
						   $whereTemp .= "".$flnameSTR."
										OR s.`phone1` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
										OR s.`phone2` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
										OR s.`mobile` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
										OR s.`email` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%')
										OR s.`company` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%') ";
					   }
					   
					   if($valueType == "orderid")
					   {
						   //$tables .= "";
							if($whereTemp != "")
							 $whereTemp .= "  OR";
						   $whereTemp .= " e.`number` = '".mysqli_real_escape_string($this->db->connection_id, $value)."' ";
						   $whereTemp .= " OR e.`id` = '".mysqli_real_escape_string($this->db->connection_id, $value)."' ";
					   }
					   
					   if($valueType == "route")
					   {
						   $tables .= "";
						   //if($whereTemp != "")
							 //$whereTemp .= "  OR";
						   $whereTemp .= "";
					   }
					   if($valueType == "notes")
					   {
						   $tables .= " LEFT JOIN " . Note::TABLE . " n ON (e.`id` = n.`entity_id`) ";
						   if($whereTemp != "")
							 $whereTemp .= "  OR";
						   $whereTemp .= " n.`text` LIKE('%".mysqli_real_escape_string($this->db->connection_id, $value)."%') ";
					   }
					   
					}
					
					$where .= $whereTemp." )";
					
			 }
					
		
				
        if (isset($_SESSION['view_id']) && ($_SESSION['view_id'] != -1)) {
           $where .= " AND e.`assigned_id` = '" . (int)$_SESSION['view_id'] . "' ";
        } else {
            //$where .= " AND e.`assigned_id` IN (" . implode(', ', Member::getCompanyMembers($this->db, $_SESSION['member']['parent_id'])) . ")";
			//$where .= " AND e.`type` = {$type} AND " . $this->getPermissionCondition($type, false, "e");
        }
		
		if($orders_search_combo != -1)
		  $where .= " AND e.`status` = {$orders_search_combo}";
		
        $where .= "  AND " . $this->getPermissionCondition($type, false, "e");
		
		//AND e.`type` = {$type}

        $where_s = $where . " GROUP BY e.`id`";
		
		

        if ($order instanceof OrderRewrite) {

            $where_s .= " " . $order->getOrder() . " ";

        }


        if (!is_null($per_page) && ctype_digit((string)$per_page)) {

            $this->pager = new PagerRewrite($this->db);

            $this->pager->UrlStart = getLink();

            $this->pager->RecordsOnPage = $per_page;

            $this->pager->init($tables, "DISTINCT(e.`id`)", "WHERE " . $where);

            //$where_s .= " " . $this->pager->getLimit() . " ";

        }

        $sql = "SELECT

						e.*

					FROM

						{$tables}

					WHERE

						{$where_s}";
//print $sql;
//exit;


		return $this->getSearchArrData($sql);
		//return $this->db->selectRows($sql);

    }

public function getSearchArrData($sql)  {

		
		$table = '('.$sql.' ) as  en LEFT JOIN app_shippers s ON s.id = en.shipper_id';

		$table .= ' LEFT JOIN app_locations o ON o.id = en.origin_id';

		$table .= ' LEFT JOIN app_locations d ON d.id = en.destination_id';
		
		$table .= ' LEFT JOIN members m ON m.id = en.assigned_id';

		//$table .= ' LEFT JOIN app_vehicles v ON v.entity_id = e.id';

		$where_str .= ' GROUP BY en.id ';


  
  $searchSQL = " select en.*,
  s.fname as s_fname,
  s.lname as s_lname,
  s.company as s_company,
  s.email as s_email,
  s.phone1 as s_phone1,
  s.city as s_city,
  s.state as s_state,
  s.zip as s_zip,
  s.country as s_country ,
 
  s.shipper_hours as s_shipper_hours,  
  s.units_per_month as s_units_per_month,
  s.shipment_type as s_shipment_type,
  
  o.city as o_city,
  o.state as o_state,
  o.zip as o_zip,
  o.country as o_country, 
 
  d.city as d_city,
  d.state as d_state,
  d.zip as d_zip,
  d.country as d_country,
  m.contactname as contactname from " .$table." ".$where_str;
  
  //print $searchSQL;
		//return $this->db->selectRows($searchSQL);
		$result = $this->db->query($searchSQL);

   $rows  = array();
   

        if ($this->db->isError)

            throw new FDException("MySQL query error");

        if ($this->db->num_rows() > 0) {

            while ($row = $this->db->fetch_row($result)) {
				if($row['type'] == 1 && $row['lead_type'] == 1)
                 {
					$rows['lead_created'][] = $row; 
				 }
				 elseif($row['type'] == 1 && $row['lead_type'] == 0)
                 {
					$rows['lead_imported'][] = $row; 
				 }
				 elseif($row['type'] == 2)
                 {
					$rows['quote'][] = $row; 
				 }
				 elseif($row['type'] == 3)
                 {
					$rows['order'][] = $row; 
				 }
               
            }

        }
		
       return $rows;
		 
	}
	
	
public function searchBatch($type, $search_type, $value, $per_page = 50, $order = null)
 {
	 /*
      print "<pre>";
	  print_r($search_type);
	  print "</pre>"; 
	 */ 

	  
        if (!array_key_exists($type, Entity::$type_name)) throw new FDException("Invalid entity type");
		//print $value;
		$SearchStr = "";
		$SearchArr = explode(",", trim($value));
 
        $sizeString = sizeof($SearchArr);
		if($value!="" && $sizeString>=1)
		{
		   for($i=0;$i<$sizeString;$i++)
			    $SearchStr .= "'".trim($SearchArr[$i])."',";
			
			$SearchStr = substr($SearchStr,0,-1);
			//$where = " e.`id`  in (".$SearchStr.") and "; 
			$where = " e.number  in (".$SearchStr.") and "; 
			//A.prefix + '-' + A.number
			
			$tables = self::TABLE . " e ";
		}
		elseif($search_type !=""){
		   	
		   $where = " ac.`company_name` ='".$search_type."' and "; 
		   
		   $tables = self::TABLE . " e inner join app_accounts as ac ON e.`account_id` = ac.id ";
		}
		//print "---==----------".$SearchStr;
         $entities = array();
      	 
		 
		 
		 
		
        $where .= " e.`type` = {$type} AND e.status!=9 AND " . $this->getPermissionCondition($type, false, "e");
		
		//AND e.`type` = {$type}

        $where_s = $where . " GROUP BY e.`id`";
		
		

        if ($order instanceof OrderRewrite) {

            $where_s .= " " . $order->getOrder() . " ";

        }

        if (!is_null($per_page) && ctype_digit((string)$per_page)) {

            $this->pager = new PagerRewrite($this->db);

            $this->pager->UrlStart = getLink();

            $this->pager->RecordsOnPage = $per_page;

            $this->pager->init($tables, "DISTINCT(e.`id`)", "WHERE " . $where);

            $where_s .= " " . $this->pager->getLimit() . " ";

        }

        $sql = "SELECT

						e.*

					FROM

						{$tables}

					WHERE

						{$where_s}";
//print $sql;
//exit;


        $result = $this->db->query($sql);

        if ($this->db->isError)

            throw new FDException("MySQL query error");

        if ($this->db->num_rows() > 0) {

            while ($row = $this->db->fetch_row($result)) {

                $entity = new Entity($this->db);

                $entity->load($row['id']);

                $entities[] = $entity;

            }

        }

        return $entities;
	
    }
	
	
	public function searchBatchConfirm($type, $value, $per_page = 50, $order = null)
 {
	 /*
      print "<pre>";
	  print_r($search_type);
	  print "</pre>"; 
	 */ 

	  
        if (!array_key_exists($type, Entity::$type_name)) throw new FDException("Invalid entity type");
		//print $value;
		$SearchStr = "";
		$SearchArr = explode(",", trim($value));
 
        $sizeString = sizeof($SearchArr);
		if($value!="" && $sizeString>=1)
		{
		   for($i=0;$i<$sizeString;$i++)
			    $SearchStr .= "'".trim($SearchArr[$i])."',";
			
			$SearchStr = substr($SearchStr,0,-1);
			//$where = " e.`id`  in (".$SearchStr.") and "; 
			$where = " e.id  in (".$SearchStr.") and "; 
			//A.prefix + '-' + A.number
			
			$tables = self::TABLE . " e ";
		}
		
		//print "---==----------".$SearchStr;
         $entities = array();
      		
        $where .= " e.`type` = {$type}  AND " . $this->getPermissionCondition($type, false, "e");
		
		//AND e.`type` = {$type}

        $where_s = $where . " GROUP BY e.`id`";
		
		

        if ($order instanceof OrderRewrite) {

            $where_s .= " " . $order->getOrder() . " ";

        }

        if (!is_null($per_page) && ctype_digit((string)$per_page)) {

            $this->pager = new PagerRewrite($this->db);

            $this->pager->UrlStart = getLink();

            $this->pager->RecordsOnPage = $per_page;

            $this->pager->init($tables, "DISTINCT(e.`id`)", "WHERE " . $where);

            $where_s .= " " . $this->pager->getLimit() . " ";

        }

        $sql = "SELECT

						e.*

					FROM

						{$tables}

					WHERE

						{$where_s}";
//print $sql;
//exit;


        $result = $this->db->query($sql);

        if ($this->db->isError)

            throw new FDException("MySQL query error");

        if ($this->db->num_rows() > 0) {

            while ($row = $this->db->fetch_row($result)) {

                $entity = new Entity($this->db);

                $entity->load($row['id']);

                $entities[] = $entity;

            }

        }

        return $entities;
	
    }
	
	public function getCarrierDataCount($entity_id = 0, $per_page = 50, $where = null)  {

		//$entity_id = $_GET['id'];
		$count_carrier = 0;
if($entity_id!=0)
{
			$where = " `Zipcode` = (SELECT         o.zip as origin_zip
							FROM  app_entities e
							Left Outer join app_locations o 
							ON o.id = e.origin_id where e.id = ".$entity_id.")";
			//print  $where;
			
			$rows_origin = $this->db->selectRows('distinct `Lat`, `vLong`,
						
						lat + (40 / 69.1) as origin_lat_front,
						
						lat - (40 / 69.1) as origin_lat_back,
						
						vLong + (40 / (69.1 * cos(lat/57.3)) ) as origin_long_front,
						
						vLong - (40 / (69.1 * cos(lat/57.3)) ) as origin_long_back', " fd_zipcode_database ", " WHERE " . $where);
			
			  if(!empty($rows_origin))
			  {
					///$messages = "<p>Order ID/Entity Id resposted</p><br>";
					//$entities = array();
					//print "<pre>";
					//print_r($rows_origin);
				
			  }
			  
			 // print "<br><br><br><br>/*********************** Get Destination zip codes *********************/<br><br>"; 
			  
			  $where = " `Zipcode` = (SELECT         o.zip as origin_zip
							FROM  app_entities e
							Left Outer join app_locations o 
							ON o.id = e.destination_id where e.id = ".$entity_id.")";
			//print  $where;
			
			$rows_destination = $this->db->selectRows('distinct `Lat`, `vLong`,
						
						lat + (40 / 69.1) as destination_lat_front,
						
						lat - (40 / 69.1) as destination_lat_back,
						
						vLong + (40 / (69.1 * cos(lat/57.3)) ) as destination_long_front,
						
						vLong - (40 / (69.1 * cos(lat/57.3)) ) as destination_long_back', " fd_zipcode_database ", " WHERE " . $where);
			
			  if(!empty($rows_destination))
			  {
					
					//print "<br><br><pre>";
					//print_r($rows_destination);
				
			  }
			 
			 //print count($rows_origin)."=================".count($rows_destination);
			 
			 if(count($rows_origin)>0 && count($rows_destination)>0)
			 {
				 
				 $sql = " 
				 
				 SELECT count(*) as count_carrier  FROM (
	 SELECT acc.id FROM 
	      app_entities en
		Left Outer Join  app_dispatch_sheets    ad
		ON en.id = ad.entity_id
		Left Outer Join app_accounts acc 
		ON ad.account_id = acc.id
		INNER JOIN 
	     (
			 SELECT origin.id
						from ( 
									  SELECT  e.id     
								FROM  app_entities e
								Left Outer join app_locations o 
								ON o.id = e.origin_id 
								inner join (SELECT distinct Zipcode
								FROM `fd_zipcode_database` WHERE lat <= ".$rows_origin[0]['origin_lat_front']."
								
																	and lat >= ".$rows_origin[0]['origin_lat_back']."
																	
																	and vLong <= ".$rows_origin[0]['origin_long_front']."
																	
																	and vlong >= ".$rows_origin[0]['origin_long_back'].") as z
								on o.zip = z.Zipcode
								where e.status = 9 OR e.status = 6 OR e.status = 8
								AND e.dispatched IS NOT NULL 
								AND e.delivered IS NOT NULL 
					) as origin
				
				INNER JOIN
				
					( 
							 SELECT  e.id       
							FROM  app_entities e
							Left Outer join app_locations o 
							ON o.id = e.destination_id
							inner join (SELECT distinct Zipcode
							FROM `fd_zipcode_database` WHERE lat <= ".$rows_destination[0]['destination_lat_front']."
							
																and lat >= ".$rows_destination[0]['destination_lat_back']."
																
																and vLong <= ".$rows_destination[0]['destination_long_front']."
																
																and vlong >= ".$rows_destination[0]['destination_long_back'].") as z
							on o.zip = z.Zipcode
							where e.status = 9 OR e.status = 6 OR e.status = 8
							AND e.dispatched IS NOT NULL 
							AND e.delivered IS NOT NULL 
					) as destination
					
				   ON origin.id = destination.id
           ) as z	on en.id = z.id	
		 
		 group by acc.id
     )  as z
			";
				 
				// print $sql;
				// print "<br><br>-----------------Output-----------------<br><br>";
					$result1 = $this->db->query($sql);
					if ($this->db->num_rows() > 0) {
						
						while ($row1 = $this->db->fetch_row($result1)) {
						    $count_carrier = $row1['count_carrier'];
							
						}
						
						return $count_carrier;
					}
				   
					return $count_carrier;
				}
				return $count_carrier;
      }// close if
	  return $count_carrier;
  }
	
	
}