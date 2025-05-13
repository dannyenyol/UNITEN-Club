<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['club_id'];
    $name = $_POST['club_name'];
    $leader = $_POST['club_leader'];
    $contact = $_POST['contact'];
    $type = $_POST['club_type'];
    $members = $_POST['total_members'];

    $stmt = $conn->prepare("UPDATE clubs SET name=?, leader=?, contact_number=?, category=? WHERE id=?");
    $stmt->bind_param("ssssi", $name, $leader, $contact, $type, $id);

    if ($stmt->execute()) {
        echo "Club updated successfully";
    } else {
        echo "Error updating club";
    }

    $stmt->close();
    $conn->close();
}
?>