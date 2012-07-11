<?php

class Authorization {
	const LOGIN = "Admin";
	const PASSWORD = '321';

	public static function IsAuthorized() {
		if (!isset($_SERVER['PHP_AUTH_USER'])) {
		    return false;
		}
		return self::checkLoginAndPass($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
	}

	private static function checkLoginAndPass($login, $pass) {
		if ($login == self::LOGIN && $pass == self::PASSWORD)
			return true;
		return false;
	}

	public static function ShowAuthorization() {
	    header('WWW-Authenticate: Basic');
	    header('HTTP/1.0 401 Unauthorized');
	}
}
?>