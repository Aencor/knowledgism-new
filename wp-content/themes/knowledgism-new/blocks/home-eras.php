<?php
/**
 * Block Name: Home Eras of Knowledge
 * Slug: home-eras
 * Description: Knowledgism Foundations / Eras block showing Warrior Era, Literacy Era, and Knowledge Era.
 *
 * @package Knowledgism
 */

$block_id = !empty($block['anchor']) ? $block['anchor'] : 'home-eras-' . ($block['id'] ?? uniqid());
$block_class = 'home-eras-block';
if (!empty($block['className'])) {
  $block_class .= ' ' . $block['className'];
}

$section_title = get_field('section_title') ?: __('The game is knowledge versus ignorance', 'knowledgism');
$button_text   = get_field('button_text') ?: __('Read the foundations', 'knowledgism');
$button_url    = get_field('button_url') ?: home_url('/about/');

// Card 1
$era1_tag   = get_field('era1_tag') ?: '6000 BC — 400 AD';
$era1_title = get_field('era1_title') ?: __('The Warrior Era', 'knowledgism');
$era1_desc  = get_field('era1_desc') ?: __('Nations and relationships were conquered, dominated and forced to comply.', 'knowledgism');

// Card 2
$era2_tag   = get_field('era2_tag') ?: '1000 AD — today';
$era2_title = get_field('era2_title') ?: __('The Literacy Era', 'knowledgism');
$era2_desc  = get_field('era2_desc') ?: __('A near-worldwide push to read, write and reckon. Lifespan almost doubled.', 'knowledgism');

// Card 3
$era3_tag   = get_field('era3_tag') ?: '21st century';
$era3_title = get_field('era3_title') ?: __('The Knowledge Era', 'knowledgism');
$era3_desc  = get_field('era3_desc') ?: __('Knowledge becomes the primary source of power — and the basis for survival.', 'knowledgism');
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
      <!-- Card 1: Warrior Era -->
      <div class="card">
        <div class="iconbox mb16">
          <svg class="ic" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><use href="#i-shield"/></svg>
        </div>
        <div class="tiny" style="color:var(--red)"><?php echo esc_html($era1_tag); ?></div>
        <h4 class="mt8"><?php echo esc_html($era1_title); ?></h4>
        <p class="sm mt8"><?php echo esc_html($era1_desc); ?></p>
      </div>

      <!-- Card 2: Literacy Era -->
      <div class="card">
        <div class="iconbox gold mb16">
          <svg class="ic" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><use href="#i-book"/></svg>
        </div>
        <div class="tiny" style="color:#B98F0C"><?php echo esc_html($era2_tag); ?></div>
        <h4 class="mt8"><?php echo esc_html($era2_title); ?></h4>
        <p class="sm mt8"><?php echo esc_html($era2_desc); ?></p>
      </div>

      <!-- Card 3: Knowledge Era -->
      <div class="card">
        <div class="iconbox green mb16">
          <svg class="ic" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><use href="#i-spark"/></svg>
        </div>
        <div class="tiny" style="color:var(--green)"><?php echo esc_html($era3_tag); ?></div>
        <h4 class="mt8"><?php echo esc_html($era3_title); ?></h4>
        <p class="sm mt8"><?php echo esc_html($era3_desc); ?></p>
      </div>
    </div>
  </div>
</div>
