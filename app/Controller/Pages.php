<?php
namespace Controller;

class Pages extends \Alma\Controller
{

    public function home()
    {
        $data = $this->getModel('pages/home');

        $this->view->display('pages/home', $data);
    }
}