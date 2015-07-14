<?php

$app = new \OCA\DeltaSync\AppInfo\Application();
$app->registerRoutes($this, [
	'routes' => [
		['name' => 'upload_api#get_zsync', 'url' => '/api/0.0.1/zsync/{path}', 'verb' => 'GET', 'requirements' => ['path' => '.+']],
		['name' => 'upload_api#start', 'url' => '/api/0.0.1/upload/start/{path}', 'verb' => 'POST', 'requirements' => ['path' => '.+']],
		['name' => 'upload_api#move', 'url' => '/api/0.0.1/upload/move/{path}', 'verb' => 'PATCH', 'requirements' => ['path' => '.+']],
		['name' => 'upload_api#add', 'url' => '/api/0.0.1/upload/add/{path}', 'verb' => 'PATCH', 'requirements' => ['path' => '.+']],
		['name' => 'upload_api#done', 'url' => '/api/0.0.1/upload/done/{path}', 'verb' => 'POST', 'requirements' => ['path' => '.+']],

	]
]);
