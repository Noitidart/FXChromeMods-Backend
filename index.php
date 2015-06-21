<!doctype html>
<html ng-app="fxchrome">
	<head>
		<title>FXChromeMods Backend</title>
		<meta http-equiv="Content-Type" content="text/html; charset=us-ascii" />
		<meta name="description" content="Manage the modifications loaded into FXChromeMods" />
		<link rel="shortcut icon" href="icon32.png" type="image/png" />
		<script src="angular-1-3-16_min.js"></script>
		<script>
			var localesBlankJSON = {
				'en-US': '',
				'en-GB': ''
			};
			var localesBlankSTRINGIFY = JSON.stringify(localesBlankJSON);
			angular.module('fxchrome', [])
			  .controller('FormController', function() {
				var THIS = this;
				THIS.ids = [];
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
				
				THIS.nextId = 0;
				for (var i=0; i<THIS.mods.length; i++) {
					THIS.ids.push(THIS.mods[i].id);
					if (THIS.mods[i].id >= THIS.nextId) {
						THIS.nextId = THIS.mods[i].id + 1;
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
				
				THIS.info = function() {
					console.info(THIS.mods);
				};
			  });


		</script>
	</head>
	<body>
		<form ng-controller="FormController as fc">
			<label>
				Username:
			</label> 
			<input type="text" name="username">
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
			</div>
			<input type="button" value="Add" ng-click="fc.add()">
			<input type="button" value="Info" ng-click="fc.info()">
		</form>
	</body>
</html>

