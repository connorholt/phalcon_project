<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;


class Ipv4Controller extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->persistent->parameters = null;
    }

    /**
     * Searches for ipv4
     */
    public function searchAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'Ipv4', $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = [];
        }
        $parameters["order"] = "id";

        $ipv4 = Ipv4::find($parameters);
        if (count($ipv4) == 0) {
            $this->flash->notice("The search did not find any ipv4");

            $this->dispatcher->forward([
                "controller" => "ipv4",
                "action" => "index"
            ]);

            return;
        }

        $paginator = new Paginator([
            'data' => $ipv4,
            'limit'=> 10,
            'page' => $numberPage
        ]);

        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Displays the creation form
     */
    public function newAction()
    {

    }

    /**
     * Edits a ipv4
     *
     * @param string $id
     */
    public function editAction($id)
    {
        if (!$this->request->isPost()) {

            $ipv4 = Ipv4::findFirstByid($id);
            if (!$ipv4) {
                $this->flash->error("ipv4 was not found");

                $this->dispatcher->forward([
                    'controller' => "ipv4",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->id = $ipv4->id;

            $this->tag->setDefault("id", $ipv4->id);
            $this->tag->setDefault("ip_from", $ipv4->ip_from);
            $this->tag->setDefault("ip_to", $ipv4->ip_to);
            $this->tag->setDefault("country_code", $ipv4->country_code);
            $this->tag->setDefault("country_name", $ipv4->country_name);
            $this->tag->setDefault("region_name", $ipv4->region_name);
            $this->tag->setDefault("city_name", $ipv4->city_name);
            $this->tag->setDefault("latitude", $ipv4->latitude);
            $this->tag->setDefault("longitude", $ipv4->longitude);
            $this->tag->setDefault("zip_code", $ipv4->zip_code);
            $this->tag->setDefault("time_zone", $ipv4->time_zone);
            
        }
    }

    /**
     * Creates a new ipv4
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "ipv4",
                'action' => 'index'
            ]);

            return;
        }

        $ipv4 = new Ipv4();
        $ipv4->ip_from = $this->request->getPost("ip_from");
        $ipv4->ip_to = $this->request->getPost("ip_to");
        $ipv4->country_code = $this->request->getPost("country_code");
        $ipv4->country_name = $this->request->getPost("country_name");
        $ipv4->region_name = $this->request->getPost("region_name");
        $ipv4->city_name = $this->request->getPost("city_name");
        $ipv4->latitude = $this->request->getPost("latitude");
        $ipv4->longitude = $this->request->getPost("longitude");
        $ipv4->zip_code = $this->request->getPost("zip_code");
        $ipv4->time_zone = $this->request->getPost("time_zone");
        

        if (!$ipv4->save()) {
            foreach ($ipv4->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "ipv4",
                'action' => 'new'
            ]);

            return;
        }

        $this->flash->success("ipv4 was created successfully");

        $this->dispatcher->forward([
            'controller' => "ipv4",
            'action' => 'index'
        ]);
    }

    /**
     * Saves a ipv4 edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "ipv4",
                'action' => 'index'
            ]);

            return;
        }

        $id = $this->request->getPost("id");
        $ipv4 = Ipv4::findFirstByid($id);

        if (!$ipv4) {
            $this->flash->error("ipv4 does not exist " . $id);

            $this->dispatcher->forward([
                'controller' => "ipv4",
                'action' => 'index'
            ]);

            return;
        }

        $ipv4->ip_from = $this->request->getPost("ip_from");
        $ipv4->ip_to = $this->request->getPost("ip_to");
        $ipv4->country_code = $this->request->getPost("country_code");
        $ipv4->country_name = $this->request->getPost("country_name");
        $ipv4->region_name = $this->request->getPost("region_name");
        $ipv4->city_name = $this->request->getPost("city_name");
        $ipv4->latitude = $this->request->getPost("latitude");
        $ipv4->longitude = $this->request->getPost("longitude");
        $ipv4->zip_code = $this->request->getPost("zip_code");
        $ipv4->time_zone = $this->request->getPost("time_zone");
        

        if (!$ipv4->save()) {

            foreach ($ipv4->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "ipv4",
                'action' => 'edit',
                'params' => [$ipv4->id]
            ]);

            return;
        }

        $this->flash->success("ipv4 was updated successfully");

        $this->dispatcher->forward([
            'controller' => "ipv4",
            'action' => 'index'
        ]);
    }

    /**
     * Deletes a ipv4
     *
     * @param string $id
     */
    public function deleteAction($id)
    {
        $ipv4 = Ipv4::findFirstByid($id);
        if (!$ipv4) {
            $this->flash->error("ipv4 was not found");

            $this->dispatcher->forward([
                'controller' => "ipv4",
                'action' => 'index'
            ]);

            return;
        }

        if (!$ipv4->delete()) {

            foreach ($ipv4->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "ipv4",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("ipv4 was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "ipv4",
            'action' => "index"
        ]);
    }

}
