<?php

require_once('class-text.php');
require_once('class-cli.php');

/**
 * Build class.
 */
class Build {

	/**
	 * Current project name.
	 * 
	 * @var	string
	 */
	private string $project = '';

	/**
	 * Current project data from projects.json.
	 * 
	 * @var	array
	 */
	private array $project_data = [];

	/**
	 * Current project directory path.
	 * 
	 * @var	string
	 */
	private string $project_dir = '';

	/**
	 * Files to copy into project directory.
	 */
	private array $copy_files = [
		'_redirects',
		'manifest.json',
		'robots.txt',
		'sitemap.xml',
		'sw.js',
		'tsconfig.json',
		'webpack.config.js',
	];
	
	/**
	 * Files for service worker to cache.
	 */
	private array $cache_files = [
		'manifest.json',
	];

	/**
	 * Constructor.
	 * 
	 * @param	array	$options	Build options.
	 */
	public function __construct(
		array $options = [
			'project' => '',
			'generate_favicons' => false,
			'build' => false,
			'deploy' => false,
		]
	) {
		if (empty($options['project'])) {
			CLI::display_error("Failed to construct build: project not provided.");
			exit;
		}

		$this->project = $options['project'];
		$this->project_dir = $this->get_project_dir();
		$this->project_data = $this->get_project_data();

		if (empty($this->project_dir)) {
			CLI::display_error("Failed to construct build: project directory is empty.");
			exit;	
		}

		if (empty($this->project_data)) {
			CLI::display_error("Failed to construct build: project data is empty.");
			exit;	
		}

		// Generate favicons.
		if (!empty($options['generate_favicons'])) {
			$this->generate_favicons();
		}

		// Build project.
		if (!empty($options['build'])) {
			$this->build_project();
		}

		// Deploy project.
		if (!empty($options['deploy'])) {
			$this->deploy_project();
		}
	}

	/**
	 * Get project directory path.
	 * 
	 * @return	string
	 */
	private function get_project_dir(): string {
		if (empty($this->project)) {
			CLI::display_error("Failed to get project directory: project is empty.");
			exit;
		}

		return dirname(__DIR__, 2) . "/{$this->project}";
	}

	/**
	 * Get project data.
	 * 
	 * @return 	array	Array of current project data from projects.json
	 */
	public function get_project_data(): array {
		if (empty($this->project)) {
			CLI::display_error("Failed to get project data: project is empty.");
			exit;
		}

		if (empty($this->project_dir)) {
			CLI::display_error("Failed to get project data: project directory path is empty.");
			exit;
		}

		// Get all projects.
		$projects = new Projects();

		if (empty($projects->projects_data) || !is_array($projects->projects_data)) {
			CLI::display_error("Failed to get project data: projects data is empty or is not an array.");
			exit;
		}
	
		// Get data for current project.
		$project_data = array_values(
			array_filter(
				$projects->projects_data,
				fn($current_project_data) => $current_project_data['url'] === $this->project,
			)
		);

		if (empty($project_data[0])) {
			CLI::display_error("Failed to get project data: could not get project from projects.json file");
			exit;
		}

		$project_data = $project_data[0];

		// Set file lists.
		$project_data['files']['compile'] = [];
		$project_data['files']['copy'] = !empty($project_data['files']['copy'])
			? array_merge($project_data['files']['copy'], $this->copy_files)
			: $this->copy_files;
		$project_data['files']['cache'] = !empty($project_data['files']['cache'])
			? array_merge($project_data['files']['cache'], $this->cache_files)
			: $this->cache_files;

		// Add data.json or posts.json to cache file list if they exist.
		$project_data_file = "{$this->project_dir}/data.json";
		if (file_exists($project_data_file)) {
			$project_data['files']['cache'][] = 'data.json';
		}

		$project_posts_file = "{$this->project_dir}/posts.json";
		if (file_exists($project_posts_file)) {
			$project_data['files']['cache'][] = 'posts.json';
		}

		// Add "cache_files" defined in project.json to cache file array.
		if (!empty($project_data['cache_files']) && is_array($project_data['cache_files'])) {
			foreach ($project_data['cache_files'] as $file) {
				$project_data['files']['cache'][] = $file;
			}
		}

		// Add files in files/scss directory to copy file array.
		$sass_files = glob(dirname(__DIR__) . '/files/scss/*');
		if (!empty($sass_files) && is_array($sass_files)) {
			foreach ($sass_files as $file) {
				$project_data['files']['copy'][] = 'scss/' . basename($file);
			}
		}

		// Add files in files/js directory to copy file array.
		$js_files = glob(dirname(__DIR__) . '/files/js/*');
		if (!empty($js_files) && is_array($js_files)) {
			foreach ($js_files as $file) {
				$project_data['files']['copy'][] = 'js/' . basename($file);
			}
		}

		// Add files in files/opt that are defined in "opt_files" in projects.json to copy and cache arrays.
		$opt_files = glob(dirname(__DIR__) . '/files/opt/*');
		if (!empty($opt_files) && is_array($opt_files)) {
			foreach ($opt_files as $file) {
				if (in_array('opt/' . basename($file), $project_data['opt_files'], true)) {
					$project_data['files']['cache'][] = 'opt/' . basename($file);
					$project_data['files']['copy'][] = 'opt/' . basename($file);
				}
			}
		}

		// Set sitemap URLs.
		$project_data['sitemap']['urls'] = [
			'index',
		];

		// Get project pages data.
		$project_data['pages'] = $this->get_pages_data($project_data);

		/**
		 * Add pages to:
		 * 	$project_data['files']['compile'], for static HTML file generation.
		 * 	$project_data['files']['cache'], for caching by service worker (also add .jpg if page type === post).
		 * 	$project_data['sitemap']['urls'], for sitemap generation.
		 */
		if (!empty($project_data['pages']) && is_array($project_data['pages'])) {
			foreach ($project_data['pages'] as $page_data) {
				if (empty($page_data['url'])) {
					continue;
				}

				$page_filename = "{$page_data['url']}.html";

				// Add page to compile file list.
				if (!in_array($page_data['url'], array_column($project_data['files']['compile'], 'page'), true)) {
					$project_data['files']['compile'][] = [
						'source_file' => dirname(__DIR__) . '/templates/home.php',
						'target_file' => $page_filename,
						'page' => $page_data['url'],
					];
				}

				// Add page to cache file list.
				if (!in_array($page_filename, $project_data['files']['cache'])) {
					$project_data['files']['cache'][] = $page_filename;
				}

				// Add page featured image to cache file list if page is a post.
				if ($page_data['type'] === 'post' && !in_array("img/{$page_data['url']}.jpg", $project_data['files']['cache'])) {
					$project_data['files']['cache'][] = "img/{$page_data['url']}.jpg";
				}

				// Add page to sitemap URLs.
				if (!in_array($page_data['url'], $project_data['sitemap']['urls'])) {
					$project_data['sitemap']['urls'][] = $page_data['url'];
				}
			}
		}

		return $project_data;
	}

