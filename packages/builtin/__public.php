<?php
const UNIT_PATH = __DIR__ . DIRECTORY_SEPARATOR;

const LF = "\n";

function is_uint($val): bool {
	return is_int($val) && $val >= 0;
}

function uint_ensure(int $num) {
	if ($num < 0) {
		throw new \ErrorException('Cannot use ' . $num . ' as a UInt value');
	}

	return $num;
}

function html_escape(?string $str): string {
	return $str === null ? '' : htmlspecialchars($str);
}

function html_unescape(?string $str): string {
	return $str === null ? '' : htmlspecialchars_decode($str);
}

function _std_split(string $it, string $separator) {
	return explode($separator, $it);
}

function _std_replace(string $it, $search, $replacement) {
	return str_replace($search, $replacement, $it);
}

function _has(array $it, string|int $key) {
	return array_key_exists($key, $it);
}

function _vals_contains(array $it, $val, bool $strict = false) {
	return in_array($val, $it, $strict);
}

function _array_find(array $it, $val) {
	return array_search($val, $it, true);
}

function _std_array_map(array $it, callable $callback) {
	return array_map($callback, $it);
}

function _std_join(array $it, string $separator) {
	return implode($separator, $it);
}

function _dict_find(array $it, $val) {
	return array_search($val, $it, true);
}

function _dict_get(array $it, string|int $key) {
	return $it[$key] ?? null;
}

function _regex_test(string $regex, string $subject): bool {
	return preg_match($regex, $subject) ? true : false;
}

function _regex_capture(string $regex, string $subject): ?array {
	$result = null;
	$count = preg_match($regex, $subject, $result);
	return $count === 0 ? null : $result;
}

function _regex_capture_all(string $regex, string $subject): ?array {
	$results = null;
	$count = preg_match_all($regex, $subject, $results);
	return $results;
}

function _build_attributes(array $items, ?array $additions = null): string {
	if ($additions !== null) {
		$items = array_replace($items, $additions);
	}

	$list = [];
	foreach ($items as $key => $val) {
		if ($val === null || $val === false) {
			continue;
		}

		$list[] = $val === true ? $key : $key . '="' . htmlspecialchars((string)$val) . '"';
	}

	return implode(' ', $list);
}


// program end

// autoloads
const __AUTOLOADS = [
	'IView' => 'dist/core.php'
];

spl_autoload_register(function ($class) {
	isset(__AUTOLOADS[$class]) && require UNIT_PATH . __AUTOLOADS[$class];
});

require_once UNIT_PATH . 'dist/core.php';

// end
