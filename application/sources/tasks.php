<?php

class ApplicationTasks extends ApplicationAction
{

    public $title = "Tasks";
    public $section = "Tasks";

    public function idx()
    {
        try {
            $this->initGlobals();
            $this->tplname = "tasks.list";
            $this->applyOrder("app_tasks");
            $this->order->setDefault("date", "desc");
            $taskManager = new TaskManager($this->daffny->DB);
            $this->daffny->tpl->data = $taskManager->getAllVisible($_SESSION['member_id'], $this->order->getOrder(), $_SESSION['per_page']);
            $this->pager = $taskManager->getPager();
            $tpl_arr = array(
                'navigation' => $this->pager->getNavigation()
                , 'current_page' => $this->pager->CurrentPage
                , 'pages_total' => $this->pager->PagesTotal
                , 'records_total' => $this->pager->RecordsTotal,
            );
            $this->input['pager'] = $this->daffny->tpl->build('grid_pager', $tpl_arr);
        } catch (FDException $e) {
            redirect(getLink(''));
        }
    }

    public function delete()
    {
        try {
            $ID = $this->checkId();
            $task = new Task($this->daffny->DB);
            $task->load($ID);
            if ($task->sender_id == $_SESSION['member_id']) {
                $this->daffny->DB->update("app_tasks", array('deleted' => 1, 'deleted_date' => date('Y-m-d h:i:s'), 'deleted_by' => $_SESSION['member_id']), " id='" . $ID . "'");
                die(json_encode(array('success' => true)));
            }
        } catch (FDException $e) {

        }
        die(json_encode(array('success' => false)));
    }
}
