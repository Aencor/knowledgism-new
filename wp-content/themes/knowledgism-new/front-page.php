<?php
/**
 * The template for displaying the Front Page (Home).
 * Renders Gutenberg / ACF blocks matching the Knowledgism Prototype.
 *
 * @package Knowledgism
 */

get_header();
?>

<main id="primary" class="site-main front-page-content">
  <?php 
  get_template_part('blocks/home-hero');
  get_template_part('blocks/home-eras');
  get_template_part('blocks/home-articles');
  get_template_part('blocks/home-zone-cta');
  get_template_part('blocks/home-newsletter');
  ?>
</main>

<?php
get_footer();
