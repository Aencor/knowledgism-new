<?php
/**
 * The template for displaying single Tribe Events.
 * Styled to match the Knowledgism Prototype and site aesthetic:
 * - Clean hero with calendar kicker, title, date/format lede, and metadata chips
 * - Two-column split layout with sticky booking sidebar card
 * - Featured image with signature 4-Zones level bar
 * - Key highlights grid (dates, schedule, format, location)
 * - Rich course description & curriculum breakdown
 * - Immersion environment / online platform details
 * - About Alan C. Walter & ACLC
 * - Calendar export (Google Calendar + direct iCal .ics download)
 * - Free Zone Profile CTA
 * - Upcoming events cross-navigation
 *
 * @package Knowledgism
 */

// Handle ICS export if requested via ?ics=1
if (isset($_GET['ics'])) {
  $event_id = get_queried_object_id();
  $start_raw = get_post_meta($event_id, '_EventStartDate', true);
  $end_raw   = get_post_meta($event_id, '_EventEndDate', true);
  $start_ts  = $start_raw ? strtotime($start_raw) : time();
  $end_ts    = $end_raw ? strtotime($end_raw) : ($start_ts + 7200);

  $summary  = get_the_title($event_id);
  $event_post = get_post($event_id);
  $desc     = $event_post ? wp_strip_all_tags($event_post->post_excerpt) : '';
  $location = 'Letoli Ranch, Saint Jo, Texas';
  if (stripos($summary, 'online') !== false || ($event_post && stripos($event_post->post_content, 'zoom') !== false)) {
    $location = 'Live Online via Zoom';
  }

  header('Content-Type: text/calendar; charset=utf-8');
  header('Content-Disposition: attachment; filename="knowledgism-event-' . $event_id . '.ics"');
  echo "BEGIN:VCALENDAR\r\n";
  echo "VERSION:2.0\r\n";
  echo "PRODID:-//Knowledgism//Events//EN\r\n";
  echo "CALSCALE:GREGORIAN\r\n";
  echo "BEGIN:VEVENT\r\n";
  echo "UID:" . $event_id . "-" . $start_ts . "@knowledgism.com\r\n";
  echo "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n";
  echo "DTSTART:" . gmdate('Ymd\THis\Z', $start_ts) . "\r\n";
  echo "DTEND:" . gmdate('Ymd\THis\Z', $end_ts) . "\r\n";
  echo "SUMMARY:" . addcslashes($summary, ",;\\") . "\r\n";
  echo "DESCRIPTION:" . addcslashes($desc, ",;\\") . "\r\n";
  echo "LOCATION:" . addcslashes($location, ",;\\") . "\r\n";
  echo "URL:" . get_permalink($event_id) . "\r\n";
  echo "END:VEVENT\r\n";
  echo "END:VCALENDAR\r\n";
  exit;
}

get_header();

