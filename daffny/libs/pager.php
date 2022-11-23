<?php

/*
+---------------------------------------------------+
|                                                   |
|                   Daffny Engine                   |
|                                                   |
|                  Page  Navigator                  |
|                                                   |
|                by Alexey Kondakov                 |
|             (c)2006 - 2007 Daffny, Inc.           |
|                                                   |
|                  www.daffny.com                   |
|                                                   |
+---------------------------------------------------+
*/

class Pager
{
    /**
	* Daffny Engine�
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
    * Where for Query
    *
    * @var string
    */
    var $where = "";


    /**
    * Results on page
    *
    * @var integer
    */
    var $on_page = 10;


    /**
    * Number of Pages Links
    *
    * @var integer
    */
    var $links = 5;


    /**
    * Current page
    *
    * @var integer
    */
    var $current = 1;


    /**
    * Records in table
    *
    * @var integer
    */
    var $records = 0;


    /**
    * Total pages
    *
    * @var integer
    */
    var $pages = 0;

    /*
     * How to show page links with LoadPage() or not
     * @var bool
     *
     */
    var $application = false;

    /**
    * put your comment there...
    *
    *
    * @var mixed
    */
    var $limit;

    /*--------------------------------------*/
    // Constructor
    /*--------------------------------------*/
    function page_navigator()
    {
        $this->url = basename($_SERVER['PHP_SELF'])."?";

        if ( count($_GET) )
        {
            $link = "";
            foreach ( $_GET as $k => $v )
            {
                if ( $k == "page" ) continue;
                if ( $v == "" )
                {
                    $link .= $k."&amp;";
                    continue;
                }
                $link .= $k."=".$v."&amp;";

                $this->url .= $link;
            }
        }
    }


    /*--------------------------------------*/
    // Init
    /*--------------------------------------*/
    function init($table, $field = "*")
    {
        // Does we have where clause?
        if ( $this->where != "" )
        {
            // Does we have word "WHERE"?
            if ( !preg_match("/WHERE/i", $this->where) )
            {
                $this->where = "WHERE ".$this->where;
            }
        }

        if ($field == "") {
            $field = "*";
        }

        // Ok, Run query
        $row = $this->daffny->DB->select_one("COUNT($field) cnt", $table, $this->where);

        // How many records we have?
        $this->records = $row['cnt'];

        // How many pages we take?
        $this->pages = ceil($this->records / $this->on_page);

        // Get LIMIT for query
        $this->limit = $this->get_limit();
    }


    /*--------------------------------------*/
    // Limit For Your Query
    /*--------------------------------------*/
    function get_limit()
    {
        $cpage = 0;
        $limit_begin = 0;

        if ( isset($_GET['page']) )
        {
            $cpage = (int)$_GET['page'];
        }

        if ( $cpage > 1 )
        {
            $this->current = $cpage;

            if ( $cpage > $this->pages )
            {
                $this->current = $this->pages;
            }

            $limit_begin = ($this->current - 1) * $this->on_page;
        }

        if ( $limit_begin < 0 ) $limit_begin = 0;

        return " LIMIT ".$limit_begin.", ".$this->on_page;
    }


    /*--------------------------------------*/
    // Pages List For Your Site
    /*--------------------------------------*/
    function get_list($level = 3)
    {
        $out = "";

        if ( $level >= 3 ) {
            $out .= $this->show_total();
        }
        if ( $level >= 2 ) {
            $out .= $this->show_page_of();
        }

        $out .= $this->show_list();

        return "<div class=\"p_navigation\">".$out."</div>";
    }


    /*--------------------------------------*/
    // Page Total
    /*--------------------------------------*/
    function show_total()
    {
        return "<span class=\"p_total\">Records:&nbsp;<span>".number_format($this->records)."</span></span>";
    }


    /*--------------------------------------*/
    // Page Of
    /*--------------------------------------*/
    function show_page_of()
    {
        return "<span class=\"p_of\">Page&nbsp;<span>".number_format($this->current)."</span>&nbsp;of&nbsp;<span>".number_format($this->pages)."</span></span>";
    }


    /*--------------------------------------*/
    // Page
    /*--------------------------------------*/
    function show_page($link, $name, $title = "")
    {
        $loadPage = "";
        if($this->application)
        {

            $loadPage = "onclick=\"LoadPage('".substr($link, 1)."');\"";
        }

        return "<a href=\"$link\" title=\"$title\" $loadPage>$name</a> ";
    }


    /*--------------------------------------*/
    // Page Total For Your Site
    /*--------------------------------------*/
    function show_page_on($page)
    {
        return "<span class=\"p_on\">$page</span> ";
    }


    /*--------------------------------------*/
    // Page List For Your Site
    /*--------------------------------------*/
    function show_list()
    {
        $out = "";

        // No Pages for show
        if ( $this->records < $this->on_page )
        {
            return;
        }


        // Show Left Navigation
        if ( $this->current > 1 )
        {
            // First page
            if ( $this->links < $this->current ) {
                $out .= $this->show_page($this->url."page=1", "<strong>&laquo;</strong>&nbsp;First");
            }

            // Previous page
            $out .= $this->show_page($this->url."page=".($this->current-1), "&lt;");
        }


        // Show Pages
        for ( $i = 1; $i <= $this->pages; $i++ )
        {
            if ( $i == $this->current )
            {
                $out .= $this->show_page_on($i);
            }
            else if ( ($this->current - $i) < $this->links && ($i - $this->current) < $this->links )
            {
                $out .= $this->show_page($this->url."page=".$i, $i);
            }
        }


        // Show Right Navigation
        if ( $this->current < $this->pages )
        {
            // Next page
            $out .= $this->show_page($this->url."page=".($this->current+1), "&gt;");

            // Last page
            if ( ($this->pages - $this->current+1) > $this->links ) {
                $out .= $this->show_page($this->url."page=".$this->pages, "Last <strong>&raquo;</strong>");
            }
        }

        return $out;
    }

}

?>