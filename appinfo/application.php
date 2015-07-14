<?php

namespace OCA\DeltaSync\AppInfo;

use \OCP\AppFramework\App;
use \OCA\DeltaSync\Controller\UploadApiController;

class Application extends App {
	public function __construct(array $urlParams=[]) {
		parent::__construct('deltasync', $urlParams);

		$container = $this->getContainer();

		/**
		 * Controllers
		 */
		$container->registerService('UploadApiController', function($c) {
			return new UploadApiController(
				$c->query('AppName'),
				$c->query('Request'),
				$c->query('UserFolder')
			);
		});

		
		$container->registerService('UserFolder', function($c) {
			return $c->query('ServerContainer')->getUserFolder();
		});
	}
}
