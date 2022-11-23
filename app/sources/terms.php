<?php

class AppTerms extends AppAction
{
    public function idx()
    {
        $this->tplname = "content.content";

		$cont = $this->getContent('terms', 1);
        $this->input['content'] = $cont['content'];
        $this->title = $cont['title'];
	}
}
?>