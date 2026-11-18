<?php
/**
 * Block Name: Home Word of the Week Newsletter
 * Slug: home-newsletter
 * Description: Knowledgism Newsletter band for The Word of the Week.
 *
 * @package Knowledgism
 */

$block_id = !empty($block['anchor']) ? $block['anchor'] : 'home-newsletter-' . ($block['id'] ?? uniqid());
$block_class = 'home-newsletter-block';
if (!empty($block['className'])) {
  $block_class .= ' ' . $block['className'];
}

$kicker            = get_field('kicker') ?: __('Free weekly', 'knowledgism');
$title             = get_field('title') ?: __('The Word of the Week', 'knowledgism');
$description       = get_field('description') ?: __('One term from the Knowledgism Dictionary, defined plainly, every Monday. Plus new articles and event dates before they are announced.', 'knowledgism');
$placeholder       = get_field('placeholder') ?: 'jane@example.com';
$button_text       = get_field('button_text') ?: __('Subscribe', 'knowledgism');
$consent_text      = get_field('consent_text') ?: __('I agree to the Privacy Policy. Unsubscribe anytime.', 'knowledgism');
?>

<div id="<?php echo esc_attr($block_id); ?>" class="block <?php echo esc_attr($block_class); ?>">
  <div class="wrap">
    <div class="newsband">
      <div class="split" style="align-items:center;gap:32px">
        <div>
          <div class="kicker">
            <svg class="ic" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><use href="#i-mail"/></svg>
            <?php echo esc_html($kicker); ?>
          </div>
          <h3 class="mt8"><?php echo esc_html($title); ?></h3>
          <p class="sm mt8"><?php echo esc_html($description); ?></p>
        </div>
        <div>
          <div class="newsForm">
            <div class="newsrow">
              <input class="input nMail" type="email" placeholder="<?php echo esc_attr($placeholder); ?>" aria-label="<?php esc_attr_e('Email address', 'knowledgism'); ?>">
              <button class="btn btn-primary nGo" type="button"><?php echo esc_html($button_text); ?></button>
            </div>
            <label class="xs" style="display:flex;gap:9px;align-items:flex-start;margin-top:12px;cursor:pointer;">
              <input type="checkbox" class="nOk" style="margin-top:3px">
              <?php echo esc_html($consent_text); ?>
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
