<?php
// File ketnoi.php - kết nối cơ sở dữ liệu
$conn = new mysqli("localhost", "root", "", "qlbanhang");

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>
