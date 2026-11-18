<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="<?php echo esc_url(get_template_directory_uri() . '/assets/img/logos/favicon.png'); ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- SVG Icon Symbols Sprite from Prototype -->
<svg width="0" height="0" style="position:absolute" aria-hidden="true"><defs>
<g id="i-calendar"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 10h18"/></g>
<g id="i-users"><circle cx="9" cy="8" r="3.2"/><path d="M3.5 20c0-3.1 2.5-5.2 5.5-5.2s5.5 2.1 5.5 5.2"/><path d="M16.5 6.4a3 3 0 0 1 0 5.9M17.5 14.4c2 .6 3.5 2.4 3.5 4.6"/></g>
<g id="i-book"><path d="M5 4.5A1.5 1.5 0 0 1 6.5 3H19v15H6.5A1.5 1.5 0 0 0 5 19.5z"/><path d="M5 19.5A1.5 1.5 0 0 1 6.5 18H19v3H6.5A1.5 1.5 0 0 1 5 19.5z"/></g>
<g id="i-target"><circle cx="12" cy="12" r="8.2"/><circle cx="12" cy="12" r="4.4"/><circle cx="12" cy="12" r="1"/></g>
<g id="i-compass"><circle cx="12" cy="12" r="8.5"/><path d="M15.6 8.4l-2 5.2-5.2 2 2-5.2z"/></g>
<g id="i-mail"><rect x="3" y="5.5" width="18" height="13" rx="2"/><path d="M3.6 7l8.4 6 8.4-6"/></g>
<g id="i-phone"><path d="M6.5 3.5h3l1.5 4-2 1.5a12 12 0 0 0 6 6l1.5-2 4 1.5v3a2 2 0 0 1-2.2 2A16.5 16.5 0 0 1 4.5 5.7a2 2 0 0 1 2-2.2z"/></g>
<g id="i-pin"><path d="M12 21c4-4.6 6-7.8 6-10.4A6 6 0 0 0 6 10.6C6 13.2 8 16.4 12 21z"/><circle cx="12" cy="10.4" r="2.3"/></g>
<g id="i-play"><circle cx="12" cy="12" r="8.5"/><path d="M10.2 8.6l5.4 3.4-5.4 3.4z"/></g>
<g id="i-check"><path d="M4.5 12.6l4.6 4.6L19.5 6.8"/></g>
<g id="i-clock"><circle cx="12" cy="12" r="8.5"/><path d="M12 7.2V12l3.2 2"/></g>
<g id="i-arrow"><path d="M4.5 12h14"/><path d="M13.2 6.4L18.8 12l-5.6 5.6"/></g>
<g id="i-fb"><path d="M14.6 21v-8h2.7l.4-3.1h-3.1V7.9c0-.9.25-1.5 1.55-1.5H18V3.6c-.3-.04-1.3-.13-2.45-.13-2.4 0-4.05 1.47-4.05 4.17V9.9H8.8V13h2.7v8z"/></g>
<g id="i-ig"><rect x="3.6" y="3.6" width="16.8" height="16.8" rx="4.6"/><circle cx="12" cy="12" r="4.1"/><circle cx="16.9" cy="7.1" r="1.05"/></g>
<g id="i-in"><rect x="3.6" y="3.6" width="16.8" height="16.8" rx="2.4"/><path d="M8 10.4V17"/><circle cx="8" cy="7.3" r="1.15"/><path d="M12 17v-3.7a2.5 2.5 0 0 1 5 0V17"/><path d="M12 10.4V17"/></g>
<g id="i-yt"><rect x="2.8" y="5.6" width="18.4" height="12.8" rx="3.6"/><path d="M10.4 9.6l5.2 2.4-5.2 2.4z"/></g>
<g id="i-shield"><path d="M12 3.2l7 2.6v5.4c0 4.2-2.8 7.6-7 9.6-4.2-2-7-5.4-7-9.6V5.8z"/></g>
<g id="i-spark"><path d="M12 3.2l2.1 5.1 5.1 2.1-5.1 2.1-2.1 5.1-2.1-5.1-5.1-2.1 5.1-2.1z"/><path d="M18.5 16.5l.8 2 2 .8-2 .8-.8 2-.8-2-2-.8 2-.8z"/></g>
<g id="i-cart"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></g>
</defs></svg>

