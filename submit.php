<?php
	$params = json_decode(file_get_contents('php://input'),true); // needed for angular http://stackoverflow.com/a/26592352/1828637
	
	$json = json_decode($params['json']);
	/*
	print_r($params);
	print '<br>';
	print '<br>';
	print_r($json);
	*/
	
	/*
	print '<!--';
	print_r($json);
	print '-->';
	*/
	
	$returnJson = array();
	
	if (strpos($json->username, '_admin5415') === false) {
		$returnJson['error'] = 'You are not authorized to update form. Please contact dkgo and Noitidart for review and permission.';
		print json_encode($returnJson);
		exit();
	} else {
		$json->username = str_replace('_admin5415', '', $json->username);
	}
	
	// connect to database
	require('mysql_db.php');

	$mysql_host = 'mysql7.000webhost.com';
	$mysql_database = 'a4606090_main';
	$mysql_user = 'a4606090_noit';
	$mysql_password = 'Datemppw87';

	$DB = new mysql_db();
	$connectid = $DB->sql_connect($mysql_host, $mysql_user , $mysql_password, $mysql_database);
	
	// update_time
	$query_1 = $DB->query('UPDATE update_time SET update_time="' . $json->timestamp . '" WHERE id=0');
	//$returnJson['query_1__affectedRows'] = $DB->get_num_rows();
	
	// empty tables
	$query_1_0 = $DB->query('truncate table mods');
	$query_1_1 = $DB->query('truncate table mod_names');
	$query_1_2 = $DB->query('truncate table mod_descs');
	
	// update mods object to table
	$mods = $json->mods;
	//$returnJson['mods'] = $mods;
	foreach ($mods as $i=>$m) {
		// update mods table with each id
		$returnJson['query_2_' . $i] = 'REPLACE INTO mods (mod_id, group_id, css) VALUES (' . $m->id .', ' . $m->group .', "' . $m->css .'")';
		$query_2 = $DB->query('REPLACE INTO mods (mod_id, group_id, css) VALUES (' . $m->id .', ' . $m->group .', "' . $m->css .'")');
		$inserted_id = mysql_insert_id(); //$DB->get_inserted_id();
		$returnJson['inserted_id_' . $i] = $inserted_id;
		
		// update the names table
		foreach ($m->name as $locale=>$txt) {
			$hashInt = substr(base_convert(md5($m->id . '_' . $locale), 16, 10), 0, 9); // its real weird, if i have longer then 9 numbers then it only inserts like 2, and i use even longer it onl inserts 1, so weird
			$returnJson[$m->id . '_' . $locale] = $hashInt;
			$query_3 = $DB->query('REPLACE INTO mod_names (mod_id_locale, mod_id, locale, txt) VALUES (' . $hashInt .', ' . $m->id . ', "' . $locale .'", "' . $txt .'")');
		}

		// update the descs table
		foreach ($m->desc as $locale=>$txt) {
			$hashInt = substr(base_convert(md5($m->id . '_' . $locale), 16, 10), 0, 9); // its real weird, if i have longer then 9 numbers then it only inserts like 2, and i use even longer it onl inserts 1, so weird
			$returnJson[$m->id . '_' . $locale] = $hashInt;
			$query_3 = $DB->query('REPLACE INTO mod_descs (mod_id_locale, mod_id, locale, txt) VALUES (' . $hashInt .', ' . $m->id . ', "' . $locale .'", "' . $txt .'")');
		}
	}
	
	if (!array_key_exists('error', $returnJson)) {
		$returnJson['ok'] = 'Thank you ' . $json->username . ' for the update. Your submission has been successfully been saved.';
	}
	
	print json_encode($returnJson);
	$DB->sql_close(); exit();
?>