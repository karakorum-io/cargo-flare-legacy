<?php
class AppNews extends AppAction
{
    public function idx()
    {
		$this->title = "<h3>News</h3>";
        $this->tplname = "news.list";

        if (!$records = $this->getList()){
            return;
        }

        foreach ($records as $i => $record){
            $record['content'] = trim(strip_tags($record['content']));
            if ($record['content'] == "") {
                continue;
            }
			$records[$i]['content'] = cutContent($record['content'], 50)." <a href=\"".getLink("news", "show", "id", $record['id'])."\"><small>More</small></a>";
        }
        $this->daffny->tpl->data = $records;
    }

    public function show($sid = '')
    {
        $id = (int)get_var("id");
		if ($sid != ""){
			$id = $sid;
		}
        $this->tplname = "news.show";
		if ($id > 0)
		{
			$row_news = array();
			$row_news = $this->daffny->DB->selectRow("*, DATE_FORMAT(news_date, '%M %d %Y') AS news_date", "news", "WHERE id = {$id}");
			if (!empty($row_news)){
				$this->title = $row_news['title'];
				$this->input = $row_news;
				$this->input['image'] = "";
				$image = UPLOADS_PATH."news/".$id.".jpg";
				if( file_exists($image) )
				{
		  			$this->input['image'] = "<img src=\"".SITE_IN."uploads/news/".$id.".jpg\" width=\"180\" height=\"130\" class=\"content_img\" align=\"left\" alt=\"".htmlspecialchars($row_news['title'])."\" />";
				}

			}else{
				redirect(SITE_IN);
			}
        }else{
        	redirect(SITE_IN);
        }
	}

    protected function getList($useNoRecords = true)
    {
        $this->applyPager("news", "", "", "grid_pager_full");
        $sql = "SELECT *
                     , DATE_FORMAT(news_date, '%M %d, %Y') AS news_date_show
                  FROM news
                  WHERE is_hidden <> 1
                  ORDER BY news_date DESC "
                     . $this->pager->getLimit();

        return $this->getGridData($sql, $useNoRecords);
    }
}

?>