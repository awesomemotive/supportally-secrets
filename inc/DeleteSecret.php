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
        if (empty($_POST['secret_url']) ) {
            echo json_encode([ 'error' => 'Not valid url' ]);
            return;
        }
		$database_info = $this->get_database_info();
        $secret_id = substr($_POST['secret_url'], strpos($_POST['secret_url'], "=") + 1);
        $decrypted_id = $this->decrypt_string($secret_id);
        $decrypted_id = intval($decrypted_id);
        $mysqli = mysqli_init();
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $mysqli->real_connect($database_info['database_host'], $database_info['database_user'], $database_info['database_password'], $database_info['database_name']);
        $stmt = $mysqli->prepare("DELETE FROM secrets WHERE id = ?");
        $stmt->bind_param('i', $decrypted_id);
        $stmt->execute();
        $stmt->close();
        $mysqli->close();
        echo json_encode([ 'success' => 'Secret deleted successfully' ]);
    }
}