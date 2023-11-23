<?php

namespace PWA_Generator;

/**
 * Projects class.
 */
class Projects {
    /**
     * Projects data from projects.json.
     *
     * @var array{
     *   url: string,
     *   title: string,
     *   description: string,
     *   keywords: string,
     *   categories: array<int, string>,
     *   netlify_id: string,
     *   gtm_id: string,
     *   fbpixel_id: string,
     *   repixel_id: string,
     *   google_ads: boolean,
     *   amazon_ads: boolean,
     *   other_ads: boolean,
     *   google_api: array<string, string>,
     *   fonts: array<string, string>,
     *   colors: array<array<string, string>>,
     *   fontawesome: boolean,
     *   android_app_id: array<string, string>,
     *   apple_app_id: array<string, string>,
     *   screenshots: array<int, string>,
     *   shortcuts: array<int, array<string, string|array<int, array<string, string>>>>,
     *   links: array<int, array<string, string>>,
     *   nav: array<string, string|array<int, array<string, string>>>,
     *   header: array<string, string>,
     *   social: array<string, string|array<int, array<string, string>>>,
     *   author: array<string, string>,
     *   redirects: array<int, array<string, string>>,
     *   cache_files: array<int, string>,
     *   opt_files: array<int, string>,
     *   sitemap_urls: array<int, string>,
     *   pages: array<string, array<string, array<string, string>|string>>
     * }[]
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
     * @return  array{
     *   url: string,
     *   title: string,
     *   description: string,
     *   keywords: string,
     *   categories: array<int, string>,
     *   netlify_id: string,
     *   gtm_id: string,
     *   fbpixel_id: string,
     *   repixel_id: string,
     *   google_ads: boolean,
     *   amazon_ads: boolean,
     *   other_ads: boolean,
     *   google_api: array<string, string>,
     *   fonts: array<string, string>,
     *   colors: array<array<string, string>>,
     *   fontawesome: boolean,
     *   android_app_id: array<string, string>,
     *   apple_app_id: array<string, string>,
     *   screenshots: array<int, string>,
     *   shortcuts: array<int, array<string, string|array<int, array<string, string>>>>,
     *   links: array<int, array<string, string>>,
     *   nav: array<string, string|array<int, array<string, string>>>,
     *   header: array<string, string>,
     *   social: array<string, string|array<int, array<string, string>>>,
     *   author: array<string, string>,
     *   redirects: array<int, array<string, string>>,
     *   cache_files: array<int, string>,
     *   opt_files: array<int, string>,
     *   sitemap_urls: array<int, string>,
     *   pages: array<string, array<string, array<string, string>|string>>
     * }[]
     * Array of projects from projects.json.
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

        $projects_data = json_decode($projects_data_json, true);

        return is_array($projects_data) ? $projects_data : [];
    }
}
