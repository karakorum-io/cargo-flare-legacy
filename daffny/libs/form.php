<?php

require_once(ROOT_PATH . "ckeditor/ckeditor.php");

class Form {

    var $input = array();
    var $afterLabel = ":";

    public function Editor($name, $width = 600, $height = 200) {
        $initialValue = htmlspecialchars_decode($this->input[$name]);

        $CKEditor = new CKEditor();
        $CKEditor->config['width'] = $width;
        $CKEditor->config['height'] = $height;
        $CKEditor->config['pasteFromWordPromptCleanup'] = true;
        $CKEditor->config['forcePasteAsPlainText'] = true;
        $CKEditor->basePath = SITE_IN . "ckeditor/";
        $config['toolbar'] = array(
            array(/* 'Source', */'-', 'Bold', 'Italic', 'Underline', 'Strike'),
            array('Image', 'Link', 'Unlink', 'Anchor'),
	        array('Source'),
			array('Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo'),
			array('NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv',
	'-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ),
			array('Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 
        'HiddenField'),
			array('Styles','Format','Font','FontSize'),
			array('TextColor','BGColor'),
        );
        $config['EnterMode'] = "br";
        ob_start();
        $CKEditor->editor($name, $initialValue, $config);

        $this->input[$name] = ob_get_clean();
    }
	
	
	public function EditorCustom($name, $width = 600, $height = 200) {
        $initialValue = htmlspecialchars_decode($this->input[$name]);

        $CKEditor = new CKEditor();
        $CKEditor->config['width'] = $width;
        $CKEditor->config['height'] = $height;
        $CKEditor->config['pasteFromWordPromptCleanup'] = true;
        $CKEditor->config['forcePasteAsPlainText'] = true;
        $CKEditor->basePath = SITE_IN . "ckeditor/";
        $config['toolbar'] = array(
            array(/* 'Source', */'-', 'Bold', 'Italic', 'Underline', 'Strike'),
            array('Image', 'Link', 'Unlink', 'Anchor'),
	        array('Source'),
			
        );
        $config['EnterMode'] = "br";
        ob_start();
        $CKEditor->editor($name, $initialValue, $config);

        $this->input[$name] = ob_get_clean();
    }


    public function CheckBox($name, $attr = array(), $label = "", $label_sep = "", $autoInsert = true, $input_id = null) {


        $attr['name'] = $name;
        $attr['type'] = "checkbox";

        if (!isset($attr['class'])) {
            $attr['class'] = "form-check-input123 ";
        } else {
            $attr['class'] = " form-check-input123 ";
        }

        if (!isset($attr['id'])) {
            $attr['id'] = $name;
        }

        if (!isset($attr['value'])) {
            $attr['value'] = 1;
        }

	    $clearName = str_replace('[]', '', $name);

        if ((isset($this->input[$name]) && $attr['value'] == $this->input[$name]) || (is_array($this->input[$clearName]) && in_array($attr['value'], $this->input[$clearName]))) {
            $attr['checked'] = "checked";
        }

        $out = $this->simpleTag("input", $attr);
        if ($label != "") {
            $tmp = $this->afterLabel;
            $this->afterLabel = "";

            $out .= $label_sep . $this->Label($attr['id'], $label);

            $this->afterLabel = $tmp;
            unset($tmp);
        }

        if ($autoInsert) {
            $this->input[is_null($input_id)?$name:$input_id] = $out;
        }


        return  $out;
    }

    public function ComboBox($name, $options, $attr = array(), $label = "", $label_sep = "") {
        $attr['name'] = $name;
	    if (isset($attr['multiple'])) {
		    $attr['name'].= '[]';
	    }

        if (!isset($attr['class'])) {
            $attr['class'] = "form-box-combobox";
        } else {
            $attr['class'] .= " form-box-combobox";
        }

        if (!isset($attr['id'])) {
            $attr['id'] = $name;
        }

        $out = "";
        if ($label != "") {
            $out .= $this->Label($attr['id'], $label) . $label_sep;
        }

        $selected = isset($this->input[$name]) ? $this->input[$name] : "";

        $text = "";
        foreach ($options as $k => $v) {
            if (!is_array($v)) {
                $opt_attr = array('value' => $k);
	            if (is_array($selected)) {
		            if (in_array($k, $selected)) {
			            $opt_attr['selected'] = "selected";
		            }
	            } else {
	                if ($k == $selected) {
	                    $opt_attr['selected'] = "selected";
	                }
	            }

                $text .= $this->complexTag("option", $opt_attr, $v);
            } else {
                $text.= '<optgroup label="' . $k . '">';
                foreach ($v as $k1 => $v1) {
                    $opt_attr = array('value' => $k1);
	                if (is_array($selected)) {
		                if (in_array($k1, $selected)) {
			                $opt_attr['selected'] = "selected";
		                }
	                } else {
		                if ($k1 == $selected) {
			                $opt_attr['selected'] = "selected";
		                }
	                }
                    $text .= $this->complexTag("option", $opt_attr, $v1);
                }
                $text.= '</optgroup>';
            }
        }
        $out .= $this->complexTag("select", $attr, $text);

        $this->input[$name] = $out;

        return $this->input[$name];
    }

    public function ComboBox_v2($name, $options, $attr = array(), $label = "", $label_sep = "") {
        $attr['name'] = $name;
	    if (isset($attr['multiple'])) {
		    $attr['name'].= '[]';
	    }

        if (!isset($attr['class'])) {
            $attr['class'] = "form-box-combobox";
        } else {
            $attr['class'] .= " form-box-combobox";
        }

        if (!isset($attr['id'])) {
            $attr['id'] = $name;
        }

        $out = "";
        if ($label != "") {
            $out .= $this->Label($attr['id'], $label) . $label_sep;
        }

        $selected = isset($this->input[$name]) ? $this->input[$name] : "";

        $text = "";
        foreach ($options as $k => $v) {
            if (!is_array($v)) {
                $opt_attr = array('value' => $k);
	            if (is_array($selected)) {
		            if (in_array($k, $selected)) {
			            $opt_attr['selected'] = "selected";
		            }
	            } else {
	                if ($k == $selected) {
	                    $opt_attr['selected'] = "selected";
	                }
	            }

                $text .= $this->complexTag("option", $opt_attr, $v);
            } else {
                $text.= '<optgroup label="' . $k . '">';
                foreach ($v as $k1 => $v1) {
                    $opt_attr = array('value' => $k1);
	                if (is_array($selected)) {
		                if (in_array($k1, $selected)) {
			                $opt_attr['selected'] = "selected";
		                }
	                } else {
		                if ($k1 == $selected) {
			                $opt_attr['selected'] = "selected";
		                }
	                }
                    $text .= $this->complexTag("option", $opt_attr, $v1);
                }
                $text.= '</optgroup>';
            }
        }
        $out .= $this->complexTag("select", $attr, $text);

        $this->input[$name] = $out;

        return $this->input[$name];
    }

    public function helperMLTPL($name, $options, $vals = array(), $attr = array(), $label = "", $label_sep = "") {
        $attr['name'] = $name;

        if (!isset($attr['class'])) {
            $attr['class'] = "form-control";
        } else {
            $attr['class'] .= "form-control";
        }

        if (!isset($attr['id'])) {
            $attr['id'] = $name;
        }

        $out = "";
        if ($label != "") {
            $out .= $this->Label($attr['id'], $label) . $label_sep;
        }

        $text = "";
        
        
        
        foreach ($options as $k => $v) {
                $opt_attr = array('value' => $k);
                
                
                if (in_array($k, $vals)) {
                    $opt_attr['selected'] = "selected";
                }
                $text .= $this->complexTag("option", $opt_attr, $v);
        }
        $out .= $this->complexTag("select", $attr, $text);

        $this->input[$name] = $out;

        return $this->input[$name];
    }
    
    

    public function Label($for, $text = "", $attr = array()) {
        $attr['for'] = $for;

        return $this->complexTag("label  ", $attr, $text . $this->afterLabel);
    }

    public function PasswordField($name, $maxlength = 100, $attr = array(), $label = "", $label_sep = "") {
        $attr['name'] = $name;
        $attr['type'] = "password";
        $attr['maxlength'] = $maxlength;

        if (!isset($attr['class'])) {
            $attr['class'] = "form-box-textfield form-control ";
        } else {
            $attr['class'] .= " form-box-textfield form-control";
        }

        if (!isset($attr['id'])) {
            $attr['id'] = $name;
        }

        if (!isset($attr['value']) && isset($this->input[$name])) {
            $attr['value'] = $this->input[$name];
        }

        $out = "";
        if ($label != "") {
            $out .= $this->Label($attr['id'], $label) . $label_sep;
        }
        $out .= $this->simpleTag("input", $attr);

        $this->input[$name] = $out;

        return $this->input[$name];
    }

    public function Radio($name, $attr = array(), $label = "", $label_sep = "", $autoInsert = true) {
        $attr['name'] = $name;
        $attr['type'] = "radio";

        if (!isset($attr['class'])) {
            $attr['class'] = "form-box-radio";
        } else {
            $attr['class'] .= " form-box-radio";
        }

        if (!isset($attr['id'])) {
            $attr['id'] = $name;
        }

        if (!isset($attr['value'])) {
            $attr['value'] = $attr['id'];
        }

        if (isset($this->input[$name]) && $attr['value'] == $this->input[$name]) {
            $attr['checked'] = "checked";
        }

        $out = $this->simpleTag("input", $attr);
        if ($label != "") {
            $tmp = $this->afterLabel;
            $this->afterLabel = "";

            $out .= $label_sep . $this->Label($attr['id'], $label);

            $this->afterLabel = $tmp;
            unset($tmp);
        }

        if ($autoInsert) {
            $this->input[$name] = $out;
        }

        return $out;
    }

    public function TextArea($name, $cols, $rows, $attr = array(), $label = "", $label_sep = "", $htmlSpChars = false) {
        $attr['name'] = $name;
        $attr['cols'] = $cols;
        $attr['rows'] = $rows;

        if (!isset($attr['class'])) {
            $attr['class'] = "form-control";
        } else {
            $attr['class'] .= " form-control";
        }

        if (!isset($attr['id'])) {
            $attr['id'] = $name;
        }

        $text = isset($this->input[$name]) ? $this->input[$name] : "";
        if ($htmlSpChars) {
            $text = htmlspecialchars($text);
        }

        $out = "";
        if ($label != "") {
            $out .= $this->Label($attr['id'], $label) . $label_sep;
        }
        $out .= $this->complexTag("textarea", $attr, $text);

        $this->input[$name] = $out;

        return $this->input[$name];
    }
	public function Hidden($name, $maxlength = 100, $attr = array(), $label = "", $label_sep = "", $autoInsert = true, $htmlSpChars = false, $input_id = null) {
        $attr['name'] = $name;
        $attr['type'] = "hidden";
        $attr['maxlength'] = $maxlength;

        if (!isset($attr['class'])) {
            $attr['class'] = "form-box-textfield";
        } else {
            $attr['class'] .= " form-box-textfield";
        }

        if (!isset($attr['id'])) {
            $attr['id'] = $name;
        }

	    $nameId = preg_replace('/[^0-9]/i', '', $name);
	    $clearName = preg_replace('/\[.*\]/i', '', $name);
	    if (is_array($this->input[$clearName]) && array_key_exists($nameId, $this->input[$clearName])) {
		    $attr['value'] = $htmlSpChars?htmlspecialchars($this->input[$clearName][$nameId]):$this->input[$clearName][$nameId];
	    } elseif (!isset($attr['value']) && isset($this->input[$name])) {
            $attr['value'] = $htmlSpChars?htmlspecialchars($this->input[$name]):$this->input[$name];
        }

        $out = "";
        if ($label != "") {
            if ($label == "&nbsp;") {
                $out .= $label . $label_sep;
            } else {
                $out .= $this->Label($attr['id'], $label) . $label_sep;
            }
        }
        $out .= $this->simpleTag("input", $attr);

        if ($autoInsert) {
            $this->input[is_null($input_id)?$name:$input_id] = $out;
        }

        return $out;
    }
    public function TextField($name, $maxlength = 100, $attr = array(), $label = "", $label_sep = "", $autoInsert = true, $htmlSpChars = false, $input_id = null) {
        $attr['name'] = $name;
        $attr['type'] = "text";
        $attr['maxlength'] = $maxlength;

        if (!isset($attr['class'])) {
            $attr['class'] =   "form-box-textfield form-control";
        } else {
            $attr['class'] =  "form-box-textfield form-control";
        }

      
        if (!isset($attr['id'])) {
            $attr['id'] = $name;
        }

	    $nameId = preg_replace('/[^0-9]/i', '', $name);
	    $clearName = preg_replace('/\[.*\]/i', '', $name);
	    if (is_array($this->input[$clearName]) && array_key_exists($nameId, $this->input[$clearName])) {
		    $attr['value'] = $htmlSpChars?htmlspecialchars($this->input[$clearName][$nameId]):$this->input[$clearName][$nameId];
	    } elseif (!isset($attr['value']) && isset($this->input[$name])) {
            $attr['value'] = $htmlSpChars?htmlspecialchars($this->input[$name]):$this->input[$name];
        }

        $out = "";
        if ($label != "") {
            if ($label == "&nbsp;") {
                $out .= $label . $label_sep;
            } else {
                $out .= $this->Label($attr['id'], $label) . $label_sep;
            }
        }
        $out .= $this->simpleTag("input", $attr);

        if ($autoInsert) {
            $this->input[is_null($input_id)?$name:$input_id] = $out;
        }

        return $out;
    }

    public function MoneyField($name, $maxlength = 100, $attr = array(), $label = "", $label_sep = "", $autoInsert = true, $htmlSpChars = false, $input_id = null) {
        if (!isset($attr['class'])) {
            $attr['class'] = "money";
        } else {
            $attr['class'] .= " money";
        }
        return $this->TextField($name, $maxlength, $attr, $label, $label_sep, $autoInsert, $htmlSpChars, $input_id);
    }

    public function DateField($name, $maxlength = 10, $attr = array(), $label = "", $label_sep = "", $autoInsert = true, $htmlSpChars = false) {
        if (!isset($attr['class'])) {
            $attr['class'] = "kt_datepicker_1";
        } else {
            $attr['class'] .= " kt_datepicker_1";
        }
        return $this->TextField($name, $maxlength, $attr, $label, $label_sep, $autoInsert, $htmlSpChars);
    }

    public function FileFiled($name, $attr = array(), $label = "", $label_sep = "") {
        $attr['name'] = $name;
        $attr['type'] = "file";

        if (!isset($attr['class'])) {
            $attr['class'] = "form-box-filefield";
        } else {
            $attr['class'] .= " form-box-filefield";
        }

        if (!isset($attr['id'])) {
            $attr['id'] = $name;
        }

        $out = "";
        if ($label != "") {
            $out .= $this->Label($attr['id'], $label) . $label_sep;
        }
        $out .= $this->simpleTag("input", $attr);

        $this->input[$name] = $out;

        return $this->input[$name];
    }

    public function helperYesNo($name, $attr = array(), $valY = "Yes", $valN = "No", $textY = "Yes", $textN = "No") {
        $out = $this->Radio($name, $attr + array('value' => $valY, 'id' => $name . "_y"), $textY, "", false);
        $out .= "&nbsp;";
        $out .= $this->Radio($name, $attr + array('value' => $valN, 'id' => $name . "_n"), $textN, "", false);

        $this->input[$name] = $out;
    }

    public function helperSendType($name, $attr = array()) {
        $out = $this->Radio($name, $attr + array('value' => 0, 'id' => $name . "_0"), "Text", "&nbsp;", false);
        $out .= "&nbsp;&nbsp;&nbsp;";
        $out .= $this->Radio($name, $attr + array('value' => 1, 'id' => $name . "_1"), "HTML", "&nbsp;", false);

        $this->input[$name] = $out;
    }

    public function helperDefineAs($name, $attr = array()) {
        $out = $this->Radio($name, $attr + array('value' => 0, 'id' => $name . "_0"), "Orders that were created during selected time period and dispatched at any time.", "&nbsp;", false);
        $out .= "<br />";
        $out .= $this->Radio($name, $attr + array('value' => 1, 'id' => $name . "_1"), "Orders that were created during any time period and dispatched during selected time period.", "&nbsp;", false);
        $this->input[$name] = $out;
    }

    public function helperSendCopy($name, $attr = array()) {
        $out = $this->Radio($name, $attr + array('value' => 0, 'id' => $name . "_0"), "Only send leads to my account", "&nbsp;", false);
        $out .= "<br />";
        $out .= $this->Radio($name, $attr + array('value' => 1, 'id' => $name . "_1"), "Send leads to my account and send a copy to this email address:", "&nbsp;", false);
        $this->input[$name] = $out;
    }

    public function helperShippers($name, $attr = array()) {
        $out = $this->Radio($name, $attr + array('value' => 0, 'id' => $name . "_0"), "None", "&nbsp;", false);
        $out .= "&nbsp;&nbsp;&nbsp;";
        $out .= $this->Radio($name, $attr + array('value' => 1, 'id' => $name . "_1"), "View", "&nbsp;", false);
        $out .= "&nbsp;&nbsp;&nbsp;";
        $out .= $this->Radio($name, $attr + array('value' => 2, 'id' => $name . "_2"), "View / Edit", "&nbsp;", false);
        $out .= "&nbsp;&nbsp;&nbsp;";
        $out .= $this->Radio($name, $attr + array('value' => 3, 'id' => $name . "_3"), "View / Edit / Add", "&nbsp;", false);
        $this->input[$name] = $out;
    }
    
    /**
     * function added by chetu to generate privilege radio button for carriers and 
     * locations it will be shown on user privileges page
     * 
     * @param type $name name of controls
     * @param type $attr attributes to be applied to the control
     */
    public function helperCarriers($name, $attr = array()){
        $out = $this->Radio($name, $attr + array('value' => 0, 'id' => $name . "_0"), "None", "&nbsp;", false);
        $out .= "&nbsp;&nbsp;&nbsp;";
        $out .= $this->Radio($name, $attr + array('value' => 1, 'id' => $name . "_1"), "View", "&nbsp;", false);
        $out .= "&nbsp;&nbsp;&nbsp;";
        $out .= $this->Radio($name, $attr + array('value' => 2, 'id' => $name . "_2"), "View / Edit", "&nbsp;", false);        
	$out .= "&nbsp;&nbsp;&nbsp;";
        $out .= $this->Radio($name, $attr + array('value' => 3, 'id' => $name . "_3"), "View / Edit / Add", "&nbsp;", false);
        $this->input[$name] = $out;
    }
    
    /**
     * helper function added by chetu to generate control for duplicate shippers
     * and carriers to implement access privileges for them
     * @param type $name name of control being genrated
     * @param type $attr attributes to be applied to the control
     */
    public function helperDuplicateAccounts($name, $attr = array()){
        $out = $this->Radio($name, $attr + array('value' => 0, 'id' => $name . "_0"), "No", "&nbsp;", false);
        $out .= "&nbsp;&nbsp;&nbsp;";
        $out .= $this->Radio($name, $attr + array('value' => 1, 'id' => $name . "_1"), "Yes", "&nbsp;", false);
        $this->input[$name] = $out;
    }

    public function helperShippersNew($name, $attr = array()) {
        // Chetu added more privilege options as per client's requirement
        $out = $this->Radio($name, $attr + array('value' => 0, 'id' => $name . "_0"), "None", "&nbsp;", false);
        $out .= "&nbsp;&nbsp;&nbsp;";
        $out .= $this->Radio($name, $attr + array('value' => 1, 'id' => $name . "_1"), "View Mine / Edit Mine", "&nbsp;", false);
        $out .= "&nbsp;&nbsp;&nbsp;";
        $out .= $this->Radio($name, $attr + array('value' => 2, 'id' => $name . "_2"), "View All / Edit Mine", "&nbsp;", false);        
        $out .= "&nbsp;&nbsp;&nbsp;<br/>";
        $out .= $this->Radio($name, $attr + array('value' => 3, 'id' => $name . "_3"), "View All / Edit All", "&nbsp;", false);
        $out .= "&nbsp;&nbsp;&nbsp;";
        $out .= $this->Radio($name, $attr + array('value' => 4, 'id' => $name . "_4"), "View All", "&nbsp;", false);
        $this->input[$name] = $out;
    }

    public function helperLeads($name, $attr = array()) {
        $out = $this->Radio($name, $attr + array('value' => 0, 'id' => $name . "_0"), "View Mine / Edit Mine", "&nbsp;", false);
        $out .= "&nbsp;&nbsp;&nbsp;";
        $out .= $this->Radio($name, $attr + array('value' => 1, 'id' => $name . "_1"), "View All / Edit Mine", "&nbsp;", false);
        $out .= "&nbsp;&nbsp;&nbsp;";
        $out .= $this->Radio($name, $attr + array('value' => 2, 'id' => $name . "_2"), "View All / Edit All", "&nbsp;", false);
        $this->input[$name] = $out;
    }
	
	 public function helperLeadsCustom($name, $attr = array(),$label) {
        $out = "<b>".$label."</b><br>";
        $out .= $this->Radio($name, $attr + array('value' => 0, 'id' => $name . "_0"), "View Mine / Edit Mine", "&nbsp;", false);
        $out .= "&nbsp;&nbsp;&nbsp;<br>";
        $out .= $this->Radio($name, $attr + array('value' => 1, 'id' => $name . "_1"), "View All / Edit Mine", "&nbsp;", false);
        $out .= "&nbsp;&nbsp;&nbsp;<br>";
        $out .= $this->Radio($name, $attr + array('value' => 2, 'id' => $name . "_2"), "View All / Edit All", "&nbsp;", false);
        
        $this->input[$name] = $out;
    }
    
    public function customValueShare($name,$value){
        $value = implode(",",$value);        
        $out = '<input type="hidden" name="'.$name.'" id="'.$name.'" value="'.$value.'">';
        $this->input[$name] = $out;
    }
     public function customValueShare1($name,$value){
        $out = '<input type="hidden" name="'.$name.'" id="'.$name.'" value="'.$value.'">';
        $this->input[$name] = $out;
    }

   public function helperNotes($name, $attr = array()) {
        $out = $this->Radio($name, $attr + array('value' => 0, 'id' => $name . "_0"), "View All", "&nbsp;", false);
        $out .= "&nbsp;&nbsp;&nbsp;";
        $out .= $this->Radio($name, $attr + array('value' => 1, 'id' => $name . "_1"), "View All / Edit Mine", "&nbsp;", false);
        $out .= "&nbsp;&nbsp;&nbsp;";
        $out .= $this->Radio($name, $attr + array('value' => 2, 'id' => $name . "_2"), "View All / Edit All", "&nbsp;", false);
        $this->input[$name] = $out;
    }
	
	public function helperGWType($name, $attr = array(),$companytype=0) {
        $out = $this->Radio($name, $attr + array('value' => 'internally', 'id' => "pt_internally"), "Record internally", "&nbsp;", false);
        $out .= "&nbsp;&nbsp;&nbsp;";
		
		  $out .= $this->Radio($name, $attr + array('value' => 'carrier', 'id' => "pt_carrier"), "Paid Carrier", "&nbsp;", false);
          $out .= "&nbsp;&nbsp;&nbsp;";
		if($companytype==1){
		$out .= $this->Radio($name, $attr + array('value' => 'terminal', 'id' => "pt_terminal"), "Paid Terminal", "&nbsp;", false);
        $out .= "&nbsp;&nbsp;&nbsp;";
		}
        $out .= $this->Radio($name, $attr + array('value' => 'gateway', 'id' => "pt_gateway"), "Process through gateway", "&nbsp;", false);
        $this->input[$name] = $out;
    }
	
	/*
    public function helperGWType($name, $attr = array()) {
        $out = $this->Radio($name, $attr + array('value' => 'internally', 'id' => "pt_internally"), "Record internally", "&nbsp;", false);
        $out .= "&nbsp;&nbsp;&nbsp;";
        $out .= $this->Radio($name, $attr + array('value' => 'gateway', 'id' => "pt_gateway"), "Process through gateway", "&nbsp;", false);
        $this->input[$name] = $out;
    }
*/
/*
    public function helperPaymentType($name, $deposit = "", $tariff = "", $other = "", $attr = array()) {
        $out = "<tr><td>";
        $out .= $this->Radio($name, $attr + array('value' => "deposit", 'id' => $name . "_deposit"), "Deposit: " . $deposit, "</td><td>", false);
        $out .= "</td></tr><tr><td>";
        $out .= $this->Radio($name, $attr + array('value' => "balance", 'id' => $name . "_balance"), "Balance Due: " . $tariff, "</td><td>", false);
        $out .= "</td></tr><tr><td>";
        $out .= $this->Radio($name, $attr + array('value' => "other", 'id' => $name . "_other"), "Other Amount: " . $other, "</td><td>", false);
        $out .= "</td></tr>";
        $this->input[$name] = $out;
    }
	
	
	public function helperPaymentType($name, $deposit = "", $tariff = "", $other = "", $attr = array()) {
        $out = "<tr><td>";
        $out .= $this->Radio($name, $attr + array('value' => "deposit", 'id' => $name . "_deposit"), "Deposit: " . ("$ " . number_format((float)$deposit, 2, ".", ",")), "</td><td>", false);
        $out .= "<input type='hidden' name='deposit_pay' value='".$deposit."'></td></tr><tr><td>";
        $out .= $this->Radio($name, $attr + array('value' => "balance", 'id' => $name . "_balance"), "Balance Due: " . ("$ " . number_format((float)$tariff, 2, ".", ",")), "</td><td>", false);
        $out .= "<input type='hidden' name='tariff_pay' value='".$tariff."'></td></tr><tr><td>";
        $out .= $this->Radio($name, $attr + array('value' => "other", 'id' => $name . "_other"), "Other Amount: " . $other, "</td><td>", false);
        $out .= "</td></tr>";
        $this->input[$name] = $out;
    }
*/

 public function helperPaymentType($name, $deposit = "", $tariff = "", $other = "", $attr = array()) {
        $out = "<tr><td>";
        $out .= $this->Radio($name, $attr + array('value' => "deposit", 'id' => $name . "_deposit"), "Deposit: <label id='".$name ."_dvalue'>" . $deposit."</label>", "</td><td>", false);
        $out .= "<input type='hidden' name='deposit_pay' id='deposit_pay' value='".$deposit."'></td></tr><tr><td>";
        $out .= $this->Radio($name, $attr + array('value' => "balance", 'id' => $name . "_balance"), "Balance Due: <label id='".$name ."_bvalue'>" . $tariff."</label>", "</td><td>", false);
        $out .= "<input type='hidden' name='tariff_pay' id='tariff_pay' value='".$tariff."'></td></tr><tr><td>";
        $out .= $this->Radio($name, $attr + array('value' => "other", 'id' => $name . "_other"), "Other Amount: " . $other, "</td><td>", false);
        $out .= "</td></tr>";
        $this->input[$name] = $out;
    }

    public function helperEmailType($name, $attr = array()) {
        $out = $this->Radio($name, $attr + array('value' => 0, 'id' => $name . "_0"), "Automatically", "&nbsp;", false);
        $out .= "<br />";
        $out .= $this->Radio($name, $attr + array('value' => 1, 'id' => $name . "_1"), "Manually, no confirmation required", "&nbsp;", false);
        $out .= "<br />";
        $out .= $this->Radio($name, $attr + array('value' => 2, 'id' => $name . "_2"), "Manually, require click-confirmation", "&nbsp;", false);
        $this->input[$name] = $out;
    }

    public function helperSurchargeType($name, $attr = array()) {
        $out = $this->Radio($name, $attr + array('value' => 0, 'id' => $name . "_0"), "Fixed Amount", "&nbsp;", false);
        $out .= "<br />";
        $out .= $this->Radio($name, $attr + array('value' => 1, 'id' => $name . "_1"), "Percentage of base price", "&nbsp;", false);
        $this->input[$name] = $out;
    }

    private function getAttributes($attr = array()) {
        if (!count($attr)) {
            return;
        }

        $attributes = "";
        foreach ($attr as $k => $v) {
            $attributes .= " " . sprintf('%s="%s"', $k, $v);
        }

        return $attributes;
    }

    private function simpleTag($tag, $attr) {
        return sprintf('<%s%s />', $tag, $this->getAttributes($attr));
    }

    private function complexTag($tag, $attr, $text = "") {
        return sprintf('<%1$s%2$s>%3$s</%1$s>', $tag, $this->getAttributes($attr), $text);
    }

}

?>