	/**
	 * Get pages data.
	 * 
	 * @param	array	$project_data	Array of project data.
	 * @return 	array	Array of pages data.
	 */
	private function get_pages_data(array $project_data): array {
		// Set up base pages for all projects.
		$pages_data = array_merge(
			$project_data['pages'] ?? [],
			[
				'index' => [
					'type' => 'index',
					'url' => 'index',
					'title' => $project_data['title'],
					'description' => $project_data['description']
				],
				'disclaimer' => [
					'type' => 'template',
					'url' => 'disclaimer',
					'title' => 'Disclaimer',
					'description' => "{$project_data['title']} disclaimer",
				],
				'privacy-policy' => [
					'type' => 'template',
					'url' => 'privacy-policy',
					'title' => 'Privacy Policy',
					'description' => "{$project_data['title']} privacy policy",
				],
				'terms-and-conditions' => [
					'type' => 'template',
					'url' => 'terms-and-conditions',
					'title' => 'Terms and Conditions',
					'description' => "{$project_data['title']} terms and conditions",
				]
			]
		);

		// Get data from data.json and add pages to $pages_data.
		$project_data_file = "{$this->project_dir}/data.json";
		if (file_exists($project_data_file)) {
			$search_result_pages_data = json_decode(file_get_contents($project_data_file), true);

			foreach ($search_result_pages_data as $page_data) {
				if (empty($page_data['title'])) {
					continue;
				}

				/**
				 * In data.json, page titles can be an array, to allow for generating different URLs with the same content.
				 * Add each title to $pages_data so that a page will be generated for each.
				 */
				if (is_array($page_data['title'])) {
					foreach ($page_data['title'] as $title) {
						if (!empty($title)) {

							// If URL isn't provided, create it from the title.
							$url = $page_data['url'] ?? Text::normalize_text($title, 'url');
							
							$pages_data[$url] = [
								...$page_data,
								'title' => $title,
								'type' => 'search',
							];
						}
					}
				} else {
					// If URL isn't provided, create it from the title.
					$url = $page_data['url'] ?? Text::normalize_text($page_data['title'], 'url');

					$pages_data[$url] = [
						...$page_data,
						'type' => 'search',
					];
				}
			}
		}
		
		// Get data from posts.json and add pages to $pages_data.
		$project_posts_file = "{$this->project_dir}/posts.json";
		if (file_exists($project_posts_file)) {
			$post_pages_data = json_decode(file_get_contents($project_posts_file), true);

			foreach ($post_pages_data as $page_data) {
				if (empty($page_data['title'])) {
					continue;
				}

				// If URL isn't provided, create it from the title.
				$url = $page_data['url'] ?? Text::normalize_text($page_data['title'], 'url');

				$pages_data[$url] = [
					...$page_data,
					'type' => 'post',
					'image' => 'img/' . $page_data['image'],
				];
			}
		}

		// Set data values for each page that aren't already set.
		foreach ($pages_data as $url => $page_data) {

			// Type.
			if (empty($page_data['type'])) {
				$page_data['type'] = 'special';
				$pages_data[$url]['type'] = 'special';
			}

			// URL.
			if (empty($page_data['url']) && (!empty($page_data['title']))) {
				$pages_data[$url]['url'] = !empty($pages_data['*']['url'])
					? str_ireplace('***TITLE***', Text::normalize_text($page_data['title'], 'url'), $pages_data['*']['url'])
					: Text::normalize_text($page_data['title'], 'url');
			}

			// Title.
			if (empty($page_data['title']) && !empty($project_data['title'])) {
				$pages_data[$url]['title'] = !empty($pages_data['*']['title'])
					? str_ireplace('***TITLE***', $project_data['title'], $pages_data['*']['title'])
					: $project_data['title'];
			}

			// SEO title.
			if (empty($page_data['title_seo']) && !empty($project_data['title'])) {
				if ($page_data['type'] === 'index') {
					$pages_data[$url]['title_seo'] = $project_data['title'];
				} else {
					if (!empty($page_data['title'])) {
						$pages_data[$url]['title_seo'] = "{$project_data['title']} - {$page_data['title']}";
					} else {
						$pages_data[$url]['title_seo'] = $project_data['title'];
					}
				}
			}

			// Description.
			if (empty($page_data['description']) && !empty($project_data['description']) && $page_data['type'] !== 'template') {
				$pages_data[$url]['description'] = $project_data['description'];
			}

			// Image and image credit.
			if (empty($page_data['image'])) {
				if ($page_data['type'] === 'post') {
					if (!empty($project_data['image'])) {
						$pages_data[$url]['image'] = $project_data['image'];
	
						if (!empty($project_data['image_credit'])) {
							$pages_data[$url]['image_credit'] = $project_data['image_credit'];
						}
					}
				} else {
					if (!empty($project_data['header']['image'])) {
						$pages_data[$url]['image'] = $project_data['header']['image'];
	
						if (!empty($project_data['header']['image_credit'])) {
							$pages_data[$url]['image_credit'] = $project_data['header']['image_credit'];
						}
					}
				}
			}

			// Image type.
			$pages_data[$url]['image_type'] = stripos($pages_data[$url]['image'], 'logo') !== false
				? 'logo'
				: 'background';

			// Keywords.
			if (empty($page_data['keywords'])) {

				$project_title = Text::normalize_text($project_data['title']);
				$page_title = Text::normalize_text($page_data['title']);

				if (!empty($page_data['title']) && $project_title !== $page_title) {

					if ($page_data['type'] === 'template') {
						$pages_data[$url]['keywords'] = "{$project_title} {$page_title}";
					} else if (!empty($pages_data['*']['keywords'])) {
						$pages_data[$url]['keywords'] = str_ireplace('***TITLE***', $page_title, $pages_data['*']['keywords']);
					} else {
						$pages_data[$url]['keywords'] = $page_title;
					}

				} else if (!empty($project_data['keywords'])) {
					$pages_data[$url]['keywords'] = $project_data['keywords'];
				}
			}

			// Author.
			if (empty($page_data['author']) && !empty($project_data['author'])) {
				$pages_data[$url]['author'] = $project_data['author'];
			}

			// Date.
			if (empty($page_data['date'])) {
				$pages_data[$url]['date'] = date('Y-m-d');
			} else {
				$pages_data[$url]['date_full'] = date('F j, Y', strtotime($page_data['date']));
			}

			// Links.
			if (empty($page_data['links']) && !empty($project_data['links'])) {
				$pages_data[$url]['links'] = $project_data['links'];
			}
		}

		return $pages_data;
	}
	
