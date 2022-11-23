<?php

class ApplicationRatings extends ApplicationAction
{

    public $title = "My Ratings";
    public $section = "My Ratings";
    public $tplname = "myaccount.ratings.list";

    public function idx()
    {
        try {
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("companyprofile") => "Profile", "" => "Ratings"));
            $this->tplname = "myaccount.ratings.list";
            $this->input = $this->getRating(getParentId());
            $cp = new CompanyProfile($this->daffny->DB);
            $cp->getByOwnerId(getParentId());
            $this->input['member_since'] = $cp->getMemberSince();
            $this->getRatingsList(getParentId());
        } catch (Exception $e) {
            redirect(getLink("application"));
        }
    }

    public function search()
    {
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("companyprofile") => "Profile", '' => "Ratings"));
        $this->tplname = "myaccount.ratings.search";
        if (!isset($_SESSION['searchq'])) {
            $_SESSION['searchq'] = "";
        }
        if (isset($_POST['searchq'])) {
            $_SESSION['searchq'] = $_POST['searchq'];
        }

        $this->applyPager("members m", "", "WHERE p.companyname LIKE '%" . mysqli_real_escape_string($this->daffny->DB->connection_id, $_SESSION['searchq']) . "%'");
        $this->applyOrder("members");

        $sql = "SELECT m.*
	    			  , p.companyname
	    			  , CONCAT_WS(', ',p.city, IF(p.state <> '', p.state, p.state_other) ) AS address
	    			  , p.phone_local
	                  FROM members m
						LEFT JOIN app_company_profile p ON p.owner_id = m.id
	                  WHERE p.companyname LIKE '%" . mysqli_real_escape_string($this->daffny->DB->connection_id, $_SESSION['searchq']) . "%'"
        . $this->order->getOrder()
        . $this->pager->getLimit();
        $this->getGridData($sql, false);
        $this->input['searchq'] = $_SESSION['searchq'];
        $this->form->TextField("searchq", 255, array(), "Enter a word or two in the company name", "</td><td>");
    }

    public function company()
    {
        $this->tplname = "myaccount.ratings.company";
        $ID = (int) get_var("id");

        //redirect if own page
        if ($ID == getParentId()) {
            redirect(getLink("ratings"));
        }

        $row = array();
        $sql = "SELECT   m.id
					   , p.*
					   , CONCAT_WS(' ', IF(is_broker=1, 'Broker/Dealership', ''), IF(is_carrier=1, 'Carrier', '')  ) AS companytype
					   , CONCAT_WS(' ', city, IF(state <> '', state, state_other) ) AS city_state
					   , DATE_FORMAT(p.insurance_expdate, '%m/%d/%Y') as insurance_expdate
                         FROM members m
                         LEFT JOIN app_company_profile p ON p.owner_id = m.id
					WHERE m.id = '" . $ID . "'";
        $row = $this->daffny->DB->selectRow($sql);
        if (!isset($row['id'])) {
            $this->setFlashError("Company not found.");
            redirect(getLink("ratings", "search"));
        }
        $this->input = $row;
        $this->input = $this->input + $this->getRating($ID);
        $cp = new CompanyProfile($this->daffny->DB);
        $cp->load($row["id"]);
        $this->input['member_since'] = $cp->getMemberSince();

        $this->title .= " " . htmlspecialchars($cp->companyname);
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("ratings") => "Ratings", '' => htmlspecialchars($cp->companyname)));

        foreach ($this->input as $key => $value) {
            $this->input[$key] = htmlspecialchars($value);
        }

        if (trim($this->input['site']) != "") {
            if (strpos($this->input['site'], "http://") === false) {
                $this->input['site'] = "http://" . $this->input['site'];
            }
            $this->input['site'] = "<a target=\"_blank\" href=\"" . $this->input['site'] . "\">" . $this->input['site'] . "</a>";
        }

        //Build comments
        $ch_arr = array();
        $rate = $this->daffny->DB->select_one("*", "app_ratings", "WHERE to_id = '" . $ID . "' AND from_id = '" . getParentId() . "' ORDER BY id DESC");
        if (!empty($rate)) {
            $ch_arr = @explode(",", $rate['commentids']);
            $this->input['rating'] = Rating::$type_name[$rate["type"]];
            $this->input['type'] = $rate["type"];
        } else {
            $this->input['rating'] = "Not Rated";
        }
        $this->daffny->tpl->rating = $this->input['rating'];

        $this->daffny->tpl->comments = array();
        $sql = "SELECT * FROM app_rating_comments
						ORDER BY id";
        $q = $this->daffny->DB->query($sql);
        while ($comments = $this->daffny->DB->fetch_row($q)) {
            if (in_array($comments['id'], $ch_arr)) {
                $comments['ch'] = "checked=\"checked\"";
            } else {
                $comments['ch'] = "";
            }
            $this->daffny->tpl->comments[] = $comments;
        }

        $this->form->ComboBox("type", Rating::$type_name + array("3" => "Remove Rating"), array('style' => "width: 100px;"), "", "");
        $this->getRatingsList($ID);
    }

    public function updaterating()
    {
        $ID = (int) get_var("id");
        if (isset($_POST['submit']) && isset($_POST['type']) && in_array((int) post_var("type"), array(0, 1, 2, 3))) {
            if ($_POST['type'] == 3) {
                $last = $this->daffny->DB->select_one("*", "app_ratings", "WHERE to_id = '" . $ID . "' AND from_id = '" . getParentId() . "' ORDER BY id DESC");
                if (isset($last['id'])) {
                    $last = $this->daffny->DB->delete("app_ratings", "id='" . $last['id'] . "'");
                }
                $this->setFlashInfo("Previous Rating has been deleted");
            } else {
                $comments = array();
                foreach ($_POST['comments'] as $key => $value) {
                    $comments[] = $value;
                }

                $ins_arr = array("from_id" => getParentId()
                    , "to_id" => $ID
                    , "type" => (int) $_POST['type']
                    , "commentids" => implode(",", $comments)
                    , "status" => 1
                    , "added" => date("Y-m-d H:i:s"),
                );
                $this->daffny->DB->insert("app_ratings", $ins_arr);
                $this->setFlashInfo("Rating has been updated");
            }
            //delete last rate
            redirect(getLink("ratings", "company", "id", $ID));
        }
        $this->setFlashError("Page not found");
        redirect(getLink("ratings", "search"));
    }

    protected function getRating($id)
    {
        $ratings_score = 0;
        $ratings_score_p = 0;
        $ratings_score_t = 0;
        $ratings_score_n = 0;
        $ratings_score1 = 0;
        $ratings_score_p1 = 0;
        $ratings_score_t1 = 0;
        $ratings_score_n1 = 0;
        $ratings_score6 = 0;
        $ratings_score_p6 = 0;
        $ratings_score_t6 = 0;
        $ratings_score_n6 = 0;
        $ratings_received = 0;

        $sql = "SELECT  'all' as period
					   , type
					   , COUNT(id) as cnt
					FROM app_ratings
				WHERE to_id = '" . $id . "' AND status = 1
					GROUP BY type
			UNION
				SELECT  'month' as period
					   , type
					   , COUNT(id) as cnt
					FROM app_ratings
				WHERE to_id = '" . $id . "' AND status = 1 AND added >= '" . date("Y-m-d H:i:s", mktime(0, 0, 0, date("n") - 1, date("d"), date("Y"))) . "'
					GROUP BY type
			UNION
				SELECT  'six' as period
					   , type
					   , COUNT(id) as cnt
					FROM app_ratings
				WHERE to_id = '" . $id . "' AND status = 1 AND added >= '" . date("Y-m-d H:i:s", mktime(0, 0, 0, date("n") - 6, date("d"), date("Y"))) . "'
					GROUP BY type";
        $q = $this->daffny->DB->query($sql);
        while ($r = $this->daffny->DB->fetch_row($q)) {
            if ($r['period'] == 'all') {
                if ($r['type'] == 0) {
                    $ratings_score_n = $r['cnt'];
                }
                if ($r['type'] == 1) {
                    $ratings_score_t = $r['cnt'];
                }
                if ($r['type'] == 2) {
                    $ratings_score_p = $r['cnt'];
                }
            }
            if ($r['period'] == 'month') {
                if ($r['type'] == 0) {
                    $ratings_score_n1 = $r['cnt'];
                }
                if ($r['type'] == 1) {
                    $ratings_score_t1 = $r['cnt'];
                }
                if ($r['type'] == 2) {
                    $ratings_score_p1 = $r['cnt'];
                }
            }
            if ($r['period'] == 'six') {
                if ($r['type'] == 0) {
                    $ratings_score_n6 = $r['cnt'];
                }
                if ($r['type'] == 1) {
                    $ratings_score_t6 = $r['cnt'];
                }
                if ($r['type'] == 2) {
                    $ratings_score_p6 = $r['cnt'];
                }
            }
        }

        $rating_received = $this->daffny->DB->selectValue("COUNT(*)", "app_ratings", "WHERE to_id = '" . $id . "' AND status = 1");

        if (($ratings_score_p + $ratings_score_t + $ratings_score_n) != 0) { //zero division
            $ratings_score = ($ratings_score_p * 1 + $ratings_score_t * 0.5 + $ratings_score_n * 0) * 100 / ($ratings_score_p + $ratings_score_t + $ratings_score_n);
        } else {
            $ratings_score = 0;
        }
        if (($ratings_score_p1 + $ratings_score_t1 + $ratings_score_n1) != 0) { //zero division
            $ratings_score1 = ($ratings_score_p1 * 1 + $ratings_score_t1 * 0.5 + $ratings_score_n1 * 0) * 100 / ($ratings_score_p1 + $ratings_score_t1 + $ratings_score_n1);
        } else {
            $ratings_score1 = 0;
        }
        if (($ratings_score_p6 + $ratings_score_t6 + $ratings_score_n6) != 0) { //zero division
            $ratings_score6 = ($ratings_score_p6 * 1 + $ratings_score_t6 * 0.5 + $ratings_score_n6 * 0) * 100 / ($ratings_score_p6 + $ratings_score_t6 + $ratings_score_n6);
        } else {
            $ratings_score6 = 0;
        }

        return array("rating_score" => number_format($ratings_score, 2, ".", ",")
            , "rating_score_p" => $ratings_score_p
            , "rating_score_t" => $ratings_score_t
            , "rating_score_n" => $ratings_score_n
            , "rating_score1" => number_format($ratings_score1, 2, ".", ",")
            , "rating_score_p1" => $ratings_score_p1
            , "rating_score_t1" => $ratings_score_t1
            , "rating_score_n1" => $ratings_score_n1
            , "rating_score6" => number_format($ratings_score6, 2, ".", ",")
            , "rating_score_p6" => $ratings_score_p6
            , "rating_score_t6" => $ratings_score_t6
            , "rating_score_n6" => $ratings_score_n6
            , "rating_received" => $rating_received,
        );
    }

    protected function getRatingsList($compid)
    {

        $this->orderdef = true;
        if (isset($_GET['gave'])) {
            $this->applyPager("app_ratings r", "", "WHERE r.from_id='" . $compid . "'");
            $this->applyOrder("app_ratings");
            $this->daffny->tpl->order->setDefault("id", "DESC");

            $sql = "SELECT r.*
	        			, CASE r.type
								WHEN 0 THEN 'ratepositive'
								WHEN 1 THEN 'rateneutral'
								WHEN 2 THEN 'ratenegative'
	        			   END AS type
	        			, CASE r.status
								WHEN 1 THEN 'Active'
								WHEN 0 THEN 'Inactive'
								WHEN 2 THEN 'Pending'
	        			   END AS status
	        			, DATE_FORMAT(r.added, '%m/%d/%Y') as added
	        			, m.companyname AS to_name
	        			, CONCAT_WS(', ', m.city, m.state) AS to_address
	        			, (SELECT
	        				SUM(CASE type
								WHEN 0 THEN 1
								WHEN 1 THEN 0.5
								WHEN 2 THEN 0
	        			   END)*100/COUNT(id)
	        					FROM app_ratings
	        					 WHERE to_id = r.to_id AND status = 1
	        			   ) AS ratings_score
	        			, (SELECT COUNT(id) FROM app_ratings WHERE to_id = r.to_id  AND status = 1 ) AS ratings_received
	                  FROM app_ratings r
						LEFT JOIN app_company_profile m ON m.owner_id = r.to_id
	                  WHERE r.from_id = '" . $compid . "' "
            . $this->order->getOrder()
            . $this->pager->getLimit();
        } else {
            $this->applyPager("app_ratings r", "", "WHERE r.to_id='" . $compid . "'");
            $this->applyOrder("app_ratings");

            $sql = "SELECT r.*
	        			, CASE r.type
								WHEN 0 THEN 'ratepositive'
								WHEN 1 THEN 'rateneutral'
								WHEN 2 THEN 'ratenegative'
	        			   END AS type
	        			, CASE r.status
								WHEN 1 THEN 'Active'
								WHEN 0 THEN 'Inactive'
								WHEN 2 THEN 'Pending'
	        			   END AS status
	        			, DATE_FORMAT(r.added, '%m/%d/%Y') as added
	        			, m.companyname AS from_name
	        			, CONCAT_WS(', ', m.city, m.state) AS from_address
	        			, (SELECT
	        				SUM(CASE type
								WHEN 0 THEN 1
								WHEN 1 THEN 0.5
								WHEN 2 THEN 0
	        			   END)*100/COUNT(id)
	        					FROM app_ratings
	        					 WHERE to_id = r.from_id AND status = 1
	        			   ) AS ratings_score
	        			, (SELECT COUNT(id) FROM app_ratings WHERE to_id=r.from_id  AND status = 1 ) AS ratings_received
	                  FROM app_ratings r
						LEFT JOIN app_company_profile m ON m.owner_id = r.from_id
	                  WHERE r.to_id = '" . $compid . "' "
            . $this->order->getOrder()
            . $this->pager->getLimit();
        }
        $this->getGridData($sql, false);
    }

    public function documents()
    {
        $ID = (int) get_var("id");
        if ($ID == getParentId()) {
            redirect(getLink("ratings"));
        }
        $this->tplname = "myaccount.ratings.documents";
        $row = array();
        $sql = "SELECT  id
					, companyname
					, is_allowed
                         FROM app_company_profile
					WHERE owner_id = '" . $ID . "'";
        $row = $this->daffny->DB->selectRow($sql);
        if (!isset($row['id'])) {
            $this->setFlashError("Company not found.");
            redirect(getLink("ratings", "search"));
        }

        $this->input['companyname'] = htmlspecialchars($row['companyname']);
        $this->title .= " " . htmlspecialchars($row['companyname']) . " Documents";
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("ratings") => "Ratings", getLink("ratings", "company", "id", (int) get_var("id")) => htmlspecialchars($row['companyname']), '' => 'Documents'));

        $this->daffny->tpl->files = array();
        if ($row['is_allowed'] == 1) {
            $sql = "SELECT *
	                  FROM app_documents
	                 WHERE owner_id = '" . $ID . "' AND status = 1
	                 ORDER BY date_uploaded";
            $FilesList = $this->daffny->DB->selectRows($sql);
            $files = array();

            foreach ($FilesList as $i => $file) {
                $files[$i] = $file;
                $files[$i]['img'] = getFileImageByType($file['type'], "Download " . $file['name_original']);
                $files[$i]['size_formated'] = size_format($file['size']);
            }
            $this->daffny->tpl->files = $files;
        }
    }

    public function getdocs()
    {
        $ID = (int) get_var("id");
        $file = $this->daffny->DB->select_one("*", "app_documents", "WHERE id = '" . $ID . "' AND status = 1");
        if (!empty($file)) {
            $row = $this->daffny->DB->select_one("*", "app_company_profile", "WHERE owner_id = '" . $file['owner_id'] . "'");
            if (!empty($row) && $row['is_allowed'] == 1) {
                $file_path = UPLOADS_PATH . "documents/" . $file["name_on_server"];
                $file_name = $file["name_original"];
                $file_size = $file["size"];
                if (file_exists($file_path)) {
                    header("Content-Type: application; filename=\"" . $file_name . "\"");
                    header("Content-Disposition: attachment; filename=\"" . $file_name . "\"");
                    header("Content-Description: \"" . $file_name . "\"");
                    header("Content-length: " . $file_size);
                    header("Expires: 0");
                    header("Cache-Control: private");
                    header("Pragma: cache");
                    $fptr = @fopen($file_path, "r");
                    $buffer = @fread($fptr, filesize($file_path));
                    @fclose($fptr);
                    echo $buffer;
                    exit(0);
                }
            }
        }
        header("HTTP/1.0 404 Not Found");
        exit(0);
    }

}