while (have_posts()) : the_post();
  $event_id = get_the_ID();
  $title    = get_the_title();
  $content  = get_the_content();
  $excerpt  = get_the_excerpt();

  // 1. Extract dates
  $start_raw = get_post_meta($event_id, '_EventStartDate', true);
  $end_raw   = get_post_meta($event_id, '_EventEndDate', true);
  $all_day   = (bool) get_post_meta($event_id, '_EventAllDay', true);
  $start_ts  = $start_raw ? strtotime($start_raw) : time();
  $end_ts    = $end_raw ? strtotime($end_raw) : $start_ts;

  $start_month_short = strtoupper(date('M', $start_ts));
  $start_month_full  = date('F', $start_ts);
  $start_day         = date('j', $start_ts);
  $end_day           = date('j', $end_ts);
  $end_month_full    = date('F', $end_ts);
  $year              = date('Y', $start_ts);

  // Formatted date span
  if (date('Y-m', $start_ts) === date('Y-m', $end_ts)) {
    if ($start_day === $end_day) {
      $date_span = sprintf('%s %s, %s', $start_month_full, $start_day, $year);
    } else {
      $date_span = sprintf('%s %s–%s, %s', $start_month_full, $start_day, $end_day, $year);
    }
  } else {
    $date_span = sprintf('%s %s – %s %s, %s', $start_month_full, $start_day, $end_month_full, $end_day, $year);
  }

  // Detect special multi-day pattern from excerpt/content (e.g. "November 2, 3, 4, 8, 9 - 2026")
  if (preg_match('/([A-Za-z]+\s+\d+(?:,\s*\d+)*(?:\s*&\s*\d+)?\s*[–\-]\s*\d{4})/i', $excerpt, $dm)) {
    $date_span_hero = trim($dm[1]);
  } elseif (preg_match('/([A-Za-z]+\s+\d+(?:,\s*\d+)*(?:\s*&\s*\d+)?\s*[–\-]\s*\d{4})/i', $content, $dm)) {
    $date_span_hero = trim($dm[1]);
  } else {
    $date_span_hero = $date_span;
  }

  // 2. Schedule & Time
  $time_str = '';
  $tz = get_post_meta($event_id, '_EventTimezoneAbbr', true) ?: 'US Central';
  if (preg_match('/(\d{1,2}:\d{2}\s*(?:AM|PM)\s*(?:–|-|to)\s*\d{1,2}:\d{2}\s*(?:AM|PM)(?:\s*\([^\)]+\))?)/i', $content . ' ' . $excerpt, $tm)) {
    $time_str = trim($tm[1]);
  } elseif (!$all_day && date('H:i', $start_ts) !== '00:00') {
    $time_str = date('g:i A', $start_ts) . ' – ' . date('g:i A', $end_ts) . ' ' . $tz;
  } else {
    $time_str = __('Full-day immersion schedule', 'knowledgism');
  }

  // 3. Format & Location
  $is_in_person = (stripos($title, 'vital') !== false || stripos($content, 'Letoli') !== false || stripos($content, 'Ranch') !== false);
  if ($is_in_person) {
    $format_badge = __('In Person Immersion', 'knowledgism');
    $venue_title  = __('Letoli Ranch', 'knowledgism');
    $venue_loc    = __('Saint Jo, Texas (North Texas Hill Country)', 'knowledgism');
    $format_desc  = __('Intensive residential retreat at Letoli Ranch with secluded focus and private grounds.', 'knowledgism');
    $location_str = 'Letoli Ranch, Saint Jo, Texas';
  } else {
    $format_badge = __('Live Online Course', 'knowledgism');
    $venue_title  = __('Live Online via Zoom', 'knowledgism');
    $venue_loc    = __('Interactive live cohorts with personal drill coaching', 'knowledgism');
    $format_desc  = __('Delivered live with interactive coaching, practical drills and pair exercises.', 'knowledgism');
    $location_str = 'Live Online via Zoom';
  }

  // 4. Pricing
  $cost_raw = get_post_meta($event_id, '_EventCost', true);
  if (empty($cost_raw) && function_exists('tribe_get_cost')) {
    $cost_raw = tribe_get_cost($event_id);
  }
  $cost_display = '';
  if (!empty($cost_raw) && $cost_raw !== '0') {
    $cost_clean = ltrim(trim($cost_raw), '$');
    $cost_display = '$' . $cost_clean;
  }

  // 5. Links (Cart registration & Landing page)
  $register_url = '';
  if (preg_match('/https:\/\/cart\.knowledgism\.com\/[^\s"\'<>]+/i', $content, $cm)) {
    $register_url = $cm[0];
  }
  $landing_url = '';
  if (preg_match('/https:\/\/landing\.knowledgism\.com\/[^\s"\'<>]+/i', $content, $lm)) {
    $landing_url = $lm[0];
  }
  if (empty($register_url)) {
    $event_url_meta = get_post_meta($event_id, '_EventURL', true);
    if (!empty($event_url_meta)) {
      if (strpos($event_url_meta, 'cart') !== false) {
        $register_url = $event_url_meta;
      } elseif (empty($landing_url)) {
        $landing_url = $event_url_meta;
      }
    }
  }
  if (empty($register_url)) {
    $register_url = !empty($landing_url) ? $landing_url : home_url('/#contact');
  }

  // 6. Target Zone Level
  $zone_level = 2;
  $zone_desc = __('Intermediate level — designed for the Yellow and Green Zones', 'knowledgism');
  if (stripos($title, 'vital') !== false) {
    $zone_level = 4;
    $zone_desc  = __('Mastery level — designed for the Green and Golden Magic Zones', 'knowledgism');
  } elseif (stripos($title, 'enhancement') !== false) {
    $zone_level = 3;
    $zone_desc  = __('Advanced practical level — designed for the Green Zone', 'knowledgism');
  } elseif (stripos($title, 'worth') !== false || stripos($title, 'confidence') !== false) {
    $zone_level = 2;
    $zone_desc  = __('Foundational level — designed for the Red and Yellow Zones', 'knowledgism');
  }

  // 7. Categories
  $terms = wp_get_post_terms($event_id, 'tribe_events_cat');
  $category_name = !empty($terms) ? $terms[0]->name : __('Live Programme', 'knowledgism');

  // 8. Google Calendar URL
  $gcal_start = gmdate('Ymd\THis\Z', $start_ts);
  $gcal_end   = gmdate('Ymd\THis\Z', $end_ts > $start_ts ? $end_ts : ($start_ts + 7200));
  $gcal_url   = 'https://calendar.google.com/calendar/render?' . http_build_query([
    'action'   => 'TEMPLATE',
    'text'     => $title,
    'dates'    => $gcal_start . '/' . $gcal_end,
    'details'  => sprintf("%s\n\n%s\n%s", $title, $format_desc, get_permalink($event_id)),
    'location' => $location_str,
  ]);
  $ics_url = add_query_arg('ics', '1', get_permalink($event_id));

  // 9. Featured image
  $thumb_url = has_post_thumbnail() ? get_the_post_thumbnail_url($event_id, 'large') : '';

  // 10. Clean editorial description
  // If the DB post has only raw links/blocks, provide rich canonical course details
  $course_overview = '';
  $course_learn = [];
  $course_who = '';

  if (stripos($title, 'vital') !== false) {
    $course_overview = __('Vital Fundamentals is Knowledgism\'s flagship residential immersion. Held at Letoli Ranch in Saint Jo, Texas, this 7-day intensive is designed to help you regain full command over your presence, dissolve subconscious charges, and experience the highest operating states described in Alan C. Walter\'s research. You will work directly with master facilitators through precise, step-by-step processing and drills that produce permanent certainty.', 'knowledgism');
    $course_learn = [
      __('Master total presence and recover hijacked life-force energy', 'knowledgism'),
      __('Dissolve emotional charges, past failures, and chronic points of overwhelm', 'knowledgism'),
      __('Operate with genuine sovereign power across business, relationships, and leadership', 'knowledgism'),
      __('Direct, personalized processing in an uninterrupted, peaceful ranch environment', 'knowledgism'),
    ];
    $course_who = __('Recommended for high-achieving leaders, executives, and individuals who are ready to transition out of survival or frantic doingness into true Green and Gold Zone mastery.', 'knowledgism');
  } elseif (stripos($title, 'enhancement') !== false) {
    $course_overview = __('The Life Enhancement Course is an intensive live online training delivered in five focused sessions. Based directly on Alan C. Walter’s core technology, it equips you with practical tools to identify where your life-force is being drained and systematically upgrade every key dynamic: personal purpose, close relationships, financial clarity, and physical vitality.', 'knowledgism');
    $course_learn = [
      __('Map your personal operating matrix and identify blind spots holding you back', 'knowledgism'),
      __('Learn and apply the exact formulas for resolving conflict and miscommunications', 'knowledgism'),
      __('Establish unwavering clarity over your goals, priorities, and daily choices', 'knowledgism'),
      __('Interactive pair drills with real-time feedback and structured exercise workbooks', 'knowledgism'),
    ];
    $course_who = __('Ideal for anyone looking to raise their everyday operational energy, eliminate recurrent friction in work or relationships, and establish durable stability.', 'knowledgism');
  } elseif (stripos($title, 'worth') !== false || stripos($title, 'confidence') !== false) {
    $course_overview = __('Redefine Your Self-Worth is an intensive live online workshop designed to isolate and resolve the core barriers to natural confidence. Real self-worth is not affirmations or surface motivation; it is the natural consequence of integrity, certainty, and operating from truth. This weekend intensive provides the exact technology to reconstruct healthy boundaries and genuine self-respect.', 'knowledgism');
    $course_learn = [
      __('Uncover the hidden sources of self-doubt, second-guessing, and impostor feeling', 'knowledgism'),
      __('Restore internal integrity and eliminate dependency on external approval', 'knowledgism'),
      __('Build unshakable conviction in your capabilities, decisions, and boundaries', 'knowledgism'),
      __('Guided live exercises with practical tools you keep and use for life', 'knowledgism'),
    ];
    $course_who = __('Essential for anyone who experiences recurring self-doubt, hesitates when stepping into larger opportunities, or wants to anchor deep self-respect.', 'knowledgism');
  } else {
    $course_overview = !empty($excerpt) ? $excerpt : __('A transformative live learning immersion based on the research and methodology of Alan C. Walter, providing practical drills and direct processing to achieve permanent personal certainty.', 'knowledgism');
    $course_learn = [
      __('Direct hands-on applications of Knowledgism technology', 'knowledgism'),
      __('Practical drills designed to increase presence and awareness', 'knowledgism'),
      __('Personal guidance and facilitation tailored to your current operating Zone', 'knowledgism'),
    ];
    $course_who = __('Open to all individuals committed to achieving personal mastery, sovereign integrity, and peak operational energy.', 'knowledgism');
  }
