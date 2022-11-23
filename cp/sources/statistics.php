<?php

/***************************************************************************************************
* Newsletters Statistics CP Class                                                                 *
*                                                                              					   *
*                                                                                                  *
* Client: 	FreightDragon                                                                          *
* Version: 	1.0                                                                                    *
* Date:    	2011-10-03                                                                             *
* Author:  	C.A.W., Inc. dba INTECHCENTER                                                          *
* Address: 	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076                             *
* E-mail:	techsupport@intechcenter.com                                                           *
* CopyRight 2011 FreightDragon. - All Rights Reserved                                              *
****************************************************************************************************/

class CpStatistics extends CpAction
{
    public $title = "Statistics";
    public $tplname = "statistics.list";

    function idx()
    {
		if (isset($_POST['template_id'])){
            $_SESSION['template_id'] = $_POST['template_id'];
        }
		if (!isset($_SESSION["template_id"])){
			$_SESSION["template_id"] = '';
		}
		if( isset($_POST['filter_date_from']) ){

		    $from = explode("/", $_POST['filter_date_from']);
		    if (count($from) == 3){
		    	$_SESSION["filter_date_from"] =  $from[2]."-".$from[0]."-".$from[1];
		    }else{
		    	unset($_SESSION["filter_date_from"]);
		    }
		    $to = explode("/", $_POST['filter_date_to']);
		    if (count($to) == 3){
		    	$_SESSION["filter_date_to"] =  $to[2]."-".$to[0]."-".$to[1];
		    }else{
		    	unset($_SESSION["filter_date_to"]);
		    }
		}

		if (!isset($_SESSION["filter_date_from"])) $_SESSION["filter_date_from"] = "";
        if (!isset($_SESSION["filter_date_to"])) $_SESSION["filter_date_to"] = "";

        // Limit
		$where = " 1 = 1 ";

		if ( isset($_SESSION['template_id']) && $_SESSION['template_id'] != '' )
		{
			if( isset($_SESSION['template_id']) && $where != '' )
			{
				$where .=" AND t.id = '".$_SESSION['template_id']."'";
			}
			else
			{
				$where =" t.id = '".$_SESSION['template_id']."'";
			}
		}

		$this->input['template_id'] = $_SESSION["template_id"];
		$this->form->ComboBox("template_id", $this->getTemplatesSub("All Templates"), array(), "E-mail template", "</td><td colspan=\"3\">");

		$from = explode("-", $_SESSION["filter_date_from"]);
		if (count($from) == 3){
			$this->input["filter_date_from"] =  $from[1]."/".$from[2]."/".$from[0];
		}
		$to = explode("-", $_SESSION["filter_date_to"]);
		if (count($to) == 3){
			$this->input["filter_date_to"] =  $to[1]."/".$to[2]."/".$to[0];
		}

		$this->form->TextField("filter_date_from", 10, array('style' => "width: 100px;"), "Actions from", "</td><td>");
		$this->form->TextField("filter_date_to", 10, array('style' => "width: 100px;"), "to", "</td><td>");


		if ( $_SESSION["filter_date_from"] !='' )
		{
			if ($where != '')
			{
				$where .= " AND s.process_date >= '".$_SESSION["filter_date_from"]." 00:00:00"."' AND s.process_date <= '".$_SESSION["filter_date_to"]." 23:59:59"."'";
			}
			else
			{
				$where  = " s.process_date >= '".$_SESSION["filter_date_from"]." 00:00:00"."' AND s.process_date <= '".$_SESSION["filter_date_to"]." 23:59:59"."'";
			}
		}

		//-------------------------
		// Statistic summary report
		//-------------------------
		$this->input['sent'] = $this->get_sent_failed($_SESSION['template_id'] ,"sent");
		$this->input['filed'] = $this->get_sent_failed($_SESSION['template_id'] ,"failed");

		//-----------------------
        $i = 0;
        $this->applyPager("subscribers_statistics s
                        INNER JOIN subscribers e ON e.id = s.email_id
                        INNER JOIN subscribers_newsletters t ON t.id = s.template_id
                        INNER JOIN subscribers_actions a ON a.id = s.action_id","", $where);
        $order = $this->applyOrder("subscribers_statistics");
        $order->setDefault("id", "desc");


        $sql = "SELECT s.id
                 , e.email AS email
                 , t.title AS template
                 , a.name AS action
                 , DATE_FORMAT(s.process_date, '%m/%d/%Y') AS process_date
              FROM subscribers_statistics s
                   INNER JOIN subscribers e ON e.id = s.email_id
                   INNER JOIN subscribers_newsletters t ON t.id = s.template_id
                   INNER JOIN subscribers_actions a ON a.id = s.action_id"
                  	 . " WHERE ".$where
                     . $this->order->getOrder()
                     . $this->pager->getLimit();
        $this->daffny->tpl->data = array();
        if (!$records = $this->getGridData($sql, false))
        {
             $this->daffny->tpl->data = array();
        }else{
        	$this->daffny->tpl->data = $records;
        }

    }

    public function queue()
    {
        $this->tplname = "statistics.queue";
        $this->title = "Queue";

        $this->applyPager("subscribers_letters", ""," 1=1 GROUP BY counter");


        $sql = "SELECT counter, COUNT(*) as cnt
            FROM subscribers_letters l
            INNER JOIN subscribers e ON e.id  = l.email_id
            INNER JOIN subscribers_newsletters t ON l.template_id = t.id"
                     . " GROUP BY l.counter "
                     . $this->pager->getLimit();

        if (!$records = $this->getGridData($sql, false))
        {
            return;
        }

        foreach ($records as $i => $record)
        {
            $record['details'] = "<a href='".getLink("statistics", "details", "counter", $record['counter'])."'>view</a>";
            $records[$i]['details'] = $record['details'];
        }
        $this->daffny->tpl->data = $records;
    }

    public function details() {
        $this->tplname = "statistics.details_queue";
        $this->title = "Queue List";
		$counter = (int)get_var("counter");
		$where = " counter = {$counter} ";

        $this->applyPager("subscribers_letters l
                   INNER JOIN subscribers_newsletters t ON t.id  = l.template_id
                   INNER JOIN subscribers e ON e.id  = l.email_id","", $where);
        $order = $this->applyOrder("subscribers_letters");
        $order->setDefault("id", "desc");


        $sql = "SELECT   l.id
                    ,l.email_id
                    ,l.from_name
                    ,l.subject
                    ,t.title
                    ,l.counter AS co
                    ,e.email
              FROM subscribers_letters l
                   INNER JOIN subscribers_newsletters t ON t.id  = l.template_id
                   INNER JOIN subscribers e ON e.id  = l.email_id"
                  	 . " WHERE ".$where
                     . $this->order->getOrder()
                     . $this->pager->getLimit();
        if (!$records = $this->getGridData($sql, false))
        {
            //return;
        }
        $this->daffny->tpl->data = $records;
    }

    private function get_unsubscribed($template_id)
	{
		$whereu = "";
		if ( $template_id !='' )
		{
			$whereu .= " AND t.id = '".$template_id."'";
		}

		if ( $_SESSION["filter_date_from"] !='' )
		{
				$whereu  .= " AND s.process_date >= '".$_SESSION["filter_date_from"]." 00:00:00"."' AND s.process_date <= '".$_SESSION["filter_date_to"]." 23:59:59"."'";
		}


 		$sql = "
            SELECT COUNT(*) cnt
              FROM subscribers_statistics s
                   INNER JOIN subscribers_newsletters t ON t.id = s.template_id
                   INNER JOIN subscribers_actions a ON a.id = s.action_id
				   WHERE a.type = '3'".$whereu;
        $q_u = $this->daffny->DB->query($sql);
		$row_u = $this->daffny->DB->fetch_row($q_u);
		return $row_u['cnt'];
	}

	private function get_visits($template_id)
	{
		$wherev = "";
		if ( $template_id !='' )
		{
			$wherev .= " AND t.id = '".$template_id."'";
		}

		if ( $_SESSION["filter_date_from"] !='' )
		{
				$wherev .= " AND s.process_date >= '".$_SESSION["filter_date_from"]." 00:00:00"."' AND s.process_date <= '".$_SESSION["filter_date_to"]." 23:59:59"."'";
		}

 		$sql = "
            SELECT COUNT(*) cnt
              FROM subscribers_statistics s
                   INNER JOIN subscribers_newsletters t ON t.id = s.template_id
                   INNER JOIN subscribers_actions a ON a.id = s.action_id
				   WHERE a.type = '2'".$wherev;
        $q_v = $this->daffny->DB->query($sql);
		$row_v = $this->daffny->DB->fetch_row($q_v);
		return $row_v['cnt'];
	}

	private function get_sent_failed($template_id, $what)
	{
		$wheref = " WHERE 1=1 ";
		if ( $template_id !='' )
		{
			$wheref .= " AND id = '".$template_id."'";
		}

		$row_s = $this->daffny->DB->select_one("SUM(sent) sent, SUM(failed) failed", "subscribers_newsletters", $wheref);
		return $row_s[$what];
	}
}

?>
