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
$tasks = [];
$teamMembers = [];
$selectedTask = null;


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

//Handle Project Selection
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['project_id'])){
    $selectedProjectId = $_GET['project_id'];

    // Fetch tasks for the selected project, sorted by assignments
      try {
          $stmt = $pdo->prepare("SELECT t.task_id, t.name AS task_name, t.start_date, t.status, t.priority,
                                       CASE WHEN ta.user_id IS NULL THEN 0 ELSE 1 END AS has_team
                                      FROM Tasks t
                                      LEFT JOIN TeamAssignments ta ON t.task_id = ta.task_id
                                      WHERE t.project_id = :project_id
                                       ORDER BY has_team ASC");
          $stmt->execute([':project_id' => $selectedProjectId]);
          $tasks = $stmt->fetchAll();

      } catch (PDOException $e) {
          $errors['db'] = "There was a problem retrieving tasks.";
          echo $e->getMessage();
      }

}


// Handle Task Selection
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['task_id']) )
{

        $selectedTask = $_GET['task_id'];
        // Fetch all team members
        try {
          $stmt = $pdo->prepare("SELECT user_id, name FROM Users WHERE role = 'Team Member'");
          $stmt->execute();
          $teamMembers = $stmt->fetchAll();

        }  catch (PDOException $e) {
              $errors['db'] = "There was a problem retrieving team members.";
              echo $e->getMessage();
         }
}

