<?php
session_start();
require 'includes/auth.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}
$userMenu ='';

if (isAdmin()){
  $userMenu = ' <li><a href="add_project.php">Add Project</a></li> <li><a href="allocate_leader.php">Allocate Team Leader</a></li><li><a href="search_task.php">Search Tasks</a></li>';
}elseif(isProjectLeader()){
     $userMenu = '<li><a href="create_task.php">Create Task</a></li>
          <li><a href="assign_team.php">Assign Team Members</a></li>
          <li><a href="search_task.php">Search Tasks</a></li>';
} elseif(isTeamMember()){
        $userMenu = '<li><a href="accept_task.php">Accept Tasks</a></li>
                    <li><a href="search_task.php">Search Tasks</a></li>';

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Task Allocator Pro</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
        <h1>Task Allocator Pro</h1>
           <a href="profile.php">User Profile</a>
           <a href="logout.php">Logout</a>
</header>

    <main class="container">
        <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
        
        <nav>
             <ul>
                  <?php echo $userMenu; ?>

             </ul>
         </nav>

        <main>
            <p>This is your dashboard.</p>
        </main>

    </main>
    <footer>
        <p>© <?php echo date("Y"); ?> Task Allocator Pro |
        Contact: <a href="mailto:contact@taskallocatorpro.com">contact@taskallocatorpro.com</a> |
        <a href="about.php">About Us</a></p>
    </footer>
</body>
</html>