<?php

/* php build.php projects                  List all projects configured for building in projects.json
** php build.php [project] [option]        Build a project using project configuration in project.json
**         options:
**                 -v verbose
**                 -b build
**                 -i generate icons
**                 -d deploy

** To create a new project, add a new project to projects.json, then build it
*/

$project_data = null;
$mode = php_sapi_name();
$verbose = false;

$copy_files = array(
	"_redirects",
	"manifest.json",
	"robots.txt",
	"sitemap.xml",
	"sw.js",
);

$cache_files = array(
	"disclaimer.html",
	"index.html",
	"manifest.json",
	"privacy-policy.html",
	"terms-and-conditions.html",
	"logo_nav.svg",
	"style.css"
);

$optional_files = array(
	"main.js",
	"data.json",
	"posts.json",
	"logo_header.svg",
	"background.jpg"
);

// Other modules available: google_auth.js
$base_modules = array(
	"common.js"
);

$fonts_available = array(
	
	// Serif
	"Arvo",					// https://fonts.google.com/specimen/Arvo
	"Bitter",				// https://fonts.google.com/specimen/Bitter
	"Bree Serif",			// https://fonts.google.com/specimen/Bree+Serif
	"Lora",					// https://fonts.google.com/specimen/Lora
	"Merriweather",			// https://fonts.google.com/specimen/Merriweather
	"Playfair Display",		// https://fonts.google.com/specimen/Playfair+Display
	"Roboto Slab",			// https://fonts.google.com/specimen/Roboto+Slab

	// Sans serif
	"Lato",					// https://fonts.google.com/specimen/Lato
	"Merriweather Sans",	// https://fonts.google.com/specimen/Merriweather+Sans
	"Montserrat",			// https://fonts.google.com/specimen/Montserrat
	"Nunito",				// https://fonts.google.com/specimen/Nunito
	"Nunito Sans",			// https://fonts.google.com/specimen/Nunito+Sans
	"Open Sans",			// https://fonts.google.com/specimen/Open+Sans
	"Oxygen",				// https://fonts.google.com/specimen/Oxygen
	"Poppins",				// https://fonts.google.com/specimen/Poppins
	"Quicksand",			// https://fonts.google.com/specimen/Quicksand
	"Raleway",				// https://fonts.google.com/specimen/Raleway
	"Roboto",				// https://fonts.google.com/specimen/Roboto
	"Work Sans",			// https://fonts.google.com/specimen/Work+Sans

	// Misc
	"Amatic SC",			// https://fonts.google.com/specimen/Amatic+SC
	"Bebas Neue",			// https://fonts.google.com/specimen/Bebas+Neue
	"Oswald",				// https://fonts.google.com/specimen/Oswald
	"Pacifico",				// https://fonts.google.com/specimen/Pacifico
	"Rye",					// https://fonts.google.com/specimen/Rye
	"Special Elite",		// https://fonts.google.com/specimen/Special+Elite
);

if ($mode == "cli") {
	if (isset($argv) && count($argv) > 1) {

		$script = $argv[0];

		$build = false;
		$deploy = false;
		$generate_favicons = false;
		$list_projects = false;

		$project = null;

		// Get projects
		$projects_data = get_projects();

		// Loop through passed in options
		foreach ($argv as $index => $option) {

			// Set project, if supplied
			if ($projects_data) {
				foreach ($projects_data as $data) {
					if (isset($data["url"]) && ($option == $data["url"])) {
						$project = $option;
						break;
					}
				}
			}

			if ($option == "projects") {
				$project = null;
				$list_projects = true;
				break;
			}

			if (substr($option, 0, 2) == "-v") {
				$verbose = true;
			}

			if (substr($option, 0, 2) == "-b") {
				$build = true;
			}

			if (substr($option, 0, 2) == "-i") {
				$generate_favicons = true;
			}

			if (substr($option, 0, 2) == "-d") {
				$deploy = true;
			}
		}

		if ($list_projects) {
			
			// List all projects configured for building
			list_projects($projects_data);

		} else if ($project && ($build || $generate_favicons || $deploy)) {

			$project_data = get_project_data($project);
			set_project_version($deploy);

			if ($build) {
				build_project($deploy);
			}
			
			if ($generate_favicons) {
				generate_favicons();
			}

			if ($deploy) {
				deploy_project();
			}
		}

	} else {

		show_usage();
	}
}

function show_usage() {
	echo "Usage:\n" .
			"\n\e[96mphp build.php projects\e[0m\t\t\tList all projects configured for building in projects.json" .
			"\n\e[96mphp build.php [project] [option]\e[0m\tBuild a project using project configuration in project.json" .
			"\n\toptions:" .
				"\n\t\t-v verbose" .
				"\n\t\t-b build" .
				"\n\t\t-i generate icons" .
				"\n\t\t-d deploy" .
			"\n\nTo create a new project, add a new project to projects.json, then build it\n";
}

function get_projects() {

	$projects_data = null;
	$projects_data_json = file_get_contents("projects.json", true);
	
	if ($projects_data_json) {
		$projects_data = json_decode($projects_data_json, true, 512, JSON_OBJECT_AS_ARRAY);
	}

	return $projects_data;
}

function list_projects($projects_data = null) {

	if (!$projects_data) {
		$projects_data = get_projects();
	}

	if ($projects_data) {

		echo "Projects listed in project configuration file (projects.json):";

		foreach ($projects_data as $data) {
			echo "\n\e[96m- " . $data["url"] . "\e[0m: " . $data["title"];
		}

		echo "\n";
	}
}

