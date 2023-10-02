<?php

/**
 * Project class.
 */
class Project {

	/**
	 * Directory path.
	 * 
	 * @var string
	 */
	public static string $directory_path = '';

	/**
	 * Version.
	 * 
	 * @var string
	 */
	public static string $version = '';

	/**
	 * URL.
	 * 
	 * @var string
	 */
	public static string $url = '';

	/**
	 * Title.
	 * 
	 * @var string
	 */
	public static string $title = '';

	/**
	 * Description.
	 * 
	 * @var string
	 */
	public static string $description = '';

	/**
	 * Keywords.
	 * 
	 * @var string
	 */
	public static string $keywords = '';

	/**
	 * Categories.
	 * 
	 * @var array<int, string>
	 */
	public static array $categories = [];

	/**
	 * Netlify ID.
	 * 
	 * @var string
	 */
	public static string $netlify_id = '';

	/**
	 * Google Tag Manager (GTM) ID.
	 * 
	 * @var string
	 */
	public static string $gtm_id = '';

	/**
	 * Facebook Pixel ID.
	 * 
	 * @var string
	 */
	public static string $fbpixel_id = '';

	/**
	 * Repixel ID.
	 * 
	 * @var string
	 */
	public static string $repixel_id = '';

	/**
	 * Google Ads (enabled/disabled).
	 * 
	 * @var boolean
	 */
	public static bool $google_ads = false;

	/**
	 * Amazon Ads (enabled/disabled).
	 * 
	 * @var boolean
	 */
	public static bool $amazon_ads = false;

	/**
	 * Other Ads (enabled/disabled).
	 * 
	 * @var boolean
	 */
	public static bool $other_ads = false;

	/**
	 * Google API data.
	 * 
	 * @var array{
	 * 	google_api_client_id: string,
	 * 	google_api_client_key: string,
	 * 	google_api_key: string,
	 * 	google_api_scope: string,
	 * 	google_api_url: string,
	 * 	google_api_callback: string,
	 * }
	 */
	public static array $google_api = [
		'google_api_client_id' => '',
		'google_api_client_key' => '',
		'google_api_key' => '',
		'google_api_scope' => '',
		'google_api_url' => '',
		'google_api_callback' => '',
	];

	/**
	 * Fonts.
	 * 
	 * @var array{
	 * 	heading: string,
	 * 	body: string,
	 * }
	 */
	public static array $fonts = [
		'heading' => '',
		'body' => '',
	];

	/**
	 * Colors.
	 * 
	 * @var array{
	 * 	main: array{
	 * 		normal: string,
	 * 		dark: string,
	 * 	},
	 * 	accent: array{
	 * 		normal: string,
	 * 		dark: string,
	 * 	},
	 * }
	 */
	public static array $colors = [
		'main' => [
			'normal' => '',
			'dark' => '',
		],
		'accent' => [
			'normal' => '',
			'dark' => '',
		]
	];

	/**
	 * Fontawesome (enabled/disabled).
	 * 
	 * @var boolean
	 */
	public static bool $fontawesome = false;

	/**
	 * Android App ID.
	 * 
	 * @var array{
	 * 	free: string,
	 * 	paid: string,
	 * }
	 */
	public static array $android_app_id = [
		'free' => '',
		'paid' => '',
	];

	/**
	 * Apple App ID.
	 * 
	 * @var array{
	 * 	free: string,
	 * 	paid: string,
	 * }
	 */
	public static array $apple_app_id = [
		'free' => '',
		'paid' => '',
	];

	/**
	 * Screenshots.
	 * 
	 * @var array<int, string>
	 */
	public static array $screenshots = [];

	/**
	 * Shortcuts.
	 * 
	 * @var array<int, array{
	 * 	name: string,
	 * 	description: string,
	 * 	url: string,
	 *	icons: array<int, array{
	 *		src: string,
	 *		sizes: string,
	 *	}>
	 * }>
	 */
	public static array $shortcuts = [
		[
			'name' => '',
			'description' => '',
			'url' => '',
			'icons' => [
				[
					'src' => '',
					'sizes' => '',
				]
			],
		]
	];

	/**
	 * Links.
	 * 
	 * @var array<int, array{
	 * 	url: string,
	 * 	title: string,
	 * }>
	 */
	public static array $links = [
		[
			'url' => '',
			'title' => '',
		]
	];

