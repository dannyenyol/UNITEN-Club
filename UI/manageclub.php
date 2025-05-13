<?php
session_start();

if (!isset($_SESSION['user_id']) && $_SESSION['user_id'] == 10001) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Clubs</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
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

    .admin-container {
      flex: 1;
      padding: 20px;
    }

    .admin-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .add-new-btn {
      padding: 10px 20px;
      background-color: #2ecc71;
      color: white;
      border: none;
      cursor: pointer;
    }

    .search-filter-bar {
      margin: 15px 0;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .search-input {
      width: 48%;
      padding: 8px;
      font-size: 14px;
    }

    #delete-selected-btn {
      padding: 8px 15px;
      background-color: #e74c3c;
      color: white;
      border: none;
      cursor: pointer;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th,
    td {
      padding: 10px;
      border: 1px solid #ccc;
      text-align: left;
    }

    .edit-btn,
    .delete-btn {
      margin: 0 5px;
      padding: 5px 10px;
      cursor: pointer;
    }

    .edit-btn {
      background-color: #3498db;
      color: white;
    }

    .delete-btn {
      background-color: #e74c3c;
      color: white;
    }

    #editModal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.6);
      justify-content: center;
      align-items: center;
    }

    #editModal .modal-content {
      background: white;
      padding: 20px;
      width: 400px;
      border-radius: 8px;
      position: relative;
    }

    #editModal input {
      width: 100%;
      padding: 8px;
      margin-bottom: 10px;
    }

    #editModal button {
      padding: 8px 12px;
      margin-right: 10px;
    }

    #editModal h3 {
      margin-top: 0;
    }
  </style>
</head>