	/**
	 * Builds a project according to build data.
	 * 
	 * @return	void
	 */
	private function build_project(): void {

		if (empty($this->project_data)) {
			CLI::display_error("Failed to build project: project data is empty.");
			exit;
		}

		if (empty($this->project_dir)) {
			CLI::display_error("Failed to build project: project directory path is empty.");
			exit;
		}
	
		if (empty($this->project_data['url'])) {
			CLI::display_error("Failed to build project: project URL is empty.");
			exit;
		}
	
		// Create and populate new website directory if it doesn't already exist.
		if (!is_dir($this->project_dir)) {
			$this->create_project_directory();
			$this->populate_project_directory();
		}

		$this->set_project_version();

		if (empty($this->project_data['version'])) {
			CLI::display_error("Failed to build project: project version is empty.");
			exit;
		}

		echo "\nBuilding project {$this->project_data['url']} v{$this->project_data['version']}";

		echo CLI::$verbose ? "\nProcessing files..." : "";

		$this->process_font_files();
		$this->copy_files();
		$this->compile_files();
		$this->generate_manifest();
		$this->template_replace_files();
	
		echo CLI::$verbose
			? "\nProcessing files DONE" . "\nBuilding project {$this->project_data['url']} DONE\n"
			: " - DONE\n";
	}
	
	/**
	 * Deploys a project to Netlify according to build data.
	 * 
	 * @return	void
	 */
	private function deploy_project(): void {

		if (empty($this->project_data)) {
			CLI::display_error("Failed to deploy project: project data is empty.");
			exit;
		}

		if (empty($this->project_dir)) {
			CLI::display_error("Failed to deploy project: project directory path is empty.");
			exit;
		}

		if (!is_dir($this->project_dir)) {
			CLI::display_error("Failed to deploy project: project directory doesn't exist.");
			exit;
		}
	
		if (empty($this->project_data['url'])) {
			CLI::display_error("Failed to deploy project: project URL is empty.");
			exit;
		}

		if (empty($this->project_data['netlify_id'])) {
			CLI::display_error("Failed to deploy project: Netlify ID is empty.");
			exit;
		}

		// Increment project version.
		$this->set_project_version(true);
		
		echo "Deploying project {$this->project_data['netlify_id']} v{$this->project_data['version']} to {$this->project_data['url']}";
		
		// Execute Netlify CLI deployment.
		$output = [];
		exec("npx netlify deploy --prod --dir={$this->project_dir} --site={$this->project_data['netlify_id']}", $output);
		$result = array_filter($output, fn($output_line) => stripos($output_line, 'Deploy URL') !== false);

		if (empty($result)) {
			CLI::display_error("Failed to deploy project.");
			exit;
		}

		echo CLI::$verbose ? "\nDeploying project {$this->project_data['url']} DONE\n" : "";
	}
	
	/**
	 * Set project version number in service worker file.
	 * 
	 * @param 	bool 	$increment 	If true, increments project version number.
	 * @return	void
	 */
	private function set_project_version(bool $increment = false): void {

		if (empty($this->project_data)) {
			CLI::display_error("Failed to set project version: project data is empty.");
			exit;
		}

		if (empty($this->project_dir)) {
			CLI::display_error("Failed to set project version: project directory path is empty.");
			exit;
		}

		if (!is_dir($this->project_dir)) {
			CLI::display_error("Failed to set project version: project directory doesn't exist.");
			exit;
		}

		$service_worker_file = "{$this->project_dir}/sw.js";
	
		if (!file_exists($service_worker_file)) {
			CLI::display_error("Failed to set project version: could not find {$service_worker_file} file");
			exit;
		}

		$file_contents = file_get_contents($service_worker_file, true);

		if (empty($file_contents)) {
			CLI::display_error("Failed to set project version: contents could not be extracted from {$service_worker_file}");
			exit;
		}
		
		// Extract version from project sw.js file before overwriting it.
		preg_match('/cache([0-9]+)/', $file_contents, $matches);
		$project_version = !empty($matches[1]) ? $matches[1] : 1;
		$project_version_length = strlen($project_version);

		if (empty($project_version)) {
			CLI::display_error("Failed to set project version: version could not be extracted from {$service_worker_file}");
			exit;
		}

		// Increment project version in service worker file.
		if (!empty($increment)) {
			$position = strpos($file_contents, $project_version);

			if (empty($position)) {
				CLI::display_error("Failed to set project version: version could not be found in {$service_worker_file}");
				exit;
			}

			$project_version++;

			$data = substr_replace($file_contents, $project_version, $position, $project_version_length);
			file_put_contents($service_worker_file, $data);
		}

		// Set project version in package.json.
		exec("npm pkg set version={$project_version} --prefix={$this->project_dir}");

		$this->project_data['version'] = $project_version;
	}
	
