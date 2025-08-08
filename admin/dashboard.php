<?php
include '../includes/session.php';
// 只允许管理员访问
checkRole(['admin']);

include '../includes/db_connect.php';

// 获取学生总数
$student_count = $conn->query("SELECT COUNT(*) AS count FROM users WHERE role = 'student'")->fetch_assoc()['count'];

// 获取最近添加的学生
$recent_students = $conn->query("SELECT u.id, u.username, s.name, s.student_id, s.class 
                                FROM users u 
                                JOIN students s ON u.id = s.user_id 
                                WHERE u.role = 'student' 
                                ORDER BY u.created_at DESC 
                                LIMIT 5");

$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理员仪表盘 - 考试成绩查询系统</title>
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
        .card {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- 侧边栏 -->
            <div class="col-md-2 sidebar">
                <h3 class="text-center mb-4">成绩管理系统</h3>
                <div class="d-flex flex-column">
                    <a href="dashboard.php" class="active"><i class="fa fa-tachometer mr-2"></i>仪表盘</a>
                    <a href="students.php"><i class="fa fa-users mr-2"></i>学生管理</a>
                    <a href="scores.php"><i class="fa fa-graduation-cap mr-2"></i>成绩管理</a>
                    <a href="profile.php"><i class="fa fa-user mr-2"></i>个人资料</a>
                    <a href="../logout.php"><i class="fa fa-sign-out mr-2"></i>退出登录</a>
                </div>
            </div>
            
            <!-- 主内容区 -->
            <div class="col-md-10 content">
                <nav class="navbar navbar-light bg-light mb-4">
                    <div class="container-fluid">
                        <span class="navbar-brand mb-0 h1">管理员仪表盘</span>
                        <span>欢迎回来，<?php echo $_SESSION['username']; ?></span>
                    </div>
                </nav>
                
                <!-- 统计卡片 -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">学生总数</h5>
                                <h2 class="display-4"><?php echo $student_count; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">课程总数</h5>
                                <h2 class="display-4">10</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">考试次数</h5>
                                <h2 class="display-4">5</h2>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 最近学生 -->
                <div class="card">
                    <div class="card-header">
                        <h5>最近添加的学生</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>用户名</th>
                                    <th>姓名</th>
                                    <th>学号</th>
                                    <th>班级</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($student = $recent_students->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $student['username']; ?></td>
                                    <td><?php echo $student['name']; ?></td>
                                    <td><?php echo $student['student_id']; ?></td>
                                    <td><?php echo $student['class']; ?></td>
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
