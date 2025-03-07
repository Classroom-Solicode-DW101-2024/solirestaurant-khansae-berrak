<?php
// Connexion à la base de données
$dsn = 'mysql:host=localhost;dbname=solirestaurant;charset=utf8';
$username = 'root';
$password = '';
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Affichage des commandes du jour avec détails العميل
$stmt = $pdo->query("
    SELECT c.idCmd, c.statut, cl.nomCl, cl.prenomCl, cl.telCl, p.nomPlat
    FROM commande c
    JOIN client cl ON c.idCl = cl.idClient
    JOIN plat p ON c.idPlat = p.idPlat
    WHERE DATE(c.dateCmd) = CURDATE()
");

$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);



// Mise à jour du statut d'une commande
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['statut'])) {
    $stmt = $pdo->prepare("UPDATE commande SET statut = ? WHERE id = ?");
    $stmt->execute([$_POST['statut'], $_POST['id']]);
    echo "Statut mis à jour avec succès !";
    exit;
}

// Récupération des statistiques
$stats = [
    'total_commandes' => $pdo->query("SELECT COUNT(*) FROM commande")->fetchColumn(),
    'plats_commandes' => $pdo->query("SELECT plat, COUNT(*) as total FROM commande GROUP BY plat")->fetchAll(PDO::FETCH_ASSOC),
    'total_clients' => $pdo->query("SELECT COUNT(DISTINCT idClient) FROM commande")->fetchColumn(),
    'commandes_annulees' => $pdo->query("SELECT COUNT(*) FROM commande WHERE statut = 'annulée'")->fetchColumn()
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion du Restaurant</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 10px; text-align: left; }
    </style>
    <script>
        function updateStatus(id) {
            let statut = document.getElementById('statut-' + id).value;
            fetch('', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}&statut=${statut}`
            }).then(response => response.text()).then(alert);
        }
    </script>
</head>
<body>
    <h1>Commandes du Jour</h1>
    <table>
        <tr><th>ID</th><th>Plat</th><th>Client</th><th>Statut</th><th>Action</th></tr>
        <?php foreach ($commandes as $commande): ?>
        <tr>
            <td><?= htmlspecialchars($commande['id']) ?></td>
            <td><?= htmlspecialchars($commande['plat']) ?></td>
            <td><?= htmlspecialchars($commande['nomCl']) . " " . htmlspecialchars($commande['prenomCl']) ?></td>
            <td>
                <select id="statut-<?= $commande['id'] ?>">
                    <option value="en attente" <?= $commande['statut'] === 'en attente' ? 'selected' : '' ?>>En attente</option>
                    <option value="en cours" <?= $commande['statut'] === 'en cours' ? 'selected' : '' ?>>En cours</option>
                    <option value="expédiée" <?= $commande['statut'] === 'expédiée' ? 'selected' : '' ?>>Expédiée</option>
                    <option value="livrée" <?= $commande['statut'] === 'livrée' ? 'selected' : '' ?>>Livrée</option>
                    <option value="annulée" <?= $commande['statut'] === 'annulée' ? 'selected' : '' ?>>Annulée</option>
                </select>
            </td>
            <td><button onclick="updateStatus(<?= $commande['id'] ?>)">Mettre à jour</button></td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <h1>Statistiques</h1>
    <p>Nombre total de commandes : <?= $stats['total_commandes'] ?></p>
    <p>Nombre total de clients : <?= $stats['total_clients'] ?></p>
    <p>Commandes annulées : <?= $stats[ commandes_annulees'] ?></p>
    
    <h2>Plats commandés</h2>
    <ul>
        <?php foreach ($stats['plats_commandes'] as $plat): ?>
            <li><?= htmlspecialchars($plat['plat']) ?> - <?= htmlspecialchars($plat['total']) ?> fois</li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