	/**
	 * Create project directory.
	 * 
	 * @return	void
	 */
	private function create_project_directory(): void {
		
		if (empty($this->project_dir)) {
			CLI::display_error("Failed to create project directory: project directory path is empty.");
			exit;
		}

		// Project directory already exists.
		if (is_dir($this->project_dir)) {
			return;
		}

		echo "\nCreating project directory {$this->project_dir}";
		mkdir($this->project_dir);

		if (!is_dir($this->project_dir)) {
			CLI::display_error("Failed to create project directory: mkdir() failed.");
			exit;
		}
		
		echo CLI::$verbose ? "\nCreating project directory DONE\n" : " - DONE";
	}

	/**
	 * Populate new project directory.
	 * 
	 * @return	void
	 */
	private function populate_project_directory(): void {

		if (empty($this->project_data)) {
			CLI::display_error("Failed to populate project directory: project data is empty.");
			exit;
		}

		if (empty($this->project_dir)) {
			CLI::display_error("Failed to populate project directory: project directory path is empty.");
			exit;
		}

		if (!is_dir($this->project_dir)) {
			CLI::display_error("Failed to populate project directory: project directory doesn't exist.");
			exit;
		}

		echo "\nPopulating new project directory {$this->project_dir}";

		// Create package.json.
		chdir($this->project_dir);
		exec("npm init -y");
		exec("npm pkg set name='{$this->project_data['url']}'");
		exec("npm pkg set description='{$this->project_data['description']}'");
		exec("npm pkg set version='1'");
		exec("npm pkg delete main");
		exec("npm pkg delete scripts.test");
		exec("npm pkg set scripts.build='webpack --mode production'");
		exec("npm install --save-dev webpack-cli ts-loader sass css-loader sass-loader style-loader");
		chdir(dirname(__DIR__));

		// Create scss directory.
		$scss_dir = "{$this->project_dir}/scss/";
		if (!is_dir($scss_dir)) {
			echo CLI::$verbose ? "\nCreating scss directory..." : "";
			mkdir($scss_dir);
			if (!is_dir($scss_dir)) {
				CLI::display_error("Failed to populate project directory: cound not create scss directory");
				exit;
			}
			echo CLI::$verbose ? "\nCreating scss directory DONE" : "";
		}

		// Create js directory.
		$js_dir = "{$this->project_dir}/js/";
		if (!is_dir($js_dir)) {
			echo CLI::$verbose ? "\nCreating js directory..." : "";
			mkdir($js_dir);
			if (!is_dir($js_dir)) {
				CLI::display_error("Failed to populate project directory: cound not create js directory");
				exit;
			}
			echo CLI::$verbose ? "\nCreating js directory DONE" : "";
		}

		// Create opt directory.
		$opt_dir = "{$this->project_dir}/opt/";
		if (!is_dir($opt_dir)) {
			echo CLI::$verbose ? "\nCreating opt directory..." : "";
			mkdir($opt_dir);
			if (!is_dir($opt_dir)) {
				CLI::display_error("Failed to populate project directory: cound not create opt directory");
				exit;
			}
			echo CLI::$verbose ? "\nCreating opt directory DONE" : "";
		}

		echo CLI::$verbose ? "\n\tCreating .gitignore file" : "";
		file_put_contents("{$this->project_dir}/.gitignore", 'node_modules');

		echo CLI::$verbose ? "\n\tCreating index.php file" : "";
		file_put_contents("{$this->project_dir}/index.php", '');
	
		echo CLI::$verbose ? "\n\tCreating scss/style.scss file" : "";
		$data = '';
		if (
			!empty($this->project_data['fonts']['heading'])
			&& !empty($this->project_data['fonts']['body'])
			&& !empty($this->project_data['colors']['main']['normal'])
			&& !empty($this->project_data['colors']['main']['dark'])
			&& !empty($this->project_data['colors']['accent']['normal'])
			&& !empty($this->project_data['colors']['accent']['dark'])
		) {
			$data = ':root {' .
				"\n\t" . '--font-heading: "' . $this->project_data['fonts']['heading'] . '";' .
				"\n\t" . '--font-body: "' . $this->project_data['fonts']['body'] . '";' .
				"\n\t" . '--color-main: #' . $this->project_data['colors']['main']['normal'] . ';' .
				"\n\t" . '--color-main-dark: #' . $this->project_data['colors']['main']['dark'] . ';' .
				"\n\t" . '--color-accent: #' . $this->project_data['colors']['accent']['normal'] . ';' .
				"\n\t" . '--color-accent-dark: #' . $this->project_data['colors']['accent']['dark'] . ';' .
			"\n" . '}';
		}
		file_put_contents("{$this->project_dir}/scss/style.scss", $data);

		echo CLI::$verbose ? "\n\tCreating js/main.ts file" : "";
		file_put_contents("{$this->project_dir}/js/main.ts", '');
	
		echo CLI::$verbose ? "\n\tCreating sw.js file" : "";
		file_put_contents("{$this->project_dir}/sw.js", 'const cacheName = "cache1";');

		echo CLI::$verbose ? "\nPopulating new project directory DONE\n" : " - DONE";
	}
	
