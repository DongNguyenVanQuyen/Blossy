<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../Models/ReportModel.php';

class AdminController extends BaseController
{
    private function guardAdmin(): void
    {
        // role_id: 1 = admin (tùy DB của bạn, sửa nếu khác)
        if (!isset($_SESSION['user']) || (int)$_SESSION['user']['role_id'] === 1) {
            $_SESSION['toast'] = ['type' => 'error', 'message' => '⚠️ Bạn không có quyền truy cập Admin!'];
            header("Location: " . BASE_URL . "index.php");
            exit;
        }
    }

    public function dashboard(): void
    {
        $this->guardAdmin();
        global $title;
        $title = "Bảng điều khiển | Blossy Admin";

        $report = new ReportModel();

        $stats = [
            'products'  => $report->countTable('products'),
            'orders'    => $report->countTable('orders'),
            'customers' => $report->countTable('users'),
            'revenue'   => $report->getTotalRevenue(),
        ];

        $recentOrders = $report->getRecentOrders(10);
        $topProducts  = $report->getTopProducts(8);
        $revMonth     = $report->getRevenueByMonth(6); // 6 tháng gần nhất cho so sánh doanh thu

        // Dữ liệu fake
        // if (count($revMonth) <= 6) {
        //     $revMonth = [
        //         ['mth' => '06/2025', 'revenue' => 7500000],
        //         ['mth' => '07/2025', 'revenue' => 7500000],
        //         ['mth' => '08/2025', 'revenue' => 10200000],
        //         ['mth' => '09/2025', 'revenue' => 12500000],
        //     ];
        // }


        $this->loadView('Admin.Dashboard', compact('stats','recentOrders','topProducts','revMonth'));
    }
    
}
