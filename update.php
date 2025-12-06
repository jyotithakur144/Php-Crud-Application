<?php
require "conn.php";
$id=intval($_GET['id']);
$row=mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM users WHERE id=$id"));

$nameErr=$emailErr=$fileErr=$genderErr=$hobbiesErr=$statusErr="";
$name=$row['name']; $email=$row['email']; $gender=$row['gender']; 
$hobbies=!empty($row['hobbies'])?explode(",",$row['hobbies']):[];
$status=$row['status']; $file=$row['file'];

if($_SERVER['REQUEST_METHOD']=='POST'){

    $name=trim($_POST['name']); if($name=='') $nameErr="Name required"; 
    elseif(!preg_match("/^[a-zA-Z ]*$/",$name)) $nameErr="Only letters allowed";

    $email=trim($_POST['email']); if($email=='') $emailErr="Email required";
    elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)) $emailErr="Invalid email";

    $gender=$_POST['gender'] ?? ""; if($gender=='') $genderErr="Gender required";

    $hobbies=$_POST['hobbies'] ?? []; if(empty($hobbies)) $hobbiesErr="Select at least one hobby";

    $status=$_POST['status'] ?? ""; if($status=='') $statusErr="Status required";

    if(!empty($_FILES['file']['name'])){
        $ext=strtolower(pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION));
        if(in_array($ext,['jpg','jpeg','png','gif','pdf'])){
            $newFile=uniqid().".$ext";
            move_uploaded_file($_FILES['file']['tmp_name'],"uploads/".$newFile);
            if(!empty($file) && file_exists("uploads/".$file)) unlink("uploads/".$file);
            $file=$newFile;
        }else $fileErr="Invalid file type";
    }

    if($nameErr=="" && $emailErr=="" && $fileErr=="" && $genderErr=="" && $hobbiesErr=="" && $statusErr==""){
        $hStr=implode(",",$hobbies);
        mysqli_query($conn,"UPDATE users SET name='$name', email='$email', file='$file', gender='$gender', hobbies='$hStr', status='$status' WHERE id=$id");
        header("Location: read.php"); exit;
    }
}
?>

<head><style>.error{color:red;}</style></head>
<h2>Update User</h2>
<form method="post" enctype="multipart/form-data">
Name:<input type="text" name="name" value="<?= htmlspecialchars($name) ?>">
<span class="error"><?= $nameErr ?></span><br><br>

Email:<input type="text" name="email" value="<?= htmlspecialchars($email) ?>">
<span class="error"><?= $emailErr ?></span><br><br>

File: <input type="file" name="file">
<?php if($file): ?><br>Current File: <a href="uploads/<?= htmlspecialchars($file) ?>" target="_blank"><?= htmlspecialchars($file) ?></a><?php endif; ?>
<span class="error"><?= $fileErr ?></span><br><br>

<select name="gender">
<option value="">Select Gender</option>
<option value="Male" <?= $gender=="Male"?"selected":"" ?>>Male</option>
<option value="Female" <?= $gender=="Female"?"selected":"" ?>>Female</option>
<option value="Other" <?= $gender=="Other"?"selected":"" ?>>Other</option>
</select>
<span class="error"><?= $genderErr ?></span><br><br>

<label><input type="checkbox" name="hobbies[]" value="Reading" <?= in_array("Reading",$hobbies)?"checked":"" ?>> Reading</label>
<label><input type="checkbox" name="hobbies[]" value="Music" <?= in_array("Music",$hobbies)?"checked":"" ?>> Music</label>
<label><input type="checkbox" name="hobbies[]" value="Sports" <?= in_array("Sports",$hobbies)?"checked":"" ?>> Sports</label>
<span class="error"><?= $hobbiesErr ?></span><br><br>

<label><input type="radio" name="status" value="Active" <?= $status=="Active"?"checked":"" ?>> Active</label>
<label><input type="radio" name="status" value="Inactive" <?= $status=="Inactive"?"checked":"" ?>> Inactive</label>
<span class="error"><?= $statusErr ?></span><br><br>

<button type="submit">Update User</button>
</form>
