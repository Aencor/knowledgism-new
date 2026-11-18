<?php
/**
 * Block Name: Home Carousel / Zone Assessment CTA
 * Slug: home-zone-cta
 * Description: Interactive Knowledgism carousel featuring the Zone Profile assessment, "The Fire Within", "The Zone Series" (pyramid of zones), and the Bookstore catalog.
 *
 * @package Knowledgism
 */

use CodeByMonk\HomeCarousel;

// Support Gutenberg block preview / screenshot (Regla 7)
$is_preview_mode = !empty($is_preview);
if ($is_preview_mode && !empty($block['data']['is_example'])) {
	$preview_img = get_template_directory_uri() . '/assets/img/blocks/home-zone-cta.png';
	echo '<div class="acf-block-preview-wrapper"><img src="' . esc_url($preview_img) . '" alt="Preview Home Carousel" style="max-width:100%;height:auto;border-radius:10px;box-shadow:0 4px 16px rgba(0,0,0,0.06);" /></div>';
	return;
}

$block_id = !empty($block['anchor']) ? $block['anchor'] : 'home-carousel-' . ($block['id'] ?? uniqid());
$block_class = 'home-zone-cta-block home-carousel-block';
if (!empty($block['className'])) {
	$block_class .= ' ' . $block['className'];
}

// Instantiate Service Class applying SOLID and DRY (Regla 5 & 6)
$carousel_service = new HomeCarousel();
$slides = $carousel_service->get_slides();

$autoplay_enabled = get_field('carousel_autoplay');
$autoplay_delay   = get_field('carousel_delay');
$autoplay_ms      = ($autoplay_enabled !== false) ? (intval($autoplay_delay) ?: 6000) : 0;
?>