function show_error($error) {
	echo "\n\e[31m ERROR: " . $error . "\e[0m\n";
}

// Builds a project according to build data
function build_project() {

	global $project_data;
	global $mode;
	global $verbose;

	$result = false;

	if (isset($project_data["url"])) {

		echo ($mode == "cli") ? "Building project " . $project_data["url"] . " v" . $project_data["version"] : "";

		// Create new website directory if it doesn't already exist
		if (!file_exists(dirname(__DIR__) . "/" . $project_data["url"] . "/")) {
			create_project_directory();
		}

		echo ($verbose && ($mode == "cli")) ? "\nProcessing files..." : "";

		copy_files();

		compile_files();

		template_replace_files();
	
		echo ($verbose && ($mode == "cli")) ? "\nProcessing files DONE" . "\nBuilding project " . $project_data["url"] . " DONE\n" : (($mode == "cli") ? " - DONE\n" : "");
		
		$result = true;
	} else {
		show_error("URL missing");
	}

	return $result;
}

// Deploys a project according to build data
function deploy_project() {

	global $project_data;
	global $mode;
	global $verbose;

	$result = false;
	$output = array();

	if (isset($project_data["url"]) && isset($project_data["netlify_id"])) {
		echo ($mode == "cli") ? "Deploying project " . $project_data["netlify_id"]  . " v" . $project_data["version"] . " to " . $project_data["url"] : "";
		exec("netlify deploy --prod --dir=" . dirname(__DIR__) . "/" . $project_data["url"] . " --site=" . $project_data["netlify_id"], $output);
		$needle = "Deploy URL";
		$result = array_keys(array_filter($output, function($haystack) use ($needle) {
			return strpos($haystack, $needle) !== false;
		})) ? true : false;
		echo ($verbose && ($mode == "cli")) ? "\nDeploying project " . $project_data["url"] . " DONE\n" : "";
	} else {
		show_error("URL or Netlify ID missing");
	}

	return $result;
}

function set_project_version($deploy = false) {

	global $project_data;

	$service_worker_file = dirname(__DIR__) . "/" . $project_data["url"] . "/sw.js";

	// Extract version from project sw.js file before overwriting it
	$project_data["version"] = null;

	if (file_exists($service_worker_file)) {
		$file_contents = file_get_contents($service_worker_file, true);

		if ($file_contents) {
			preg_match("/cache([0-9]+)/", $file_contents, $version);
			$project_data["version"] = (count($version) == 2) ? $version[1] : 1;

			if (isset($project_data["version"]) && $project_data["version"]) {

				// If deploying, increment project version
				if ($deploy) {

					// Replace project version in service worker file
					$old_version = $project_data["version"];
					$project_data["version"]++;

					file_put_contents($service_worker_file, str_replace($old_version, $project_data["version"], $file_contents));
				}

			} else {
				show_error("version could not be extracted from " . $service_worker_file . " file contents");
			}

		} else {
			show_error("contents could not be extracted from " . $service_worker_file);
		}
		
	} else {
		show_error("could not find " . $service_worker_file . " file");
	}
}

function create_project_directory() {
	
	global $project_data;
	global $verbose;

	echo "\nCreating project directory " . $project_data["url"];

	mkdir(dirname(__DIR__) . "/" . $project_data["url"]);

	echo $verbose ? "\n\tCreating main.php file" : "";
	file_put_contents(dirname(__DIR__) . "/" . $project_data["url"] . "/main.php", "");

	echo $verbose ? "\n\tCreating style.scss file" : "";
	file_put_contents(dirname(__DIR__) . "/" . $project_data["url"] . "/style.scss",
		'@use "../files/common" as * with (' . "\n" .
			"\t" . '$font-heading: ' . $project_data["fonts"]["heading"] . ",\n" .
			"\t" . '$font-body: ' . $project_data["fonts"]["body"] . ",\n" .
			"\t" . '$color-main: ' . $project_data["colors"]["main"]["normal"] . ",\n" .
			"\t" . '$color-main-dark: ' . $project_data["colors"]["main"]["dark"] . ",\n" .
			"\t" . '$color-accent: ' . $project_data["colors"]["accent"]["normal"] . ",\n" .
			"\t" . '$color-accent-dark: ' . $project_data["colors"]["accent"]["dark"] . ",\n" .
		');'
	);

	echo $verbose ? "\n\tCreating main.js file" : "";
	file_put_contents(dirname(__DIR__) . "/" . $project_data["url"] . "/main.js", 'window.addEventListener("load", () => {});');

	echo $verbose ? "\n\tCreating sw.js file with version=1" : "";
	file_put_contents(dirname(__DIR__) . "/" . $project_data["url"] . "/sw.js", 'const cacheName = "cache1";');
	
	echo $verbose ? "\n\tCopying default favicon image" : "";
	copy(dirname(__DIR__) . "/favicon.svg", $project_data["url"] . "/favicon.svg");

	echo $verbose ? "\nCreating project directory DONE\n" : " - DONE";
}

