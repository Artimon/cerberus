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

		$ftp = new Ftp();
		if (!$ftp->connect($login['domain'], $login['user'], $login['password'])) {
			$this->response(false);
			return;
		}

		$deployFile = new DeployFile();
		$deployFile
			->setRemotePath($_POST['remotePath'])
			->setLocalPath($_POST['localPath'])
			->setAction($_POST['action']);

		if ($deployFile->hasDeleteAction()) {
			$success = $ftp->delete(
				$deployFile->remotePath()
			);
		}
		else {
			$success = $ftp->upload(
				$deployFile->remotePath(),
				$deployFile->localPath()
			);
		}

		$this->response($success);
		$ftp->disconnect();
	}
}