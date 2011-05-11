<?php
namespace Controller;

class Pages extends \Alma\Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function home()
    {
        $data = $this->getModel('pages/home');


        /*
          $img = \Alma\Image\Base64::convert(realpath('./autotest1.jpg'), ALMA_DIR_CACHE);
          echo "<img src=\"{$img}\">";
          $img = \Alma\Image\Base64::convert(realpath('./te.PNG'), ALMA_DIR_CACHE);
          echo "<img src=\"{$img}\">";
         */

        $this->view->display('pages/home', $data);
    }
}