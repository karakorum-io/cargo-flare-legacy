<?php
class ReviewOrders extends ApplicationAction {

    public function construct() {
        parent::construct();
    }

    public function idx() {        
        try {
            if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id']))
                throw new UserException("Invalid Order ID", getLink('orders'));
            $this->tplname = "orders.detail";
            $this->title = "Order Review";
                        
            $entity = new Entity($this->daffny->DB);
            $entity->load($_GET['id']);
            
            /* Documents */
            $this->daffny->tpl->entity = $entity;
            
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('orders'));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect($e->getRedirectUrl());
        }
    }
    
    public function response() {
        $this->tplname = "orders.review";
        $this->title = "Review Greetings";
        if(isset($_GET['id'])){
            $data['message']="You have already rated this Order";
        } else {
            $data['message']="This review has been forwarded to our management team.  We appreciate the feedback. ";
        }
        $this->daffny->tpl->review = $data;
    }

}
