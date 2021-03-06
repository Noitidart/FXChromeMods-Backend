<?php
	// connect to database
	require('mysql_db.php');

	$mysql_host = 'mysql7.000webhost.com';
	$mysql_database = 'a4606090_main';
	$mysql_user = 'a4606090_noit';
	$mysql_password = 'Datemppw87';

	$DB = new mysql_db();
	$connectid = $DB->sql_connect($mysql_host, $mysql_user , $mysql_password, $mysql_database);
	
	$query_1 = $DB->query('SELECT * FROM mods');
	$numRows = $DB->get_num_rows($query_1);
	
	$modsJson = array();
	
	while ($row = mysql_fetch_assoc($query_1)) {
		$modsJson[$row['mod_id']] = array(
			'id' => $row['mod_id'],
			'group' => $row['group_id'],
			'css' => $row['css'],
			'name' => array(),
			'desc' => array()
		);
	}
	
	$query2 = $DB->query('SELECT tn.mod_id, tn.locale, tn.txt AS ntxt, td.txt AS dtxt FROM mod_names AS tn INNER JOIN mod_descs AS td ON tn.mod_id_locale = td.mod_id_locale');
	while ($row = mysql_fetch_assoc($query2)) {
		//print_r($row);
		//print '<br>';
		$modsJson[$row['mod_id']]['name'][$row['locale']] = $row['ntxt'];
		$modsJson[$row['mod_id']]['desc'][$row['locale']] = $row['dtxt'];
	}
	
	$modsJson = json_encode(array_values($modsJson));
	
	//print $modsJson;
