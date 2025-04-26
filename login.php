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
    $username = $_POST['username'];
    $password = $_POST['password'];


    if(empty($username) || empty($password)) {
        $errors['auth'] = "Please enter your username and password.";

    } else {
        try {
        $stmt = $pdo->prepare("SELECT user_id, username, password_hash, role FROM Users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

         if ($user && password_verify($password, $user['password_hash'])) {
             $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
             header("Location: dashboard.php");
             exit();
          } else{
              $errors['auth'] = "Invalid username or password.";
          }

        }
        catch(PDOException $e)
        {
            $errors['auth'] =  "There was a problem logging you in, please try again.";
         }
    }


}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Task Allocator Pro</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
        <h1>Task Allocator Pro</h1>
</header>
    <main class="container">
        <h2>Login</h2>
        <?php if(isset($errors['auth'])): ?>
            <p class="error"><?= $errors['auth'] ?></p>
        <?php endif ?>
        <form method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
         <p>Don't have an account? <a href="signup.php">Sign up</a></p>
         
      
             <p>Username: Abd123, password: Abd12301230 , role: manager </p>
              <p>Username: Ali333, password: Ali12301230 , role: project leader </p>
              <p>Username: User11, password: U112301230, role: team member</p>
           <p>Username: User22, password: U212301230, role: team member</p>
            <p>Username: User33, password: U312301230, role: team member</p>
         </ul>
         
    </main>
    <footer>
        <p>© <?php echo date("Y"); ?> Task Allocator Pro |
        Contact: <a href="mailto:contact@taskallocatorpro.com">contact@taskallocatorpro.com</a> |
        <a href="about.php">About Us</a></p>
    </footer>
</body>
</html>