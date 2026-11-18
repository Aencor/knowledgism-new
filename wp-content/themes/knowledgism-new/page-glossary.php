<?php
/**
 * Template Name: Training Glossary
 * Description: Interactive training glossary page with live search, alphabet filtering, and dictionary CTA matching prototype.
 *
 * @package Knowledgism
 */

get_header();

$terms = function_exists('knowledgism_get_glossary_terms') ? knowledgism_get_glossary_terms() : [];
$total_count = count($terms);

// Determine initial letter: check query param 'letter' or 'a' by default
$initial_letter = isset($_GET['letter']) ? strtoupper(sanitize_text_field(wp_unslash($_GET['letter']))) : 'A';
if (strlen($initial_letter) !== 1 && $initial_letter !== 'ALL') {
	$initial_letter = 'A';
}
?>

<div class="hero tint">
  <div class="wrap">
    <div class="kicker">
      <svg class="ic" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><use href="#i-book"/></svg>
      <?php esc_html_e('Training glossary', 'knowledgism'); ?>
    </div>
    <h1 class="mt16"><?php esc_html_e('Every word has an exact definition', 'knowledgism'); ?></h1>
    <p class="lede mt16" style="max-width:64ch">
      <?php esc_html_e('Compiled from the Knowledgism Dictionary. This is where the power of knowing begins: if the word is blurry, so is the concept.', 'knowledgism'); ?>
    </p>
  </div>
</div>

<main class="global-main site-content block">
  <div class="wrap">
    <div style="display:flex;gap:16px;flex-wrap:wrap;align-items:center;justify-content:space-between">
      <input class="input" id="glosSearch" type="search" placeholder="<?php esc_attr_e('Search a term…', 'knowledgism'); ?>" style="max-width:340px" autocomplete="off" aria-label="<?php esc_attr_e('Search terms or definitions', 'knowledgism'); ?>" />
      <span class="tiny" id="glosCount" style="color:var(--muted)">
        <?php echo sprintf(esc_html__('Showing %d definitions', 'knowledgism'), $total_count); ?>
      </span>
    </div>

    <!-- A-Z Alphabet Filter Bar -->
    <div class="azbar" id="azbar" role="toolbar" aria-label="<?php esc_attr_e('Alphabet Filter', 'knowledgism'); ?>"></div>

    <!-- Glossary Terms Grid -->
    <div class="g2" id="glosList">
      <?php
      // Initial server-side fallback render for letter A (SEO & instant load)
      $filtered_init = array_filter($terms, function($t) use ($initial_letter) {
        return ($initial_letter === 'ALL') || ($t['letter'] === $initial_letter);
      });
      if (empty($filtered_init) && !empty($terms)) {
        $filtered_init = array_slice($terms, 0, 15);
      }
      foreach ($filtered_init as $item) :
        $zone_badge = '';
        if (!empty($item['zone'])) {
          $color = ($item['zone'] === 'gold') ? 'var(--gold)' : (($item['zone'] === 'red') ? 'var(--red)' : 'var(--green)');
          $zone_badge = '<span class="tiny" style="color:' . esc_attr($color) . ';margin-left:8px;font-weight:600">' . esc_html(strtoupper($item['zone'])) . ' ZONE</span>';
        }
      ?>
        <div id="term-<?php echo esc_attr($item['slug']); ?>" style="padding:14px 0;border-top:1px solid var(--line)">
          <div style="font-weight:700;font-family:var(--mono);font-size:13px;letter-spacing:.06em">
            <?php echo esc_html($item['word']); ?>
            <?php echo $zone_badge; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
          </div>
          <p class="sm mt8" style="color:#4A5764;line-height:1.6">
            <?php echo wp_kses_post($item['meaning']); ?>
          </p>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Dictionary Upsell Card -->
    <div class="card mt32 split" style="background:var(--kn);color:#fff;border-color:var(--kn);align-items:center">
      <div>
        <div class="tiny" style="color:var(--gold)"><?php esc_html_e('Complete definitions', 'knowledgism'); ?></div>
        <h3 class="mt8" style="color:#fff"><?php esc_html_e('Knowledgism Dictionary', 'knowledgism'); ?></h3>
        <p class="sm mt8" style="color:rgba(255,255,255,.7)">
          <?php esc_html_e('The full reference the glossary is drawn from.', 'knowledgism'); ?>
        </p>
      </div>
      <div>
        <div class="price mb16" style="font-size:22px;color:#fff">$97.00</div>
        <a class="btn btn-primary btn-block" href="<?php echo esc_url(function_exists('wc_get_page_id') && wc_get_page_id('shop') > 0 ? get_permalink(wc_get_page_id('shop')) : home_url('/bookstore/')); ?>">
          <?php esc_html_e('View the dictionary', 'knowledgism'); ?>
        </a>
      </div>
    </div>
  </div>
</main>

<!-- Word of the Week Newsletter -->
<div class="block">
  <div class="wrap">
    <div class="newsband">
      <div class="split" style="align-items:center;gap:32px">
        <div>
          <div class="kicker">
            <svg class="ic" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><use href="#i-mail"/></svg>
            <?php esc_html_e('Free weekly', 'knowledgism'); ?>
          </div>
          <h3 class="mt8"><?php esc_html_e('The Word of the Week', 'knowledgism'); ?></h3>
          <p class="sm mt8">
            <?php esc_html_e('One term from the Knowledgism Dictionary, defined plainly, every Monday. Plus new articles and event dates before they are announced.', 'knowledgism'); ?>
          </p>
        </div>
        <div>
          <div class="newsForm">
            <div class="newsrow">
              <input class="input nMail" type="email" placeholder="jane@example.com" aria-label="<?php esc_attr_e('Email address', 'knowledgism'); ?>">
              <button class="btn btn-primary nGo"><?php esc_html_e('Subscribe', 'knowledgism'); ?></button>
            </div>
            <label class="xs" style="display:flex;gap:9px;align-items:flex-start;margin-top:12px">
              <input type="checkbox" class="nOk" style="margin-top:3px">
              <?php esc_html_e('I agree to the Privacy Policy. Unsubscribe anytime.', 'knowledgism'); ?>
            </label>
          </div>
          <div class="newsDone" hidden>
            <h4 style="color:var(--shgreen)"><?php esc_html_e('You are subscribed', 'knowledgism'); ?></h4>
            <p class="sm mt8 newsTxt"></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script id="glossary-dataset" type="application/json">
<?php echo wp_json_encode($terms, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>
</script>

<?php get_footer(); ?>
