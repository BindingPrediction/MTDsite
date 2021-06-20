<html>
<title> Protein Molecule Binding Sites Prediction </title>
<!-- Yuedong Yang,  4/12/2010 -->
<META name=keywords content="Protein Design, Sequence Profile">
<head>
<center>
<h1> <img src =../../images/tianhe2.jpg width=80 height=40 align=middle><font color=green > Tianhe2-MTDsites </font>: Protein Molecule Binding Sites Prediction</h1>
<b> (ACADEMIC USE ONLY)<br><br> </b>
</center>
</head>

<body bgcolor="cornsilk">
<center>
<p>

<?php
$method = "MTDsites";
$is_query = $_REQUEST['IS_QUERY'];

# debug
#$is_query = "DEBUG";

if($is_query == ""){
#<a href=queue.php>Check the current Queue to prevent DUPLICATE submits</a>
?>
<FORM enctype='multipart/form-data' ACTION="index.php" METHOD="post">
<table border="0" >
<tr>
   <td align=left> Jobid (if resubmitted): </td>
   <td align=left> <INPUT TYPE="TEXT" VALUE="" NAME="JOBID" SIZE=40></td>
</tr>

<tr>
   <td align=left> E-mail address<br> (Mandatory if submitting mutliple queries): </td>
   <td align=left> <INPUT TYPE="TEXT" VALUE="" NAME="REPLY-EMAIL" SIZE=40></td>
</tr>
<tr>
   <td align=left> Query Name(optional):</td>
   <td align=left> <input type="text" value="" name="QNAME" size=15></td>
</tr>

<tr>
   <td align=left> <Strong>Input your Fasta file for a Protein chain:</Strong></td>
 	<td align=left> <input type="file" name="FQUERY" size=30></td>
</tr> 
<tr>
</tr>
<?php
#<tr>
#<td colspan=2>
#    <textarea  name="SEQUENCE" rows=8 cols=80></textarea>
#</td>
#</tr>
?>
</table>
<br>
<INPUT TYPE="hidden" NAME="IS_QUERY" VALUE="Yes">
<br>
<br>
<!--<INPUT TYPE="hidden" NAME="METHOD" VALUE="SPIDER3">-->
<INPUT TYPE="SUBMIT" VALUE="Submit ">
<INPUT TYPE="RESET"  VALUE="Clear">
</FORM> 
<br>

<?php

# (<font color=red><b>At most 100 Protein sequence at a time</b> in <a href=http://en.wikipedia.org/wiki/FASTA_format>FASTA format</a></font>: <a href=../misc/pdbs.seq> an EXAMPLE </a>)<br>
# <br>
# <b> (<font color=red>Users submitting more than 100 jobs might be banned due to limited resources. Please use standalone version for such cases!</font>)<br><br> </b>
# (Refer <a href=http://web.expasy.org/translate/> ExPASy </a> to translate from DNA to protein if you only have DNA or RNA sequence</font>)<br><br>
# (Refer <a href=../misc/aa321.htm> Convert 3-character amino acids to one-character</a> if you have only amino acids in 3-characters) <br> <br>

} else {
	$fquery_dir = $_FILES['FQUERY']['tmp_name'];
	$fquery_name = $_FILES['FQUERY']['name'];
	$error = $_FILES['FQUERY']['error'];
        #echo "fd:$fquery_dir\n";
	#echo "fn:$fquery_name\n";
	#echo "error:$error";
	##echo phpinfo();
	if($is_query == "DEBUG"){
		$fquery_dir = "/tmp/1.jpg";
		$fquery_name = "test";
	}

	if($fquery_name == "" && $fquery == ""){
		echo "<font size=10 color=red> Nothing has been input!<br>\n</font><br><br>";
		return;
	}
	
	include("../misc/misc.php");
	$qname = $_REQUEST['QNAME'];
	$R_email = $_REQUEST['REPLY-EMAIL'];
	$jobid = $_REQUEST['JOBID'];
	$hostnm = getremotehost();
	$date = date("Y-m-d/H:m:s");
#
	if($R_email == "") {$R_email = "unknown";}
	if($qname == "") {$qname = "unknown";}
#
	$fquery_bn = "query_fas";
# write the queue_info
	if($jobid == "") {
		$jobid = substr(strrev(uniqid()), 0, 7);
		$rundir = "../info/$method/$jobid";
		mkdir("$rundir"); chmod("$rundir", 0777);
		$fquery_loc = "$rundir/$fquery_bn";
		$fweb = "$rundir/result.htm";
		#echo "name:$fquery_dir";
		exec("cp $fquery_dir $fquery_loc 2>&1",$output, $return_val);
		#var_dump($output);
		exec("chmod 777 $fquery_loc");
	}
	$fp = fopen("log/lockqueue", "w");
	fclose($fp);

	$fque = "log/_queue.dat";
	$fp = fopen($fque, "a");
	if($fp == NULL){echo "fail to open: $fque\n"; return;}
	$jobinfo = "$method $jobid; $hostnm $date $R_email $fquery_name";
	fprintf($fp, "JOBID: %s\n", $jobinfo);
	fclose($fp);

	exec("rm -f log/lockqueue");
#
# build a temporary web file
	$fp = fopen($fweb, "w");
	chmod($fweb, 0666);
	chmod($fp,0777);
	fprintf($fp, "<html><title>Result</title>\n");
	fprintf($fp, "<meta http-equiv='refresh' content='60;url=result.htm'>");
	fprintf($fp, "<body><b>Your job is being  processed</b><br><br>\n");
	fprintf($fp, "Please wait for 10 minutes or more depending on how large your file is and how busy the server is.<br>\n<br>\n");
	fprintf($fp, "JOBID: %s<br>\n", $jobid);
	fprintf($fp, "Date: %s<br>\n", $date);
	fprintf($fp, "TARGET: %s<br>\n", $qname);
	fprintf($fp, "File: %s<br>\n", $fquery_name);
	$pred_bn="query_fas.pred";
	exec("chmod 777 $pred_bn");
	fprintf($fp, "<a href=%s target=\"show\">Predict-Result</a>", $pred_bn);
	fprintf($fp, "</body></html>\n");
	fclose($fp);
#
# build the files
	$pr = fopen("$rundir/$pred_bn", "w");
	chmod($pr,0777);
	exec("chmod 777 $rundir/query_fas.pred");
	fprintf($pr, "please go back or refresh this page until it shows the correct prediction result, it may take 10 minutes or more");
	$fp = fopen("$rundir/query1.info", "w");
	fprintf($fp, "JOBID: %s\n", $jobid);
	fprintf($fp, "FQNAME: %s\n", $fquery_name);
	fprintf($fp, "Email: %s\n", $R_email);
	fprintf($fp, "QUERY: %s\n", $qname);
	fprintf($fp, "INFO: %s %s\n", $hostnm, $date);
	fclose($fp);
#
	echo "<font size=6><a href=$rundir/result.htm>Click the link</a> for results</font>";
	writeLOG("../LOG-services", $jobinfo);
	echo "<br>\n";
}
?>
<br>
<?php
#<strong><a href=/yueyang/download/index.php?Download=SPOT-Seq-DNA.tbz>Locally Running Version for Download</a></strong>
?>
</center>
<p>
<b><br>Reference:<br> </b>
(To appear)
<br>
<br>

<p>

<center>
<font color=red><b>*** The use of this server means that you have read and accepted</b></font>
<a href="/Softwares-Services_files/notices.htm"> Disclaims, Warranties, Legal Notices</a>
</p>
<p> <font size=1> Copyright @ 2019 Sun Yat-sen University</font></p>

</center>
<?php include("../misc/misc.php"); contact_admin();?>

</body>
</html>