?>
<!doctype html>
<html ng-app="fxchrome">
	<head>
		<title>FXChromeMods Backend</title>
		<meta http-equiv="Content-Type" content="text/html; charset=us-ascii" />
		<meta name="description" content="Manage the modifications loaded into FXChromeMods" />
		<link rel="shortcut icon" href="https://github.com/Noitidart/FXChromeMods-Backend/blob/master/icon32.png?raw=true" type="image/png" />
		<script src="angular-1-3-16_min.js"></script>
		<script src="angular-cookie-1-3-16_min.js"></script>
		<script>
			var localesBlankJSON = { // update this obj with keys of locales i want supported
				'en-US': '',
				'en-GB': ''
			};
			var localesBlankSTRINGIFY = JSON.stringify(localesBlankJSON);
			
			angular.module('fxchrome', [/*'ngCookie'*/])
			  .controller('FormController', ['$http', /*'$cookies',*/ function($http/*, $cookies*/) {

				var THIS = this;
				THIS.username = '';
				THIS.ids = [];
				THIS.mods = <?php print $modsJson ?>;
				/*
				THIS.mods = [
					{
						id: 0,
						name: {
							'en-US': 'New Tab Plus',
							'en-GB': 'GB New Tab Plus'
						},
						desc: {
							'en-US': 'Add a plus button inside the new tab button',
							'en-GB': 'GB Add a plus button inside the new tab button'
						},
						css: 'css1',
						group: 0
					},
					{
						id: 1,
						name: {
							'en-US': 'Thin Bookmarks',
							'en-GB': 'GB Thin Bookmarks'
						},
						desc: {
							'en-US': 'Make bookmarks bar thinner',
							'en-GB': 'GB Make bookmarks bar thinner'
						},
						css: '2222',
						group: 1
					}
				];
				*/
				THIS.nextId = 0;
				for (var i=0; i<THIS.mods.length; i++) {
					THIS.ids.push(THIS.mods[i].id);
					if (THIS.mods[i].id >= THIS.nextId) {
						THIS.nextId = parseInt(THIS.mods[i].id) + 1;
					}
					for (var locale in THIS.mods[i].name) {
						if (!(locale in localesBlankJSON)) {
							//local no longer supported, so delete the key
							delete THIS.mods[i].name[locale];
						}
					}
					for (var locale in localesBlankJSON) {
						if (!(locale in THIS.mods[i].name)) {
							THIS.mods[i].name[locale] = '';
						}
					}
				}
				for (var i=0; i<THIS.mods.length; i++) {
					if (THIS.ids.indexOf(THIS.mods[i].group) == -1) {
						THIS.mods[i].group = THIS.mods[i].id;
					}
				}
				
				THIS.add = function() {
					THIS.ids.push(THIS.nextId);
					THIS.mods.push({
						id: THIS.nextId,
						name: JSON.parse(localesBlankSTRINGIFY),
						desc: JSON.parse(localesBlankSTRINGIFY),
						css: '',
						group: THIS.nextId
					});
					THIS.nextId++;
				};
				
				THIS.delete = function(id) {
					for (var i=0; i<THIS.mods.length; i++) {
						if (THIS.mods[i].id == id) {
							THIS.mods.splice(i, 1);
							return;
						}
					}
				};
				
				THIS.submit_update = function() {
					var update_obj = {
						username: THIS.username,
						timestamp: Date.now(),
						mods: THIS.mods
					};
					
					var json_str = angular.toJson(update_obj);
					console.info(json_str);

					$http.post('/submit.php', {
						json: json_str
					}).
					success(function(response) {
						console.log('got back succcessful status code, response:', response);
						if (typeof response != 'object') {
							alert('Update failed on server side, JSON object not returned, see browser console for more information.\n\n' + response);
						} else {
							if (response.error) {
								alert(response.error);
							} else {
								if (response.ok) {
									alert(response.ok);
								} else {
									alert('Update Successful');
								}
								window.location.reload(); // reload page, as if an id went was submited as something but ogt incremented due to duplicate, then the reloaded page will have the new id
							}
						}
					}).
					error(function(response) {
						//$scope.codeStatus = response || "Request failed";
						console.error('Connection Failed - See browser console for details on response object. Response:', response);
						alert('Connection Failed - See browser console for details on response object. ' + JSON.stringify(response));
					});
					
				};
				
				THIS.info = function() {
					console.info(THIS.mods);
				};
				
			  }]);


		</script>
	</head>
	<body>
		<form ng-controller="FormController as fc">
			<label>
				Username:
			</label> 
			<input type="text" name="username" ng-model="fc.username">
			<div>
				GUIDE:
				<ul>
					<li>
						To make something file browsable in the addon front end, just in clude <pre style="display:inline;">%FILE.PATH%</pre> or <pre style="display:inline;">%FILE.NAME%</pre> in the CSS of it.
					</li>
					<li>
						To make a select box in the addon front end, then set the group to the id of which you want it to belong.	
					</li>
			</div>
			<hr>
			<div ng-repeat="mod in fc.mods">
				<div>
					<span>
						ID
					</span>
					<span>
						{{mod.id}}
					</span>
					<span>
						<input type="button" ng-click="fc.delete(mod.id)" value="Delete">
					</span>
				</div>
				<div>
					<span>
						Group
					</span>
					<span>
						<select data-ng-model="mod.group" data-ng-options="id for id in fc.ids"></select>
					</span>
				</div>
				<div>
					<span>
						Name
					</span>
					<div ng-repeat="(key, value) in mod.name">
						<span>
							{{key}}
						</span>
						<span>
							<input type="text" ng-model="mod.name[key]">
						</span>
					</div>
				</div>
				<div>
					<span>
						Description
					</span>
					<div ng-repeat="(key, value) in mod.desc">
						<span>
							{{key}}
						</span>
						<span>
							<input type="text" ng-model="mod.desc[key]">
						</span>
					</div>
				</div>
				<div>
					<span>
					CSS:
					</span>
					<span>
						<textarea style="width:150px; height:75px;" ng-model="mod.css"></textarea>
					</span>
				</div>
				<hr style="width:200px; float:left;">
				<br>
			</div>
			<input type="button" value="Add" ng-click="fc.add()">
			<input type="button" value="Info" ng-click="fc.info()" style="display:none;">
			<br>
			<br>
			<input type="button" value="Update" ng-click="fc.submit_update()" style="width:200px;">
		</form>
	</body>
</html>

