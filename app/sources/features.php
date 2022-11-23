<?php

class AppFeatures extends AppAction
{
    public function idx()
    {    
    	
        $this->tplname = "content.content";
		$cont = $this->getContent('features', 1);
		
        $this->input['content'] = $cont['content'];
        $this->title = $cont['title'];
	}
}
?>