<div id="<?php echo esc_attr($block_id); ?>" class="block <?php echo esc_attr($block_class); ?>">
  <div class="wrap">
    <div class="home-carousel-wrapper" data-autoplay="<?php echo esc_attr($autoplay_ms); ?>" role="region" aria-roledescription="carousel" aria-label="<?php esc_attr_e('Featured Highlights Carousel', 'knowledgism'); ?>">

      <!-- Top Navigation Tabs & Arrow Controls -->
      <div class="home-carousel-header">
        <div class="home-carousel-tabs" role="tablist" aria-label="<?php esc_attr_e('Carousel slides', 'knowledgism'); ?>">
          <?php foreach ($slides as $idx => $slide) : ?>
            <button type="button" class="home-carousel-tab <?php echo ($idx === 0) ? 'active' : ''; ?>" data-index="<?php echo esc_attr($idx); ?>" role="tab" aria-selected="<?php echo ($idx === 0) ? 'true' : 'false'; ?>" aria-controls="slide-<?php echo esc_attr($idx); ?>-<?php echo esc_attr($block_id); ?>">
              <span class="tab-badge"><?php echo esc_html($idx + 1); ?></span>
              <span class="tab-text"><?php echo esc_html($slide['tab_label']); ?></span>
            </button>
          <?php endforeach; ?>
        </div>

        <div class="home-carousel-arrows">
          <button type="button" class="home-carousel-nav-btn prev" aria-label="<?php esc_attr_e('Previous slide', 'knowledgism'); ?>">
            <svg class="ic" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
          </button>
          <button type="button" class="home-carousel-nav-btn next" aria-label="<?php esc_attr_e('Next slide', 'knowledgism'); ?>">
            <svg class="ic" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
          </button>
        </div>
      </div>

      <!-- Carousel Viewport & Sliding Track -->
      <div class="home-carousel-viewport">
        <div class="home-carousel-track">
          <?php foreach ($slides as $idx => $s) : ?>
            <div id="slide-<?php echo esc_attr($idx); ?>-<?php echo esc_attr($block_id); ?>" class="home-carousel-slide <?php echo ($idx === 0) ? 'active' : ''; ?>" role="tabpanel" data-slide="<?php echo esc_attr($idx); ?>" aria-label="<?php echo esc_attr($s['title']); ?>">
              <div class="card tint pad-lg home-slide-card <?php echo esc_attr($s['id']); ?>">
                <div class="home-slide-grid">
                  
                  <!-- Left: Slide Content / Media -->
                  <div class="home-slide-content <?php echo ($s['media_type'] === 'image' || $s['media_type'] === 'bookstore_icon') ? 'with-media' : ''; ?>">
                    <?php if ($s['media_type'] === 'image') : ?>
                      <div class="home-slide-cover-wrap">
                        <img class="home-book-img" src="<?php echo esc_url($s['image_url']); ?>" alt="<?php echo esc_attr($s['image_alt']); ?>" width="<?php echo esc_attr($s['image_width'] ?? 110); ?>" height="<?php echo esc_attr($s['image_height'] ?? 130); ?>" loading="lazy" decoding="async" />
                      </div>
                    <?php elseif ($s['media_type'] === 'bookstore_icon') : ?>
                      <div class="home-slide-cover-wrap bookstore-badge-box">
                        <div class="bookstore-icon-container">
                          <svg class="ic" width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><use href="#i-book"/></svg>
                        </div>
                      </div>
                    <?php endif; ?>

                    <div class="home-slide-details">
                      <?php if (!empty($s['kicker'])) : ?>
                        <div class="kicker mb8" <?php echo !empty($s['kicker_color']) ? 'style="color:' . esc_attr($s['kicker_color']) . '"' : ''; ?>>
                          <?php if (!empty($s['kicker_icon'])) : ?>
                            <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><use href="<?php echo esc_attr($s['kicker_icon']); ?>"/></svg>
                          <?php endif; ?>
                          <?php echo esc_html($s['kicker']); ?>
                        </div>
                      <?php endif; ?>

                      <h3 class="home-slide-heading"><?php echo esc_html($s['title']); ?></h3>
                      <p class="sm mt8 home-slide-desc"><?php echo esc_html($s['desc']); ?></p>

                      <?php if (!empty($s['show_zones'])) : ?>
                        <div class="home-slide-zones mt16">
                          <div class="zones">
                            <i class="z1"></i><i class="z2"></i><i class="z3"></i><i class="z4"></i>
                          </div>
                          <div class="zlabels">
                            <span class="tiny"><?php esc_html_e('Red', 'knowledgism'); ?></span>
                            <span class="tiny"><?php esc_html_e('Yellow', 'knowledgism'); ?></span>
                            <span class="tiny"><?php esc_html_e('Green', 'knowledgism'); ?></span>
                            <span class="tiny"><?php esc_html_e('Gold', 'knowledgism'); ?></span>
                          </div>
                        </div>
                      <?php endif; ?>

                      <?php if (!empty($s['badge'])) : ?>
                        <div class="mt12">
                          <span class="chip on" style="background:<?php echo esc_attr($s['badge_bg'] ?? 'var(--navy)'); ?>;color:<?php echo esc_attr($s['badge_color'] ?? '#fff'); ?>;"><?php echo esc_html($s['badge']); ?></span>
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>

                  <!-- Right: CTA Button Box -->
                  <div class="home-slide-action">
                    <a class="btn <?php echo esc_attr($s['btn_class']); ?> btn-block" href="<?php echo esc_url($s['btn_url']); ?>" <?php echo ($s['btn_target'] === '_blank') ? 'target="_blank" rel="noopener noreferrer"' : ''; ?> <?php echo !empty($s['btn_style']) ? 'style="' . esc_attr($s['btn_style']) . '"' : ''; ?> aria-label="<?php echo esc_attr($s['btn_text'] . ' — ' . $s['title']); ?>">
                      <span><?php echo esc_html($s['btn_text']); ?></span>
                      <svg class="ic" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><use href="#i-arrow"/></svg>
                    </a>
                    <?php if (!empty($s['subtext'])) : ?>
                      <div class="tiny center mt8 home-subtext"><?php echo esc_html($s['subtext']); ?></div>
                    <?php endif; ?>
                  </div>

                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Bottom Dots Indicator -->
      <div class="home-carousel-dots">
        <?php foreach ($slides as $idx => $s) : ?>
          <button type="button" class="home-carousel-dot <?php echo ($idx === 0) ? 'active' : ''; ?>" data-index="<?php echo esc_attr($idx); ?>" aria-label="<?php echo esc_attr(sprintf(__('Slide %d: %s', 'knowledgism'), $idx + 1, $s['tab_label'])); ?>"></button>
        <?php endforeach; ?>
      </div>

    </div>
  </div>
</div>
