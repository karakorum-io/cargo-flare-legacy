<?php

class Appsitemap extends AppAction
{
    public function idx()
    {
        $this->tplname = "content.sitemap";

		$cont = $this->getContent('sitemap', 1);
        $this->input['content'] = $cont['content'];
        $this->title = $this->getBreadCrumbs($cont['title']);
        $this->daffny->tpl->usl = $this->getList();
    }

    protected function getList($useNoRecords = true)
    {
        $sql = "SELECT *
                  FROM products
                  ORDER BY id DESC ";
        return $this->getGridData($sql, $useNoRecords);
    }
}
?>