function copy_files() {

	global $project_data;
	global $verbose;

	if (isset($project_data["files"]["copy"])) {

		$files_to_copy = $project_data["files"]["copy"];
		echo $verbose ? "\nCopying " . count($files_to_copy) . " files" : "";

		foreach ($files_to_copy as $file) {

			$target_file = $project_data["url"] . "/" . $file;

			if ($file == "sw.js") {
				echo $verbose ? "\n\tCopying service-worker.js to " . $target_file : "";
				copy("files/service-worker.js", dirname(__DIR__) . "/" . $target_file);
			} else {
				echo $verbose ? "\n\tCopying " . $file . " to " . $target_file : "";
				copy("files/" . $file, dirname(__DIR__) . "/" . $target_file);
			}
		}

		echo $verbose ? "\nCopying files DONE" : "";
	}
}

function compile_files() {

	global $project_data;
	global $verbose;

	$content_to_minify = null;

	if (file_exists(dirname(__DIR__) . "/" . $project_data["url"] . "/style.scss")) {
		echo $verbose ? "\nCompiling " . $project_data["url"] . "/style.scss to " . $project_data["url"] . "/style.css" : "";
		exec("sass --style=compressed --embed-source-map " . dirname(__DIR__) . "/" . $project_data["url"] . "/style.scss:" . dirname(__DIR__) . "/" . $project_data["url"] . "/style.css");
		echo $verbose ? "\nCompiling " . $project_data["url"] . "/style.css DONE" : "";
	}

	if (isset($project_data["files"]["compile"])) {

		echo $verbose ? "\nCompiling " . count($project_data["files"]["compile"]) . " files" : "";

		foreach ($project_data["files"]["compile"] as $source_php => $target_html) {
			$target_file = $project_data["url"] . "/" . $target_html;

			// Check for params
			if (stripos($source_php, " ") !== false) {
				$url_parts = explode(" ", $source_php);

				if (is_array($url_parts)) {
					$source_file = $url_parts[0] . " " . $project_data["url"];

					foreach ($url_parts as $index => $param) {
						if (stripos($param, ".") === false) { // Ignore domain parameter
							$source_file .= " " . $param;
						}
					}
				}

			} else {
				$source_file = $source_php . " " . $project_data["url"];
			}

			echo $verbose ? "\n\tCompiling " . $source_file . " to " . $target_file : "";

			// Open the file using PHP
			exec("php " . $source_file . " > " . dirname(__DIR__) . "/" . $target_file);

			if (file_exists(dirname(__DIR__) . "/" . $target_file)) {
				$content_to_minify = file_get_contents(dirname(__DIR__) . "/" . $target_file);
			}

			if ($content_to_minify) {

				// Minify content
				$minified_content = preg_replace("/\s+/S", " ", $content_to_minify);

				file_put_contents(dirname(__DIR__) . "/" . $target_file, $minified_content);

			} else {
				show_error("error generating file " . $target_file);
			}
		}

		echo $verbose ? "\nCompiling files DONE" : "";
	}
}

// Find and replace patterns in all files in project directory
function template_replace_files() {

	global $project_data;
	global $verbose;

	$files = "";

	echo $verbose ? "\nReplacing data in project files..." : "";

	// Files
	if (is_array($project_data["files"]["cache"])) {

		foreach ($project_data["files"]["cache"] as $index => $file) {
			$files .= '"' . $file . '",';

			// Only add new line if followed by another file
			if (isset($project_data["files"]["cache"][$index+1])) {
				$files .= "\n\t";
			}
		}
	}

	// URLs
	$urls = get_sitemap();

	// URL redirects
	$redirects = get_redirects();

	$patterns = array(
		"***URLS***" => $urls,
		"***REDIRECT_URLS***" => $redirects,
		"// ***FILES***" => $files,
		"***URL***" => $project_data["url"],
		"***TITLE***" => $project_data["title"],
		"***DESCRIPTION***" => $project_data["description"],
		"***VERSION***" => $project_data["version"],
		"***DATE***" => date("Y-m-d"),
	);

	// Replace patterns in compiled files, files in $project_data["files"]["copy"], and special files
	$html_files = array();

	if (isset($project_data["files"]["compile"]) && is_array($project_data["files"]["compile"])) {
		foreach ($project_data["files"]["compile"] as $index => $file) {
			if (stripos($file, "html") !== false) {
				$html_files[] = $file;
			}
		}
	}

	$copied_files = $project_data["files"]["copy"];
	$special_files = array(
		"sw.js", // ***VERSION***, ***FILES***
		"sitemap.xml", // ***URLS***
		"_redirects", // ***REDIRECT_URLS***
		"manifest.json", // ***TITLE***, ***DESCRIPTION***
	);

	$replace_files = array_merge(
		$html_files,
		$copied_files,
		$special_files
	);

	// Remove duplicate files
	$replace_files = array_unique($replace_files);

	if (is_array($replace_files)) {

		foreach ($replace_files as $index => $file) {

			$replace_file = $project_data["url"] . "/" . $file;

			if (file_exists(dirname(__DIR__) . "/" . $replace_file)) {

				$file_contents = file_get_contents(dirname(__DIR__) . "/" . $replace_file);

				if ($file == "manifest.json") {
					echo $verbose ? "\n\tGenerating " . $replace_file : "";
					$file_contents = get_manifest($file_contents);
					echo $verbose ? "\n\tGenerating " . $replace_file . " DONE" : "";
				}
				
				echo $verbose ? "\n\tReplacing content in " . $replace_file : "";
		
				// Replace common patterns in files
				foreach ($patterns as $find => $replace) {
					if (stripos($file_contents, $find) !== false) {
						$file_contents = str_replace($find, $replace, $file_contents);
					}
				}

				// Replace file
				file_put_contents(dirname(__DIR__) . "/" . $replace_file, $file_contents);

			} else {
				show_error("file " . $replace_file . " does not exist");
			}
		}

	} else {
		show_error("project_files is not an array");
	}

	echo $verbose ? "\nReplacing data in project files DONE" : "";
}

