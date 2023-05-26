<?php
/**
 * Class ViewSecret
 */
namespace ShareSecret;

use ShareSecret\ShareSecret;

class ViewSecret extends ShareSecret
{
	/**
	 * Get the secret and decrypt it.
	 */
	public function view_secret()
	{

		if ( empty( $_GET['view'] ) ) {
			return 'Not valid url';
		}
		$secret_id = $_GET['view'];
		$decrypted_id = $this->decrypt_string( $secret_id );
		$decrypted_id = intval( $decrypted_id );
		
		if ( ! $decrypted_id ) {
			return 'Not valid url';
		}
		$database_info = $this->get_database_info();
		$mysqli = mysqli_init();
		mysqli_report( MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT );
		try {
			$mysqli->real_connect( $database_info['database_host'], $database_info['database_user'], $database_info['database_password'], $database_info['database_name'] );
		} catch ( \Exception $e ) {
			error_log( 'Error : ' . $e->getMessage() );
			return 'DataBase Connection error';
		}
		$stmt = $mysqli->prepare( "SELECT secret FROM secrets WHERE id = ?" );
		$stmt->bind_param( 'i', $decrypted_id );
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		$stmt->close();
		$mysqli->close();
		if ( empty( $row ) ) {
			return $decrypted_secret = '';
		}

		$secret = $row['secret'];
		$decrypted_secret = $this->decrypt_string( $secret );
		return $decrypted_secret;
	}

	/**
	 * Display the secret on the page.
	 */
	public function display()
	{
		include_once __DIR__.'/view.php';
	}
}


