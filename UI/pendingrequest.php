<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['user_id']) || trim($_SESSION['role']) !== 'President') {
  header("Location: ./function/logout.php");
  exit;
}

require_once './function/db.php';

$userId = $_SESSION['user_id'];

// Step 1: Get the club ID of the current President
$clubQuery = "SELECT id FROM clubs WHERE leader = ?";
$stmt = $conn->prepare($clubQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$clubResult = $stmt->get_result()->fetch_assoc();
$clubId = $clubResult['id'] ?? null;
$stmt->close();


$pendingApplications = [];
if ($clubId) {
  // Step 2: Get pending applications for this club
  $query = "SELECT cr.id AS application_id, u.username, u.email, cr.created_at 
  FROM club_registrations cr
  JOIN user u ON cr.user_id = u.id
  WHERE cr.club_id = ? AND cr.status = 'pending'";

  $stmt = $conn->prepare($query);
  $stmt->bind_param("i", $clubId);
  $stmt->execute();
  $pendingApplications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
  $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pending Requests</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      display: flex;
    }

    /* Sidebar styling */
    .sidebar {
      width: 250px;
      background-color: #237ad2;
      color: white;
      height: 100vh;
      padding: 20px;
      box-sizing: border-box;
    }

    .sidebar h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    .sidebar a {
      color: white;
      text-decoration: none;
      display: block;
      margin: 10px 0;
      padding: 10px;
      border-radius: 5px;
    }

    .sidebar a:hover {
      background-color: #34495e;
    }

    .sidebar img {
      width: 150px;
      height: auto;
      margin-bottom: 10px;
    }

    /* Main content styling */
    .main-content {
      flex-grow: 1;
      padding: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    table,
    th,
    td {
      border: 1px solid #ddd;
    }

    th,
    td {
      padding: 10px;
      text-align: left;
    }

    th {
      background-color: #f4f4f4;
    }

    .action-buttons button {
      margin-right: 5px;
      padding: 5px 10px;
      border: none;
      border-radius: 3px;
      cursor: pointer;
    }

    .accept-btn {
      background-color: #28a745;
      color: white;
    }

    .reject-btn {
      background-color: #dc3545;
      color: white;
    }
  </style>
</head>

<body>
  <div class="sidebar">
    <img src="../assets/images/Universiti_Tenaga_Nasional_Logo.png" alt="Admin Logo">
    <h2>President Panel</h2>
    <p>Welcome, <span id="username">President</span>!</p>
    <a href="presidentpage.php">My Club</a>
    <a href="pendingrequest.php" class="active">Pending Request</a>
    <a href="#" onclick="logout()">Logout</a>
  </div>
  <div class="main-content">
    <h1>Pending Member Applications</h1>
    <p>Review and manage pending applications for your club.</p>
    <table>
      <thead>
        <tr>
          <th>Applicant Name</th>
          <th>Email</th>
          <th>Application Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="applications-table">
        <?php if (count($pendingApplications) > 0): ?>
          <?php foreach ($pendingApplications as $app): ?>
            <tr>
              <td><?= htmlspecialchars($app['username']) ?></td>
              <td><?= htmlspecialchars($app['email']) ?></td>
              <td><?= htmlspecialchars($app['created_at']) ?></td>
              <td class="action-buttons">
  <button class="accept-btn" onclick="handleApplication(this, 'accept')" data-id="<?= $app['application_id'] ?>">Accept</button>
  <button class="reject-btn" onclick="handleApplication(this, 'reject')" data-id="<?= $app['application_id'] ?>">Reject</button>
</td>

            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="4">No pending applications.</td>
          </tr>
        <?php endif; ?>
      </tbody>

    </table>
  </div>
  <script>
function logout() {
  window.location.href = "./function/logout.php";
}

function handleApplication(button, action) {
  const row = button.closest('tr');
  const applicationId = button.getAttribute('data-id');

  fetch('./function/handle_application.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: `application_id=${applicationId}&action=${action}`
  })
  .then(response => response.text())
  .then(data => {
    alert(data);
    row.remove();
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred while processing the request.');
  });
}
</script>

</body>

</html>