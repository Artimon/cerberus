<?php

class UploadFile {
	/**
	 * @return UploadFile
	 */
	public static function getInstance() {
		return new self();
	}

	/**
	 * @param bool $success
	 */
	public function response($success) {
		header("Content-type: application/json");

		echo json_encode(
			array('success' => (bool)$success)
		);
	}

	/**
	 * @param array $login
	 * @param bool $debugMode
	 * @return void
	 */
	public function commit(array $login, $debugMode) {
		if (!$debugMode) {
			return;
		}

		$domain = $login['domain'];
		$cache = new Cache($domain);
		$cache->prepare();

		$ftp = new Ftp();
		if (!$ftp->connect($domain, $login['user'], $login['password'])) {
			$this->response(false);
			return;
		}

		$deployFile = new DeployFile();
		$deployFile
			->setRemotePath($_POST['remotePath'])
			->setLocalPath($_POST['localPath'])
			->setAction($_POST['action']);

		$remotePath = $deployFile->remotePath();
		if ($deployFile->hasDeleteAction()) {
			$success = $ftp->delete($remotePath);

			if ($success) {
				$cache->remove($remotePath);
			}
		}
		elseif ($deployFile->hasDirCreateAction()) {
			$success = $ftp->createDirectory($remotePath);
		}
		else {
			$success = $ftp->upload(
				$remotePath,
				$deployFile->localPath()
			);

			if ($success) {
				// Update next time.
				$cache->remove($remotePath);
			}
		}

		$this->response($success);
		$ftp->disconnect();

		$cache->write();
	}
}