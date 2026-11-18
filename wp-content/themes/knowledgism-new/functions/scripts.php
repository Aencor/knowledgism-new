<?php

function knowledgism_theme_scripts() {
	if (is_single() && comments_open() && get_option("thread_comments")) {
		wp_enqueue_script("comment-reply");
	}

	// Google Fonts: Fraunces, Inter, Space Mono (exactly as in prototype)
	wp_enqueue_style(
		"knowledgism-fonts",
		"https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap",
		[],
		null
	);

	// Main Theme Stylesheet (style.css contains complete prototype CSS)
	wp_enqueue_style(
		"knowledgism-style",
		get_stylesheet_uri(),
		["knowledgism-fonts"],
		time() // Cache buster during development
	);

	// Main Script (Mobile menu toggle and prototype interactions)
	wp_enqueue_script(
		"knowledgism-main",
		get_template_directory_uri() . "/assets/js/main.js",
		[],
		time(),
		true
	);
}

add_action("wp_enqueue_scripts", "knowledgism_theme_scripts", 9999);
