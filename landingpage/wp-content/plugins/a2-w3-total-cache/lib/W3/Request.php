<?php

/**
 * W3 Request object
 */

/**
 * Class W3_Request
 */
class W3_Request {
	/**
	 * Current request (merge of $_GET and $_POST)
	 * use W3_Request::get_request() for read this var
	 *
	 * @var array
	 */
	private static $request = null;
	
	/**
	 * Returns request value
	 *
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public static function get($key, $default = null) {
		$request = W3_Request::get_request();

		if (isset($request[$key])) {
			$value = $request[$key];
		
			// PHP 7+ complains about get_magic_quotes_gpc() being deprecated and can be removed.
			// We're doing an if/else as it is probably need for PHP 5.3 and older at least
			// https://github.com/W3EDGE/w3-total-cache/issues/92
			if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
				if (defined('TEMPLATEPATH')) {
					$value = w3_stripslashes($value);
				}
			} else {
				if (defined('TEMPLATEPATH') || get_magic_quotes_gpc()) {
					$value = w3_stripslashes($value);
				}
			}

			return $value;
		}

		return $default;
	}

	/**
	 * Returns string value
	 *
	 * @param string $key
	 * @param string $default
	 * @param boolean $trim
	 * @return string
	 */
	public static function get_string($key, $default = '', $trim = true) {
		$value = (string) W3_Request::get($key, $default);

		return ($trim) ? trim($value) : $value;
	}

	/**
	 * Returns integer value
	 *
	 * @param string $key
	 * @param integer $default
	 * @return integer
	 */
	public static function get_integer($key, $default = 0) {
		return (integer) W3_Request::get($key, $default);
	}

	/**
	 * Returns double value
	 *
	 * @param string $key
	 * @param double|float $default
	 * @return double
	 */
	public static function get_double($key, $default = 0.) {
		return (double) W3_Request::get($key, $default);
	}

	/**
	 * Returns boolean value
	 *
	 * @param string $key
	 * @param boolean $default
	 * @return boolean
	 */
	public static function get_boolean($key, $default = false) {
		return w3_to_boolean(W3_Request::get($key, $default));
	}

	/**
	 * Returns array value
	 *
	 * @param string $key
	 * @param array $default
	 * @return array
	 */
	public static function get_array($key, $default = []) {
		$value = W3_Request::get($key);

		if (is_array($value)) {
			return $value;
		} elseif ($value != '') {
			return preg_split("/[\r\n,;]+/", trim($value));
		}

		return $default;
	}

	/**
	 * Returns array value
	 *
	 * @param string $prefix
	 * @param array $default
	 * @return array
	 */
	public static function get_as_array($prefix, $default = []) {
		$request = W3_Request::get_request();
		$array = [];
		foreach ($request as $key => $value) {
			if (strpos($key, $prefix) === 0 || strpos($key, str_replace('.', '_', $prefix)) === 0) {
				$array[substr($key, strlen($prefix))] = $value;
			}
		}

		return $array;
	}

	/**
	 * Returns request array
	 *
	 * @return array
	 */
	public static function get_request() {
		if ( !isset(self::$request) || current_filter() === 'init' ) {
			if (!isset($_GET)) {
				$_GET = [];
			}
	
			if (!isset($_POST)) {
				$_POST = [];
			}
	
			self::$request = array_merge($_GET, $_POST);
		}
		
		return self::$request;
	}
}
