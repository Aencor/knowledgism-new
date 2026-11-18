<?php get_header(); ?>

<main class="global-main site-content wrap" style="min-height: 50vh; padding: 48px 24px;">
  <?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class('block'); ?>>
        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        <div class="entry-content mt16">
          <?php the_content(); ?>
        </div>
      </article>
    <?php endwhile; ?>
  <?php else : ?>
    <div class="block">
      <h2><?php esc_html_e('No posts found', 'knowledgism'); ?></h2>
    </div>
  <?php endif; ?>
</main>

<?php get_footer(); ?>
