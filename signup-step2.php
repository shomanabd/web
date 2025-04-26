<?php
session_start();
require 'includes/dbconfig.in.php';
require 'includes/auth.php';


if (isLoggedIn() || !isset($_SESSION['registration_step']) || $_SESSION['registration_step'] != 2 ) {
    header("Location: dashboard.php");
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_SESSION['registration_data'])){
        $_SESSION['registration_data']  = array_merge($_SESSION['registration_data'], array_map('trim', $_POST));
   }else{
         $_SESSION['registration_data']  = array_map('trim', $_POST);
   }


    $requiredFields2 = ['username', 'password', 'password_confirmation'];
    foreach($requiredFields2 as $field) {
        if(empty($_SESSION['registration_data'][$field])) {
           $errors[$field] = "This field is required";
        }
    }

     // Validate username format and length
     if(!empty($_SESSION['registration_data']['username']) && (!ctype_alnum($_SESSION['registration_data']['username']) || strlen($_SESSION['registration_data']['username']) < 6 || strlen($_SESSION['registration_data']['username']) > 13)){
         $errors['username'] = "Username must be between 6 and 13 chars, alphanumeric";
     }

     // Validate password format and length
    if(!empty($_SESSION['registration_data']['password']) && (strlen($_SESSION['registration_data']['password']) < 8 || strlen($_SESSION['registration_data']['password']) > 12 || !preg_match('/[a-zA-Z]/',$_SESSION['registration_data']['password']) || !preg_match('/\d/',$_SESSION['registration_data']['password'])))
    {
         $errors['password'] = "Password must be between 8 and 12 chars and include letters and numbers.";
     }

    if( $_SESSION['registration_data']['password'] !== $_SESSION['registration_data']['password_confirmation'])
    {
        $errors['password_confirmation'] = "Passwords do not match.";
    }

    // If there are no errors move to step 3
    if (empty($errors)) {
        $_SESSION['registration_step'] = 3;
        header("Location: signup-step3.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up - Step 2 - Task Allocator Pro</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
        <h1>Task Allocator Pro</h1>
    </header>
    <main class="container">
        <h2>User Registration - Step 2</h2>
           <form method="post">
                <h3>E-Account Creation</h3>
               <label for="username">Username:</label>
               <input type="text" id="username" name="username" value="<?= isset($_SESSION['registration_data']['username']) ? $_SESSION['registration_data']['username'] : '' ?>" required>
                <?php if(isset($errors['username'])): ?>
                     <p class="error"><?= $errors['username'] ?></p>
                    <?php endif ?>


                 <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                 <?php if(isset($errors['password'])): ?>
                      <p class="error"><?= $errors['password'] ?></p>
                     <?php endif ?>


                <label for="password_confirmation">Confirm Password:</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
                <?php if(isset($errors['password_confirmation'])): ?>
                     <p class="error"><?= $errors['password_confirmation'] ?></p>
                 <?php endif ?>

             <button type="submit">Proceed</button>

        </form>
    </main>
    <footer>
        <p>© <?php echo date("Y"); ?> Task Allocator Pro |
        Contact: <a href="mailto:contact@taskallocatorpro.com">contact@taskallocatorpro.com</a> |
        <a href="about.php">About Us</a></p>
    </footer>
</body>
</html>