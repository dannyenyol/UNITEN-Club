<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ./logout.php");
    exit;
}

require_once './db.php';

$userId = $_SESSION['user_id'];
$clubId = $_POST['club_id'] ?? null;
$email = $_POST['email'] ?? '';

if ($clubId && filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $stmt = $conn->prepare("INSERT INTO club_registrations (user_id, club_id, email) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $userId, $clubId, $email);
    $stmt->execute();
    header("Location: ../userpage.php?success=1");
    exit;
} else {
    header("Location: ../userpage.php?error=1");
    exit;
}
