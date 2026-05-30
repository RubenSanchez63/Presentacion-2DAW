<?php
namespace Clases;
use PDO;

	final class Database
	{
		private const string DBHOST = "localhost:3306";
		private const string DBUSER = "root";
		private const string DBPASS = "";
		private const string DBNAME = "pdf_library";
		
		private static ?PDO $instance = null;

		private function __clone() {}
		private function __construct() {}
		
		/**
		 * Returns the PDO database connection
		 * @return PDO
		 */
		public static function connect(): PDO
		{
			if (self::$instance === null) {
				try {
					$dsn = "mysql:host=" . self::DBHOST . ";dbname=" . self::DBNAME . ";charset=utf8mb4";
					self::$instance = new PDO($dsn, self::DBUSER, self::DBPASS, [
						PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
						PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
					]);
				} catch(\PDOException $pdoe) {
					die(json_encode(["error" => "Connection error: " . $pdoe->getMessage()]));
				}
			}
			return self::$instance;
		}
	}
