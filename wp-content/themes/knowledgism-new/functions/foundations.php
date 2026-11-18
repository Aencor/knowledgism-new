<?php
/**
 * Foundations Pages & AJAX Support
 * Handles lateral menu navigation, AJAX loading, and Flatsome shortcode compatibility.
 *
 * @package Knowledgism
 */

// 1. Flatsome [title] shortcode compatibility
function knowledgism_flatsome_title_shortcode($atts, $content = '') {
	$a = shortcode_atts([
		'text'  => '',
		'style' => 'center',
	], $atts);

	$text = !empty($a['text']) ? $a['text'] : $content;
	$align = ($a['style'] === 'center') ? 'center' : 'left';

	return '<h2 class="title-block mt32 mb16" style="text-align:' . esc_attr($align) . ';font-family:var(--display);">' . esc_html($text) . '</h2>';
}
add_shortcode('title', 'knowledgism_flatsome_title_shortcode');

// 2. Helper to get foundations menu structure
function knowledgism_get_foundations_menu() {
	return [
		['slug' => 'what-is-knowledgism', 'id' => 12, 'title' => __('What Is Knowledgism', 'knowledgism')],
		['slug' => 'the-knowledge-era-is-upon-us', 'id' => 8, 'title' => __('The Knowledge Era', 'knowledgism')],
		['slug' => 'the-rules-of-knowledge', 'id' => 79, 'title' => __('The Rules of Knowledge', 'knowledgism')],
		['slug' => 'what-is-a-knowledgist', 'id' => 74, 'title' => __('What Is a Knowledgist', 'knowledgism')],
		['slug' => 'the-knowledge-ladder-of-society', 'id' => 69, 'title' => __('The Knowledge Ladder', 'knowledgism')],
		['slug' => 'meet-alan-c-walter', 'id' => 87, 'title' => __('Meet Alan C. Walter', 'knowledgism')],
		['slug' => 'the-knowledgism-project', 'id' => 163, 'title' => __('The Knowledgism Project', 'knowledgism')],
		['slug' => 'about', 'id' => 6, 'title' => __('About', 'knowledgism')],
	];
}

// 3. Helper to determine if a post is in the foundations section
function knowledgism_is_foundations_page($post_obj = null) {
	if (!$post_obj) {
		$post_obj = get_queried_object();
	}
	if (!$post_obj || !isset($post_obj->ID)) {
		return false;
	}
	if ($post_obj->ID === 6 || $post_obj->post_parent === 6) {
		return true;
	}
	$menu = knowledgism_get_foundations_menu();
	$slugs = wp_list_pluck($menu, 'slug');
	return in_array($post_obj->post_name, $slugs, true);
}

// 4. AJAX endpoint for Foundations pages
function knowledgism_ajax_foundations_handler() {
	if (isset($_GET['ajax_foundations']) && $_GET['ajax_foundations'] == '1') {
		$page_id = isset($_GET['page_id']) ? intval($_GET['page_id']) : 0;
		$slug    = isset($_GET['slug']) ? sanitize_text_field($_GET['slug']) : '';

		$target_post = null;

		if ($page_id > 0) {
			$target_post = get_post($page_id);
		} elseif (!empty($slug)) {
			$target_post = get_page_by_path('about/' . $slug);
			if (!$target_post) {
				$target_post = get_page_by_path($slug);
			}
		}

		if (!$target_post && get_queried_object_id()) {
			$target_post = get_post(get_queried_object_id());
		}

		if (!$target_post && have_posts()) {
			global $post;
			$target_post = $post;
		}

		if ($target_post && $target_post->post_status === 'publish') {
			setup_postdata($target_post);
			$raw_content = $target_post->post_content;
			$content = apply_filters('the_content', $raw_content);
			$title   = get_the_title($target_post->ID);
			$url     = get_permalink($target_post->ID);
			$slug    = $target_post->post_name;

			// Hero title matching prototype
			$hero_title = $title;
			if (in_array($slug, ['what-is-knowledgism', 'about'])) {
				$hero_title = __('The game being played is knowledge versus ignorance', 'knowledgism');
			}

			// Add the 3 paradigms card if slug is what-is-knowledgism or about
			$paradigms_html = '';
			if (in_array($slug, ['what-is-knowledgism', 'about'])) {
				$paradigms_html = '
				<div class="card tint mt32 mb32">
				  <div class="tiny mb16">' . esc_html__('Eight thousand years, three paradigms', 'knowledgism') . '</div>
				  <div class="g3">
				    <div>
				      <div class="tiny" style="color:var(--red)">' . esc_html__('Warrior Era', 'knowledgism') . '</div>
				      <p class="xs mt8">' . esc_html__('Conquest, domination and compliance as the prevailing paradigm.', 'knowledgism') . '</p>
				    </div>
				    <div>
				      <div class="tiny" style="color:#B98F0C">' . esc_html__('Literacy Era', 'knowledgism') . '</div>
				      <p class="xs mt8">' . esc_html__('Reading, writing and arithmetic spread almost worldwide. Survival needs met for most.', 'knowledgism') . '</p>
				    </div>
				    <div>
				      <div class="tiny" style="color:var(--green)">' . esc_html__('Knowledge Era', 'knowledgism') . '</div>
				      <p class="xs mt8">' . esc_html__('Recorded skill and smart machines change what you must know and be to keep winning.', 'knowledgism') . '</p>
				    </div>
				  </div>
				</div>';
			}

			wp_reset_postdata();

			wp_send_json_success([
				'id'             => $target_post->ID,
				'title'          => $title,
				'hero_title'     => $hero_title,
				'slug'           => $slug,
				'url'            => $url,
				'content'        => $content,
				'paradigms_html' => $paradigms_html,
			]);
		} else {
			wp_send_json_error(['message' => 'Page not found']);
		}
	}
}
add_action('template_redirect', 'knowledgism_ajax_foundations_handler', 5);
