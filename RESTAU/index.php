<?php 
session_start();
try {
    $connect = new PDO("mysql:host=localhost;dbname=solirestaurant", "root", "");
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Get distinct categories and types of cuisine
$categories = $connect->query("SELECT DISTINCT categoriePlat FROM plat WHERE categoriePlat IS NOT NULL AND categoriePlat != ''")->fetchAll(PDO::FETCH_COLUMN);
$typesCuisine = $connect->query("SELECT DISTINCT TRIM(TypeCuisine) FROM plat WHERE TypeCuisine IS NOT NULL AND TypeCuisine != '' LIMIT 5")->fetchAll(PDO::FETCH_COLUMN);

// Handle adding to cart
if (isset($_POST['add_to_cart'])) {
    $idPlat = $_POST['idPlat'];
    $nomPlat = $_POST['nomPlat'];
    $prix = $_POST['prix'];

    if (!isset($_SESSION['cart'][$idPlat])) {
        $_SESSION['cart'][$idPlat] = ['nomPlat' => $nomPlat, 'prix' => $prix, 'quantity' => 1];
    } else {
        $_SESSION['cart'][$idPlat]['quantity']++;
    }
}

// Prepare query with filters if applied
$whereClauses = [];
$params = [];
if (!empty($_GET['category'])) {
    $whereClauses[] = "categoriePlat = :category";
    $params['category'] = $_GET['category'];
}
if (!empty($_GET['typeCuisine'])) {
    $whereClauses[] = "TypeCuisine = :typeCuisine";
    $params['typeCuisine'] = $_GET['typeCuisine'];
}

$query = "SELECT * FROM plat";
if (!empty($whereClauses)) {
    $query .= " WHERE " . implode(" AND ", $whereClauses);
}

$stmt = $connect->prepare($query);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <style>
        
        header {
            background-color: #333;
            color: white;
            padding: 15px 0;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }

        .header-container h3 {
            font-size: 1.8em;
        }

        .header-nav a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-weight: bold;
            font-size: 1.1em;
            transition: color 0.3s ease;
        }

        .header-nav a:hover {
            color: #ff6347;
        }

        /* Menu Section */
        .menu-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            padding: 20px;
            margin-top: 20px;
        }

        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
            text-align: center;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-bottom: 2px solid #eee;
        }

        .card h3 {
            color: #333;
            font-size: 1.5em;
            margin: 15px 0;
        }

        .card p {
            color: #777;
            font-size: 1em;
            margin-bottom: 10px;
        }

        .card .price {
            font-size: 1.2em;
            color: #2e8b57;
            margin-bottom: 15px;
        }

        .add_to_cart {
            background-color: #ff6347;
            color: white;
            padding: 10px 20px;
            font-size: 1em;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-bottom: 15px;
        }

        .add_to_cart:hover {
            background-color: #e55347;
        }

        /* Responsive Styling */
        @media (max-width: 768px) {
            .menu-container {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
        }

        @media (max-width: 480px) {
            .menu-container {
                grid-template-columns: 1fr;
            }

            .header-nav a {
                font-size: 1em;
                margin-left: 10px;
            }
        }
        .hero-bg {
        position: relative;
        width: 100%;
        height: 60vh; /* Adjust the height as needed */
        overflow: hidden;
    }

    .hero-bg .overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.4); /* Semi-transparent overlay */
        z-index: 1;
    }

    .RESTAU-video {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: 0; /* Makes sure the video stays behind the overlay */
    }

    /* Optional: Add text or headings to be displayed over the video */
    .hero-bg h1 {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-size: 3em;
        z-index: 2;
    }
    .filter-form {
    display: flex;
    margin-left : 820px;
    gap: 15px;
    padding: 15px;
    background-color: #222; /* لون خلفية مناسب للهيدر */
    border-radius: 8px;
    margin-top: -30px;
    width: fit-content;
}

.filter-form select {
    padding: 10px 15px;
    font-size: 1em;
    border: 2px solid #ff6347; /* لون مميز */
    border-radius: 5px;
    background-color: white;
    color: #333;
    cursor: pointer;
    transition: all 0.3s ease;
}

.filter-form select:hover {
    background-color: #ff6347;
    color: white;
    border-color: #e55347;
}

.filter-form select:focus {
    outline: none;
    border-color: #2e8b57; /* تغيير لون التحديد */
}

    </style>
</head>

<header>
    <div class="header-container">
        <nav class="header-nav">
            <a href="index.php">Home</a>
            <a href="cart.php">Cart</a>
            <a href="login.php">Login</a>
        </nav>
    </div>

    <!-- Filter Form -->
    <form method="GET" class="filter-form">
        <select name="category" onchange="this.form.submit()">
            <option value="">Select Category</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= htmlspecialchars($category) ?>" <?= ($_GET['category'] ?? '') == $category ? 'selected' : '' ?>><?= htmlspecialchars($category) ?></option>
            <?php endforeach; ?>
        </select>

        <select name="typeCuisine" onchange="this.form.submit()">
            <option value="">Select Cuisine Type</option>
            <?php foreach ($typesCuisine as $type): ?>
                <option value="<?= htmlspecialchars($type) ?>" <?= ($_GET['typeCuisine'] ?? '') == $type ? 'selected' : '' ?>><?= htmlspecialchars($type) ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</header>

<!-- Hero Section -->
<section class="hero-bg">
    <div class="overlay"></div>
    <video autoplay loop muted class="RESTAU-video">
        <source src="restauphp.mp4" type="video/mp4">
    </video>
</section>

<body>
    <div class="menu-container">
        <?php foreach ($rows as $row): ?>
            <div class="card">
                <img src="<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['nomPlat']) ?>">
                <h3><?= htmlspecialchars($row['nomPlat']) ?></h3>
                <p>Category: <?= htmlspecialchars($row['categoriePlat']) ?></p>
                <p>TypeCuisine: <?= htmlspecialchars($row['TypeCuisine']) ?></p>
                <p class="price"><?= htmlspecialchars($row['prix']) ?> DH</p>
                <form method="post">
                    <input type="hidden" name="idPlat" value="<?= htmlspecialchars($row['idPlat']) ?>">
                    <input type="hidden" name="nomPlat" value="<?= htmlspecialchars($row['nomPlat']) ?>">
                    <input type="hidden" name="prix" value="<?= htmlspecialchars($row['prix']) ?>">
                    <input type="submit" name="add_to_cart" value="Add to Cart" class="add_to_cart">
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
