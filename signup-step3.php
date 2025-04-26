<?php
session_start();
require 'includes/dbconfig.in.php';
require 'includes/auth.php';

if (isLoggedIn() || !isset($_SESSION['registration_step']) || $_SESSION['registration_step'] != 3 ) {
    header("Location: dashboard.php");
    exit();
}


$errors = [];
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
       // combine data for the db insert
       $userData = $_SESSION['registration_data'];
       try {
            // Begin Transaction
            $pdo->beginTransaction();

            // Hash the password
            $password_hash = password_hash($userData['password'], PASSWORD_DEFAULT);


            // Insert user data into database

            $stmt = $pdo->prepare("INSERT INTO Users (name, address, date_of_birth, id_number, email, telephone, role, qualification, skills, username, password_hash) VALUES
            (:name, :address, :date_of_birth, :id_number, :email, :telephone, :role, :qualification, :skills, :username, :password_hash)");

             // Combine the address fields
             $address =  $userData['address_flat'] . "," .  $userData['address_street'] . "," .  $userData['address_city'] . "," .  $userData['address_country'];
             $stmt->execute([
                ':name' => $userData['name'],
                ':address' => $address,
                ':date_of_birth' => $userData['date_of_birth'],
                ':id_number' => $userData['id_number'],
                ':email' => $userData['email'],
                ':telephone' => $userData['telephone'],
                ':role' => $userData['role'],
                ':qualification' => $userData['qualification'],
                ':skills' => $userData['skills'],
                ':username' => $userData['username'],
                ':password_hash' => $password_hash
            ]);

           // Fetch user_id for generating the 10 digit system id
            $user_id = $pdo->lastInsertId();


            // Commit Transaction
            $pdo->commit();

            unset($_SESSION['registration_data']);
            unset($_SESSION['registration_step']);
            $successMessage = "Registration successful! Your user ID is " . str_pad($user_id, 10, '0', STR_PAD_LEFT)  . ". <a href='login.php'>Go to login page.</a>";
            header("Location: login.php");
             exit();

        } catch (PDOException $e) {
            $pdo->rollBack(); // Rollback transaction in case of error
            $errors['db'] = "There was a problem registering you, please try again.";
         echo $e->getMessage(); // Print the database error
        }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up - Step 3 - Task Allocator Pro</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
        <h1>Task Allocator Pro</h1>
    </header>
    <div class="container">
        <h2>User Registration - Step 3</h2>
          <?php if (!empty($successMessage)) :?>
               <p class="success"><?= $successMessage ?></p>
          <?php else : ?>

               <p>Please review the information and click to confirm.</p>
                 <h3>User Details:</h3>
                 <p>Name: <?php echo $_SESSION['registration_data']['name']; ?> </p>
                 <p>Address: <?php echo $_SESSION['registration_data']['address_flat'] . ", " . $_SESSION['registration_data']['address_street']. ", " .  $_SESSION['registration_data']['address_city'] . ", " . $_SESSION['registration_data']['address_country']; ?></p>
                 <p>Date of Birth: <?php echo $_SESSION['registration_data']['date_of_birth']; ?></p>
                <p>ID Number: <?php echo $_SESSION['registration_data']['id_number']; ?> </p>
                <p>Email: <?php echo $_SESSION['registration_data']['email']; ?> </p>
                 <p>Telephone: <?php echo $_SESSION['registration_data']['telephone']; ?> </p>
                 <p>Role: <?php echo $_SESSION['registration_data']['role']; ?> </p>
                 <p>Qualification: <?php echo $_SESSION['registration_data']['qualification']; ?> </p>
                 <p>Skills: <?php echo $_SESSION['registration_data']['skills']; ?> </p>
                <p>Username: <?php echo $_SESSION['registration_data']['username']; ?> </p>
               <form method="post">
                     <button type="submit">Confirm</button>
                     <?php if(isset($errors['db'])): ?>
                         <p class="error"><?= $errors['db'] ?></p>
                     <?php endif ?>
             </form>

       <?php endif ?>
    </div>

<footer>
    <p>© <?= date("Y") ?> Task Allocator Pro |
        Contact: <a href="mailto:contact@taskallocatorpro.com">contact@taskallocatorpro.com</a> |
        <a href="about.php">About Us</a></p>
</footer>
</body>
</html>