	/**
	 * Copy files in project copy array into project directory.
	 * 
	 * @return	void
	 */
	private function copy_files(): void {

		if (empty($this->project_data)) {
			CLI::display_error("Failed to copy files: project data is empty.");
			exit;
		}
	
		if (empty($this->project_data['files']['copy']) || !is_array($this->project_data['files']['copy'])) {
			return;
		}
	
		if (empty($this->project_dir)) {
			CLI::display_error("Failed to copy files: project directory path is empty.");
			exit;
		}

		if (!is_dir($this->project_dir)) {
			CLI::display_error("Failed to copy files: project directory doesn't exist.");
			exit;
		}

		echo CLI::$verbose ? "\nCopying " . count($this->project_data['files']['copy']) . " files" : "";

		// Copy files into project directory.
		foreach ($this->project_data['files']['copy'] as $file) {
			$target_file = "{$this->project_dir}/{$file}";
			echo CLI::$verbose ? "\n\tCopying {$file} to {$target_file}" : "";
			copy(dirname(__DIR__) . "/files/{$file}", $target_file);
		}

		echo CLI::$verbose ? "\nCopying files DONE" : "";
	}
	
	/**
	 * Compiles and minifies style.scss and files in project compile array into project directory.
	 * 
	 * @return	void
	 */
	private function compile_files(): void {

		if (empty($this->project_data)) {
			CLI::display_error("Failed to compile files: project data is empty.");
			exit;
		}

		if (empty($this->project_dir)) {
			CLI::display_error("Failed to compile files: project directory path is empty.");
			exit;
		}

		if (!is_dir($this->project_dir)) {
			CLI::display_error("Failed to compile files: project directory doesn't exist.");
			exit;
		}

		if (empty($this->project_data['url'])) {
			CLI::display_error("Failed to compile files: project URL is empty.");
			exit;
		}

		$content_to_minify = null;

		// Compile Sass JavaScript.
		if (file_exists("{$this->project_dir}/js/index.ts")) {
			// First, remove any existing bundle.*.js files.
			exec("rm -rf {$this->project_dir}/js/bundle.*.js");

			// Create new bundle.
			echo CLI::$verbose ? "\nCompiling bundle" : "";
			exec("npm run build --prefix={$this->project_dir}");

			// Set bundle file name for use in <head>.
			$bundle_path = glob("{$this->project_dir}/js/bundle.*.js");

			if (!empty($bundle_path[0])) {
				$bundle_filename = basename($bundle_path[0]);
				$this->project_data['js_bundle_filename'] = 'js/' . $bundle_filename;
				$this->project_data['files']['cache'][] = 'js/' . $bundle_filename;
			}

			echo CLI::$verbose ? "\nCompiling {$bundle_filename} DONE" : "";
		}
	
		if (empty($this->project_data['files']['compile'] || !is_array($this->project_data['files']['compile']))) {
			return;
		}
	
		// Compile PHP files.
		echo CLI::$verbose ? "\nCompiling " . count($this->project_data['files']['compile']) . " files" : "";

		foreach ($this->project_data['files']['compile'] as $file) {
			if (empty($file['source_file']) || empty($file['target_file']) || empty($file['page'])) {
				continue;
			}

			// Skip any non-php files.
			if (stripos($file['source_file'], '.php') === false) {
				continue;
			}

			$target_file_path = dirname(__DIR__, 2) . "/{$this->project_data['url']}/{$file['target_file']}";

			echo CLI::$verbose ? "\n\tCompiling {$file['source_file']} to {$target_file_path}" : "";

			// Open the file using PHP with the project, page, and project_data as arguments.
			$project_data_json = escapeshellarg(json_encode($this->project_data));
			exec("php {$file['source_file']} {$this->project} {$file['page']} {$project_data_json} > {$target_file_path}");

			if (!file_exists($target_file_path)) {
				CLI::display_error("Failed to compile files: file {$target_file_path} does not exist.");
				continue;
			}

			// Get the file contents.
			$file_contents = file_get_contents($target_file_path);

			if (empty($file_contents)) {
				CLI::display_error("Failed to compile files: target file {$target_file_path} content is empty.");
				continue;
			}

			// Minify the file contents.
			$minified_file_contents = preg_replace('/\s+/S', ' ', $file_contents);

			if (empty($minified_file_contents)) {
				CLI::display_error("Failed to compile files: target file {$target_file_path} minified content is empty.");
				continue;
			}

			// Replace file.
			file_put_contents($target_file_path, $minified_file_contents);
		}

		echo CLI::$verbose ? "\nCompiling files DONE" : "";
	}
	
