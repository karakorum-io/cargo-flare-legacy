<?php

class AppPrivacy extends AppAction
{
    public function idx()
    {
        $this->tplname = "content.content";

		$cont = $this->getContent('privacy', 1);
        $this->input['content'] = $cont['content'];
        $this->title = $cont['title'];
	}
}
?>