<?php

class AppWhy extends AppAction
{
    public function idx()
    {
        $this->tplname = "content.content";

		$cont = $this->getContent('why', 1);
        $this->input['content'] = $cont['content'];
        $this->title = $cont['title'];
	}
}
?>