	/**
	 * Navigation.
	 * 
	 * @var array{
	 * 	image: string,
	 * 	items: array<int, array{
	 * 		url: string,
	 * 		title: string,
	 * 	}>,
	 * }
	 */
	public static array $nav = [
		'image' => '',
		'items' => [
			[
				'url' => '',
				'title' => '',
			]
		],
	];

	/**
	 * Header.
	 * 
	 * @var array{
	 * 	title: string,
	 * 	description: string,
	 * 	image: string,
	 * 	image_credit: string,
	 * }
	 */
	public static array $header = [
		'title' => '',
		'description' => '',
		'image' => '',
		'image_credit' => '',
	];

	/**
	 * Social.
	 * 
	 * @var array{
	 * 	mailto: string,
	 * 	facebook: string,
	 * 	twitter: string,
	 * 	github: string,
	 * 	patreon: string,
	 * 	paypal: string,
	 * 	portfolio: string,
	 * 	yelp: string,
	 * 	tripadvisor: string,
	 * 	custom: array<int, array{
	 * 		url: string,
	 * 		label: string,
	 * 		text: string,
	 * 		link: string,
	 * 	}>,
	 * }
	 */
	public static array $social = [
		'mailto' => '',
		'facebook' => '',
		'twitter' => '',
		'github' => '',
		'patreon' => '',
		'paypal' => '',
		'portfolio' => '',
		'yelp' => '',
		'tripadvisor' => '',
		'custom' => [
			[
				'url' => '',
				'label' => '',
				'text' => '',
				'link' => '',
			]
		]
	];

	/**
	 * Author.
	 * 
	 * @var array{
	 * 	name: string,
	 * 	twitter: string,
	 * }
	 */
	public static array $author = [
		'name' => '',
		'twitter' => '',
	];

	/**
	 * Redirects.
	 * 
	 * @var array<int, array{
	 * 	from: string,
	 * 	to: string,
	 * }>
	 */
	public static array $redirects = [
		[
			'from' => '',
			'to' => '',
		]
	];

	/**
	 * Cache files.
	 * 
	 * @var array<int, string>
	 */
	public static array $cache_files = [];

	/**
	 * Optional files.
	 * 
	 * @var array<int, string>
	 */
	public static array $opt_files = [];

	/**
	 * Sitemap URLs.
	 * 
	 * @var array<int, string>
	 */
	public static array $sitemap_urls = [
		'index',
	];

	/**
	 * Pages.
	 * 
	 * @var array<string, array{
	 * 	type: string,
	 * 	url: string,
	 * 	title: string,
	 * 	title_seo?: string,
	 * 	description: string,
	 * 	image?: string,
	 * 	image_credit?: string,
	 * 	image_type?: string,
	 * 	keywords?: string,
	 * 	author?: array{
	 * 		name: string,
	 * 		twitter: string,
	 * 	},
	 * 	date?: string,
	 * 	date_full?: string,
	 * 	content?: string,
	 * }>
	 */
	public static array $pages = [
		'' => [
			'type' => '',
			'url' => '',
			'title' => '',
			'title_seo' => '',
			'description' => '',
			'image' => '',
			'image_credit' => '',
			'image_type' => '',
			'keywords' => '',
			'author' => [
				'name' => '',
				'twitter' => '',
			],
			'date' => '',
			'date_full' => '',
			'content' => '',
		],
	];

	/**
	 * Set project version.
	 * 
	 * @param	string	$version	Project version.
	 */
	public static function set_project_version(string $version): void {
		self::$version = $version;
	}

	/**
	 * Constructor.
	 * 
	 * Sets up project data from projects.json.
	 * 
	 * @param	string	$project	Project name.
	 */
	public function __construct(string $project) {
		if (empty($project)) {
			CLI::display_error("Failed to construct project data: project is empty.");
			exit;
		}

		// Get project from projects.json.
		$project_data = $this->get_project($project);
		
		// Set project directory.
		self::$directory_path = dirname(__DIR__, 2) . '/' . $project;

		// Set all class variables from $project_data.
		foreach ($project_data as $field => $value) {
			self::${$field} = $value;
		}

		// Set pages.
		$this->set_pages();
	}

