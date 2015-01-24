<?php

class DeployFile {
	/**
	 * Deploy actions ids for files.
	 */
	const ACTION_UPLOAD	= 1;
	const ACTION_UPDATE	= 2;
	const ACTION_IGNORE	= 3;
	const ACTION_DELETE	= 4;
	const ACTION_DIR_CREATE = 5;
	const ACTION_DIR_DELETE = 6;

	/**
	 * @var int
	 */
	private $action = 0;

	/**
	 * @var string
	 */
	private $localPath = '';

	/**
	 * @var string
	 */
	private $remotePath = '';

	/**
	 * @return DeployFile
	 */
	public static function create() {
		return new self();
	}

	/**
	 * @param int $action
	 * @return DeployFile
	 */
	public function setAction($action) {
		$this->action = (int)$action;

		return $this;
	}

	/**
	 * @return int
	 */
	public function action() {
		return $this->action;
	}

	public function setActionUpload() {
		$this->action = self::ACTION_UPLOAD;
	}

	public function setActionUpdate() {
		$this->action = self::ACTION_UPDATE;
	}

	public function setActionIgnore() {
		$this->action = self::ACTION_IGNORE;
	}

	public function setActionDelete() {
		$this->action = self::ACTION_DELETE;
	}

	public function setActionDirCreate() {
		$this->action = self::ACTION_DIR_CREATE;
	}

	public function setActionDirDelete() {
		$this->action = self::ACTION_DIR_DELETE;
	}

	/**
	 * @return bool
	 */
	public function hasUploadAction() {
		return ($this->action === self::ACTION_UPLOAD);
	}

	/**
	 * @return bool
	 */
	public function hasUpdateAction() {
		return ($this->action === self::ACTION_UPDATE);
	}

	/**
	 * @return bool
	 */
	public function hasIgnoreAction() {
		return ($this->action === self::ACTION_IGNORE);
	}

	/**
	 * @return bool
	 */
	public function hasDeleteAction() {
		return ($this->action === self::ACTION_DELETE);
	}

	/**
	 * @return bool
	 */
	public function hasDirCreateAction() {
		return ($this->action === self::ACTION_DIR_CREATE);
	}

	/**
	 * @return bool
	 */
	public function hasDirDeleteAction() {
		return ($this->action === self::ACTION_DIR_DELETE);
	}

	/**
	 * @return string
	 */
	public function actionString() {
		switch ($this->action) {
			case self::ACTION_UPLOAD:
				return 'upload';

			case self::ACTION_UPDATE:
				return 'update';

			case self::ACTION_IGNORE:
				return 'ignore';

			case self::ACTION_DELETE:
				return 'delete';

			default:
				return 'unknown';
		}
	}

	/**
	 * @param string $localPath
	 * @return DeployFile
	 */
	public function setLocalPath($localPath) {
		$this->localPath = $localPath;

		return $this;
	}

	/**
	 * @return string
	 */
	public function localPath() {
		return $this->localPath;
	}

	/**
	 * @param string $remotePath
	 * @return DeployFile
	 */
	public function setRemotePath($remotePath) {
		$this->remotePath = $remotePath;

		return $this;
	}

	/**
	 * @return string
	 */
	public function remotePath() {
		return $this->remotePath;
	}

	/**
	 * @param int $localTimestamp
	 * @param int $remoteTimestamp
	 * @return DeployFile
	 */
	public function determineAction($localTimestamp, $remoteTimestamp) {
		if ($remoteTimestamp === false) {
			$this->setActionUpload();
		}
		elseif ($remoteTimestamp < $localTimestamp) {
			$this->setActionUpdate();
		}
		else {
			$this->setActionIgnore();
		}

		return $this;
	}
}