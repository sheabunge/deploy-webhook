<?php

namespace Shea\DeployWebhook;

class Webhook {

	private $secret_token;

	private $data;

	public function __construct( $secret_token ) {
		$this->secret_token = $secret_token;
	}

	private function calculate_signature( $secret_token, $payload ) {
		return 'sha256=' . hash_hmac( 'sha256', $payload, $secret_token );
	}

	public function verify_signature() {
		$payload = file_get_contents( 'php://input' );

		if ( 0 === strpos( $payload, 'payload=' ) ) {
			$payload = substr( urldecode( $payload ), 8 );
		}

		$signature = $this->calculate_signature( $this->secret_token, $payload );

		if ( ! isset( $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ) || ! hash_equals( $_SERVER['HTTP_X_HUB_SIGNATURE_256'], $signature ) ) {
			trigger_error( "Invalid signature $signature", E_USER_ERROR );
		}

		$this->data = json_decode( $payload );
	}

	public function get_data() {
		return $this->data;
	}
}