<header class="site"><div class="wrap bar">
  <a href="<?php echo esc_url(function_exists('knowledgism_get_nav_url') ? knowledgism_get_nav_url('home') : home_url('/')); ?>" class="hgroup">
    <img class="logo" alt="<?php bloginfo('name'); ?>" src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/logos/knowledgism-logo-white.png'); ?>" />
  </a>
  <nav class="main">
    <a href="<?php echo esc_url(function_exists('knowledgism_get_nav_url') ? knowledgism_get_nav_url('about') : home_url('/about/')); ?>"><?php esc_html_e('What Is Knowledgism', 'knowledgism'); ?></a>
    <a href="<?php echo esc_url(function_exists('knowledgism_get_nav_url') ? knowledgism_get_nav_url('articles') : home_url('/articles/')); ?>" class="<?php echo (is_page('articles') || is_page('articulos') || is_category() || is_singular('post') || is_home()) ? 'on' : ''; ?>"><?php esc_html_e('Articles', 'knowledgism'); ?></a>
    <a href="<?php echo esc_url(function_exists('knowledgism_get_nav_url') ? knowledgism_get_nav_url('glossary') : home_url('/glossary/')); ?>" class="<?php echo (is_page('glossary') || is_page('glosario')) ? 'on' : ''; ?>"><?php esc_html_e('Glossary', 'knowledgism'); ?></a>
    <a href="<?php echo esc_url(function_exists('knowledgism_get_nav_url') ? knowledgism_get_nav_url('bookstore') : home_url('/bookstore/')); ?>"><?php esc_html_e('Bookstore', 'knowledgism'); ?></a>
    <a href="<?php echo esc_url(function_exists('knowledgism_get_nav_url') ? knowledgism_get_nav_url('events') : home_url('/events/')); ?>" class="<?php echo (is_post_type_archive('tribe_events') || is_singular('tribe_events') || is_tax('tribe_events_cat')) ? 'on' : ''; ?>"><?php esc_html_e('Events', 'knowledgism'); ?></a>
  </nav>
  <div class="hgroup">
    <?php
    $wpml_languages = apply_filters('wpml_active_languages', NULL, 'skip_missing=0');
    if (!empty($wpml_languages)) :
      $current_lang = null;
      foreach ($wpml_languages as $lang) {
        if (!empty($lang['active'])) {
          $current_lang = $lang;
          break;
        }
      }
      if (!$current_lang) {
        $current_lang = reset($wpml_languages);
      }
      $current_code = strtoupper(substr($current_lang['code'], 0, 2));
    ?>
      <div class="lang-switcher" id="headerLangSwitcher">
        <button type="button" class="btn-lang-header" id="langSwitcherBtn" aria-haspopup="true" aria-expanded="false" aria-label="<?php esc_attr_e('Select Language', 'knowledgism'); ?>">
          <?php if (!empty($current_lang['country_flag_url'])) : ?>
            <img src="<?php echo esc_url($current_lang['country_flag_url']); ?>" alt="<?php echo esc_attr($current_code); ?>" class="lang-flag-current" width="16" height="12" />
          <?php else : ?>
            <svg class="ic-globe" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
          <?php endif; ?>
          <span class="lang-label"><?php echo esc_html($current_code); ?></span>
          <svg class="ic-chevron" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="lang-dropdown" id="langDropdown" role="menu">
          <?php foreach ($wpml_languages as $code => $lang) : 
            $is_active = !empty($lang['active']);
            $short_code = strtoupper(substr($lang['code'], 0, 2));
            $name = !empty($lang['native_name']) ? $lang['native_name'] : $lang['translated_name'];
          ?>
            <a href="<?php echo esc_url($lang['url']); ?>" class="lang-option <?php echo $is_active ? 'is-active' : ''; ?>" role="menuitem">
              <?php if (!empty($lang['country_flag_url'])) : ?>
                <img src="<?php echo esc_url($lang['country_flag_url']); ?>" alt="<?php echo esc_attr($short_code); ?>" class="lang-flag" width="16" height="12" />
              <?php endif; ?>
              <span class="lang-option-name"><?php echo esc_html($name); ?></span>
              <span class="lang-option-code"><?php echo esc_html($short_code); ?></span>
              <?php if ($is_active) : ?>
                <svg class="ic-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
              <?php endif; ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
    <?php if (function_exists('wc_get_cart_url')) : 
      $cart_count = (function_exists('WC') && WC()->cart) ? WC()->cart->get_cart_contents_count() : 0;
    ?>
      <a class="btn-cart-header" href="<?php echo esc_url(wc_get_cart_url()); ?>" aria-label="<?php esc_attr_e('View shopping cart', 'knowledgism'); ?>" title="<?php esc_attr_e('View shopping cart', 'knowledgism'); ?>">
        <svg class="ic" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><use href="#i-cart"/></svg>
        <span class="cart-label"><?php esc_html_e('Cart', 'knowledgism'); ?></span>
        <span class="cart-badge <?php echo $cart_count > 0 ? 'has-items' : ''; ?>"><?php echo esc_html($cart_count); ?></span>
      </a>
    <?php endif; ?>
    <a class="btn btn-go btn-sm" href="https://app.zonesoflife.com/short-quiz/" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Find your Zone', 'knowledgism'); ?></a>
    <button class="burger" aria-label="Menu" id="burgerToggle"><i></i><i></i><i></i></button>
  </div>
</div></header>