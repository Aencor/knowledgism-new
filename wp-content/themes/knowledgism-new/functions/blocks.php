<?php
/**
 * Register Gutenberg / ACF Custom Blocks and Categories
 *
 * @package Knowledgism
 */

/**
 * Register custom block category for Knowledgism
 */
function knowledgism_block_category($categories) {
	return array_merge(
		[
			[
				'slug'  => 'knowledgism',
				'title' => __('Knowledgism Blocks', 'knowledgism'),
				'icon'  => 'book',
			],
		],
		$categories
	);
}
add_filter('block_categories_all', 'knowledgism_block_category', 10, 1);

/**
 * Register ACF Block Types
 */
function register_acf_block_types() {
	// Custom blocks with specific configurations
	$custom_blocks = [
		'home-hero' => [
			'title'       => __('Home - Hero', 'knowledgism'),
			'description' => __('Hero Dark with title, lede, CTAs, 4-Zones signature bar, and upcoming events card.', 'knowledgism'),
			'icon'        => 'cover-image',
			'category'    => 'knowledgism',
			'keywords'    => ['home', 'hero', 'events', 'zones', 'knowledgism'],
		],
		'home-eras' => [
			'title'       => __('Home - Eras of Knowledge', 'knowledgism'),
			'description' => __('Foundations grid displaying the Warrior Era, Literacy Era, and Knowledge Era.', 'knowledgism'),
			'icon'        => 'shield',
			'category'    => 'knowledgism',
			'keywords'    => ['home', 'eras', 'foundations', 'warrior', 'literacy', 'knowledgism'],
		],
		'home-articles' => [
			'title'       => __('Home - Featured Articles', 'knowledgism'),
			'description' => __('Grid of 3 featured article category cards by Alan C. Walter.', 'knowledgism'),
			'icon'        => 'admin-post',
			'category'    => 'knowledgism',
			'keywords'    => ['home', 'articles', 'categories', 'knowledgism'],
		],
		'home-zone-cta' => [
			'title'       => __('Home - Carousel / Zone Assessment CTA', 'knowledgism'),
			'description' => __('Interactive carousel featuring the Zone Profile assessment, The Fire Within, The Zone Series, and Bookstore catalog.', 'knowledgism'),
			'icon'        => 'slides',
			'category'    => 'knowledgism',
			'keywords'    => ['home', 'carousel', 'cta', 'zone', 'profile', 'fire within', 'books', 'knowledgism'],
		],
		'home-newsletter' => [
			'title'       => __('Home - Word of the Week Newsletter', 'knowledgism'),
			'description' => __('Newsletter subscription band for The Word of the Week.', 'knowledgism'),
			'icon'        => 'email-alt',
			'category'    => 'knowledgism',
			'keywords'    => ['home', 'newsletter', 'word of the week', 'knowledgism'],
		],
		'glossary' => [
			'title'       => __('Glossary / Glosario', 'knowledgism'),
			'description' => __('Glossary block with words and meanings automatically sorted and grouped by letters (A-Z).', 'knowledgism'),
			'icon'        => 'translation',
			'category'    => 'knowledgism',
			'keywords'    => ['glossary', 'glosario', 'dictionary', 'words', 'alphabet', 'knowledgism'],
		],
	];

	foreach ($custom_blocks as $name => $opts) {
		$block_data = [
			'name'            => $name,
			'title'           => $opts['title'],
			'description'     => $opts['description'],
			'render_template' => "blocks/{$name}.php",
			'category'        => $opts['category'],
			'icon'            => $opts['icon'],
			'keywords'        => $opts['keywords'],
			'supports'        => [
				'align'  => false,
				'anchor' => true,
				'mode'   => true,
			],
			'example'         => [
				'attributes' => [
					'mode' => 'preview',
					'data' => [
						'is_example' => true,
					],
				],
			],
		];

		$js_path = get_template_directory() . "/assets/js/{$name}.js";
		if (file_exists($js_path)) {
			$block_data['enqueue_script'] = get_template_directory_uri() . "/assets/js/{$name}.js";
		}

		acf_register_block_type($block_data);
	}

	// Legacy / general block names
	$general_block_names = [
		'hero',
		'side-by-side',
		'cards',
		'tabs',
		'stats',
		'cta-label',
		'logo-grid',
		'styleguide',
	];

	foreach ($general_block_names as $name) {
		$title = ucwords(str_replace(['-', '_'], ' ', $name));
		$block = [
			'name'            => $name,
			'title'           => __($title),
			'description'     => __($title),
			'render_template' => "blocks/{$name}.php",
			'category'        => 'common',
			'icon'            => 'editor-alignleft',
			'keywords'        => [$name],
		];

		$js_path = get_template_directory() . "/assets/js/{$name}.js";
		if (file_exists($js_path)) {
			$block['enqueue_script'] = get_template_directory_uri() . "/assets/js/{$name}.js";
		}

		acf_register_block_type($block);
	}
}

