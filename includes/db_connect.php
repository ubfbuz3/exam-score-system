<?php
// 数据库配置
$host = 'localhost';
$dbname = 'exam_scores';
$username = 'root';
$password = 'root'; // 确保与MySQL实际密码一致

// 创建MySQLi连接
$conn = new mysqli($host, $username, $password, $dbname);

// 检查连接
if ($conn->connect_error) {
    die("数据库连接失败: " . $conn->connect_error);
}
?>