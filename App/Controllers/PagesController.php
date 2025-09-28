<?php
require_once 'BaseController.php';

class PagesController extends BaseController
{
    public function about()
    {
        global $title;
        $title = "Giới thiệu | Blossy";

        $this->loadView('Pages.AboutUs');
    }

    public function contact()
    {
        global $title;
        $title = "Liên hệ | Blossy";

        $this->loadView('Pages.Contact');
    }
}
