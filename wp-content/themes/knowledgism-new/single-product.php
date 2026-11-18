<?php
/**
 * The Template for displaying all single products.
 * Styled to match the Knowledgism Prototype Product detail section.
 *
 * @package Knowledgism
 */

defined('ABSPATH') || exit;

get_header('shop');
?>

<?php while (have_posts()) : the_post(); global $product; ?>
  <div class="block">
    <div class="wrap">
      <div class="split top">
        <!-- Product Cover Image -->
        <div>
          <?php
          $cats = wp_get_post_terms(get_the_ID(), 'product_cat');
          $cat_slugs = !empty($cats) ? wp_list_pluck($cats, 'slug') : [];
          $primary_cat = !empty($cats) ? $cats[0]->name : __('Book', 'knowledgism');

          $lv = 1;
          $level_desc = __('Entry level — written for the Red and Yellow Zones', 'knowledgism');
          $bg = '#001722';
          if (in_array('advanced-knowledgism-materials', $cat_slugs)) {
            $lv = 3;
            $level_desc = __('Advanced level — written for the Green Zone', 'knowledgism');
            $bg = '#1F3A5F';
          } elseif (in_array('omni-sovereignty', $cat_slugs)) {
            $lv = 4;
            $level_desc = __('Mastery level — written for the Golden Magic Zone', 'knowledgism');
            $bg = '#2A1F3D';
          } elseif (in_array('spiritual-literacy', $cat_slugs) || in_array('business-leadership', $cat_slugs) || in_array('finance', $cat_slugs)) {
            $lv = 2;
            $level_desc = __('Intermediate level — written for the Yellow and Green Zones', 'knowledgism');
            $bg = '#123044';
          }

          $thumb_url = has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'large') : '';
          ?>
          <div class="prodcover" style="position:relative;background:#f8fafc;min-height:380px;max-width:320px;border-radius:12px;display:flex;align-items:center;justify-content:center;padding:16px;border:1px solid rgba(0,0,0,0.06);">
            <?php if ($thumb_url) : ?>
              <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php the_title_attribute(); ?>" style="max-height:350px;width:auto;max-width:100%;object-fit:contain;border-radius:6px;box-shadow:0 8px 24px rgba(0,0,0,0.12);" />
            <?php endif; ?>
          </div>
        </div>

        <!-- Product Details & Purchase Form -->
        <div>
          <div class="kicker"><?php echo esc_html($primary_cat); ?></div>
          <h1 class="mt16" style="font-size:clamp(28px, 3.2vw, 38px);"><?php the_title(); ?></h1>
          
          <?php if (has_excerpt()) : ?>
            <div class="lede mt16"><?php the_excerpt(); ?></div>
          <?php endif; ?>

          <div class="mt24" style="max-width:360px;">
            <div class="zones dim" style="height:5px;display:flex;gap:3px;border-radius:3px;overflow:hidden;">
              <i class="z1 <?php echo $lv >= 1 ? 'on' : ''; ?>" style="flex:1;background:var(--red);"></i>
              <i class="z2 <?php echo $lv >= 2 ? 'on' : ''; ?>" style="flex:1;background:var(--amber);"></i>
              <i class="z3 <?php echo $lv >= 3 ? 'on' : ''; ?>" style="flex:1;background:var(--green);"></i>
              <i class="z4 <?php echo $lv >= 4 ? 'on' : ''; ?>" style="flex:1;background:var(--gold);"></i>
            </div>
            <div class="tiny mt8" style="color:var(--muted);"><?php echo esc_html($level_desc); ?></div>
          </div>

          <div class="hgroup mt24" style="gap:18px;flex-wrap:wrap;align-items:baseline;">
            <span class="price" style="font-size:26px;font-family:var(--mono);font-weight:700;"><?php echo $product->get_price_html(); ?></span>
            <?php if ($product->is_virtual() || $product->is_downloadable()) : ?>
              <span class="pill"><?php esc_html_e('Instant Download · eBook / Audio', 'knowledgism'); ?></span>
            <?php else : ?>
              <span class="pill"><?php esc_html_e('Paperback / Hardcover', 'knowledgism'); ?></span>
            <?php endif; ?>
          </div>

          <div class="mt24 single-add-to-cart-wrap" style="max-width:360px;">
            <?php woocommerce_template_single_add_to_cart(); ?>
            <?php if (function_exists('wc_get_cart_url') && function_exists('WC') && WC()->cart && WC()->cart->get_cart_contents_count() > 0) : ?>
              <a class="btn btn-ghost btn-sm btn-block mt12" href="<?php echo esc_url(wc_get_cart_url()); ?>" style="display:inline-flex;gap:8px;align-items:center;justify-content:center;margin-top:10px;">
                <svg class="ic" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><use href="#i-cart"/></svg>
                <?php echo sprintf(esc_html__('View Cart (%d)', 'knowledgism'), WC()->cart->get_cart_contents_count()); ?>
              </a>
            <?php endif; ?>
            <div class="tiny center mt8" style="color:var(--muted);"><?php esc_html_e('One-step checkout · secure purchase', 'knowledgism'); ?></div>
          </div>
        </div>
      </div>

      <!-- Full-Width Description & Overview -->
      <hr class="rule mt32 mb32">

      <div class="product-description-full" style="width:100%;max-width:100%;">
        <div class="tiny mb16"><?php esc_html_e('Description & Overview', 'knowledgism'); ?></div>
        <div class="entry-content" style="line-height:1.75;max-width:100%;">
          <?php the_content(); ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Related Products Section -->
  <?php
  $related_limit = 4;
  $related_ids = function_exists('wc_get_related_products') ? wc_get_related_products(get_the_ID(), $related_limit) : [];
  
  if (!empty($related_ids)) {
    $related_query = new WP_Query([
      'post_type'      => 'product',
      'post_status'    => 'publish',
      'posts_per_page' => $related_limit,
      'post__in'       => $related_ids,
      'orderby'        => 'post__in',
    ]);
  } else {
    $related_query = new WP_Query([
      'post_type'      => 'product',
      'post_status'    => 'publish',
      'posts_per_page' => $related_limit,
      'post__not_in'   => [get_the_ID()],
      'orderby'        => 'rand',
    ]);
  }

  if ($related_query->have_posts()) :
  ?>
  <div class="block">
    <div class="wrap">
      <div class="hgroup mb24" style="justify-content:space-between;flex-wrap:wrap;gap:12px;align-items:baseline;">
        <div>
          <div class="kicker"><?php esc_html_e('Explore more', 'knowledgism'); ?></div>
          <h2 class="mt8" style="font-size:24px;font-family:var(--display);"><?php esc_html_e('Related Books & Materials', 'knowledgism'); ?></h2>
        </div>
        <?php if (function_exists('wc_get_page_permalink')) : ?>
          <a class="btn btn-ghost btn-sm" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">
            <?php esc_html_e('View all books', 'knowledgism'); ?> &rarr;
          </a>
        <?php endif; ?>
      </div>

      <div class="g4">
        <?php
        while ($related_query->have_posts()) : $related_query->the_post();
          $rel_product = wc_get_product(get_the_ID());
          if (empty($rel_product) || !$rel_product->is_visible()) continue;

          $rel_cats = wp_get_post_terms(get_the_ID(), 'product_cat');
          $rel_cat_slugs = !empty($rel_cats) ? wp_list_pluck($rel_cats, 'slug') : [];
          $rel_primary_cat = !empty($rel_cats) ? $rel_cats[0]->name : __('Book', 'knowledgism');

          $rel_lv = 1;
          $rel_bg = '#001722';
          if (in_array('advanced-knowledgism-materials', $rel_cat_slugs)) {
            $rel_lv = 3;
            $rel_bg = '#1F3A5F';
          } elseif (in_array('omni-sovereignty', $rel_cat_slugs)) {
            $rel_lv = 4;
            $rel_bg = '#2A1F3D';
          } elseif (in_array('spiritual-literacy', $rel_cat_slugs) || in_array('business-leadership', $rel_cat_slugs) || in_array('finance', $rel_cat_slugs)) {
            $rel_lv = 2;
            $rel_bg = '#123044';
          }

          $rel_thumb_url = has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'medium') : '';
        ?>
          <a class="card card-link product-card" href="<?php the_permalink(); ?>" style="display:flex;flex-direction:column;justify-content:space-between;text-decoration:none;color:inherit;">
            <div>
              <div class="prodcover" style="position:relative;overflow:hidden;background:<?php echo esc_attr($rel_bg); ?>;height:160px;border-radius:8px;display:flex;align-items:flex-end;padding:12px;margin-bottom:14px;">
                <?php if ($rel_thumb_url) : ?>
                  <img src="<?php echo esc_url($rel_thumb_url); ?>" alt="<?php the_title_attribute(); ?>" style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;opacity:0.85;" />
                <?php endif; ?>
                <div class="zones dim" style="height:4px;width:100%;position:relative;z-index:2;border-radius:2px;overflow:hidden;display:flex;gap:2px;">
                  <i class="z1 <?php echo $rel_lv >= 1 ? 'on' : ''; ?>" style="flex:1;background:var(--red);"></i>
                  <i class="z2 <?php echo $rel_lv >= 2 ? 'on' : ''; ?>" style="flex:1;background:var(--amber);"></i>
                  <i class="z3 <?php echo $rel_lv >= 3 ? 'on' : ''; ?>" style="flex:1;background:var(--green);"></i>
                  <i class="z4 <?php echo $rel_lv >= 4 ? 'on' : ''; ?>" style="flex:1;background:var(--gold);"></i>
                </div>
              </div>
              <div class="tiny" style="color:var(--muted);"><?php echo esc_html($rel_primary_cat); ?></div>
              <h4 class="mt8" style="font-size:15px;line-height:1.35;font-weight:600;"><?php the_title(); ?></h4>
            </div>
            <div class="mt16" style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
              <span class="price"><?php echo $rel_product->get_price_html(); ?></span>
            </div>
          </a>
        <?php endwhile; wp_reset_postdata(); ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Zone CTA Cross-Sell Block -->
  <div class="block">
    <div class="wrap">
      <div class="card tint pad-lg split" style="align-items:center;">
        <div>
          <h3><?php esc_html_e('Which book comes after this one?', 'knowledgism'); ?></h3>
          <p class="sm mt8"><?php esc_html_e('The free Zone Profile answers that in fourteen questions.', 'knowledgism'); ?></p>
        </div>
        <a class="btn btn-go" href="https://app.zonesoflife.com/short-quiz/" target="_blank" rel="noopener noreferrer">
          <?php esc_html_e('Find your Zone', 'knowledgism'); ?>
        </a>
      </div>
    </div>
  </div>
<?php endwhile; ?>

<?php
get_footer('shop');
