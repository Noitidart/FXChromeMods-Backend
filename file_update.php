<?php

	$localesBlankJSON = array(// update this obj with keys of locales i want supported
		'en-US' => array(),
		'en-GB' => array()
	);

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
	$update_time = $row['update_time'];
	
	$query2 = $DB->query('SELECT * FROM mods');
	//$numRows = $DB->get_num_rows($query2);
	
	while ($row = mysql_fetch_assoc($query2)) {
		$modsJson[$row['mod_id']] = array(
			'dtd' => $row['mod_id'],
			'group' => $row['group_id'],
			'css' => $row['css']
		);
	}

	$dtd = $localesBlankJSON;
	
	$query3 = $DB->query('SELECT tn.mod_id, tn.locale, tn.txt AS ntxt, td.txt AS dtxt FROM mod_names AS tn INNER JOIN mod_descs AS td ON tn.mod_id_locale = td.mod_id_locale');
	while ($row = mysql_fetch_assoc($query3)) {
		/*
		print_r($row);
		print '<br>';
		*/
		$dtd[$row['locale']][] = '<!ENTITY mods.name.' . $row['mod_id'] . ' \'' . $row['ntxt'] . '\'>';
		$dtd[$row['locale']][] = '<!ENTITY mods.desc.' . $row['mod_id'] . ' \'' . $row['dtxt'] . '\'>';
	}
	/*
	print '<br>';
	print_r($dtd);
	print '<br>';
	print '<br>';
	*/
	foreach ($dtd as $k=>$v) {
		$dtd[$k] = implode('', $v);
	}
	
	$modsJson = json_encode(array(
		'update_time' => $update_time,
		'mods' => array_values($modsJson)
	));
	$dtdsJson = json_encode($dtd);
	
	
	$returnJson['mods.json'] = $modsJson;
	$returnJson['mods.dtd'] = $dtdsJson;
	
	print json_encode($returnJson); exit();
?>