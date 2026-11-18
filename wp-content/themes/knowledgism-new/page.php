<?php
/**
 * The template for displaying all pages.
 * Styled to match the Knowledgism Prototype simple page layout.
 *
 * @package Knowledgism
 */

if (function_exists('knowledgism_is_foundations_page') && knowledgism_is_foundations_page()) {
	include locate_template('template-foundations.php');
	return;
}

get_header(); ?>

<?php if (is_front_page()) : ?>
  <main class="global-main site-content">
    <?php while (have_posts()) : the_post(); ?>
      <div class="entry-content">
        <?php the_content(); ?>
      </div>
    <?php endwhile; ?>
  </main>
<?php else : ?>
  <?php while (have_posts()) : the_post(); 
    // Determine kicker: custom field, parent title, or section
    $kicker = get_post_meta(get_the_ID(), 'kicker', true);
    if (empty($kicker)) {
      if ($post->post_parent) {
        $kicker = get_the_title($post->post_parent);
      } elseif (is_page(['privacy-policy', 'terms-of-use', 'cookie-policy', 'cookies', 'terms'])) {
        $kicker = __('Legal', 'knowledgism');
      } elseif (is_page(['about', 'what-is-knowledgism', 'meet-alan-c-walter', 'the-rules-of-knowledge', 'the-knowledge-ladder-of-society', 'the-knowledgism-project'])) {
        $kicker = __('Foundations', 'knowledgism');
      }
    }
  ?>
    <div class="hero tint">
      <div class="wrap">
        <?php if (!empty($kicker)) : ?>
          <div class="kicker"><?php echo esc_html($kicker); ?></div>
        <?php endif; ?>
        <h1 class="mt16"><?php the_title(); ?></h1>
        <?php if (has_excerpt()) : ?>
          <p class="lede mt16" style="max-width:62ch"><?php echo get_the_excerpt(); ?></p>
        <?php elseif (is_page(['privacy-policy', 'cookie-policy', 'terms-of-use'])) : ?>
          <p class="lede mt16" style="max-width:62ch"><?php esc_html_e('Last updated: to be confirmed on publication.', 'knowledgism'); ?></p>
        <?php endif; ?>
      </div>
    </div>

    <main class="global-main site-content block">
      <div class="wrap legalpage">
        <article id="page-<?php the_ID(); ?>" <?php post_class('entry-content'); ?>>
          <?php the_content(); ?>
        </article>
      </div>
    </main>
  <?php endwhile; ?>
<?php endif; ?>

<?php get_footer(); ?>

