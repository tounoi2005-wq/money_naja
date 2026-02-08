<?php
$conn = mysqli_connect("localhost", "root", "", "project_money");

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // 1. ลบข้อมูลแถวที่เลือกออกจาก phpMyAdmin ทันที
    $sql_delete = "DELETE FROM money WHERE id = $id";
    mysqli_query($conn, $sql_delete);

    // 2. เทคนิคพิเศษ: จัดระเบียบเลข ID ใหม่ให้เรียง 1, 2, 3... (สำหรับโปรเจกต์ส่งอาจารย์)
    // ขั้นแรก: ตั้งตัวแปรนับใหม่
    mysqli_query($conn, "SET @count = 0;");
    // ขั้นสอง: อัปเดต ID ทุกแถวให้เรียงใหม่ตามลำดับ
    mysqli_query($conn, "UPDATE money SET id = (@count:= @count + 1);");
    // ขั้นสาม: รีเซ็ตตัวนับ Auto Increment ให้เท่ากับจำนวนแถวที่มีอยู่จริง
    mysqli_query($conn, "ALTER TABLE money AUTO_INCREMENT = 1;");

    // เสร็จแล้วเด้งกลับหน้าหลัก
    header("Location: index.php");
}
mysqli_close($conn);
?>