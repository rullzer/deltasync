<?php

namespace OCA\DeltaSync\Controller;

use \OCP\AppFramework\ApiController;
use \OCP\IRequest;
use \OCP\AppFramework\Http;
use \OCP\AppFramework\Http\JSONResponse;
use \OCP\AppFramework\Http\DataResponse;
use \OCP\AppFramework\Http\NotFoundResponse;
use \OCP\Files\Folder;

class UploadApiController extends APIController {

	/** @var Folder */
	private $userFolder;

	public function __construct($appName, 
								IRequest $request,
								Folder $userFolder) {
		parent::__construct($appName, $request);

		$this->userFolder = $userFolder;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * Get the zsync file from the server
	 *
	 * @param string $path
	 */
	public function getZsync($path) {
		$path = $path . '.zsync';

		try {
			$node = $this->userFolder->get($path);
			$content = $node->getContent();
			return new \OCP\AppFramework\Http\DataDisplayResponse($content);
		} catch (\OCP\Files\NotFoundException $exception) {
			return new JSONResponse([], Http::STATUS_NOT_FOUND);
		} catch (\OCP\Files\NotPermittedException $e) {
			return new JSONResponse("WTF");
		}
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * Start delta sync upload
	 *
	 * @param string $path
	 * @param int $size the new size of the file
	 */
	public function start($path, $size) {
		$np = $path . '.new';
		$npf = $this->userFolder->getFullPath($np);

		try {
			$node = $this->userFolder->get($path);
		} catch (\OCP\Files\NotFoundException $exception) {
			return new JSONResponse([], Http::STATUS_NOT_FOUND);
		}

		$node->copy($npf);
		$node = $this->userFolder->get($np);

		$file = $node->fopen('r+');
		$res = ftruncate($file, $size);

		if (!$res) {
			//Truncate failed
		} 

		fclose($file);
		return new JSONResponse($size);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @param string $path
	 * @param int $from
	 * @param int $to
	 * @param int $size
	 */
	public function move($path, $from, $to, $size) {
		$np = $path . '.new';

		try {
			$node_orig = $this->userFolder->get($path);
			$node_new = $this->userFolder->get($np);
		} catch (\OCP\Files\NotFoundException $exception) {
			return new JSONResponse([], Http::STATUS_NOT_FOUND);
		}

		$file_orig = $node_orig->fopen('r');
		$file_new = $node_new->fopen('r+');

		fseek($file_orig, $from, SEEK_SET);
		fseek($file_new, $to, SEEK_SET);

		$data = fread($file_orig, $size);
		$res = fwrite($file_new, $data);

		return new JSONResponse($res);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @param string $path
	 * @param int $start
	 * @param int $size
	 * @param string $data
	 */
	public function add($path, $start, $size, $data) {
		$np = $path . '.new';

		try {
			$node_new = $this->userFolder->get($np);
		} catch (\OCP\Files\NotFoundException $exception) {
			return new JSONResponse([], Http::STATUS_NOT_FOUND);
		}

		$file_new = $node_new->fopen('r+');

		fseek($file_new, $start, SEEK_SET);

		$res = fwrite($file_new, $data, $size);

		return new JSONResponse($res);	
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @param string $path
	 */
	public function done($path) {
		//TODO: move file
		$np = $path . '.new';

		try {
			$node_new = $this->userFolder->get($np);
		} catch (\OCP\Files\NotFoundException $exception) {
			return new JSONResponse([], Http::STATUS_NOT_FOUND);
		}

		$hash = $node_new->hash('sha1');

		return new JSONResponse($hash);
	}
}
