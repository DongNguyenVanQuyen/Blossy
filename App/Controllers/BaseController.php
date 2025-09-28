<?php

// Tên file: controllers/BaseController.php

class BaseController 
{
    /**
     * Phương thức load View (giao diện) và truyền dữ liệu ra ngoài.
     * Quy ước đường dẫn: "folder.file" (ví dụ: product.index sẽ load views/product/index.php)
     * * @param string $viewPath Đường dẫn View theo quy ước dấu chấm.
     * @param array $data Mảng dữ liệu cần truyền ra View.
     */
    public function loadView($viewPath, $data = [])
    {
        // 1. Xử lý dữ liệu: 
        // Hàm extract() biến các key của mảng $data thành các biến PHP
        // Ví dụ: $data = ['title' => 'Sản phẩm'] sẽ thành biến $title = 'Sản phẩm' trong View.
        if (!empty($data)) {
            extract($data);
        }
        
        // 2. Xử lý đường dẫn: Thay thế dấu chấm (.) bằng dấu gạch chéo (/)
        $filePath = str_replace('.', '/', $viewPath);
        
        // 3. Xây dựng đường dẫn file View hoàn chỉnh
        $fullPath = __DIR__ . '/../Views/' . $filePath . '.php';

        
        // 4. Kiểm tra và load file View
        if (file_exists($fullPath)) {
            require_once $fullPath;
        } else {
            // Xử lý lỗi nếu không tìm thấy View
            echo "Lỗi: Không tìm thấy file View: " . $fullPath;
        }
    }

    /**
     * Phương thức load file Model (mô hình dữ liệu).
     * * @param string $modelName Tên Model (ví dụ: 'Product' sẽ load ProductModel.php).
     */
    public function loadModel($modelName)
    {
        // Tên Model theo quy ước là ModelName + 'Model'
        $modelClassName = $modelName . 'Model';
        
        // Xây dựng đường dẫn file Model hoàn chỉnh
        $fullPath = 'models/' . $modelClassName . '.php';

        // Kiểm tra và load file Model
        if (file_exists($fullPath)) {
            require_once $fullPath;
        } else {
            // Xử lý lỗi nếu không tìm thấy Model
            echo "Lỗi: Không tìm thấy file Model: " . $fullPath;
        }
    }
}