<?php


include 'db_connect.php';

// 检查是否有文件上传
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['avatar'])) {
    $user_id = $_POST['user_id'];
    $role = $_POST['role'];
    
    // 上传文件信息
    $file = $_FILES['avatar'];
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_error = $file['error'];
    
    // 获取文件扩展名
    $file_ext = explode('.', $file_name);
    $file_ext = strtolower(end($file_ext));
    
    // 允许的文件类型
    $allowed = array('jpg', 'jpeg', 'png', 'gif');
    
    if (in_array($file_ext, $allowed)) {
        if ($file_error === 0) {
            // 限制文件大小为2MB
            if ($file_size <= 2097152) {
                // 生成唯一文件名
                $file_name_new = uniqid('', true) . '.' . $file_ext;
                // 设置上传目录
                $file_destination = '../assets/images/avatars/' . $file_name_new;
                
                // 移动上传文件
                if (move_uploaded_file($file_tmp, $file_destination)) {
                    // 更新数据库中的头像路径
                    $stmt = $conn->prepare("UPDATE users SET avatar = ? WHERE id = ?");
                    $stmt->bind_param("si", $file_name_new, $user_id);
                    
                    if ($stmt->execute()) {
                        // 更新成功，返回成功消息
                        $_SESSION['avatar'] = $file_name_new;
                        header("Location: " . ($role == 'admin' ? '../admin/profile.php' : '../student/profile.php') . "?success=头像上传成功");
                        exit();
                    } else {
                        header("Location: " . ($role == 'admin' ? '../admin/profile.php' : '../student/profile.php') . "?error=数据库更新失败");
                        exit();
                    }
                    
                    $stmt->close();
                } else {
                    header("Location: " . ($role == 'admin' ? '../admin/profile.php' : '../student/profile.php') . "?error=文件上传失败");
                    exit();
                }
            } else {
                header("Location: " . ($role == 'admin' ? '../admin/profile.php' : '../student/profile.php') . "?error=文件过大，最大支持2MB");
                exit();
            }
        } else {
            header("Location: " . ($role == 'admin' ? '../admin/profile.php' : '../student/profile.php') . "?error=文件上传时发生错误");
            exit();
        }
    } else {
        header("Location: " . ($role == 'admin' ? '../admin/profile.php' : '../student/profile.php') . "?error=只允许上传图片文件(jpg, jpeg, png, gif)");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}

$conn->close();
?>