function process_font_files() {

	global $project_data;
	global $verbose;

	if ($project_data) {

		$fontawesome_files = array(
			"fonts/fontawesome/css/brands.min.css",
			"fonts/fontawesome/css/fontawesome.min.css",
			"fonts/fontawesome/css/solid.min.css",
			"fonts/fontawesome/webfonts/fa-brands-400.eot",
			"fonts/fontawesome/webfonts/fa-brands-400.svg",
			"fonts/fontawesome/webfonts/fa-brands-400.ttf",
			"fonts/fontawesome/webfonts/fa-brands-400.woff",
			"fonts/fontawesome/webfonts/fa-brands-400.woff2",
			"fonts/fontawesome/webfonts/fa-solid-900.eot",
			"fonts/fontawesome/webfonts/fa-solid-900.svg",
			"fonts/fontawesome/webfonts/fa-solid-900.ttf",
			"fonts/fontawesome/webfonts/fa-solid-900.woff",
			"fonts/fontawesome/webfonts/fa-solid-900.woff2"
		);
	
		if (isset($project_data["fonts"]) && $project_data["fonts"]) {
			if (!file_exists(dirname(__DIR__) . "/" . $project_data["url"] . "/fonts")) {
				echo $verbose ? "\nCreating fonts directory..." : "";
				mkdir(dirname(__DIR__) . "/" . $project_data["url"] . "/fonts");
				echo $verbose ? "\nCreating fonts directory DONE" : "";
			}
	
			$heading_font = $project_data["fonts"]["heading"];
			$heading_font_decoded = strtolower(str_ireplace(" ", "-", $heading_font));
	
			if (!file_exists(dirname(__DIR__) . "/" . $project_data["url"] . "/fonts/" . $heading_font_decoded)) {
				echo $verbose ? "\nCreating " . $heading_font_decoded . " directory..." : "";
				mkdir(dirname(__DIR__) . "/" . $project_data["url"] . "/fonts/" . $heading_font_decoded);
				echo $verbose ? "\nCreating " . $heading_font_decoded . " directory DONE" : "";
			}
	
			$body_font = $project_data["fonts"]["body"];
			$body_font_decoded = strtolower(str_ireplace(" ", "-", $body_font));
	
			if (!file_exists(dirname(__DIR__) . "/" . $project_data["url"] . "/fonts/" . $body_font_decoded)) {
				echo $verbose ? "\nCreating " . $body_font_decoded . " directory..." : "";
				mkdir(dirname(__DIR__) . "/" . $project_data["url"] . "/fonts/" . $body_font_decoded);
				echo $verbose ? "\nCreating " . $body_font_decoded . " directory DONE" : "";
			}
	
			$heading_font_files = scandir(dirname(__FILE__) . "/files/fonts/" . $heading_font_decoded);
			$heading_font_files = array_diff($heading_font_files, array(".", ".."));
			foreach ($heading_font_files as $index => $heading_font_file) {
				$heading_font_files[$index] = "fonts/" . $heading_font_decoded . "/" . $heading_font_file;
			}
	
			$body_font_files = scandir(dirname(__FILE__) . "/files/fonts/" . $body_font_decoded);
			$body_font_files = array_diff($body_font_files, array(".", ".."));
			foreach ($body_font_files as $index => $body_font_file) {
				$body_font_files[$index] = "fonts/" . $body_font_decoded . "/" . $body_font_file;
			}
	
			$font_files = array_merge($heading_font_files, $body_font_files);
		}
	
		if (isset($project_data["fontawesome"]) && $project_data["fontawesome"]) {
			if (!file_exists(dirname(__DIR__) . "/" . $project_data["url"] . "/fonts/fontawesome")) {
				echo $verbose ? "\nCreating fontawesome directory..." : "";
				mkdir(dirname(__DIR__) . "/" . $project_data["url"] . "/fonts/fontawesome");
				echo $verbose ? "\nCreating fontawesome directory DONE" : "";
			}
			if (!file_exists(dirname(__DIR__) . "/" . $project_data["url"] . "/fonts/fontawesome/css")) {
				echo $verbose ? "\nCreating fontawesome/css directory..." : "";
				mkdir(dirname(__DIR__) . "/" . $project_data["url"] . "/fonts/fontawesome/css");
				echo $verbose ? "\nCreating fontawesome/css directory DONE" : "";
			}
			if (!file_exists(dirname(__DIR__) . "/" . $project_data["url"] . "/fonts/fontawesome/webfonts")) {
				echo $verbose ? "\nCreating fontawesome/webfonts directory..." : "";
				mkdir(dirname(__DIR__) . "/" . $project_data["url"] . "/fonts/fontawesome/webfonts");
				echo $verbose ? "\nCreating fontawesome/webfonts directory DONE" : "";
			}
		}

		$project_data["files"]["copy"] = isset($project_data["files"]["copy"]) ? array_merge($project_data["files"]["copy"], $font_files, $fontawesome_files) : array_merge($font_files, $fontawesome_files);
		$project_data["files"]["cache"] = isset($project_data["files"]["cache"]) ? array_merge($project_data["files"]["cache"], $font_files, $fontawesome_files) : array_merge($font_files, $fontawesome_files);

	} else {
		show_error("project_data not set");
	}
}

