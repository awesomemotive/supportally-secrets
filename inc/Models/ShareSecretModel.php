<?php
namespace ShareSecret\Models;

/**
 * Class ShareSecretModel
 */
class ShareSecretModel {
	/**
	 * @var array
	 */
	private $database_info;
	
	/**
	 * ShareSecretModel constructor.
	 */
	public function __construct() {
		$this->database_info = array(
			'database_host'     => DB_HOST,
			'database_user'     => DB_USER,
			'database_password' => DB_PASSWORD,
			'database_name'     => DB_NAME,
		);
	}

	/**
	 * Get the database info.
	 */
	protected function get_database_info() {
		return $this->database_info;
		$this->create_the_table();
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
	 * Save the secret to the database
	 */
	public function save_secret( $secret_enctypted )
	{
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

		// Get the ID of the record.
		$secret_id = $mysqli->insert_id;
		$stmt->close();
		$mysqli->close();
		return $secret_id;
	}

	/**
	 * Delete the data from the database after 30 days
	 */
	public function delete_expired_secrets()
	{
		$database_info = $this->get_database_info();
		$mysqli = mysqli_init();
		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
		try {
			$mysqli->real_connect( $database_info['database_host'], $database_info['database_user'], $database_info['database_password'], $database_info['database_name'] );
		} catch ( \Exception $e ) {
			error_log( "Error : " . $e->getMessage());
			echo "Error : " . $e->getMessage();
			return;
		}
		try {
			$delete_sql = "DELETE FROM secrets WHERE created_at < (NOW() - INTERVAL 30 DAY)";
		} catch ( \Exception $e ) {
			error_log( "Error : " . $e->getMessage());
			echo "Error : " . $e->getMessage();
			return;
		}

		$stmt = $mysqli->prepare( $delete_sql );
		$stmt->execute();
		$stmt->close();
		$mysqli->close();
		echo "Deleted old data";
	}

	/**
	 * Get the secret from the database.
	 * @param $decrypted_id
	 * @return string
	 */
	public function get_secret( $decrypted_id )
	{
		$database_info = $this->get_database_info();
		$mysqli = mysqli_init();
		mysqli_report( MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT );
		try {
			$mysqli->real_connect( $database_info['database_host'], $database_info['database_user'], $database_info['database_password'], $database_info['database_name'] );
		} catch ( \Exception $e ) {
			error_log( 'Error : ' . $e->getMessage() );
			return htmlspecialchars( 'DataBase Connection error' );
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
		
		return $secret;
	}

	/**
	 * Delete the secret from the database.
	 * @param $decrypted_id
	 * @return string
	 */
	public function delete_secret( $decrypted_id )
	{
		$database_info = $this->get_database_info();
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
	}	
}
