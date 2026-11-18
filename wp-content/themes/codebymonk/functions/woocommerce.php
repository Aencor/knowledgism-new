<?php
/**
 * WooCommerce Customizations & Helpers
 */

if (!class_exists('WooCommerce')) {
    return;
}

/**
 * Bookstore Category Filter Bar (Neutral, accessible, removes color-dependency)
 */
function monk_bookstore_category_nav() {
    if (!is_shop() && !is_product_category()) {
        return;
    }
    $categories = get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'exclude'    => [get_option('default_product_cat', 0)],
    ]);
    if (empty($categories) || is_wp_error($categories)) {
        return;
    }

    $current_term_id = is_product_category() ? get_queried_object_id() : 0;
    $shop_url = get_permalink(wc_get_page_id('shop'));

    echo '<nav class="bookstore-category-filter" aria-label="' . esc_attr__('Product categories', 'codebymonk') . '">';
    echo '<a href="' . esc_url($shop_url) . '" class="category-pill ' . ($current_term_id === 0 ? 'active' : '') . '">' . esc_html__('All Products', 'codebymonk') . '</a>';
    foreach ($categories as $cat) {
        $active = ($current_term_id === $cat->term_id) ? 'active' : '';
        echo '<a href="' . esc_url(get_term_link($cat)) . '" class="category-pill ' . $active . '">' . esc_html($cat->name) . ' <span class="count">(' . $cat->count . ')</span></a>';
    }
    echo '</nav>';
}
add_action('woocommerce_before_shop_loop', 'monk_bookstore_category_nav', 5);

/**
 * Display category badge on product loop items (accessible text badge without color coding)
 */
function monk_show_product_category_badge() {
    global $product;
    if (!$product) {
        return;
    }
    $terms = get_the_terms($product->get_id(), 'product_cat');
    if ($terms && !is_wp_error($terms)) {
        // filter out uncategorized
        $default_cat = get_option('default_product_cat', 0);
        $filtered = array_filter($terms, function($t) use ($default_cat) {
            return $t->term_id != $default_cat;
        });
        if (!empty($filtered)) {
            $first_term = array_shift($filtered);
            echo '<span class="product-category-badge">' . esc_html($first_term->name) . '</span>';
        }
    }
}
add_action('woocommerce_before_shop_loop_item_title', 'monk_show_product_category_badge', 9);