function get_sitemap() {

	global $project_data;

	$urls = "";

	if ($project_data) {
	
		$sitemap_template = "\t<url>\n" .
								"\t\t<loc>https://{url}</loc>\n" . 
								"\t\t<lastmod>{date}</lastmod>\n" . 
							"\t</url>\n";
	
		// If redirects are set, add to urls
		if (isset($project_data["redirects"]) && is_array($project_data["redirects"])) {
			foreach ($project_data["redirects"] as $index => $redirect) {
				$url = trim($redirect["from"], "/");
				if (($url != "") && ($url != "index") && (stripos($url, ".") !== false) && (stripos($url, "*") !== false)) {
					$project_data["sitemap"]["urls"][] .= $url;
				}
			}
		}
	
		// Generate sitemap
		if (isset($project_data["sitemap"]["urls"]) && is_array($project_data["sitemap"]["urls"])) {
	
			foreach ($project_data["sitemap"]["urls"] as $index => $url) {
				$url = ($url == "index") ? "" : $url;
				$sitemap_url = str_ireplace("{url}", $project_data["url"] . "/" . $url, $sitemap_template);
				$sitemap_url = str_ireplace("{date}", date("Y-m-d"), $sitemap_url);
				$urls .= $sitemap_url;
			}
		}

	} else {
		show_error("project_data not set");
	}

	return $urls;
}

function get_redirects() {

	global $project_data;

	$redirects = "";

	if ($project_data) {

		if (isset($project_data["files"]["compile"]) && is_array($project_data["files"]["compile"])) {

			foreach ($project_data["files"]["compile"] as $index => $url) {
				if ($url != "index.html") {
					$redirects .= str_ireplace("{title}", str_ireplace(".html", "", $url), "/{title}/* /{title}.html 200") . "\n";
				}
			}
		}
	
		// If redirects are set, add to urls
		if (isset($project_data["redirects"]) && is_array($project_data["redirects"])) {
			foreach ($project_data["redirects"] as $index => $redirect) {
				$redirects .= $redirect["from"] . " " . $redirect["to"] . "\n";
			}
		}
	
		$redirects = trim($redirects, "\n");
		$redirects .= "\n/* /index.html 200";

	} else {
		show_error("project_data not set");
	}

	return $redirects;
}

function get_manifest($file_contents) {

	global $project_data;

	$data = "";

	if ($project_data) {

		$data = json_decode($file_contents);
	
		if (isset($project_data["categories"])) {
			$data->categories = $project_data["categories"];
		}

		if (isset($project_data["screenshots"])) {
			$data->screenshots = array();
			foreach ($project_data["screenshots"] as $file) {
				$data->screenshots[] = array(
					"src" => $file,
					"sizes" => "1500x800",
					"type" => "image/png"
				);
			}
		}

		if (isset($project_data["shortcuts"])) {
			$data->shortcuts = $project_data["shortcuts"];
		}

		$related_applications[] = array(
			"platform" => "webapp",
			"url" => "https://" . $project_data["url"] . "/manifest.json"
		);
	
		if (isset($project_data["android_app_id"]) || isset($project_data["apple_app_id"])) {
	
			$android_app_id = null;
			if (isset($project_data["android_app_id"]["paid"])) {
				$android_app_id = $project_data["android_app_id"]["paid"];
			} else if (isset($project_data["android_app_id"]["free"])) {
				$android_app_id = $project_data["android_app_id"]["free"];
			}
	
			$apple_app_id = null;
			if (isset($project_data["apple_app_id"]["paid"])) {
				$apple_app_id = $project_data["apple_app_id"]["paid"];
			} else if (isset($project_data["apple_app_id"]["free"])) {
				$apple_app_id = $project_data["apple_app_id"]["free"];
			}
	
			if ($android_app_id && ($android_app_id != null) && ($android_app_id != "disabled")) {
				$related_applications[] = array(
					"platform" => "play",
					"url" => "https://play.google.com/store/apps/details?id=" . $android_app_id,
					"id" => $android_app_id
				);
				$data->prefer_related_applications = true;
			}
	
			if ($apple_app_id && ($apple_app_id != null) && ($apple_app_id != "disabled")) {
				$related_applications[] = array(
					"platform" => "itunes",
					"url" => "https://itunes.apple.com/app/example-app1/id123456789" . $apple_app_id,
				);
				$data->prefer_related_applications = true;
			}
	
			if ($related_applications) {
				$data->related_applications = $related_applications;
			}
		}

	} else {
		show_error("project_data not set");
	}

	return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
}

