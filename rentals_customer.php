<?php
session_start();
include 'db.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

/* ===== ข้อมูลลูกค้า ===== */
$q_customer = mysqli_query($conn,"
    SELECT fullname 
    FROM customers 
    WHERE customer_id = '$customer_id'
");
$customer = mysqli_fetch_assoc($q_customer);

/* ===== บันทึกการเช่า ===== */
if (isset($_POST['save'])) {

    $motorcycles_id = $_POST['motorcycles_id'];
    $rent_date      = $_POST['rent_date'];
    $return_date    = $_POST['return_date'];

    $d1 = new DateTime($rent_date);
    $d2 = new DateTime($return_date);
    $total_date = $d1->diff($d2)->days;
    if ($total_date <= 0) { $total_date = 1; }

    $q_price = mysqli_query($conn,"
        SELECT price_per_day 
        FROM motorcycles 
        WHERE motorcycles_id='$motorcycles_id'
    ");
    $price = mysqli_fetch_assoc($q_price)['price_per_day'];
    $total_price = $total_date * $price;

    mysqli_query($conn,"
        INSERT INTO rentals
        (customers_id, motorcycles_id, rent_date, return_date,
         start_mileage, total_date, total_price, rentals_status)
        VALUES
        ('$customer_id', '$motorcycles_id', '$rent_date', '$return_date',
         0, '$total_date', '$total_price', 'รอแอดมินยืนยัน')
    ");

    echo "<script>alert('ส่งคำขอเช่าเรียบร้อย');location.href='rentals_customer.php';</script>";
}

/* ===== รถที่ว่าง ===== */
$motorcycles = mysqli_query($conn,"
    SELECT * FROM motorcycles
    WHERE motorcycles_id NOT IN (
        SELECT motorcycles_id 
        FROM rentals 
        WHERE rentals_status = 'กำลังเช่า'
    )
");

/* ===== รายการเช่าของลูกค้า ===== */
$rentals = mysqli_query($conn,"
    SELECT r.*, m.brand, m.model
    FROM rentals r
    JOIN motorcycles m ON r.motorcycles_id = m.motorcycles_id
    WHERE r.customers_id = '$customer_id'
    ORDER BY r.rentals_id DESC
");
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>เช่ามอเตอร์ไซค์</title>

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
    max-width:1100px;
    margin:40px auto;
    background:#FFF8F0;
    padding:35px;
    border-radius:16px;
    border:1px solid #D2B48C;
}
form.rent-box{
    background:#FFF;
    padding:20px;
    border-radius:12px;
    border:1px solid #D2B48C;
    margin-bottom:40px;
}
label{ font-weight:bold; }
input,select{
    padding:8px;
    width:100%;
    margin-top:6px;
    margin-bottom:15px;
}
button{
    background:#8B5E3C;
    color:white;
    border:none;
    padding:8px 16px;
    border-radius:6px;
}
table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}
th,td{
    border:1px solid #D2B48C;
    padding:10px;
    text-align:center;
}
th{ background:#FFEBCD; }
.btn-delete{ background:#C0392B; }
</style>
</head>

<body>

<header>🏍️ เช่ารถมอเตอร์ไซค์</header>

<div class="container">

<h2>รายการเช่ารถ</h2>
<p>ลูกค้า: <b><?= $customer['fullname']; ?></b></p>

<form method="post" class="rent-box">
    <label>กรุณาเลือกรถทีต้องการเช่า</label>
    <select name="motorcycles_id" required>
        <option value="">-- เลือกรถ (ที่ว่าง) --</option>
        <?php while($m = mysqli_fetch_assoc($motorcycles)){ ?>
            <option value="<?= $m['motorcycles_id']; ?>">
                <?= $m['brand'].' '.$m['model']; ?> (<?= $m['price_per_day']; ?>/วัน)
            </option>
        <?php } ?>
    </select>

    <label>วันที่เช่า</label>
    <input type="date" name="rent_date" required>

    <label>วันที่คืน</label>
    <input type="date" name="return_date" required>

    <button type="submit" name="save">ยืนยันการเช่า</button>
</form>

<h2>รายการเช่าของฉัน</h2>

<table>
<tr>
    <th>ลำดับ</th>
    <th>รถ</th>
    <th>วันที่เช่า</th>
    <th>วันที่คืน</th>
    <th>ราคารวม</th>
    <th>สถานะ</th>
    <th>จัดการ</th>
    <th>อื่นๆ</th>
</tr>

<?php $no=1; while($r=mysqli_fetch_assoc($rentals)){ ?>
<tr>
    <td><?= $no++; ?></td>
    <td><?= $r['brand'].' '.$r['model']; ?></td>
    <td><?= $r['rent_date']; ?></td>
    <td><?= $r['return_date']; ?></td>
    <td><?= number_format($r['total_price']); ?> บาท</td>

    <!-- ===== สถานะ (แก้ตรงนี้อย่างเดียว) ===== -->
    <td>
    <?php
    if ($r['rentals_status'] == 'รอแอดมินยืนยัน') {
        echo '<button style="background:#999;" disabled>รอแอดมินยืนยัน</button>';
    } elseif ($r['rentals_status'] == 'กำลังเช่า') {
        echo '<button style="background:#FFA500;" disabled>กำลังเช่า</button>';
    } elseif ($r['rentals_status'] == 'รอแอดมินยืนยันคืนรถ') {
        echo '<button style="background:#666;" disabled>รอแอดมินยืนยันคืนรถ</button>';
    } elseif ($r['rentals_status'] == 'คืนแล้ว') {
        echo '<button style="background:green;" disabled>คืนรถแล้ว</button>';
    }
    ?>
    </td>

    <!-- ===== ปุ่มคืนรถ ===== -->
    <td>
    <?php if($r['rentals_status']=='กำลังเช่า'){ ?>
        <form method="post" action="request_return.php">
            <input type="hidden" name="rentals_id" value="<?= $r['rentals_id']; ?>">
            <button>ต้องการคืนรถ</button>
        </form>
    <?php } else echo '-'; ?>
    </td>

    <td>
        <form method="post" action="delete_rental.php"
              onsubmit="return confirm('ยืนยันลบข้อมูล?');">
            <input type="hidden" name="rentals_id" value="<?= $r['rentals_id']; ?>">
            <button class="btn-delete">ลบ</button>
        </form>
    </td>
</tr>
<?php } ?>
</table>
<div class="back">
    <a href="dashboard_customer.php">⬅ กลับ Dashboard</a>
</div>
</div>
</body>
</html>