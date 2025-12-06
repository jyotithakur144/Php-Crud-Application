<?php
require "conn.php";
$id = intval($_GET['id']);
$sql = "DELETE FROM users WHERE id=$id";
if (mysqli_query($conn, $sql)) {
    header("Location:read.php");
} else {
    echo "Error:" . mysqli_error($conn);
}
