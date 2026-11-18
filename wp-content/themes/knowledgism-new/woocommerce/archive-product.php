<?php
/**
 * The Template for displaying product archives, including the main shop page (Bookstore).
 * Styled to match the Knowledgism Prototype Bookstore section.
 * Includes interactive filters by Zone colors, Price, Alphabetical (A-Z) and Rating.
 *
 * @package Knowledgism
 */

defined('ABSPATH') || exit;

get_header('shop');

// Product categories for filtering
$product_categories = get_terms([
  'taxonomy'   => 'product_cat',
  'hide_empty' => true,
  'exclude'    => [get_option('default_product_cat')],
]);
$total_count = wp_count_posts('product')->publish;

$is_cat_view = is_product_category();
$catalog_args = [
  'post_type'      => 'product',
  'post_status'    => 'publish',
  'posts_per_page' => -1,
  'orderby'        => 'title',
  'order'          => 'ASC',
];
if ($is_cat_view) {
  $catalog_args['tax_query'] = [
    [
      'taxonomy' => 'product_cat',
      'field'    => 'term_id',
      'terms'    => get_queried_object_id(),
    ],
  ];
}
$catalog_query = new WP_Query($catalog_args);
?>

<div class="hero tint">
  <div class="wrap">
    <div class="kicker">
      <svg class="ic" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><use href="#i-book"/></svg> <?php esc_html_e('Bookstore', 'knowledgism'); ?>
    </div>
    <h1 class="mt16">
      <?php 
      if ($is_cat_view) {
        single_term_title();
      } else {
        echo esc_html__('The work, in its original formulation', 'knowledgism');
      }
      ?>
    </h1>
    <p class="lede mt16" style="max-width:62ch">
      <?php
      if ($is_cat_view && term_description()) {
        echo wp_strip_all_tags(term_description());
      } else {
        echo sprintf(
          esc_html__('%d titles across eleven categories, from the first book to the complete dictionary. Bought, downloaded and read on your own — coaching lives at ACLC.', 'knowledgism'),
          $total_count
        );
      }
      ?>
    </p>
  </div>
</div>

