<?php

require_once('class-cli.php');

/**
 * Projects class.
 */
class Projects {

	/**
	 * Projects data from projects.json.
	 * 
	 * @var array
	 */
	public array $projects_data = [];

	/**
	 * Constructor.
	 * 
	 * Sets projects data.
	 */
	public function __construct() {
		$this->projects_data = $this->get_projects();
	}

	/**
	 * Gets projects data from projects.json file contents.
	 * 
	 * @return	array	Array of projects from projects.json.
	 */
	private function get_projects(): array {

		$projects_file_path = dirname(__FILE__, 2) . '/projects.json';
	
		if (!file_exists($projects_file_path)) {
			CLI::display_error("Failed to get projects data from projects.json: projects.json is missing.");
			exit;
		}

		$projects_data_json = file_get_contents($projects_file_path, true);

		if (empty($projects_data_json)) {
			CLI::display_error("Failed to get projects data from projects.json: projects.json file contents is empty.");
			exit;
		}

		return json_decode($projects_data_json, true);
	}
}