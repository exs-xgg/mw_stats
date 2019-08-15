<?php
echo "x\07";
error_reporting(0);

define("DB_ADDR","192.168.100.11");
define("DB_PORT", "3306");
define("DB_USER","root");
define("DB_PASS","root");
include 'db.php';
function is_mw($database_name){
	$conn2 = mysqli_connect(DB_ADDR,DB_USER,DB_PASS, $database_name);

	$res = $conn2->query("select 1 from consult");
	if ($res->num_rows > 0) {
		return true;
	}else{
		return false;
	}
}

$TOTAL_PASYENTE = 0;
$TOTAL_AVERAGE_DAILY_CONSULT = 0;
$TOTAL_CONSULT = 0;
$sd = "2019-01-01";
$ed = "2019-07-19";
foreach ($dbarray as $key => $value) {
    $dbname = $value;
    echo $dbname . PHP_EOL;
	$qry = "SELECT 1 from consult limit 1";
	$qry_all_pt = is_mw($dbname) ? "select count(*) as ct from patient where created_at between date('".$sd."') and date('".$ed."')" : "select count(*) as ct from m_patient where registration_date between date('".$sd."') and date('".$ed."')";

	$conn = mysqli_connect(DB_ADDR,DB_USER,DB_PASS,$dbname);
    $res = $conn->query($qry_all_pt);
    $r['ct'] = 0;
    if ($res->num_rows > 0) {
		while ($r1 = $res->fetch_assoc()) {
			$TOTAL_PASYENTE += $r1['ct'];
		}
    }
   


    $qry_all_pt = is_mw($dbname) ? "SELECT count(*) as ct FROM consult where created_at between date('".$sd."') and date('".$ed."')" : " SELECT count(*) as ct FROM m_consult where consult_date between date('".$sd."') and date('".$ed."')";

	$conn = mysqli_connect(DB_ADDR,DB_USER,DB_PASS,$dbname);
    $res = $conn->query($qry_all_pt);
    $r['ct'] = 0;
    if ($res->num_rows > 0) {
		while ($r2 = $res->fetch_assoc()) {
			$TOTAL_CONSULT += $r2['ct'];
		}
    }
    


    $qry_all_pt = is_mw($dbname) ? "select avg(ct) as ct from (select count(1) as ct from consult where created_at between date('".$sd."') and date('".$ed."')  group by date(created_at)) MyTable" : " select avg(ct) as ct from (select count(1) as ct from m_consult where consult_timestamp between date('".$sd."') and date('".$ed."')  group by date(consult_timestamp)) MyTable";

	$conn = mysqli_connect(DB_ADDR,DB_USER,DB_PASS,$dbname);
    $res = $conn->query($qry_all_pt);
    $r['ct'] = 0;
    if ($res->num_rows > 0) {
		while ($r3 = $res->fetch_assoc()) {
			$TOTAL_AVERAGE_DAILY_CONSULT += $r3['ct'];
		}
    }
    



}
echo "TOTAL_AVERAGE_DAILY_CONSULT = $TOTAL_AVERAGE_DAILY_CONSULT" . PHP_EOL;
    echo "TOTAL_CONSULT = $TOTAL_CONSULT" . PHP_EOL;
    echo "TOTAL_PASYENTE = $TOTAL_PASYENTE" . PHP_EOL;
    
echo "x\07";
?>