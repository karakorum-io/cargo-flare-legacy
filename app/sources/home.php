<?php

class AppHome extends AppAction
{
    public $title;

    function idx()
    {
     
    	$this->tplname = "home";
    	$cont = $this->getContent("home",1);
		$this->input['home_text'] = $cont['content'];

		/* Featured news */
		$this->daffny->tpl->news = array();
		$news_sql = "SELECT *
                  FROM news
                  WHERE is_featured = 1 AND is_hidden <> 1
                 LIMIT 0, 1";
		$this->daffny->tpl->news = $this->daffny->DB->selectRows($news_sql);
    }
}

?>