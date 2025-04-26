<?php
session_start();
require 'includes/dbconfig.in.php';
require 'includes/auth.php';

if (!isTeamMember()) {
    header("Location: dashboard.php");
    exit();
}

$errors = [];
$successMessage = "";
$tasks = [];


try {
    $stmt = $pdo->prepare("SELECT t.task_id, t.name AS task_name, p.title as project_name, ta.start_date FROM Tasks t
                            INNER JOIN TeamAssignments ta ON t.task_id = ta.task_id
                            INNER JOIN Projects p ON t.project_id = p.project_id
                            WHERE ta.user_id = :user_id");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $tasks = $stmt->fetchAll();
} catch (PDOException $e) {
    $errors['db'] = "There was a problem retrieving your tasks.";
        echo $e->getMessage();
}


if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['task_id']) && isset($_GET['action']))
{
      $taskId = trim($_GET['task_id']);
      $action = trim($_GET['action']);

     try {
         // Begin Transaction
           $pdo->beginTransaction();

       if($action == 'accept')
          {
            $stmt = $pdo->prepare("UPDATE Tasks set status = 'In Progress'
            WHERE task_id = :task_id");
             $stmt->execute([':task_id' => $taskId]);

            $successMessage = "Task successfully accepted.";

         } else if ($action == 'reject') {

             $stmt = $pdo->prepare("DELETE FROM TeamAssignments WHERE task_id = :task_id AND user_id = :user_id");
              $stmt->execute([
                 ':task_id' => $taskId,
                ':user_id' => $_SESSION['user_id']

             ]);

          $successMessage = "Task assignment successfully rejected.";
         }

        // Commit transaction
        $pdo->commit();
          header("Location: accept_task.php");
            exit();

        } catch (PDOException $e) {
             $pdo->rollBack(); // Rollback transaction
            $errors['db'] =  "There was a problem accepting the task, please try again.";
           echo $e->getMessage();
        }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Accept Task - Task Allocator Pro</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
        <h1>Task Allocator Pro</h1>
    <a href="profile.php">User Profile</a>
    <a href="logout.php">Logout</a>
</header>
    <main class="container">
        <h2>Accept Task Assignments</h2>
         <p><a href="dashboard.php">Back to Dashboard</a></p>

           <?php if (!empty($successMessage)): ?>
               <p class="success"><?= $successMessage; ?></p>
            <?php endif; ?>

          <?php if (!empty($tasks)): ?>
                  <table>
                     <thead>
                           <tr>
                               <th>Task ID</th>
                                <th>Task Name</th>
                               <th>Project Name</th>
                                <th>Start Date</th>
                              <th>Action</th>
                           </tr>
                      </thead>
                      <tbody>
                         <?php foreach ($tasks as $task): ?>
                            <tr>
                              <td><?= $task['task_id']; ?></td>
                               <td><?= $task['task_name']; ?></td>
                               <td><?= $task['project_name']; ?></td>
                               <td><?= $task['start_date']; ?></td>
                               <td>
                                  <a href="accept_task.php?task_id=<?= $task['task_id']; ?>&action=accept">Accept</a> |
                                <a href="accept_task.php?task_id=<?= $task['task_id']; ?>&action=reject">Reject</a>
                            </td>
                            </tr>
                           <?php endforeach; ?>
                     </tbody>
                  </table>

             <?php else : ?>
                 <p>No task assignments at this time.</p>
             <?php endif; ?>

    </main>
    <footer>
        <p>© <?php echo date("Y"); ?> Task Allocator Pro |
        Contact: <a href="mailto:contact@taskallocatorpro.com">contact@taskallocatorpro.com</a> |
        <a href="about.php">About Us</a></p>
    </footer>
</body>
</html>