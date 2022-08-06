<?php

namespace Shea\DeployWebhook;

class Webhook {

	private $secret_token;

	public function __construct( $secret_token ) {
		$this->secret_token = $secret_token;

		if ( ! isset( $_POST['payload'] ) ) {
			trigger_error( 'Webhook payload not provided.', E_USER_ERROR );
		}

		$this->verify_signature();
		json_decode( $_POST['payload'] );
	}

	private function calculate_signature() {
		return 'sha256=' . hash_hmac( 'sha256', $_POST['payload'], $this->secret_token );
	}

	public function verify_signature() {
		$signature = $this->calculate_signature();

		if ( ! isset( $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ) || $_SERVER['HTTP_X_HUB_SIGNATURE_256'] !== $signature ) {
			trigger_error( "Invalid signature $signature", E_USER_ERROR );
		}
	}
}
