#!/usr/local/bin/php

<?php // phpcs:ignore PSR12.Files.FileHeader.HeaderPosition

/**
 * PWA Generator
 *
 * @author    Nicole Furlan <info@nicolefurlan.com>
 * @link      https://github.com/nikkifurls/pwagenerator
 *
 * Usage:
 * ./pwagenerator.php projects                  List all projects configured for building in projects.json
 * ./pwagenerator.php [project] [option]        Build, deploy, and/or generate icons for a project using project
 *                                              configuration in projects.json
 *
 * Options:
 *  -v verbose
 *  -b build
 *  -i generate icons
 *  -d deploy
 *
 * Example:
 * ./pwagenerator exampleproject.com -b
 *
 * To create a new project, add it to projects.json, then build it.
 */

require 'vendor/autoload.php';

// Only run via CLI.
if ('cli' !== php_sapi_name()) {
    exit;
}

$cli = new PWA_Generator\CLI();

// Display usage if insufficient arguments provided.
if (empty($argv) || count($argv) <= 1) {
    $cli->show_usage();
    exit;
}

// List projects.
if (in_array('projects', $argv, true)) {
    $cli->list_projects();
    exit;
}

// Set verbose mode.
if (in_array('-v', $argv, true)) {
    $cli->set_verbose(true);
}

$project = '';
$generate_favicons = false;
$build = false;
$deploy = false;

if (!empty($argv[1])) {
    $project = $argv[1];
}

if (in_array('-i', $argv, true)) {
    $generate_favicons = true;
}

if (in_array('-b', $argv, true)) {
    $build = true;
}

if (in_array('-d', $argv, true)) {
    $deploy = true;
}

$build = new PWA_Generator\Build(
    $project,
    [
        'generate_favicons' => $generate_favicons,
        'build' => $build,
        'deploy' => $deploy,
    ]
);
