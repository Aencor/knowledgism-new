<?php
/**
 * Template Name: Foundations (Sidebar Layout)
 *
 * Used for Foundations section pages (About, What Is Knowledgism, The Knowledge Era, etc.)
 *
 * @package Knowledgism
 */

get_header();

while (have_posts()) : the_post();
	$current_id   = get_the_ID();
	$current_slug = $post->post_name;
	$hero_title   = get_the_title();

	if (in_array($current_slug, ['what-is-knowledgism', 'about'], true)) {
		$hero_title = __('The game being played is knowledge versus ignorance', 'knowledgism');
	}
?>

<div class="hero tint">
  <div class="wrap">
    <div class="kicker"><?php esc_html_e('Foundations', 'knowledgism'); ?></div>
    <h1 class="mt16" id="foundations-hero-title" style="max-width:22ch"><?php echo esc_html($hero_title); ?></h1>
  </div>
</div>

<main class="global-main site-content block">
  <div class="wrap withside">
    <aside class="side foundations-sidebar" aria-label="<?php esc_attr_e('Foundations navigation', 'knowledgism'); ?>">
      <div class="tiny mb16"><?php esc_html_e('Foundations', 'knowledgism'); ?></div>
      <?php 
      $menu = function_exists('knowledgism_get_foundations_menu') ? knowledgism_get_foundations_menu() : [];
      foreach ($menu as $item) :
        $is_active = ($current_id === $item['id'] || $current_slug === $item['slug']);
        $item_url  = get_permalink($item['id']);
      ?>
        <a href="<?php echo esc_url($item_url); ?>" 
           class="<?php echo $is_active ? 'on' : ''; ?>"
           data-id="<?php echo esc_attr($item['id']); ?>"
           data-slug="<?php echo esc_attr($item['slug']); ?>">
          <?php echo esc_html($item['title']); ?>
        </a>
      <?php endforeach; ?>
    </aside>

    <div id="foundations-main" style="min-width:0">
      <div id="foundations-content-area" class="entry-content">
        <?php the_content(); ?>

        <?php if (in_array($current_slug, ['what-is-knowledgism', 'about'], true)) : ?>
          <div class="card tint mt32 mb32">
            <div class="tiny mb16"><?php esc_html_e('Eight thousand years, three paradigms', 'knowledgism'); ?></div>
            <div class="g3">
              <div>
                <div class="tiny" style="color:var(--red)"><?php esc_html_e('Warrior Era', 'knowledgism'); ?></div>
                <p class="xs mt8"><?php esc_html_e('Conquest, domination and compliance as the prevailing paradigm.', 'knowledgism'); ?></p>
              </div>
              <div>
                <div class="tiny" style="color:#B98F0C"><?php esc_html_e('Literacy Era', 'knowledgism'); ?></div>
                <p class="xs mt8"><?php esc_html_e('Reading, writing and arithmetic spread almost worldwide. Survival needs met for most.', 'knowledgism'); ?></p>
              </div>
              <div>
                <div class="tiny" style="color:var(--green)"><?php esc_html_e('Knowledge Era', 'knowledgism'); ?></div>
                <p class="xs mt8"><?php esc_html_e('Recorded skill and smart machines change what you must know and be to keep winning.', 'knowledgism'); ?></p>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <hr class="rule">
      <div class="split" style="align-items:center">
        <div>
          <h3><?php esc_html_e('Which Zone are you operating from?', 'knowledgism'); ?></h3>
          <p class="sm mt8"><?php esc_html_e('Theory becomes useful when you apply it to your own case.', 'knowledgism'); ?></p>
        </div>
        <a class="btn btn-go" href="<?php echo esc_url(home_url('/the-zones-of-life/')); ?>"><?php esc_html_e('Find your Zone', 'knowledgism'); ?></a>
      </div>
    </div>
  </div>
</main>

<?php 
endwhile;

get_footer();
