<?php

namespace Shea\DeployWebhook;

ini_set( 'display_errors', 'On' );

require dirname( __DIR__ ) . '/vendor/autoload.php';

$webhook = new Webhook( SECRET_TOKEN );
$webhook->verify_signature();

if ( ! $webhook->is_push_event() ) {
	trigger_error( 'Only responds to push events', E_USER_ERROR );
}

$runner = new Runner( WORKING_DIR );
$runner->run_tasks();
