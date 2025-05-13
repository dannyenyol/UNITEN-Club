<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ./function/logout.php");
    exit;
}

require_once './function/db.php';
$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id, name, description FROM clubs");
$stmt->execute();
$clubs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Page</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
       
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
        }

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

        .main-content {
            flex: 1;
            padding: 20px;
            box-sizing: border-box;
        }

        .clubs-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .club-card {
            background-color: #f4f4f4;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .club-card h3 {
            margin: 10px 0;
            color: #237ad2;
        }

        .club-card p {
            font-size: 14px;
            color: #555;
        }

        .club-card button {
            background-color: #237ad2;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .club-card button:hover {
            background-color: #1a5ba3;
        }

        .pagination {
            margin-top: 20px;
            text-align: right;
            padding-right: 20px;
        }

        .pagination button {
            background-color: #237ad2;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin: 0 5px;
        }

        .pagination button.active {
            background-color: #1a5ba3;
        }

        .pagination button:hover {
            background-color: #1a5ba3;
        }

        .modal {
            background-color: rgba(0, 0, 0, 0.5);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
            text-align: center;
            position: relative;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .modal-content .close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            cursor: pointer;
        }

        .modal-content form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .modal-content form input,
        .modal-content form button {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .modal-content form button {
            background-color: #237ad2;
            color: white;
            cursor: pointer;
        }

        .modal-content form button:hover {
            background-color: #1a5ba3;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <img src="../assets/images/Universiti_Tenaga_Nasional_Logo.png" alt="Admin Logo">
        <h2>User Panel</h2>
        <p>Welcome, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>!</p>
        <?php
        if (trim(strtolower($_SESSION['role'])) == "president") {
            echo '<a href="./presidentpage.php">Manage Club</a>';
            echo '<a href="./pendingrequest.php">Pending Request</a>';
        }
        ?>
        <a href="./function/logout.php">Logout</a>
    </div>

    <div class="main-content">
        <h1>Available Clubs</h1>
        <div class="clubs-container">
            <?php foreach ($clubs as $club): ?>
                <div class="club-card">
                    <h3><?= htmlspecialchars($club['name']) ?></h3>
                    <p><?= htmlspecialchars($club['description']) ?></p>
                    <button
                        onclick="viewClubDetails(<?= $club['id'] ?>, '<?= addslashes(htmlspecialchars($club['name'])) ?>')">Register</button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="club-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modal-club-name">Club Name</h2>
            <form id="registration-form" method="POST" action="./function/join_club.php">
                <input type="hidden" name="club_id" id="modal-club-id">
                <label for="email">Your Email:</label>
                <input type="email" id="email" name="email" required>
                <button type="submit">Register</button>
            </form>
        </div>
    </div>

    <script>
        function viewClubDetails(id, name) {
            document.getElementById('modal-club-name').textContent = name;
            document.getElementById('modal-club-id').value = id;
            document.getElementById('club-modal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('club-modal').style.display = 'none';
        }
    </script>
</body>

</html>