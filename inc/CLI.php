<?php

namespace PWA_Generator;

/**
 * CLI class.
 */
class CLI {
    /**
     * Whether verbose mode is on or off.
     *
     * @var bool
     */
    public static bool $verbose = false;

    /**
     * Set CLI verbose mode.
     *
     * @param   bool    $verbose    If true, turns verbose mode on. Defaults to false.
     * @return  void
     */
    public function set_verbose($verbose = false): void {
        self::$verbose = $verbose;
    }

    /**
     * Displays usage on the command line.
     *
     * @return  void
     */
    public function show_usage(): void {
        echo "Usage:\n" .
            "\n\e[96m./pwagenerator.php projects\e[0m\t\t\tList all projects configured for building in projects.json" .
            "\n\e[96m./pwagenerator.php [project] [option]\e[0m\tBuild a project using project configuration in project.json" . // phpcs:ignore Generic.Files.LineLength.TooLong
            "\n\toptions:" .
                "\n\t\t-v verbose" .
                "\n\t\t-i generate icons" .
                "\n\t\t-b build" .
                "\n\t\t-d deploy" .
            "\n\nTo create a new project, add a new project to projects.json, then build it\n";
    }

    /**
     * Displays a formatted list of projects on the command line.
     *
     * @return  void
     */
    public function list_projects(): void {

        $projects = new Projects();
        $projects_data = $projects->projects_data;

        if (empty($projects_data) || !is_array($projects_data)) {
            $this->display_error("Failed to list projects: projects data is empty.");
            exit;
        }

        echo "Projects listed in project configuration file (projects.json):";

        foreach ($projects_data as $project_data) {
            echo "\n\e[96m- " . $project_data["url"] . "\e[0m: " . $project_data["title"];
        }

        echo "\n";
    }

    /**
     * Displays a color formatted error message on the command line.
     *
     * @param   string  $error  Error message to display.
     * @return  void
     */
    public static function display_error($error): void {
        if (empty($error)) {
            return;
        }

        echo "\n\e[31m ERROR: " . $error . "\e[0m\n";
    }
}
