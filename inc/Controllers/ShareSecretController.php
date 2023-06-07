<?php
/**
 * Class ShareSecret
 */
namespace ShareSecret\Controllers;
use ShareSecret\Models\ShareSecretModel;
use RuntimeException;

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-config.php';

/**
 * Class ShareSecret
 */
class ShareSecretController
{
	private $secretKey;

	/**
	 * ShareSecret constructor.
	 * @param ShareSecretModel $model
	 */
	public function __construct( private ShareSecretModel $model )
	{
		if ( ! defined( 'KEY' ) ) {
			throw new RuntimeException( 'Missing key' );
		}
		$this->secretKey = sodium_hex2bin( KEY );
	}

	/**
	 * Init the plugin.
	 */
	public function init()
	{
		if ( isset( $_POST['secret_url'] ) ) {
			$this->delete_secret();
		} elseif ( isset( $_POST['secret'] ) ) {
			$this->save_secret();
		} elseif( isset( $_GET['view'] ) ) {
			$this->display_secret();
		} else {
			$this->display_form();
		}
	}

	/**
	 * Get the secret key.
	 */
	protected function get_secret_key() {
		return $this->secretKey;
	}

	/**
	 * Display the form.
	 */
	public function display_form()
	{
		include_once __DIR__.'/../views/form.php';
	}

	public function display_secret()
	{
		include_once __DIR__.'/../views/secret.php';
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
	 * Save the secret.
	 */
	public function save_secret()
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
			$secret_id = $this->model->save_secret( $secret_enctypted );
			if ( ! $secret_id ) {
				return;
			}
			$encrypted_id = $this->encrypt( $secret_id );
			$site_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
			$link = http_build_query( array_merge( $_GET, array( 'view'=>$encrypted_id ) ) );
			$secret_url = $site_url . '?' . $link;
			$success = 'Your secret has been saved! Here is the link to your secret';
			echo json_encode( [ 'success' => $success, 'secret_url' => $secret_url] );
		}
	}

	/**
	 * Delete secret on click.
	 */
	public function delete_secret()
	{
		if (empty( $_POST['secret_url'] ) ) {
			echo json_encode( [ 'error' => 'Not valid url' ] );
			return;
		}
		$secret_id = substr( $_POST['secret_url'], strpos( $_POST['secret_url'], "=" ) + 1 );
		$decrypted_id = $this->decrypt_string( $secret_id );
		$decrypted_id = intval( $decrypted_id );
		$delete = $this->model->delete_secret( $decrypted_id );
		if ( $delete === true ) {
			echo json_encode( [ 'success' => 'Secret deleted successfully' ] );
		} else {
			echo json_encode( [ 'error' => 'DB error' ] );
		}

	}

	/**
	 * Display the secret.
	 */
	public function decrypt_secret()
	{
		if ( empty( $_GET['view'] ) ) {
			return 'Not valid url';
		}
		$secret_id = $_GET['view'];

		$decrypted_id = $this->decrypt_string( $secret_id );
		$decrypted_id = intval( $decrypted_id );

		if ( ! $decrypted_id ) {
			return 'Not a valid url';
		}

		$secret = $this->model->get_secret( $decrypted_id );
		if ( ! $secret ) {
			return 'Not a valid url';
		}
		$decrypted_secret = $this->decrypt_string( $secret );
		return $decrypted_secret;
	}

	/**
	 * Delete expired secrets after 30 days.
	 */
	public function delete_expired_secrets()
	{
		$this->model->delete_expired_secrets();
	}
}
