<?php

$con = mysqli_connect('localhost', 'brookefeild_chromozomes', 'Chromozomes@123', 'brookefeild_app');

if (!$con) {
	echo "not conntected to server ";

}


$name = $_POST['name'];
$email = $_POST['email'];
$birth = $_POST['dob'];
$phnumber = $_POST['phone'];
$apointmentdate = $_POST['apt-date'];
$department = $_POST['department'];
$subject = $_POST['subject'];
$message = $_POST['msg'];

$sql = "INSERT INTO appointment (name, email, bday, phone, apdate, dpt, sub, msg) VALUES ('$name','$email','$birth','$phnumber','$apointmentdate','$department','$subject','$message') ";

if (!mysqli_query($con,$sql)) {
	echo "data not inserted";
}
else{
	echo "data inserted";
}

header("refresh:2; url=index.php");

?>