	/**
	 * Get project from projects.json.
	 * 
	 * @param	string	$project	Project name.
	 * @return	array{
	 * 	url: string,
	 * 	title: string,
	 * 	description: string,
	 * 	keywords: string,
	 * 	categories: array<int, string>,
	 * 	netlify_id: string,
	 * 	gtm_id: string,
	 * 	fbpixel_id: string,
	 * 	repixel_id: string,
	 * 	google_ads: boolean,
	 * 	amazon_ads: boolean,
	 * 	other_ads: boolean,
	 * 	google_api: array{
	 * 		google_api_client_id: string,
	 * 		google_api_client_key: string,
	 * 		google_api_key: string,
	 * 		google_api_scope: string,
	 * 		google_api_url: string,
	 * 		google_api_callback: string,
	 * 	},
	 * 	fonts: array{
	 * 		heading: string,
	 * 		body: string,
	 * 	},
	 * 	colors: array{
	 * 		main: array{
	 * 			normal: string,
	 * 			dark: string,
	 * 		},
	 * 		accent: array{
	 * 			normal: string,
	 * 			dark: string,
	 * 		},
	 * 	},
	 * 	fontawesome: boolean,
	 * 	android_app_id: array{
	 * 		free: string,
	 * 		paid: string,
	 * 	},
	 * 	apple_app_id: array{
	 * 		free: string,
	 * 		paid: string,
	 * 	},
	 * 	screenshots: array<int, string>,
	 * 	shortcuts: array<int, array{
	 * 		name: string,
	 * 		description: string,
	 * 		url: string,
	 *		icons: array<int, array{
	 *			src: string,
	 *			sizes: string,
	 *		}>
	 * 	}>,
	 * 	links: array<int, array{
	 * 		url: string,
	 * 		title: string,
	 * 	}>,
	 * 	nav: array{
	 * 		image: string,
	 * 		items: array<int, array{
	 * 			url: string,
	 * 			title: string,
	 * 		}>,
	 * 	},
	 * 	header: array{
	 * 		title: string,
	 * 		description: string,
	 * 		image: string,
	 * 		image_credit: string,
	 * 	},
	 * 	social: array{
	 * 		mailto: string,
	 * 		facebook: string,
	 * 		twitter: string,
	 * 		github: string,
	 * 		patreon: string,
	 * 		paypal: string,
	 * 		portfolio: string,
	 * 		yelp: string,
	 * 		tripadvisor: string,
	 * 		custom: array<int, array{
	 * 			url: string,
	 * 			label: string,
	 * 			text: string,
	 * 			link: string,
	 * 		}>,
	 * 	},
	 * 	author: array{
	 * 		name: string,
	 * 		twitter: string,
	 * 	},
	 * 	redirects: array<int, array{
	 * 		from: string,
	 * 		to: string,
	 * 	}>,
	 * 	cache_files: array<int, string>,
	 * 	opt_files: array<int, string>,
	 * 	sitemap_urls: array<int, string>,
	 * 	pages: array<string, array{
	 * 		type: string,
	 * 		url: string,
	 * 		title: string,
	 * 		title_seo?: string,
	 * 		description: string,
	 * 		image?: string,
	 * 		image_credit?: string,
	 * 		image_type?: string,
	 * 		keywords?: string,
	 * 		author?: array{
	 * 			name: string,
	 * 			twitter: string,
	 * 		},
	 * 		date?: string,
	 * 		date_full?: string,
	 * 		content?: string,
	 * 	}>
	 * }
	 */
	private function get_project(string $project): array {
		if (empty($project)) {
			CLI::display_error("Failed to get project: project is empty.");
			exit;
		}

		// Get all projects.
		$projects = new Projects();

		if (empty($projects->projects_data) || !is_array($projects->projects_data)) {
			CLI::display_error("Failed to get project: projects data is empty or is not an array.");
			exit;
		}
	
		// Get data for current project.
		$project_data = array_values(
			array_filter(
				$projects->projects_data,
				fn($current_project_data) => $current_project_data['url'] === $project,
			)
		);

		if (empty($project_data[0])) {
			CLI::display_error("Failed to get project: could not get project from projects.json file");
			exit;
		}

		/**
		 * @var	array{
		 * 	url: string,
		 * 	title: string,
		 * 	description: string,
		 * 	keywords: string,
		 * 	categories: array<int, string>,
		 * 	netlify_id: string,
		 * 	gtm_id: string,
		 * 	fbpixel_id: string,
		 * 	repixel_id: string,
		 * 	google_ads: boolean,
		 * 	amazon_ads: boolean,
		 * 	other_ads: boolean,
		 * 	google_api: array{
		 * 		google_api_client_id: string,
		 * 		google_api_client_key: string,
		 * 		google_api_key: string,
		 * 		google_api_scope: string,
		 * 		google_api_url: string,
		 * 		google_api_callback: string,
		 * 	},
		 * 	fonts: array{
		 * 		heading: string,
		 * 		body: string,
		 * 	},
		 * 	colors: array{
		 * 		main: array{
		 * 			normal: string,
		 * 			dark: string,
		 * 		},
		 * 		accent: array{
		 * 			normal: string,
		 * 			dark: string,
		 * 		},
		 * 	},
		 * 	fontawesome: boolean,
		 * 	android_app_id: array{
		 * 		free: string,
		 * 		paid: string,
		 * 	},
		 * 	apple_app_id: array{
		 * 		free: string,
		 * 		paid: string,
		 * 	},
		 * 	screenshots: array<int, string>,
		 * 	shortcuts: array<int, array{
		 * 		name: string,
		 * 		description: string,
		 * 		url: string,
		 *		icons: array<int, array{
		 *			src: string,
 		 *			sizes: string,
		 *		}>
		 * 	}>,
		 * 	links: array<int, array{
		 * 		url: string,
		 * 		title: string,
		 * 	}>,
		 * 	nav: array{
		 * 		image: string,
		 * 		items: array<int, array{
		 * 			url: string,
		 * 			title: string,
		 * 		}>,
		 * 	},
		 * 	header: array{
		 * 		title: string,
		 * 		description: string,
		 * 		image: string,
		 * 		image_credit: string,
		 * 	},
		 * 	social: array{
		 * 		mailto: string,
		 * 		facebook: string,
		 * 		twitter: string,
		 * 		github: string,
		 * 		patreon: string,
		 * 		paypal: string,
		 * 		portfolio: string,
		 * 		yelp: string,
		 * 		tripadvisor: string,
		 * 		custom: array<int, array{
		 * 			url: string,
		 * 			label: string,
		 * 			text: string,
		 * 			link: string,
		 * 		}>,
		 * 	},
		 * 	author: array{
		 * 		name: string,
		 * 		twitter: string,
		 * 	},
		 * 	redirects: array<int, array{
		 * 		from: string,
		 * 		to: string,
		 * 	}>,
		 * 	cache_files: array<int, string>,
		 * 	opt_files: array<int, string>,
		 * 	sitemap_urls: array<int, string>,
		 * 	pages: array<string, array{
		 * 		type: string,
		 * 		url: string,
		 * 		title: string,
		 * 		title_seo?: string,
		 * 		description: string,
		 * 		image?: string,
		 * 		image_credit?: string,
		 * 		image_type?: string,
		 * 		keywords?: string,
		 * 		author?: array{
		 * 			name: string,
		 * 			twitter: string,
		 * 		},
		 * 		date?: string,
		 * 		date_full?: string,
		 * 		content?: string,
		 * 	}>
		 * }
		 */
		return $project_data[0];
	}

