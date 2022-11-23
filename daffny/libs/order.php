<?php

/*
+---------------------------------------------------+
|                                                   |
|                   Daffny Engine                   |
|                                                   |
|                       Order                       |
|                                                   |
|                by Alexey Kondakov                 |
|             (c)2006 - 2007 Daffny, Inc.           |
|                                                   |
|                  www.daffny.com                   |
|                                                   |
+---------------------------------------------------+
*/

class order
{
    /**
	* Daffny Engine?
	*
	* @var object
	*/
    var $daffny;
    
    
    /**
    * Save URL
    *
    * @var string
    */
    var $url = "";
    
    
    /**
    * Order By
    *
    * @var string
    */
    var $cur_order = "";
    
    
    /**
    * Order Arrow
    *
    * @var string
    */
    var $cur_arrow = "asc";
    
    
    /**
    * Collect here order fields
    *
    * @var array
    */
    var $fields = array();
    
    
    /**
    * Order words for query
    *
    * @var string
    */
    var $orderby = "";
    
    
    /*--------------------------------------*/
    // Constructor
    /*--------------------------------------*/
    function order()
    {
        // Save Url
        $this->url = basename($_SERVER['PHP_SELF'])."?";
        
        if ( count($_GET) )
        {
            foreach ( $_GET as $k => $v )
            {
                if ( $k == "order" || $k == "arrow" ) continue;
                
                $this->url .= $k."=".$v."&amp;";
            }
        }
        
        // Current Order By
        if ( isset($_GET['order']) )
        {
            $this->cur_order = get_var("order");
        }
        
        // Current Arrow By
        if ( isset($_GET['arrow']) )
        {
            $this->cur_arrow = (get_var("arrow") == "desc" ? "desc" : "asc");
        }
    }
    
    
    /*--------------------------------------*/
    // Get Table Column Title
    /*--------------------------------------*/
    function get_title($field, $title)
    {
        // Collect Fields
        $this->fields[] = $field;
        
        $img = "";
        $arrow = "asc";
        
        // Current Title
        if ( $this->cur_order == $field )
        {
            $arrow = ( $this->cur_arrow == "desc" ) ? "asc" : "desc";
            $img   = "&nbsp;".$this->daffny->html->img($this->daffny->img_path."daffny/images/arrow-".strtolower($this->cur_arrow).".png", array('border'=>0, 'align'=>"top", 'alt'=>""));
        }
        
        $this->get_order();
        
        return $this->daffny->html->a($this->url."order=".$field."&amp;arrow=".$arrow, $title).$img;
    }
    
    
    /*--------------------------------------*/
    // Get Order For Query
    /*--------------------------------------*/
    function get_order()
    {
        if ( !in_array($this->cur_order, $this->fields) || $this->cur_arrow == "" )
        {
            return;
        }
        
        $this->orderby = " ORDER BY ".$this->cur_order." ".strtoupper($this->cur_arrow);
    }
    
    
    /*--------------------------------------*/
    // Set Default Order
    /*--------------------------------------*/
    function setdefault($field, $arrow = "asc")
    {
        if ( $this->cur_order != "" )
        {
            return;
        }
        
        $this->cur_order = $field;
        $this->cur_arrow = $arrow;
    }
    
}

?>