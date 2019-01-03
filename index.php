<?php
	// error_reporting(E_ERROR | E_PARSE);
	require_once 'db.php';
	// $dbHost = "localhost";
	// $dbUser = "root";
	// $dbPass = "root";	
	session_start();



							$dbConnect = mysql_connect("localhost","root","root");
?>
<html>
<head>
	<title>Hybrid Report Generation</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
	<meta author="Wireless Access for Health">
</head>
<body>
	<br>
	<div class="container">
		
		<form method='post' action=''>
          			<select name='rhu'>
			              <?php 
			        
			  		foreach($dbarray as $key => $value){
					if($_POST['rhu'] == $value){
						$rhu = $key;
					}
					  echo "<option value='$value' ".($_POST['rhu'] == $value ? 'selected' : '')."> $key</option>
					  ";
					}
			 	      ?>
          			  </select>
          			  <input type="date" name="start_date" <?php if (isset( $_POST['start_date'])): ?>
          			  	value="<?php echo( $_POST['start_date']); ?>"
          			  <?php endif ?>>
          			  <input type="date" name="end_date"<?php if (isset( $_POST['start_date'])): ?>
          			  	value="<?php echo( $_POST['end_date']); ?>"
          			  <?php endif ?>>
				  <input type='submit' class="btn btn-outline-primary" name='go' value='Submit'>  	
		</form>
		<div class="row">
			<div class="col-lg-12 col-md-12 col-xs-12 text-center"><div class="alert alert-danger">Technical Statistics</div></div>
		</div>
			<table class='table'>
				<thead>
					<tr>
						<th>Patient Registered Count</th>
						<th>Family Folder Count</th>
						<th>Consult Notes Count</th>
						<th>Catchment Population</th>
					</tr>
				</thead>
				<tbody>
					<?php 

						if (isset($_REQUEST['go']) && $_REQUEST['go'] == 'Submit')
						{
							// if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
								$start_date = $_POST['start_date'];
								$end_date = $_POST['end_date'];
							// }
							$dbName = $_POST['rhu'];
							$_SESSION['rhu'] = $dbName;

							mysql_select_db($dbName,$dbConnect);

							$patientCount = "SELECT ( SELECT count(*) FROM patient where created_at between date('$start_date') and date('$end_date')) as patient,
							( SELECT count(*) FROM consult where created_at between date('$start_date') and date('$end_date')) as consult,
							( SELECT count(*) FROM family ) as family,
							( SELECT sum(population) FROM lib_catchment_barangay) as catchment";

							
							$query1 = mysql_query($patientCount);
							// echo "$patientCount";
							$total1 = mysql_num_rows($query1);
							if ($total1 == 0) {
								//WAFFLE QUERY FAILOVER
								$patientCount = "SELECT ( SELECT count(*) FROM m_patient where registration_date between date('$start_date') and date('$end_date')) as patient,
								( SELECT count(*) FROM m_consult where consult_date between date('$start_date') and date('$end_date')) as consult,
								( SELECT count(*) FROM m_family) as family,
								( SELECT sum(barangay_population) FROM m_lib_barangay) as catchment";
								echo "$patientCount";
								$query1 = mysql_query($patientCount);
								
								$total1 = mysql_num_rows($query1);
							}

							$result1= mysql_fetch_array($query1);
							echo "<tr>";
								echo '<td>'.$result1['patient'].'</td>';
								echo '<td>'.$result1['family'].'</td>';
								echo '<td>'.$result1['consult'].'</td>';
								echo '<td>'.$result1['catchment'].'</td>';
							echo  "</tr>";
							
						}
					?>
				</tbody>
			</table>
			<br>
			<table class='table'>
				<thead>
					<tr>
						<th>NHTS Members Registered</th>
						<th>PhilHealth Member Count</th>
						<th>PhilHealth Dependent Count</th>
						<th>PhilHealth Count</th>
						<th>User Count/Active/In-active</th>
					</tr>
				</thead>
				<tbody>
					<?php
						if (isset($_REQUEST['go']) && $_REQUEST['go'] == 'Submit')
						{ 

							$patient_philhealth = "SELECT (SELECT count(*) FROM patient_philhealth WHERE member_cat_id ='18') AS nhts,
							(SELECT count(*) FROM patient_philhealth WHERE member_id ='MM') AS member,
							(SELECT count(*) FROM patient_philhealth WHERE member_id ='DD') AS dependent,
							(SELECT count(*) FROM patient_philhealth) AS patient_philhealth,
							(SELECT count(*) FROM user) AS user,
							(SELECT count(*) FROM user WHERE is_active = 'Y') AS useractive,
							(SELECT count(*) FROM user WHERE is_active = 'N') AS userinactive";
							// $query2 = $database->_dbQuery($patient_philhealth);
							// $result2=$database->_dbFetch($query2);
							$query2 = mysql_query($patient_philhealth);
							$total2 = mysql_num_rows($query2);
							
							if ($total2 == 0) {
								$patient_philhealth = "SELECT (SELECT count(*) FROM m_patient_philhealth WHERE member_cat_id ='18') AS nhts,
							(SELECT count(*) FROM m_patient_philhealth WHERE member_id ='MM') AS member,
							(SELECT count(*) FROM m_patient_philhealth WHERE member_id ='DD') AS dependent,
							(SELECT count(*) FROM m_patient_philhealth) AS patient_philhealth,
							(SELECT count(*) FROM game_user) AS user,
							(SELECT count(*) FROM game_user WHERE user_active = 'Y') AS useractive,
							(SELECT count(*) FROM game_user WHERE user_active = 'N') AS userinactive";
								// $query2 = $database->_dbQuery($patient_philhealth);
								// $result2=$database->_dbFetch($query2);
								$query2 = mysql_query($patient_philhealth);
								$total2 = mysql_num_rows($query2);
							}



							$result2= mysql_fetch_array($query2);
							echo "<tr>";
								echo '<td>'.$result2['nhts'].'</td>';
								echo '<td>'.$result2['member'].'</td>';
								echo '<td>'.$result2['dependent'].'</td>';
								echo '<td>'.$result2['patient_philhealth'].'</td>';
								echo '<td>'.$result2['user'].'/'.$result2['useractive'].'/'.$result2['userinactive'].'</td>';
							echo  "</tr>";
							
						}	
					?>
				</tbody>
			</table>
			<div class="row">
				<div class="col-lg-12 col-md-12 col-xs-12 text-center"><div class="alert alert-success">HPP Statistics</div></div>
			</div>
			<table class="table">
				<tr><th>Invalid Philhealth Numbers</th><th>Empty Family Folder</th><th>Invalid Phone Numbers</th>
						<th>Duplicate Patiens (WAHFFLE)</th></tr>
				<?php
						if (isset($_REQUEST['go']) && $_REQUEST['go'] == 'Submit')
						{ 

							$patient_philhealth = "SELECT 
							(SELECT count(*) FROM patient_philhealth where ((length(philhealth_id)!=14) and philhealth_id like '%-%') OR (length(philhealth_id)!=12 and philhealth_id not like '%-%')) AS invalid_philhealth,
							(0) AS empty_fam,
							(SELECT count(*) FROM patient WHERE mobile_number = '0000000000' or length(mobile_number) != 10 or mobile_number = '1111111111' or mobile_number='9000000000') AS invalid_phone";
							// $query2 = $database->_dbQuery($patient_philhealth);
							// $result2=$database->_dbFetch($query2);
							//
							$query2 = mysql_query($patient_philhealth);
							$total2 = mysql_num_rows($query2);
							
							if ($total2 == 0) {
								$patient_philhealth = "SELECT 
							(SELECT count(*) FROM m_patient_philhealth where ((length(philhealth_id)!=14) and philhealth_id like '%-%') OR (length(philhealth_id)!=12 and philhealth_id not like '%-%')) AS invalid_philhealth,
							(SELECT count(*) FROM m_family left join `m_family_members` on m_family.family_id = m_family_members.family_id where patient_id is null) AS empty_fam,
							(SELECT count(*) FROM m_patient WHERE patient_cellphone = '0000000000' or length(patient_cellphone) != 11 or patient_cellphone = '1111111111' or patient_cellphone='9000000000') AS invalid_phone;";
								// $query2 = $database->_dbQuery($patient_philhealth);
								// $result2=$database->_dbFetch($query2);
								$query2 = mysql_query($patient_philhealth);
								$total2 = mysql_num_rows($query2);
							}


							$t="";
							$result2= mysql_fetch_array($query2);
							echo "<tr>";
							echo '<td>'.$result2['invalid_philhealth'].'</td>';
							echo '<td>'.$result2['empty_fam'].'</td>';
							echo '<td>'.$result2['invalid_phone'].'</td>';
							$sum_distint_query = " SELECT COUNT(1) as ct
							FROM		m_patient
							GROUP BY	patient_firstname, patient_lastname, patient_middle, patient_mother
							HAVING		COUNT(1) > 1";
							$sumres =  mysql_query($sum_distint_query);
							$total3 = mysql_num_rows($sumres);
							$t = 0;
							//if ($total3==0) {
								while ($sums = mysql_fetch_assoc($sumres)) {
									$t += $sums['ct'];
								}
								echo "<td>$t </td>";
							//}

								
							echo  "</tr>";
							
						}	
					?>
			</table>
			<button class="btn btn-success" onclick="phic()">Generate Invalid Philhealth</button> <button class="btn btn-primary" onclick="cell()">Generate Invalid Numbers</button>
	</div>
</body>
<script type="text/javascript">
	function phic(){
		var password = prompt("You are about to view sensitive information. Password is required");
		window.location.href = "generate_invalid_phic.php?p=" + (password);
	}
	function cell(){
		var password = prompt("You are about to view sensitive information. Password is required");
		window.location.href = "generate_invalid_numbers.php?p=" + (password);
	}
</script>
</html>


