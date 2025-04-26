<?php
session_start();
require 'includes/dbconfig.in.php';
require 'includes/auth.php';

if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['registration_data'] = array_map('trim', $_POST);

    // Required fields validation for step 1
    $requiredFields1 = ['name', 'address_flat', 'address_street', 'address_city', 'address_country', 'date_of_birth', 'id_number', 'email', 'telephone', 'role', 'qualification', 'skills'];
    foreach($requiredFields1 as $field) {
        if(empty($_SESSION['registration_data'][$field])) {
           $errors[$field] = "This field is required";
        }
    }
    // date format
    $date_regex = '/^\d{4}-\d{2}-\d{2}$/';
    if (!preg_match($date_regex, $_SESSION['registration_data']['date_of_birth'])){
        $errors["date_of_birth"] = "Invalid date format";
    }
    // Validate email format
    if(!empty($_SESSION['registration_data']['email']) && !filter_var($_SESSION['registration_data']['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email address";
    }
    // Validate ID number is numeric and has 10 chars
    if(!empty($_SESSION['registration_data']['id_number']) && (!is_numeric($_SESSION['registration_data']['id_number']) || strlen($_SESSION['registration_data']['id_number']) != 10 )) {
        $errors['id_number'] = "ID number must be numeric with 10 chars.";
    }

    // Check if there are no errors move to the next step
    if (empty($errors)) {
        $_SESSION['registration_step'] = 2;
         header("Location: signup-step2.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up - Step 1 - Task Allocator Pro</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Task Allocator Pro</h1>
    </header>
    <main class="container">
        <h2>User Registration - Step 1</h2>
        <form method="post">
            <!-- Step 1 User Information -->
            <h3>User Information</h3>
            <label for="name">Full Name:</label>
            <input type="text" id="name" name="name" value="<?= isset($_SESSION['registration_data']['name']) ? $_SESSION['registration_data']['name'] : '' ?>"  required>
            <?php if(isset($errors['name'])): ?>
                <p class="error"><?= $errors['name'] ?></p>
                <?php endif ?>

            <label for="address_flat">Flat/House No:</label>
            <input type="text" id="address_flat" name="address_flat" value="<?= isset($_SESSION['registration_data']['address_flat']) ? $_SESSION['registration_data']['address_flat'] : '' ?>" required>
            <?php if(isset($errors['address_flat'])): ?>
                 <p class="error"><?= $errors['address_flat'] ?></p>
            <?php endif ?>

           <label for="address_street">Street:</label>
            <input type="text" id="address_street" name="address_street" value="<?= isset($_SESSION['registration_data']['address_street']) ? $_SESSION['registration_data']['address_street'] : '' ?>"  required>
            <?php if(isset($errors['address_street'])): ?>
                  <p class="error"><?= $errors['address_street'] ?></p>
            <?php endif ?>

            <label for="address_city">City:</label>
            <input type="text" id="address_city" name="address_city" value="<?= isset($_SESSION['registration_data']['address_city']) ? $_SESSION['registration_data']['address_city'] : '' ?>" required>
            <?php if(isset($errors['address_city'])): ?>
                <p class="error"><?= $errors['address_city'] ?></p>
                <?php endif ?>

             <label for="address_country">Country:</label>
            <input type="text" id="address_country" name="address_country" value="<?= isset($_SESSION['registration_data']['address_country']) ? $_SESSION['registration_data']['address_country'] : '' ?>" required>
              <?php if(isset($errors['address_country'])): ?>
                  <p class="error"><?= $errors['address_country'] ?></p>
                <?php endif ?>


            <label for="date_of_birth">Date of Birth:</label>
            <input type="date" id="date_of_birth" name="date_of_birth" value="<?= isset($_SESSION['registration_data']['date_of_birth']) ? $_SESSION['registration_data']['date_of_birth'] : '' ?>" required>
            <?php if(isset($errors['date_of_birth'])): ?>
                 <p class="error"><?= $errors['date_of_birth'] ?></p>
                <?php endif ?>

            <label for="id_number">ID Number:</label>
            <input type="text" id="id_number" name="id_number" value="<?= isset($_SESSION['registration_data']['id_number']) ? $_SESSION['registration_data']['id_number'] : '' ?>" required>
              <?php if(isset($errors['id_number'])): ?>
                  <p class="error"><?= $errors['id_number'] ?></p>
                  <?php endif ?>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= isset($_SESSION['registration_data']['email']) ? $_SESSION['registration_data']['email'] : '' ?>"  required>
              <?php if(isset($errors['email'])): ?>
                   <p class="error"><?= $errors['email'] ?></p>
                <?php endif ?>


            <label for="telephone">Telephone:</label>
            <input type="text" id="telephone" name="telephone" value="<?= isset($_SESSION['registration_data']['telephone']) ? $_SESSION['registration_data']['telephone'] : '' ?>" required>
              <?php if(isset($errors['telephone'])): ?>
                    <p class="error"><?= $errors['telephone'] ?></p>
                <?php endif ?>

            <label for="role">Role:</label>
            <select id="role" name="role" required>
                 <option value="Manager" <?= (isset($_SESSION['registration_data']['role']) && $_SESSION['registration_data']['role'] == 'Manager') ? 'selected' : ''  ?>>Manager</option>
                <option value="Project Leader" <?= (isset($_SESSION['registration_data']['role']) && $_SESSION['registration_data']['role'] == 'Project Leader') ? 'selected' : ''  ?>>Project Leader</option>
                <option value="Team Member"  <?= (isset($_SESSION['registration_data']['role']) && $_SESSION['registration_data']['role'] == 'Team Member') ? 'selected' : ''  ?>>Team Member</option>
            </select>
            <?php if(isset($errors['role'])): ?>
                 <p class="error"><?= $errors['role'] ?></p>
                 <?php endif ?>


            <label for="qualification">Qualification:</label>
            <input type="text" id="qualification" name="qualification" value="<?= isset($_SESSION['registration_data']['qualification']) ? $_SESSION['registration_data']['qualification'] : '' ?>" required>
            <?php if(isset($errors['qualification'])): ?>
                 <p class="error"><?= $errors['qualification'] ?></p>
                <?php endif ?>


            <label for="skills">Skills:</label>
            <textarea id="skills" name="skills" required><?= isset($_SESSION['registration_data']['skills']) ? $_SESSION['registration_data']['skills'] : '' ?> </textarea>
            <?php if(isset($errors['skills'])): ?>
                <p class="error"><?= $errors['skills'] ?></p>
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