<?php
require_once './db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $applicationId = $_POST['application_id'] ?? null;
    $action = $_POST['action'] ?? null;

    if (!$applicationId || !in_array($action, ['accept', 'reject'])) {
        http_response_code(400);
        echo "Invalid request";
        exit;
    }

    $newStatus = $action === 'accept' ? 'accepted' : 'rejected';

    $stmt = $conn->prepare("UPDATE club_registrations SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $newStatus, $applicationId);

    if ($stmt->execute()) {
        echo "Application $action successfully.";
    } else {
        http_response_code(500);
        echo "Failed to update application.";
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(405);
    echo "Method Not Allowed";
}
?>
