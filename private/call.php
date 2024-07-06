<?php
@session_start();
function displayMessage($message, $color) {
    echo '<div style="color: ' . $color . ';">' . $message . '</div>';
    unset($_SESSION['success']);
    unset($_SESSION['failure']);
}
if (isset($_SESSION['success'])) {
    displayMessage($_SESSION['success'], '#00ff00'); 
} elseif (isset($_SESSION['failure'])) {
    displayMessage($_SESSION['failure'], '#ff0000'); 
}
?>