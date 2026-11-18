<?php
/**
 * Template Name: Articles
 * Description: Articles archive with 10 categories filter tabs and card grid matching prototype.
 *
 * @package Knowledgism
 */

get_header();

// 10 canonical categories from prototype
$categories_spec = [
  'all'                 => __('ALL', 'knowledgism'),
  'the-zones-of-life'   => __('THE ZONES OF LIFE', 'knowledgism'),
  'spiritual-literacy'  => __('SPIRITUAL LITERACY', 'knowledgism'),
  'presence'            => __('PRESENCE', 'knowledgism'),
  'love'                => __('LOVE', 'knowledgism'),
  'truth'               => __('TRUTH', 'knowledgism'),
  'relationships'       => __('RELATIONSHIPS', 'knowledgism'),
  'knowledge'           => __('KNOWLEDGE', 'knowledgism'),
  'integrity'           => __('INTEGRITY', 'knowledgism'),
  'mastery'             => __('MASTERY', 'knowledgism'),
  'knowledgism'         => 'KNOWLEDGIS|M',
];

// Determine active filter from query param if provided
$requested_cat = isset($_GET['category']) ? sanitize_title(wp_unslash($_GET['category'])) : 'all';
if (!array_key_exists($requested_cat, $categories_spec)) {
  $requested_cat = 'all';
}

// Curated prototype excerpts mapping
$curated_excerpts = [
  'the-zones-and-your-will'              => __('Your Zone describes where you are choosing from, not only what you choose.', 'knowledgism'),
  'the-prime-targets-of-great-people'    => __('What great people aim at, and why the target itself is the skill.', 'knowledgism'),
  'the-source-points-of-getting-rich'    => __('Where wealth originates before it ever shows up as money.', 'knowledgism'),
  'being-present-is-a-skill-not-a-mood'  => __('Presence can be trained the same way any other ability is trained.', 'knowledgism'),
  'integrity-as-a-structural-property'   => __('Why integrity behaves like load-bearing structure rather than virtue.', 'knowledgism'),
  'what-love-requires-you-to-know'       => __('Affection without knowledge runs out. Here is what has to be understood.', 'knowledgism'),
  'truth-and-the-cost-of-not-looking'    => __('Avoidance is expensive, and the bill arrives with interest.', 'knowledgism'),
  'the-anatomy-of-a-working-relationship'=> __('The components that have to be present before a relationship can hold.', 'knowledgism'),
  'spiritual-literacy-defined'           => __('A vocabulary for the part of experience most people cannot describe.', 'knowledgism'),
];

// Query all published articles
$args = [
  'post_type'      => 'post',
  'post_status'    => 'publish',
  'posts_per_page' => -1,
  'orderby'        => 'date',
  'order'          => 'DESC',
];
$articles_query = new WP_Query($args);
?>

<div class="hero tint">
  <div class="wrap">
    <div class="kicker"><?php esc_html_e('Articles', 'knowledgism'); ?></div>
    <h1 class="mt16"><?php esc_html_e('Ten categories, one body of work', 'knowledgism'); ?></h1>
    <p class="lede mt16" style="max-width:60ch">
      <?php esc_html_e('Everything below was written or recorded by Alan C. Walter. Filter by the theme you need today.', 'knowledgism'); ?>
    </p>
  </div>
</div>

