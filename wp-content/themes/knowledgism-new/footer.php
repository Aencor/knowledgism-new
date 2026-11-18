<footer class="site"><div class="wrap">
 <div class="footgrid">
  <div><img class="logo" alt="Knowledgism" src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/logos/knowledgism-logo-white.png'); ?>" style="height:28px; width:auto;">
   <p class="xs mt16" style="color:rgba(255,255,255,.6);max-width:30ch"><?php esc_html_e('The written and recorded work of Alan C. Walter.', 'knowledgism'); ?></p>
   <h4 class="mt24"><?php esc_html_e('Follow', 'knowledgism'); ?></h4>
   <div class="social">
    <a href="<?php echo esc_url(function_exists('knowledgism_get_nav_url') ? knowledgism_get_nav_url('contact') : home_url('/#contact')); ?>" class="tbd" aria-label="Facebook"><svg class="ic " width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><use href="#i-fb"/></svg></a>
    <a href="<?php echo esc_url(function_exists('knowledgism_get_nav_url') ? knowledgism_get_nav_url('contact') : home_url('/#contact')); ?>" class="tbd" aria-label="Instagram"><svg class="ic " width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><use href="#i-ig"/></svg></a>
    <a href="<?php echo esc_url(function_exists('knowledgism_get_nav_url') ? knowledgism_get_nav_url('contact') : home_url('/#contact')); ?>" class="tbd" aria-label="YouTube"><svg class="ic " width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><use href="#i-yt"/></svg></a>
   </div>
  </div>
  <div>
    <h4><?php esc_html_e('Knowledgism', 'knowledgism'); ?></h4>
    <a href="<?php echo esc_url(function_exists('knowledgism_get_nav_url') ? knowledgism_get_nav_url('about') : home_url('/about/')); ?>"><?php esc_html_e('What Is Knowledgism', 'knowledgism'); ?></a>
    <a href="<?php echo esc_url(function_exists('knowledgism_get_nav_url') ? knowledgism_get_nav_url('era') : home_url('/about/the-knowledge-era-is-upon-us/')); ?>"><?php esc_html_e('The Knowledge Era', 'knowledgism'); ?></a>
    <a href="<?php echo esc_url(function_exists('knowledgism_get_nav_url') ? knowledgism_get_nav_url('alan') : home_url('/about/meet-alan-c-walter/')); ?>"><?php esc_html_e('Meet Alan C. Walter', 'knowledgism'); ?></a>
  </div>
  <div>
    <h4><?php esc_html_e('Learn', 'knowledgism'); ?></h4>
    <a href="<?php echo esc_url(function_exists('knowledgism_get_nav_url') ? knowledgism_get_nav_url('articles') : home_url('/articles/')); ?>"><?php esc_html_e('Articles', 'knowledgism'); ?></a>
    <a href="<?php echo esc_url(function_exists('knowledgism_get_nav_url') ? knowledgism_get_nav_url('glossary') : home_url('/glossary/')); ?>"><?php esc_html_e('Glossary', 'knowledgism'); ?></a>
    <a href="<?php echo esc_url(function_exists('knowledgism_get_nav_url') ? knowledgism_get_nav_url('events') : home_url('/events/')); ?>"><?php esc_html_e('Events', 'knowledgism'); ?></a>
  </div>
  <div>
    <h4><?php esc_html_e('Shop', 'knowledgism'); ?></h4>
    <a href="<?php echo esc_url(function_exists('knowledgism_get_nav_url') ? knowledgism_get_nav_url('bookstore') : home_url('/bookstore/')); ?>"><?php esc_html_e('Bookstore', 'knowledgism'); ?></a>
    <a href="<?php echo esc_url(home_url('/product-category/libros-en-espanol/')); ?>"><?php esc_html_e('Books in Spanish', 'knowledgism'); ?></a>
    <a href="<?php echo esc_url(function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart/')); ?>"><?php esc_html_e('Cart', 'knowledgism'); ?></a>
  </div>
  <div>
    <h4><?php esc_html_e('Company', 'knowledgism'); ?></h4>
    <a href="<?php echo esc_url(function_exists('knowledgism_get_nav_url') ? knowledgism_get_nav_url('contact') : home_url('/#contact')); ?>"><?php esc_html_e('Contact', 'knowledgism'); ?></a>
    <a href="<?php echo esc_url(function_exists('knowledgism_get_nav_url') ? knowledgism_get_nav_url('privacy') : home_url('/#privacy')); ?>"><?php esc_html_e('Privacy policy', 'knowledgism'); ?></a>
    <a href="<?php echo esc_url(home_url('/#cookies')); ?>"><?php esc_html_e('Cookie policy', 'knowledgism'); ?></a>
  </div>
 </div>
 <div class="legal">
   <span>© <?php echo date('Y'); ?> Applied Knowledge, LLC</span>
   <span><?php esc_html_e('Coaching delivered by ACLC at Letoli Ranch, Texas', 'knowledgism'); ?></span>
 </div>
</div></footer>

<div class="cookiebar" id="cookieBar" hidden><div class="wrap cookieinner">
 <div style="flex:1;min-width:260px">
  <div style="font-weight:600;font-size:14px"><?php esc_html_e('We use cookies', 'knowledgism'); ?></div>
  <p class="xs mt8" style="color:rgba(255,255,255,.68);max-width:66ch"><?php esc_html_e('Necessary cookies keep the site working. Analytics and marketing cookies help us understand what people need. You choose.', 'knowledgism'); ?></p></div>
 <div class="hgroup" style="gap:10px;flex-wrap:wrap">
  <a class="btn btn-onDark btn-sm" href="<?php echo esc_url(home_url('/#cookies')); ?>"><?php esc_html_e('Manage', 'knowledgism'); ?></a>
  <button class="btn btn-onDark btn-sm" id="ckReject" type="button"><?php esc_html_e('Reject non-essential', 'knowledgism'); ?></button>
  <button class="btn btn-primary btn-sm" id="ckAccept" type="button"><?php esc_html_e('Accept all', 'knowledgism'); ?></button></div>
</div></div>

<?php wp_footer(); ?>
</body>
</html>
