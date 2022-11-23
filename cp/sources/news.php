<?php
/***************************************************************************************************
* News CP Class                                                                 *
*                                                                              					   *
*                                                                                                  *
* Client: 	FreightDragon                                                                          *
* Version: 	1.0                                                                                    *
* Date:    	2011-09-30                                                                             *
* Author:  	C.A.W., Inc. dba INTECHCENTER                                                          *
* Address: 	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076                             *
* E-mail:	techsupport@intechcenter.com                                                           *
* CopyRight 2011 FreightDragon. - All Rights Reserved                                              *
****************************************************************************************************/

require_once(DAFFNY_PATH."libs/upload.php");
require_once(DAFFNY_PATH."libs/cropper.php");

class Cpnews extends CpAction
{
    /**
    * Index method
    *
    */
    var $title = "News";

    public function idx()
    {
        $this->tplname = "news.list";

        if (!$records = $this->getList(false))
        {
            return;
        }

        foreach ($records as $i => $record)
        {
            $record['content'] = trim(strip_tags($record['content']));

            if ($record['content'] == "") {
                continue;
            }
			$records[$i]['content'] = cutContent($record['content'], 10);
        }
        $this->daffny->tpl->data = $records;
    }

    /**
    * Get a list on news
    *
    */
    protected function getList($useNoRecords = true)
    {
        $this->applyPager("news");
        $order = $this->applyOrder("news");
        $order->setDefault("news_date", "desc");

        $sql = "SELECT *
                     , DATE_FORMAT(news_date, '%d/%m/%Y') AS news_date_show
                  FROM news"
                     . $this->order->getOrder()
                     . $this->pager->getLimit();

        return $this->getGridData($sql, $useNoRecords);
    }

    /**
    * Edit or add new news
    *
    */
    public function edit()
    {
        $id = (int)get_var("id");

        $this->title .= ($id > 0) ? " - Edit" : " - Add";
        $this->tplname = "news.form";
        $this->input = $this->SaveFormVars();

        //$this->getList(false);

        if (isset($_POST['submit']))
        {
            $sql_arr = array(
                'title'           => post_var("title")
              , 'content'         => post_var("content")
              , 'news_date'       => $this->validateDate(post_var("news_date"), "Date")
              , 'is_featured'     => (post_var("is_featured") == "1"?1:0)
              , 'is_hidden'       => (post_var("is_hidden") == "1"?1:0)
            );

            $this->isEmpty("title", "Title");
            $this->isEmpty("news_date", "Date");
            $this->isEmpty("content", "Content");

            if (!count($this->err))
            {
                if ($id > 0)
                {
                    $this->daffny->DB->update("news", $sql_arr, "id = $id");
                    $_SESSION['inf_message'] = "Information has been updated.";
                }
                else
                {
                    $sql_arr['create_date'] = "now()";
                    $this->daffny->DB->insert("news", $sql_arr);
                    $_SESSION['inf_message'] = "Information has been added.";
                    $id = $this->daffny->DB->get_insert_id();
                    @rename(UPLOADS_PATH."news/0.jpg", UPLOADS_PATH."news/".$id.".jpg");
                }
	            if ($sql_arr['is_featured'] == 1){
					$this->daffny->DB->update("news", array('is_featured'=>0), "id <> $id");
				}
                redirect(getLink("news"));
            }
        }
        else
        {
            if ($id > 0)
            {
                $row_news = $this->daffny->DB->selectRow("*, DATE_FORMAT(news_date, '%m/%d/%Y') AS news_date", "news", "WHERE id = {$id}");
				if(empty($row_news)){
					redirect(getLink('news'));
				}
                $row = array();
                foreach ($row_news as $key=>$value){
                	$row[$key] = htmlspecialchars($value);
                }
                $this->input = $row;
            }
        }


		$this->form->Editor('content', 820, 300);
        $this->form->TextField("title", 255, array('style' => "width: 400px;"), $this->requiredTxt."Title", "</td><td>");
        $this->form->TextField("news_date", 10, array('style' => "width: 100px;"), $this->requiredTxt."Date", "</td><td>");
        $this->form->CheckBox("is_featured", array(), "Show on main page", "&nbsp;");
        $this->form->CheckBox("is_hidden", array(), "Hide", "&nbsp;");
        $this->form->FileFiled("image", array(), "Upload image", "</td><td>");
		$this->input['image_file'] = $this->getImage($id);
    }

    /**
    * Delete
    *
    */

	public function delete()
    {
        $ID = $this->checkId();
    	$out = array('success' => false);
    	try {
	        $this->daffny->DB->delete("news", "id = $ID");
	        if ($this->daffny->DB->isError){
						throw new Exception($this->getDBErrorMessage());
			}else{
				$out = array('success' => true);
			}
		} catch (FDException $e) {}
		die(json_encode($out));
    }

    protected function uploadFile()
    {
        $newsID = (int)$_POST["news_id"];
        $upload = new upload();
        $upload->out_file_dir = UPLOADS_PATH."news/";
        $upload->max_file_size = 10000000;
        $upload->form_field = "image";
        $upload->make_script_safe = 1;
		$upload->allowed_file_ext = array("jpg", "gif", "png", "jpeg");
        $upload->save_as_file_name = md5(time()."-".rand());
        $upload->upload_process();
        switch ($upload->error_no)
        {
            case 0:
            {
				$cropper = new ImageCropper;
				$cropper->resize_and_crop(UPLOADS_PATH."news/".$upload->save_as_file_name, UPLOADS_PATH."news/".$newsID.".jpg", 180, 130);
				$out = "<div id=\"file-".$newsID."\"><img src=\"".SITE_IN."uploads/news/".$newsID.".jpg?".md5(rand(10000,99999))."\" width=\"180\" height=\"130\" class=\"content_img\" /><br /><a href=\"#\" onclick=\"return deleteFile('".getLink("news", "delete-file" )."', ".$newsID.", ".$newsID.");\">delete</a></div>";
				@unlink(UPLOADS_PATH."news/".$upload->save_as_file_name);
                return $out;
            }

            case 1:
                return "ERROR:File not selected or empty.";
            case 2:
            case 5:
                return "ERROR:Invalid File Extension";
            case 3:
                return "ERROR:File too big";
            case 4:
                return "ERROR:Cannot move uploaded file";
        }
		return true;
    }

	public function upload_file()
    {
        $result = $this->uploadFile();
        echo $result;
        exit();
    }

    protected function getImage($id)
    {
    	$image = UPLOADS_PATH."news/".$id.".jpg";
		if( file_exists($image) )
		{
  			return  "<div id=\"file-".$id."\"><img src=\"".SITE_IN."uploads/news/".$id.".jpg\" width=\"180\" height=\"130\" class=\"content_img\" /><br /><a href=\"#\" onclick=\"return deleteFile('".getLink("news", "delete-file" )."', ".$id.", ".$id.");\">delete</a></div>";
		}else{
			return "";
		}
    }

    public function delete_file()
    {
        $result = "";
        $id = (int)post_var("id");

		if ($id <= 0){
            $result = "Action N/A";
        }else{

        	$file = UPLOADS_PATH."news/".$id.".jpg";
	        if (file_exists($file)){
	            @unlink($file);
	        }
        }
        echo $result;
        exit();
    }
}

?>