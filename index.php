<?php
session_start();
// 检查用户是否已登录，如果已登录则跳转到对应面板
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: student/dashboard.php");
    }
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 确保数据库连接文件存在
    $db_connect_file = 'includes/db_connect.php';
    if (!file_exists($db_connect_file)) {
        $error = "数据库配置文件缺失";
    } else {
        include $db_connect_file;
        
        // 检查数据库连接是否成功
        if (!$conn || ($conn instanceof mysqli && $conn->connect_error)) {
            $error = "数据库连接失败，请检查配置";
        } else {
            $username = $_POST['username'];
            $password = $_POST['password'];
            
            // 准备查询
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
            if (!$stmt) {
                $error = "数据库查询准备失败: " . $conn->error;
            } else {
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows == 1) {
                    $user = $result->fetch_assoc();
                    // 验证密码
                    if (password_verify($password, $user['password'])) {
                        // 密码正确，设置会话
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['role'] = $user['role'];
                        
                        // 跳转到对应面板
                        if ($user['role'] == 'admin') {
                            header("Location: admin/dashboard.php");
                        } else {
                            header("Location: student/dashboard.php");
                        }
                        exit();
                    } else {
                        $error = "用户名或密码不正确";
                    }
                } else {
                    $error = "用户名或密码不正确";
                }
                
                $stmt->close();
            }
            $conn->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>考试成绩查询系统 - 登录</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="text-center mb-4">考试成绩查询系统</h2>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">用户名</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">密码</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">登录</button>
        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>