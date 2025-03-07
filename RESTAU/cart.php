<?php 
session_start();

try {
    $connect = new PDO("mysql:host=localhost;dbname=solirestaurant", "root", "");
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $idPlat = $_POST['idPlat'] ?? null;
    $nomPlat = $_POST['nomPlat'] ?? '';
    $prix = $_POST['prix'] ?? 0;

    switch ($_POST['action']) {
        case 'add_to_cart':
            if (!isset($_SESSION['cart'][$idPlat])) {
                $_SESSION['cart'][$idPlat] = ['nomPlat' => $nomPlat, 'prix' => $prix, 'quantity' => 1];
            } else {
                $_SESSION['cart'][$idPlat]['quantity']++;
            }
            break;

        case 'increase_quantity':
            if (isset($_SESSION['cart'][$idPlat])) {
                $_SESSION['cart'][$idPlat]['quantity']++;
            }
            break;

        case 'decrease_quantity':
            if (isset($_SESSION['cart'][$idPlat])) {
                if ($_SESSION['cart'][$idPlat]['quantity'] > 1) {
                    $_SESSION['cart'][$idPlat]['quantity']--;
                } else {
                    unset($_SESSION['cart'][$idPlat]);
                }
            }
            break;

        case 'remove_from_cart':
            unset($_SESSION['cart'][$idPlat]);
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <style>
        /* General Styling */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8;
        }
        .cart {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .cart h3 {
            text-align: center;
            font-size: 24px;
            color: #333;
        }
        .card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            margin: 15px auto;
            background: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 250px;
            text-align: center;
        }
        .quantity-btn, .remove-btn {
            padding: 8px 12px;
            font-size: 14px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }
        .quantity-btn {
            background-color: #007bff;
            color: white;
            margin: 5px;
        }
        .quantity-btn:hover {
            background-color: #0056b3;
        }
        .remove-btn {
            background-color: orange;
            color: white;
            margin-top: 10px;
        }
        .remove-btn:hover {
            background-color : darkorange;
        }
        h3.total {
            text-align: center;
            color: #333;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="cart">
    <h3>Your Cart</h3>
    <?php 
    $total = 0;
    if (!empty($_SESSION['cart'])): 
        foreach ($_SESSION['cart'] as $idPlat => $item): 
            $subtotal = $item['prix'] * $item['quantity'];
            $total += $subtotal;
    ?>
        <div class="card">
            <p><strong>Dish: </strong><?= htmlspecialchars($item['nomPlat']) ?></p>
            <p><strong>Quantity: </strong><?= htmlspecialchars($item['quantity']) ?></p>
          
            <form method="post">
                <input type="hidden" name="idPlat" value="<?= $idPlat ?>">
                <button type="submit" name="action" value="decrease_quantity" class="quantity-btn">-</button>
                <button type="submit" name="action" value="increase_quantity" class="quantity-btn">+</button>
            </form>
            <form method="post">
                <input type="hidden" name="idPlat" value="<?= $idPlat ?>">
                <button type="submit" name="action" value="remove_from_cart" class="remove-btn">Remove</button>
            </form>
        </div>
    <?php endforeach; ?>
        <h3>Total: <?= $total ?> DH</h3>
    <?php else: ?>
        <p>Your cart is empty!</p>
    <?php endif; ?>
</div>
</body>
</html>
