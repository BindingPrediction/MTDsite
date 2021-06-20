<html>
<title>JOBS QUEUE</title>
<head>
<center>
</head>
<h1><font size=6 color=green>Jobs Queue</font><br></h1></center>
<body bgcolor="cornsilk">
<a href=./>Back to the server</a><br><br>
<?php
	$email = preg_replace('/\s+/', '', $_REQUEST['EMAIL']);
	$hostnm = gethostbyaddr($_SERVER['HTTP_X_FORWARDED_FOR']);
	if($hostnm == "") $hostnm = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	echo "You're from visiting from: <font color=red>$hostnm</font><br>";
	echo "<font color=green>(Below show jobs submitted from your current computer/IP or the provided email)<br><br></font>";
?>
<FORM enctype='multipart/form-data' METHOD="post">
<tr>
   <td align=left> E-mail: </td>
   <td align=left> <INPUT TYPE="TEXT" VALUE="<?php echo $email; ?>" NAME="EMAIL" SIZE=53></td>
</tr>
<br>
<tr>
   <td align=left> Nseq: </td>
   <td align=left> <INPUT TYPE="TEXT" VALUE="30" NAME="NSEQ" ></td>
</tr>
<INPUT TYPE="SUBMIT" VALUE="Submit">
<br>
<br>
<?php
	$n = $_REQUEST['NSEQ'];
	if($n == "") {$n = 30;}
	if($email == "") {$email='UNK';}
	system("../misc/readque2.py log/_queue.dat 0 $hostnm $email");
	echo "<br>\n";
	echo "<font color=green><b>Your Recently Finished/Processing Records(name, sequence)<br>(the first one may be still in processing):</b></font><br><br>\n";
	system("../misc/readque2.py log/_queue.his $n $hostnm $email");
#		echo "../misc/readque2.py log/_queue.his 100 $hostnm $email<br>";
#		echo "<a href=?n=500>More..</a>\n";
	echo "<br>\n";
?>
</body>
</html>