	/**
	 * Set pages.
	 */
	private function set_pages(): void {
		// Set up base pages for all projects.
		self::$pages = array_merge(
			self::$pages ?? [],
			[
				'index' => [
					'type' => 'index',
					'url' => 'index',
					'title' => self::$title,
					'description' => self::$description,
				],
				'disclaimer' => [
					'type' => 'template',
					'url' => 'disclaimer',
					'title' => 'Disclaimer',
					'description' => self::$title . ' disclaimer',
				],
				'privacy-policy' => [
					'type' => 'template',
					'url' => 'privacy-policy',
					'title' => 'Privacy Policy',
					'description' => self::$title . ' privacy policy',
				],
				'terms-and-conditions' => [
					'type' => 'template',
					'url' => 'terms-and-conditions',
					'title' => 'Terms and Conditions',
					'description' => self::$title . ' terms and conditions',
				]
			]
		);
		
		$this->set_data_file_pages();
		$this->set_posts_file_pages();
		$this->set_page_defaults();
	}

	/**
	 * Get page data. Ensures only certain array keys are included in page data.
	 * 
	 * @param  array{
	 * 	url: string,
	 * 	title: string,
	 * 	title_seo: string,
	 * 	description: string,
	 * 	image: string,
	 * 	image_credit: string,
	 * 	image_type: string,
	 * 	keywords: string,
	 * 	author: array{
	 * 		name: string,
	 * 		twitter: string,
	 * 	},
	 * 	date: string,
	 * 	date_full: string,
	 * 	content: string,
	 * }	$page	Page data.
	 * @return 	array{
	 * 	url: string,
	 * 	title: string,
	 * 	title_seo: string,
	 * 	description: string,
	 * 	image: string,
	 * 	image_credit: string,
	 * 	image_type: string,
	 * 	keywords: string,
	 * 	author: array{
	 * 		name: string,
	 * 		twitter: string,
	 * 	},
	 * 	date: string,
	 * 	date_full: string,
	 * 	content: string,
	 * }
	 */
	private function get_page_data(array $page): array {
		return [
			'url' => $page['url'] ?? '',
			'title' => $page['title'] ?? '',
			'title_seo' => $page['title_seo'] ?? '',
			'description' => $page['description'] ?? '',
			'image' => !empty($page['image']) && is_string($page['image']) ? 'img/' . $page['image'] : '',
			'image_credit' => $page['image_credit'] ?? '',
			'image_type' => $page['image_type'] ?? '',
			'keywords' => $page['keywords'] ?? '',
			'author' => $page['author'] ?? [],
			'date' => $page['date'] ?? '',
			'date_full' => $page['date_full'] ?? '',
			'content' => $page['content'] ?? '',
		];
	}

