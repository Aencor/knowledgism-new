<?php
/**
 * Block Name: Home Hero
 * Slug: home-hero
 * Description: Knowledgism Home Hero section with kicker, title, lede, 4-Zones bar, and upcoming events card.
 *
 * @package Knowledgism
 */

$block_id = !empty($block['anchor']) ? $block['anchor'] : 'home-hero-' . ($block['id'] ?? uniqid());
$block_class = 'home-hero-block';
if (!empty($block['className'])) {
  $block_class .= ' ' . $block['className'];
}

// ACF fields with fallbacks to prototype data
$kicker             = get_field('kicker') ?: __('The Knowledge Era', 'knowledgism');
$title              = get_field('title') ?: __('Knowledge without action does not produce results.', 'knowledgism');
$lede               = get_field('lede') ?: __('The written and recorded work of Alan C. Walter, organized so you can actually use it. Start by finding out which Zone you are operating from today.', 'knowledgism');
$primary_btn_text   = get_field('primary_btn_text') ?: __('Find your Zone — free', 'knowledgism');
$primary_btn_url    = get_field('primary_btn_url');
if (empty($primary_btn_url) || $primary_btn_url === '#profile' || $primary_btn_url === 'https://app.zonesoflife.com') {
  $primary_btn_url = 'https://app.zonesoflife.com/short-quiz/';
}
$secondary_btn_text = get_field('secondary_btn_text') ?: __('Download the free chapter', 'knowledgism');
$secondary_btn_url  = get_field('secondary_btn_url');
if (empty($secondary_btn_url) || $secondary_btn_url === '#profile' || $secondary_btn_url === home_url('/#profile')) {
  $secondary_btn_url = home_url('/wp-content/uploads/2020/07/THE_CLEAN_SLATE_SERIES_sample_chapter.pdf');
}
$show_zones         = get_field('show_zones');
if ($show_zones === null || $show_zones === '') {
  $show_zones = true;
}

$events_title       = get_field('events_title') ?: __('Upcoming events', 'knowledgism');
$events_btn_text    = get_field('events_btn_text') ?: __('See all events', 'knowledgism');
$events_btn_url     = get_field('events_btn_url') ?: home_url('/events/');

// Query 4 published events from Tribe Events Calendar, or fallback to canonical events from prototype
// Query upcoming published events from Tribe Events Calendar
$db_events = get_posts([
  'post_type'      => 'tribe_events',
  'posts_per_page' => 4,
  'post_status'    => 'publish',
  'meta_key'       => '_EventStartDate',
  'orderby'        => 'meta_value',
  'order'          => 'ASC',
  'meta_query'     => [
    [
      'key'     => '_EventStartDate',
      'value'   => date('Y-m-d H:i:s'),
      'compare' => '>=',
      'type'    => 'DATETIME',
    ],
  ],
]);

$hero_events = [];
if (!empty($db_events)) {
  foreach ($db_events as $ev) {
    $start_raw = get_post_meta($ev->ID, '_EventStartDate', true);
    $end_raw   = get_post_meta($ev->ID, '_EventEndDate', true);
    $cost_raw  = get_post_meta($ev->ID, '_EventCost', true);
    $start_ts  = $start_raw ? strtotime($start_raw) : time();
    $end_ts    = $end_raw ? strtotime($end_raw) : $start_ts;
    
    $start_d = date('j', $start_ts);
    $end_d   = date('j', $end_ts);
    $month   = date('M', $start_ts);
    $year    = date('Y', $start_ts);
    
    if (date('Y-m', $start_ts) === date('Y-m', $end_ts)) {
      $date_label = ($start_d === $end_d) ? ($start_d . ' ' . $month . ' ' . $year) : ($start_d . '–' . $end_d . ' ' . $month . ' ' . $year);
    } else {
      $date_label = $start_d . ' ' . $month . ' – ' . $end_d . ' ' . date('M', $end_ts) . ' ' . $year;
    }
    
    $price_label = !empty($cost_raw) ? ' · $' . ltrim($cost_raw, '$') : '';
    
    $hero_events[] = [
      'date'  => $date_label . $price_label,
      'title' => $ev->post_title,
      'url'   => get_permalink($ev->ID),
    ];
  }
}

