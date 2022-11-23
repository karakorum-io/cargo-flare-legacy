<?php

class ApplicationBoard extends ApplicationAction
{
    public $title = 'Freight Board';
    public $section = 'Search Vehicles';

    public function idx()
    {
        try {
            $this->tplname = "board.search";
            $this->breadcrumbs = $this->getBreadCrumbs(array(
                getLink('board') => 'Freight Board',
                'Search Vehicles',
            ));
            $this->input['task_minibox'] = $this->daffny->tpl->build('task_minibox');
            $this->daffny->tpl->regions = $this->getRegions();
            $this->daffny->tpl->states = $this->getStates();
            $this->daffny->tpl->canadaStates = $this->getCanadaStates();
            $rows = $this->daffny->DB->selectRows('`name`', '`app_vehicles_types`');
            $vehicle_types = array();
            foreach ($rows as $row) {
                $vehicle_types[] = $row['name'];
            }

            $this->daffny->tpl->vehicleTypes = $vehicle_types;
            $savedSearchManager = new SavedSearchManager($this->daffny->DB);

            $this->daffny->tpl->searches = $savedSearchManager->getByMemberId($_SESSION['member_id']);
        } catch (FDException $e) {
            redirect(getLink(''));
        }
    }

    public function search()
    {
        if (isset($_GET['delete'])) {
            if (ctype_digit((string) $_GET['delete'])) {
                $savedSearch = new SavedSearch($this->daffny->DB);
                $savedSearch->load($_GET['delete']);
                if ($savedSearch->member_id == $_SESSION['member_id']) {
                    $savedSearch->delete(null, true);
                    die(json_encode(array('success' => true)));
                }
            }
            die(json_encode(array('success' => false)));
        }
        if (count($_POST) > 0) {
            foreach ($_POST as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $k => $v) {
                        if (trim($v) == '') {
                            unset($_POST[$key][$k]);
                        }

                    }
                } else {
                    if ((trim($value) != "") && substr($key, 0, 2) == "b_") {
                        $_POST[substr($key, 2, strlen($key) - 2)] = trim($value);
                        unset($_POST[$key]);
                        continue;
                    }
                    $_POST[$key] = trim($value);
                    if ($_POST[$key] == "") {
                        unset($_POST[$key]);
                    }

                }
            }
            $fbManager = new FbManager($this->daffny->DB);
            // add new fbManager method to search without trucks
            $this->daffny->tpl->results = $fbManager->search($_POST);
        }
        $this->idx();
    }
}