?>

<!-- Hero Section -->
<div class="hero tint event-hero">
  <div class="wrap">
    <!-- Breadcrumb back link -->
    <a href="<?php echo esc_url(home_url('/events/')); ?>" class="event-back-link">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><use href="#i-arrow" transform="rotate(180 12 12)"/></svg>
      <?php esc_html_e('Calendar & All Events', 'knowledgism'); ?>
    </a>

    <div class="kicker mt16">
      <svg class="ic" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><use href="#i-calendar"/></svg>
      <?php echo esc_html($category_name); ?> &middot; <?php echo esc_html($format_badge); ?>
    </div>

    <h1 class="mt16 event-hero-title"><?php echo esc_html($title); ?></h1>

    <p class="lede mt16 event-hero-lede" style="max-width:68ch">
      <?php echo esc_html($date_span_hero); ?> &middot; <?php echo esc_html($venue_title); ?>
    </p>

    <!-- Event Quick Meta Chips -->
    <div class="event-meta-chips mt24">
      <span class="event-chip">
        <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><use href="#i-calendar"/></svg>
        <?php echo esc_html($date_span); ?>
      </span>
      <span class="event-chip">
        <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><use href="#i-pin"/></svg>
        <?php echo esc_html($venue_title); ?>
      </span>
      <span class="event-chip">
        <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><use href="#i-target"/></svg>
        <?php echo sprintf(esc_html__('Level %d Programme', 'knowledgism'), $zone_level); ?>
      </span>
      <?php if (!empty($cost_display)) : ?>
        <span class="event-chip event-chip-price">
          <?php echo esc_html($cost_display); ?>
        </span>
      <?php else : ?>
        <span class="pending" style="font-size:11px;padding:5px 10px;">
          <?php esc_html_e('Price TBD', 'knowledgism'); ?>
        </span>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Main Event Section -->
