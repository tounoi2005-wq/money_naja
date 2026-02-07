<?php
// 1. ตั้งค่าการเชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_money"; // ชื่อ DB ตามรูปที่คุณส่งมา

$conn = mysqli_connect($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// 2. ส่วนของ Logic เมื่อกดปุ่ม "บันทึกข้อมูล"
if (isset($_POST['submit'])) {
    $principle = $_POST['principle'];
    $income = $_POST['income'];
    $expenses = $_POST['expenses'];
    
    // คำนวณยอดคงเหลือของรายการนี้
    $total = ($principle + $income) - $expenses;

    // บันทึกข้อมูลรายการปัจจุบันลงตาราง money
    $sql_insert = "INSERT INTO money (principle, expenses, income, total) 
                   VALUES ('$principle', '$expenses', '$income', '$total')";
    
    if (mysqli_query($conn, $sql_insert)) {
        $last_id = mysqli_insert_id($conn); // เอา id ล่าสุดที่เพิ่งบันทึก

        // --- เริ่มการคำนวณแบบธนาคาร (Week/Month/Year) ---
        
        // ก. นับจำนวนครั้งทั้งหมดที่เคยบันทึกมา
        $res_count = mysqli_query($conn, "SELECT COUNT(id) as total_rows FROM money");
        $row_count = mysqli_fetch_assoc($res_count);
        $count = $row_count['total_rows'];

        // ข. รวมยอดเงินคงเหลือสะสมทั้งหมด (Grand Total)
        $res_sum = mysqli_query($conn, "SELECT SUM(total) as grand_total FROM money");
        $row_sum = mysqli_fetch_assoc($res_sum);
        $current_balance = $row_sum['grand_total'];

        // ค. คำนวณรอบตามเงื่อนไข (7 ครั้ง = 1 สัปดาห์)
        $total_week = floor($count / 7);
        $total_month = floor($count / 30);
        $total_year = floor($count / 365);

        // ง. อัปเดตยอดสะสมกลับเข้าไปในแถวล่าสุด
        $sql_update = "UPDATE money SET 
                       total_week = '$total_week', 
                       total_month = '$total_month', 
                       total_year = '$total_year' 
                       WHERE id = '$last_id'";
        mysqli_query($conn, $sql_update);

        echo "<div style='text-align:center; font-family: sans-serif;'>";
        echo "<h2>✅ บันทึกข้อมูลสำเร็จ</h2>";
        echo "<h3>ยอดคงเหลือปัจจุบัน: " . number_format($current_balance) . " บาท</h3>";
        echo "<p>บันทึกแล้วทั้งหมด: $count ครั้ง ($total_week สัปดาห์)</p>";
        echo "<hr>";
        echo "<a href='index.php'>กลับไปบันทึกเพิ่ม</a> | <a href='data.php?view=all'>ดูประวัติบัญชีทั้งหมด</a>";
        echo "</div>";

    } else {
        echo "เกิดข้อผิดพลาด: " . mysqli_error($conn);
    }
}

// 3. ส่วนของการแสดงผลตาราง (เมื่อคลิก "ดูประวัติบัญชีทั้งหมด")
if (isset($_GET['view']) && $_GET['view'] == 'all') {
    echo "<h2 style='text-align:center;'>สมุดบัญชีรายรับ-รายจ่าย</h2>";
    $result = mysqli_query($conn, "SELECT * FROM money ORDER BY id DESC");
    
    echo "<table border='1' align='center' cellpadding='10' style='border-collapse: collapse; width: 80%;'>";
    echo "<tr bgcolor='#f2f2f2'>
            <th>ครั้งที่</th>
            <th>รายรับ</th>
            <th>รายจ่าย</th>
            <th>เงินคงเหลือ</th>
            <th>สัปดาห์ที่</th>
          </tr>";
    
    while($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td align='center'>".$row['id']."</td>";
        echo "<td align='right'>".number_format($row['income'])."</td>";
        echo "<td align='right'>".number_format($row['expenses'])."</td>";
        echo "<td align='right' style='font-weight:bold;'>".number_format($row['total'])."</td>";
        echo "<td align='center'>".$row['total_week']."</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<p align='center'><a href='index.html'>กลับหน้าแรก</a></p>";
}

mysqli_close($conn);
?>