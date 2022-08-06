<?php

namespace Shea\DeployWebhook;

class Runner {

	private $working_directory;

	public function __construct( $working_directory ) {
		$this->working_directory = $working_directory;
		$this->ensure_directory_exists();
	}

	private function ensure_directory_exists() {
		if ( ! is_dir( $this->working_directory ) ) {
			trigger_error( 'Could not find working directory', E_USER_ERROR );
		}
	}

	private function run_command( $command ) {
		echo "\n$ ", htmlentities( $command ), "\n";
		echo shell_exec( $command );
	}

	public function git() {
		$this->run_command( 'git pull origin HEAD' );
	}

	public function composer() {
		if ( ! is_file( 'composer.json' ) ) {
			return;
		}

		if ( empty( COMPOSER_BIN ) || ! is_executable( COMPOSER_BIN ) ) {
			trigger_error( sprintf( 'Could not locate Composer executable at %s', COMPOSER_BIN ), E_USER_WARNING );
			return;
		}

		$this->run_command( COMPOSER_BIN . ' self-update' );
		$this->run_command( COMPOSER_BIN . ' install' );
	}

	public function npm() {
		if ( ! is_file( 'package.json' ) ) {
			return;
		}

		$this->run_command( 'npm install' );
		$this->run_command( 'npm run build' );
	}

	public function run_tasks() {
		if ( ! chdir( $this->working_directory ) ) {
			trigger_error( "Error when changing directory to $this->working_directory", E_USER_ERROR );
		}

		echo 'cd ', getcwd(), "\n";
		$this->git();
		$this->composer();
		$this->npm();
	}
}
