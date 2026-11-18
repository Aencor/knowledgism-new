<?php
/**
 * The template for displaying Events (Calendar archive).
 * Styled to match the Knowledgism Prototype Events section.
 *
 * @package Knowledgism
 */

get_header();

// Canonical events from prototype
$canonical_events = [
  [
    'month'    => 'SEP',
    'day'      => '5',
    'title'    => 'Redefine Your Self-Worth',
    'subtitle' => '5 & 6 September &middot; live online',
    'price'    => null, // TBD
    'action'   => 'Details',
    'btn_cls'  => 'btn-ghost',
    'highlight'=> false,
    'slug'     => 'redefine-your-self-worth',
  ],
  [
    'month'    => 'SEP',
    'day'      => '19',
    'title'    => 'Vital Fundamentals',
    'subtitle' => '19&ndash;26 September &middot; in person, Letoli Ranch',
    'price'    => null, // TBD
    'action'   => 'Details',
    'btn_cls'  => 'btn-ghost',
    'highlight'=> false,
    'slug'     => 'vital-fundamentals-september-19-to-26-2026',
  ],
  [
    'month'    => 'NOV',
    'day'      => '2',
    'title'    => 'Life Enhancement Course',
    'subtitle' => '2, 3, 4, 8 & 9 November &middot; 7:00&ndash;12:00 US Central',
    'price'    => null, // TBD
    'action'   => 'Details',
    'btn_cls'  => 'btn-ghost',
    'highlight'=> false,
    'slug'     => 'life-enhancement-course-november-2026',
  ],
  [
    'month'    => 'DEC',
    'day'      => '5',
    'title'    => 'Vital Fundamentals',
    'subtitle' => '5&ndash;12 December &middot; in person, Letoli Ranch',
    'price'    => '$3,997',
    'action'   => 'Book',
    'btn_cls'  => 'btn-go',
    'highlight'=> true,
    'slug'     => 'vital-fundamentals-december-5-to-12-2026',
  ],
];
?>

<div class="hero tint">
  <div class="wrap">
    <div class="kicker">
      <svg class="ic" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><use href="#i-calendar"/></svg>
      <?php esc_html_e('Calendar', 'knowledgism'); ?>
    </div>
    <h1 class="mt16"><?php esc_html_e('The best way to learn it is to do it', 'knowledgism'); ?></h1>
    <p class="lede mt16" style="max-width:62ch">
      <?php esc_html_e('Every live event with its dates, format and price in one line. Immersions are held at Letoli Ranch in Saint Jo, Texas.', 'knowledgism'); ?>
    </p>
  </div>
</div>

<div class="block">
  <div class="wrap">
    <div class="rowbox">
      <?php foreach ($canonical_events as $ev) : 
        // Try finding matching post in DB for real link
        $event_link = home_url('/#events');
        $found_post = get_page_by_path($ev['slug'], OBJECT, 'tribe_events');
        if (!$found_post) {
          // Search by title snippet
          $search_posts = get_posts([
            'post_type'      => 'tribe_events',
            'posts_per_page' => 1,
            's'              => $ev['title'],
            'post_status'    => 'publish',
          ]);
          if (!empty($search_posts)) {
            $found_post = $search_posts[0];
          }
        }
        if ($found_post) {
          $event_link = get_permalink($found_post->ID);
        }
        $row_bg = $ev['highlight'] ? 'background:var(--tint);' : '';
      ?>
        <div class="row" style="<?php echo esc_attr($row_bg); ?>">
          <div class="datebox">
            <div class="tiny" style="font-weight:700;color:var(--muted);"><?php echo esc_html($ev['month']); ?></div>
            <div class="d" style="font-family:var(--display);font-size:24px;font-weight:600;line-height:1;margin-top:2px;">
              <?php echo esc_html($ev['day']); ?>
            </div>
          </div>

          <div class="grow">
            <div style="font-weight:600;font-size:15px;color:var(--ink);">
              <?php echo esc_html($ev['title']); ?>
            </div>
            <div class="xs mt4" style="color:var(--muted);margin-top:3px;">
              <?php echo wp_kses_post($ev['subtitle']); ?>
            </div>
          </div>

          <?php if (!empty($ev['price'])) : ?>
            <span class="price" style="font-size:17px;font-family:var(--mono);font-weight:700;color:var(--ink);">
              <?php echo esc_html($ev['price']); ?>
            </span>
          <?php else : ?>
            <span class="pending">
              <?php esc_html_e('price TBD', 'knowledgism'); ?>
            </span>
          <?php endif; ?>

          <a class="btn <?php echo esc_attr($ev['btn_cls']); ?> btn-sm" href="<?php echo esc_url($event_link); ?>">
            <?php echo esc_html($ev['action']); ?>
          </a>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Bottom CTA Card: Not ready to book? -->
    <div class="card mt32 split" style="align-items:center;background:#fff;border:1px solid var(--line);border-radius:10px;padding:24px;">
      <div>
        <h3 style="font-family:var(--display);font-size:20px;font-weight:600;">
          <?php esc_html_e('Not ready to book?', 'knowledgism'); ?>
        </h3>
        <p class="sm mt8" style="color:#4A5764;">
          <?php esc_html_e('Find your Zone first. It takes two minutes and tells you which programme fits.', 'knowledgism'); ?>
        </p>
      </div>
      <div style="text-align:right;">
        <a class="btn btn-go" href="https://app.zonesoflife.com/short-quiz/" target="_blank" rel="noopener noreferrer" style="min-width:220px;">
          <?php esc_html_e('Find your Zone', 'knowledgism'); ?>
        </a>
      </div>
    </div>
  </div>
</div>

<?php get_footer(); ?>
