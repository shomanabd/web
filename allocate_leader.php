<?php
session_start();
require 'includes/dbconfig.in.php';
require 'includes/auth.php';

if (!isAdmin()) {
    header("Location: dashboard.php");
    exit();
}

$errors = [];
$successMessage = "";
$showAllocationForm = false;
$selectedProjectId = null;

// Fetch unallocated projects
try {
    $stmt = $pdo->prepare("SELECT project_id, title, start_date, end_date FROM Projects WHERE project_id NOT IN (SELECT project_id FROM ProjectTeamLeaders)");
    $stmt->execute();
    $unallocatedProjects = $stmt->fetchAll();
} catch (PDOException $e) {
    $errors['db'] = "There was a problem retrieving project data.";
    echo $e->getMessage();
}

// Handle Allocation Form
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['project_id']) ) {
   $showAllocationForm = true;
   $selectedProjectId = $_GET['project_id'];

} else if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['allocate_leader'])) {
    $project_id = trim($_POST['project_id']);
    $team_leader_id = trim($_POST['team_leader_id']);
    $selectedProjectId = $project_id;
     $showAllocationForm = true;
      if(empty($team_leader_id)){
         $errors['team_leader_id'] = "Team Leader field is required";
      }

    if(empty($errors)){
         try {

             // Begin Transaction
            $pdo->beginTransaction();

            //Get the team leader role
            $stmt = $pdo->prepare("SELECT role FROM Users WHERE user_id = :team_leader_id");
            $stmt->execute([':team_leader_id' => $team_leader_id]);
            $teamLeaderData = $stmt->fetch();

            if ($teamLeaderData && $teamLeaderData['role'] == 'Project Leader')
            {
                 // Insert a new entry into TeamAssignments to define the team leader as the manager of this project
                $stmt = $pdo->prepare("INSERT INTO TeamAssignments (task_id, user_id, role, contribution_percentage, start_date) VALUES
                 (NULL, :team_leader_id, 'Project Leader', 100.0, CURDATE() )");

                   $stmt->execute([':team_leader_id' => $team_leader_id]);
                   //Get the id that was inserted
                 $lastId =  $pdo->lastInsertId();


              // Insert team leader into project relation
                 $stmt = $pdo->prepare("INSERT INTO ProjectTeamLeaders (project_id, team_leader_id, assignment_id) VALUES (:project_id, :team_leader_id, :assignment_id)");
                 $stmt->execute([
                    ':project_id' => $project_id,
                    ':team_leader_id' => $team_leader_id,
                    ':assignment_id' => $lastId
                  ]);

            // Commit transaction
            $pdo->commit();
                 $successMessage = "Team Leader successfully allocated to Project  $project_id.";
                 $showAllocationForm = false;
            } else {
                $errors['team_leader_id'] = "The team member must have a role of Project Leader.";
            }

         }  catch (PDOException $e) {
             $pdo->rollBack(); // Rollback transaction
                $errors['db'] = "There was a problem allocating the leader, please try again.";
               echo $e->getMessage();
         }
    }
}

// Fetch All Project leaders
try {
    $stmt = $pdo->prepare("SELECT user_id, name FROM Users WHERE role = 'Project Leader'");
    $stmt->execute();
    $teamLeaders = $stmt->fetchAll();
} catch (PDOException $e) {
    $errors['db'] = "There was a problem retrieving the list of team leaders.";
        echo $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Allocate Team Leader - Task Allocator Pro</title>
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
        <h2>Allocate Team Leader to Project</h2>
         <p><a href="dashboard.php">Back to Dashboard</a></p>

        <?php if (!empty($successMessage)): ?>
           <p class="success"><?= $successMessage; ?></p>
       <?php endif; ?>

          <?php if (!empty($unallocatedProjects)): ?>
                  <table>
                      <thead>
                           <tr>
                                 <th>Project ID</th>
                                <th>Project Title</th>
                               <th>Start Date</th>
                               <th>End Date</th>
                                <th>Action</th>
                           </tr>
                      </thead>
                      <tbody>
                           <?php foreach ($unallocatedProjects as $project): ?>
                               <tr>
                                 <td><?= $project['project_id']; ?></td>
                                  <td><?= $project['title']; ?></td>
                                  <td><?= $project['start_date']; ?></td>
                                  <td><?= $project['end_date']; ?></td>
                                   <td>
                                        <a href="allocate_leader.php?project_id=<?= $project['project_id']; ?>">Allocate Team Leader</a>
                                   </td>
                               </tr>
                           <?php endforeach; ?>
                     </tbody>
                  </table>

             <?php else : ?>
                 <p>No unassigned projects at this time.</p>
             <?php endif; ?>


          <div id="allocation-form"  class="allocation-form <?php if ($showAllocationForm) echo 'show'; ?>">
            <h3>Allocate Team Leader</h3>
            <form method="post">
               <input type="hidden" name="project_id" value="<?= $selectedProjectId ?>">
                <label for="team_leader_id">Select Team Leader:</label>
                <select id="team_leader_id" name="team_leader_id">
                    <option value="">Select a Leader</option>
                        <?php foreach($teamLeaders as $leader): ?>
                              <option value="<?= $leader['user_id'] ?>"> <?= $leader['name'] . ' - ' . $leader['user_id']?> </option>
                        <?php endforeach; ?>
                 </select>
                  <?php if(isset($errors['team_leader_id'])): ?>
                        <p class="error"><?= $errors['team_leader_id'] ?></p>
                     <?php endif ?>
                <button type="submit" name="allocate_leader">Confirm Allocation</button>
                <?php if(isset($errors['db'])): ?>
                     <p class="error"><?= $errors['db'] ?></p>
                <?php endif ?>
            </form>
        </div>
    </main>
    <footer>
        <p>© <?php echo date("Y"); ?> Task Allocator Pro |
        Contact: <a href="mailto:contact@taskallocatorpro.com">contact@taskallocatorpro.com</a> |
        <a href="about.php">About Us</a></p>
    </footer>

</body>
</html>