// Handle Assignment of Team Members to Tasks
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assign_team_member']))
{
    $selectedTask = $_POST['task_id'];
    $teamMemberId = $_POST['team_member_id'];
    $role = $_POST['role'];
    $contributionPercentage = $_POST['contribution_percentage'];

    if (empty($teamMemberId) || empty($role) || empty($contributionPercentage)){
        $errors['assignment'] = "Team member, role, and contribution are mandatory.";
    }
       //validate contribution percentage
    if (!empty($contributionPercentage) && (!is_numeric($contributionPercentage) || $contributionPercentage <=0 || $contributionPercentage > 100)){
            $errors['contribution_percentage'] = "Contribution percentage must be numeric between 0 and 100.";
    }
    if (empty($errors)){

       try {

          // Begin Transaction
            $pdo->beginTransaction();
           $stmt = $pdo->prepare("INSERT INTO TeamAssignments (task_id, user_id, role, contribution_percentage, start_date) VALUES
                                        (:task_id, :user_id, :role, :contribution_percentage, CURDATE())");
            $stmt->execute([
               ':task_id' => $selectedTask,
               ':user_id' => $teamMemberId,
               ':role' => $role,
               ':contribution_percentage' => $contributionPercentage,
            ]);

              // Commit transaction
            $pdo->commit();

             $successMessage = "Team member successfully assigned to task.";
            // Refresh the page to show the changes
            header("Location: assign_team.php?project_id=" . $_POST['project_id'] . "&task_id=" . $selectedTask);
             exit();

       }   catch (PDOException $e) {
           $pdo->rollBack(); // Rollback transaction
            $errors['db'] =  "There was a problem assigning the task, please try again.";
          echo $e->getMessage();
       }

    }

    // Fetch all team members
      try {
          $stmt = $pdo->prepare("SELECT user_id, name FROM Users WHERE role = 'Team Member'");
          $stmt->execute();
          $teamMembers = $stmt->fetchAll();

        }  catch (PDOException $e) {
              $errors['db'] = "There was a problem retrieving team members.";
              echo $e->getMessage();
         }

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Team Members - Task Allocator Pro</title>
    <link rel="stylesheet" href="css/style.css">
  <style>
        .allocation-form {
            display: none;
        }

      .allocation-form.show {
           display: block;
        }
  </style>
</head>
<body>
    <header>
        <h1>Task Allocator Pro</h1>
           <a href="profile.php">User Profile</a>
           <a href="logout.php">Logout</a>
    </header>
    <main class="container">
        <h2>Assign Team Members to Tasks</h2>
            <p><a href="dashboard.php">Back to Dashboard</a></p>
         <?php if (!empty($successMessage)): ?>
           <p class="success"><?= $successMessage; ?></p>
         <?php endif; ?>
          <!-- Project Selection Section -->
            <form method="GET">
                    <label for="project_id">Select Project:</label>
                    <select id="project_id" name="project_id" onchange="this.form.submit()">
                         <option value="">Select a Project</option>
                         <?php foreach($activeProjects as $project): ?>
                              <option value="<?= $project['project_id'] ?>"  <?= (isset($selectedProjectId) && $selectedProjectId == $project['project_id'])? 'selected' : ''  ?> > <?= $project['title'] . ' - '. $project['project_id']  ?></option>
                         <?php endforeach; ?>
                   </select>
            </form>

            <?php if (!empty($tasks)): ?>
                  <table>
                      <thead>
                           <tr>
                                 <th>Task ID</th>
                                <th>Task Name</th>
                               <th>Start Date</th>
                               <th>Status</th>
                                <th>Priority</th>
                                <th>Action</th>
                           </tr>
                      </thead>
                      <tbody>
                         <?php foreach ($tasks as $task): ?>
                           <tr>
                               <td><?= $task['task_id']; ?></td>
                             <td><?= $task['task_name']; ?></td>
                              <td><?= $task['start_date']; ?></td>
                               <td><?= $task['status']; ?></td>
                             <td><?= $task['priority']; ?></td>
                                <td>
                                 <a href="assign_team.php?project_id=<?= $selectedProjectId ?>&task_id=<?= $task['task_id']; ?>">Assign Team Members</a>
                               </td>
                           </tr>
                         <?php endforeach; ?>
                   </tbody>
              </table>
            <?php  elseif(isset($selectedProjectId)) : ?>
                <p>No tasks in this project</p>
            <?php endif; ?>



     <?php if (!empty($teamMembers) && isset($selectedTask)) :?>
            <div id="allocation-form" class="allocation-form show">
                 <h3>Assign Team Members</h3>
                <form method="post">
                     <input type="hidden" name="task_id" value="<?= $selectedTask ?>">
                      <input type="hidden" name="project_id" value="<?= $selectedProjectId ?>">
                     <label for="team_member_id">Select Team Member:</label>
                        <select id="team_member_id" name="team_member_id" required>
                           <option value="">Select a Team Member</option>
                            <?php foreach($teamMembers as $member): ?>
                                  <option value="<?= $member['user_id']; ?>"> <?= $member['name'] . ' - ' . $member['user_id'];  ?></option>
                           <?php endforeach; ?>
                     </select>


                       <label for="role">Role:</label>
                           <select id="role" name="role" required>
                            <option value="Developer">Developer</option>
                            <option value="Designer">Designer</option>
                            <option value="Tester">Tester</option>
                            <option value="Analyst">Analyst</option>
                            <option value="Support">Support</option>
                      </select>


                      <label for="contribution_percentage">Contribution Percentage:</label>
                      <input type="number" name="contribution_percentage" id="contribution_percentage"  required>

                      <?php if(isset($errors['assignment'])): ?>
                          <p class="error"><?= $errors['assignment'] ?></p>
                      <?php endif ?>

                      <?php if(isset($errors['contribution_percentage'])): ?>
                           <p class="error"><?= $errors['contribution_percentage'] ?></p>
                       <?php endif ?>

                 <button type="submit" name="assign_team_member">Assign Team Member</button>
                     <?php if(isset($errors['db'])): ?>
                          <p class="error"><?= $errors['db'] ?></p>
                      <?php endif; ?>

               </form>
           </div>
      <?php endif; ?>
    </main>
      <footer>
        <p>© <?php echo date("Y"); ?> Task Allocator Pro |
        Contact: <a href="mailto:contact@taskallocatorpro.com">contact@taskallocatorpro.com</a> |
        <a href="about.php">About Us</a></p>
    </footer>
</body>
</html>