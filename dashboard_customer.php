<?php
session_start();
include 'db.php';

/* ===== เช็กล็อกอิน ===== */
if(!isset($_SESSION['customer_id'])){
    header("Location: login.php");
    exit();
}

$fullname = $_SESSION['fullname'];
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Dashboard ลูกค้า</title>

<style>
body{
    margin:0;
    font-family: Arial;
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
    max-width:900px;
    margin:40px auto;
    background:#FFF8F0;
    padding:35px;
    border-radius:16px;
    border:1px solid #D2B48C;
}
h2{
    color:#8B5E3C;
}
.card{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
    margin-top:30px;
}
.box{
    background:#FFEBCD;
    padding:25px;
    border-radius:12px;
    text-align:center;
    border:1px solid #D2B48C;
}
.box a{
    text-decoration:none;
    color:#8B5E3C;
    font-size:18px;
    font-weight:bold;
}
.logout{
    margin-top:30px;
    text-align:center;
}
.logout a{
    color:red;
    font-weight:bold;
    text-decoration:none;
}
</style>
</head>

<body>

<header>🏍️ Dashboard ลูกค้า</header>

<div class="container">
    <h2>สวัสดีคุณ <?= $fullname; ?></h2>

    <div class="card">
        <div class="box">
            <a href="motorcycles_customer.php">🏍️ รายการรถมอเตอร์ไซค์</a>
        </div>
        <div class="box">
           
            <a href="rentals_customer.php">📋 เช่ามอเตอร์ไซค์</a>
        </div>
        <div class="box">
           
            <a href="rentals_summary.php">🧾 สรุปการเช่ารถ</a>
        </div>
        
    </div>

    <div class="logout">
        
        <a href="logout.php"> ออกจากระบบ</a>
    </div>
</div>

</body>
</html>