<?php

namespace PWA_Generator;

/**
 * Build class.
 */
class Build {
    /**
     * Files for service worker to cache.
     *
     * @var array<int, string>
     */
    private array $cache_files = [
        'manifest.json',
    ];

    /**
     * Files to compile into build directory.
     *
     * @var array<int, array<string, string>>
     */
    private array $compile_files = [];

    /**
     * Files to copy into build directory.
     *
     * @var array<int, string>
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
     * JavaScript bundle filename (including hash).
     *
     * @var string
     */
    private string $js_bundle_filename = '';

    /**
     * Constructor.
     *
     * Sets up project data and performs actions based on build options provided.
     *
     * @param   string          $project    Project name.
     * @param   array<boolean>  $options    Build options.
     */
    public function __construct(
        string $project,
        array $options = [
            'generate_favicons' => false,
            'build' => false,
            'deploy' => false,
        ]
    ) {
        if (empty($project)) {
            CLI::display_error("Failed to construct build: project not provided.");
            exit;
        }

        // Set up project data.
        $project = new Project($project);

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
     * Builds a project according to build data.
     *
     * @return  void
     */
    private function build_project(): void {

        if (empty(Project::$directory_path)) {
            CLI::display_error("Failed to build project: project directory path is empty.");
            exit;
        }

        if (empty(Project::$url)) {
            CLI::display_error("Failed to build project: URL is empty.");
            exit;
        }

        // Create and populate new website directory if it doesn't already exist.
        if (!is_dir(Project::$directory_path)) {
            $this->create_build_directory();
            $this->populate_build_directory();
        }

        $this->set_build_version();

        if (empty(Project::$version)) {
            CLI::display_error("Failed to build project: project version is empty.");
            exit;
        }

        echo "\nBuilding project " . Project::$url . ' v' . Project::$version;

        $this->set_file_arrays();
        $this->process_font_files();
        $this->copy_files();
        $this->compile_files();
        $this->generate_manifest();
        $this->template_replace_files();

        echo CLI::$verbose
            ? "\nBuilding project " . Project::$url . " DONE\n"
            : " - DONE\n";
    }

    /**
     * Deploys a project to Netlify according to build data.
     *
     * @return  void
     */
    private function deploy_project(): void {

        if (empty(Project::$directory_path)) {
            CLI::display_error("Failed to deploy project: project directory path is empty.");
            exit;
        }

        if (!is_dir(Project::$directory_path)) {
            CLI::display_error("Failed to deploy project: project directory doesn't exist.");
            exit;
        }

        if (empty(Project::$url)) {
            CLI::display_error("Failed to deploy project: URL is empty.");
            exit;
        }

        if (empty(Project::$netlify_id)) {
            CLI::display_error("Failed to deploy project: Netlify ID is empty.");
            exit;
        }

        // Increment build version.
        $this->set_build_version(true);

        echo 'Deploying project ' . Project::$netlify_id . ' v' . Project::$version . ' to ' . Project::$url;

        // Execute Netlify CLI deployment.
        $output = [];
        // phpcs:ignore Generic.Files.LineLength.TooLong
        exec('npx netlify deploy --prod --dir=' . Project::$directory_path . ' --site=' . Project::$netlify_id, $output);
        $result = array_filter($output, fn($output_line) => stripos($output_line, 'Deploy URL') !== false);

        if (empty($result)) {
            CLI::display_error("Failed to deploy project.");
            exit;
        }

        echo CLI::$verbose ? "\nDeploying project " . Project::$url . " DONE\n" : "";
    }

    /**
     * Set build version number in service worker file.
     *
     * @param   bool    $increment  If true, increments build version number.
     * @return  void
     */
    private function set_build_version(bool $increment = false): void {

        if (empty(Project::$directory_path)) {
            CLI::display_error("Failed to set build version: project directory path is empty.");
            exit;
        }

        if (!is_dir(Project::$directory_path)) {
            CLI::display_error("Failed to set build version: project directory doesn't exist.");
            exit;
        }

        $service_worker_file = Project::$directory_path . '/sw.js';

        if (!file_exists($service_worker_file)) {
            CLI::display_error("Failed to set build version: could not find {$service_worker_file} file");
            exit;
        }

        $file_contents = file_get_contents($service_worker_file, true);

        if (empty($file_contents)) {
            CLI::display_error(
                "Failed to set build version: contents could not be extracted from {$service_worker_file}"
            );
            exit;
        }

        // Extract version from project sw.js file before overwriting it.
        preg_match('/cache([0-9]+)/', $file_contents, $matches);
        $project_version = !empty($matches[1]) ? $matches[1] : '1';
        $project_version_length = strlen($project_version);

        // Increment build version in service worker file.
        if (!empty($increment)) {
            $position = strpos($file_contents, $project_version);

            if (empty($position)) {
                CLI::display_error(
                    "Failed to set build version: project version could not be found in {$service_worker_file}"
                );
                exit;
            }

            $project_version++;

            $data = substr_replace($file_contents, $project_version, $position, $project_version_length);
            file_put_contents($service_worker_file, $data);
        }

        // Set build version in package.json.
        exec("npm pkg set version={$project_version} --prefix=" . Project::$directory_path);

        Project::set_project_version($project_version);
    }

    /**
     * Create build directory.
     *
     * @return  void
     */
    private function create_build_directory(): void {

        if (empty(Project::$directory_path)) {
            CLI::display_error("Failed to create build directory: project directory path is empty.");
            exit;
        }

        // build directory already exists.
        if (is_dir(Project::$directory_path)) {
            return;
        }

        echo "\nCreating build directory " . Project::$directory_path;
        mkdir(Project::$directory_path);

        if (!is_dir(Project::$directory_path)) {
            CLI::display_error("Failed to create build directory: mkdir() failed.");
            exit;
        }

        echo CLI::$verbose ? "\nCreating build directory DONE\n" : " - DONE";
    }

    /**
     * Populate new build directory.
     *
     * @return  void
     */
    private function populate_build_directory(): void {

        if (empty(Project::$directory_path)) {
            CLI::display_error("Failed to populate build directory: project directory path is empty.");
            exit;
        }

        if (!is_dir(Project::$directory_path)) {
            CLI::display_error("Failed to populate build directory: project directory doesn't exist.");
            exit;
        }

        echo "\nPopulating new build directory " . Project::$directory_path;

        // Create package.json.
        chdir(Project::$directory_path);
        exec("npm init -y");
        exec("npm pkg set name='" . Project::$url . "'");
        exec("npm pkg set description='" . Project::$description . "'");
        exec("npm pkg set version='1'");
        exec("npm pkg delete main");
        exec("npm pkg delete scripts.test");
        exec("npm pkg set scripts.build='webpack --mode production'");
        exec("npm install --save-dev webpack-cli ts-loader sass css-loader sass-loader style-loader");
        chdir(dirname(__DIR__));

        // Create scss directory.
        $scss_dir = Project::$directory_path . '/scss/';
        if (!is_dir($scss_dir)) {
            echo CLI::$verbose ? "\nCreating scss directory..." : "";
            mkdir($scss_dir);
            if (!is_dir($scss_dir)) {
                CLI::display_error("Failed to populate build directory: cound not create scss directory");
                exit;
            }
            echo CLI::$verbose ? "\nCreating scss directory DONE" : "";
        }

        // Create js directory.
        $js_dir = Project::$directory_path . '/js/';
        if (!is_dir($js_dir)) {
            echo CLI::$verbose ? "\nCreating js directory..." : "";
            mkdir($js_dir);
            if (!is_dir($js_dir)) {
                CLI::display_error("Failed to populate build directory: cound not create js directory");
                exit;
            }
            echo CLI::$verbose ? "\nCreating js directory DONE" : "";
        }

        // Create opt directory.
        $opt_dir = Project::$directory_path . '/opt/';
        if (!is_dir($opt_dir)) {
            echo CLI::$verbose ? "\nCreating opt directory..." : "";
            mkdir($opt_dir);
            if (!is_dir($opt_dir)) {
                CLI::display_error("Failed to populate build directory: cound not create opt directory");
                exit;
            }
            echo CLI::$verbose ? "\nCreating opt directory DONE" : "";
        }

        echo CLI::$verbose ? "\n\tCreating .gitignore file" : "";
        file_put_contents(Project::$directory_path . '/.gitignore', 'node_modules');

        echo CLI::$verbose ? "\n\tCreating index.php file" : "";
        file_put_contents(Project::$directory_path . '/index.php', '');

        echo CLI::$verbose ? "\n\tCreating scss/style.scss file" : "";
        $data = '';
        if (
            !empty(Project::$fonts['heading'])
            && !empty(Project::$fonts['body'])
            && !empty(Project::$colors['main']['normal'])
            && !empty(Project::$colors['main']['dark'])
            && !empty(Project::$colors['accent']['normal'])
            && !empty(Project::$colors['accent']['dark'])
        ) {
            $data = ':root {' .
                "\n\t" . '--font-heading: "' . Project::$fonts['heading'] . '";' .
                "\n\t" . '--font-body: "' . Project::$fonts['body'] . '";' .
                "\n\t" . '--color-main: #' . Project::$colors['main']['normal'] . ';' .
                "\n\t" . '--color-main-dark: #' . Project::$colors['main']['dark'] . ';' .
                "\n\t" . '--color-accent: #' . Project::$colors['accent']['normal'] . ';' .
                "\n\t" . '--color-accent-dark: #' . Project::$colors['accent']['dark'] . ';' .
            "\n" . '}';
        }
        file_put_contents(Project::$directory_path . '/scss/style.scss', $data);

        echo CLI::$verbose ? "\n\tCreating js/main.ts file" : "";
        file_put_contents(Project::$directory_path . '/js/main.ts', '');

        echo CLI::$verbose ? "\n\tCreating sw.js file" : "";
        file_put_contents(Project::$directory_path . '/sw.js', 'const cacheName = "cache1";');

        echo CLI::$verbose ? "\nPopulating new build directory DONE\n" : " - DONE";
    }

    /**
     * Set up file arrays for caching, compiling, and copying.
     */
    private function set_file_arrays(): void {
        // Add data.json or posts.json to cache file list if they exist.
        $project_data_file = Project::$directory_path . '/data.json';
        if (file_exists($project_data_file)) {
            $this->cache_files[] = 'data.json';
        }

        $project_posts_file = Project::$directory_path . '/posts.json';
        if (file_exists($project_posts_file)) {
            $this->cache_files[] = 'posts.json';
        }

        // Add "cache_files" defined in project.json to cache file array.
        if (!empty(Project::$cache_files) && is_array(Project::$cache_files)) {
            foreach (Project::$cache_files as $file) {
                $this->cache_files[] = $file;
            }
        }

        // Add "opt_files" defined in project.json to cache and copy file arrays if they exist in files/opt.
        $opt_files = glob(dirname(__DIR__) . '/files/opt/*');
        if (!empty($opt_files) && is_array($opt_files)) {
            foreach ($opt_files as $file) {
                if (in_array('opt/' . basename($file), Project::$opt_files, true)) {
                    $this->cache_files[] = 'opt/' . basename($file);
                    $this->copy_files[] = 'opt/' . basename($file);
                }
            }
        }

        // Add files in files/scss directory to copy file array.
        $sass_files = glob(dirname(__DIR__) . '/files/scss/*');
        if (!empty($sass_files) && is_array($sass_files)) {
            foreach ($sass_files as $file) {
                $this->copy_files[] = 'scss/' . basename($file);
            }
        }

        // Add files in files/js directory to copy file array.
        $js_files = glob(dirname(__DIR__) . '/files/js/*');
        if (!empty($js_files) && is_array($js_files)) {
            foreach ($js_files as $file) {
                $this->copy_files[] = 'js/' . basename($file);
            }
        }

        /**
         * Add pages to:
         *  $this->compile_files, for static HTML file generation.
         *  $this->cache_files, for caching by service worker (also add .jpg if page type === post).
         *  Project::$sitemap_urls, for sitemap generation.
         */
        if (!empty(Project::$pages) && is_array(Project::$pages)) {
            foreach (Project::$pages as $page_data) {
                if (empty($page_data['url']) || !is_string($page_data['url'])) {
                    continue;
                }

                $page_filename = "{$page_data['url']}.html";

                // Add page to compile file list.
                if (!in_array($page_data['url'], array_column($this->compile_files, 'page'), true)) {
                    $this->compile_files[] = [
                        'source_file' => dirname(__DIR__) . '/templates/home.php',
                        'target_file' => $page_filename,
                        'page' => $page_data['url'],
                    ];
                }

                // Add page to cache file list.
                if (!in_array($page_filename, $this->cache_files)) {
                    $this->cache_files[] = $page_filename;
                }

                // Add page featured image to cache file list if page is a post.
                if ($page_data['type'] === 'post' && !in_array("img/{$page_data['url']}.jpg", $this->cache_files)) {
                    $this->cache_files[] = "img/{$page_data['url']}.jpg";
                }

                // Add page to sitemap URLs.
                if (!in_array($page_data['url'], Project::$sitemap_urls)) {
                    Project::$sitemap_urls[] = $page_data['url'];
                }
            }
        }
    }

    /**
     * Copy files in project copy array into build directory.
     *
     * @return  void
     */
    private function copy_files(): void {

        if (empty($this->copy_files) || !is_array($this->copy_files)) {
            return;
        }

        if (empty(Project::$directory_path)) {
            CLI::display_error("Failed to copy files: project directory path is empty.");
            exit;
        }

        if (!is_dir(Project::$directory_path)) {
            CLI::display_error("Failed to copy files: project directory doesn't exist.");
            exit;
        }

        echo CLI::$verbose ? "\nCopying " . count($this->copy_files) . " files" : "";

        // Copy files into build directory.
        foreach ($this->copy_files as $file) {
            $target_file = Project::$directory_path . "/{$file}";
            echo CLI::$verbose ? "\n\tCopying {$file} to {$target_file}" : "";
            copy(dirname(__DIR__) . "/files/{$file}", $target_file);
        }

        echo CLI::$verbose ? "\nCopying files DONE" : "";
    }

    /**
     * Compiles and minifies style.scss and files in project compile array into build directory.
     *
     * @return  void
     */
    private function compile_files(): void {

        if (empty(Project::$directory_path)) {
            CLI::display_error("Failed to compile files: project directory path is empty.");
            exit;
        }

        if (!is_dir(Project::$directory_path)) {
            CLI::display_error("Failed to compile files: project directory doesn't exist.");
            exit;
        }

        $content_to_minify = null;

        // Compile Sass and JavaScript.
        if (file_exists(Project::$directory_path . '/js/index.ts')) {
            // First, remove any existing bundle.*.js files.
            exec('rm -rf ' . Project::$directory_path . '/js/bundle.*.js');

            // Create new bundle.
            echo CLI::$verbose ? "\nCompiling bundle" : "";
            exec('npm run build --prefix=' . Project::$directory_path);

            // Set bundle file name for use in <head>.
            $bundle_path = glob(Project::$directory_path . '/js/bundle.*.js');

            $bundle_filename = !empty($bundle_path[0]) ? basename($bundle_path[0]) : '';

            if (empty($bundle_filename)) {
                CLI::display_error("Failed to compile files: bundle filename is empty.");
                exit;
            }

            $this->js_bundle_filename = 'js/' . $bundle_filename;
            $this->cache_files[] = 'js/' . $bundle_filename;

            echo CLI::$verbose ? "\nCompiling {$bundle_filename} DONE" : "";
        }

        if (empty($this->compile_files || !is_array($this->compile_files))) {
            return;
        }

        // Compile PHP files.
        echo CLI::$verbose ? "\nCompiling " . count($this->compile_files) . " files" : "";

        // Gather, encode, and escape project data for passing to php files.
        $project_reflection_class = new \ReflectionClass('PWA_Generator\Project');
        $project_data = $project_reflection_class->getStaticProperties();

        // Add build files array and js bundle filename to project data.
        $project_data['files'] = [
            'cache' => $this->cache_files,
            'compile' => $this->compile_files,
            'copy' => $this->copy_files,
        ];
        $project_data['js_bundle_filename'] = $this->js_bundle_filename;

        // Encode project data.
        $project_data_json = json_encode($project_data);

        if (empty($project_data_json)) {
            CLI::display_error("Failed to compile files: encoded project data is empty.");
            exit;
        }

        // Escape project data.
        $project_data_json_escaped = escapeshellarg($project_data_json);

        foreach ($this->compile_files as $file) {
            if (empty($file['source_file']) || empty($file['target_file']) || empty($file['page'])) {
                continue;
            }

            // Skip any non-php files.
            if (stripos($file['source_file'], '.php') === false) {
                continue;
            }

            $target_file_path = Project::$directory_path . "/{$file['target_file']}";

            echo CLI::$verbose ? "\n\tCompiling {$file['source_file']} to {$target_file_path}" : "";

            // Open the file using PHP with the page and project_data as arguments.
            exec("php {$file['source_file']} {$file['page']} {$project_data_json_escaped} > {$target_file_path}");

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
                CLI::display_error(
                    "Failed to compile files: target file {$target_file_path} minified content is empty."
                );
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
     * @return  void
     */
    private function template_replace_files(): void {

        if (empty(Project::$url)) {
            CLI::display_error("Failed to replace patterns in template files: URL is empty.");
            exit;
        }

        if (empty(Project::$title)) {
            CLI::display_error("Failed to replace patterns in template files: title is empty.");
            exit;
        }

        if (empty(Project::$description)) {
            CLI::display_error("Failed to replace patterns in template files: description is empty.");
            exit;
        }

        if (empty(Project::$version)) {
            CLI::display_error("Failed to replace patterns in template files: project version is empty.");
            exit;
        }

        echo CLI::$verbose ? "\nReplacing data in project files..." : "";

        $files = '';

        // Set files string from cache files array.
        if (!empty($this->cache_files) && is_array($this->cache_files)) {
            foreach ($this->cache_files as $index => $file) {
                $files .= "\"{$file}\",";

                // Only add new line if followed by another file.
                if (!empty($this->cache_files[$index + 1])) {
                    $files .= "\n\t";
                }
            }
        }

        // Set patterns to find and replace.
        $patterns = [
            '// ***FILES***' => $files,
            '***URLS***' => $this->get_sitemap_urls(),
            '***REDIRECT_URLS***' => $this->get_redirect_urls(),
            '***URL***' => Project::$url,
            '***TITLE***' => Project::$title,
            '***DESCRIPTION***' => Project::$description,
            '***VERSION***' => Project::$version,
            '***DATE***' => date('Y-m-d'),
        ];

        // Set array of files in which to replace patterns.
        $replace_files = array_unique(
            array_merge(
                array_column($this->compile_files, 'target_file'),
                $this->copy_files,
            ),
        );

        if (empty($replace_files)) {
            CLI::display_error("Failed to replace patterns in template files: replace files array is empty.");
            exit;
        }

        // Replace common patterns in files.
        foreach ($replace_files as $file) {
            $replace_file = Project::$url . "/{$file}";
            $replace_file_path = dirname(__DIR__, 2) . "/{$replace_file}";

            if (!file_exists($replace_file_path)) {
                CLI::display_error(
                    "Failed to replace patterns in template files: file {$replace_file} does not exist."
                );
                continue;
            }

            echo CLI::$verbose ? "\n\tReplacing content in {$replace_file}" : "";

            $file_contents = file_get_contents($replace_file_path);

            if (!empty($file_contents)) {
                foreach ($patterns as $find => $replace) {
                    if (stripos($file_contents, $find) !== false) {
                        $file_contents = str_replace($find, $replace, $file_contents);
                    }
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
     * @return  void
     */
    private function process_font_files(): void {

        if (empty(Project::$directory_path)) {
            CLI::display_error("Failed to process font files: project directory path is empty.");
            exit;
        }

        if (!is_dir(Project::$directory_path)) {
            CLI::display_error("Failed to process font files: project directory doesn't exist.");
            exit;
        }

        // Create font directories and add heading and body font files to project data.
        if (!empty(Project::$fonts)) {
            // Create fonts directory.
            $fonts_dir = Project::$directory_path . '/fonts';
            if (!is_dir($fonts_dir)) {
                echo CLI::$verbose ? "\nCreating fonts directory..." : "";
                mkdir($fonts_dir);
                if (!is_dir($fonts_dir)) {
                    CLI::display_error("Failed to process font files: cound not create fonts directory");
                    exit;
                }
                echo CLI::$verbose ? "\nCreating fonts directory DONE" : "";
            }

            if (!empty(Project::$fonts['heading'])) {
                $heading_font = Project::$fonts['heading'];
                $heading_font_decoded = strtolower(str_ireplace(' ', '-', $heading_font));

                // Create heading font directory.
                $heading_font_dir = Project::$directory_path . "/fonts/{$heading_font_decoded}";
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
                $heading_font_files = glob(dirname(__DIR__) . "/files/fonts/{$heading_font_decoded}/*");
                if (!empty($heading_font_files) && is_array($heading_font_files)) {
                    foreach ($heading_font_files as $index => $file) {
                        $heading_font_file = basename($file);
                        $heading_font_files[$index] = "fonts/{$heading_font_decoded}/{$heading_font_file}";
                    }

                    $this->copy_files = !empty($this->copy_files)
                        ? array_merge($this->copy_files, $heading_font_files)
                        : $heading_font_files;
                    $this->cache_files = !empty($this->cache_files)
                        ? array_merge($this->cache_files, $heading_font_files)
                        : $heading_font_files;
                }
            }

            if (!empty(Project::$fonts['body'])) {
                $body_font = Project::$fonts['body'];
                $body_font_decoded = strtolower(str_ireplace(' ', '-', $body_font));

                // Create body font directory.
                $body_font_dir = Project::$directory_path . "/fonts/{$body_font_decoded}";
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
                $body_font_files = glob(dirname(__DIR__) . "/files/fonts/{$body_font_decoded}/*");
                if (!empty($body_font_files) && is_array($body_font_files)) {
                    foreach ($body_font_files as $index => $file) {
                        $body_font_file = basename($file);
                        $body_font_files[$index] = "fonts/{$body_font_decoded}/{$body_font_file}";
                    }

                    $this->copy_files = !empty($this->copy_files)
                        ? array_merge($this->copy_files, $body_font_files)
                        : $body_font_files;
                    $this->cache_files = !empty($this->cache_files)
                        ? array_merge($this->cache_files, $body_font_files)
                        : $body_font_files;
                }
            }
        }

        // Create fontawesome directories and add files to project data.
        if (!empty(Project::$fontawesome)) {
            // Create fontawesome directories.
            $directories = [
                Project::$directory_path . '/fonts/fontawesome',
                Project::$directory_path . '/fonts/fontawesome/css',
                Project::$directory_path . '/fonts/fontawesome/webfonts',
            ];
            foreach ($directories as $directory_path) {
                if (!is_dir($directory_path)) {
                    echo CLI::$verbose ? "\nCreating {$directory_path} directory..." : "";
                    mkdir($directory_path);
                    if (!is_dir($directory_path)) {
                        CLI::display_error(
                            "Failed to process font files: could not create {$directory_path} directory"
                        );
                        exit;
                    }
                    echo CLI::$verbose ? "\nCreating {$directory_path} directory DONE" : "";
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

            $this->copy_files = !empty($this->copy_files)
                ? array_merge($this->copy_files, $fontawesome_files)
                : $fontawesome_files;
            $this->cache_files = !empty($this->cache_files)
                ? array_merge($this->cache_files, $fontawesome_files)
                : $fontawesome_files;
        }
    }

    /**
     * Get sitemap URLs.
     *
     * Used in sitemap.xml file.
     *
     * @return  string  Sitemap URLs.
     */
    private function get_sitemap_urls(): string {

        // Add any redirected URLs to sitemap URLs.
        if (!empty(Project::$redirects) && is_array(Project::$redirects)) {
            foreach (Project::$redirects as $index => $redirect) {
                $url = trim($redirect['from'], '/');
                if ($url !== '' && $url !== 'index' && stripos($url, '.') !== false && stripos($url, '*') !== false) {
                    Project::$sitemap_urls[] = $url;
                }
            }
        }

        $sitemap_urls = '';

        // Add <url> data for each URL.
        if (!empty(Project::$sitemap_urls) && is_array(Project::$sitemap_urls)) {
            foreach (Project::$sitemap_urls as $index => $url) {
                $sitemap_urls .= str_ireplace(
                    [
                        '{url}',
                        '{date}',
                    ],
                    [
                        Project::$url . '/' . ($url !== 'index' ? $url : ''),
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
     * @return  string  Sitemap URLs.
     */
    private function get_redirect_urls(): string {

        $redirect_urls = '';

        if (!empty($this->compile_files) && is_array($this->compile_files)) {
            foreach ($this->compile_files as $file) {
                if (empty($file['target_file'])) {
                    continue;
                }

                if ($file['page'] !== 'index') {
                    $redirect_urls .= str_ireplace('{title}', $file['page'], '/{title}/* /{title}.html 200') . "\n";
                }
            }
        }

        // If redirects are set, add to urls.
        if (!empty(Project::$redirects) && is_array(Project::$redirects)) {
            foreach (Project::$redirects as $index => $redirect_url) {
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
     * @return  void
     */
    private function generate_manifest(): void {

        if (empty(Project::$directory_path)) {
            CLI::display_error("Failed to generate manifest: project directory path is empty.");
            exit;
        }

        if (!is_dir(Project::$directory_path)) {
            CLI::display_error("Failed to generate manifest: project directory doesn't exist.");
            exit;
        }

        $manifest_file_path = Project::$directory_path . '/manifest.json';

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
        $manifest_data = json_decode($file_contents, true);

        if (empty($manifest_data) || !is_array($manifest_data)) {
            CLI::display_error("Failed to generate manifest: manifest file data is empty or is not an array.");
            exit;
        }

        echo CLI::$verbose ? "\n\tGenerating manifest.json" : "";

        if (!empty(Project::$categories)) {
            $manifest_data['categories'] = Project::$categories;
        }

        if (!empty(Project::$screenshots) && is_array(Project::$screenshots)) {
            $manifest_data['screenshots'] = [];
            foreach (Project::$screenshots as $file) {
                $manifest_data['screenshots'][] = [
                    'src' => $file,
                    'sizes' => '1500x800',
                    'type' => 'image/png'
                ];
            }
        }

        if (!empty(Project::$shortcuts)) {
            $manifest_data['shortcuts'] = Project::$shortcuts;
        }

        $manifest_data['related_applications'][] = [
            'platform' => 'webapp',
            'url' => 'https://' . Project::$url . '/manifest.json',
        ];

        if (!empty(Project::$android_app_id) || !empty(Project::$apple_app_id)) {
            $android_app_id = null;
            if (!empty(Project::$android_app_id['paid'])) {
                $android_app_id = Project::$android_app_id['paid'];
            } elseif (!empty(Project::$android_app_id['free'])) {
                $android_app_id = Project::$android_app_id['free'];
            }

            $apple_app_id = null;
            if (!empty(Project::$apple_app_id['paid'])) {
                $apple_app_id = Project::$apple_app_id['paid'];
            } elseif (!empty(Project::$apple_app_id['free'])) {
                $apple_app_id = Project::$apple_app_id['free'];
            }

            if (!empty($android_app_id) && ($android_app_id !== 'disabled')) {
                $manifest_data['related_applications'][] = [
                    'platform' => 'play',
                    'url' => "https://play.google.com/store/apps/details?id={$android_app_id}",
                    'id' => $android_app_id
                ];
                $manifest_data['prefer_related_applications'] = true;
            }

            if (!empty($apple_app_id) && ($apple_app_id !== 'disabled')) {
                $manifest_data['related_applications'][] = [
                    'platform' => 'itunes',
                    'url' => "https://itunes.apple.com/app/example-app1/id123456789{$apple_app_id}",
                ];
                $manifest_data['prefer_related_applications'] = true;
            }
        }

        // Replace file.
        file_put_contents($manifest_file_path, json_encode($manifest_data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        echo CLI::$verbose ? "\n\tGenerating manifest.json DONE" : "";
    }

    /**
     * Generate favicons.
     *
     * @return  void
     */
    private function generate_favicons(): void {

        if (empty(Project::$directory_path)) {
            CLI::display_error("Failed to generate favicons: build directory path is empty.");
            exit;
        }

        if (!is_dir(Project::$directory_path)) {
            CLI::display_error("Failed to generate favicons: build directory doesn't exist.");
            exit;
        }

        if (!file_exists(Project::$directory_path . '/img/favicon.svg')) {
            CLI::display_error("Failed to generate favicons: favicon.svg is missing.");
            exit;
        }

        echo "Generating favicons...";

        // Set up favicon config data.
        $favicon_data_config = [
            'masterPicture' => Project::$directory_path . '/img/favicon.svg',
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
        file_put_contents(
            Project::$directory_path . '/favicon_config.json',
            json_encode($favicon_data_config, JSON_PRETTY_PRINT)
        );

        // Generate favicons.
        // phpcs:ignore Generic.Files.LineLength.TooLong
        exec('npx real-favicon generate ' . Project::$directory_path . '/favicon_config.json favicon_data.json ' . Project::$directory_path . '/img/');

        // Remove generated favicon data file.
        exec('rm -rf favicon_data.json');

        // Remove favicon data config file.
        exec('rm -rf ' . Project::$directory_path . '/favicon_config.json');

        // Remove favicon manifest file.
        exec('rm -rf ' . Project::$directory_path . '/img/site.webmanifest');

        echo CLI::$verbose ? "\nGenerating favicons DONE" : " - DONE\n";
    }
}
