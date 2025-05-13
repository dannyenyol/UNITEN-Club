<?php
session_start();
require_once './db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['member_id'])) {
    $memberId = intval($_POST['member_id']);
    $stmt = $conn->prepare("UPDATE user SET club_id = NULL WHERE id = ?");
    $stmt->bind_param("i", $memberId);
    $success = $stmt->execute();
    $stmt->close();

    echo json_encode(['success' => $success]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}

$conn->close();
?>