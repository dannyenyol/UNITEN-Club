<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clubIds = json_decode($_POST['club_ids'], true);

    if (!empty($clubIds) && is_array($clubIds)) {
        $placeholders = implode(',', array_fill(0, count($clubIds), '?'));
        $types = str_repeat('i', count($clubIds));
        $stmt = $conn->prepare("DELETE FROM clubs WHERE id IN ($placeholders)");

        $stmt->bind_param($types, ...$clubIds);
        if ($stmt->execute()) {
            echo "Selected clubs deleted.";
        } else {
            echo "Failed to delete selected clubs.";
        }
        $stmt->close();
    }
}

$conn->close();
?>