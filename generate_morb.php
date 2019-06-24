<!DOCTYPE html>
<html>
<head>
	<title>Invalid Numbers</title>
	<meta charset="ascii">
</head>
<body>

</body>
</html>
<?php


error_reporting(E_ERROR | E_PARSE);
$password = shell_exec("cat .passwd");
$password = explode("=", $password)[1];
if (!(isset($_REQUEST['p']) && ((md5($_REQUEST['p']))) == $password)) {
	header("location: index.php");
}
session_start();
$dbName = $_SESSION['rhu'];
// echo "$dbName";
$conn = mysqli_connect("localhost","root","root",$dbName) or die("No connection could be made");


$test_res = $conn->query("select 1 from patient");



if ($test_res->num_rows > 0) {
		$result = $conn->query("SELECT first_name as fn, last_name as ln, middle_name as mn, philhealth_id as phic FROM patient inner join patient_philhealth on patient.id=patient_philhealth.patient_id  where ((length(philhealth_id)!=14) and philhealth_id like '%-%') OR (length(philhealth_id)!=12 and philhealth_id not like '%-%')");
	}
		else{
			$result = $conn->query("SELECT patient_firstname as fn, patient_lastname as ln, patient_middle as mn, philhealth_id as phic FROM m_patient inner join m_patient_philhealth on m_patient.patient_id=m_patient_philhealth.patient_id where ((length(philhealth_id)!=14) and philhealth_id like '%-%') OR (length(philhealth_id)!=12 and philhealth_id not like '%-%')");
		}

echo "<table><tr><th>Last Name</th><th>First Name</th><th>Middle Name</th><th>Philhealth Number</th><tr>";
while ($row = $result->fetch_assoc()) {
	echo "<tr><td>" . $row['ln'] . "</td><td>" . $row['fn'] . "</td><td>" . $row['mn']. "</td><td>" . (($row['phic']==""?"none":$row['phic'])) ."</td></tr>";
}

echo "</table>"
?>