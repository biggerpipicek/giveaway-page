<?php

$host = "127.0.0.1";
$user = "root";
$pass = "";
$db = "giveaway-page";

$conn = new mysqli($host, $user, $pass, $db);

// LOSOVÁNÍ
$winner = null;
if (isset($_POST['draw'])) {
    $query = "SELECT * FROM participants WHERE verified = 1 ORDER BY RAND() LIMIT 1";
    $result = $conn->query($query);
    $winner = $result->fetch_assoc();
}

// SMAZÁNÍ
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM participants WHERE id = $id");
}

// NAČTENÍ VŠECH
$result = $conn->query("SELECT * FROM participants ORDER BY created_at DESC");

?>

<!DOCTYPE html>
<html lang="cs">
<head>
<meta charset="UTF-8">
<title>Admin panel</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-light">

<div class="container mt-5">

    <h2>Admin – Soutěž</h2>

    <!-- LOSOVÁNÍ -->
    <form method="POST" class="mb-4">
        <button name="draw" class="btn btn-success">🎲 Vylosovat vítěze</button>
    </form>

    <?php if ($winner): ?>
        <div class="alert alert-info">
            <strong>Vítěz:</strong><br>
            <?= htmlspecialchars($winner['name']) ?><br>
            <?= htmlspecialchars($winner['contact_type']) ?>: 
            <?= htmlspecialchars($winner['contact_value']) ?>
        </div>
    <?php endif; ?>

    <!-- TABULKA -->
    <table class="table table-dark table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Jméno</th>
                <th>Kontakt</th>
                <th>Typ</th>
                <th>Datum</th>
                <th>Akce</th>
            </tr>
        </thead>

        <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['contact_value']) ?></td>
                <td><?= $row['contact_type'] ?></td>
                <td><?= $row['created_at'] ?></td>
                <td>
                    <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm">Smazat</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

</div>

</body>
</html>