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
$fileInputsCount = isset($_POST['file_inputs_count']) ? intval($_POST['file_inputs_count']) : 1; // Initialize to at least 1

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $project_id = trim($_POST['project_id']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $customer_name = trim($_POST['customer_name']);
    $total_budget = trim($_POST['total_budget']);
    $start_date = trim($_POST['start_date']);
    $end_date = trim($_POST['end_date']);


      // Validate mandatory fields
      $requiredFields = ['project_id', 'title', 'description', 'customer_name', 'total_budget', 'start_date', 'end_date'];
      foreach ($requiredFields as $field) {
            if(empty(${$field})){
                $errors[$field] = "This field is required.";
            }
      }
        // Project Id must be in the right format
        $id_regex = '/^[A-Z]{4}-\d{5}$/';
        if(!empty($project_id) && !preg_match($id_regex, $project_id)){
            $errors['project_id'] = "Project ID must be 4 uppercase chars followed by a dash and 5 digits";
        }

          // Date validation : End date must be after the start date
         if(!empty($start_date) && !empty($end_date) && $end_date <= $start_date){
            $errors['end_date'] = "End date must be after the start date";
        }

         if(!empty($total_budget) && !is_numeric($total_budget) || $total_budget < 0){
            $errors['total_budget'] = "Budget must be a positive numeric value";
         }

    if (empty($errors)) {
        try {
                // Begin transaction
             $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO Projects (project_id, title, description, customer_name, total_budget, start_date, end_date)
                                  VALUES (:project_id, :title, :description, :customer_name, :total_budget, :start_date, :end_date)");

            $stmt->execute([
                ':project_id' => $project_id,
                ':title' => $title,
                ':description' => $description,
                ':customer_name' => $customer_name,
                ':total_budget' => $total_budget,
                ':start_date' => $start_date,
                ':end_date' => $end_date
             ]);


              if(isset($_FILES['supporting_docs']) && is_array($_FILES['supporting_docs']['name']))
              {
                 $fileCount = count($_FILES['supporting_docs']['name']);
                 if($fileCount > 3) {
                       $errors['files'] = "You can upload only three files.";
                 }
                 for($i=0; $i < $fileCount; $i++){
                   $fileName = $_FILES['supporting_docs']['name'][$i];
                   $fileTmpPath = $_FILES['supporting_docs']['tmp_name'][$i];
                   $fileSize = $_FILES['supporting_docs']['size'][$i];
                   $fileError = $_FILES['supporting_docs']['error'][$i];
                    $fileTitle = $_POST['document_title'][$i];

                    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                     //allowed extensions
                    $allowedExt = ['pdf', 'docx', 'png', 'jpg'];
                  //check file extention
                    if(!in_array($fileExt, $allowedExt)){
                            $errors['files'] = "File must be of type (pdf, docx, png, jpg).";
                        break;
                    }
                   //validate file size
                    if($fileSize > 2 * 1024 * 1024) {
                       $errors['files'] = "Max file size allowed is 2MB.";
                         break;
                    }
                    if($fileError === 0){
                      $destination = 'images/' . uniqid() . '_' . $fileName;
                       if(move_uploaded_file($fileTmpPath, $destination)){
                               $stmt = $pdo->prepare("INSERT INTO Documents (project_id, title, file_path) VALUES (:project_id, :title, :file_path)");
                               $stmt->execute([
                                    ':project_id' => $project_id,
                                    ':title' => $fileTitle,
                                    ':file_path' => $destination,
                               ]);
                        } else {
                            $errors['files'] = "Error uploading the file, please try again.";
                            break;
                         }
                   }else{
                        $errors['files'] = "There was an error uploading the file";
                         break;
                   }
                 }
             }


                // Commit the transaction
            $pdo->commit();
               $successMessage = "Project successfully added.";
          }  catch (PDOException $e) {
           // Rollback transaction
            $pdo->rollBack();
            $errors['db'] = "There was a problem adding the project, please try again.";
           echo $e->getMessage();
        }

    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Project - Task Allocator Pro</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
        <h1>Task Allocator Pro</h1>
           <a href="profile.php">User Profile</a>
           <a href="logout.php">Logout</a>
</header>
    <main class="container">
        <h2>Add Project</h2>
        <p><a href="dashboard.php">Back to Dashboard</a></p>
           <?php if(!empty($successMessage)) : ?>
                <p class="success"><?= $successMessage ?></p>
           <?php else: ?>
                <form method="post" enctype="multipart/form-data">
                  <label for="project_id">Project ID:</label>
                    <input type="text" id="project_id" name="project_id" value="<?= isset($project_id) ? $project_id: ''  ?>" required>
                     <?php if(isset($errors['project_id'])): ?>
                        <p class="error"><?= $errors['project_id'] ?></p>
                     <?php endif ?>


                    <label for="title">Project Title:</label>
                    <input type="text" id="title" name="title" value="<?= isset($title) ? $title: ''  ?>" required>
                    <?php if(isset($errors['title'])): ?>
                        <p class="error"><?= $errors['title'] ?></p>
                    <?php endif ?>

                    <label for="description">Project Description:</label>
                   <textarea id="description" name="description"  required>  <?= isset($description) ? $description: ''  ?>  </textarea>
                     <?php if(isset($errors['description'])): ?>
                        <p class="error"><?= $errors['description'] ?></p>
                     <?php endif ?>


                    <label for="customer_name">Customer Name:</label>
                    <input type="text" id="customer_name" name="customer_name" value="<?= isset($customer_name) ? $customer_name: ''  ?>" required>
                     <?php if(isset($errors['customer_name'])): ?>
                        <p class="error"><?= $errors['customer_name'] ?></p>
                     <?php endif ?>


                    <label for="total_budget">Total Budget:</label>
                    <input type="number" id="total_budget" name="total_budget" value="<?= isset($total_budget) ? $total_budget: ''  ?>" required>
                    <?php if(isset($errors['total_budget'])): ?>
                        <p class="error"><?= $errors['total_budget'] ?></p>
                    <?php endif ?>

                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" value="<?= isset($start_date) ? $start_date: ''  ?>" required>
                     <?php if(isset($errors['start_date'])): ?>
                        <p class="error"><?= $errors['start_date'] ?></p>
                     <?php endif ?>

                    <label for="end_date">End Date:</label>
                     <input type="date" id="end_date" name="end_date" value="<?= isset($end_date) ? $end_date: ''  ?>" required>
                     <?php if(isset($errors['end_date'])): ?>
                        <p class="error"><?= $errors['end_date'] ?></p>
                    <?php endif ?>

                    <label for="supporting_docs">Supporting Documents (Max 3):</label>
                   <div id="file-inputs">
                    <?php for ($i = 0; $i < $fileInputsCount; $i++): ?>
                         <div class="file-upload-container">
                                <input type="file" name="supporting_docs[]">
                             <input type="text" name="document_title[]" placeholder="Title" >
                       </div>
                   <?php endfor; ?>
                    </div>
                     <?php if(isset($errors['files'])): ?>
                          <p class="error"><?= $errors['files'] ?></p>
                    <?php endif ?>
                   <input type="hidden" name="file_inputs_count" value="<?= $fileInputsCount + 1 ?>">
                    <button type="submit" name="add_file_button" >Add another file</button>


                   <button type="submit">Add Project</button>
                </form>
           <?php endif ?>

    </main>
    <footer>
        <p>© <?php echo date("Y"); ?> Task Allocator Pro |
        Contact: <a href="mailto:contact@taskallocatorpro.com">contact@taskallocatorpro.com</a> |
        <a href="about.php">About Us</a></p>
    </footer>
</body>
</html>