function generate_favicons() {

	global $project_data;
	global $verbose;

	if (isset($project_data["url"])) {

		echo "Generating favicons...";

		if (file_exists(dirname(__DIR__) . "/" . $project_data["url"] . "/favicon.svg")) {

			$favicon_data_config = array(
				"masterPicture" => dirname(__DIR__) . "/" . $project_data["url"] . "/favicon.svg",
				"iconsPath" => "/",
				"design" =>
				array(
					"ios" =>
					array(
						"pictureAspect" => "backgroundAndMargin",
						"backgroundColor" => "#000000",
						"margin" => "18%",
						"assets" =>
						array(
							"ios6AndPriorIcons" => true,
							"ios7AndLaterIcons" => true,
							"precomposedIcons" => false,
							"declareOnlyDefaultIcon" => true,
						),
					),
					"desktopBrowser" =>
					array(
						"design" => "raw",
					),
					"windows" =>
					array(
						"pictureAspect" => "noChange",
						"backgroundColor" => "#000000",
						"onConflict" => "override",
						"assets" =>
						array(
							"windows80Ie10Tile" => false,
							"windows10Ie11EdgeTiles" =>
							array(
								"small" => false,
								"medium" => true,
								"big" => false,
								"rectangle" => false,
							),
						),
					),
					"androidChrome" =>
					array(
						"pictureAspect" => "noChange",
						"themeColor" => "#000000",
						"manifest" =>
						array(
							"display" => "standalone",
							"orientation" => "notSet",
							"onConflict" => "override",
							"declared" => true,
						),
						"assets" =>
						array(
							"legacyIcon" => false,
							"lowResolutionIcons" => true,
						),
					),
					"safariPinnedTab" =>
					array(
						"pictureAspect" => "blackAndWhite",
						"threshold" => 89.21875,
						"themeColor" => "#000000",
					),
				),
				"settings" =>
				array(
					"compression" => 2,
					"scalingAlgorithm" => "Mitchell",
					"errorOnImageTooSmall" => false,
					"readmeFile" => false,
					"htmlCodeFile" => false,
					"usePathAsIs" => false,
				),
			);
			file_put_contents(dirname(__DIR__) . "/" . $project_data["url"] . "/favicon_config.json", json_encode($favicon_data_config, JSON_PRETTY_PRINT));
			exec("real-favicon generate " . dirname(__DIR__) . "/" . $project_data["url"] . "/favicon_config.json favicon_data.json " . dirname(__DIR__) . "/" . $project_data["url"]);
			exec("rm -rf favicon_data.json");
			exec("rm -rf " . dirname(__DIR__) . "/" . $project_data["url"] . "/favicon_config.json");
			exec("rm -rf " . dirname(__DIR__) . "/" . $project_data["url"] . "/site.webmanifest");
			echo $verbose ? "\nGenerating favicons DONE" : " - DONE\n";
		} else {
			show_error("favicon.svg missing");
		}

	} else {
		show_error("project not set");
	}
}

function normalize_data($data, $type = "text") {
	$data_normalized = "";

	if ($data && is_string($data)) {

		// $func1 = strtr(utf8_decode($data), utf8_decode("àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ"), "aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY");
		// $func2 = iconv("UTF-8", "ASCII//TRANSLIT//IGNORE", $data);
		
		$data_normalized = strtolower(iconv("UTF-8", "ASCII//TRANSLIT//IGNORE", strtr(utf8_decode($data), utf8_decode("àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ"), "aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY")));

		// If URL, replace spaces with dashes
		if ($type == "url") {
			$data_normalized = str_replace(" ", "-", $data_normalized);
		} else {
			$data_normalized = str_replace("-", " ", $data_normalized);
		}
	}

	return $data_normalized;
}

