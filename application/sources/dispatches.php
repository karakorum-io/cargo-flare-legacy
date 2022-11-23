<?php

class ApplicationDispatches extends ApplicationAction
{
    public $title = "Dispatches";
    public $section = "Dispatches";
    public $tplname = "dispatches.main";
    public $breadcrumbs;

    public function idx()
    {
        $this->dispatched();
    }

    public function dispatched()
    {
        $this->loadDispatchPage(Entity::STATUS_DISPATCHED);
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('dispatches') => 'Dispatches', '' => 'Dispatched'));
    }

    public function notsigned()
    {
        $this->loadDispatchPage(Entity::STATUS_NOTSIGNED);
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('dispatches') => 'Dispatches', '' => 'Not Signed'));
    }

    public function pickedup()
    {
        $this->loadDispatchPage(Entity::STATUS_PICKEDUP);
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('dispatches') => 'Dispatches', '' => 'Picked-Up'));
    }

    public function delivered()
    {
        $this->loadDispatchPage(Entity::STATUS_DELIVERED);
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('dispatches') => 'Dispatches', '' => 'Delivered'));
    }
    public function archived()
    {
        $this->loadDispatchPage(Entity::STATUS_ARCHIVED);
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('dispatches') => 'Dispatches', '' => 'Archived'));
    }

    public function loadDispatchPage($status)
    {
        try {
            $this->applyOrder(Entity::TABLE);
            $this->order->setDefault("id", "desc");
            $em = new EntityManager($this->daffny->DB);
            $this->daffny->tpl->entities = $em->getDispatchedTo($this->order->getOrder(), $status, $_SESSION['per_page']);
            $counts = $em->getDispatchedCount();
            $this->input['notsigned_count'] = $counts[Entity::STATUS_NOTSIGNED];
            $this->input['dispatched_count'] = $counts[Entity::STATUS_DISPATCHED];
            $this->input['pickedup_count'] = $counts[Entity::STATUS_PICKEDUP];
            $this->input['delivered_count'] = $counts[Entity::STATUS_DELIVERED];
            $this->input['archived_count'] = $counts[Entity::STATUS_ARCHIVED];
            $this->input['signature_tool'] = $this->daffny->tpl->build('signature_tool');
        } catch (FDException $e) {
            redirect(getLink(''));
        }
    }

    public function signature()
    {
        $dispatchSheet = new DispatchSheet($this->daffny->DB);
        $dispatchSheet->load($_GET['id']);
        if (is_null($dispatchSheet->signature)) {
            exit();
        }

        ob_end_clean();
        header('Content-Length: ' . strlen($dispatchSheet->signature));
        header('Content-Type: image/png');
        echo $dispatchSheet->signature;
    }
}
