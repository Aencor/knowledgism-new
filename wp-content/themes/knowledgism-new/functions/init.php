<?php
/**
 * Initial setup and constants
 */
function knowledgism_theme_setup() {
	// Enable plugins to manage the document title
	add_theme_support("title-tag");

	// Add post thumbnails
	add_theme_support("post-thumbnails");

	// Add Menus
	add_theme_support("menus");
	register_nav_menus([
		'primary' => __('Primary Navigation', 'knowledgism'),
		'footer'  => __('Footer Navigation', 'knowledgism'),
	]);

	// Add HTML5 markup for captions
	add_theme_support("html5", ["caption", "comment-form", "comment-list"]);

	// Adding Theme Support for Custom Logo
	add_theme_support("custom-logo", [
		'height'      => 50,
		'width'       => 250,
		'flex-height' => true,
		'flex-width'  => true,
	]);

	// Adding WooCommerce Theme Support
	add_theme_support("woocommerce");
	add_theme_support("wc-product-gallery-zoom");
	add_theme_support("wc-product-gallery-lightbox");
	add_theme_support("wc-product-gallery-slider");
}
add_action("after_setup_theme", "knowledgism_theme_setup");

/**
 * Update cart count badge via WooCommerce AJAX fragments
 */
function knowledgism_header_cart_fragments($fragments) {
	$cart_count = (function_exists('WC') && WC()->cart) ? WC()->cart->get_cart_contents_count() : 0;
	$has_items = $cart_count > 0 ? 'has-items' : '';
	$fragments['.btn-cart-header .cart-badge'] = '<span class="cart-badge ' . esc_attr($has_items) . '">' . esc_html($cart_count) . '</span>';
	return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'knowledgism_header_cart_fragments');

/**
 * Route Events Calendar archive and single requests to custom templates
 */
function knowledgism_events_template_include($template) {
	if (is_singular('tribe_events')) {
		$custom = locate_template('single-tribe_events.php');
		if ($custom) {
			return $custom;
		}
	}

	if (
		is_post_type_archive('tribe_events') ||
		(function_exists('tribe_is_events_front_page') && tribe_is_events_front_page()) ||
		(function_exists('tribe_is_view') && tribe_is_view()) ||
		(function_exists('tribe_is_upcoming') && tribe_is_upcoming()) ||
		(function_exists('tribe_is_past') && tribe_is_past()) ||
		(function_exists('tribe_is_month') && tribe_is_month()) ||
		(function_exists('tribe_is_day') && tribe_is_day())
	) {
		$custom = locate_template('archive-tribe_events.php');
		if ($custom) {
			return $custom;
		}
	}
	return $template;
}
add_filter('template_include', 'knowledgism_events_template_include', 9999);

/**
 * Intercept /articles/ and /glossary/ requests even if 404 in DB
 */
function knowledgism_intercept_custom_routes() {
	if (is_admin()) {
		return;
	}
	$request_path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
	if ($request_path === 'glossary' || $request_path === 'articles') {
		global $wp_query;
		$wp_query->is_404 = false;
		status_header(200);
	}
}
add_action('template_redirect', 'knowledgism_intercept_custom_routes', 1);

function knowledgism_custom_routes_template_include($template) {
	if (is_admin()) {
		return $template;
	}
	global $wp_query;
	$request_path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');

	if ($request_path === 'glossary' || is_page('glossary')) {
		$glossary_file = locate_template('page-glossary.php');
		if ($glossary_file) {
			$wp_query->is_404 = false;
			status_header(200);
			return $glossary_file;
		}
	}

	if ($request_path === 'articles' || is_page('articles')) {
		$articles_file = locate_template('page-articles.php');
		if ($articles_file) {
			$wp_query->is_404 = false;
			status_header(200);
			return $articles_file;
		}
	}

	return $template;
}
add_filter('template_include', 'knowledgism_custom_routes_template_include', 999);

/**
 * Automatically ensure 'articles' and 'glossary' pages exist in the DB
 */
function knowledgism_ensure_required_pages() {
	if (!get_page_by_path('articles')) {
		wp_insert_post([
			'post_title'     => 'Articles',
			'post_name'      => 'articles',
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'page_template'  => 'page-articles.php',
		]);
	}
	if (!get_page_by_path('glossary')) {
		$training = get_page_by_path('training-glossary');
		if ($training) {
			wp_update_post([
				'ID'        => $training->ID,
				'post_name' => 'glossary',
			]);
		} else {
			wp_insert_post([
				'post_title'     => 'Training Glossary',
				'post_name'      => 'glossary',
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'page_template'  => 'page-glossary.php',
			]);
		}
	}
}
add_action('init', 'knowledgism_ensure_required_pages');

