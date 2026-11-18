<?php

/**
 * Includes
 *
 * The $monk_includes array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 *
 * Please note that missing files will produce a fatal error.
 *
 */
$monk_includes = [
	"functions/init.php", // Initial theme setup and constants
	"functions/config.php", // Configuration
	"functions/scripts.php", // Scripts and stylesheets
	"functions/security.php", // Security focused settings
	"functions/options.php", // ACF Theme Options
	"functions/shortcodes.php", // Custom Wordpress Shortcodes for WYSIWYGs
	"functions/blocks.php", // Declare Custom Blocks
	"functions/foundations.php", // Foundations AJAX and layout
	"functions/glossary.php", // Training Glossary data and helpers
	"functions/i18n.php" // Theme internationalization and Spanish dictionary
];

function nylon_include($includes) {
	if (!is_array($includes)) {
		$includes = [$includes];
	}
	foreach ($includes as $file) {
		if (!($filepath = locate_template($file))) {
			trigger_error(
				sprintf(__("Error locating %s for inclusion", "nylon"), $file),
				E_USER_ERROR
			);
		}
		require_once $filepath;
	}
}
nylon_include($monk_includes);

/**
 * Autoload classes from classes/ directory (Regla 5).
 */
spl_autoload_register(function ($class) {
	$prefix = 'CodeByMonk\\';
	$base_dir = get_template_directory() . '/classes/';
	$len = strlen($prefix);
	if (strncmp($prefix, $class, $len) !== 0) {
		return;
	}
	$relative_class = substr($class, $len);
	$file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
	if (file_exists($file)) {
		require_once $file;
	}
});

/*
 * Editor styles
 * Uncomment to bring custom styles or fonts into the admin editor.
 * add_theme_support('editor-styles');
 * add_editor_style('/assets/build/style-editor.css');
 *
 */

add_theme_support("disable-custom-colors");

add_theme_support("editor-color-palette", [
	[
		"name" => __("Blue", "nylon"),
		"slug" => "blue",
		"color" => "#59BACC"
	],
	[
		"name" => __("Green", "nylon"),
		"slug" => "green",
		"color" => "#58AD69"
	],
	[
		"name" => __("Orange", "nylon"),
		"slug" => "orange",
		"color" => "#FFBC49"
	],
	[
		"name" => __("Red", "nylon"),
		"slug" => "red",
		"color" => "#E2574C"
	],
	[
		"name" => __("White", "nylon"),
		"slug" => "white",
		"color" => "#FFF"
	],
	[
		"name" => __("Transparent", "nylon"),
		"slug" => "transparent",
		"color" => "transparent"
	]
]);
