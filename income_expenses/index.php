<?php
// เชื่อมต่อฐานข้อมูล
$conn = mysqli_connect("localhost", "root", "", "project_money");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>บันทึกรายรับรายจ่าย</title>
    <style>
        body { font-family: sans-serif; margin: 30px; line-height: 1.6; }
        .form-box { background: #f4f4f4; padding: 20px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #ddd; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 12px; text-align: center; }
        th { background-color: #2c3e50; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .btn-delete { color: red; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

    <div class="form-box">
        <h2>💰 บันทึกรายรับรายจ่าย</h2>
        <form action="data.php" method="POST">
            ต้นทุน: <input type="number" name="principle" required> 
            รายรับ: <input type="number" name="income" required> 
            รายจ่าย: <input type="number" name="expenses" required> 
            <button type="submit" name="submit">บันทึกข้อมูล</button>
        </form>
    </div>

    <h2>📜 รายการล่าสุด</h2>
    <table>
        <thead>
            <tr>
                <th>ครั้งที่</th>
                <th>รายรับ</th>
                <th>รายจ่าย</th>
                <th>ยอดคงเหลือสะสม</th>
                <th>สัปดาห์ที่</th>
                <th>เดือนที่</th>
                <th>จัดการ</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM money ORDER BY id DESC";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . number_format($row['income']) . "</td>";
                    echo "<td>" . number_format($row['expenses']) . "</td>";
                    echo "<td style='font-weight:bold; color:green;'>" . number_format($row['total']) . "</td>";
                    echo "<td>" . $row['total_week'] . "</td>";
                    echo "<td>" . $row['total_month'] . "</td>";
                    echo "<td><a href='delete.php?id=" . $row['id'] . "' class='btn-delete' onclick='return confirm(\"ต้องการลบรายการนี้ใช่หรือไม่?\")'>ลบ</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>ยังไม่มีข้อมูลในระบบ</td></tr>";
            }
            ?>
        </tbody>
    </table>

</body>
</html>
<?php mysqli_close($conn); ?>