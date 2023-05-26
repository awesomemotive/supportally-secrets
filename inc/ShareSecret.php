<?php 
/**
 * Class ShareSecret
 */
namespace ShareSecret;
require_once 'wp-config.php';
/**
 * Class ShareSecret
 */
class ShareSecret
{
	private $database_info;
	private $secretKey;

	/**
	 * ShareSecret constructor.
	 */
	public function __construct()
	{
		$this->database_info = [
			'database_host' => DB_HOST,
			'database_user' => DB_USER,
			'database_password' => DB_PASSWORD,
			'database_name' => DB_NAME,
		];
		$this->secretKey = sodium_hex2bin( KEY );
	}

	/**
	 * Get the secret key.
	 */
	protected function get_secret_key() {	
		return $this->secretKey;
	}

	/**
	 * Get the database info.
	 */
	protected function get_database_info() {
		return $this->database_info;
	}

	/**
	 * Start it all.
	 */
	public function init()
	{
		$this->create_the_table();
		$this->delete_data();
	}

	/**
	 * Display the form.
	 */
	public function display_form()
	{
		include_once __DIR__.'/form.html';
	}

	/**
	 * Create the table if it doesn't exist
	 */
	public function create_the_table()
	{	
		$database_info = $this->get_database_info();
		$mysqli = mysqli_init();
		mysqli_report( MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT );
		try {
			$mysqli->real_connect( $database_info['database_host'], $database_info['database_user'], $database_info['database_password'], $database_info['database_name'] );
		} catch ( \Exception $e ) {
			error_log( "Error : " . $e->getMessage());
			return;
		}

	   $sql = "CREATE TABLE IF NOT EXISTS secrets (
			id BIGINT(20) AUTO_INCREMENT PRIMARY KEY,
			secret LONGTEXT NOT NULL,
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
		)";
		$stmt = $mysqli->prepare( $sql );
		$stmt->execute();
		$stmt->close();
		$mysqli->close();
	}

	/**
	 * Url safe base64 encode
	 *
	 * @param string $data
	 *
	 * @return string
	 */
	public function base64url_encode( $data )
	{
		$base64Url = strtr( base64_encode( $data ), '+/', '-_' );

		return rtrim( $base64Url, '=' );
	}

	/**
	 * Url safe base64 decode
	 *
	 * @param string $base64Url
	 *
	 * @return bool|string
	 */
	public function base64url_decode( $base64Url )
	{
		return base64_decode(strtr( $base64Url, '-_', '+/' ) );
	}

	/**
	 * Encrypt the string
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	public function encrypt( $str )
	{
		$nonce = random_bytes( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
		$encrypted_text = $this->base64url_encode($nonce . sodium_crypto_secretbox( $str, $nonce, $this->secretKey ) );
		return $encrypted_text;
	}

	/**
	 * Decrypt the string
	 *
	 * @param string $encrypted_text
	 *
	 * @return string
	 */
	public function decrypt_string( $encrypted_text )
	{
		$decoded = $this->base64url_decode( $encrypted_text );
		$nonce = mb_substr( $decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit' );
		$cipher_text = mb_substr( $decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit' );
		return sodium_crypto_secretbox_open( $cipher_text, $nonce, $this->secretKey );
	}

	/**
	 * Delete the data from the database after 30 days
	 */
	public function delete_data()
	{
		$database_info = $this->get_database_info();
		$mysqli = mysqli_init();
		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
		try {
			$mysqli->real_connect( $database_info['database_host'], $database_info['database_user'], $database_info['database_password'], $database_info['database_name'] );
		} catch ( \Exception $e ) {
			error_log( "Error : " . $e->getMessage());
			return;
		}
		$delete_sql = "DELETE FROM secrets WHERE created_at < (NOW() - INTERVAL 30 DAY)";
		$stmt = $mysqli->prepare( $delete_sql );
		$stmt->execute();
		$stmt->close();
		$mysqli->close();
	}
}