// Supplement or fallback to prototype canonical events if less than 4
$canonical_prototype_events = [
  [
    'date'  => '5–6 Sep 2026',
    'title' => 'Redefine Your Self-Worth',
    'url'   => home_url('/events/'),
  ],
  [
    'date'  => '19–26 Sep 2026',
    'title' => 'Vital Fundamentals',
    'url'   => home_url('/events/'),
  ],
  [
    'date'  => '2–9 Nov 2026',
    'title' => 'Life Enhancement Course',
    'url'   => home_url('/events/'),
  ],
  [
    'date'  => '5–12 Dec 2026 · $3,997',
    'title' => 'Vital Fundamentals',
    'url'   => home_url('/events/'),
  ],
];

if (empty($hero_events)) {
  $hero_events = $canonical_prototype_events;
} else {
  // Ensure we always have 4 events in the card, exactly like the prototype
  foreach ($canonical_prototype_events as $proto_ev) {
    if (count($hero_events) >= 4) break;
    $hero_events[] = $proto_ev;
  }
}
?>

<div id="<?php echo esc_attr($block_id); ?>" class="hero dark <?php echo esc_attr($block_class); ?>">
  <div class="wrap split top">
    <div>
      <div class="kicker"><?php echo esc_html($kicker); ?></div>
      <h1 class="mt16"><?php echo esc_html($title); ?></h1>
      <p class="lede mt24" style="max-width:48ch"><?php echo esc_html($lede); ?></p>
      <div class="hgroup mt32" style="flex-wrap:wrap;gap:12px">
        <a class="btn btn-go" href="<?php echo esc_url($primary_btn_url); ?>" target="_blank" rel="noopener noreferrer">
          <?php echo esc_html($primary_btn_text); ?>
          <svg class="ic" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><use href="#i-arrow"/></svg>
        </a>
        <a class="btn btn-onDark" href="<?php echo esc_url($secondary_btn_url); ?>" target="_blank" rel="noopener noreferrer">
          <?php echo esc_html($secondary_btn_text); ?>
        </a>
      </div>
      <?php if ($show_zones) : ?>
        <div class="mt32" style="max-width:440px">
          <div class="zones dim">
            <i class="z1"></i><i class="z2"></i><i class="z3"></i><i class="z4"></i>
          </div>
          <div class="zlabels">
            <span class="tiny"><?php esc_html_e('Red', 'knowledgism'); ?></span>
            <span class="tiny"><?php esc_html_e('Yellow', 'knowledgism'); ?></span>
            <span class="tiny"><?php esc_html_e('Green', 'knowledgism'); ?></span>
            <span class="tiny"><?php esc_html_e('Golden Magic', 'knowledgism'); ?></span>
          </div>
        </div>
      <?php endif; ?>
    </div>
    
    <div class="card" style="background:#04202D;border-color:#12374A;color:#fff">
      <div class="tiny" style="color:var(--gold)">
        <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><use href="#i-calendar"/></svg>
        <?php echo esc_html($events_title); ?>
      </div>
      <?php 
      $total_events = count($hero_events);
      foreach ($hero_events as $idx => $hev) : 
        if ($idx === 0) {
          $item_style = 'border-bottom:1px solid #12374A;padding-bottom:12px;margin-top:16px;';
        } elseif ($idx === $total_events - 1) {
          $item_style = 'padding-top:12px;';
        } else {
          $item_style = 'border-bottom:1px solid #12374A;padding:12px 0;';
        }
      ?>
        <div style="<?php echo esc_attr($item_style); ?>">
          <div class="tiny" style="color:#7A8B9B"><?php echo esc_html($hev['date']); ?></div>
          <div style="font-weight:600;margin-top:3px">
            <a href="<?php echo esc_url($hev['url']); ?>" style="color:#fff;">
              <?php echo esc_html($hev['title']); ?>
            </a>
          </div>
        </div>
      <?php endforeach; ?>
      <a class="btn btn-onDark btn-sm btn-block mt16" href="<?php echo esc_url($events_btn_url); ?>">
        <?php echo esc_html($events_btn_text); ?>
      </a>
    </div>
  </div>
</div>
