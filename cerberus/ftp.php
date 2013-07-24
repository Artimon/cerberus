<?php

class Ftp {
	/**
	 * @var resource|bool
	 */
	private $ftpStream = false;

	/**
	 * @return bool
	 */
	public function isConnected() {
		return ($this->ftpStream !== false);
	}

	/**
	 * @param string $domain
	 * @param string $user
	 * @param string $password
	 * @return bool
	 */
	public function connect($domain, $user, $password) {
		$this->ftpStream = ftp_connect($domain);

		if (!$this->isConnected()) {
			return false;
		}

		return ftp_login(
			$this->ftpStream,
			$user,
			$password
		);
	}

	/**
	 * @return bool
	 */
	public function disconnect() {
		if ($this->isConnected()) {
			return ftp_close($this->ftpStream);
		}

		return true;
	}

	/**
	 * @param string $remotePath
	 * @return array
	 */
	public function folderContent($remotePath) {
		if (!$this->isConnected()) {
			return array();
		}

		return (array)ftp_nlist($this->ftpStream, "./httpdocs/{$remotePath}");
	}

	/**
	 * @param string $localPath
	 * @return bool|int
	 */
	public function fileModTime($localPath) {
		$timestamp = false;

		if ($this->isConnected()) {
			$timestamp = ftp_mdtm($this->ftpStream, $localPath);
			if ($timestamp === -1) {
				$timestamp = false;
			}
		}

		return $timestamp;
	}

	/**
	 * @param string $remotePath
	 * @param string $localPath
	 * @return bool
	 */
	public function upload($remotePath, $localPath) {
		return ftp_put(
			$this->ftpStream,
			$remotePath,
			$localPath,
			FTP_BINARY
		);
	}

	/**
	 * @param string $remotePath
	 * @return bool
	 */
	public function delete($remotePath) {
		return ftp_delete($this->ftpStream, $remotePath);
	}
}