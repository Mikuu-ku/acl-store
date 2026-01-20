<?php
session_start();
include "config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

if (isset($_POST['add_to_cart'])) {
    $p_id = $_POST['product_id'];
    $qty = $_POST['quantity'];
    $size = $_POST['size'];
    
    $check = mysqli_query($conn, "SELECT * FROM cart WHERE user_id = $user_id AND product_id = $p_id AND size = '$size'");
    
    if (mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "UPDATE cart SET quantity = quantity + $qty WHERE user_id = $user_id AND product_id = $p_id AND size = '$size'");
    } else {
        mysqli_query($conn, "INSERT INTO cart (user_id, product_id, quantity, size) VALUES ($user_id, $p_id, $qty, '$size')");
    }
    header("Location: cart.php");
    exit;
}

$result = mysqli_query($conn, "SELECT cart.*, products.name, products.price, products.image FROM cart JOIN products ON cart.product_id = products.id WHERE cart.user_id = $user_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart - Apparel</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .cart-table { width: 100%; border-collapse: collapse; margin-top: 40px; }
        .cart-table th { text-align: left; padding: 15px 0; border-bottom: 2px solid #111; font-size: 11px; text-transform: uppercase; letter-spacing: 2px; }
        .cart-table td { padding: 25px 0; border-bottom: 1px solid #eee; }
        .cart-img { width: 90px; }
        .total-text { font-size: 28px; font-weight: 700; margin: 10px 0 30px; }
        .checkout-modal-content { max-width: 500px; padding: 40px; position: relative; }
        .form-control { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; box-sizing: border-box; }
        .close-receipt { position: absolute; right: 20px; top: 15px; font-size: 24px; cursor: pointer; color: #888; }
    </style>
</head>
<body>

<main class="container" style="padding: 60px 0;">
    <h1>Shopping Cart</h1>
    <?php if (mysqli_num_rows($result) > 0): ?>
        <table class="cart-table">
            <thead><tr><th>Product</th><th>Details</th><th>Price</th><th>Qty</th><th style="text-align: right;">Total</th></tr></thead>
            <tbody>
                <?php $total = 0; while ($row = mysqli_fetch_assoc($result)): 
                    $subtotal = $row['price'] * $row['quantity']; $total += $subtotal; ?>
                    <tr>
                        <td><img src="assets/images/<?php echo $row['image']; ?>" class="cart-img"></td>
                        <td><strong><?php echo $row['name']; ?></strong><br><small>Size: <?php echo $row['size']; ?></small></td>
                        <td>₱<?php echo number_format($row['price'], 2); ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td style="text-align: right;">₱<?php echo number_format($subtotal, 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div style="text-align: right; margin-top: 40px;">
            <div class="total-text">₱<?php echo number_format($total, 2); ?></div>
            <button onclick="document.getElementById('checkoutModal').style.display='block'" class="btn-add-cart" style="width: auto; padding: 15px 50px;">CHECKOUT</button>
        </div>
    <?php else: ?>
        <p>Your cart is empty.</p>
        <a href="index.php" style="text-decoration: underline; color: black;">Go back to shop</a>
    <?php endif; ?>
</main>

<div id="checkoutModal" class="modal">
    <div class="modal-content checkout-modal-content">
        <span class="close" onclick="document.getElementById('checkoutModal').style.display='none'">&times;</span>
        <h2 style="text-align: center; letter-spacing: 2px;">SHIPPING DETAILS</h2>
        <form action="place_order.php" method="POST">
            <input type="text" name="full_name" class="form-control" placeholder="Full Name" required>
            <textarea name="address" class="form-control" placeholder="Shipping Address" required></textarea>
            <select name="payment_method" class="form-control">
                <option value="COD">Cash on Delivery</option>
                <option value="GCash">GCash</option>
            </select>
            <button type="submit" class="btn-add-cart">PLACE ORDER</button>
        </form>
    </div>
</div>

<?php if(isset($_GET['order_success'])): 
    $oid = $_GET['id'];
    $order_query = mysqli_query($conn, "SELECT * FROM orders WHERE id = $oid");
    if($order = mysqli_fetch_assoc($order_query)): ?>
    <div id="successModal" class="modal" style="display: block;">
        <div class="modal-content" style="max-width: 400px; text-align: center; position: relative;">
            <span class="close-receipt" onclick="document.getElementById('successModal').style.display='none'">&times;</span>
            <i class="fas fa-check-circle" style="font-size: 60px; color: #28a745; margin-bottom: 20px;"></i>
            <h2 style="margin-bottom: 10px;">ORDER SUCCESS</h2>
            <p style="color: #666; font-size: 14px;">Thank you for your purchase!</p>
            <hr style="border: 0; border-top: 1px dashed #ddd; margin: 20px 0;">
            <div style="text-align: left; font-size: 14px; line-height: 2;">
                <p><strong>Order ID:</strong> #<?php echo $oid; ?></p>
                <p><strong>Total Amount:</strong> ₱<?php echo number_format($order['total_amount'], 2); ?></p>
                <p><strong>Shipping To:</strong> <?php echo $order['full_name']; ?></p>
            </div>
            <a href="index.php" class="btn-add-cart" style="display: block; text-decoration: none; margin-top: 25px; line-height: 50px;">CONTINUE SHOPPING</a>
        </div>
    </div>
<?php endif; endif; ?>

</body>
</html>