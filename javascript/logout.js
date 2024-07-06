var modal = document.getElementById("logoutModal");
var btn = document.getElementById("logoutBtn");
var confirmLogoutBtn = document.getElementById("confirmLogout");
var cancelLogoutBtn = document.getElementById("cancelLogout");

btn.onclick = function() {
    modal.style.display = "block";
}

confirmLogoutBtn.onclick = function() {
    alert("Logging out..."); 
    window.location.href = 'logout.php';
}

cancelLogoutBtn.onclick = function() {
    modal.style.display = "none";
}

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