<div class="block">
  <div class="wrap withside">
    <!-- Sidebar: Categories, Zones & Zone CTA -->
    <aside class="side">
      <div class="tiny mb16"><?php esc_html_e('Categories', 'knowledgism'); ?></div>
      <div id="catList">
        <?php
        $shop_url = function_exists('wc_get_page_id') && wc_get_page_id('shop') > 0 ? get_permalink(wc_get_page_id('shop')) : home_url('/bookstore/');
        $current_term_id = $is_cat_view ? get_queried_object_id() : 0;
        ?>
        <a href="<?php echo esc_url($shop_url); ?>" class="<?php echo (!$is_cat_view) ? 'on' : ''; ?>" style="display:flex;justify-content:space-between;gap:10px;align-items:center;">
          <span><?php esc_html_e('All products', 'knowledgism'); ?></span>
          <span class="tiny"><?php echo esc_html($total_count); ?></span>
        </a>
        <?php
        $categories = get_terms([
          'taxonomy'   => 'product_cat',
          'hide_empty' => true,
        ]);
        if (!empty($categories) && !is_wp_error($categories)) :
          foreach ($categories as $cat) :
            // Do not display categories that have 0 products
            if (empty($cat->count) || intval($cat->count) <= 0) {
              continue;
            }
            $is_current = ($current_term_id === $cat->term_id);
        ?>
            <a href="<?php echo esc_url(get_term_link($cat)); ?>" class="<?php echo $is_current ? 'on' : ''; ?>" style="display:flex;justify-content:space-between;gap:10px;align-items:center;">
              <span><?php echo esc_html($cat->name); ?></span>
              <span class="tiny"><?php echo esc_html($cat->count); ?></span>
            </a>
        <?php 
          endforeach;
        endif; 
        ?>
      </div>

      <!-- Quick Category Shortcuts in Sidebar -->
      <div class="tiny mt24 mb16" style="letter-spacing:.08em;text-transform:uppercase;color:var(--muted);font-weight:600;"><?php esc_html_e('Categories', 'knowledgism'); ?></div>
      <div id="sideCategoryFilterList">
        <a href="javascript:void(0)" class="side-cat-trigger on" data-cat-target="all" style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;text-decoration:none;color:inherit;font-size:13px;font-weight:600;">
          <span><?php esc_html_e('All Titles', 'knowledgism'); ?></span>
          <span class="tiny"><?php echo $total_count; ?></span>
        </a>
        <?php if (!empty($product_categories) && !is_wp_error($product_categories)) : ?>
          <?php foreach ($product_categories as $pcat) : ?>
          <a href="javascript:void(0)" class="side-cat-trigger" data-cat-target="<?php echo esc_attr($pcat->slug); ?>" style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;text-decoration:none;color:var(--muted);font-size:13px;">
            <span><?php echo esc_html($pcat->name); ?></span>
            <span class="tiny"><?php echo $pcat->count; ?></span>
          </a>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <!-- Zone CTA Card with Spacious, Uncompressed Button -->
      <div class="card tint mt24">
        <h4><?php esc_html_e('Not sure where to start?', 'knowledgism'); ?></h4>
        <p class="xs mt8"><?php esc_html_e('The free profile tells you which book you need first.', 'knowledgism'); ?></p>
        <a class="btn btn-go btn-block mt16" href="https://app.zonesoflife.com/short-quiz/" target="_blank" rel="noopener noreferrer" style="min-height:46px;display:flex;align-items:center;justify-content:center;padding:12px 20px;line-height:1.2;font-size:14.5px;font-weight:600;border-bottom:none;text-decoration:none;box-sizing:border-box;">
          <?php esc_html_e('Find your Zone', 'knowledgism'); ?>
        </a>
      </div>
    </aside>

    <!-- Main Catalog Grid & Interactive Filters -->
    <main class="site-main">
      <!-- Interactive Filter Panel -->
      <div class="bookstore-filter-panel" id="bookstoreFilterPanel">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:12px;">
          <div class="tiny" style="letter-spacing:.08em;text-transform:uppercase;color:var(--muted);font-weight:600;">
            <svg class="ic" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="margin-right:4px;"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
            <?php esc_html_e('Filter by Category', 'knowledgism'); ?>
          </div>
          <div class="filter-count-badge" id="filterResultCount">
            <?php echo sprintf(esc_html__('Showing %d titles', 'knowledgism'), $catalog_query->post_count); ?>
          </div>
        </div>

        <!-- 1. Filtros por Categorías (alternativa neutral sin depender del color) -->
        <div class="category-filter-bar" style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:14px;">
          <button type="button" class="category-pill-btn active" data-filter-cat="all">
            <span><?php esc_html_e('All Titles', 'knowledgism'); ?></span>
            <span class="cat-count">(<?php echo $total_count; ?>)</span>
          </button>
          <?php if (!empty($product_categories) && !is_wp_error($product_categories)) : ?>
            <?php foreach ($product_categories as $pcat) : ?>
            <button type="button" class="category-pill-btn" data-filter-cat="<?php echo esc_attr($pcat->slug); ?>">
              <span><?php echo esc_html($pcat->name); ?></span>
              <span class="cat-count">(<?php echo $pcat->count; ?>)</span>
            </button>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <!-- 2. Filtros secundarios: Precio, Alfabetización y Calificación -->
        <div class="secondary-filters-bar">
          <div class="filter-selects-wrap">
            <!-- Filtro por Precio -->
            <label class="filter-select-item">
              <span class="tiny" style="color:var(--muted);font-weight:600;"><?php esc_html_e('Price:', 'knowledgism'); ?></span>
              <select class="filter-select" id="filterPrice" aria-label="<?php esc_attr_e('Filter by price', 'knowledgism'); ?>">
                <option value="all"><?php esc_html_e('All Prices', 'knowledgism'); ?></option>
                <option value="0-20"><?php esc_html_e('Under $20', 'knowledgism'); ?></option>
                <option value="20-50"><?php esc_html_e('$20 to $50', 'knowledgism'); ?></option>
                <option value="50-100"><?php esc_html_e('$50 to $100', 'knowledgism'); ?></option>
                <option value="100-plus"><?php esc_html_e('$100 and above', 'knowledgism'); ?></option>
              </select>
            </label>

            <!-- Filtro por Calificación -->
            <label class="filter-select-item">
              <span class="tiny" style="color:var(--muted);font-weight:600;"><?php esc_html_e('Rating:', 'knowledgism'); ?></span>
              <select class="filter-select" id="filterRating" aria-label="<?php esc_attr_e('Filter by rating', 'knowledgism'); ?>">
                <option value="all"><?php esc_html_e('All Ratings', 'knowledgism'); ?></option>
                <option value="5"><?php esc_html_e('★ 5 Stars', 'knowledgism'); ?></option>
                <option value="4.5"><?php esc_html_e('★ 4.5 & up', 'knowledgism'); ?></option>
                <option value="4"><?php esc_html_e('★ 4.0 & up', 'knowledgism'); ?></option>
                <option value="3.5"><?php esc_html_e('★ 3.5 & up', 'knowledgism'); ?></option>
              </select>
            </label>

            <!-- Ordenamiento: Alfabetización (A-Z / Z-A), Precio y Calificación -->
            <label class="filter-select-item">
              <span class="tiny" style="color:var(--muted);font-weight:600;"><?php esc_html_e('Sort by:', 'knowledgism'); ?></span>
              <select class="filter-select" id="filterSort" aria-label="<?php esc_attr_e('Sort products', 'knowledgism'); ?>">
                <option value="default"><?php esc_html_e('Default', 'knowledgism'); ?></option>
                <option value="title_asc"><?php esc_html_e('Alphabetical: A → Z', 'knowledgism'); ?></option>
                <option value="title_desc"><?php esc_html_e('Alphabetical: Z → A', 'knowledgism'); ?></option>
                <option value="price_asc"><?php esc_html_e('Price: Low to High', 'knowledgism'); ?></option>
                <option value="price_desc"><?php esc_html_e('Price: High to Low', 'knowledgism'); ?></option>
                <option value="rating_desc"><?php esc_html_e('Rating: Highest Rated', 'knowledgism'); ?></option>
              </select>
            </label>
          </div>

          <!-- Botón Limpiar filtros -->
          <div>
            <button type="button" class="btn-reset-filters" id="btnResetFilters" style="display:none;">
              <svg class="ic" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
              <?php esc_html_e('Clear filters', 'knowledgism'); ?>
            </button>
          </div>
        </div>
      </div>

      <?php if ($catalog_query->have_posts()) : ?>
        <div class="g3" id="prodGrid">
          <?php
          while ($catalog_query->have_posts()) : $catalog_query->the_post();
            global $product;
            if (empty($product) || !$product->is_visible()) continue;

            $cats = wp_get_post_terms(get_the_ID(), 'product_cat');
            $cat_slugs = !empty($cats) ? wp_list_pluck($cats, 'slug') : [];
            $primary_cat = !empty($cats) ? $cats[0]->name : __('Book', 'knowledgism');

            // Map level to Zone colors according to prototype design system
            $lv = 1;
            $zone_key = 'red';
            $bg = '#001722';
            if (in_array('advanced-knowledgism-materials', $cat_slugs)) {
              $lv = 3;
              $zone_key = 'green';
              $bg = '#1F3A5F';
            } elseif (in_array('omni-sovereignty', $cat_slugs)) {
              $lv = 4;
              $zone_key = 'gold';
              $bg = '#2A1F3D';
            } elseif (in_array('spiritual-literacy', $cat_slugs) || in_array('business-leadership', $cat_slugs) || in_array('finance', $cat_slugs)) {
              $lv = 2;
              $zone_key = 'amber';
              $bg = '#123044';
            }

            $thumb_url = has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'medium') : '';
            $raw_price = floatval($product->get_price());
            $rating_val = floatval($product->get_average_rating() ?: 4.8);
            $review_count = intval($product->get_rating_count() ?: 12);
            ?>
            <a class="card card-link product-card" 
               href="<?php the_permalink(); ?>"
               data-cat="<?php echo esc_attr(implode(' ', $cat_slugs)); ?>"
               data-price="<?php echo esc_attr($raw_price); ?>"
               data-title="<?php echo esc_attr(mb_strtolower(get_the_title())); ?>"
               data-rating="<?php echo esc_attr($rating_val); ?>"
               style="display:flex;flex-direction:column;justify-content:space-between;text-decoration:none;color:inherit;">
              <div>
                <div class="prodcover" style="position:relative;background:#f8fafc;height:280px;border-radius:8px;display:flex;align-items:center;justify-content:center;padding:12px;margin-bottom:14px;border:1px solid rgba(0,0,0,0.06);">
                  <?php if ($thumb_url) : ?>
                    <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php the_title_attribute(); ?>" style="max-height:250px;width:auto;max-width:100%;object-fit:contain;border-radius:4px;box-shadow:0 4px 10px rgba(0,0,0,0.08);" />
                  <?php endif; ?>
                </div>
                <div style="margin-bottom:6px;">
                  <span class="product-category-badge" style="display:inline-block;padding:2px 8px;font-size:11px;font-weight:600;border-radius:4px;background:#f1f5f9;color:#475569;letter-spacing:0.02em;"><?php echo esc_html($primary_cat); ?></span>
                </div>
                <h4 class="mt4" style="font-size:15px;line-height:1.35;font-weight:600;"><?php the_title(); ?></h4>
              </div>
              <div class="mt16" style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
                <span class="price"><?php echo $product->get_price_html(); ?></span>
                <span class="card-rating" title="<?php echo sprintf(esc_attr__('%s / 5 (%d reviews)', 'knowledgism'), number_format($rating_val, 1), $review_count); ?>">
                  <span class="stars-gold">★</span>
                  <span><?php echo number_format($rating_val, 1); ?></span>
                  <span class="rating-count">(<?php echo $review_count; ?>)</span>
                </span>
              </div>
            </a>
          <?php endwhile; wp_reset_postdata(); ?>

          <!-- Empty state when no cards match filters -->
          <div class="card pad-lg center" id="noFilterResults" style="display:none;grid-column:1 / -1;padding:48px 24px;text-align:center;">
            <p class="lede" style="font-size:18px;color:var(--ink);font-weight:600;"><?php esc_html_e('No products found matching the selected filters.', 'knowledgism'); ?></p>
            <p class="sm mt8" style="color:var(--muted);"><?php esc_html_e('Try selecting a different category, price range, or rating.', 'knowledgism'); ?></p>
            <button type="button" class="btn btn-ghost btn-sm mt16" onclick="document.getElementById('btnResetFilters').click();" style="margin-top:16px;">
              <?php esc_html_e('Reset all filters', 'knowledgism'); ?>
            </button>
          </div>
        </div>
      <?php else : ?>
        <div class="card pad-lg">
          <p class="sm"><?php esc_html_e('No products found in this category.', 'knowledgism'); ?></p>
        </div>
      <?php endif; ?>
    </main>
  </div>
