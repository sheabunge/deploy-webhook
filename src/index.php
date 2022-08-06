<?php

namespace Shea\DeployWebhook;

ini_set( 'display_errors', 'On' );

require dirname( __DIR__ ) . '/vendor/autoload.php';

function run_command( $command ) {
	echo "\n$ ", htmlentities( $command ), "\n";
	echo shell_exec( $command );
}

if ( empty( WORKING_DIR ) || ! is_dir( WORKING_DIR ) ) {
	trigger_error( 'Could not find working directory', E_USER_ERROR );
}

$webhook = new Webhook( SECRET_TOKEN );
$webhook->verify_signature();

echo 'Changing directory to ', WORKING_DIR, "\n";
if ( ! chdir( WORKING_DIR ) ) {
	trigger_error( 'Error when changing directory', E_USER_ERROR );
}

run_command( 'git pull origin HEAD' );

if ( is_file( 'composer.json' ) ) {
	if ( empty( COMPOSER_BIN ) || ! is_executable( COMPOSER_BIN ) ) {
		trigger_error( sprintf( 'Could not locate Composer executable at %s', COMPOSER_BIN ), E_USER_WARNING );
	} else {
		run_command( COMPOSER_BIN . ' self-update' );
		run_command( COMPOSER_BIN . ' install' );
	}
}

if ( is_file( 'package.json' ) ) {
	run_command( 'npm install' );
	run_command( 'npm run build' );
}
