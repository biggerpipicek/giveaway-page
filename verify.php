<?php

$conn = new mysqli("127.0.0.1", "root", "", "giveaway-page");

if (!isset($_GET['token'])) {
    die("Neplatný odkaz.");
}

$token = $_GET['token'];

$stmt = $conn->prepare("UPDATE participants SET verified = 1 WHERE token = ?");

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("s", $token);
$stmt->execute();

// 👇 TADY JE KLÍČ
if ($stmt->affected_rows === 0) {
    die("Token neexistuje nebo už byl použit.");
}

echo "Účet byl ověřen! 🎉";