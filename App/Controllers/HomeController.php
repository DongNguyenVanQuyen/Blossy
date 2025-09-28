<?php
require_once 'BaseController.php';

class HomeController extends BaseController
{
    public function index()
    {
        // Gán tiêu đề trang để hiển thị trên <title>
        global $title;
        $title = "Trang chủ | Blossy";

        // Dữ liệu cần truyền ra View
        $data = [
            'pageHeading' => 'Chào mừng đến với Blossy',
            'introMessage' => 'Gửi tặng một bó hoa tình yêu chỉ với một cú nhấp chuột.'
        ];

        // Load view Home/index.php
        $this->loadView('Home.index', $data);
    }
}
