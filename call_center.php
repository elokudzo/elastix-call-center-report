<?php
set_time_limit(0);
$conn = new mysqli('localhost', 'root', '', 'reportdb');
$done = 0;


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Elastix call center report generator</title>
<link rel="stylesheet" type="text/css" href="styles.css">
</head>

<body>

<h2><center>Elastix Call Center Report Generator</center></h2>

<div id="theform">
<p>CALL CENTER REPORT</p>
<form method="post" action="" enctype="multipart/form-data">
<input type="file" name="file" />
<br /><br>
<input type="submit" name="submit" value="Submit" />

</form>
</div>
<br>
<center>To download a sample file <a href="call_center.csv" download>click here</a></center>
<br>


<br>
<div id="mybody">
<?php
$newsql = $conn->query("TRUNCATE TABLE csvtbl") or die("truncate table did not work");

if(isset($_POST['submit']))

	{

$file = $_FILES['file']['tmp_name'];
	$handle = fopen($file,"r");
	

while (($fileop = fgetcsv($handle, 1000, ",")) !== FALSE) 
{
	

if(!isset($fileop[0]) ||  !isset($fileop[1]) || !isset($fileop[2])  )
{

echo '<script> alert("Error getting report,check your file and try again") </script>';
echo '<script language="JavaScript"> window.location.href="call_center.php" </script>';

}


$enddate = $fileop[0];
$num = $fileop[1];
$status = $fileop[2];
if(isset($fileop[3])){

echo '<script> alert("Error getting report,check your file and try again") </script>';
echo '<script language="JavaScript"> window.location.href="call_center.php" </script>';
}
if($fileop[0] != "End Date"){
$sql = $conn->query("INSERT INTO csvtbl(enddate,num,status) VALUES('$enddate','$num','$status')");
if(!$sql){
echo '<script> alert("Error getting report,check your file and try again") </script>';
echo '<script language="JavaScript"> window.location.href="cdr.php" </script>';

}


}

}

$sql2 = $conn->query("UPDATE csvtbl SET enddate = STR_TO_DATE(enddate, '%d/%m/%Y')");

$sql3=$conn->query("ALTER TABLE csvtbl ORDER BY enddate ASC");

$sql4 = $conn->query("SELECT DISTINCT enddate FROM csvtbl");

echo "<center><table width='500px' border='1'>
<tr>
<th>Dates</th>
<th>Successful Calls</th>
<th>Abandonned Calls</th></tr>";
$ok_sum=0;
$no_sum=0;
while($queryrow_date= $sql4->fetch_object()){

	echo "<tr><td>$queryrow_date->enddate</td>";
	$sql5= $conn->query("SELECT * FROM csvtbl WHERE  enddate= '$queryrow_date->enddate' AND status = 'Success'") or die("errrrrrrrrrrrrr");
	$ok = $sql5->num_rows; 
	$ok_sum += $ok;
	 echo "<td>$ok</td>";


$sql6= $conn->query("SELECT * FROM csvtbl WHERE  enddate= '$queryrow_date->enddate' AND status = 'Abandoned'") or die("errrrrrrrrrrrrr");
	$no = $sql6->num_rows;
	$no_sum += $no;
	 echo "<td>$no</td></tr>";



}
echo "<tr><td><b>Total</b></td><td><b>$ok_sum</b></td><td><b>$no_sum</b></td></tr>";
echo "</table></center><br><br>";
$done = 1;
	

}


if($done==1){
	echo("<script>alert(\"Done!\")</script>");
	}

?>
</div>
</body>
</html>