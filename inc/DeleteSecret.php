<?php
/**
 * Class DeleteSecret
 */
namespace ShareSecret;

use ShareSecret\ShareSecret;


class DeleteSecret extends ShareSecret
{
	/**
	 * Delete the secret from the database
	 */
	public function delete_secret()
	{
		if (empty( $_POST['secret_url'] ) ) {
			echo json_encode( [ 'error' => 'Not valid url' ] );
			return;
		}
		$database_info = $this->get_database_info();
		$secret_id = substr( $_POST['secret_url'], strpos( $_POST['secret_url'], "=" ) + 1 );
		$decrypted_id = $this->decrypt_string( $secret_id );
		$decrypted_id = intval( $decrypted_id );
		$mysqli = mysqli_init();
		mysqli_report( MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT );
		try {
			$mysqli->real_connect( $database_info['database_host'], $database_info['database_user'], $database_info['database_password'], $database_info['database_name'] );
		} catch ( \Exception $e ) {
			error_log( "Error : " . $e->getMessage() );
			echo ( json_encode( ['error' => "DataBase Connection error "] ) );
			return;
		}
		$stmt = $mysqli->prepare( "DELETE FROM secrets WHERE id = ?" );
		$stmt->bind_param( 'i', $decrypted_id );
		try {
			$stmt->execute();
		} catch ( \Exception $e ) {
			echo ( json_encode( ['error' => "Error : " . mysqli_error( $mysqli ) ]));
			return;
		}
		$stmt->close();
		$mysqli->close();
		echo json_encode([ 'success' => 'Secret deleted successfully' ]);
	}
}