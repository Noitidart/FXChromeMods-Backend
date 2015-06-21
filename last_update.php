<?php
	// connect to database
	require('mysql_db.php');

	$mysql_host = 'mysql7.000webhost.com';
	$mysql_database = 'a4606090_main';
	$mysql_user = 'a4606090_noit';
	$mysql_password = 'Datemppw87';

	$DB = new mysql_db();
	$connectid = $DB->sql_connect($mysql_host, $mysql_user , $mysql_password, $mysql_database);
	
	$query_1 = $DB->query('SELECT update_time FROM update_time WHERE id=0');
	
	$row = mysql_fetch_assoc($query_1);
	$modsJson = array(
		'update_time' => $row['update_time']
	);
		
	$modsJson = json_encode($modsJson);
	print $modsJson; exit();
?>