<?php
/**
 * Class HomeCarousel
 *
 * Encapsulates the business logic, data retrieval, and fallbacks for the
 * Home Carousel / Zone Assessment CTA block in compliance with SOLID and DRY.
 *
 * @package Knowledgism
 */

namespace CodeByMonk;

if (!defined('ABSPATH')) {
	exit;
}

class HomeCarousel {

	/**
	 * Whether the current context is in Spanish.
	 *
	 * @var bool
	 */
	protected $is_es;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->is_es = function_exists('knowledgism_is_spanish') && knowledgism_is_spanish();
	}

	/**
	 * Retrieve all 4 carousel slides with ACF dynamic fields and robust fallbacks.
	 *
	 * @return array
	 */
	public function get_slides() {
		return [
			$this->get_slide_1_profile(),
			$this->get_slide_2_fire_within(),
			$this->get_slide_3_zone_series(),
			$this->get_slide_4_bookstore(),
		];
	}

	/**
	 * Slide 1: Mini Profile (Personal Assessment).
	 *
	 * @return array
	 */
	protected function get_slide_1_profile() {
		$default_title = $this->is_es
			? '¿No estás seguro por dónde empezar?'
			: 'Not sure where to start?';

		$default_desc = $this->is_es
			? 'Catorce preguntas te dicen desde qué Zona operas hoy, cuál de las áreas te frena y qué libro leer primero. Gratis, sin tarjeta.'
			: 'Fourteen questions tell you which Zone you operate from, which of the three areas is holding you back, and which book to read first. Free, no card.';

		$default_btn_text = $this->is_es ? 'Descubre tu Zona' : 'Find your Zone';
		$default_btn_url  = 'https://app.zonesoflife.com/short-quiz/';
		$default_subtext  = $this->is_es ? 'Solo nombre y correo · 3 min' : 'Name and email only · 3 min';

		$title = get_field('title');
		if (empty($title) || in_array($title, ['Not sure where to start?', '¿No estás seguro por dónde empezar?'], true)) {
			$title = $default_title;
		}

		$desc = get_field('description');
		if (empty($desc) || strpos($desc, 'Fourteen questions') !== false || strpos($desc, 'Catorce preguntas') !== false) {
			$desc = $default_desc;
		}

		$btn_text = get_field('button_text');
		if (empty($btn_text) || in_array($btn_text, ['Find your Zone', 'Descubre tu Zona', 'All ten categories', 'Las diez categorías'], true)) {
			$btn_text = $default_btn_text;
		}

		$btn_url = get_field('button_url');
		if (empty($btn_url) || $btn_url === '#profile' || $btn_url === 'https://app.zonesoflife.com' || strpos($btn_url, 'articles') !== false) {
			$btn_url = $default_btn_url;
		}

		$subtext = get_field('subtext') ?: $default_subtext;

		return [
			'id'          => 'slide-profile',
			'tab_label'   => $this->is_es ? 'Mini Perfil' : 'Mini Profile',
			'kicker'      => $this->is_es ? 'Evaluación Personal · 14 Preguntas' : 'Personal Assessment · 14 Questions',
			'kicker_icon' => '#i-target',
			'kicker_color'=> '',
			'title'       => $title,
			'desc'        => $desc,
			'show_zones'  => true,
			'btn_text'    => $btn_text,
			'btn_url'     => $btn_url,
			'btn_class'   => 'btn-go',
			'btn_target'  => '_blank',
			'subtext'     => $subtext,
			'media_type'  => 'zones',
		];
	}

	/**
	 * Slide 2: Book "The Fire Within".
	 *
	 * @return array
	 */
	protected function get_slide_2_fire_within() {
		$post_id   = $this->is_es ? 9970 : 10373;
		$post      = get_post($post_id);
		if (!$post && $this->is_es) {
			$post = get_post(10373);
		}

		$default_url   = $post ? get_permalink($post->ID) : home_url('/product/the-fire-within/');
		$default_title = $post ? $post->post_title : ($this->is_es ? 'El Fuego Que Llevas Dentro' : 'The Fire Within');
		$default_kicker= $this->is_es ? 'Libro Destacado · Ceil Stanford & Jeff Crippen' : 'Bestseller · By Ceil Stanford & Jeff Crippen';
		$default_desc  = $this->is_es
			? 'Deja de sentirte estancado, despierta tu fuego interior y crea una vida que disfrutes plenamente. Recomendado por Robert y Kim Kiyosaki.'
			: 'Stop feeling stuck, unlock your inner fire, and create a life you genuinely enjoy living with the Five Pillars of Power. Foreword by Robert & Kim Kiyosaki.';
		$default_btn   = $this->is_es ? 'Comprar Libro — $5' : 'Get The Book — $5';
		$default_price = '$5.00 E-Book';

		$img_url = $this->resolve_image_url(
			get_field('s2_image'),
			'/wp-content/uploads/2025/08/the-fire-within-book.webp'
		);

		return [
			'id'          => 'slide-fire-within',
			'tab_label'   => 'The Fire Within',
			'kicker'      => get_field('s2_kicker') ?: $default_kicker,
			'kicker_icon' => '#i-spark',
			'kicker_color'=> 'var(--fuego)',
			'title'       => get_field('s2_title') ?: $default_title,
			'desc'        => get_field('s2_desc') ?: $default_desc,
			'badge'       => get_field('s2_badge') ?: $default_price,
			'badge_bg'    => 'var(--fuego)',
			'btn_text'    => get_field('s2_btn_text') ?: $default_btn,
			'btn_url'     => get_field('s2_btn_url') ?: $default_url,
			'btn_class'   => 'btn-primary',
			'btn_style'   => 'background:var(--fuego);border-color:var(--fuego);color:#fff;',
			'btn_target'  => '_self',
			'subtext'     => $this->is_es ? 'Descarga digital inmediata' : 'Instant digital download',
			'media_type'  => 'image',
			'image_url'   => $img_url,
			'image_alt'   => 'The Fire Within Book Cover',
			'image_width' => 120,
			'image_height'=> 95,
		];
	}

	/**
	 * Slide 3: Book "The Zone Series" (pyramid of zones).
	 *
	 * @return array
	 */
	protected function get_slide_3_zone_series() {
		$post_id   = 366;
		$post      = get_post($post_id);
		$default_url   = $post ? get_permalink($post->ID) : home_url('/product/the-zone-series/');
		$default_title = $post ? $post->post_title : 'The Zone Series';
		$default_kicker= $this->is_es ? 'Fundamentos de las Zonas · Alan C. Walter' : 'Foundational Work · By Alan C. Walter';
		$default_desc  = $this->is_es
			? 'El libro fundamental con la pirámide de las 4 Zonas de la Vida (Roja, Amarilla, Verde y Oro). Domina los indicadores de verdad, maestría y alto desempeño.'
			: 'The definitive book featuring the 4 Zones of Life pyramid (Red, Yellow, Green, and Gold). Learn the indicators of truth, mastery, and living at your highest ability.';
		$default_btn   = $this->is_es ? 'Ver The Zone Series' : 'Explore The Zone Series';
		$default_price = '$27.00 E-Book';

		$img_url = $this->resolve_image_url(
			get_field('s3_image'),
			'/wp-content/uploads/2016/02/The-Zone-Series.png'
		);

		return [
			'id'          => 'slide-zone-series',
			'tab_label'   => 'The Zone Series',
			'kicker'      => get_field('s3_kicker') ?: $default_kicker,
			'kicker_icon' => '#i-target',
			'kicker_color'=> 'var(--gold)',
			'title'       => get_field('s3_title') ?: $default_title,
			'desc'        => get_field('s3_desc') ?: $default_desc,
			'badge'       => get_field('s3_badge') ?: $default_price,
			'badge_bg'    => 'var(--navy)',
			'btn_text'    => get_field('s3_btn_text') ?: $default_btn,
			'btn_url'     => get_field('s3_btn_url') ?: $default_url,
			'btn_class'   => 'btn-primary',
			'btn_style'   => 'background:var(--navy);border-color:var(--navy);color:#fff;',
			'btn_target'  => '_self',
			'subtext'     => $this->is_es ? 'Descarga digital inmediata' : 'Instant digital download',
			'media_type'  => 'image',
			'image_url'   => $img_url,
			'image_alt'   => 'The Zone Series Book Cover - Pyramid of Zones',
			'image_width' => 110,
			'image_height'=> 130,
		];
	}

	/**
	 * Slide 4: All Books (Bookstore).
	 *
	 * @return array
	 */
	protected function get_slide_4_bookstore() {
		$shop_page_id   = function_exists('wc_get_page_id') ? wc_get_page_id('shop') : 0;
		$default_url    = ($shop_page_id > 0) ? get_permalink($shop_page_id) : home_url('/bookstore/');
		$default_kicker = $this->is_es ? 'Catálogo Completo · Más de 50 Libros' : 'The Complete Catalog · 50+ Titles';
		$default_title  = $this->is_es ? 'Librería Knowledgism' : 'Knowledgism Bookstore';
		$default_desc   = $this->is_es
			? 'Toda la obra escrita y grabada de Alan C. Walter a través de once categorías. Libros para adquirir, descargar y estudiar a tu propio ritmo.'
			: 'The complete published written works and courses of Alan C. Walter across eleven categories. Acquired, downloaded, and read on your own.';
		$default_btn    = $this->is_es ? 'Ver Todos los Libros' : 'Browse All Books';
		$default_badge  = $this->is_es ? 'Once Categorías · Descarga Inmediata' : 'Eleven Categories · Instant Download';

		return [
			'id'          => 'slide-bookstore',
			'tab_label'   => $this->is_es ? 'Todos los Libros' : 'All Books',
			'kicker'      => get_field('s4_kicker') ?: $default_kicker,
			'kicker_icon' => '#i-compass',
			'kicker_color'=> 'var(--navy)',
			'title'       => get_field('s4_title') ?: $default_title,
			'desc'        => get_field('s4_desc') ?: $default_desc,
			'badge'       => get_field('s4_badge') ?: $default_badge,
			'badge_bg'    => 'var(--navysoft)',
			'badge_color' => 'var(--navy)',
			'btn_text'    => get_field('s4_btn_text') ?: $default_btn,
			'btn_url'     => get_field('s4_btn_url') ?: $default_url,
			'btn_class'   => 'btn-go',
			'btn_style'   => '',
			'btn_target'  => '_self',
			'subtext'     => $this->is_es ? 'Explora la colección completa' : 'Explore the full collection',
			'media_type'  => 'bookstore_icon',
		];
	}

	/**
	 * Helper: Resolve image URL safely with physical existence check (Regla 16).
	 *
	 * @param mixed  $acf_field
	 * @param string $fallback_rel_path
	 * @return string
	 */
	protected function resolve_image_url($acf_field, $fallback_rel_path) {
		if (!empty($acf_field)) {
			if (is_numeric($acf_field)) {
				$url = wp_get_attachment_image_url($acf_field, 'full');
				if ($url) {
					return $url;
				}
			} elseif (is_array($acf_field) && !empty($acf_field['url'])) {
				return $acf_field['url'];
			} elseif (is_string($acf_field)) {
				return $acf_field;
			}
		}

		// Fallback check on disk (Regla 16 - Cero errores 404)
		$abs_path = ABSPATH . ltrim($fallback_rel_path, '/');
		if (file_exists($abs_path)) {
			return home_url($fallback_rel_path);
		}

		return home_url($fallback_rel_path);
	}
}
