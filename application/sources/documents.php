<?php

require_once DAFFNY_PATH . "libs/upload.php";

class ApplicationDocuments extends ApplicationAction
{
    public $title = "My Documents";
    public $section = "My Documents";
    public $tplname = "myaccount.documents";

    public function construct()
    {
        if (!$this->check_access('preferences')) {
            $this->setFlashError('Access Denied.');
            redirect(getLink());
        }
        return parent::construct();
    }

    public function idx()
    {
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("companyprofile") => "My Account", '' => "Documents"));
        $this->check_access("settings");

        if (isset($_POST['submit'])) {
            $sql_arr = array(
                "is_allowed" => (post_var("is_allowed") == "1" ? 1 : 0),
            );

            if (!count($this->err)) {
                $upd_arr = $this->daffny->DB->PrepareSql("app_company_profile", $sql_arr);
                $this->daffny->DB->update("app_company_profile", $upd_arr, "owner_id = '" . getParentId() . "'");
                if ($this->dbError()) {
                    $this->setFlashError("Access Denied. Undefined error.");
                    redirect(getLink("documents"));
                } else {
                    $this->setFlashInfo("Status has been updated.");
                    redirect(getLink("documents"));
                }
            } else {
                $inp = $sql_arr;
            }
        } else {
            $inp = $this->daffny->DB->selectRow("*", "app_company_profile", "WHERE owner_id='" . getParentId() . "'");
        }

        foreach ($inp as $key => $value) {
            $this->input[$key] = htmlspecialchars($value);
        }
        $this->daffny->tpl->files = $this->getFiles();
        $this->form->FileFiled("files_upload", array(), "Upload file", "</td><td>");
        $this->form->CheckBox("is_allowed", array(), "Allow Others to Access my Current Document Packet", "&nbsp;");
    }
    
    public function upload_file()
    {
        $id = getParentId();
        $upload = new upload();
        $upload->out_file_dir = UPLOADS_PATH . "documents/";
        $upload->max_file_size = 100000000;
        $upload->form_field = "file";
        $upload->make_script_safe = 1;
        $upload->allowed_file_ext = array("pdf", "doc", "docx");
        $upload->save_as_file_name = md5(time() . "-" . rand()) . time();
        $upload->upload_process();
        switch ($upload->error_no) {
            case 0:
                {
                    $sql_arr = array(
                        'name_original' => $_FILES[$upload->form_field]['name']
                        , 'name_on_server' => $upload->save_as_file_name
                        , 'size' => $_FILES[$upload->form_field]['size']
                        , 'type' => $upload->file_extension
                        , 'date_uploaded' => "now()"
                        , 'owner_id' => getParentId()
                        , 'status' => 0,
                    );
                    $ins_arr = $this->daffny->DB->PrepareSql("app_documents", $sql_arr);
                    $this->daffny->DB->insert("app_documents", $ins_arr);
                    $insid = $this->daffny->DB->get_insert_id();
                    $out = getFileImageByType($upload->file_extension) . " ";
                    $out .= $_FILES[$upload->form_field]['name'];
                    $out .= " (" . size_format($_FILES[$upload->form_field]['size']) . ") " . colorRate("Pending") . " ";
                    $out .= "<a href=\"#\" onclick=\"return deleteFile('" . getLink("documents", "delete-file") . "','" . $insid . "');\"><img src=\"" . SITE_IN . "images/icons/delete.png\" alt=\"delete\" style=\"vertical-align:middle;\" width=\"16\" height=\"16\" /></a>";
                    die("<li id=\"file-" . $insid . "\">" . $out . "</li>");
                }

            case 1:
                die("ERROR:File not selected or empty.");
            case 2:
            case 5:
                die("ERROR:Invalid File Extension");
            case 3:
                die("ERROR:File too big");
            case 4:
                die("ERROR:Cannot move uploaded file");
        }
        exit;
    }

    public function delete_file()
    {
        $out = array('success' => false);
        $id = (int) get_var('id');
        try {
            if ($row = $this->daffny->DB->selectRow('*', "app_documents", "WHERE id = '$id' AND owner_id = '" . getParentId() . "'")) {
                if ($this->daffny->DB->isError) {
                    throw new Exception($this->getDBErrorMessage());
                } else {
                    $file_path = UPLOADS_PATH . "documents/" . $row["name_on_server"];
                    $this->daffny->DB->delete('app_documents', "id = '" . quote($id) . "'");
                    $out = array('success' => true);
                    @unlink($file_path);
                }
            }
        } catch (FDException $e) {}
        die(json_encode($out));
    }

    protected function getFiles()
    {
        $sql = "SELECT *
        		, IF(status=1,'Approved','Pending') AS status
                  FROM app_documents
                 WHERE owner_id = '" . getParentId() . "'
                 ORDER BY date_uploaded";
        $FilesList = $this->daffny->DB->selectRows($sql);
        $files = array();
        foreach ($FilesList as $i => $file) {
            $files[$i] = $file;
            $files[$i]['img'] = getFileImageByType($file['type'], "Download " . $file['name_original']);
            $files[$i]['size_formated'] = size_format($file['size']);
        }
        return $files;
    }
}