if (function_exists('acf_register_block_type')) {
	add_action('acf/init', 'register_acf_block_types');
}

/**
 * Register ACF Field Groups for Home Blocks
 */
function knowledgism_register_home_block_field_groups() {
	if (!function_exists('acf_add_local_field_group')) {
		return;
	}

	// 1. Home - Hero
	acf_add_local_field_group([
		'key'      => 'group_home_hero',
		'title'    => __('Block - Home Hero', 'knowledgism'),
		'fields'   => [
			[
				'key'           => 'field_hh_kicker',
				'label'         => __('Kicker', 'knowledgism'),
				'name'          => 'kicker',
				'type'          => 'text',
				'default_value' => 'The Knowledge Era',
			],
			[
				'key'           => 'field_hh_title',
				'label'         => __('Title', 'knowledgism'),
				'name'          => 'title',
				'type'          => 'text',
				'default_value' => 'Knowledge without action does not produce results.',
			],
			[
				'key'           => 'field_hh_lede',
				'label'         => __('Lede / Subtitle', 'knowledgism'),
				'name'          => 'lede',
				'type'          => 'textarea',
				'rows'          => 3,
				'default_value' => 'The written and recorded work of Alan C. Walter, organized so you can actually use it. Start by finding out which Zone you are operating from today.',
			],
			[
				'key'           => 'field_hh_primary_btn_text',
				'label'         => __('Primary Button Text', 'knowledgism'),
				'name'          => 'primary_btn_text',
				'type'          => 'text',
				'default_value' => 'Find your Zone — free',
			],
			[
				'key'           => 'field_hh_primary_btn_url',
				'label'         => __('Primary Button URL', 'knowledgism'),
				'name'          => 'primary_btn_url',
				'type'          => 'text',
				'default_value' => 'https://app.zonesoflife.com',
			],
			[
				'key'           => 'field_hh_secondary_btn_text',
				'label'         => __('Secondary Button Text', 'knowledgism'),
				'name'          => 'secondary_btn_text',
				'type'          => 'text',
				'default_value' => 'Download the free chapter',
			],
			[
				'key'           => 'field_hh_secondary_btn_url',
				'label'         => __('Secondary Button URL', 'knowledgism'),
				'name'          => 'secondary_btn_url',
				'type'          => 'text',
				'default_value' => '#profile',
			],
			[
				'key'           => 'field_hh_show_zones',
				'label'         => __('Show 4-Zones Signature Bar', 'knowledgism'),
				'name'          => 'show_zones',
				'type'          => 'true_false',
				'default_value' => 1,
				'ui'            => 1,
			],
			[
				'key'           => 'field_hh_events_title',
				'label'         => __('Events Card Title', 'knowledgism'),
				'name'          => 'events_title',
				'type'          => 'text',
				'default_value' => 'Upcoming events',
			],
			[
				'key'           => 'field_hh_events_btn_text',
				'label'         => __('Events Card Button Text', 'knowledgism'),
				'name'          => 'events_btn_text',
				'type'          => 'text',
				'default_value' => 'See all events',
			],
			[
				'key'           => 'field_hh_events_btn_url',
				'label'         => __('Events Card Button URL', 'knowledgism'),
				'name'          => 'events_btn_url',
				'type'          => 'text',
				'default_value' => '/events/',
			],
		],
		'location' => [
			[
				[
					'param'    => 'block',
					'operator' => '==',
					'value'    => 'acf/home-hero',
				],
			],
		],
	]);

	// 2. Home - Eras of Knowledge
	acf_add_local_field_group([
		'key'      => 'group_home_eras',
		'title'    => __('Block - Home Eras of Knowledge', 'knowledgism'),
		'fields'   => [
			[
				'key'           => 'field_he_section_title',
				'label'         => __('Section Title', 'knowledgism'),
				'name'          => 'section_title',
				'type'          => 'text',
				'default_value' => 'The game is knowledge versus ignorance',
			],
			[
				'key'           => 'field_he_button_text',
				'label'         => __('Button Text', 'knowledgism'),
				'name'          => 'button_text',
				'type'          => 'text',
				'default_value' => 'Read the foundations',
			],
			[
				'key'           => 'field_he_button_url',
				'label'         => __('Button URL', 'knowledgism'),
				'name'          => 'button_url',
				'type'          => 'text',
				'default_value' => '/about/',
			],
			// Era 1
			[
				'key'           => 'field_he_era1_tag',
				'label'         => __('Era 1 - Tag / Date', 'knowledgism'),
				'name'          => 'era1_tag',
				'type'          => 'text',
				'default_value' => '6000 BC — 400 AD',
			],
			[
				'key'           => 'field_he_era1_title',
				'label'         => __('Era 1 - Title', 'knowledgism'),
				'name'          => 'era1_title',
				'type'          => 'text',
				'default_value' => 'The Warrior Era',
			],
			[
				'key'           => 'field_he_era1_desc',
				'label'         => __('Era 1 - Description', 'knowledgism'),
				'name'          => 'era1_desc',
				'type'          => 'textarea',
				'rows'          => 2,
				'default_value' => 'Nations and relationships were conquered, dominated and forced to comply.',
			],
			// Era 2
			[
				'key'           => 'field_he_era2_tag',
				'label'         => __('Era 2 - Tag / Date', 'knowledgism'),
				'name'          => 'era2_tag',
				'type'          => 'text',
				'default_value' => '1000 AD — today',
			],
			[
				'key'           => 'field_he_era2_title',
				'label'         => __('Era 2 - Title', 'knowledgism'),
				'name'          => 'era2_title',
				'type'          => 'text',
				'default_value' => 'The Literacy Era',
			],
			[
				'key'           => 'field_he_era2_desc',
				'label'         => __('Era 2 - Description', 'knowledgism'),
				'name'          => 'era2_desc',
				'type'          => 'textarea',
				'rows'          => 2,
				'default_value' => 'A near-worldwide push to read, write and reckon. Lifespan almost doubled.',
			],
			// Era 3
			[
				'key'           => 'field_he_era3_tag',
				'label'         => __('Era 3 - Tag / Date', 'knowledgism'),
				'name'          => 'era3_tag',
				'type'          => 'text',
				'default_value' => '21st century',
			],
			[
				'key'           => 'field_he_era3_title',
				'label'         => __('Era 3 - Title', 'knowledgism'),
				'name'          => 'era3_title',
				'type'          => 'text',
				'default_value' => 'The Knowledge Era',
			],
			[
				'key'           => 'field_he_era3_desc',
				'label'         => __('Era 3 - Description', 'knowledgism'),
				'name'          => 'era3_desc',
				'type'          => 'textarea',
				'rows'          => 2,
				'default_value' => 'Knowledge becomes the primary source of power — and the basis for survival.',
			],
		],
		'location' => [
			[
				[
					'param'    => 'block',
					'operator' => '==',
					'value'    => 'acf/home-eras',
				],
			],
		],
	]);

	// 3. Home - Featured Articles
	acf_add_local_field_group([
		'key'      => 'group_home_articles',
		'title'    => __('Block - Home Featured Articles', 'knowledgism'),
		'fields'   => [
			[
				'key'           => 'field_ha_section_title',
				'label'         => __('Section Title', 'knowledgism'),
				'name'          => 'section_title',
				'type'          => 'text',
				'default_value' => 'Articles by Alan C. Walter',
			],
			[
				'key'           => 'field_ha_button_text',
				'label'         => __('Button Text', 'knowledgism'),
				'name'          => 'button_text',
				'type'          => 'text',
				'default_value' => 'All ten categories',
			],
			[
				'key'           => 'field_ha_button_url',
				'label'         => __('Button URL', 'knowledgism'),
				'name'          => 'button_url',
				'type'          => 'text',
				'default_value' => '/articles/',
			],
		],
		'location' => [
			[
				[
					'param'    => 'block',
					'operator' => '==',
					'value'    => 'acf/home-articles',
				],
			],
		],
	]);

	// 4. Home - Carousel / Zone Assessment CTA (Regla 2 & 4: Accordion + 100% Dynamic)
	acf_add_local_field_group([
		'key'      => 'group_home_zone_cta',
		'title'    => __('Block - Home Carousel / Zone CTA', 'knowledgism'),
		'fields'   => [
			// Accordion Principal del Bloque (Regla 2)
			[
				'key'           => 'field_hz_accordion_main',
				'label'         => __('Carrusel / Zone Assessment CTA', 'knowledgism'),
				'name'          => 'accordion_main',
				'type'          => 'accordion',
				'open'          => 1,
				'multi_expand'  => 1,
				'endpoint'      => 0,
			],

			// Slide 1 Accordion
			[
				'key'           => 'field_hz_accordion_s1',
				'label'         => __('Slide 1: Mini Perfil (Personal Assessment)', 'knowledgism'),
				'name'          => 'accordion_s1',
				'type'          => 'accordion',
				'open'          => 1,
				'multi_expand'  => 1,
				'endpoint'      => 0,
			],
			[
				'key'           => 'field_hz_title',
				'label'         => __('Slide 1 - Title', 'knowledgism'),
				'name'          => 'title',
				'type'          => 'text',
				'default_value' => 'Not sure where to start?',
			],
			[
				'key'           => 'field_hz_desc',
				'label'         => __('Slide 1 - Description', 'knowledgism'),
				'name'          => 'description',
				'type'          => 'textarea',
				'rows'          => 3,
				'default_value' => 'Fourteen questions tell you which Zone you operate from, which of the three areas is holding you back, and which book to read first. Free, no card.',
			],
			[
				'key'           => 'field_hz_button_text',
				'label'         => __('Slide 1 - Button Text', 'knowledgism'),
				'name'          => 'button_text',
				'type'          => 'text',
				'default_value' => 'Find your Zone',
			],
			[
				'key'           => 'field_hz_button_url',
				'label'         => __('Slide 1 - Button URL', 'knowledgism'),
				'name'          => 'button_url',
				'type'          => 'text',
				'default_value' => 'https://app.zonesoflife.com/short-quiz/',
			],
			[
				'key'           => 'field_hz_subtext',
				'label'         => __('Slide 1 - Sub-label', 'knowledgism'),
				'name'          => 'subtext',
				'type'          => 'text',
				'default_value' => 'Name and email only',
			],

			// Slide 2 Accordion
			[
				'key'           => 'field_hz_accordion_s2',
				'label'         => __('Slide 2: Libro The Fire Within', 'knowledgism'),
				'name'          => 'accordion_s2',
				'type'          => 'accordion',
				'open'          => 0,
				'multi_expand'  => 1,
				'endpoint'      => 0,
			],
			[
				'key'           => 'field_hz_s2_kicker',
				'label'         => __('Slide 2 - Kicker', 'knowledgism'),
				'name'          => 's2_kicker',
				'type'          => 'text',
				'default_value' => 'Bestseller · By Ceil Stanford & Jeff Crippen',
			],
			[
				'key'           => 'field_hz_s2_title',
				'label'         => __('Slide 2 - Title', 'knowledgism'),
				'name'          => 's2_title',
				'type'          => 'text',
				'default_value' => 'The Fire Within',
			],
			[
				'key'           => 'field_hz_s2_desc',
				'label'         => __('Slide 2 - Description', 'knowledgism'),
				'name'          => 's2_desc',
				'type'          => 'textarea',
				'rows'          => 3,
				'default_value' => 'Stop feeling stuck, unlock your inner fire, and create a life you genuinely enjoy living with the Five Pillars of Power. Foreword by Robert & Kim Kiyosaki.',
			],
			[
				'key'           => 'field_hz_s2_badge',
				'label'         => __('Slide 2 - Price / Badge', 'knowledgism'),
				'name'          => 's2_badge',
				'type'          => 'text',
				'default_value' => '$5.00 E-Book',
			],
			[
				'key'           => 'field_hz_s2_btn_text',
				'label'         => __('Slide 2 - Button Text', 'knowledgism'),
				'name'          => 's2_btn_text',
				'type'          => 'text',
				'default_value' => 'Get The Book — $5',
			],
			[
				'key'           => 'field_hz_s2_btn_url',
				'label'         => __('Slide 2 - Button URL', 'knowledgism'),
				'name'          => 's2_btn_url',
				'type'          => 'text',
				'default_value' => '/product/the-fire-within/',
			],
			[
				'key'           => 'field_hz_s2_image',
				'label'         => __('Slide 2 - Cover Image', 'knowledgism'),
				'name'          => 's2_image',
				'type'          => 'image',
				'return_format' => 'url',
				'preview_size'  => 'medium',
			],

			// Slide 3 Accordion
			[
				'key'           => 'field_hz_accordion_s3',
				'label'         => __('Slide 3: Libro The Zone Series (Pirámide)', 'knowledgism'),
				'name'          => 'accordion_s3',
				'type'          => 'accordion',
				'open'          => 0,
				'multi_expand'  => 1,
				'endpoint'      => 0,
			],
			[
				'key'           => 'field_hz_s3_kicker',
				'label'         => __('Slide 3 - Kicker', 'knowledgism'),
				'name'          => 's3_kicker',
				'type'          => 'text',
				'default_value' => 'Foundational Work · By Alan C. Walter',
			],
			[
				'key'           => 'field_hz_s3_title',
				'label'         => __('Slide 3 - Title', 'knowledgism'),
				'name'          => 's3_title',
				'type'          => 'text',
				'default_value' => 'The Zone Series',
			],
			[
				'key'           => 'field_hz_s3_desc',
				'label'         => __('Slide 3 - Description', 'knowledgism'),
				'name'          => 's3_desc',
				'type'          => 'textarea',
				'rows'          => 3,
				'default_value' => 'The definitive book featuring the 4 Zones of Life pyramid (Red, Yellow, Green, and Gold). Learn the indicators of truth, mastery, and living at your highest ability.',
			],
			[
				'key'           => 'field_hz_s3_badge',
				'label'         => __('Slide 3 - Price / Badge', 'knowledgism'),
				'name'          => 's3_badge',
				'type'          => 'text',
				'default_value' => '$27.00 E-Book',
			],
			[
				'key'           => 'field_hz_s3_btn_text',
				'label'         => __('Slide 3 - Button Text', 'knowledgism'),
				'name'          => 's3_btn_text',
				'type'          => 'text',
				'default_value' => 'Explore The Zone Series',
			],
			[
				'key'           => 'field_hz_s3_btn_url',
				'label'         => __('Slide 3 - Button URL', 'knowledgism'),
				'name'          => 's3_btn_url',
				'type'          => 'text',
				'default_value' => '/product/the-zone-series/',
			],
			[
				'key'           => 'field_hz_s3_image',
				'label'         => __('Slide 3 - Cover Image', 'knowledgism'),
				'name'          => 's3_image',
				'type'          => 'image',
				'return_format' => 'url',
				'preview_size'  => 'medium',
			],

			// Slide 4 Accordion
			[
				'key'           => 'field_hz_accordion_s4',
				'label'         => __('Slide 4: Catálogo Completo (Bookstore)', 'knowledgism'),
				'name'          => 'accordion_s4',
				'type'          => 'accordion',
				'open'          => 0,
				'multi_expand'  => 1,
				'endpoint'      => 0,
			],
			[
				'key'           => 'field_hz_s4_kicker',
				'label'         => __('Slide 4 - Kicker', 'knowledgism'),
				'name'          => 's4_kicker',
				'type'          => 'text',
				'default_value' => 'The Complete Catalog · 50+ Titles',
			],
			[
				'key'           => 'field_hz_s4_title',
				'label'         => __('Slide 4 - Title', 'knowledgism'),
				'name'          => 's4_title',
				'type'          => 'text',
				'default_value' => 'Knowledgism Bookstore',
			],
			[
				'key'           => 'field_hz_s4_desc',
				'label'         => __('Slide 4 - Description', 'knowledgism'),
				'name'          => 's4_desc',
				'type'          => 'textarea',
				'rows'          => 3,
				'default_value' => 'The complete published written works and courses of Alan C. Walter across eleven categories. Acquired, downloaded, and read on your own.',
			],
			[
				'key'           => 'field_hz_s4_badge',
				'label'         => __('Slide 4 - Badge', 'knowledgism'),
				'name'          => 's4_badge',
				'type'          => 'text',
				'default_value' => 'Eleven Categories · Instant Download',
			],
			[
				'key'           => 'field_hz_s4_btn_text',
				'label'         => __('Slide 4 - Button Text', 'knowledgism'),
				'name'          => 's4_btn_text',
				'type'          => 'text',
				'default_value' => 'Browse All Books',
			],
			[
				'key'           => 'field_hz_s4_btn_url',
				'label'         => __('Slide 4 - Button URL', 'knowledgism'),
				'name'          => 's4_btn_url',
				'type'          => 'text',
				'default_value' => '/bookstore/',
			],

			// Settings Accordion
			[
				'key'           => 'field_hz_accordion_settings',
				'label'         => __('Configuración del Carrusel', 'knowledgism'),
				'name'          => 'accordion_settings',
				'type'          => 'accordion',
				'open'          => 0,
				'multi_expand'  => 1,
				'endpoint'      => 0,
			],
			[
				'key'           => 'field_hz_autoplay',
				'label'         => __('Activar Autoplay', 'knowledgism'),
				'name'          => 'carousel_autoplay',
				'type'          => 'true_false',
				'default_value' => 1,
				'ui'            => 1,
			],
			[
				'key'           => 'field_hz_delay',
				'label'         => __('Intervalo de Transición (Milisegundos)', 'knowledgism'),
				'name'          => 'carousel_delay',
				'type'          => 'number',
				'default_value' => 6000,
				'min'           => 2000,
				'max'           => 20000,
				'step'          => 500,
			],
			[
				'key'           => 'field_hz_accordion_end',
				'label'         => '',
				'name'          => 'accordion_end',
				'type'          => 'accordion',
				'endpoint'      => 1,
			],
		],
		'location' => [
			[
				[
					'param'    => 'block',
					'operator' => '==',
					'value'    => 'acf/home-zone-cta',
				],
			],
		],
	]);

	// 5. Home - Word of the Week Newsletter
	acf_add_local_field_group([
		'key'      => 'group_home_newsletter',
		'title'    => __('Block - Home Newsletter', 'knowledgism'),
		'fields'   => [
			[
				'key'           => 'field_hn_kicker',
				'label'         => __('Kicker', 'knowledgism'),
				'name'          => 'kicker',
				'type'          => 'text',
				'default_value' => 'Free weekly',
			],
			[
				'key'           => 'field_hn_title',
				'label'         => __('Title', 'knowledgism'),
				'name'          => 'title',
				'type'          => 'text',
				'default_value' => 'The Word of the Week',
			],
			[
				'key'           => 'field_hn_desc',
				'label'         => __('Description', 'knowledgism'),
				'name'          => 'description',
				'type'          => 'textarea',
				'rows'          => 3,
				'default_value' => 'One term from the Knowledgism Dictionary, defined plainly, every Monday. Plus new articles and event dates before they are announced.',
			],
			[
				'key'           => 'field_hn_placeholder',
				'label'         => __('Input Placeholder', 'knowledgism'),
				'name'          => 'placeholder',
				'type'          => 'text',
				'default_value' => 'jane@example.com',
			],
			[
				'key'           => 'field_hn_button_text',
				'label'         => __('Button Text', 'knowledgism'),
				'name'          => 'button_text',
				'type'          => 'text',
				'default_value' => 'Subscribe',
			],
			[
				'key'           => 'field_hn_consent_text',
				'label'         => __('Consent Text', 'knowledgism'),
				'name'          => 'consent_text',
				'type'          => 'text',
				'default_value' => 'I agree to the Privacy Policy. Unsubscribe anytime.',
			],
		],
		'location' => [
			[
				[
					'param'    => 'block',
					'operator' => '==',
					'value'    => 'acf/home-newsletter',
				],
			],
		],
	]);

	// 6. Glossary Block
	acf_add_local_field_group([
		'key'      => 'group_glossary',
		'title'    => __('Block - Glossary / Glosario', 'knowledgism'),
		'fields'   => [
			[
				'key'           => 'field_gl_kicker',
				'label'         => __('Kicker', 'knowledgism'),
				'name'          => 'kicker',
				'type'          => 'text',
				'default_value' => 'Terminology & Principles',
			],
			[
				'key'           => 'field_gl_title',
				'label'         => __('Title', 'knowledgism'),
				'name'          => 'title',
				'type'          => 'text',
				'default_value' => 'Glossary of Knowledgism',
			],
			[
				'key'           => 'field_gl_lede',
				'label'         => __('Lede / Description', 'knowledgism'),
				'name'          => 'lede',
				'type'          => 'textarea',
				'rows'          => 3,
				'default_value' => 'Key definitions, operating principles, and distinctions formulated by Alan C. Walter.',
			],
			[
				'key'           => 'field_gl_show_search',
				'label'         => __('Show Real-Time Search Box', 'knowledgism'),
				'name'          => 'show_search',
				'type'          => 'true_false',
				'default_value' => 1,
				'ui'            => 1,
			],
			[
				'key'           => 'field_gl_show_alphabet_nav',
				'label'         => __('Show Alphabet Navigation Bar (A-Z)', 'knowledgism'),
				'name'          => 'show_alphabet_nav',
				'type'          => 'true_false',
				'default_value' => 1,
				'ui'            => 1,
			],
			[
				'key'          => 'field_gl_terms',
				'label'        => __('Glossary Words & Meanings', 'knowledgism'),
				'name'         => 'terms',
				'type'         => 'repeater',
				'instructions' => __('Add words and their definitions. They will be sorted and grouped automatically by their first letter (A-Z).', 'knowledgism'),
				'button_label' => __('Add Word / Meaning', 'knowledgism'),
				'layout'       => 'block',
				'sub_fields'   => [
					[
						'key'           => 'field_gl_term_word',
						'label'         => __('Word / Term', 'knowledgism'),
						'name'          => 'word',
						'type'          => 'text',
						'required'      => 1,
						'placeholder'   => 'e.g. Life Force, Presence, Charge...',
					],
					[
						'key'           => 'field_gl_term_meaning',
						'label'         => __('Meaning / Definition', 'knowledgism'),
						'name'          => 'meaning',
						'type'          => 'textarea',
						'required'      => 1,
						'rows'          => 4,
						'placeholder'   => 'Write the definition or meaning here...',
					],
					[
						'key'           => 'field_gl_term_zone',
						'label'         => __('Associated Zone (Optional)', 'knowledgism'),
						'name'          => 'zone',
						'type'          => 'select',
						'choices'       => [
							''      => __('General / No Zone', 'knowledgism'),
							'red'   => __('Red Zone', 'knowledgism'),
							'amber' => __('Yellow Zone', 'knowledgism'),
							'green' => __('Green Zone', 'knowledgism'),
							'gold'  => __('Gold Zone', 'knowledgism'),
						],
						'default_value' => '',
						'allow_null'    => 1,
					],
					[
						'key'           => 'field_gl_term_notes',
						'label'         => __('Context / Reference (Optional)', 'knowledgism'),
						'name'          => 'notes',
						'type'          => 'text',
						'placeholder'   => 'e.g. Formulated in 1994, Foundations Vol. 1',
					],
				],
			],
		],
		'location' => [
			[
				[
					'param'    => 'block',
					'operator' => '==',
					'value'    => 'acf/glossary',
				],
			],
		],
	]);
}

if (function_exists('acf_add_local_field_group')) {
	add_action('acf/init', 'knowledgism_register_home_block_field_groups');
}
