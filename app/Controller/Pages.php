<?php
namespace Controller;

use Alma\Lists\Helper as HelperList;

class Pages extends \Alma\Controller
{
    /**
     * ビューオブジェクトを保持する
     * @var \Alma\Helper\View\IView
     */
    protected $view;
    
    public function __construct()
    {
        parent::__construct();
       
        $this->loadHelper(HelperList::VIEW_TWIG, 'view');
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