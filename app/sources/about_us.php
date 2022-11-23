<?php

class Appabout_us extends AppAction
{
    public function idx()
    {
        $this->tplname = "content.content";

		$cont = $this->getContent('about_us', 1);
        $this->input['content'] = $cont['content'];
        $this->title = $cont['title'];
	}
}
?>