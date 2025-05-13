
<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clubId = $_POST['club_id'] ?? null;
    $status = $_POST['status'] ?? null;

    if ($clubId !== null && $status !== null) { 
        $stmt = $conn->prepare("UPDATE clubs SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $clubId);
        if ($stmt->execute()) {
            echo "Status updated successfully.";
        } else {
            echo "Error updating status: " . $stmt->error; 
        }
        $stmt->close();
    } else {
        echo "Error: club_id or status not provided.";
    }
}

$conn->close();
?>