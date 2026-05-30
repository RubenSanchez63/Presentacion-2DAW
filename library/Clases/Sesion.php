<?php
	
	namespace Clases ;
	
	use Models\User;
	use Clases\JWT;
	use Clases\Redirect;
	
	final class Sesion
	{
		const MAX_TIEMPO = 18000 ;
		
		/**
		 * @return void
		 */
		public static function update(): void
		{
			self::set("time", time()) ;
		}

		/**
		 * @return User|null
		 */
		public static function user(): User|null
		{
			// Intentar obtener del JWT primero
			$payload = self::getTokenPayload();
			if ($payload && isset($payload['sub'])) {
				return User::getById($payload['sub']);
			}

			// Fallback a sesión tradicional
			if (self::active()) {
				$email = self::get("user");
				return User::getByEmail($email);
			}
			return null;
		}

		/**
		 * @return string
		 */
		public static function getRole(): string
		{
			// Intentar obtener del JWT primero
			$payload = self::getTokenPayload();
			if ($payload && isset($payload['role'])) {
				return $payload['role'];
			}

			// Fallback a sesión tradicional
			if (self::active()) {
				$email = self::get("user");
				return User::getByEmail($email)->getRole();
			}
			Redirect::to('/');
			exit;
		}

		/**
		 * @return void
		 */
		public static function init(User $user): void
		{
			self::set("user", $user->email) ;
			self::update() ;
		}

		/**
		 * Inicializa la sesión a partir de un JWT token
		 * @return void
		 */
		public static function initFromToken(string $token): void
		{
			$payload = JWT::verify($token);
			if ($payload) {
				self::set("jwt_payload", $payload);
				self::set("user", $payload['email']);
				self::update();
			}
		}

		/**
		 * Obtiene el payload del JWT de la cookie o header
		 * @return array|null
		 */
		public static function getTokenPayload(): array|null
		{
			// Desde cookie
			$token = $_COOKIE['auth_token'] ?? null;
			
			// Desde header Authorization
			if (!$token && isset($_SERVER['HTTP_AUTHORIZATION'])) {
				$header = $_SERVER['HTTP_AUTHORIZATION'];
				if (str_starts_with($header, 'Bearer ')) {
					$token = substr($header, 7);
				}
			}

			if ($token) {
				return JWT::verify($token);
			}

			return null;
		}

		/**
		 * Verifica si el usuario está autenticado (con JWT o sesión)
		 * @return bool
		 */
		public static function isAuthenticated(): bool
		{
			// Verificar JWT
			$payload = self::getTokenPayload();
			if ($payload) {
				return true;
			}

			// Verificar sesión tradicional
			return self::active();
		}
		
		/**
		 * @return bool
		 */
		public static function active(): bool
		{
			return (session_status() === PHP_SESSION_ACTIVE) &&
				   (self::get("user")) &&
				   ((time() - self::get("time")) <= self::MAX_TIEMPO)	;
		}
		
		/**
		 * @param string $clave
		 * @param mixed $valor
		 * @return void
		 */
		public static function set(string $clave, mixed $valor): void
		{
			$_SESSION[$clave] = $valor ;
		}
		
		/**
		 * @param string $clave
		 * @return mixed
		 */
		public static function get(string $clave): mixed
		{
			#return $_SESSION[$clave]??null ;
			return $_SESSION[$clave] ;
		}
		
		/**
		 * @return void
		 */
		public static function close(): void
		{
			# cerramos la sesión
			$_SESSION = [] ;
			session_destroy() ;
		}
	}
