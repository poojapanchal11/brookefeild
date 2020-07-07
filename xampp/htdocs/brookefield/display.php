<?php
//make connection 
$con=mysqli_connect('localhost', 'chromozome_brookfeild', 'Brookfeildhospital');

//select db
mysqli_select_db($con, 'chromozome_brookfeild');

$sql = " SELECT * FROM appointment ";
$records = mysqli_query($con, $sql);



?>
<!DOCTYPE html>
<html>
<head>
	<title>Appointent list</title>
	<link rel="stylesheet" type="text/css" href="css/display.css">
</head>
<body>
	<table cellpadding="1" cellspacing="1" border="1" style="width: 100%; ">
		<tr>
			<th>ID</th>
			<th>NAME</th>
			<th>EMAIL</th>
			<th>BIRTH</th>
			<th>PHONE NO.</th>
			<th>APPOINTMENT DATE</th>
			<th>DEPARTMENT</th>
			<th>SUBJECT</th>
			<th>MESSAGE</th>
		</tr>
		<?php
			while ($appointment=mysqli_fetch_assoc($records)) {
				echo "<tr>";
					echo "<td>".$appointment['id']."</td>";
					echo "<td>".$appointment['name']."</td>";
					echo "<td>".$appointment['email']."</td>";
					echo "<td>".$appointment['bday']."</td>";
					echo "<td>".$appointment['phone']."</td>";
					echo "<td>".$appointment['apdate']."</td>";
					echo "<td>".$appointment['dpt']."</td>";
					echo "<td>".$appointment['sub']."</td>";
					echo "<td>".$appointment['msg']."</td>";
				echo "</tr>";
			}
		?>
	</table>
		
	</table>

</body>
</html>