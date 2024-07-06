<link rel="stylesheet" href="css/menu.css">
  <style>
.modal {
    display: none; 
    position: fixed;
    z-index: 1; 
    left: 0;
    top: 0;
    width: 100%; 
    height: 100%; 
    overflow: auto; 
    background-color: rgb(0,0,0); 
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto; 
    padding: 20px;
    border: 1px solid #888;
    width: 80%; 
    max-width: 300px; 
    text-align: center;
}
button {
    margin: 5px;
    padding: 10px 20px;
    border: none;
    cursor: pointer;
    border-radius: 5px;
}

#confirmLogout {
    background-color: #f44336;
    color: white;
}

#cancelLogout {
    background-color: #ccc;
    color: black;
}
.logout{
    background: none; 
    border: none;
    font-size: 26px; 
    padding: 0; 
    margin: 0; 
    cursor: pointer;
    display: inline;
}
  </style>
</head>
<body>
  <header>
    <div class="topnav">
      <span class="dropdown">
      <?php
      require_once 'private/autoload.php';
      getadmin();
        if(isset($_SESSION['name'])){
          echo  $_SESSION['name'];
        }
        ?>
        <span class='dropdownlist'>Account</span>
        <div class="dropdown-content">
          <a href="changepassword.php">Change Password</a>
          <button id="logoutBtn" class="logout">Logout</button>
        </div>

        <!-- The Modal -->
        <div id="logoutModal" class="modal">
          <div class="modal-content">
            <span class="close">&times;</span>
            <p style='color:black;'>Are you sure you want to logout?</p>
            <div class="modal-buttons">
                <button id="confirmLogout" class="btn">Yes, Logout</button>
                <button id="cancelLogout" class="btn">Cancel</button>
            </div>
          </div>
        </div>
      </span>
    </div>
  </header>
  <nav class="navbar-nav">
    <div class="sidenav-menu">
      <div class="leftnav">
        <div class='sidenav'>
          <a class="sidenav-heading" href="dashboard.php">CFM System</a>
        </div>
        <div class='navbar'>
          <ul class="nav-navbar-nav">
            <li><a class='nav-link' href="dashboard.php">Dashboard</a></li>
            <li><a class='nav-link' href="admin.php">Admin</a></li>
            <li><a class='nav-link' href="course.php">Course</a></li>
            <li><a class='nav-link' href="semester.php">Semester</a></li>
            <li><a class='nav-link' href="batch.php">Batch</a></li>
            <li><a class='nav-link' href="student.php">Student</a></li>
            <li><a class='nav-link' href="fee.php">Fee</a>
            <ul>
                <li><a class='nav-link' href="income.php">Income</a></li>
              </ul>
            </li>
            <li><a class='nav-link' href="payment.php">Payment</a>
            <ul>
              <li><a class='nav-link' href="receivefee.php">Payment receive</a></li>
            </ul>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </nav>
<script src='javascript/logout.js'></script>