// If project URL is provided,
//	Get all project data from projects.json[project]
// 	If $page is provided,
//	 Get page data from project/data.json or project/posts.json
//	Else
//	 Get all pages data from project/data.json or project/posts.json
// else,
//	Get all projects data from projects.json
function get_project_data($project = null, $page = null) {

	global $project_data;

	global $base_modules;
	global $optional_files;
	global $copy_files;
	global $cache_files;

	$project_data = array();
	$project_found = false;

	// Get all projects data from projects.json
	if (file_exists(dirname(__FILE__) . "/projects.json")) {
		$project_data = json_decode(file_get_contents(dirname(__FILE__) . "/projects.json"), true);

		if (isset($project_data) && is_array($project_data)) {

			// If $project is provided, remove other projects from $project_data
			foreach ($project_data as $index => $data) {
				if ((isset($data["url"])) && ($data["url"] == $project)) {
					$project_data = $data;
					$project_found = true;
					break;
				}
			}

			if ($project && $project_found) {

				// Set project data
				if (!isset($project_data["pages"])) {
					$project_data["pages"] = array();
				}

				// Set page(s) data
				$project_data["pages"] = array_merge($project_data["pages"], array(
					"index" => array(
						"type" => "index",
						"url" => "index",
						"title" => $project_data["title"],
						"description" => $project_data["description"]
					),
					"disclaimer" => array(
						"type" => "template",
						"url" => "disclaimer",
						"title" => "Disclaimer",
						"description" => $project_data["title"] . " disclaimer",
					),
					"privacy-policy" => array(
						"type" => "template",
						"url" => "privacy-policy",
						"title" => "Privacy Policy",
						"description" => $project_data["title"] . " privacy policy",
					),
					"terms-and-conditions" => array(
						"type" => "template",
						"url" => "terms-and-conditions",
						"title" => "Terms and Conditions",
						"description" => $project_data["title"] . " terms and conditions",
					)
				));

				// Populate search result pages or posts
				if ((file_exists(dirname(__DIR__) . "/" . $project_data["url"] . "/data.json")) || file_exists(dirname(__DIR__) . "/" . $project_data["url"] . "/posts.json")) {
					
					if (file_exists(dirname(__DIR__) . "/" . $project_data["url"] . "/data.json")) {
						$page_file = "data.json";
						$page_type = "search";
					} else {
						$page_file = "posts.json";
						$page_type = "post";
					}

					$pages_data = json_decode(file_get_contents(dirname(__DIR__) . "/" . $project_data["url"] . "/" . $page_file), true);
					
					if (isset($pages_data) && is_array($pages_data)) {

						if ($page_type == "search") {

							foreach ($pages_data as $url => $page_data) {
								
								// If page title is an array, page content should be duplicated and added to the end of the array
								if (is_array($page_data["title"])) {
									$pages_data[$url]["title"] = $page_data["title"][0];

									foreach ($page_data["title"] as $url => $title) {
										if ($url) {

											$new_page_data = $page_data;
											// $new_page_data["type"] = "post";
											$new_page_data["title"] = $title;
											$new_page_data["url"] = normalize_data($title, "url");

											$pages_data[$new_page_data["url"]] = $new_page_data;
										}
									}
								}
							}
						}

						foreach ($pages_data as $url => $page_data) {

							$page_data["type"] = $page_type;

							// URL
							if (!isset($page_data["url"]) && (isset($page_data["title"]))) {
								$page_data["url"] = normalize_data($page_data["title"], "url");
							}

							// Add to project_data array
							if (isset($project_data["pages"][$page_data["url"]])) {
								$project_data["pages"][$page_data["url"]] = array_merge($project_data["pages"][$page_data["url"]], $page_data);
							} else {
								$project_data["pages"][$page_data["url"]] = $page_data;
							}
						}
					}
				}

				// Set default page values
				foreach ($project_data["pages"] as $url => $page_data) {

					// Type
					if (!isset($page_data["type"])) {
						$page_data["type"] = "special";
						$project_data["pages"][$url]["type"] = "special";
					}

					// URL
					if (!isset($page_data["url"]) && (isset($page_data["title"]))) {
						if (isset($project_data["pages"]["*"]["url"])) {
							$project_data["pages"][$url]["url"] = str_ireplace("***TITLE***", normalize_data($page_data["title"], "url"), $project_data["pages"]["*"]["url"]);
						} else {
							$project_data["pages"][$url]["url"] = normalize_data($page_data["title"], "url");
						}
					}

					// Full URL
					if (!isset($page_data["url_full"])) {
						if (isset($project_data["url"]) && isset($page_data["url"])) {
							if ($page_data["url"] == "index") {
								$project_data["pages"][$url]["url_full"] =  $project_data["url"];
							} else {
								$project_data["pages"][$url]["url_full"] =  $project_data["url"] . "/" . $page_data["url"];
							}
						} else if (isset($project_data["url"])) {
							$project_data["pages"][$url]["url_full"] =  $project_data["url"];
						}
					}

					// Title
					if (!isset($page_data["title"]) && isset($project_data["title"])) {

						if (isset($project_data["pages"]["*"]["title"])) {
							$project_data["pages"][$url]["title"] = str_ireplace("***TITLE***", $project_data["title"], $project_data["pages"]["*"]["title"]);
						} else {
							$project_data["pages"][$url]["title"] = $project_data["title"];
						}
					}

					// SEO title
					if ($page_data["type"] == "index") {
						$project_data["pages"][$url]["title_seo"] = $project_data["title"];
					} else {
						if (!isset($page_data["title_seo"])) {
							if (isset($project_data["title"]) && isset($page_data["title"])) {
								$project_data["pages"][$url]["title_seo"] =  $project_data["title"] . " - " . $page_data["title"];
							} else if (isset($project_data["title"])) {
								$project_data["pages"][$url]["title_seo"] =  $project_data["title"];
							}
						}
					}

					// Description
					if (($page_data["type"] != "template") && !isset($page_data["description"]) && isset($project_data["description"])) {
						$project_data["pages"][$url]["description"] = $project_data["description"];
					}

					// Image and image credit
					if (($page_data["type"] != "post")) {
						if (!isset($page_data["image"]) && isset($project_data["header"]) && isset($project_data["header"]["image"])) {
							$project_data["pages"][$url]["image"] = $project_data["header"]["image"];

							if (isset($project_data["header"]["image_credit"])) {
								$project_data["pages"][$url]["image_credit"] = $project_data["header"]["image_credit"];
							}
						}
					} else {
						if (!isset($page_data["image"]) && isset($project_data["image"])) {
							$project_data["pages"][$url]["image"] = $project_data["image"];

							if (isset($project_data["image_credit"])) {
								$project_data["pages"][$url]["image_credit"] = $project_data["image_credit"];
							}
						}
					}

					// Image type
					if ((stripos($project_data["pages"][$url]["image"], "logo") !== false)) {
						$project_data["pages"][$url]["image_type"] = "logo";
					} else {
						$project_data["pages"][$url]["image_type"] = "background";
					}

					// Keywords
					if (!isset($page_data["keywords"])) {

						if (isset($page_data["title"]) && (normalize_data($project_data["title"]) != normalize_data($page_data["title"]))) {

							$project_title = normalize_data($project_data["title"]);
							$page_title = normalize_data($page_data["title"]);

							if ($page_data["type"] == "template") {
								$project_data["pages"][$url]["keywords"] = $project_title . " " . $page_title;
							} else if (isset($project_data["pages"]["*"]["keywords"])) {
								$project_data["pages"][$url]["keywords"] = str_ireplace("***TITLE***", $page_title, $project_data["pages"]["*"]["keywords"]);
							} else {
								$project_data["pages"][$url]["keywords"] = $page_title;
							}

						} else if (isset($project_data["keywords"])) {
							$project_data["pages"][$url]["keywords"] = $project_data["keywords"];
						}
					}

					// Author
					if (!isset($page_data["author"]) && isset($project_data["author"])) {
						$project_data["pages"][$url]["author"] = $project_data["author"];
					}

					// Date
					if (!isset($page_data["date"])) {
						$project_data["pages"][$url]["date"] = date("Y-m-d");
					}

					if (isset($page_data["date"])) {
						$project_data["pages"][$url]["date_full"] = date("F j, Y", strtotime($page_data["date"]));
					}

					// Links
					if (!isset($page_data["links"]) && isset($project_data["links"])) {
						$project_data["pages"][$url]["links"] = $project_data["links"];
					}
				}

				// Set sitemap URLs
				$project_data["sitemap"]["urls"] = array(
					"index",
					"disclaimer",
					"privacy-policy",
					"terms-and-conditions",
				);

				// Set files lists	
				$project_data["files"]["copy"] = isset($project_data["files"]["copy"]) ? array_merge($project_data["files"]["copy"], $base_modules, $copy_files) : array_merge($base_modules, $copy_files);
				$project_data["files"]["cache"] = isset($project_data["files"]["cache"]) ? array_merge($project_data["files"]["cache"], $base_modules, $cache_files) : array_merge($base_modules, $cache_files);

				if (isset($optional_files) && is_array($optional_files)) {
					foreach ($optional_files as $file) {
						if (file_exists(dirname(__DIR__) . "/" . $project_data["url"] . "/" . $file)) {
							$project_data["files"]["cache"][] = $file;
						}
					}
				}

				// Process images
				if (isset($project_data["images"]) && is_array($project_data["images"])) {
					foreach ($project_data["images"] as $image) {
						$project_data["files"]["cache"][] = $image;
					}
				}

				// Process modules
				if (isset($project_data["modules"]) && is_array($project_data["modules"])) {
					foreach ($project_data["modules"] as $module) {
						$project_data["files"]["copy"][] = $module . ".js";
						$project_data["files"]["cache"][] = $module . ".js";
					}
				}

				$compile_files = array();

				foreach ($project_data["sitemap"]["urls"] as $page) {
					$compile_files[dirname(__FILE__) . "/main.php " . $project_data["url"] . " " . $page] = $page . ".html";
				}

				$project_data["files"]["compile"] = $compile_files;

				// Add project urls from data file to:
				//	$project_data["files"]["compile"], for static HTML file generation
				//	$project_data["files"]["cache"], for caching by service worker (also add .jpg if page type == post)
				// 	$project_data["sitemap"]["urls"], for sitemap generation
				if (isset($project_data["pages"]) && is_array($project_data["pages"])) {
					foreach ($project_data["pages"] as $page_data) {
						if (isset($page_data["url"])) {
							if (($page_data["url"] != "") && ($page_data["url"] != "index")) {
								$project_data["files"]["compile"][dirname(__FILE__) . "/main.php " . $project_data["url"] . " " . $page_data["url"]] = $page_data["url"] . ".html";

								if (!in_array($page_data["url"], $project_data["sitemap"]["urls"])) {
									$project_data["sitemap"]["urls"][] = $page_data["url"];
								}

								if (!in_array($page_data["url"] . ".html", $project_data["files"]["cache"])) {
									$project_data["files"]["cache"][] = $page_data["url"] . ".html";
								}

								if (!in_array($page_data["url"] . ".jpg", $project_data["files"]["cache"]) && ($page_data["type"] == "post")) {
									$project_data["files"]["cache"][] = $page_data["url"] . ".jpg";
								}
							}
						} else {
							// echo "\nWARNING: url not set for page";
							// print_r($page_data);
						}
					}
				}

				// Add font files
				process_font_files();
		
			} else {
				show_error("project not set or not found in projects.json");
			}

		} else {
			show_error("could not get projects from projects.json file");
		}

	} else {
		show_error("could not find projects.json file");
	}

	return $project_data;
}

/*
** API Functions
*/

function api_generate_project($project, $deploy = false) {

	$result = false;
	$result_build = false;
	$result_deploy = false;

	if ($project) {
		$project_data = get_project_data($project);

		set_project_version($deploy);
		$result_build = build_project();
	
		if ($result_build && $deploy) {
			$result_deploy = deploy_project();
		}

		$result = (($result_build && (!$deploy)) || ($result_build && $result_deploy)) ? true : false;
	}

	return $result;
}

function api_update_project($project, $field, $value) {

	$result = false;

	if ($project && $field && $value) {
		$projects_data = get_projects();

		if ($projects_data) {
			foreach ($projects_data as $index => $project_data) {
				if ($project_data["url"] == $project) {
					$project_data[$field] = $value;
					$projects_data[$index] = $project_data;
				}		
			}
		
			$projects_data_json = str_ireplace("    ", "\t", json_encode($projects_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

			$result = file_put_contents(dirname(__FILE__) . "/projects.json", $projects_data_json) ? true : false;
		}
	}

	return $result;
}