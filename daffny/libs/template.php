<?php
/*
+---------------------------------------------------+
|                                                   |
|                   Daffny Engine                   |
|                                                   |
|                     Template                      |
|                                                   |
|                by Alexey Kondakov                 |
|             (c)2006 - 2007 Daffny, Inc.           |
|                                                   |
|                  www.daffny.com                   |
|                                                   |
+---------------------------------------------------+
*/
class template
{
    /**
	* Daffny Engine®
	*
	* @var object
	*/
    var $daffny;
    
	/**
	* Templates Path
	*
	* @var string
	*/
    var $path;
    
	/**
	* Template name
	*
	* @var string
	*/
    var $name;
	
	/**
	* Template Vars
	*
	* @var string
	*/
    var $search = array();
    
	/**
	* Template input vars
	*
	* @var array
	*/
    var $replace = array();
    
    
    
	/**
    * Constructor
    * 
    * 
    */
	function template()
    { 
		$this->path = TPL_PATH;
    }
    
	/**
    * Get Template file full path
    * 
    * 
    */
    function build($name, $replacement = array())
    {
        // Get full template path
        $file = $this->get_full_path($name);
        
        // File exists?
        if ( !file_exists($file) )
        {
            return "File <b>".$file."</b> not exists.<br />\n";
        }
        
        // Get template content 
        $content = $this->get_content($file);
        
        if ( count($replacement) > 0 )
        {
            return $this->get_parsed_from_array($content, $replacement);
        }
        
        return $this->get_parsed($content);
    }
    
    function get_var_names($tpl_name)
    {
        // Get full template path
        $file = $this->get_full_path($tpl_name);
        
        // File exists?
        if ( !file_exists($file) )
        {
            $this->daffny->error("File <b>".$file."</b> not exists.", "Create this file");
        }
        
        // Get template content 
        $content = $this->get_content($file);
        
        return $this->get_vars($content);
    }
    
	/*
	* Get Template file full path
	* 
    * 
	*/
    function get_full_path($name)
    {
        $name = str_replace(".", "/", $name);
        
        $path = $this->path."/".$name.".php";
        
        $file = str_replace("//", "/", $path);
        
        return $file;
    }
    
    /*
	* Get Template file full path
	*
	* 
	*/
    function get_content($file)
    {
        ob_start();
        include($file);
        $content = ob_get_contents();
        ob_end_clean();
        
        return $content;
    }
    
	/*
	* Get Parsed
	* 
	* 
	*/
    function get_parsed($content)
    {
        if ( count($this->search) && count($this->replace) )
        {
            return str_replace($this->search, $this->replace, $content);
        }
        
        return $content;
    }
    
	/*
	* Get Parsed from array
	* 
	* 
	*/
    function get_parsed_from_array($content, $arr)
    {
        if (!is_array($arr) || !count($arr)) {
            return $content;
        }
        
        $search = $replace = array();
        foreach ( $arr as $k => $v )
        {
			if (is_array($v)) continue;
            $search[] = "@$k@";
            $replace[] = $v;
        }
        
        return @str_replace($search, $replace, $content);
    }
    
	/*
	* Get Template Vars
	* 
	* 
	*/
    function get_vars($content)
    {
        $vars = array();
        
        if ( preg_match_all("/@([a-zA-Z0-9_]+)@/", $content, $res) )
        {
            $vars = $res[1];
        }
        
        return $vars;
    }
    
	/*
	* Asign Var
	* 
	* 
	*/
    function assign_var($search, $replace)
    {
        $this->search[] = "@$search@";
        $this->replace[] = $replace;
    }
    
}
?>