<div class="block">
  <div class="wrap">
    <!-- Category Filter Tabs -->
    <div class="tabs" id="artTabs" role="tablist" aria-label="<?php esc_attr_e('Article Categories', 'knowledgism'); ?>">
      <?php foreach ($categories_spec as $slug => $label) : 
        $is_on = ($requested_cat === $slug);
        $cat_url = ($slug === 'all') ? home_url('/articles/') : home_url('/category/' . $slug . '/');
      ?>
        <button
          type="button"
          class="tab <?php echo $is_on ? 'on' : ''; ?>"
          data-c="<?php echo esc_attr($slug); ?>"
          data-url="<?php echo esc_url($cat_url); ?>"
          role="tab"
          aria-selected="<?php echo $is_on ? 'true' : 'false'; ?>">
          <?php echo esc_html($label); ?>
        </button>
      <?php endforeach; ?>
    </div>

    <!-- Articles Grid -->
    <div class="g3" id="artGrid">
      <?php if ($articles_query->have_posts()) : ?>
        <?php while ($articles_query->have_posts()) : $articles_query->the_post(); 
          $post_slug = get_post_field('post_name', get_the_ID());
          $terms = wp_get_post_terms(get_the_ID(), 'category');
          $term_slugs = !empty($terms) ? wp_list_pluck($terms, 'slug') : [];
          
          // Identify primary category to display
          $primary_term_name = __('Knowledgism', 'knowledgism');
          if (!empty($terms)) {
            // Prefer one of the canonical 10 categories if available
            $found_primary = false;
            foreach ($terms as $term) {
              if (array_key_exists($term->slug, $categories_spec) && $term->slug !== 'all') {
                $primary_term_name = $term->name;
                $found_primary = true;
                break;
              }
            }
            if (!$found_primary) {
              $primary_term_name = $terms[0]->name;
            }
          }

          // Format category tag in green
          $primary_tag_display = mb_strtoupper($primary_term_name, 'UTF-8');

          // Excerpt lookup: curated prototype excerpt > custom post_excerpt > trimmed content
          if (isset($curated_excerpts[$post_slug])) {
            $card_excerpt = $curated_excerpts[$post_slug];
          } elseif (has_excerpt()) {
            $card_excerpt = get_the_excerpt();
          } else {
            $raw_content = wp_strip_all_tags(get_the_content());
            $card_excerpt = wp_trim_words($raw_content, 18, '...');
          }

          $card_cats_attr = implode(' ', $term_slugs);
        ?>
          <a class="card card-link art-card"
             href="<?php the_permalink(); ?>"
             data-cats="<?php echo esc_attr($card_cats_attr); ?>"
             style="display:flex;flex-direction:column;justify-content:flex-start;text-decoration:none;color:inherit;">
            <div class="tiny" style="color:var(--green);font-weight:700;letter-spacing:.08em;">
              <?php echo esc_html($primary_tag_display); ?>
            </div>
            <h4 class="mt8" style="font-family:var(--display);font-size:18px;font-weight:600;line-height:1.25;color:var(--ink);">
              <?php the_title(); ?>
            </h4>
            <p class="sm mt8" style="color:#4A5764;font-size:14px;line-height:1.55;margin-top:8px;">
              <?php echo esc_html($card_excerpt); ?>
            </p>
          </a>
        <?php endwhile; wp_reset_postdata(); ?>
      <?php else : ?>
        <p class="sm"><?php esc_html_e('No articles found.', 'knowledgism'); ?></p>
      <?php endif; ?>
    </div>

    <div id="artEmpty" class="sm mt24" style="display:none;color:var(--muted);">
      <?php esc_html_e('No articles in this category yet.', 'knowledgism'); ?>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var tabsContainer = document.getElementById('artTabs');
  var grid = document.getElementById('artGrid');
  var emptyMsg = document.getElementById('artEmpty');
  if (!tabsContainer || !grid) return;

  var tabs = tabsContainer.querySelectorAll('.tab');
  var cards = grid.querySelectorAll('.art-card');

  function filterCategory(catSlug, updateUrl) {
    var visibleCount = 0;

    // Update active tab styles
    tabs.forEach(function (tab) {
      var isTarget = tab.dataset.c === catSlug;
      tab.classList.toggle('on', isTarget);
      tab.setAttribute('aria-selected', isTarget ? 'true' : 'false');
    });

    // Filter cards
    cards.forEach(function (card) {
      var cats = (card.dataset.cats || '').split(' ');
      var matches = (catSlug === 'all') || cats.indexOf(catSlug) !== -1;
      if (matches) {
        card.style.display = 'flex';
        visibleCount++;
      } else {
        card.style.display = 'none';
      }
    });

    if (emptyMsg) {
      emptyMsg.style.display = (visibleCount === 0) ? 'block' : 'none';
    }

    if (updateUrl && history.replaceState) {
      var newUrl = catSlug === 'all' ? window.location.pathname : window.location.pathname + '#category=' + catSlug;
      history.replaceState(null, '', newUrl);
    }
  }

  // Handle Tab Clicks
  tabs.forEach(function (tab) {
    tab.addEventListener('click', function (e) {
      e.preventDefault();
      var cat = tab.dataset.c;
      filterCategory(cat, true);
    });
  });

  // Check URL hash or query param on initial load
  var initialCat = 'all';
  var hashMatch = window.location.hash.match(/category=([a-z0-9-]+)/);
  if (hashMatch && hashMatch[1]) {
    initialCat = hashMatch[1];
  } else {
    var rawHash = window.location.hash.replace('#', '');
    if (rawHash && rawHash !== '') {
      var found = Array.from(tabs).some(function(t) { return t.dataset.c === rawHash; });
      if (found) initialCat = rawHash;
    }
  }

  var params = new URLSearchParams(window.location.search);
  if (params.has('category')) {
    initialCat = params.get('category');
  }

  if (initialCat !== 'all') {
    filterCategory(initialCat, false);
  }
});
</script>

<?php get_footer(); ?>