	/**
	 * Find and replace patterns in compiled and copied files.
	 * 
	 * @return	void
	 */
	private function template_replace_files(): void {

		if (empty($this->project_data)) {
			CLI::display_error("Failed to replace patterns in template files: project data is empty.");
			exit;
		}

		if (empty($this->project_data['url'])) {
			CLI::display_error("Failed to replace patterns in template files: project URL is empty.");
			exit;
		}

		if (empty($this->project_data['title'])) {
			CLI::display_error("Failed to replace patterns in template files: project title is empty.");
			exit;
		}

		if (empty($this->project_data['description'])) {
			CLI::display_error("Failed to replace patterns in template files: project description is empty.");
			exit;
		}

		if (empty($this->project_data['version'])) {
			CLI::display_error("Failed to replace patterns in template files: project version is empty.");
			exit;
		}
	
		echo CLI::$verbose ? "\nReplacing data in project files..." : "";

		$files = '';
	
		// Set files string from cache files array.
		if (!empty($this->project_data['files']['cache']) && is_array($this->project_data['files']['cache'])) {
	
			foreach ($this->project_data['files']['cache'] as $index => $file) {
				$files .= "\"{$file}\",";
	
				// Only add new line if followed by another file.
				if (!empty($this->project_data['files']['cache'][$index+1])) {
					$files .= "\n\t";
				}
			}
		}
	
		// Set patterns to find and replace.
		$patterns = [
			'// ***FILES***' => $files,
			'***URLS***' => $this->get_sitemap_urls(),
			'***REDIRECT_URLS***' => $this->get_redirect_urls(),
			'***URL***' => $this->project_data['url'],
			'***TITLE***' => $this->project_data['title'],
			'***DESCRIPTION***' => $this->project_data['description'],
			'***VERSION***' => $this->project_data['version'],
			'***DATE***' => date('Y-m-d'),
		];
	
		// Set array of files in which to replace patterns.
		$replace_files = array_unique(
			array_merge(
				array_column($this->project_data['files']['compile'], 'target_file'),
				$this->project_data['files']['copy'],
			),
		);

		if (empty($replace_files)) {
			CLI::display_error("Failed to replace patterns in template files: replace files array is empty.");
			exit;
		}
	
		// Replace common patterns in files.
		foreach ($replace_files as $file) {
			$replace_file = "{$this->project_data['url']}/{$file}";
			$replace_file_path = dirname(__DIR__, 2) . "/{$replace_file}";

			if (!file_exists($replace_file_path)) {
				CLI::display_error("Failed to replace patterns in template files: file {$replace_file} does not exist.");
				continue;
			}

			echo CLI::$verbose ? "\n\tReplacing content in {$replace_file}" : "";
			
			$file_contents = file_get_contents($replace_file_path);
			foreach ($patterns as $find => $replace) {
				if (stripos($file_contents, $find) !== false) {
					$file_contents = str_replace($find, $replace, $file_contents);
				}
			}

			// Replace file.
			file_put_contents($replace_file_path, $file_contents);
		}
	
		echo CLI::$verbose ? "\nReplacing data in project files DONE" : "";
	}
	
	/**
	 * Process font files.
	 * 
	 * Creates font directories, copies appropriate font files over, and adds them to cached files list.
	 * 
	 * @return	void
	 */
	private function process_font_files(): void {
	
		if (empty($this->project_data)) {
			CLI::display_error("Failed to process font files: project data is empty.");
			exit;
		}

		if (empty($this->project_dir)) {
			CLI::display_error("Failed to process font files: project directory path is empty.");
			exit;
		}

		if (!is_dir($this->project_dir)) {
			CLI::display_error("Failed to process font files: project directory doesn't exist.");
			exit;
		}
	
		// Create font directories and add heading and body font files to project data.
		if (!empty($this->project_data['fonts'])) {
			// Create fonts directory.
			$fonts_dir = "{$this->project_dir}/fonts";
			if (!is_dir($fonts_dir)) {
				echo CLI::$verbose ? "\nCreating fonts directory..." : "";
				mkdir($fonts_dir);
				if (!is_dir($fonts_dir)) {
					CLI::display_error("Failed to process font files: cound not create fonts directory");
					exit;
				}
				echo CLI::$verbose ? "\nCreating fonts directory DONE" : "";
			}
	
			if (!empty($this->project_data['fonts']['heading'])) {
				$heading_font = $this->project_data['fonts']['heading'];
				$heading_font_decoded = strtolower(str_ireplace(' ', '-', $heading_font));
				
				// Create heading font directory.
				$heading_font_dir = "{$this->project_dir}/fonts/{$heading_font_decoded}";
				if (!is_dir($heading_font_dir)) {
					echo CLI::$verbose ? "\nCreating {$heading_font_dir} directory..." : "";
					mkdir($heading_font_dir);
					if (!is_dir($heading_font_dir)) {
						CLI::display_error("Failed to process font files: cound not create heading font directory");
						exit;
					}
					echo CLI::$verbose ? "\nCreating {$heading_font_dir} directory DONE" : "";
				}

				// Add heading font files to copy and cache arrays.
				$heading_font_files = scandir(dirname(__DIR__) . "/files/fonts/{$heading_font_decoded}");
				$heading_font_files = array_diff($heading_font_files, ['.', '..']);
				foreach ($heading_font_files as $index => $heading_font_file) {
					$heading_font_files[$index] = "fonts/{$heading_font_decoded}/{$heading_font_file}";
				}

				if (!empty($heading_font_files)) {
					$this->project_data['files']['copy'] = !empty($this->project_data['files']['copy'])
						? array_merge($this->project_data['files']['copy'], $heading_font_files)
						: $heading_font_files;
					$this->project_data['files']['cache'] = !empty($this->project_data['files']['cache'])
						? array_merge($this->project_data['files']['cache'], $heading_font_files)
						: $heading_font_files;
				}
			}
	
			if (!empty($this->project_data['fonts']['body'])) {
				$body_font = $this->project_data['fonts']['body'];
				$body_font_decoded = strtolower(str_ireplace(' ', '-', $body_font));
				
				// Create body font directory.
				$body_font_dir = "{$this->project_dir}/fonts/{$body_font_decoded}";
				if (!is_dir($body_font_dir)) {
					echo CLI::$verbose ? "\nCreating {$body_font_dir} directory..." : "";
					mkdir($body_font_dir);
					if (!is_dir($body_font_dir)) {
						CLI::display_error("Failed to process font files: cound not create body font directory");
						exit;
					}
					echo CLI::$verbose ? "\nCreating {$body_font_dir} directory DONE" : "";
				}

				// Add body font files to copy and cache arrays.
				$body_font_files = scandir(dirname(__DIR__) . "/files/fonts/{$body_font_decoded}");
				$body_font_files = array_diff($body_font_files, ['.', '..']);
				foreach ($body_font_files as $index => $body_font_file) {
					$body_font_files[$index] = "fonts/{$body_font_decoded}/{$body_font_file}";
				}
		
				if (!empty($body_font_files)) {
					$this->project_data['files']['copy'] = !empty($this->project_data['files']['copy'])
						? array_merge($this->project_data['files']['copy'], $body_font_files)
						: $body_font_files;
					$this->project_data['files']['cache'] = !empty($this->project_data['files']['cache'])
						? array_merge($this->project_data['files']['cache'], $body_font_files)
						: $body_font_files;
				}
			}
		}
	
		// Create fontawesome directories and add files to project data.
		if (!empty($this->project_data['fontawesome'])) {
			// Create fontawesome directories.
			$directories = [
				"{$this->project_dir}/fonts/fontawesome",
				"{$this->project_dir}/fonts/fontawesome/css",
				"{$this->project_dir}/fonts/fontawesome/webfonts",
			];
			foreach ($directories as $directory) {
				if (!is_dir($directory)) {
					echo CLI::$verbose ? "\nCreating {$directory} directory..." : "";
					mkdir($directory);
					if (!is_dir($directory)) {
						CLI::display_error("Failed to process font files: cound not create {$directory} directory");
						exit;
					}
					echo CLI::$verbose ? "\nCreating {$directory} directory DONE" : "";
				}				
			}

			// Add fontawesome files to copy and cache arrays.
			$fontawesome_files = [
				'fonts/fontawesome/css/brands.min.css',
				'fonts/fontawesome/css/fontawesome.min.css',
				'fonts/fontawesome/css/solid.min.css',
				'fonts/fontawesome/webfonts/fa-brands-400.eot',
				'fonts/fontawesome/webfonts/fa-brands-400.svg',
				'fonts/fontawesome/webfonts/fa-brands-400.ttf',
				'fonts/fontawesome/webfonts/fa-brands-400.woff',
				'fonts/fontawesome/webfonts/fa-brands-400.woff2',
				'fonts/fontawesome/webfonts/fa-solid-900.eot',
				'fonts/fontawesome/webfonts/fa-solid-900.svg',
				'fonts/fontawesome/webfonts/fa-solid-900.ttf',
				'fonts/fontawesome/webfonts/fa-solid-900.woff',
				'fonts/fontawesome/webfonts/fa-solid-900.woff2'
			];
	
			$this->project_data['files']['copy'] = !empty($this->project_data['files']['copy'])
				? array_merge($this->project_data['files']['copy'], $fontawesome_files)
				: $fontawesome_files;
			$this->project_data['files']['cache'] = !empty($this->project_data['files']['cache'])
				? array_merge($this->project_data['files']['cache'], $fontawesome_files)
				: $fontawesome_files;
		}
	}
	
