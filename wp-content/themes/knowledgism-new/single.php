<?php
/**
 * The template for displaying all single posts (Articles).
 * Matches the Knowledgism Prototype article layout:
 * - Sidebar on the left (Terms, Categories, Which Zones CTA card)
 * - Main content on the right (Kicker, Title, Lede, Body)
 *
 * @package Knowledgism
 */

get_header();

// Canonical categories
$canonical_cats = [
  'the-zones-of-life'   => __('The Zones of Life', 'knowledgism'),
  'spiritual-literacy'  => __('Spiritual Literacy', 'knowledgism'),
  'presence'            => __('Presence', 'knowledgism'),
  'love'                => __('Love', 'knowledgism'),
  'truth'               => __('Truth', 'knowledgism'),
  'relationships'       => __('Relationships', 'knowledgism'),
  'knowledge'           => __('Knowledge', 'knowledgism'),
  'integrity'           => __('Integrity', 'knowledgism'),
  'mastery'             => __('Mastery', 'knowledgism'),
  'knowledgism'         => __('Knowledgism', 'knowledgism'),
];

while (have_posts()) : the_post();
  $post_cats = wp_get_post_terms(get_the_ID(), 'category');
  $primary_cat_name = __('Articles', 'knowledgism');
  $current_cat_slug = '';
  if (!empty($post_cats)) {
    foreach ($post_cats as $pc) {
      if (array_key_exists($pc->slug, $canonical_cats)) {
        $primary_cat_name = $pc->name;
        $current_cat_slug = $pc->slug;
        break;
      }
    }
    if ($primary_cat_name === __('Articles', 'knowledgism')) {
      $primary_cat_name = $post_cats[0]->name;
      $current_cat_slug = $post_cats[0]->slug;
    }
  }

  $author_name = get_the_author();
  if (empty($author_name) || $author_name === 'admin') {
    $author_name = 'Alan C. Walter';
  }
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('block'); ?>>
  <div class="wrap withside">
    <!-- Left Sidebar: Terms, Categories, and Zone CTA -->
    <aside class="side">
      <div class="tiny mb16" style="font-weight:700;color:var(--muted);"><?php esc_html_e('Terms in this article', 'knowledgism'); ?></div>
      <a href="<?php echo esc_url(home_url('/glossary/#term-zone')); ?>">Zones &rarr;</a>
      <a href="<?php echo esc_url(home_url('/glossary/#term-presence')); ?>">Presence &rarr;</a>
      <a href="<?php echo esc_url(home_url('/glossary/#term-life-force')); ?>">Life force &rarr;</a>

      <div class="tiny mb16 mt32" style="font-weight:700;color:var(--muted);"><?php esc_html_e('Categories', 'knowledgism'); ?></div>
      <div id="sideCatList">
        <?php foreach ($canonical_cats as $cat_slug => $cat_name) : 
          $cat_url = home_url('/category/' . $cat_slug . '/');
          $is_current = ($current_cat_slug === $cat_slug);
        ?>
          <a href="<?php echo esc_url($cat_url); ?>" class="<?php echo $is_current ? 'on' : ''; ?>">
            <?php echo esc_html($cat_name); ?>
          </a>
        <?php endforeach; ?>
      </div>

      <!-- Which Zones CTA Card (underneath menu) -->
      <div class="card tint mt24" style="background:var(--tint);border:1px solid transparent;border-radius:10px;padding:20px;">
        <div class="zones dim" style="height:5px;width:100%;margin-bottom:12px;border-radius:3px;overflow:hidden;display:flex;gap:2px;">
          <i class="z1 on" style="flex:1;background:var(--red);"></i>
          <i class="z2 on" style="flex:1;background:var(--amber);"></i>
          <i class="z3 on" style="flex:1;background:var(--green);"></i>
          <i class="z4 on" style="flex:1;background:var(--gold);"></i>
        </div>
        <h4 style="font-family:var(--display);font-size:16px;font-weight:600;line-height:1.3;">
          <?php esc_html_e('Which Zone are you choosing from today?', 'knowledgism'); ?>
        </h4>
        <p class="xs mt8" style="color:#5A6875;line-height:1.55;">
          <?php esc_html_e('Fourteen questions, two minutes. You will know which Zone you operate from and which of the three areas is holding you back. Free.', 'knowledgism'); ?>
        </p>
        <a class="btn btn-go btn-sm btn-block mt16" href="https://app.zonesoflife.com/short-quiz/" target="_blank" rel="noopener noreferrer">
          <?php esc_html_e('Find your Zone', 'knowledgism'); ?>
        </a>
      </div>
    </aside>

    <!-- Right Column: Main Article Content -->
    <main class="site-main" style="max-width:760px;">
      <div class="kicker">
        <?php echo esc_html($primary_cat_name); ?> &middot; <?php echo esc_html($author_name); ?>
      </div>

      <h1 class="mt16" style="font-size:clamp(32px, 4vw, 44px);line-height:1.15;letter-spacing:-.015em;">
        <?php the_title(); ?>
      </h1>

      <?php if (has_excerpt()) : ?>
        <p class="lede mt24" style="font-size:18px;line-height:1.65;color:#3D4A57;">
          <?php echo get_the_excerpt(); ?>
        </p>
      <?php endif; ?>

      <div class="entry-content mt24">
        <?php the_content(); ?>
      </div>
    </main>
  </div>
</article>

<?php endwhile; ?>

<?php get_footer(); ?>
