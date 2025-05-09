<?php
// 从配置文件中获取数据库连接信息
require_once __DIR__ . '/../bootstrap.php';
use Config\Database;

// 获取数据库连接
$conn = Database::getConnection();

// 准备SQL语句，插入用户
$sql1 = "INSERT INTO users (username, password_hash, email, role, status) 
        VALUES ('user123', 'user123', 'user123@example.com', 'homeowner', 'active')";
$sql2 = "INSERT INTO users (username, password_hash, email, role, status) 
        VALUES ('cleaner123', 'cleaner123', 'cleaner123@example.com', 'cleaner', 'active')";
$sql3 = "INSERT INTO users (username, password_hash, email, role, status) 
        VALUES ('admin123', 'admin123', 'admin123@example.com', 'admin', 'active')";

// 执行SQL语句
if (mysqli_query($conn, $sql1)) {
    echo "Homeowner user added successfully\n";
} else {
    echo "Error adding homeowner user: " . mysqli_error($conn) . "\n";
}

if (mysqli_query($conn, $sql2)) {
    echo "Cleaner user added successfully\n";
} else {
    echo "Error adding cleaner user: " . mysqli_error($conn) . "\n";
}

if (mysqli_query($conn, $sql3)) {
    echo "Admin user added successfully\n";
} else {
    echo "Error adding admin user: " . mysqli_error($conn) . "\n";
}

// 关闭连接
mysqli_close($conn);

echo "Test users have been created."; 