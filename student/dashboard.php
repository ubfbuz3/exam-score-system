<?php
include '../includes/session.php';
// 只允许学生访问
checkRole(['student']);

include '../includes/db_connect.php';

$user_id = $_SESSION['user_id'];

// 获取学生信息
$stmt = $conn->prepare("SELECT s.*, u.avatar FROM students s JOIN users u ON s.user_id = u.id WHERE s.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

// 获取学生成绩
$scores_stmt = $conn->prepare("SELECT * FROM scores WHERE student_id = ?");
$scores_stmt->bind_param("i", $student['id']);
$scores_stmt->execute();
$scores = $scores_stmt->get_result();

$stmt->close();
$scores_stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>学生仪表盘 - 考试成绩查询系统</title>
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
        .profile-card {
            margin-bottom: 20px;
        }
        .avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
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
                    <a href="dashboard.php" class="active"><i class="fa fa-tachometer mr-2"></i>我的成绩</a>
                    <a href="profile.php"><i class="fa fa-user mr-2"></i>个人资料</a>
                    <a href="../logout.php"><i class="fa fa-sign-out mr-2"></i>退出登录</a>
                </div>
            </div>
            
            <!-- 主内容区 -->
            <div class="col-md-10 content">
                <nav class="navbar navbar-light bg-light mb-4">
                    <div class="container-fluid">
                        <span class="navbar-brand mb-0 h1">学生成绩查询</span>
                        <span>欢迎回来，<?php echo $student['name']; ?></span>
                    </div>
                </nav>
                
                <!-- 学生信息卡片 -->
                <div class="card profile-card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
                                <?php if (!empty($student['avatar'])): ?>
                                    <img src="../assets/images/avatars/<?php echo $student['avatar']; ?>" alt="学生头像" class="avatar">
                                <?php else: ?>
                                    <img src="../assets/images/default-avatar.png" alt="默认头像" class="avatar">
                                <?php endif; ?>
                            </div>
                            <div class="col-md-10">
                                <h4><?php echo $student['name']; ?></h4>
                                <p>学号: <?php echo $student['student_id']; ?> | 班级: <?php echo $student['class']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 成绩表格 -->
                <div class="card">
                    <div class="card-header">
                        <h5>我的考试成绩</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>科目</th>
                                    <th>成绩</th>
                                    <th>考试日期</th>
                                    <th>等级</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($score = $scores->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $score['subject']; ?></td>
                                    <td><?php echo $score['score']; ?></td>
                                    <td><?php echo $score['exam_date']; ?></td>
                                    <td>
                                        <?php 
                                        if ($score['score'] >= 90) {
                                            echo '<span class="badge bg-success">优秀</span>';
                                        } elseif ($score['score'] >= 80) {
                                            echo '<span class="badge bg-primary">良好</span>';
                                        } elseif ($score['score'] >= 60) {
                                            echo '<span class="badge bg-warning">及格</span>';
                                        } else {
                                            echo '<span class="badge bg-danger">不及格</span>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
