<?php

class Cache {
	/**
	 * @var string
	 */
	private $filename = '';

	/**
	 * @var null|array
	 */
	private $fileModTimes;

	/**
	 * @param string $domain
	 */
	public function __construct($domain) {
		$this->filename = dirname(__FILE__) . '/cache/' . $domain;
	}

	public function prepare() {
		$data = @file_get_contents($this->filename);
		$this->fileModTimes = $data
			? json_decode($data, true)
			: array();
	}

	public function write() {
		file_put_contents(
			$this->filename,
			json_encode($this->fileModTimes)
		);
	}

	/**
	 * @param string $path
	 * @param int $time
	 */
	public function add($path, $time) {
		$this->fileModTimes[$path] = $time;
	}

	/**
	 * @param string $path
	 */
	public function remove($path) {
		unset($this->fileModTimes[$path]);
	}

	/**
	 * @param string $path
	 * @return bool
	 */
	public function fileModTime($path) {
		if (!is_array($this->fileModTimes)) {
			return false;
		}

		return array_key_exists($path, $this->fileModTimes)
			? $this->fileModTimes[$path]
			: false;
	}
}