<body>
  <div class="sidebar">
    <img src="../assets/images/Universiti_Tenaga_Nasional_Logo.png" alt="Admin Logo" />
    <h2>Admin Panel</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="manageclub.php" class="active">Manage Clubs</a>
    <a href="registerclub.php">Register Club</a>
    <a href="./function/logout.php">Logout</a>
  </div>

  <div class="admin-container">
    <header class="admin-header">
      <h1>Clubs List</h1>
      <button class="add-new-btn" onclick="window.location.href='registerclub.html'">Add New +</button>
    </header>

    <div class="search-filter-bar">
      <input type="text" id="search-input" placeholder="Search here" class="search-input" />
      <button id="delete-selected-btn" onclick="deleteSelectedClubs()">Delete Selected</button>
    </div>

    <table class="customers-table" id="clubs-table">
      <thead>
        <tr>
          <th><input type="checkbox" id="select-all-checkbox" /></th>
          <th>Club Name</th>
          <th>Club Leader</th>
          <th>Contact</th>
          <th>Club Type</th>
          <th>Total Members</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php include './function/fetch_club.php'; ?>
      </tbody>
    </table>
  </div>

  <div id="editModal">
    <div class="modal-content">
      <h3>Edit Club</h3>
      <form id="editForm">
        <input type="hidden" id="editClubId" name="club_id" />
        <label>Club Name</label>
        <input type="text" id="editClubName" name="club_name" required />
        <label>Club Leader</label>
        <input type="text" id="editClubLeader" name="club_leader" required />
        <label>Contact</label>
        <input type="text" id="editContact" name="contact" required />
        <label>Club Type</label>
        <input type="text" id="editClubType" name="club_type" required />
        <label>Total Members</label>
        <input type="number" id="editTotalMembers" name="total_members" required />
        <button type="submit">Save Changes</button>
        <button type="button" onclick="closeEditModal()">Cancel</button>
      </form>
    </div>
  </div>

  <script>
    function logout() {
      window.location.href = "./function/logout.php";
    }

    function toggleStatus(buttonElement) {
      console.log('here');
      const clubId = buttonElement.getAttribute("data-club-id");
      const statusCell = buttonElement.parentNode.previousElementSibling;
      const currentStatus = statusCell.textContent.trim();
      var newStatus;

      if (currentStatus == 'Active') {
        newStatus = 0;
        statusCell.textContent = "Not Active";
      } else {
        newStatus = 1;
        statusCell.textContent = "Active";
      }

      const xhr = new XMLHttpRequest();
      xhr.open("POST", "./function/update_club_status.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.send("club_id=" + clubId + "&status=" + newStatus);
    }

    function editClub(clubId) {
      console.log("editClub called with ID:", clubId);
      const row = document.querySelector(`#clubs-table tbody input[value="${clubId}"]`).closest("tr");
      const name = row.children[1].textContent.trim();
      const leader = row.children[2].textContent.trim();
      const contact = row.children[3].textContent.trim();
      const type = row.children[4].textContent.trim();
      const totalMembers = row.children[5].textContent.trim();

      document.getElementById("editClubId").value = clubId;
      document.getElementById("editClubName").value = name;
      document.getElementById("editClubLeader").value = leader;
      document.getElementById("editContact").value = contact;
      document.getElementById("editClubType").value = type;
      document.getElementById("editTotalMembers").value = totalMembers;

      document.getElementById("editModal").style.display = "flex";
    }

    function closeEditModal() {
      document.getElementById("editModal").style.display = "none";
    }

    document.getElementById("editForm").addEventListener("submit", function (e) {
      e.preventDefault();
      const formData = new FormData(this);

      console.log("Submitting form with data:");
      for (let [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
      }

      const xhr = new XMLHttpRequest();
      xhr.open("POST", "./function/edit_club.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
          console.log("Response received:", xhr.responseText);
          if (xhr.status === 200) {
            closeEditModal();
            
          } else {
            console.error("Error during request:", xhr.status, xhr.statusText);
          }
        }
      };
      xhr.send(new URLSearchParams(formData).toString());
    });


    document.addEventListener("DOMContentLoaded", () => {
      const searchInput = document.getElementById("search-input");
      const clubsTable = document.getElementById("clubs-table");
      const selectAllCheckbox = document.getElementById("select-all-checkbox");

      const checkboxes = clubsTable.querySelectorAll("tbody input[type='checkbox']");

      searchInput.addEventListener("input", () => {
        const searchTerm = searchInput.value.toLowerCase();
        const rows = clubsTable.querySelectorAll("tbody tr");

        rows.forEach((row) => {
          const clubName = row.children[1].textContent.toLowerCase();
          const clubLeader = row.children[2].textContent.toLowerCase();
          const clubType = row.children[4].textContent.toLowerCase();

          row.style.display = clubName.includes(searchTerm) || clubLeader.includes(searchTerm) || clubType.includes(searchTerm) ? "" : "none";
        });
      });

      selectAllCheckbox.addEventListener("change", () => {
        checkboxes.forEach((checkbox) => {
          checkbox.checked = selectAllCheckbox.checked;
        });
      });

      checkboxes.forEach((checkbox) => {
        checkbox.addEventListener("change", () => {
          if (!checkbox.checked) {
            selectAllCheckbox.checked = false;
          } else if ([...checkboxes].every((cb) => cb.checked)) {
            selectAllCheckbox.checked = true;
          }
        });
      });
    });

    function deleteSelectedClubs() {
      const checkboxes = document.querySelectorAll("#clubs-table tbody input[type='checkbox']:checked");
      const clubIds = Array.from(checkboxes).map((checkbox) => checkbox.value);

      if (clubIds.length > 0 && confirm("Are you sure you want to delete the selected clubs?")) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "./function/delete_selected_clubs.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
          if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            location.reload();
          }
        };
        xhr.send("club_ids=" + JSON.stringify(clubIds));
      }
    }

    function deleteClub(clubId) {
      if (confirm("Are you sure you want to delete this club?")) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "./function/delete_club.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
          if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            location.reload();
          }
        };
        xhr.send("club_id=" + clubId);
      }
    }
  </script>
</body>

</html>