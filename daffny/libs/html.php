<?php

/*
+---------------------------------------------------+
|                                                   |
|                   Daffny Engine                   |
|                                                   |
|                       HTML                        |
|                                                   |
|                by Alexey Kondakov                 |
|             (c)2006 - 2007 Daffny, Inc.           |
|                                                   |
|                  www.daffny.com                   |
|                                                   |
+---------------------------------------------------+
*/

class html
{
    function explode_add($add)
    {
        if ( !is_array($add) ) {
            return;
        }

        $addons = "";
        foreach ( $add as $key => $value ) {
            $addons .= " $key=\"$value\"";
        }

        return $addons;
    }


    /*--------------------------------------*/
    // A
    /*--------------------------------------*/
    function a($href, $name, $add = array())
    {
        return "<a href=\"$href\"".$this->explode_add($add).">$name</a>";
    }

    /*--------------------------------------*/
    // BR
    /*--------------------------------------*/
    function br($repeat = 1)
    {
        return str_repeat("<br />", $repeat);
    }

    /*--------------------------------------*/
    // DIV
    /*--------------------------------------*/
    function div($content = "", $add = array())
    {
        return "<div".$this->explode_add($add).">".$content."</div>";
    }

    /*--------------------------------------*/
    // FORM
    /*--------------------------------------*/
    function form_begin($action = "", $method = "", $add = array())
    {
        if ($method == "") $method = "POST";

        return "<form action=\"$action\" method=\"$method\"".$this->explode_add($add).">";
    }
    function form_end()
    {
        return "</form>";
    }

    /*--------------------------------------*/
    // IMG
    /*--------------------------------------*/
    function img($src, $add = array())
    {
        $img_size = "";
        $img_add = $this->explode_add($add);

        if (!preg_match("/height/i", $img_add) && !preg_match("/width/i", $img_add))
        {
            if ($img_tmp = @getimagesize($src)) {
                $img_size = $img_tmp[3];
            }
        }

        return "<img src=\"$src\" $img_size$img_add />";
    }

    /*--------------------------------------*/
    // INPUT
    /*--------------------------------------*/
    function input($type, $name = "", $value = "", $add = array())
    {
        $name = ($name !== "") ? " name=\"$name\"" : "";
        $value = ($value !== "") ? " value=\"$value\"" : "";

        return "<input type=\"$type\"$name$value".$this->explode_add($add)." />";
    }

    /*--------------------------------------*/
    // LABEL
    /*--------------------------------------*/
    function label($for, $text, $add = array())
    {
        return "<label for=\"$for\"".$this->explode_add($add).">$text</label>";
    }

    /*--------------------------------------*/
    // LINK
    /*--------------------------------------*/
    function link($href)
    {
        return "<link type=\"text/css\" href=\"$href\" rel=\"stylesheet\" />";
    }

    /*--------------------------------------*/
    // SCRIPT
    /*--------------------------------------*/
    function script($src)
    {
        return "<script type=\"text/javascript\" src=\"$src\"></script>";
    }

    /*--------------------------------------*/
    // SELECT
    /*--------------------------------------*/
    function select($name, $options, $selected = "", $sel_add = array(), $opt_add = array())
    {
        if ( is_array($options) )
        {
            $opt = "";
            foreach ( $options as $oval => $oname )
            {
				if (is_array($oname)) {
					$opt .= "<optgroup label='".htmlspecialchars($oval)."'>";
					foreach ($oname as $ooval => $ooname) {
						$s = ( $ooval == $selected ) ? ' selected="selected"' : "";
						$opt .= "<option value=\"$ooval\"".$this->explode_add($opt_add)."$s>$ooname</option>";
					}
					$opt .= "</optgroup>";
				} else {
					$s = ( $oval == $selected ) ? ' selected="selected"' : "";
					$opt .= "<option value=\"$oval\"".$this->explode_add($opt_add)."$s>$oname</option>";
				}
            }
        }else{
            $opt = "<option>Can't build tag select, need options array...</option>";
        }

        return "<select class='form-control' name=\"$name\"".$this->explode_add($sel_add).">$opt</select>";
    }

    /*--------------------------------------*/
    // STRONG
    /*--------------------------------------*/
    function strong($text, $add = array())
    {
        return "<strong".$this->explode_add($add).">$text</strong>";
    }

    /*--------------------------------------*/
    // SPAN
    /*--------------------------------------*/
    function span($text, $add = array())
    {
        return "<span".$this->explode_add($add).">$text</span>";
    }

    /*--------------------------------------*/
    // TITLE
    /*--------------------------------------*/
    function title($title = "")
    {
        return "<title>$title</title>";
    }

    /*--------------------------------------*/
    // TEXTAREA
    /*--------------------------------------*/
    function textarea($name, $value = "", $cols = 60, $rows = 80, $add = array())
    {
        return "<textarea name=\"$name\" cols=\"$cols\" rows=\"$rows\"".$this->explode_add($add).">$value</textarea>";
    }

    /*--------------------------------------*/
    // Java Button
    /*--------------------------------------*/
    function java_button($name, $val, $mess = "", $add = "")
    {
        if ( $mess == "" ) $mess = "Загрузка...";

        return "<div id=\"btn_".$name."\"><input type=\"submit\" name=\"".$name."\" value=\"".$val."\" onclick=\"HideButton('".$name."');\"".$add." /></div>
                <div id=\"proc_".$name."\" style=\"display: none; color: red; font-weight: bold;\">".$mess."</div>";
    }

    /*--------------------------------------*/
    // Java Button 2
    /*--------------------------------------*/
    function java_button2($name, $val, $mess = "", $add = "")
    {
        if ( $mess == "" ) $mess = "Загрузка...";

        return "<div id=\"btn_".$name."\"><input type=\"button\" name=\"".$name."\" value=\"".$val."\" onclick=\"HideButton('".$name."');\"".$add." /></div>
                <div id=\"proc_".$name."\" style=\"display: none; color: red; font-weight: bold;\">".$mess."</div>";
    }

    function ulList($items = array())
    {
        if (!count($items)) {
            return;
        }

        $out  = "<ul>";
        foreach ($items as $item) {
            $out .= "<li>$item</li>";
        }
        $out .= "</ul>";

        return $out;
    }

    function nbsp($repeat = 1)
    {
        $repeat = (int)$repeat;
        if ($repeat <= 0) {
            return;
        }

        return str_repeat("&nbsp;", $repeat);
    }
}

?>