	/**
	 * Get sitemap URLs.
	 * 
	 * Used in sitemap.xml file.
	 * 
	 * @return	string	Sitemap URLs.
	 */
	private function get_sitemap_urls(): string {

		if (empty($this->project_data)) {
			CLI::display_error("Failed to get sitemap URLs: project data is empty.");
			exit;
		}
		
		// Add any redirected URLs to sitemap URLs.
		if (!empty($this->project_data['redirects']) && is_array($this->project_data['redirects'])) {
			foreach ($this->project_data['redirects'] as $index => $redirect) {
				$url = trim($redirect['from'], '/');
				if (($url !== '') && ($url !== 'index') && (stripos($url, '.') !== false) && (stripos($url, '*') !== false)) {
					$this->project_data['sitemap']['urls'][] .= $url;
				}
			}
		}

		$sitemap_urls = '';
	
		// Add <url> data for each URL.
		if (!empty($this->project_data['sitemap']['urls']) && is_array($this->project_data['sitemap']['urls'])) {
			foreach ($this->project_data['sitemap']['urls'] as $index => $url) {
				$sitemap_urls .= str_ireplace(
					[
						'{url}',
						'{date}',
					],
					[
						"{$this->project_data['url']}/" . ($url !== 'index' ? $url : ''),
						date('Y-m-d'),
					],
					"\t<url>\n" .
						"\t\t<loc>https://{url}</loc>\n" . 
						"\t\t<lastmod>{date}</lastmod>\n" . 
					"\t</url>\n",
				);
			}
		}
	
		return $sitemap_urls;
	}
	
	/**
	 * Get redirect URLs.
	 * 
	 * Used in _redirects file.
	 * 
	 * @return	string	Sitemap URLs.
	 */
	private function get_redirect_urls(): string {

		if (empty($this->project_data)) {
			CLI::display_error("Failed to get redirect URLs: project data is not set.");
			exit;
		}
	
		$redirect_urls = '';
	
		if (!empty($this->project_data['files']['compile']) && is_array($this->project_data['files']['compile'])) {
			foreach ($this->project_data['files']['compile'] as $file) {
				if (empty($file['target_file'])) {
					continue;
				}

				if ($file['page'] !== 'index') {
					$redirect_urls .= str_ireplace('{title}', $file['page'], '/{title}/* /{title}.html 200') . "\n";
				}
			}
		}
	
		// If redirects are set, add to urls.
		if (!empty($this->project_data['redirects']) && is_array($this->project_data['redirects'])) {
			foreach ($this->project_data['redirects'] as $index => $redirect_url) {
				$redirect_urls .= "{$redirect_url['from']} {$redirect_url['to']}\n";
			}
		}
	
		$redirect_urls = trim($redirect_urls, "\n");
		$redirect_urls .= "\n/* /index.html 200";
	
		return $redirect_urls;
	}
	
