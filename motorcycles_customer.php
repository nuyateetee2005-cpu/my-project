<?php
session_start();
include 'db.php';

/* ===== เช็กล็อกอินลูกค้า ===== */
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

/* ===== ดึงข้อมูลรถที่ว่าง ===== */
$motorcycles = mysqli_query($conn,"
    SELECT * FROM motorcycles
    WHERE motorcycles_id NOT IN (
        SELECT motorcycles_id 
        FROM rentals 
        WHERE rentals_status = 'กำลังเช่า'
    )
");
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>เลือกรถมอเตอร์ไซค์</title>

<style>
body{
    margin:0;
    font-family:Arial;
    background:linear-gradient(120deg,#FFF5E6,#FFEBCD);
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
    max-width:1200px;
    margin:40px auto;
    padding:20px;
}
.grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
    gap:25px;
}
.card{
    background:#FFF8F0;
    border-radius:16px;
    border:1px solid #D2B48C;
    box-shadow:2px 4px 10px rgba(0,0,0,.15);
    overflow:hidden;
}
.card img{
    width:100%;
    height:200px;
    object-fit:cover;
}
.card-body{
    padding:18px;
    text-align:center;
}
.card-body h3{
    margin:0 0 10px;
    color:#8B5E3C;
}
.price{
    font-weight:bold;
    font-size:16px;
}
.back{
    text-align:center;
    margin-top:30px;
}
.back a{
    color:#8B5E3C;
    font-weight:bold;
    text-decoration:none;
}
</style>
</head>

<body>

<header>🏍️ รายการมอเตอร์ไซค์ให้เช่า</header>

<div class="container">

<div class="grid">
<?php while($m = mysqli_fetch_assoc($motorcycles)){ ?>
    <div class="card">
        <img src="uploads/<?= $m['image']; ?>" alt="motorcycle">
        <div class="card-body">
            <h3><?= $m['brand'].' '.$m['model']; ?></h3>
            <div class="price">
                ราคา <?= number_format($m['price_per_day']); ?> บาท / วัน
            </div>
        </div>
    </div>
<?php } ?>
</div>

<div class="back">
    <a href="dashboard_customer.php">⬅ กลับ Dashboard</a>
</div>

</div>
</body>
</html>