<?php



function isAdmin() {
    return isLoggedIn() && $_SESSION['role'] == 'Manager';
}


function isLoggedIn() {
    
    return isset($_SESSION['user_id']);
}

function isProjectLeader() {
    
    return isLoggedIn() && $_SESSION['role'] == 'Project Leader';
}

function logout() {
    session_start();
    
    session_destroy();
    
    header("Location: login.php");
    exit();
}

function isTeamMember() {
    return isLoggedIn() && $_SESSION['role'] == 'Team Member';
}
?>