<div class="block">
  <div class="wrap split top">
    
    <!-- Left Column: Media, Details, Curriculum, Experience -->
    <div class="event-main-content">
      
      <!-- Featured Image Banner with Signature Zones Bar -->
      <?php if ($thumb_url) : ?>
        <div class="event-cover-box">
          <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php the_title_attribute(); ?>" class="event-cover-img" />
          <div class="zones dim event-cover-zones" style="height:6px;width:100%;display:flex;gap:3px;border-radius:3px;overflow:hidden;margin-top:12px;">
            <i class="z1 <?php echo $zone_level >= 1 ? 'on' : ''; ?>" style="flex:1;background:var(--red);"></i>
            <i class="z2 <?php echo $zone_level >= 2 ? 'on' : ''; ?>" style="flex:1;background:var(--amber);"></i>
            <i class="z3 <?php echo $zone_level >= 3 ? 'on' : ''; ?>" style="flex:1;background:var(--green);"></i>
            <i class="z4 <?php echo $zone_level >= 4 ? 'on' : ''; ?>" style="flex:1;background:var(--gold);"></i>
          </div>
          <div class="tiny mt8" style="color:var(--muted);"><?php echo esc_html($zone_desc); ?></div>
        </div>
      <?php else : ?>
        <div class="event-zones-card mb24">
          <div class="zones dim" style="height:6px;width:100%;display:flex;gap:3px;border-radius:3px;overflow:hidden;">
            <i class="z1 <?php echo $zone_level >= 1 ? 'on' : ''; ?>" style="flex:1;background:var(--red);"></i>
            <i class="z2 <?php echo $zone_level >= 2 ? 'on' : ''; ?>" style="flex:1;background:var(--amber);"></i>
            <i class="z3 <?php echo $zone_level >= 3 ? 'on' : ''; ?>" style="flex:1;background:var(--green);"></i>
            <i class="z4 <?php echo $zone_level >= 4 ? 'on' : ''; ?>" style="flex:1;background:var(--gold);"></i>
          </div>
          <div class="tiny mt8" style="color:var(--muted);"><?php echo esc_html($zone_desc); ?></div>
        </div>
      <?php endif; ?>

      <!-- 4 Highlights Grid -->
      <div class="g2 mt24 event-quick-grid">
        <div class="card event-stat-card">
          <div class="iconbox mb12">
            <svg class="ic" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><use href="#i-calendar"/></svg>
          </div>
          <div class="tiny" style="color:var(--muted);"><?php esc_html_e('Dates', 'knowledgism'); ?></div>
          <div class="event-stat-val mt4"><?php echo esc_html($date_span); ?></div>
        </div>

        <div class="card event-stat-card">
          <div class="iconbox mb12">
            <svg class="ic" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><use href="#i-clock"/></svg>
          </div>
          <div class="tiny" style="color:var(--muted);"><?php esc_html_e('Schedule', 'knowledgism'); ?></div>
          <div class="event-stat-val mt4"><?php echo esc_html($time_str); ?></div>
        </div>

        <div class="card event-stat-card">
          <div class="iconbox mb12">
            <svg class="ic" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><use href="#i-pin"/></svg>
          </div>
          <div class="tiny" style="color:var(--muted);"><?php esc_html_e('Location', 'knowledgism'); ?></div>
          <div class="event-stat-val mt4"><?php echo esc_html($venue_title); ?></div>
        </div>

        <div class="card event-stat-card">
          <div class="iconbox mb12">
            <svg class="ic" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><use href="#i-users"/></svg>
          </div>
          <div class="tiny" style="color:var(--muted);"><?php esc_html_e('Format', 'knowledgism'); ?></div>
          <div class="event-stat-val mt4"><?php echo esc_html($format_badge); ?></div>
        </div>
      </div>

      <!-- Course Overview & Editorial Narrative -->
      <div class="mt32 event-section">
        <h2 style="font-size:24px;"><?php esc_html_e('About This Programme', 'knowledgism'); ?></h2>
        <div class="entry-content mt16" style="line-height:1.75;color:#3D4A57;">
          <p><?php echo esc_html($course_overview); ?></p>
        </div>
      </div>

      <!-- What You Will Learn / Breakthroughs -->
      <div class="mt32 event-section">
        <h3 style="font-size:20px;"><?php esc_html_e('Key Outcomes & Breakthroughs', 'knowledgism'); ?></h3>
        <div class="event-outcomes-list mt16">
          <?php foreach ($course_learn as $outcome) : ?>
            <div class="event-outcome-item">
              <span class="event-outcome-icon">
                <svg class="ic" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><use href="#i-check"/></svg>
              </span>
              <div class="event-outcome-text"><?php echo esc_html($outcome); ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Who Is This Programme For? -->
      <div class="card tint mt32" style="background:var(--tint);border-radius:10px;padding:24px;">
        <div class="tiny mb8" style="color:var(--muted);font-weight:700;">
          <?php esc_html_e('Prerequisites & Fit', 'knowledgism'); ?>
        </div>
        <h4 style="font-family:var(--display);font-size:18px;font-weight:600;">
          <?php esc_html_e('Who this programme is built for', 'knowledgism'); ?>
        </h4>
        <p class="sm mt8" style="color:#4A5764;line-height:1.65;">
          <?php echo esc_html($course_who); ?>
        </p>
      </div>

      <!-- Venue / Delivery Setting Card -->
      <div class="card mt32 pad-lg" style="background:#fff;border:1px solid var(--line);border-radius:10px;">
        <div class="split" style="align-items:start;grid-template-columns:auto 1fr;gap:20px;">
          <div class="iconbox" style="background:var(--navysoft);color:var(--brand);width:52px;height:52px;">
            <svg class="ic" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <?php if ($is_in_person) : ?>
                <use href="#i-pin"/>
              <?php else : ?>
                <use href="#i-play"/>
              <?php endif; ?>
            </svg>
          </div>
          <div>
            <div class="tiny" style="color:var(--muted);"><?php esc_html_e('Setting & Delivery', 'knowledgism'); ?></div>
            <h3 class="mt4" style="font-size:19px;"><?php echo esc_html($venue_title); ?></h3>
            <p class="sm mt8" style="color:#4A5764;line-height:1.65;">
              <?php if ($is_in_person) : ?>
                <?php esc_html_e('Live immersions are delivered at Letoli Ranch, an intentional sanctuary located in Saint Jo, Texas. Set in open, tranquil ranch country away from the noise of city life, the facility provides an optimal environment for intense focus, clear presence, and lasting transformation. Lodging, peaceful grounds, and dedicated facilitation rooms are integrated on site.', 'knowledgism'); ?>
              <?php else : ?>
                <?php esc_html_e('Live online programmes are delivered directly over Zoom with high-engagement interactive facilitation. Rather than passive webinars, sessions feature live drills, small breakout pairing exercises, personal correction from trained coaches, and real-time Q&A. Detailed exercise materials and workbook sheets are supplied prior to the start date.', 'knowledgism'); ?>
              <?php endif; ?>
            </p>
          </div>
        </div>
      </div>

      <!-- About Alan C. Walter and ACLC -->
      <div class="mt32 event-section">
        <div class="tiny mb8" style="color:var(--muted);"><?php esc_html_e('Methodology & Lineage', 'knowledgism'); ?></div>
        <h3 style="font-size:20px;"><?php esc_html_e('Delivered by the Advanced Coaching & Leadership Center', 'knowledgism'); ?></h3>
        <p class="sm mt12" style="color:#4A5764;line-height:1.68;">
          <?php esc_html_e('Knowledgism programmes are built on more than four decades of rigorous philosophical, operational, and spiritual research by Alan C. Walter. Training is facilitated exclusively by senior coaches certified by the Advanced Coaching & Leadership Center (ACLC), ensuring complete fidelity to the original technology.', 'knowledgism'); ?>
        </p>
      </div>

      <!-- Add to Calendar Bar -->
      <div class="event-cal-bar mt32">
        <div class="tiny mb8" style="color:var(--muted);"><?php esc_html_e('Save this date', 'knowledgism'); ?></div>
        <div class="event-cal-buttons">
          <a class="btn btn-ghost btn-sm" href="<?php echo esc_url($gcal_url); ?>" target="_blank" rel="noopener noreferrer">
            <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><use href="#i-calendar"/></svg>
            <?php esc_html_e('Add to Google Calendar', 'knowledgism'); ?>
          </a>
          <a class="btn btn-ghost btn-sm" href="<?php echo esc_url($ics_url); ?>" download>
            <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><use href="#i-arrow" transform="rotate(90 12 12)"/></svg>
            <?php esc_html_e('Download iCal (.ics)', 'knowledgism'); ?>
          </a>
        </div>
      </div>

    </div>

    <!-- Right Column: Sticky Booking Card & Cross-Sells -->
    <aside class="event-sidebar">
      
      <!-- Sticky Primary Booking Card -->
      <div class="card event-booking-card">
        
        <!-- Date callout box -->
        <div class="event-booking-header">
          <div class="datebox">
            <div class="tiny" style="font-weight:700;color:var(--muted);"><?php echo esc_html($start_month_short); ?></div>
            <div class="d" style="font-family:var(--display);font-size:26px;font-weight:600;line-height:1;margin-top:2px;">
              <?php echo esc_html($start_day); ?>
            </div>
          </div>
          <div class="grow" style="margin-left:14px;">
            <div style="font-weight:700;font-size:16px;color:var(--ink);">
              <?php echo esc_html($date_span); ?>
            </div>
            <div class="xs mt4" style="color:var(--muted);">
              <?php echo esc_html($format_badge); ?>
            </div>
          </div>
        </div>

        <hr class="rule" style="margin:18px 0;">

        <!-- Quick Specs List -->
        <div class="event-specs-list">
          <div class="event-spec-item">
            <div class="tiny" style="color:var(--muted);"><?php esc_html_e('Schedule / Times', 'knowledgism'); ?></div>
            <div class="sm mt2" style="font-weight:600;color:var(--ink);"><?php echo esc_html($time_str); ?></div>
          </div>

          <div class="event-spec-item mt12">
            <div class="tiny" style="color:var(--muted);"><?php esc_html_e('Location / Venue', 'knowledgism'); ?></div>
            <div class="sm mt2" style="font-weight:600;color:var(--ink);"><?php echo esc_html($venue_title); ?></div>
            <div class="xs" style="color:var(--muted);"><?php echo esc_html($venue_loc); ?></div>
          </div>

          <div class="event-spec-item mt12">
            <div class="tiny" style="color:var(--muted);"><?php esc_html_e('Target Level', 'knowledgism'); ?></div>
            <div class="sm mt2" style="font-weight:600;color:var(--ink);"><?php echo sprintf(esc_html__('Level %d (%s)', 'knowledgism'), $zone_level, $zone_level >= 3 ? 'Green/Gold' : 'Yellow/Green'); ?></div>
          </div>
        </div>

        <hr class="rule" style="margin:18px 0;">

        <!-- Pricing Block -->
        <div class="event-price-block">
          <div class="tiny" style="color:var(--muted);margin-bottom:6px;"><?php esc_html_e('Registration & Tuition', 'knowledgism'); ?></div>
          <?php if (!empty($cost_display)) : ?>
            <div class="event-price-val" style="font-family:var(--mono);font-size:30px;font-weight:700;color:var(--ink);line-height:1;">
              <?php echo esc_html($cost_display); ?>
            </div>
            <div class="xs mt6" style="color:var(--muted);">
              <?php esc_html_e('Includes all materials, workbook & briefing.', 'knowledgism'); ?>
            </div>
          <?php else : ?>
            <div>
              <span class="pending" style="font-size:12px;padding:6px 12px;">
                <?php esc_html_e('Tuition Price TBD', 'knowledgism'); ?>
              </span>
            </div>
            <div class="xs mt6" style="color:var(--muted);">
              <?php esc_html_e('Early registration details available through admissions.', 'knowledgism'); ?>
            </div>
          <?php endif; ?>
        </div>

        <!-- Booking Actions -->
        <div class="mt20">
          <a class="btn btn-go btn-block" href="<?php echo esc_url($register_url); ?>" style="font-size:15px;padding:14px 20px;">
            <?php echo !empty($cost_display) ? esc_html__('Book Your Spot', 'knowledgism') : esc_html__('Register & Enquire', 'knowledgism'); ?>
          </a>

          <?php if (!empty($landing_url) && $landing_url !== $register_url) : ?>
            <a class="btn btn-ghost btn-sm btn-block mt10" href="<?php echo esc_url($landing_url); ?>" target="_blank" rel="noopener noreferrer">
              <?php esc_html_e('Full Programme Landing Page', 'knowledgism'); ?> &rarr;
            </a>
          <?php endif; ?>

          <div class="tiny center mt12" style="color:var(--muted);line-height:1.4;">
            <?php esc_html_e('Direct booking · Limited class size for personal coaching', 'knowledgism'); ?>
          </div>
        </div>

        <!-- Help / Admissions Prompt -->
        <div class="event-support-box mt20" style="background:var(--navysoft);padding:12px 14px;border-radius:8px;">
          <div class="xs" style="color:#2B3A48;line-height:1.45;">
            <strong><?php esc_html_e('Questions about attendance?', 'knowledgism'); ?></strong><br>
            <?php esc_html_e('Talk directly with our admissions team.', 'knowledgism'); ?>
            <a href="<?php echo esc_url(home_url('/contact/')); ?>" style="color:var(--brand);font-weight:600;text-decoration:underline;">
              <?php esc_html_e('Contact Us', 'knowledgism'); ?> &rarr;
            </a>
          </div>
        </div>

      </div>

      <!-- Free Zone Profile Card -->
      <div class="card tint mt24" style="background:var(--tint);border:1px solid transparent;border-radius:10px;padding:22px;">
        <div class="zones dim" style="height:5px;width:100%;margin-bottom:12px;border-radius:3px;overflow:hidden;display:flex;gap:2px;">
          <i class="z1 on" style="flex:1;background:var(--red);"></i>
          <i class="z2 on" style="flex:1;background:var(--amber);"></i>
          <i class="z3 on" style="flex:1;background:var(--green);"></i>
          <i class="z4 on" style="flex:1;background:var(--gold);"></i>
        </div>
        <h4 style="font-family:var(--display);font-size:16px;font-weight:600;line-height:1.3;">
          <?php esc_html_e('Not sure which event fits where you are?', 'knowledgism'); ?>
        </h4>
        <p class="xs mt8" style="color:#5A6875;line-height:1.55;">
          <?php esc_html_e('Fourteen questions, two minutes. Find your current Zone and discover which programme is tailored to your next breakthrough. 100% free.', 'knowledgism'); ?>
        </p>
        <a class="btn btn-go btn-sm btn-block mt16" href="https://app.zonesoflife.com/short-quiz/" target="_blank" rel="noopener noreferrer">
          <?php esc_html_e('Find your Zone', 'knowledgism'); ?>
        </a>
      </div>

      <!-- Other Upcoming Events Mini-List -->
      <?php
      $other_events = get_posts([
        'post_type'      => 'tribe_events',
        'posts_per_page' => 3,
        'post__not_in'   => [$event_id],
        'post_status'    => 'publish',
        'orderby'        => 'meta_value',
        'meta_key'       => '_EventStartDate',
        'order'          => 'ASC',
      ]);

      if (!empty($other_events)) : ?>
        <div class="card mt24" style="background:#fff;border:1px solid var(--line);border-radius:10px;padding:20px;">
          <div class="tiny mb12" style="font-weight:700;color:var(--muted);"><?php esc_html_e('Other Upcoming Dates', 'knowledgism'); ?></div>
          <div class="event-other-list">
            <?php foreach ($other_events as $oe) : 
              $oe_start = get_post_meta($oe->ID, '_EventStartDate', true);
              $oe_ts    = $oe_start ? strtotime($oe_start) : time();
              $oe_cost  = get_post_meta($oe->ID, '_EventCost', true);
            ?>
              <div class="event-other-item" style="padding:10px 0;border-bottom:1px solid var(--line);">
                <div class="split" style="grid-template-columns:auto 1fr;gap:12px;align-items:center;">
                  <div class="datebox" style="width:48px;">
                    <div class="tiny" style="font-size:9px;color:var(--muted);"><?php echo esc_html(strtoupper(date('M', $oe_ts))); ?></div>
                    <div class="d" style="font-size:18px;"><?php echo esc_html(date('j', $oe_ts)); ?></div>
                  </div>
                  <div>
                    <a href="<?php echo esc_url(get_permalink($oe->ID)); ?>" style="font-weight:600;font-size:13.5px;color:var(--ink);display:block;line-height:1.3;">
                      <?php echo esc_html($oe->post_title); ?>
                    </a>
                    <div class="xs mt2" style="color:var(--muted);font-size:11.5px;">
                      <?php echo !empty($oe_cost) ? '$' . esc_html(ltrim($oe_cost, '$')) : __('Price TBD', 'knowledgism'); ?>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          <a class="btn btn-ghost btn-sm btn-block mt16" href="<?php echo esc_url(home_url('/events/')); ?>">
            <?php esc_html_e('View Full Calendar', 'knowledgism'); ?> &rarr;
          </a>
        </div>
      <?php endif; ?>

    </aside>

  </div>
