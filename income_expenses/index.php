<?php
// เชื่อมต่อฐานข้อมูลเพื่อดึงข้อมูลมาแสดง
$conn = mysqli_connect("localhost", "root", "", "project_money");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>บันทึกรายรับรายจ่าย</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        .form-section { background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
    </style>
</head>
<body>

    <div class="form-section">
        <h2>➕ เพิ่มรายการใหม่</h2>
        <form action="data.php" method="POST">
            เงินต้น: <input type="number" name="principle" required> 
            รายรับ: <input type="number" name="income" required> 
            รายจ่าย: <input type="number" name="expenses" required> 
            <button type="submit" name="submit">บันทึกข้อมูล</button>
        </form>
    </div>

    <hr>

    <h2>📜 รายการย้อนหลัง</h2>
    <table>
        <thead>
            <tr>
                <th>ครั้งที่</th>
                <th>รายรับ</th>
                <th>รายจ่าย</th>
                <th>ยอดคงเหลือ</th>
                <th>สัปดาห์ที่</th>
                <th>เดือนที่</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // ดึงข้อมูล 10 รายการล่าสุดมาโชว์
            $sql = "SELECT * FROM money ORDER BY id DESC LIMIT 10";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . number_format($row['income']) . "</td>";
                    echo "<td>" . number_format($row['expenses']) . "</td>";
                    echo "<td style='font-weight:bold; color:blue;'>" . number_format($row['total']) . "</td>";
                    echo "<td>" . $row['total_week'] . "</td>";
                    echo "<td>" . $row['total_month'] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>ยังไม่มีข้อมูลบันทึก</td></tr>";
            }
            ?>
        </tbody>
    </table>

</body>
</html>

<?php mysqli_close($conn); ?>