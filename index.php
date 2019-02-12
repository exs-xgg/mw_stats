<?php
	error_reporting(E_ERROR | E_PARSE);
	require_once 'db.php';
	// $dbHost = "localhost";
	// $dbUser = "root";
	// $dbPass = "root";	
	session_start();
	if (isset($_POST['start_date']) && isset($_POST['end_date']) && !($_POST['start_date']=="" && $_POST['end_date']=="")) {
		$start_date = $_POST['start_date'];
		$end_date = $_POST['end_date'];
	}else{
		$start_date = '2000-01-01';
		$end_date = '2100-12-31';
	}


$dbConnect = mysql_connect("mw2.wahlocal.ph","root","root");
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
		Fields marked with (*) are not sortable by dates
		<div class="row">
			<div class="col-lg-12 col-md-12 col-xs-12 text-center"><div class="alert alert-warning">Technical Statistics</div></div>
		</div>
			<table class='table'>
				<thead>
					<tr>
						<th>Patient Registered Count</th>
						<th>Family Folder Count*</th>
						<th>Consult Notes Count</th>
						<th>Catchment Population*</th>
					</tr>
				</thead>
				<tbody>
					<?php 

						if (isset($_REQUEST['go']) && $_REQUEST['go'] == 'Submit')
						{
							
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
								// echo "$patientCount";
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
						<th>NHTS Members Registered*</th>
						<th>PhilHealth Member Count*</th>
						<th>PhilHealth Dependent Count*</th>
						<th>PhilHealth Count*</th>
						<th>User Count/Active/In-active*</th>
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
				<div class="col-lg-12 col-md-12 col-xs-12 text-center"><div class="alert alert-info">HPP Statistics</div></div>
			</div>
			<table class="table">
				<tr>
				<th>Invalid Philhealth Numbers*</th>
				<th>Empty Family Folder*</th>
				<th>Invalid Phone Numbers*</th>
				<th>Duplicate Patiens (WAHFFLE)*</th>
				<th>Date of Last Consultation</th>
				</tr>
				<?php
						if (isset($_REQUEST['go']) && $_REQUEST['go'] == 'Submit')
						{ 

							$patient_philhealth = "SELECT 
							(SELECT count(*) FROM patient_philhealth where ((length(philhealth_id)!=14) and philhealth_id like '%-%') OR (length(philhealth_id)!=12 and philhealth_id not like '%-%')) AS invalid_philhealth,
							(0) AS empty_fam,
							(SELECT count(*) FROM patient WHERE mobile_number = '0000000000' or length(mobile_number) != 10 or mobile_number = '1111111111' or mobile_number='9000000000') AS invalid_phone,
							(SELECT date(created_at) FROM consult order by created_at desc limit 1) as last_consult";
							// $query2 = $database->_dbQuery($patient_philhealth);
							// $result2=$database->_dbFetch($query2);
							//
							$query2 = mysql_query($patient_philhealth);
							$total2 = mysql_num_rows($query2);
							
							if ($total2 == 0) {
								$patient_philhealth = "SELECT 
							(SELECT count(*) FROM m_patient_philhealth where ((length(philhealth_id)!=14) and philhealth_id like '%-%') OR (length(philhealth_id)!=12 and philhealth_id not like '%-%')) AS invalid_philhealth,
							(SELECT count(*) FROM m_family left join `m_family_members` on m_family.family_id = m_family_members.family_id where patient_id is null) AS empty_fam,
							(SELECT count(*) FROM m_patient WHERE patient_cellphone = '0000000000' or length(patient_cellphone) != 11 or patient_cellphone = '1111111111' or patient_cellphone='9000000000') AS invalid_phone,
							(SELECT date(consult_timestamp) FROM m_consult order by consult_timestamp desc limit 1) as last_consult;";
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
							echo "<td>" . $result2['last_consult'].'</td>';

								
							echo  "</tr>";
							
						}	
					?>
			</table>
			<div class="row">
				<div class="col-lg-12 col-md-12 col-xs-12 text-center"><div class="alert alert-danger">Blood Type*</div></div>
			</div>
			<?php
if (isset($_REQUEST['go']) && $_REQUEST['go'] == 'Submit')
{ 
	//MISUWAH
	$patient_philhealth = "select 
	(select count(*) as om from patient where blood_type='O-') as om ,
	(select count(*) as om from patient where blood_type='O+') as op, 
	(select count(*) as om from patient where blood_type='A-') as am, 
	(select count(*) as om from patient where blood_type='A+') as ap,
	(select count(*) as om from patient where blood_type='B-') as bm, 
	(select count(*) as om from patient where blood_type='B+') as bp, 
	(select count(*) as om from patient where blood_type='AB+') as abp, 
	(select count(*) as om from patient where blood_type='AB-') as abm,
	(select count(*) as om from patient where blood_type='' or blood_type='NA') as na";
	// $query2 = $database->_dbQuery($patient_philhealth);
	// $result2=$database->_dbFetch($query2);
	//
	$query2 = mysql_query($patient_philhealth);
	$total2 = mysql_num_rows($query2);
	

	//WAFFLE
	if ($total2 == 0) {
		$patient_philhealth = "select 
		(select count(*) as om from m_patient where blood_type='O-') as om ,
		(select count(*) as om from m_patient where blood_type='O+') as op, 
		(select count(*) as om from m_patient where blood_type='A-') as am, 
		(select count(*) as om from m_patient where blood_type='A+') as ap, 
		(select count(*) as om from m_patient where blood_type='B-') as bm, 
		(select count(*) as om from m_patient where blood_type='B+') as bp, 
		(select count(*) as om from m_patient where blood_type='AB+') as abp, 
		(select count(*) as om from m_patient where blood_type='AB-') as abm,
		(select count(*) as om from m_patient where blood_type='') as na";
		// $query2 = $database->_dbQuery($patient_philhealth);
		// $result2=$database->_dbFetch($query2);
		$query2 = mysql_query($patient_philhealth);
		$total2 = mysql_num_rows($query2);
	}


	$t="";
	$result2= mysql_fetch_array($query2);
	$om=$result2['om'];
	$op=$result2['op'];
	$am=$result2['am'];
	$ap=$result2['ap'];
	$abp=$result2['abp'];
	$abm=$result2['abm'];
	$na=$result2['na'];
	$bp=$result2['bp'];
	$bm=$result2['bm'];

	
}

?>
			<table class="table table-striped">
			<tr><td>A+	</td><td><?php echo $ap; ?></td></tr>
			<tr><td>A-	</td><td><?php echo $am; ?></td></tr>
			<tr><td>B+	</td><td><?php echo $bp; ?></td></tr>
			<tr><td>B-	</td><td><?php echo $bm; ?></td></tr>
			<tr><td>AB+	</td><td><?php echo $abp; ?></td></tr>
			<tr><td>AB-	</td><td><?php echo $abm; ?></td></tr>
			<tr><td>O+	</td><td><?php echo $op; ?></td></tr>
			<tr><td>O-	</td><td><?php echo $om; ?></td></tr>
			<tr><td>Unknown/NA</td><td>	<?php echo $na; ?></td>
			</table>
			
			<div class="row">
				<div class="col-lg-12 col-md-12 col-xs-12 text-center"><div class="alert alert-success">Demographics (abridged)</div></div>
			</div>

			<?php
if (isset($_REQUEST['go']) && $_REQUEST['go'] == 'Submit')
{ 
	//MISUWAH
	$patient_philhealth = "
	select 
	(select count(*) as ct from patient where FLOOR((datediff(now(), birthdate)/365)) <= (5 ) and created_at between date('$start_date') and date('$end_date') ) as '_5b',
	(select count(*) as ct from patient where FLOOR((datediff(now(), birthdate)/365)) <= (5 ) and created_at between date('$start_date') and date('$end_date') and gender like 'M') as '_5bm',
	(select count(*) as ct from patient where FLOOR((datediff(now(), birthdate)/365)) <= (5 ) and created_at between date('$start_date') and date('$end_date') and gender like 'F') as '_5bf',

	(select count(*) as ct from patient where (FLOOR((datediff(now(), birthdate)/365)) between (6 ) and (17 )) and created_at between date('$start_date') and date('$end_date')) as '_6to17',
	(select count(*) as ct from patient where (FLOOR((datediff(now(), birthdate)/365)) between (6 ) and (17 )) and created_at between date('$start_date') and date('$end_date') and gender like 'M') as '_6to17m',
	(select count(*) as ct from patient where (FLOOR((datediff(now(), birthdate)/365)) between (6 ) and (17 )) and created_at between date('$start_date') and date('$end_date') and gender like 'F') as '_6to17f',


	(select count(*) as ct from patient where (FLOOR((datediff(now(), birthdate)/365)) between (18 ) and (35 ))  and created_at between date('$start_date') and date('$end_date') ) as '_18to35',
	(select count(*) as ct from patient where (FLOOR((datediff(now(), birthdate)/365)) between (18 ) and (35 ))  and created_at between date('$start_date') and date('$end_date') and gender like 'M') as '_18to35m',
	(select count(*) as ct from patient where (FLOOR((datediff(now(), birthdate)/365)) between (18 ) and (35 ))  and created_at between date('$start_date') and date('$end_date') and gender like 'F') as '_18to35f',
	
	(select count(*) as ct from patient where (FLOOR((datediff(now(), birthdate)/365)) between (36 ) and (59 ))  and created_at between date('$start_date') and date('$end_date') ) as '_36to59',
	(select count(*) as ct from patient where (FLOOR((datediff(now(), birthdate)/365)) between (36 ) and (59 ))  and created_at between date('$start_date') and date('$end_date') and gender like 'M') as '_36to59m',
	(select count(*) as ct from patient where (FLOOR((datediff(now(), birthdate)/365)) between (36 ) and (59 ))  and created_at between date('$start_date') and date('$end_date') and gender like 'F') as '_36to59f',
	
	(select count(*) as ct from patient where ((60 ) <= FLOOR((datediff(now(), birthdate)/365))) and created_at between date('$start_date') and date('$end_date') ) as '_60a',
	(select count(*) as ct from patient where ((60 ) <= FLOOR((datediff(now(), birthdate)/365))) and created_at between date('$start_date') and date('$end_date') and gender like 'F') as '_60af',
	(select count(*) as ct from patient where ((60 ) <= FLOOR((datediff(now(), birthdate)/365))) and created_at between date('$start_date') and date('$end_date') and gender like 'M') as '_60am',
	
	(select count(distinct(patient.id)) from patient_mc inner join patient on patient.id=patient_mc.patient_id where FLOOR((datediff(lmp_date, birthdate)/365)) < 18 and  date('lmp_date') between date('$start_date') and date('$end_date')) as 'total_preggy'
	";
	// $query2 = $database->_dbQuery($patient_philhealth);
	// $result2=$database->_dbFetch($query2);
	//
	$query2 = mysql_query($patient_philhealth);
	$total2 = mysql_num_rows($query2);
	

	//WAFFLE
	if ($total2 == 0) {
		$patient_philhealth = "select 
		(select count(*) as ct from m_patient where FLOOR((datediff(now(), patient_dob)/365)) <= (5 ) and registration_date between date('$start_date') and date('$end_date')) as '_5b',
		(select count(*) as ct from m_patient where FLOOR((datediff(now(), patient_dob)/365)) <= (5 ) and registration_date between date('$start_date') and date('$end_date')  and patient_gender like 'M') as '_5bm',
		(select count(*) as ct from m_patient where FLOOR((datediff(now(), patient_dob)/365)) <= (5 ) and registration_date between date('$start_date') and date('$end_date')  and patient_gender like 'F') as '_5bf',

		(select count(*) as ct from m_patient where (FLOOR((datediff(now(), patient_dob)/365)) between (6 ) and (17 )) and registration_date between date('$start_date') and date('$end_date')) as '_6to17',
		(select count(*) as ct from m_patient where (FLOOR((datediff(now(), patient_dob)/365)) between (6 ) and (17 )) and registration_date between date('$start_date') and date('$end_date') and patient_gender like 'M') as '_6to17m',
		(select count(*) as ct from m_patient where (FLOOR((datediff(now(), patient_dob)/365)) between (6 ) and (17 )) and registration_date between date('$start_date') and date('$end_date') and patient_gender like 'F') as '_6to17f',

		(select count(*) as ct from m_patient where (FLOOR((datediff(now(), patient_dob)/365)) between (18 ) and (35 ))  and registration_date between date('$start_date') and date('$end_date') ) as '_18to35',
		(select count(*) as ct from m_patient where (FLOOR((datediff(now(), patient_dob)/365)) between (18 ) and (35 )) and registration_date between date('$start_date') and date('$end_date') and patient_gender like 'F') as '_18to35f',
		(select count(*) as ct from m_patient where (FLOOR((datediff(now(), patient_dob)/365)) between (18 ) and (35 )) and registration_date between date('$start_date') and date('$end_date') and patient_gender like 'M') as '_18to35m',

		(select count(*) as ct from m_patient where (FLOOR((datediff(now(), patient_dob)/365)) between (36 ) and (59))  and registration_date between date('$start_date') and date('$end_date') ) as '_36to59',
		(select count(*) as ct from m_patient where (FLOOR((datediff(now(), patient_dob)/365)) between (36 ) and (59))  and registration_date between date('$start_date') and date('$end_date') and patient_gender like 'M') as '_36to59m',
		(select count(*) as ct from m_patient where (FLOOR((datediff(now(), patient_dob)/365)) between (36 ) and (59))  and registration_date between date('$start_date') and date('$end_date') and patient_gender like 'F') as '_36to59f',

		(select count(*) as ct from m_patient where ((60 ) <= FLOOR((datediff(now(), patient_dob)/365))) and registration_date between date('$start_date') and date('$end_date') ) as '_60a',
		(select count(*) as ct from m_patient where ((60 ) <= FLOOR((datediff(now(), patient_dob)/365))) and registration_date between date('$start_date') and date('$end_date') and patient_gender like 'F') as '_60af',
		(select count(*) as ct from m_patient where ((60 ) <= FLOOR((datediff(now(), patient_dob)/365))) and registration_date between date('$start_date') and date('$end_date') and patient_gender like 'M') as '_60am',
		
		(select count(distinct(m_patient.patient_id)) from m_patient_mc inner join m_patient on m_patient.patient_id=m_patient_mc.patient_id where FLOOR((datediff(patient_lmp, patient_dob)/365)) < 18 and  date('mc_consult_date') between date('$start_date') and date('$end_date')) as 'total_preggy'
		";

		//get all pregnant teenages
		//select count(1) as ct, FLOOR((datediff(mc_consult_date, patient_dob)/365)) as age from m_patient_mc inner join m_patient on m_patient.patient_id=m_patient_mc.patient_id where FLOOR((datediff(now(), patient_dob)/365)) < 18 group by age

		// $query2 = $database->_dbQuery($patient_philhealth);
		// $result2=$database->_dbFetch($query2);
		$query2 = mysql_query($patient_philhealth);
		$total2 = mysql_num_rows($query2);
	}


	$t="";
	$result2= mysql_fetch_array($query2);
	// print_r($result2);
	$_5b=$result2['_5b'];
	$_6to17=$result2['_6to17'];
	$_18to35=$result2['_18to35'];
	$_36to59=$result2['_36to59'];
	$_60a=$result2['_60a'];

	
	$_5b=$result2['_5b'];
	$_5bf=$result2['_5bf'];
	$_5bm=$result2['_5bm'];

	$_6to17=$result2['_6to17'];
	$_6to17f=$result2['_6to17f'];
	$_6to17m=$result2['_6to17m'];
	
	$_18to35=$result2['_18to35'];
	$_18to35m=$result2['_18to35m'];
	$_18to35f=$result2['_18to35f'];

	$_36to59=$result2['_36to59'];
	$_36to59f=$result2['_36to59f'];
	$_36to59m=$result2['_36to59m'];

	$_60a=$result2['_60a'];
	$_60af=$result2['_60af'];
	$_60am=$result2['_60am'];

	$total_preggy=$result2['total_preggy'];	
}

?>
			<table class="taable" align="center">
				<tr>
				<th colspan="2">5 below</th>
				<th colspan="2">6-17</th>
				<th colspan="2">18-35</th>
				<th colspan="2">36-59</th>
				<th colspan="2">60 above</th>
				<th colspan="2">Total</th>
				</tr>
				<tr>
					<td>M</td>
					<td>F</td>
					<td>M</td>
					<td>F</td>
					<td>M</td>
					<td>F</td>
					<td>M</td>
					<td>F</td>
					<td>M</td>
					<td>F</td>
					<td>M</td>
					<td>F</td>
				</tr>
				<tr>
				
				</tr>
					<td><?php echo $_5bm; ?></td>
					<td><?php echo $_5bf; ?></td>
					<td><?php echo $_6to17m; ?></td>
					<td><?php echo $_6to17f; ?></td>
					<td><?php echo $_18to35m; ?></td>
					<td><?php echo $_18to35f; ?></td>
					<td><?php echo $_36to59m; ?></td>
					<td><?php echo $_36to59f; ?></td>
					<td><?php echo $_60am; ?></td>
					<td><?php echo $_60af; ?></td>

					<td><?php echo $_5bm + $_6to17m + $_18to35m + $_36to59m + $_60am; ?></td>
					<td><?php echo $_5bf + $_6to17f + $_18to35f + $_36to59f + $_60af; ?></td>
				</th>
				<tr>
					<td colspan="2"><?php echo $_5b; ?></td>
					<td colspan="2"><?php echo $_6to17; ?></td>
					<td colspan="2"><?php echo $_18to35; ?></td>
					<td colspan="2"><?php echo $_36to59; ?></td>
					<td colspan="2"><?php echo $_60a; ?></td>
					<td colspan="2"><?php echo $_5b + $_6to17 + $_18to35 + $_36to59 + $_60a; ?></td>
				</tr>
			</table>
			<div class="row">
				<div class="col-lg-12 col-md-12 col-xs-12 text-center"><div class="alert alert-danger">PREGNANT TEENAGERS REGISTERED</div></div>
				<h1 align="center"><?php echo $total_preggy; ?></h1>
			</div>
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



<style>
table, th, td {
  border: 1px solid black;
}
th, td {
  padding: 15px;
  text-align: center;
}
</style>
