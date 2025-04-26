<?php
session_start();
require 'includes/dbconfig.in.php';
require 'includes/auth.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$errors = [];
$userProfile = [];

try {
    $stmt = $pdo->prepare("SELECT user_id, name, address, date_of_birth, id_number, email, telephone, role, qualification, skills, username FROM Users WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $userProfile = $stmt->fetch();

    if(!$userProfile){
        $errors['user'] = "User not found.";
     }
} catch (PDOException $e) {
    $errors['db'] = "There was a problem fetching user details.";
        echo $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile - Task Allocator Pro</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
        <h1>Task Allocator Pro</h1>
        <a href="profile.php">User Profile</a>
        <a href="logout.php">Logout</a>
</header>
    <main class="container">
       <h2>User Profile</h2>
        <p><a href="dashboard.php">Back to Dashboard</a></p>

         <?php if (isset($errors['user'])): ?>
              <p class="error"><?= $errors['user'] ?></p>
        <?php else: ?>
              <?php if (!empty($userProfile)): ?>
                 <p><strong>Name:</strong> <?=  $userProfile['name']  ?></p>
                <p><strong>Address:</strong> <?=  $userProfile['address'] ?></p>
                 <p><strong>Date of Birth:</strong> <?=  $userProfile['date_of_birth'] ?></p>
                   <p><strong>ID Number:</strong> <?= $userProfile['id_number']  ?></p>
                <p><strong>Email:</strong>  <?=  $userProfile['email'] ?></p>
               <p><strong>Telephone:</strong> <?= $userProfile['telephone'] ?></p>
              <p><strong>Role:</strong> <?=  $userProfile['role'] ?></p>
              <p><strong>Qualification:</strong> <?=  $userProfile['qualification'] ?></p>
               <p><strong>Skills:</strong> <?=  $userProfile['skills'] ?></p>
                <p><strong>Username:</strong> <?= $userProfile['username'] ?></p>
           <?php endif; ?>
           <?php if (isset($errors['db'])): ?>
               <p class="error"><?= $errors['db'] ?></p>
           <?php endif; ?>
       <?php endif; ?>
    </main>
    <footer>
        <p>© <?php echo date("Y"); ?> Task Allocator Pro |
        Contact: <a href="mailto:contact@taskallocatorpro.com">contact@taskallocatorpro.com</a> |
        <a href="about.php">About Us</a></p>
    </footer>
</body>
</html>