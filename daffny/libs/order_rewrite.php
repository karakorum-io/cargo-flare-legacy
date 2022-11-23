<?php

/*
+---------------------------------------------------+
|                                                   |
|                   Daffny Engine                   |
|                                                   |
|                   Order Rewrite                   |
|                                                   |
|                by Alexey Kondakov                 |
|             (c)2006 - 2009 Daffny, Inc.           |
|                                                   |
|                  www.daffny.com                   |
|                                                   |
+---------------------------------------------------+
*/

class OrderRewrite
{
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    var $DB;
    
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    var $UrlStart = "/";
    
    /**
    * Save URL
    *
    * @var string
    */
    var $FullUrl = "";
    
    /**
    * Order By
    *
    * @var string
    */
    var $CurrentOrder = "";
    
    /**
    * Order Arrow
    *
    * @var string
    */
    var $CurrentArrow = "asc";
    
    /**
    * Collect here order fields
    *
    * @var array
    */
    var $Fields = array();
    
    /**
    * Order words for query
    *
    * @var string
    */
    var $OrderBy = "";
    
    /**
    * Order table index
    *
    * @var string
    */
    var $index = "";
    
    /**
    * put your comment there...
    * 
    * @param mixed $DB
    * @return OrderRewrite
    */
    public function __construct($DB = null)
    {
        $this->DB = $DB;
    }
    
    /**
    * put your comment there...
    * 
    */
    public function init()
    {
        $url = "";
		
        foreach ($_GET as $k => $v)
        {
			
            if ($k == "url" || $k == "order" || $k == "arrow" || $v == "" )
            {
                continue;
            }
//print $k."-".$url;
            if(in_array($k, array('start_date', 'end_date'))) {
                $v = str_replace('/', '-', $v);
            }

            if(in_array($k, array('accounts', 'reports', 'orders'))) {
                $url .= sprintf('%s/',$v);
            } else {
                $url .= sprintf('%s/%s/', $k, $v);
            }
        }
		if(isset($_GET['leads']) && $_GET['leads']=='') {
			$url .= "leads/";
		}

        if(isset($_GET['url'])) {
            $data = explode('/', $_GET['url']);
			
			if($data[0]!="leads")
             $url = $data[0] . '/' . $url;
        }

        $this->FullUrl = str_replace("/", "/", $this->UrlStart.$url);
        
        // Current Order By
        if (isset($_GET['order']))
        {
            $this->CurrentOrder = $_GET['order'];
        }
        
        // Current Arrow
        if (isset($_GET['arrow']))
        {
            $this->CurrentArrow = $_GET['arrow'] == "desc" ? "desc" : "asc";
        }
        
        $this->getOrder();
    }
    
    public function setTableIndex($index) {
    	$this->index = $index;
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $field
    * @param mixed $title
    */
    public function getTitle($field, $title)
    {
        
        if ($this->CurrentOrder == $field)
        {
            $arrow = $this->CurrentArrow == "desc" ? "asc" : "desc";
            $arrow_class = "-".($this->CurrentArrow == "desc" ? "desc" : "asc");
        }
        else
        {
            $arrow = "asc";
            $arrow_class = "";
        }

        return sprintf('<a href="%sorder/%s/arrow/%s" class="order%s">%s</a>', $this->FullUrl, $field, $arrow, $arrow_class, $title);       
    }
    
    /**
    * put your comment there...
    * 
    */
    public function getOrder()
    {
        if (in_array($this->CurrentOrder, $this->Fields) && $this->CurrentArrow != "")
        {
            $this->OrderBy = sprintf(' ORDER BY %s %s ', ($this->index == "")?$this->CurrentOrder:$this->index.'.'.$this->CurrentOrder, strtoupper($this->CurrentArrow));
        }
        
        return $this->OrderBy;
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $field
    * @param mixed $arrow
    */
    public function setDefault($field, $arrow = "asc")
    {
        if ($this->CurrentOrder != "")
        {
            return;
        }
        
        $this->CurrentOrder = $field;
        $this->CurrentArrow = $arrow;
    }
}

?>