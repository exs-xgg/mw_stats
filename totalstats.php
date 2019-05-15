<html>
<head>
	<title>Hybrid Report Generation</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
	<meta author="Wireless Access for Health">
</head>
<style>
table, th, td {
  border: 1px solid black;
}
th, td {
  padding: 15px;
  text-align: center;
}
</style>
<div class="jumbotron">
	<h1>Overall Statistics</h1>
	<div class="container">
		<div class="row">
			<div class="col-8 mx-auto">
				<table class="table">
					<thead>
						<tr>
							<th>Patients Registered</th><th>Patients(M) Registered</th><th>Patients(F) Registered</th><th>Consults Registered</th><th>Patients Served Daily</th>
						</tr>
					</thead>
					<tbody>
						<tr>
						<td id="pt_reg">Patients Registered</td><td id="pt_reg_m">Patients(M) Registered</td><td id="pt_reg_f">Patients(F) Registered</td><td id="cons_reg">Consults Registered</td><td id="pt_reg_avg">Patients Served Daily</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<table class="table table-striped">
	<thead>
		<tr><th>Site</th><th>Patients Registered</th><td>Invalid Numbers</td><th>Patients(M) Registered</th><th>Patients(F) Registered</th><th>Consults Registered</th><th>Patients Served Daily</th><th>Date of Last Consult</th>
		</tr>
	</thead>
<?php

	error_reporting(E_ERROR | E_PARSE);
require_once 'db.php';
//GLOBAL VARS
$start_date = $_GET['s'];
$end_date = $_GET['e'];
$totalPatientRegistered = 0;
$totalPatientRegistered_M = 0;
$totalPatientRegistered_F = 0;
$totalConsultations = 0;
$totalServedDaily = 0;
//END GLOBAL VARS



foreach ($dbarray as $key => $value) {
	//DB CONNECTOR 
	$conn = mysqli_connect("mw2.wahlocal.ph","root","root",$value);
	$test = $conn->query("select * from patient");
	$is_misuwah = $test->num_rows;
	if($is_misuwah > 0){
		$query_string = "SELECT ( SELECT count(*) FROM patient where created_at between date('$start_date') and date('$end_date')) as patient,

		( SELECT count(*) FROM consult where created_at between date('$start_date') and date('$end_date')) as consult,

( SELECT date(created_at) FROM consult order by created_at desc limit 1) as last_date,

								(select count(*) as ct from patient where created_at between date('$start_date') and date('$end_date') and gender like 'F') as 'patient_f',

								(select count(*) as ct from patient where created_at between date('$start_date') and date('$end_date') and gender like 'M') as 'patient_m',
								(SELECT count(*) FROM patient WHERE mobile_number = '0000000000' or length(mobile_number) != 10 or mobile_number = '1111111111' or mobile_number='9000000000') AS invalid_phone,
								(select avg(ct) as avg_registered from (select count(1) as ct from consult where created_at between date('$start_date') and date('$end_date')  group by date(created_at)) MyTable) as avg_registered";
	}else{
		$query_string = "SELECT ( SELECT count(*) FROM m_patient where registration_date between date('$start_date') and date('$end_date')) as patient,

( SELECT count(*) FROM m_consult where consult_date between date('$start_date') and date('$end_date')) as consult,
( SELECT date(`consult_timestamp`) FROM m_consult order by `consult_timestamp` desc limit 1) as last_date,

								(select count(*) as ct from m_patient where registration_date between date('$start_date') and date('$end_date')  and patient_gender like 'M') as 'patient_f',
								(SELECT count(*) FROM m_patient WHERE patient_cellphone = '0000000000' or length(patient_cellphone) != 11 or patient_cellphone = '1111111111' or patient_cellphone='9000000000') AS invalid_phone,
								(select count(*) as ct from m_patient where registration_date between date('$start_date') and date('$end_date')  and patient_gender like 'F') as 'patient_m',

								select (select avg(ct) as avg_registered from (select count(1) as ct from m_consult where consult_timestamp between date('2018-01-01') and date('2018-12-13')  group by date(consult_timestamp)) MyTable) as avg_registered";
	}

	$result = $conn->query($query_string);
	if ($result->num_rows > 0) {
		$result_array = $result->fetch_assoc();
		echo "<tr>";
		echo "<td>$value</td>";
		echo "<td>" . $result_array['patient'] . '</td>';
		echo "<td>" . $result_array['invalid_phone'] . '</td>';
		echo "<td>" . $result_array['patient_m'] . '</td>';
		echo "<td>" . $result_array['patient_f'] . '</td>';
		echo "<td>" . $result_array['consult'] . '</td>';
		echo "<td>" . $result_array['avg_registered'] . '</td>';
		echo "<td>" . $result_array['last_date'] . '</td>';
	
		$totalPatientRegistered += $result_array['patient'];
		$totalPatientRegistered_M += $result_array['patient_m'];
		$totalPatientRegistered_F += $result_array['patient_f'];
		$totalConsultations += $result_array['consult'];
		$totalServedDaily += $result_array['avg_registered'];

		echo "</tr>";
	}
	

	
}

?>
</table>
<script type="text/javascript">
	// <td id="pt_reg">Patients Registered</td><td id="pt_reg_m">Patients(M) Registered</td><td id="pt_reg_f">Patients(F) Registered</td><td id="cons_reg">Consults Registered</td><td id="pt_reg_avg">Patients Served Daily</td>
	var pt_reg = document.getElementById("pt_reg");
	var pt_reg_m = document.getElementById("pt_reg_m");
	var pt_reg_f = document.getElementById("pt_reg_f");
	var cons_reg = document.getElementById("cons_reg");
	var pt_reg_avg = document.getElementById("pt_reg_avg");

	pt_reg.innerHTML = "<?php echo $totalPatientRegistered; ?>";
	pt_reg_m.innerHTML = "<?php echo $totalPatientRegistered_M; ?>";
	pt_reg_f.innerHTML = "<?php echo $totalPatientRegistered_F; ?>";
	cons_reg.innerHTML = "<?php echo $totalConsultations; ?>";
	pt_reg_avg.innerHTML = "<?php echo $totalServedDaily; ?>";
</script>
</html>



