// Simulated user data
const users = [
    { username: "admin", password: "admin123" },
    { username: "user", password: "user123" }
  ];
  
  function login(event) {
    event.preventDefault();
  
    const usernameField = document.getElementById("username");
    const passwordField = document.getElementById("password");
  
    const username = usernameField.value.trim();
    const password = passwordField.value;
  
    const user = users.find(u => u.username === username && u.password === password);
  
    if (user) {
      localStorage.setItem("loggedInUser", username);
      alert("Login successful!");
      window.location.href = "dashboard.html";
    } else {
      alert("Incorrect username or password. Please try again.");
      // Clear input fields
      usernameField.value = "";
      passwordField.value = "";
      // Optionally focus back on username field
      usernameField.focus();
    }
  }

document.querySelectorAll('.action-btn').forEach(button => {
    button.addEventListener('click', () => {
        const dropdown = button.nextElementSibling;
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    });
});

function toggleStatus(button) {
    const statusCell = button.closest('tr').querySelector('.status-cell');
    if (statusCell.textContent === 'Active') {
        statusCell.textContent = 'Not Active';
        button.textContent = 'Set Active';
        button.classList.remove('active-btn');
        button.classList.add('not-active-btn');
    } else {
        statusCell.textContent = 'Active';
        button.textContent = 'Set Not Active';
        button.classList.remove('not-active-btn');
        button.classList.add('active-btn');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const statusCell = row.querySelector('.status-cell');
        const statusButton = row.querySelector('.status-btn');

        if (statusCell.textContent.trim() === 'Active') {
            statusButton.classList.add('active-btn');
            statusButton.textContent = 'Set Not Active';
        } else {
            statusButton.classList.add('not-active-btn');
            statusButton.textContent = 'Set Active';
        }
    });
});
