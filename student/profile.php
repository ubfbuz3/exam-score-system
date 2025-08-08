<?php
include '../includes/session.php';
// 只允许学生访问
checkRole(['student']);

include '../includes/db_connect.php';

$user_id = $_SESSION['user_id'];

// 获取学生信息
$stmt = $conn->prepare("SELECT s.*, u.username, u.avatar, u.created_at FROM students s JOIN users u ON s.user_id = u.id WHERE s.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

$message = '';
$message_type = '';

// 处理消息参数
if (isset($_GET['success'])) {
    $message = $_GET['success'];
    $message_type = 'success';
} elseif (isset($_GET['error'])) {
    $message = $_GET['error'];
    $message_type = 'danger';
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>个人资料 - 学生</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css">
    <style>
        .sidebar {
            height: 100vh;
            background-color: #343a40;
            color: white;
            padding-top: 20px;
        }
        .sidebar a {
            color: rgba(255, 255, 255, 0.7);
            display: block;
            padding: 10px 20px;
            text-decoration: none;
        }
        .sidebar a:hover, .sidebar a.active {
            color: white;
            background-color: #495057;
        }
        .content {
            padding: 20px;
        }
        .avatar-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #ddd;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- 侧边栏 -->
            <div class="col-md-2 sidebar">
                <h3 class="text-center mb-4">成绩查询系统</h3>
                <div class="d-flex flex-column">
                    <a href="dashboard.php"><i class="fa fa-tachometer mr-2"></i>我的成绩</a>
                    <a href="profile.php" class="active"><i class="fa fa-user mr-2"></i>个人资料</a>
                    <a href="../logout.php"><i class="fa fa-sign-out mr-2"></i>退出登录</a>
                </div>
            </div>
            
            <!-- 主内容区 -->
            <div class="col-md-10 content">
                <nav class="navbar navbar-light bg-light mb-4">
                    <div class="container-fluid">
                        <span class="navbar-brand mb-0 h1">个人资料</span>
                        <span>欢迎回来，<?php echo $student['name']; ?></span>
                    </div>
                </nav>
                
                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <div class="card">
                            <div class="card-header">
                                <h5>学生信息</h5>
                            </div>
                            <div class="card-body">
                                <!-- 头像上传 -->
                                <div class="avatar-container">
                                    <?php if (!empty($student['avatar'])): ?>
                                        <img src="../assets/images/avatars/<?php echo $student['avatar']; ?>" alt="学生头像" class="avatar">
                                    <?php else: ?>
                                        <img src="../assets/images/default-avatar.png" alt="默认头像" class="avatar">
                                    <?php endif; ?>
                                    
                                    <form method="POST" action="../includes/upload_avatar.php" enctype="multipart/form-data">
                                        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                                        <input type="hidden" name="role" value="student">
                                        <div class="mb-3">
                                            <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                                            <small class="form-text text-muted">支持JPG、JPEG、PNG、GIF格式，最大2MB</small>
                                        </div>
                                        <button type="submit" class="btn btn-primary">上传头像</button>
                                    </form>
                                </div>
                                
                                <!-- 个人信息 -->
                                <form>
                                    <div class="mb-3">
                                        <label for="username" class="form-label">用户名</label>
                                        <input type="text" class="form-control" id="username" value="<?php echo $student['username']; ?>" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label for="name" class="form-label">姓名</label>
                                        <input type="text" class="form-control" id="name" value="<?php echo $student['name']; ?>" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label for="student_id" class="form-label">学号</label>
                                        <input type="text" class="form-control" id="student_id" value="<?php echo $student['student_id']; ?>" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label for="class" class="form-label">班级</label>
                                        <input type="text" class="form-control" id="class" value="<?php echo $student['class']; ?>" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label for="joined_date" class="form-label">注册日期</label>
                                        <input type="text" class="form-control" id="joined_date" value="<?php echo $student['created_at']; ?>" disabled>
                                    </div>
                                </form>
                                
                                <!-- 密码修改 -->
                                <div class="mt-4">
                                    <h5>修改密码</h5>
                                    <form method="POST" action="update_password.php">
                                        <div class="mb-3">
                                            <label for="current_password" class="form-label">当前密码</label>
                                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="new_password" class="form-label">新密码</label>
                                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="confirm_password" class="form-label">确认新密码</label>
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">更新密码</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
