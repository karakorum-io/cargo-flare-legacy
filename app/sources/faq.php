<?php
class Appfaq extends AppAction
{
    public function idx()
    {
    	$this->daffny->tpl->emptyText = "No records";
		$this->title = "F.A.Q.";
        $this->tplname = "faq.list";

        if (!$records = $this->getList())
        {
            return;
        }
        $this->daffny->tpl->data = $records;
    }

    /**
    * Get a list on faq
    *
    */
    protected function getList($useNoRecords = true)
    {
        $sql = "SELECT *
                  FROM faq
                  ORDER BY id DESC ";
        return $this->getGridData($sql, $useNoRecords);
    }
}

?>