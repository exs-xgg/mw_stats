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
$result = $conn->query("SELECT first_name as fn, last_name as ln, middle_name as mn, mobile_number as cell FROM patient WHERE mobile_number = '0000000000' or length(mobile_number) != 10 or mobile_number = '1111111111' or mobile_number='9000000000'");
}else{
$result = $conn->query("SELECT patient_firstname as fn, patient_lastname as ln, patient_middle as mn, patient_cellphone as cell FROM m_patient WHERE patient_cellphone = '0000000000' or length(patient_cellphone) != 11 or patient_cellphone = '1111111111' or patient_cellphone='9000000000'");
}










$total = $result->num_rows;
if ($total) {
	
}
echo "<table><tr><th>Last Name</th><th>First Name</th><th>Middle Name</th><th>Mobile Number</th><tr>";
while ($row = $result->fetch_assoc()) {
	echo "<tr><td>" . $row['ln'] . "</td><td>" . $row['fn'] . "</td><td>" . $row['mn']. "</td><td>" . $row['cell'] ."</td></tr>";
}

echo "</table>"
?>