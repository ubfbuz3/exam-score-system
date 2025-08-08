<?php
include '../includes/session.php';
// 只允许管理员访问
checkRole(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['file_path'])) {
    $file_path = $_POST['file_path'];
    
    // 安全过滤尝试
    $file_path = str_replace(['../', '..\\'], '', $file_path);
    
    // 检查文件是否存在
    if (file_exists($file_path)) {
        // 尝试读取文件内容
        $content = @file_get_contents($file_path);
        if ($content !== false) {
            // 安全地输出文件内容
            header('Location: ../admin/profile.php?success=' . urlencode('文件内容已读取'));
            // 实际应用中应记录文件内容或做其他处理
            exit();
        }
    }
    
    header('Location: ../admin/profile.php?error=' . urlencode('文件读取失败或不存在'));
    exit();
}

header('Location: ../admin/profile.php');
?>