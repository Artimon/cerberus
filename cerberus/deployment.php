<?php

/**
 * Handles file comparison for deployment.
 */
class Deployment {
	/**
	 * @var Ftp
	 */
	private $ftp;

	/**
	 * @var string
	 */
	private $root = './';

	/**
	 * @var array
	 */
	private $configs;

	/**
	 * @var array
	 */
	private $folders;

	/**
	 * @var DeployFile[]
	 */
	private $deployFiles;

	/**
	 * @return Ftp
	 */
	public function ftp() {
		if ($this->ftp === null) {
			$this->ftp = new Ftp();
		}

		return $this->ftp;
	}

	/**
	 * @param string $directory
	 * @return deployment
	 */
	public function setRoot($directory) {
		$this->root = $directory;
		$this->folders = array();
		$this->configs = array();

		return $this;
	}

	/**
	 * @param string $generator
	 * @param string $target
	 * @return deployment
	 */
	public function addConfig($generator, $target) {
		$this->configs[$generator] = $target;

		return $this;
	}

	/**
	 * @param string $folderName
	 * @param bool $filesOnly
	 * @return deployment
	 */
	public function addFolder($folderName, $filesOnly = false) {
		$this->folders[$folderName] = $filesOnly;

		return $this;
	}

	/**
	 * @param string $directoryPath
	 * @return deployment
	 */
	public function cleanDirectory($directoryPath) {
		$directoryResource = opendir($directoryPath);

		while ($fileName = readdir($directoryResource)) {
			if (strpos($fileName, '.') === 0) {
				continue;
			}

			$filePath = $this->filePath($directoryPath, $fileName);
			unlink($filePath);
		}

		closedir($directoryResource);

		return $this;
	}

	/**
	 * @return deployment
	 */
	public function generateConfigs() {
		foreach ($this->configs as $generator => $target) {
			ob_start();

			include $generator;

			$config = ob_get_clean();

			$currentConfig = file_get_contents($target);
			if ($config === $currentConfig) {
				continue;
			}

			$handle = fopen($target, 'w');
			fwrite($handle, $config);
			fclose($handle);
		}

		return $this;
	}

	/**
	 * @param array $login
	 * @return DeployFile[]
	 */
	public function deployFiles(array $login) {
		$this->deployFiles = array();

		set_time_limit(60);

		$this->generateConfigs();

		$this->ftp()->connect($login['domain'], $login['user'], $login['password']);
		foreach ($this->folders as $directoryPath => $filesOnly) {
			$this->collectLocalFiles($directoryPath, $filesOnly);
			$this->collectFtpFiles($directoryPath);
		}
		$this->ftp()->disconnect();

		return $this->deployFiles;
	}

	/**
	 * @param string $directoryPath
	 * @param string $fileName
	 * @return mixed|string
	 */
	protected function filePath($directoryPath, $fileName) {
		$filePath = $directoryPath . '/' . $fileName;
		$filePath = str_replace('./', '', $filePath);

		return $filePath;
	}

	/**
	 * @param string $directoryPath
	 * @param bool $filesOnly
	 */
	protected function collectLocalFiles($directoryPath, $filesOnly) {
		$directoryResource = opendir($directoryPath);

		while ($fileName = readdir($directoryResource)) {
			if (strpos($fileName, '.') === 0) {
				continue;
			}

			$filePath = $this->filePath($directoryPath, $fileName);
			$ftpFilePath = $this->root . $filePath;

			if (is_dir($filePath)) {
				if (!$filesOnly) {
					$this->collectLocalFiles($filePath, false);
				}

				continue;
			}

			$liveTimestamp = $this->ftp()->fileModTime($ftpFilePath);
			$localTimestamp = filemtime($filePath);

			$deployFile = new DeployFile();
			$deployFile
				->setLocalPath($filePath)
				->setRemotePath($ftpFilePath)
				->determineAction($localTimestamp, $liveTimestamp);

			$this->deployFiles[$ftpFilePath] = $deployFile;
		}

		closedir($directoryResource);
	}

	/**
	 * @param string $directoryPath
	 */
	protected function collectFtpFiles($directoryPath) {
		$ftpFileList = $this->ftp()->folderContent($directoryPath);

		foreach ($ftpFileList as $ftpFilePath) {
			// Weak approach to file detection.
			if (substr($ftpFilePath, -4, 1) !== '.') {
				continue;
			}

			$ftpFilePath = "./{$ftpFilePath}";
			if (array_key_exists($ftpFilePath, $this->deployFiles)) {
				continue;
			}

			$deployFile = new DeployFile();
			$deployFile
				->setLocalPath($ftpFilePath . ' (removed)')
				->setRemotePath($ftpFilePath)
				->setActionDelete();

			$this->deployFiles[$ftpFilePath] = $deployFile;
		}
	}
}