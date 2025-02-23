<?php
/**
 * This file is part of the Tea programming language project
 * @copyright 	(c)2019 tealang.org
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Tea;

class PHPUnitScanner
{
	private $path;
	private $function_map = [];
	private $class_map = [];

	public function scan(string $path)
	{
		$this->path = $path;
		$this->scan_files($path);
		$this->scan_sub_directories($path);

		return $this->class_map;
	}

	public function scan_sub_directories(string $path)
	{
		$items = array_diff(scandir($path), ['..', '.']);
		foreach ($items as $item) {
			$sub_path = $path . $item;
			if (is_dir($sub_path)) {
				$this->scan_files($sub_path . DS);
				$this->scan_sub_directories($sub_path . DS);
			}
		}
	}

	public function scan_files(string $path)
	{
		$files = glob($path . '*.php');

		foreach ($files as $file) {
			$file = realpath($file);
			$code = file_get_contents($file);
			$file = str_replace($this->path, '', $file);

			preg_match('/namespace\s+([a-z0-9_\\\\]+)/i', $code, $matches);
			$namespace = $matches[1] ?? null;

			preg_match_all('/\n\s?(?:(?:abstract\s+|final\s+)?class|interface|trait)\s+([a-z0-9_]+)/i',
				$code,
				$matches,
				PREG_PATTERN_ORDER
			);

			$classes = $matches[1];
			if ($classes) {
				foreach ($classes as $class) {
					if ($namespace) {
						$class = $namespace . _BACK_SLASH . $class;
					}

					if (isset($this->class_map[$class])) {
						throw new Exception("Class '$class' in file '$file' duplicated.");
					}

	 				$this->class_map[$class] = $file;
				}
			}
			elseif (preg_match('/\bfunction\s+[a-z][a-z0-9]/i', $code)) {
				$this->function_map[] = $file;
			}
		}
	}
}

// end