</div>

<!-- Bottom Wide CTA Banner -->
<div class="block" style="background:var(--tint);border-top:1px solid var(--line);">
  <div class="wrap">
    <div class="card pad-lg split" style="background:#fff;border:1px solid var(--line);border-radius:12px;align-items:center;">
      <div>
        <div class="kicker"><svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><use href="#i-calendar"/></svg> <?php esc_html_e('Knowledgism Live Immersions', 'knowledgism'); ?></div>
        <h3 class="mt8" style="font-family:var(--display);font-size:22px;font-weight:600;">
          <?php esc_html_e('The best way to learn it is to do it.', 'knowledgism'); ?>
        </h3>
        <p class="sm mt8" style="color:#4A5764;max-width:54ch;">
          <?php esc_html_e('Every live event delivers direct, experiential certainty. Find your operating Zone today or speak with our team about private coaching at Letoli Ranch.', 'knowledgism'); ?>
        </p>
      </div>
      <div style="display:flex;gap:12px;flex-wrap:wrap;justify-content:flex-end;">
        <a class="btn btn-ghost" href="<?php echo esc_url(home_url('/contact/')); ?>">
          <?php esc_html_e('Talk to Admissions', 'knowledgism'); ?>
        </a>
        <a class="btn btn-go" href="https://app.zonesoflife.com/short-quiz/" target="_blank" rel="noopener noreferrer">
          <?php esc_html_e('Find your Zone', 'knowledgism'); ?>
        </a>
      </div>
    </div>
  </div>
</div>

<?php
endwhile;

get_footer();