	/**
	 * Set data file pages. Get data from data.json and add pages to self::$pages.
	 */
	private function set_data_file_pages(): void {
		if (empty(self::$directory_path)) {
			CLI::display_error("Failed to set data file pages: project directory path is empty.");
			exit;
		}

		if (!is_dir(self::$directory_path)) {
			CLI::display_error("Failed to set data file pages: project directory doesn't exist.");
			exit;
		}

		$data_file_path = self::$directory_path . '/data.json';

		// Data file is optional. If it doesn't exist, do nothing.
		if (!file_exists($data_file_path)) {
			return;
		}

		$search_result_pages = $this->get_json_file_data($data_file_path);

		if (empty($search_result_pages)) {
			CLI::display_error("Failed to set posts file pages: posts file json is empty");
			exit;
		}

		if (is_array($search_result_pages)) {
			foreach ($search_result_pages as $page) {
				if (empty($page['title'])) {
					continue;
				}

				/**
				 * In data.json, page titles can be an array, to allow for generating different URLs with the same content.
				 * Add each title to self::$pages so that a page will be generated for each.
				 */
				if (is_array($page['title'])) {
					foreach ($page['title'] as $title) {
						if (!empty($title)) {

							// If URL isn't provided, create it from the title.
							$url = (string) ($page['url'] ?? Text::normalize_text($title, 'url'));

							$page_data = $this->get_page_data($page);
							
							self::$pages[$url] = [
								'type' => 'search',
								...$page_data,
								'title' => $title,
							];
						}
					}
				} else if (is_string($page['title'])) {
					// If URL isn't provided, create it from the title.
					$url = (string) ($page['url'] ?? Text::normalize_text($page['title'], 'url'));

					$page_data = $this->get_page_data($page);

					self::$pages[$url] = [
						'type' => 'search',
						...$page_data,
					];
				}
			}
		}
	}

	/**
	 * Set posts file pages. Get data from posts.json and add pages to self::$pages.
	 */
	private function set_posts_file_pages(): void {
		if (empty(self::$directory_path)) {
			CLI::display_error("Failed to set posts file pages: project directory path is empty.");
			exit;
		}

		if (!is_dir(self::$directory_path)) {
			CLI::display_error("Failed to set posts file pages: project directory doesn't exist.");
			exit;
		}

		$posts_file_path = self::$directory_path . '/posts.json';

		// Posts file is optional. If it doesn't exist, do nothing.
		if (!file_exists($posts_file_path)) {
			return;
		}

		$post_pages = $this->get_json_file_data($posts_file_path);

		if (empty($post_pages)) {
			CLI::display_error("Failed to set posts file pages: posts file json is empty.");
			exit;
		}

		if (is_array($post_pages)) {
			foreach ($post_pages as $page) {
				if (empty($page['title']) || !is_string($page['title'])) {
					continue;
				}

				// If URL isn't provided, create it from the title.
				$url = (string) ($page['url'] ?? Text::normalize_text($page['title'], 'url'));

				$page_data = $this->get_page_data($page);

				self::$pages[$url] = [
					'type' => 'post',
					...$page_data,
				];
			}
		}
	}

