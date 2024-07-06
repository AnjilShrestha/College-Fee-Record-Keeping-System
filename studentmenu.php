<?php
require_once 'private/autoload.php';
getuser();
if(isset($_SESSION['id'])){
  $id=$_SESSION['id'];
}
$sql="SELECT * FROM student_tb WHERE student_id='$id'";
$result = $connection->query($sql);
$result = mysqli_query($connection,$sql);
if(mysqli_num_rows($result) > 0){
  $data = mysqli_fetch_assoc($result);
  extract($data);
}
?>
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
.top-right-image {
    position: absolute;
    float:right;
    top: 5px;
    right:15em;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    border: 1px solid #007bff;
    object-fit: cover;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}
  </style>
</head>
<body>
<header>
  <div class="topnav">
    <span class="dropdown">
      <img src="./upload/<?php echo isset($data['image'])?$data['image']:'noprofil.jpg'; ?>" alt="Profile Picture" class="top-right-image">
      <span class='dropdownlist'> 
        <?php echo $data['name'];?> Account</span>
      <div class="dropdown-content">
        <a href="password.php">Change Password</a>
        <button id="logoutBtn" class="logout">Logout</button>
      </div>
      <div id="logoutModal" class="modal">
        <div class="modal-content">
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
          <li><a class='nav-link' href="studentdashboard.php">Dashboard</a></li>
          <li><a class='nav-link' href="personaldetails.php">Personal Details</a></li>
          <li><a class='nav-link' href="studentfee.php">Fee</a></li>
          <li><a class='nav-link' href="paidfee.php">Payment</a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>
<script src='./javascript/logout.js'></script>
