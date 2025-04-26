<?php
session_start();
require 'includes/dbconfig.in.php';
require 'includes/auth.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$errors = [];
$successMessage = "";
$searchResults = [];
$searchFilters = [];
$whereClauses = [];
$queryParams = [];


// Handle Search Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['search_tasks'])) {
    $searchFilters = array_map('trim', $_GET);
    //Construct where clauses based on user role.
    if(isAdmin())
    {
        //Manager can see all tasks, nothing to do.
    }else if (isProjectLeader()){
        //team leaders can see tasks within the projects that they manage.
           $whereClauses[] = " t.project_id IN ( SELECT p.project_id FROM Projects p
                              INNER JOIN ProjectTeamLeaders tl ON p.project_id = tl.project_id
                              INNER JOIN TeamAssignments ta ON tl.assignment_id = ta.assignment_id
                               WHERE ta.user_id = :user_id)";
            $queryParams[':user_id'] = $_SESSION['user_id'];

    }else if(isTeamMember()){
        //team members can search within the task that they are assigned to.
          $whereClauses[] = " t.task_id IN ( SELECT task_id from TeamAssignments WHERE user_id = :user_id)";
          $queryParams[':user_id'] = $_SESSION['user_id'];

    }

    // Construct WHERE clause based on user input
        if(!empty($searchFilters['task_id'])) {
            $whereClauses[] = " t.task_id = :task_id";
            $queryParams[':task_id'] = $searchFilters['task_id'];
        }

        if (!empty($searchFilters['task_name'])) {
             $whereClauses[] = " t.name LIKE :task_name";
            $queryParams[':task_name'] = '%' . $searchFilters['task_name'] . '%';
        }

      if (!empty($searchFilters['project_id'])) {
            $whereClauses[] = " t.project_id = :project_id";
             $queryParams[':project_id'] = $searchFilters['project_id'];
      }

      if (!empty($searchFilters['priority'])) {
             $whereClauses[] = " t.priority = :priority";
              $queryParams[':priority'] = $searchFilters['priority'];
      }

        if(!empty($searchFilters['status'])){
             $whereClauses[] = "t.status = :status";
            $queryParams[':status'] = $searchFilters['status'];
       }

        if(!empty($searchFilters['start_date']) && !empty($searchFilters['end_date'])){
             $whereClauses[] = "t.start_date >= :start_date AND t.end_date <= :end_date";
              $queryParams[':start_date'] = $searchFilters['start_date'];
             $queryParams[':end_date'] = $searchFilters['end_date'];
         }

    // Construct full SQL query
    $sql = "SELECT t.task_id, t.name AS task_name, p.title as project_name, t.status, t.priority, t.start_date, t.end_date,
                      (SELECT SUM(contribution_percentage) FROM TeamAssignments WHERE task_id = t.task_id) as progress
            FROM Tasks t
             INNER JOIN Projects p ON t.project_id = p.project_id";

    if (!empty($whereClauses))
    {
        $sql .= " WHERE " . implode(" AND ", $whereClauses);
    }


      $sql .= " ORDER BY t.task_id ";
    // Execute the query
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($queryParams);
        $searchResults = $stmt->fetchAll();
    } catch (PDOException $e) {
        $errors['db'] = "There was a problem fetching task details. Please try again";
       echo $e->getMessage();
    }
}
//Fetch all projects for project dropdown
try {
        $stmt = $pdo->prepare("SELECT project_id, title FROM Projects");
        $stmt->execute();
        $allProjects = $stmt->fetchAll();
    } catch (PDOException $e) {
        $errors['db'] = "There was a problem fetching projects.";
            echo $e->getMessage();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Tasks - Task Allocator Pro</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
        <h1>Task Allocator Pro</h1>
    <a href="profile.php">User Profile</a>
    <a href="logout.php">Logout</a>
</header>
    <main class="container">
        <h2>Search Tasks</h2>
           <p><a href="dashboard.php">Back to Dashboard</a></p>

        <form method="GET">
          <label for="task_id">Task ID:</label>
          <input type="text" id="task_id" name="task_id" value="<?= isset($searchFilters['task_id']) ? $searchFilters['task_id'] : ''; ?>">


           <label for="task_name">Task Name:</label>
          <input type="text" id="task_name" name="task_name"  value="<?= isset($searchFilters['task_name']) ? $searchFilters['task_name'] : ''; ?>">

          <label for="project_id">Project:</label>
          <select id="project_id" name="project_id" >
             <option value="">Select a Project</option>
               <?php foreach ($allProjects as $project): ?>
                   <option value="<?= $project['project_id']; ?>" <?= (isset($searchFilters['project_id']) && $searchFilters['project_id'] == $project['project_id']) ? 'selected' : '' ?>>
                        <?= $project['title']  . ' - '. $project['project_id'] ?>
                    </option>
                <?php endforeach; ?>
        </select>


            <label for="status">Status:</label>
            <select id="status" name="status">
                <option value="">Select Status</option>
                 <option value="Pending" <?= (isset($searchFilters['status']) && $searchFilters['status'] == 'Pending') ? 'selected' : '' ?>>Pending</option>
                <option value="In Progress" <?= (isset($searchFilters['status']) && $searchFilters['status'] == 'In Progress') ? 'selected' : '' ?>>In Progress</option>
                <option value="Completed" <?= (isset($searchFilters['status']) && $searchFilters['status'] == 'Completed') ? 'selected' : '' ?>>Completed</option>
            </select>

         <label for="priority">Priority:</label>
        <select id="priority" name="priority">
                <option value="">Select Priority</option>
                <option value="Low"  <?= (isset($searchFilters['priority']) && $searchFilters['priority'] == 'Low') ? 'selected' : '' ?>>Low</option>
                 <option value="Medium" <?= (isset($searchFilters['priority']) && $searchFilters['priority'] == 'Medium') ? 'selected' : '' ?>>Medium</option>
                <option value="High" <?= (isset($searchFilters['priority']) && $searchFilters['priority'] == 'High') ? 'selected' : '' ?>>High</option>
            </select>

             <label for="start_date">Start Date:</label>
              <input type="date" id="start_date" name="start_date" value="<?= isset($searchFilters['start_date']) ? $searchFilters['start_date'] : ''; ?>">

           <label for="end_date">End Date:</label>
          <input type="date" id="end_date" name="end_date" value="<?= isset($searchFilters['end_date']) ? $searchFilters['end_date'] : ''; ?>">


            <button type="submit" name="search_tasks">Search</button>
             <?php if(isset($errors['db'])): ?>
                <p class="error"><?= $errors['db'] ?></p>
            <?php endif ?>
        </form>

           <?php if (!empty($searchResults)): ?>
                <table>
                    <thead>
                        <tr>
                             <th>Task ID</th>
                            <th>Task Name</th>
                            <th>Project Name</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Start Date</th>
                            <th>Due Date</th>
                            <th>Progress</th>
                             <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                          <?php foreach ($searchResults as $task): ?>
                             <tr>
                                  <td><?= $task['task_id']; ?></td>
                                <td><?= $task['task_name']; ?></td>
                                 <td><?= $task['project_name']; ?></td>
                                <td><?= $task['status']; ?></td>
                               <td><?= $task['priority']; ?></td>
                                 <td><?= $task['start_date']; ?></td>
                              <td><?= $task['end_date']; ?></td>
                                 <td><?=  $task['progress'] === NULL ? '0%' :  $task['progress'] . "%" ;?> </td>
                                 <td>
                                      <a href="task_details.php?task_id=<?= $task['task_id']; ?>">View Details</a>
                                      <a href="task_update.php?task_id=<?= $task['task_id']; ?>">Update</a>
                                 </td>
                             </tr>
                         <?php endforeach; ?>
                    </tbody>
                </table>
           <?php elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['search_tasks'])) :?>
                <p>No tasks found matching your criteria.</p>
            <?php endif; ?>

    </main>
    <footer>
        <p>© <?php echo date("Y"); ?> Task Allocator Pro |
        Contact: <a href="mailto:contact@taskallocatorpro.com">contact@taskallocatorpro.com</a> |
        <a href="about.php">About Us</a></p>
    </footer>
</body>
</html>