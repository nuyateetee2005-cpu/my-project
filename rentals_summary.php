<?php
session_start();
include 'db.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

/* ===== ดึงเฉพาะรายการที่คืนแล้ว ===== */
$summary = mysqli_query($conn, "
    SELECT r.*, m.brand, m.model
    FROM rentals r
    JOIN motorcycles m ON r.motorcycles_id = m.motorcycles_id
    WHERE r.customers_id = '$customer_id'
    AND r.rentals_status = 'คืนแล้ว'
    ORDER BY r.rentals_id DESC
");
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>สรุปการเช่ารถ</title>

<style>
body{
    margin:0;
    font-family:Arial;
    background: linear-gradient(120deg,#FFF5E6,#FFEBCD);
}
header{
    background:#8B5E3C;
    color:white;
    padding:22px;
    text-align:center;
    font-size:26px;
    font-weight:bold;
}
.container{
    max-width:950px;
    margin:40px auto;
    background:#FFF8F0;
    padding:35px;
    border-radius:16px;
}
table{
    width:100%;
    border-collapse:collapse;
    margin-top:25px;
}
th,td{
    border:1px solid #D2B48C;
    padding:10px;
    text-align:center;
}
th{ background:#FFEBCD; }

.status{
    background:green;
    color:white;
    padding:6px 12px;
    border-radius:8px;
    font-size:14px;
}

.back{
    margin-top:25px;
    display:inline-block;
}
button{
    background:#8B5E3C;
    color:white;
    border:none;
    padding:10px 22px;
    border-radius:8px;
}
</style>
</head>

<body>

<header>📄 สรุปการเช่ารถ (เสร็จสิ้น)</header>

<div class="container">

<table>
<tr>
    <th>ลำดับ</th>
    <th>รถ</th>
    <th>วันที่เช่า</th>
    <th>วันที่คืน</th>
    <th>จำนวนวัน</th>
    <th>ราคารวม</th>
    <th>สถานะ</th>
</tr>

<?php
$no = 1;
$total_all = 0;
while($r = mysqli_fetch_assoc($summary)){
    $total_all += $r['total_price'];
?>
<tr>
    <td><?= $no++; ?></td>
    <td><?= $r['brand'].' '.$r['model']; ?></td>
    <td><?= $r['rent_date']; ?></td>
    <td><?= $r['return_date']; ?></td>
    <td><?= $r['total_date']; ?> วัน</td>
    <td><?= number_format($r['total_price']); ?> บาท</td>
    <td><span class="status">คืนรถแล้ว</span></td>
</tr>
<?php } ?>

<?php if(mysqli_num_rows($summary)==0){ ?>
<tr>
    <td colspan="7">ยังไม่มีรายการเช่าที่เสร็จสิ้น</td>
</tr>
<?php } ?>
</table>

<?php if(mysqli_num_rows($summary)>0){ ?>
<h3 style="text-align:right;margin-top:20px;">
    รวมทั้งหมด: <?= number_format($total_all); ?> บาท
</h3>
<?php } ?>

<a href="rentals_customer.php" class="back">
    <button>← กลับหน้าการเช่า</button>
</a>

</div>
</body>
</html>