	/**
	 * Generate manifest.json.
	 *
	 * @return	void
	 */
	private function generate_manifest(): void {
	
		if (empty($this->project_data)) {
			CLI::display_error("Failed to generate manifest: project data is not set.");
			exit;
		}

		if (empty($this->project_data['url'])) {
			CLI::display_error("Failed to generate manifest: project URL is empty.");
			exit;
		}

		$manifest_file_path = dirname(__DIR__, 2) . "/{$this->project_data['url']}/manifest.json";

		if (!file_exists($manifest_file_path)) {
			CLI::display_error("Failed to generate manifest: file {$manifest_file_path} does not exist.");
			exit;
		}

		$file_contents = file_get_contents($manifest_file_path);

		if (empty($file_contents)) {
			CLI::display_error("Failed to generate manifest: manifest file contents is empty.");
			exit;
		}
	
		// Modify manifest data from project data.
		$manifest_data = json_decode($file_contents);

		if (empty($manifest_data)) {
			CLI::display_error("Failed to generate manifest: manifest file data is empty.");
			exit;
		}

		echo CLI::$verbose ? "\n\tGenerating manifest.json" : "";
	
		if (!empty($this->project_data['categories'])) {
			$manifest_data->categories = $this->project_data['categories'];
		}

		if (!empty($this->project_data['screenshots']) && is_array($this->project_data['screenshots'])) {
			$manifest_data->screenshots = [];
			foreach ($this->project_data['screenshots'] as $file) {
				$manifest_data->screenshots[] = [
					'src' => $file,
					'sizes' => '1500x800',
					'type' => 'image/png'
				];
			}
		}

		if (!empty($this->project_data['shortcuts'])) {
			$manifest_data->shortcuts = $this->project_data['shortcuts'];
		}

		if (!empty($this->project_data['url'])) {
			$manifest_data->related_applications[] = [
				'platform' => 'webapp',
				'url' => "https://{$this->project_data['url']}/manifest.json",
			];
		}
	
		if (!empty($this->project_data['android_app_id']) || !empty($this->project_data['apple_app_id'])) {
	
			$android_app_id = null;
			if (!empty($this->project_data['android_app_id']['paid'])) {
				$android_app_id = $this->project_data['android_app_id']['paid'];
			} else if (!empty($this->project_data['android_app_id']['free'])) {
				$android_app_id = $this->project_data['android_app_id']['free'];
			}
	
			$apple_app_id = null;
			if (!empty($this->project_data['apple_app_id']['paid'])) {
				$apple_app_id = $this->project_data['apple_app_id']['paid'];
			} else if (!empty($this->project_data['apple_app_id']['free'])) {
				$apple_app_id = $this->project_data['apple_app_id']['free'];
			}
	
			if (!empty($android_app_id) && ($android_app_id !== 'disabled')) {
				$manifest_data->related_applications[] = [
					'platform' => 'play',
					'url' => "https://play.google.com/store/apps/details?id={$android_app_id}",
					'id' => $android_app_id
				];
				$manifest_data->prefer_related_applications = true;
			}
	
			if (!empty($apple_app_id) && ($apple_app_id !== 'disabled')) {
				$manifest_data->related_applications[] = [
					'platform' => 'itunes',
					'url' => "https://itunes.apple.com/app/example-app1/id123456789{$apple_app_id}",
				];
				$manifest_data->prefer_related_applications = true;
			}
		}

		// Replace file.
		file_put_contents($manifest_file_path, json_encode($manifest_data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

		echo CLI::$verbose ? "\n\tGenerating manifest.json DONE" : "";
	}
	
	/**
	 * Generate favicons.
	 * 
	 * @return	void
	 */
	private function generate_favicons(): void {

		if (empty($this->project_dir)) {
			CLI::display_error("Failed to generate favicons: project directory path is empty.");
			exit;
		}

		if (!is_dir($this->project_dir)) {
			CLI::display_error("Failed to generate favicons: project directory doesn't exist.");
			exit;
		}
			
		if (!file_exists("{$this->project_dir}/img/favicon.svg")) {
			CLI::display_error("Failed to generate favicons: favicon.svg is missing.");
			exit;
		}

		echo "Generating favicons...";

		// Set up favicon config data.
		$favicon_data_config = [
			'masterPicture' => "{$this->project_dir}/img/favicon.svg",
			'iconsPath' => '/img/',
			'design' =>
			[
				'ios' =>
				[
					'pictureAspect' => 'backgroundAndMargin',
					'backgroundColor' => '#000000',
					'margin' => '18%',
					'assets' =>
					[
						'ios6AndPriorIcons' => true,
						'ios7AndLaterIcons' => true,
						'precomposedIcons' => false,
						'declareOnlyDefaultIcon' => true,
					],
				],
				'desktopBrowser' =>
				[
					'design' => 'raw',
				],
				'windows' =>
				[
					'pictureAspect' => 'noChange',
					'backgroundColor' => '#000000',
					'onConflict' => 'override',
					'assets' =>
					[
						'windows80Ie10Tile' => false,
						'windows10Ie11EdgeTiles' =>
						[
							'small' => false,
							'medium' => true,
							'big' => false,
							'rectangle' => false,
						],
					],
				],
				'androidChrome' =>
				[
					'pictureAspect' => 'noChange',
					'themeColor' => '#000000',
					'manifest' =>
					[
						'display' => 'standalone',
						'orientation' => 'notSet',
						'onConflict' => 'override',
						'declared' => true,
					],
					'assets' =>
					[
						'legacyIcon' => false,
						'lowResolutionIcons' => true,
					],
				],
				'safariPinnedTab' =>
				[
					'pictureAspect' => 'blackAndWhite',
					'threshold' => 89.21875,
					'themeColor' => '#000000',
				],
			],
			'settings' =>
			[
				'compression' => 2,
				'scalingAlgorithm' => 'Mitchell',
				'errorOnImageTooSmall' => false,
				'readmeFile' => false,
				'htmlCodeFile' => false,
				'usePathAsIs' => false,
			],
		];

		// Create favicon data config file.
		file_put_contents("{$this->project_dir}/favicon_config.json", json_encode($favicon_data_config, JSON_PRETTY_PRINT));

		// Generate favicons.
		exec("npx real-favicon generate {$this->project_dir}/favicon_config.json favicon_data.json {$this->project_dir}/img/");

		// Remove generated favicon data file.
		exec('rm -rf favicon_data.json');

		// Remove favicon data config file.
		exec("rm -rf {$this->project_dir}/favicon_config.json");
		
		// Remove favicon manifest file.
		exec("rm -rf {$this->project_dir}/img/site.webmanifest");
		
		echo CLI::$verbose ? "\nGenerating favicons DONE" : " - DONE\n";
	}
}