	/**
	 * Get JSON file data.
	 * 
	 * @param 	string	$path	JSON file path.
	 * @return	array<mixed>
	 */
	private function get_json_file_data(string $path): array {
		if (empty($path)) {
			CLI::display_error("Failed to get json file contents: path is empty.");
			exit;
		}

		$file_contents = file_get_contents($path);

		if (empty($file_contents)) {
			CLI::display_error("Failed to get json file contents: file contents is empty.");
			exit;
		}

		$json = (array) json_decode($file_contents, true);

		if (empty($json)) {
			CLI::display_error("Failed to get json file contents: json is empty.");
			exit;
		}

		return $json;
	}

	/**
	 * Set page defaults. Set data values for each page that aren't already set.
	 */
	private function set_page_defaults(): void {
		if (empty(self::$pages) || !is_array(self::$pages)) {
			CLI::display_error("Failed to set page defaults: pages is empty or is not an array.");
			exit;
		}

		// Set defaults.
		foreach (self::$pages as $url => $page) {

			// Type.
			if (empty($page['type'])) {
				$page['type'] = 'special';
				self::$pages[$url]['type'] = 'special';
			}

			// URL.
			if (empty($page['url']) && !empty($page['title']) && is_string($page['title'])) {
				self::$pages[$url]['url'] = !empty(self::$pages['*']['url'])
					? (string) str_ireplace('***TITLE***', Text::normalize_text($page['title'], 'url'), self::$pages['*']['url'])
					: Text::normalize_text($page['title'], 'url');
			}

			// Title.
			if (empty($page['title']) && !empty(self::$title)) {
				self::$pages[$url]['title'] = !empty(self::$pages['*']['title'])
					? str_ireplace('***TITLE***', self::$title, self::$pages['*']['title'])
					: self::$title;
			}

			// SEO title.
			if (empty($page['title_seo']) && !empty(self::$title)) {
				if ($page['type'] === 'index') {
					self::$pages[$url]['title_seo'] = self::$title;
				} else {
					if (!empty($page['title']) && is_string($page['title'])) {
						self::$pages[$url]['title_seo'] = self::$title . " - {$page['title']}";
					} else {
						self::$pages[$url]['title_seo'] = self::$title;
					}
				}
			}

			// Description.
			if (empty($page['description']) && !empty(self::$description) && $page['type'] !== 'template') {
				self::$pages[$url]['description'] = self::$description;
			}

			// Image and image credit.
			if (empty($page['image']) && !empty(self::$header['image'])) {
				self::$pages[$url]['image'] = self::$header['image'];

				if (!empty(self::$header['image_credit'])) {
					self::$pages[$url]['image_credit'] = self::$header['image_credit'];
				}
			}

			// Image type.
			if (!empty(self::$pages[$url]['image']) && is_string(self::$pages[$url]['image'])) {
				self::$pages[$url]['image_type'] = stripos(self::$pages[$url]['image'], 'logo') !== false
					? 'logo'
					: 'background';
			}

			// Keywords.
			if (empty($page['keywords'])) {

				$project_title = Text::normalize_text(self::$title);
				$page_title = is_string($page['title']) ? Text::normalize_text($page['title']) : '';

				if (!empty($page['title']) && $project_title !== $page_title) {

					if ($page['type'] === 'template') {
						self::$pages[$url]['keywords'] = "{$project_title} {$page_title}";
					} else if (!empty(self::$pages['*']['keywords'])) {
						self::$pages[$url]['keywords'] = str_ireplace('***TITLE***', $page_title, self::$pages['*']['keywords']);
					} else {
						self::$pages[$url]['keywords'] = $page_title;
					}

				} else if (!empty(self::$keywords)) {
					self::$pages[$url]['keywords'] = self::$keywords;
				}
			}

			// Author.
			if (empty($page['author']) && !empty(self::$author)) {
				self::$pages[$url]['author'] = self::$author;
			}

			// Date.
			if (empty($page['date'])) {
				self::$pages[$url]['date'] = date('Y-m-d');
			} else if (is_string($page['date'])) {
				$timestamp = strtotime($page['date']);

				if (is_int($timestamp)) {
					self::$pages[$url]['date_full'] = date('F j, Y', $timestamp);
				}
			}

			// Links.
			if (empty($page['links']) && !empty(self::$links)) {
				self::$pages[$url]['links'] = self::$links;
			}
		}
	}
}