</div>

<style>
.category-pill-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  border-radius: 9999px;
  font-size: 13px;
  font-weight: 500;
  border: 1px solid rgba(0,0,0,0.12);
  background: #ffffff;
  color: var(--ink, #1e293b);
  cursor: pointer;
  transition: all 0.2s ease;
  line-height: 1.4;
}
.category-pill-btn:hover {
  background: #f1f5f9;
  border-color: rgba(0,0,0,0.2);
}
.category-pill-btn.active {
  background: #0f172a;
  color: #ffffff;
  border-color: #0f172a;
}
.category-pill-btn .cat-count {
  opacity: 0.7;
  font-size: 11px;
}
.side-cat-trigger.on {
  font-weight: 600;
  color: var(--ink, #0f172a) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const panel = document.getElementById('bookstoreFilterPanel');
  const grid = document.getElementById('prodGrid');
  if (!panel || !grid) return;

  const cards = Array.from(grid.querySelectorAll('.product-card'));
  const catBtns = panel.querySelectorAll('[data-filter-cat]');
  const sideCatTriggers = document.querySelectorAll('.side-cat-trigger');
  const priceSelect = document.getElementById('filterPrice');
  const ratingSelect = document.getElementById('filterRating');
  const sortSelect = document.getElementById('filterSort');
  const resetBtn = document.getElementById('btnResetFilters');
  const countBadge = document.getElementById('filterResultCount');
  const emptyState = document.getElementById('noFilterResults');

  let activeCat = 'all';
  let activePrice = 'all';
  let activeRating = 'all';
  let activeSort = 'default';

  // Read URL params if present
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.has('cat')) {
    activeCat = urlParams.get('cat');
    syncCatUI(activeCat);
  }
  if (urlParams.has('price') && priceSelect) {
    activePrice = urlParams.get('price');
    priceSelect.value = activePrice;
  }
  if (urlParams.has('rating') && ratingSelect) {
    activeRating = urlParams.get('rating');
    ratingSelect.value = activeRating;
  }
  if (urlParams.has('sort') && sortSelect) {
    activeSort = urlParams.get('sort');
    sortSelect.value = activeSort;
  }

  // Record original DOM position for default sorting
  cards.forEach((card, idx) => {
    card.setAttribute('data-orig-idx', idx);
  });

  function syncCatUI(cat) {
    catBtns.forEach(btn => {
      btn.classList.toggle('active', btn.getAttribute('data-filter-cat') === cat);
    });
    sideCatTriggers.forEach(trigger => {
      trigger.classList.toggle('on', trigger.getAttribute('data-cat-target') === cat);
    });
  }

  function applyFilters() {
    let visibleCount = 0;
    const isFiltered = (activeCat !== 'all' || activePrice !== 'all' || activeRating !== 'all' || activeSort !== 'default');

    if (resetBtn) {
      resetBtn.style.display = isFiltered ? 'inline-flex' : 'none';
    }

    cards.forEach(card => {
      const cardCats = (card.getAttribute('data-cat') || '').split(' ');
      const price = parseFloat(card.getAttribute('data-price')) || 0;
      const rating = parseFloat(card.getAttribute('data-rating')) || 0;

      const matchCat = (activeCat === 'all' || cardCats.includes(activeCat));

      let matchPrice = true;
      if (activePrice === '0-20') matchPrice = (price < 20);
      else if (activePrice === '20-50') matchPrice = (price >= 20 && price <= 50);
      else if (activePrice === '50-100') matchPrice = (price > 50 && price <= 100);
      else if (activePrice === '100-plus') matchPrice = (price > 100);

      let matchRating = true;
      if (activeRating !== 'all') {
        const minRating = parseFloat(activeRating);
        matchRating = (rating >= minRating);
      }

      const show = matchCat && matchPrice && matchRating;
      card.style.display = show ? 'flex' : 'none';
      if (show) visibleCount++;
    });

    // Sort visible cards
    const visibleCards = cards.filter(c => c.style.display !== 'none');
    visibleCards.sort((a, b) => {
      if (activeSort === 'title_asc') {
        return a.getAttribute('data-title').localeCompare(b.getAttribute('data-title'));
      } else if (activeSort === 'title_desc') {
        return b.getAttribute('data-title').localeCompare(a.getAttribute('data-title'));
      } else if (activeSort === 'price_asc') {
        return (parseFloat(a.getAttribute('data-price')) || 0) - (parseFloat(b.getAttribute('data-price')) || 0);
      } else if (activeSort === 'price_desc') {
        return (parseFloat(b.getAttribute('data-price')) || 0) - (parseFloat(a.getAttribute('data-price')) || 0);
      } else if (activeSort === 'rating_desc') {
        return (parseFloat(b.getAttribute('data-rating')) || 0) - (parseFloat(a.getAttribute('data-rating')) || 0);
      }
      return (parseInt(a.getAttribute('data-orig-idx')) || 0) - (parseInt(b.getAttribute('data-orig-idx')) || 0);
    });

    visibleCards.forEach(card => grid.appendChild(card));

    // Update Result Counter & Empty State
    if (countBadge) {
      countBadge.textContent = 'Showing ' + visibleCount + ' ' + (visibleCount === 1 ? 'title' : 'titles');
    }
    if (emptyState) {
      emptyState.style.display = (visibleCount === 0) ? 'block' : 'none';
    }

    // Sync URL without reloading
    const newParams = new URLSearchParams(window.location.search);
    if (activeCat !== 'all') newParams.set('cat', activeCat); else newParams.delete('cat');
    if (activePrice !== 'all') newParams.set('price', activePrice); else newParams.delete('price');
    if (activeRating !== 'all') newParams.set('rating', activeRating); else newParams.delete('rating');
    if (activeSort !== 'default') newParams.set('sort', activeSort); else newParams.delete('sort');
    
    const newQuery = newParams.toString();
    const newUrl = window.location.pathname + (newQuery ? '?' + newQuery : '');
    window.history.replaceState({}, '', newUrl);
  }

  // Category pill buttons click
  catBtns.forEach(btn => {
    btn.addEventListener('click', function() {
      activeCat = this.getAttribute('data-filter-cat');
      syncCatUI(activeCat);
      applyFilters();
    });
  });

  // Sidebar Category links click
  sideCatTriggers.forEach(trigger => {
    trigger.addEventListener('click', function(e) {
      e.preventDefault();
      activeCat = this.getAttribute('data-cat-target');
      syncCatUI(activeCat);
      applyFilters();
    });
  });

  // Price dropdown
  if (priceSelect) {
    priceSelect.addEventListener('change', function() {
      activePrice = this.value;
      applyFilters();
    });
  }

  // Rating dropdown
  if (ratingSelect) {
    ratingSelect.addEventListener('change', function() {
      activeRating = this.value;
      applyFilters();
    });
  }

  // Sort dropdown
  if (sortSelect) {
    sortSelect.addEventListener('change', function() {
      activeSort = this.value;
      applyFilters();
    });
  }

  // Reset button
  if (resetBtn) {
    resetBtn.addEventListener('click', function() {
      activeCat = 'all';
      activePrice = 'all';
      activeRating = 'all';
      activeSort = 'default';
      syncCatUI('all');
      if (priceSelect) priceSelect.value = 'all';
      if (ratingSelect) ratingSelect.value = 'all';
      if (sortSelect) sortSelect.value = 'default';
      applyFilters();
    });
  }

  // Initial run if URL params exist
  if (urlParams.toString()) {
    applyFilters();
  }
});
</script>

<?php
get_footer('shop');
