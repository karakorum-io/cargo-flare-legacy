<?php

/*
+---------------------------------------------------+
|                                                   |
|                   Daffny Engine                   |
|                                                   |
|              Page Navigator Rewrite               |
|                                                   |
|                by Alexey Kondakov                 |
|             (c)2006 - 2009 Daffny, Inc.           |
|                                                   |
|                  www.daffny.com                   |
|                                                   |
+---------------------------------------------------+
*/

class PagerRewrite
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
    * Results on page
    *
    * @var integer
    */
    var $RecordsOnPage = 10;

    /**
    * Number of Pages Links
    *
    * @var integer
    */
    var $LinksOnPage = 5;

    /**
    * Current page
    *
    * @var integer
    */
    var $CurrentPage = 1;

    /**
    * Records in table
    *
    * @var integer
    */
    var $RecordsTotal = 0;

    /**
    * Total pages
    *
    * @var integer
    */
    var $PagesTotal = 0;

    /**
    * put your comment there...
    *
    *
    * @var mixed
    */
    var $Limit;

	/**
	 * put your comment there...
	 *
	 * @param object $DB
	 *
	 * @return \PagerRewrite
	 */
    public function __construct($DB = null)
    {
        $this->DB = $DB;
    }

    /**
    * put your comment there...
    *
    * @param string $table
    * @param string $field
    * @param string $where
    * @return void
    */
    public function init($table, $field = "", $where = "")
    {
        if ($this->FullUrl == "") {
            $this->prepareFullUrl();
        }

        if ($field == "") {
            $field = "*";
        }

        if ($where != "" && !preg_match("/WHERE/", $where)) {
            $where = sprintf('WHERE %s', $where);
        }

        if (is_null($this->DB)) {
            die("Can't init pager, need Daffny's DB class.");
        }

        // How many records we have?
        $this->RecordsTotal = $this->DB->selectValue(sprintf('COUNT(%s) cnt', $field), $table, $where);

        // How many pages we take?
        $this->PagesTotal = ceil($this->RecordsTotal / $this->RecordsOnPage);


        $limitStart = 0;
        if (isset($_GET['page']) && (int)$_GET['page'] > 1)
        {
            $this->CurrentPage = $_GET['page'];

            if ($this->CurrentPage > $this->PagesTotal) {
                $this->CurrentPage = $this->PagesTotal;
            }

            $limitStart = ($this->CurrentPage - 1) * $this->RecordsOnPage;

            if ($limitStart < 0) {
                $limitStart = 0;
            }
        }

        $this->Limit = sprintf(' LIMIT %d, %d ', $limitStart, $this->RecordsOnPage);
    }

    /**
    * put your comment there...
    *
    */
    public function getLimit()
    {
        return $this->Limit;
    }

    /**
    * put your comment there...
    *
    * @param mixed $level
    */
    public function get_list($level = 3)
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

    /**
    * put your comment there...
    *
    */
    public function getNavigation($tpl = '<div class="pager">%s</div>')
    {
        // No pages to show
        if ($this->RecordsTotal < $this->RecordsOnPage) {
            return;
        }

        $out = "";

        // Show left navigation
        if ($this->CurrentPage > 1)
        {
            // First page
            if ($this->LinksOnPage < $this->CurrentPage) {
                $out .= $this->showPage("page/1", "<strong>&laquo;</strong>&nbsp;First");
            }

            // Previous page
            $out .= $this->showPage("page/".($this->CurrentPage - 1), "&lt;");
        }

        // Show pages
        for ($i = 1; $i <= $this->PagesTotal; $i++)
        {
            if ($i == $this->CurrentPage) {
                $out .= $this->showPageOn($i);
            }
            else if (($this->CurrentPage - $i) < $this->LinksOnPage && ($i - $this->CurrentPage) < $this->LinksOnPage) {
                $out .= $this->showPage("page/".$i, $i);
            }
        }


        // Show Right Navigation
        if ($this->CurrentPage < $this->PagesTotal)
        {
            // Next page
            $out .= $this->showPage("page/".($this->CurrentPage + 1), "&gt;");

            // Last page
            if (($this->PagesTotal - $this->CurrentPage + 1) > $this->LinksOnPage) {
                $out .= $this->showPage("page/".$this->PagesTotal, "Last <strong>&raquo;</strong>");
            }
        }

        return sprintf($tpl, substr($out, 0, -1));
    }

    /**
    * put your comment there...
    *
    * @param mixed $link
    * @param mixed $name
    * @param mixed $title
    */
    private function showPage($link, $name)
    {
        return sprintf('<a href="%s">%s</a> ', $this->FullUrl.$link, $name);
    }

    /**
    * put your comment there...
    *
    * @param mixed $page
    */
    private function showPageOn($page)
    {
        return "<span>$page</span> ";
    }

    /**
    * put your comment there...
    *
    */
    private function prepareFullUrl()
    {
        $url = "";
        foreach ($_GET as $k => $v)
        {
            if ($k == "url" || $k == "page") {
                continue;
            }

            $url .= sprintf('%s/%s/', $k, $v);
        }
        $this->FullUrl = str_replace("/", "/", $this->UrlStart.$url);
    }
}

?>