<?php
require "conn.php";

$nameErr = $emailErr = $fileErr = $genderErr = $hobbiesErr = $statusErr = "";
$name = $email = $file = $gender = $hobbies = $status = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = trim($_POST['name']);
    if ($name === "") $nameErr = "Name is required";
    elseif (!preg_match("/^[a-zA-Z ]*$/", $name)) $nameErr = "Only letters and white space allowed";

    $email = trim($_POST['email']);
    if ($email === "") $emailErr = "Email is required";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $emailErr = "Invalid email format";

    if (!empty($_FILES['file']['name'])) {
        $fileTmp  = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];
        $ext      = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];

        if (!in_array($ext, $allowedExt)) $fileErr = "Invalid file type";
        else {
            $file = uniqid() . "." . $ext;
            if (!is_dir("uploads")) mkdir("uploads", 0777, true);
            move_uploaded_file($fileTmp, "uploads/" . $file);
        }
    } else $fileErr = "Please choose a file";

    $gender = $_POST['gender'] ?? "";
    if ($gender == "") $genderErr = "Gender is required";

    $hobbies = !empty($_POST['hobbies']) ? implode(",", $_POST['hobbies']) : "";
    if ($hobbies == "") $hobbiesErr = "Select at least one hobby";

    $status = $_POST['status'] ?? "";
    if ($status == "") $statusErr = "Status is required";

    if ($nameErr=="" && $emailErr=="" && $fileErr=="" && $genderErr=="" && $hobbiesErr=="" && $statusErr=="") {
        $sql = "INSERT INTO users (name, email, file, gender, hobbies, status)
                VALUES ('$name', '$email', '$file', '$gender', '$hobbies', '$status')";
        if (mysqli_query($conn, $sql)) {
            header("Location: read.php"); exit;
        } else echo "Database Error: " . mysqli_error($conn);
    }
}
?>

<head>
<style>.error { color: red; }</style>
</head>

<h1>Create User</h1>
<form method="post" enctype="multipart/form-data">
    Name: <input type="text" name="name" value="<?= htmlspecialchars($name) ?>">
    <span class="error"><?= $nameErr ?></span><br><br>

    Email: <input type="email" name="email" value="<?= htmlspecialchars($email) ?>">
    <span class="error"><?= $emailErr ?></span><br><br>

    File: <input type="file" name="file">
    <span class="error"><?= $fileErr ?></span><br><br>

    Gender:
    <select name="gender">
        <option value="">Select Gender</option>
        <option value="Male" <?= $gender=="Male"?"selected":"" ?>>Male</option>
        <option value="Female" <?= $gender=="Female"?"selected":"" ?>>Female</option>
        <option value="Other" <?= $gender=="Other"?"selected":"" ?>>Other</option>
    </select>
    <span class="error"><?= $genderErr ?></span><br><br>

    Hobbies:
    <label><input type="checkbox" name="hobbies[]" value="Reading" <?= strpos($hobbies,"Reading")!==false?"checked":"" ?>> Reading</label>
    <label><input type="checkbox" name="hobbies[]" value="Music" <?= strpos($hobbies,"Music")!==false?"checked":"" ?>> Music</label>
    <label><input type="checkbox" name="hobbies[]" value="Sports" <?= strpos($hobbies,"Sports")!==false?"checked":"" ?>> Sports</label>
    <span class="error"><?= $hobbiesErr ?></span><br><br>

    Status:
    <label><input type="radio" name="status" value="Active" <?= $status=="Active"?"checked":"" ?>> Active</label>
    <label><input type="radio" name="status" value="Inactive" <?= $status=="Inactive"?"checked":"" ?>> Inactive</label>
    <span class="error"><?= $statusErr ?></span><br><br>

    <button type="submit">Add User</button>
</form>
