<?php
session_start();
require 'includes/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Us - Task Allocator Pro</title>
     <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
        <h1>Task Allocator Pro</h1>
           <a href="profile.php">User Profile</a>
           <a href="logout.php">Logout</a>
</header>
    <main class="container">
        <h2>About Us</h2>
           <p>Task Allocator Pro is a task management system built to facilitate efficient task allocation and monitoring within small teams. </p>
           <p>Our goal is to provide a tool that allows managers, project leaders and team members to allocate tasks, track progress and communicate effectively.</p>
           <p>This system allows the managers to create new projects, allocate project leaders and monitor the completion of each project. Project leaders can create and assign tasks, monitor their completion and track progress. Team members can accept or reject the assigned tasks and update the progress of those tasks as they go on with their work.</p>
    </main>
    <footer>
        <p>© <?php echo date("Y"); ?> Task Allocator Pro |
        Contact: <a href="mailto:contact@taskallocatorpro.com">contact@taskallocatorpro.com</a> |
        <a href="about.php">About Us</a></p>
    </footer>
</body>
</html>