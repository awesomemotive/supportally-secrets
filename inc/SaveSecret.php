<?php
/**
 * Class SaveSecret
 */
namespace ShareSecret;

define( "RECAPTCHA_V2_SECRET_KEY", '6LfzLTwmAAAAAID0W5e-AYwY0b_jWhzH_6QBmLVZ' );

class SaveSecret extends ShareSecret
{
	/**
	 * Save the secret to the database
	 */
	public function save_data()
	{

		if ( empty($_POST['secret'] ) ) {
			echo json_encode( [ 'error' => 'Please enter a secret' ] );
			return;
		}

		if( ! isset( $_POST['g-recaptcha-response'] ) ) {
			echo json_encode( [ 'error' => 'Invalid reCaptcha, please try again.' ] );
			return;
		
		}
		$recaptcha = $_POST['g-recaptcha-response'];
		$url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode( RECAPTCHA_V2_SECRET_KEY ) .  '&response=' . urlencode( $recaptcha );
		$response = file_get_contents( $url );
		$responseKeys = json_decode( $response,true );
		if ( ! isset( $responseKeys["success"] ) || $responseKeys["success"] !== true ) {
			error_log( 'reCaptcha error: ' . print_r( $responseKeys["error-codes"] , true ) );
			echo json_encode( [ 'error' => 'Invalid reCaptcha, please try again'] );
			return;
		} else {
			$secret = $_POST['secret'];
			$secret_enctypted = $this->encrypt( $secret );
			// Save the secret at the DB.
			$database_info = $this->get_database_info();
			$mysqli = mysqli_init();
			mysqli_report( MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT );
			try {
				$mysqli->real_connect( $database_info['database_host'], $database_info['database_user'], $database_info['database_password'], $database_info['database_name'] );
			} catch ( \Exception $e ) {
				error_log( "Error : " . $e->getMessage());
				echo ( json_encode( ['error' => "DataBase Connection error "]));
				return;
			}
		
			$insert_sql = "INSERT INTO secrets (secret) VALUES (?)";
			$stmt = $mysqli->prepare( $insert_sql );
			$stmt->bind_param( 's', $secret_enctypted );
			try {
				$stmt->execute();
			} catch ( \Exception $e ) {
				echo ( json_encode( ['error' => "Error : " . mysqli_error( $mysqli ) ]));
				return;
			}

			// Get the ID of the record and encrypt it.
			$secret_id = $mysqli->insert_id;
			$stmt->close();
			$mysqli->close();
			$encrypted_id = $this->encrypt( $secret_id );
			$site_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
			$link = http_build_query( array_merge( $_GET, array( 'view'=>$encrypted_id ) ) );
			$secret_url = $site_url . '?' . $link;
			$success = 'Your secret has been saved! Here is the link to your secret';
			echo json_encode( [ 'success' => $success, 'secret_url' => $secret_url] );
		}
	}
}