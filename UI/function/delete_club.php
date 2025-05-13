<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clubId = $_POST['club_id'] ?? null;

    if ($clubId) {
        $stmt = $conn->prepare("DELETE FROM clubs WHERE id = ?");
        $stmt->bind_param("i", $clubId);
        if ($stmt->execute()) {
            echo "Club deleted successfully.";
        } else {
            echo "Failed to delete club.";
        }
        $stmt->close();
    }
}

$conn->close();
?>