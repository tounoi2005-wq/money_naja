<?php
$conn = mysqli_connect("localhost", "root", "", "project_money");

if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }

if (isset($_POST['submit'])) {
    $principle = $_POST['principle'];
    $income = $_POST['income'];
    $expenses = $_POST['expenses'];

    // 1. หาจำนวนรายการที่มีอยู่จริง เพื่อกำหนด "ครั้งที่" (ลำดับการนับสัปดาห์)
    $res_count = mysqli_query($conn, "SELECT COUNT(id) as total_rows FROM money");
    $row_count = mysqli_fetch_assoc($res_count);
    $current_order = $row_count['total_rows'] + 1; 

    // 2. คำนวณรอบสัปดาห์/เดือน (7 ครั้ง = 1 สัปดาห์)
    $total_week = ceil($current_order / 7);
    $total_month = ceil($current_order / 30);
    $total_year = ceil($current_order / 365);

    // 3. คำนวณยอดเงินสะสม (หายอดล่าสุดในตารางมาบวกเพิ่ม)
    $res_last = mysqli_query($conn, "SELECT total FROM money ORDER BY id DESC LIMIT 1");
    $row_last = mysqli_fetch_assoc($res_last);
    $last_total = isset($row_last['total']) ? $row_last['total'] : 0;
    
    // ยอดคงเหลือ = ยอดเก่า + รายรับ - รายจ่าย
    $new_total = $last_total + $income - $expenses;

    // 4. บันทึกลง Database
    $sql = "INSERT INTO money (principle, expenses, income, total, total_week, total_month, total_year) 
            VALUES ('$principle', '$expenses', '$income', '$new_total', '$total_week', '$total_month', '$total_year')";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php"); // บันทึกเสร็จเด้งกลับหน้าแรกทันที
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
mysqli_close($conn);
?>