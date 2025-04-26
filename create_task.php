<?php
session_start();
require 'includes/dbconfig.in.php';
require 'includes/auth.php';

if (!isProjectLeader()) {
    header("Location: dashboard.php");
    exit();
}

$errors = [];
$successMessage = "";

// Fetch active projects managed by the logged-in Project Leader
try {
    $stmt = $pdo->prepare("SELECT p.project_id, p.title FROM Projects p
                             INNER JOIN ProjectTeamLeaders tl ON p.project_id = tl.project_id
                             INNER JOIN TeamAssignments ta ON tl.assignment_id = ta.assignment_id
                             WHERE ta.user_id = :user_id");

    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $activeProjects = $stmt->fetchAll();
} catch (PDOException $e) {
    $errors['db'] = "There was a problem retrieving your projects.";
        echo $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task_name = trim($_POST['task_name']);
    $description = trim($_POST['description']);
    $project_id = trim($_POST['project_id']);
    $start_date = trim($_POST['start_date']);
    $end_date = trim($_POST['end_date']);
    $effort = trim($_POST['effort']);
    $status = trim($_POST['status']);
    $priority = trim($_POST['priority']);

    // Validate Mandatory fields
    $requiredFields = ['task_name', 'description', 'project_id', 'start_date', 'end_date', 'effort', 'status', 'priority'];
    foreach ($requiredFields as $field) {
        if (empty(${$field})) {
            $errors[$field] = "This field is required.";
        }
    }

      //Date Validation
       //Date Validation
    if(!empty($start_date) && !empty($end_date))
    {
             //Get project start date
         $stmt = $pdo->prepare("SELECT start_date, end_date FROM Projects WHERE project_id = :project_id");
         $stmt->execute([':project_id' => $project_id]);
         $projectData = $stmt->fetch();

          if ($projectData) {
                 // The start date cannot be earlier than project start date
              if($start_date < $projectData['start_date']){
                 $errors['start_date'] = "The Start Date of the task cannot be earlier than the project's start date";
                }
                  //The end date cannot be later than project end date.
             if ($end_date > $projectData['end_date']){
                  $errors['end_date'] = "The End Date of the task cannot exceed the project's end date.";
             }

           }
     }
         //validate effort
         if (!empty($effort) && !is_numeric($effort) || $effort <=0)
         {
             $errors['effort'] = "Effort value must be a positive numeric value.";
         }


    if (empty($errors)) {
        try {
              $stmt = $pdo->prepare("INSERT INTO Tasks (name, description, project_id, start_date, end_date, effort, status, priority)
                                   VALUES (:name, :description, :project_id, :start_date, :end_date, :effort, :status, :priority)");
            $stmt->execute([
                ':name' => $task_name,
                ':description' => $description,
                ':project_id' => $project_id,
                ':start_date' => $start_date,
                ':end_date' => $end_date,
                ':effort' => $effort,
                ':status' => $status,
                ':priority' => $priority
            ]);

            $successMessage = "Task '$task_name' successfully created.";
        } catch (PDOException $e) {
            $errors['db'] = "There was a problem creating the task, please try again.";
          echo $e->getMessage();
        }
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Task - Task Allocator Pro</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Task Allocator Pro</h1>
           <a href="profile.php">User Profile</a>
            <a href="logout.php">Logout</a>
    </header>
    <main class="container">
        <h2>Create Task</h2>
          <p><a href="dashboard.php">Back to Dashboard</a></p>
        <?php if (!empty($successMessage)): ?>
             <p class="success"><?= $successMessage; ?></p>
        <?php endif; ?>

         <form method="post">
            <label for="task_name">Task Name:</label>
            <input type="text" id="task_name" name="task_name" value="<?= isset($task_name) ? $task_name : '' ?>"  required>
              <?php if (isset($errors['task_name'])): ?>
                <p class="error"><?= $errors['task_name'] ?></p>
            <?php endif; ?>

            <label for="description">Description:</label>
             <textarea id="description" name="description" required><?= isset($description) ? $description : '' ?></textarea>
            <?php if (isset($errors['description'])): ?>
                 <p class="error"><?= $errors['description'] ?></p>
           <?php endif; ?>


           <label for="project_id">Project:</label>
           <select id="project_id" name="project_id" required>
            <option value="">Select a Project</option>
                <?php foreach($activeProjects as $project): ?>
                     <option value="<?= $project['project_id']  ?>"  <?= (isset($project_id) && $project_id == $project['project_id'])? 'selected' : '' ?> > <?= $project['title'] . " - " . $project['project_id']?> </option>
                <?php endforeach; ?>
           </select>
           <?php if (isset($errors['project_id'])): ?>
               <p class="error"><?= $errors['project_id'] ?></p>
            <?php endif; ?>


            <label for="start_date">Start Date:</label>
           <input type="date" id="start_date" name="start_date" value="<?= isset($start_date) ? $start_date : '' ?>"  required>
             <?php if (isset($errors['start_date'])): ?>
                <p class="error"><?= $errors['start_date'] ?></p>
           <?php endif; ?>

           <label for="end_date">End Date:</label>
            <input type="date" id="end_date" name="end_date" value="<?= isset($end_date) ? $end_date : '' ?>"  required>
              <?php if (isset($errors['end_date'])): ?>
                 <p class="error"><?= $errors['end_date'] ?></p>
           <?php endif; ?>

           <label for="effort">Effort (man-months):</label>
           <input type="number" id="effort" name="effort" value="<?= isset($effort) ? $effort : '' ?>"  required>
            <?php if (isset($errors['effort'])): ?>
                 <p class="error"><?= $errors['effort'] ?></p>
           <?php endif; ?>

           <label for="status">Status:</label>
           <select id="status" name="status" required>
               <option value="Pending"  <?= (isset($status) && $status == 'Pending') ? 'selected' : '' ?>>Pending</option>
              <option value="In Progress"  <?= (isset($status) && $status == 'In Progress') ? 'selected' : '' ?>>In Progress</option>
              <option value="Completed"  <?= (isset($status) && $status == 'Completed') ? 'selected' : '' ?>>Completed</option>
           </select>
            <?php if (isset($errors['status'])): ?>
                 <p class="error"><?= $errors['status'] ?></p>
            <?php endif; ?>

           <label for="priority">Priority:</label>
           <select id="priority" name="priority" required>
              <option value="Low"  <?= (isset($priority) && $priority == 'Low') ? 'selected' : '' ?>>Low</option>
               <option value="Medium"  <?= (isset($priority) && $priority == 'Medium') ? 'selected' : '' ?>>Medium</option>
               <option value="High"  <?= (isset($priority) && $priority == 'High') ? 'selected' : '' ?>>High</option>
           </select>
            <?php if (isset($errors['priority'])): ?>
                  <p class="error"><?= $errors['priority'] ?></p>
            <?php endif; ?>

           <button type="submit">Create Task</button>
             <?php if (isset($errors['db'])): ?>
                 <p class="error"><?= $errors['db'] ?></p>
             <?php endif; ?>
        </form>
    </main>
    <footer>
        <p>© <?php echo date("Y"); ?> Task Allocator Pro |
        Contact: <a href="mailto:contact@taskallocatorpro.com">contact@taskallocatorpro.com</a> |
        <a href="about.php">About Us</a></p>
    </footer>
</body>
</html>