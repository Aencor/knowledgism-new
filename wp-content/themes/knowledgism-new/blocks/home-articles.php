<?php
/**
 * Block Name: Home Featured Articles
 * Slug: home-articles
 * Description: Knowledgism Featured Articles block showing 3 category cards from the prototype.
 *
 * @package Knowledgism
 */

$block_id = !empty($block['anchor']) ? $block['anchor'] : 'home-articles-' . ($block['id'] ?? uniqid());
$block_class = 'home-articles-block';
if (!empty($block['className'])) {
  $block_class .= ' ' . $block['className'];
}

$is_es = function_exists('knowledgism_is_spanish') && knowledgism_is_spanish();
$default_title = $is_es ? 'Boletines de Alan' : 'Bulletins by Alan';
$default_btn   = $is_es ? 'Las diez categorías' : 'All ten categories';
$default_url   = function_exists('knowledgism_get_nav_url') ? knowledgism_get_nav_url('articles') : home_url('/articles/');

$section_title = get_field('field_ha_section_title');
if (empty($section_title) || in_array($section_title, ['The game is knowledge versus ignorance', 'El juego es conocimiento contra ignorancia', 'Articles by Alan C. Walter', 'Artículos de Alan C. Walter'], true)) {
  $section_title = $default_title;
}

$button_text = get_field('field_ha_button_text');
if (empty($button_text) || $button_text === 'Lee los fundamentos' || $button_text === 'Read the foundations') {
  $button_text = $default_btn;
}

$button_url = get_field('field_ha_button_url');
if (empty($button_url) || strpos($button_url, 'about') !== false) {
  $button_url = $default_url;
}

// Query 3 published articles if available, or fallback to the 3 canonical prototype articles
$db_posts = get_posts([
  'post_type'      => 'post',
  'posts_per_page' => 3,
  'post_status'    => 'publish',
]);

$articles = [];
if (!empty($db_posts)) {
  foreach ($db_posts as $p) {
    $cats = wp_get_post_categories($p->ID, ['fields' => 'names']);
    $cat_name = !empty($cats) ? $cats[0] : __('Articles', 'knowledgism');
    $excerpt = !empty($p->post_excerpt) ? $p->post_excerpt : wp_trim_words($p->post_content, 14);

    $articles[] = [
      'category' => $cat_name,
      'title'    => $p->post_title,
      'excerpt'  => $excerpt,
      'url'      => get_permalink($p->ID),
    ];
  }
}

// Fallback to prototype canonical articles if none in DB
if (empty($articles)) {
  $articles = [
    [
      'category' => 'The Zones of Life',
      'title'    => 'The Zones and Your Will',
      'excerpt'  => 'Your Zone describes where you are choosing from, not only what you choose.',
      'url'      => home_url('/articles/'),
    ],
    [
      'category' => 'Mastery',
      'title'    => 'The Prime Targets of Great People',
      'excerpt'  => 'What great people aim at, and why the target itself is the skill.',
      'url'      => home_url('/articles/'),
    ],
    [
      'category' => 'Riches',
      'title'    => 'The Source Points of Getting Rich',
      'excerpt'  => 'Where wealth actually originates before it shows up as money.',
      'url'      => home_url('/articles/'),
    ],
  ];
}
?>

<div id="<?php echo esc_attr($block_id); ?>" class="block <?php echo esc_attr($block_class); ?>">
  <div class="wrap">
    <div class="hgroup mb24" style="justify-content:space-between;flex-wrap:wrap;gap:12px">
      <h2><?php echo esc_html($section_title); ?></h2>
      <a class="btn btn-ghost btn-sm" href="<?php echo esc_url($button_url); ?>">
        <?php echo esc_html($button_text); ?>
      </a>
    </div>
    <div class="g3">
      <?php foreach ($articles as $art) : ?>
        <a class="card card-link" href="<?php echo esc_url($art['url']); ?>">
          <div class="tiny" style="color:var(--green)"><?php echo esc_html($art['category']); ?></div>
          <h4 class="mt8"><?php echo esc_html($art['title']); ?></h4>
          <p class="sm mt8"><?php echo esc_html($art['excerpt']); ?></p>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>
