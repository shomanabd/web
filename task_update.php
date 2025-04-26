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
$taskDetails = [];
$taskId = isset($_GET['task_id']) ? trim($_GET['task_id']) : null;


if (!$taskId){
    $errors['taskId'] = "Task ID is missing";
}else{
    // Fetch task details for the selected task
    try {
        $stmt = $pdo->prepare("SELECT t.task_id, t.name AS task_name, p.title as project_name, t.status, t.start_date, t.end_date, t.effort
                              ,  (SELECT SUM(contribution_percentage) FROM TeamAssignments WHERE task_id = t.task_id) as progress
                             FROM Tasks t
                               INNER JOIN Projects p ON t.project_id = p.project_id
                               WHERE t.task_id = :task_id AND
                                    t.task_id IN (SELECT task_id FROM TeamAssignments WHERE user_id = :user_id)");
        $stmt->execute([':task_id' => $taskId, ':user_id' => $_SESSION['user_id']]);
        $taskDetails = $stmt->fetch();


           if(!$taskDetails) {
              $errors['taskId'] = "Task not found or not assigned to you.";
            }
    } catch (PDOException $e) {
        $errors['db'] = "There was a problem fetching task details.";
        echo $e->getMessage();
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($taskDetails) ) {
   $current_progress = trim($_POST['current_progress']);
   $current_status = trim($_POST['current_status']);
    // Validate Mandatory fields
    $requiredFields = [ 'current_progress', 'current_status'];
    foreach ($requiredFields as $field) {
         if(empty(${$field})){
            $errors[$field] = "This field is required.";
         }
    }
   if (empty($errors)){
      try {
           // Begin Transaction
            $pdo->beginTransaction();

         $stmt = $pdo->prepare("INSERT INTO TaskProgress (task_id, user_id, progress_percentage, status) VALUES
                                   (:task_id, :user_id, :progress_percentage, :status)");

           $stmt->execute([
              ':task_id' => $taskId,
              ':user_id' =>  $_SESSION['user_id'],
                ':progress_percentage' => $current_progress,
               ':status' => $current_status
           ]);

              // Update the status on the tasks table as well.
          if ($current_progress == 100){
              $stmt = $pdo->prepare("UPDATE Tasks SET status = 'Completed' WHERE task_id = :task_id");
               $stmt->execute([
                   ':task_id' => $taskId
               ]);
           } else if ($current_progress > 0 && $current_status == 'In Progress'){
                 $stmt = $pdo->prepare("UPDATE Tasks SET status = 'In Progress' WHERE task_id = :task_id");
               $stmt->execute([
                   ':task_id' => $taskId
               ]);
           } else if ($current_progress == 0 && $current_status == 'Pending'){
                   $stmt = $pdo->prepare("UPDATE Tasks SET status = 'Pending' WHERE task_id = :task_id");
                    $stmt->execute([
                   ':task_id' => $taskId
               ]);
          }

           // Commit transaction
            $pdo->commit();

            $successMessage = "Task updated successfully.";
      }  catch (PDOException $e) {
        $pdo->rollBack(); // Rollback transaction
           $errors['db'] =  "There was a problem updating the task, please try again.";
           echo $e->getMessage();
      }
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Task - Task Allocator Pro</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
        <h1>Task Allocator Pro</h1>
           <a href="profile.php">User Profile</a>
           <a href="logout.php">Logout</a>
</header>
    <div class="container">
        <h2>Update Task</h2>
         <p><a href="dashboard.php">Back to Dashboard</a></p>

        <?php if (!empty($successMessage)): ?>
            <p class="success"><?= $successMessage; ?></p>
        <?php endif; ?>
      <?php if (!empty($errors['taskId'])): ?>
              <p class="error"><?= $errors['taskId'] ?></p>
       <?php else: ?>
       <?php if (!empty($taskDetails)): ?>
             <h3>Task Details</h3>
           <p>Task Name: <?=  $taskDetails['task_name'] ?></p>
          <p>Project: <?=  $taskDetails['project_name'] ?></p>
          <p>Start Date: <?=  $taskDetails['start_date'] ?></p>
             <p>End Date: <?=  $taskDetails['end_date'] ?></p>
         <p>Total Effort: <?=  $taskDetails['effort'] ?></p>
              <p>Progress : <?= $taskDetails['progress'] === NULL ? '0' : $taskDetails['progress'] ?>%</p>
           <form method="post">
            <label for="current_progress">Progress: <span ><?=  $taskDetails['progress'] === NULL ? '0' : $taskDetails['progress'] ?>%</span></label>
            <input type="range" id="current_progress" name="current_progress" min="0" max="100" value="<?=  $taskDetails['progress'] === NULL ? '0' : $taskDetails['progress'] ?>" >

              <?php if(isset($errors['current_progress'])): ?>
                 <p class="error"><?= $errors['current_progress'] ?></p>
           <?php endif; ?>
            <label for="current_status">Status:</label>
            <select id="current_status" name="current_status">
               <option value="Pending" <?= (isset($taskDetails['status']) && $taskDetails['status'] == 'Pending') ? 'selected' : ''  ?>>Pending</option>
               <option value="In Progress" <?= (isset($taskDetails['status']) && $taskDetails['status'] == 'In Progress') ? 'selected' : ''  ?>>In Progress</option>
                <option value="Completed"  <?= (isset($taskDetails['status']) && $taskDetails['status'] == 'Completed') ? 'selected' : ''  ?>>Completed</option>
            </select>
             <?php if(isset($errors['current_status'])): ?>
                <p class="error"><?= $errors['current_status'] ?></p>
           <?php endif; ?>
              <button type="submit">Update Task</button>
              <?php if(isset($errors['db'])): ?>
                    <p class="error"><?= $errors['db'] ?></p>
               <?php endif; ?>
        </form>
     <?php endif; ?>
    <?php endif ?>

    </div>
    <footer>
        <p>© <?php echo date("Y"); ?> Task Allocator Pro</p>
    </footer>

</body>
</html>