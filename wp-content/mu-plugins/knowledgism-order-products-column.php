<?php
/**
 * Plugin Name: Knowledgism - WooCommerce Order Purchased Products Column & CSV Exporter
 * Description: Adds a "Productos" column to the WooCommerce admin orders table and an "Exportar Pedidos (CSV)" button to export all order data.
 * Version: 1.1.0
 * Author: Antigravity
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Add "Productos" column to WooCommerce orders table (HPOS and Legacy)
 *
 * @param array $columns
 * @return array
 */
function knowledgism_add_order_products_column( array $columns ): array {
    $new_columns = array();

    foreach ( $columns as $key => $column ) {
        $new_columns[ $key ] = $column;
        // Insert right after status or order_number
        if ( 'order_status' === $key || 'order_number' === $key ) {
            if ( ! isset( $new_columns['purchased_products'] ) ) {
                $new_columns['purchased_products'] = __( 'Productos', 'woocommerce' );
            }
        }
    }

    // Fallback if insertion point was not found
    if ( ! isset( $new_columns['purchased_products'] ) ) {
        $new_columns['purchased_products'] = __( 'Productos', 'woocommerce' );
    }

    return $new_columns;
}
add_filter( 'woocommerce_shop_order_list_table_columns', 'knowledgism_add_order_products_column', 20 );
add_filter( 'manage_edit-shop_order_columns', 'knowledgism_add_order_products_column', 20 );

/**
 * Render purchased products column content for HPOS order list table
 *
 * @param string $column
 * @param WC_Order|int $order
 * @return void
 */
function knowledgism_render_order_products_column_content( string $column, $order ): void {
    if ( 'purchased_products' !== $column ) {
        return;
    }

    if ( is_numeric( $order ) ) {
        $order = wc_get_order( $order );
    }

    if ( ! $order || ! is_a( $order, 'WC_Order' ) ) {
        echo '-';
        return;
    }

    $items = $order->get_items();
    if ( empty( $items ) ) {
        echo '-';
        return;
    }

    $product_list = array();
    foreach ( $items as $item ) {
        $product_name = $item->get_name();
        $quantity     = $item->get_quantity();
        $product      = $item->get_product();

        $display_name = $product_name;
        if ( $quantity > 1 ) {
            $display_name = $quantity . ' &times; ' . $product_name;
        }

        if ( $product && $product->get_id() ) {
            $edit_url = admin_url( 'post.php?post=' . $product->get_id() . '&action=edit' );
            $product_list[] = sprintf(
                '<a href="%s" style="font-weight: 500; text-decoration: none;">%s</a>',
                esc_url( $edit_url ),
                esc_html( $display_name )
            );
        } else {
            $product_list[] = esc_html( $display_name );
        }
    }

    echo implode( '<br>', $product_list );
}
add_action( 'woocommerce_shop_order_list_table_custom_column', 'knowledgism_render_order_products_column_content', 10, 2 );

/**
 * Render purchased products column content for Legacy post-type order list table
 *
 * @param string $column
 * @param int $post_id
 * @return void
 */
function knowledgism_render_order_products_column_content_legacy( string $column, int $post_id ): void {
    if ( 'purchased_products' === $column ) {
        $order = wc_get_order( $post_id );
        if ( $order ) {
            knowledgism_render_order_products_column_content( $column, $order );
        }
    }
}
add_action( 'manage_shop_order_posts_custom_column', 'knowledgism_render_order_products_column_content_legacy', 10, 2 );

/**
 * Custom CSS for admin order table column width
 */
function knowledgism_order_products_column_admin_css(): void {
    $screen = get_current_screen();
    if ( $screen && in_array( $screen->id, array( 'edit-shop_order', 'woocommerce_page_wc-orders' ), true ) ) {
        echo '<style>
            .wp-list-table .column-purchased_products { width: 22%; }
        </style>';
    }
}
add_action( 'admin_head', 'knowledgism_order_products_column_admin_css' );

/**
 * Add "Exportar Pedidos (CSV)" button to WooCommerce Orders list table
 */
function knowledgism_add_export_orders_button(): void {
    $screen = get_current_screen();
    if ( ! $screen || ! in_array( $screen->id, array( 'edit-shop_order', 'woocommerce_page_wc-orders' ), true ) ) {
        return;
    }

    $export_url = wp_nonce_url(
        add_query_arg(
            array(
                'action' => 'export_orders_csv',
            ),
            admin_url( 'admin-post.php' )
        ),
        'knowledgism_export_orders'
    );

    // Maintain active filters in export URL if set by user
    if ( ! empty( $_GET['status'] ) ) {
        $export_url = add_query_arg( 'status', sanitize_text_field( $_GET['status'] ), $export_url );
    }
    if ( ! empty( $_GET['m'] ) ) {
        $export_url = add_query_arg( 'm', sanitize_text_field( $_GET['m'] ), $export_url );
    }
    if ( ! empty( $_GET['s'] ) ) {
        $export_url = add_query_arg( 's', sanitize_text_field( $_GET['s'] ), $export_url );
    }

    printf(
        '<a href="%s" class="button button-primary" style="margin-left: 8px; display: inline-flex; align-items: center; gap: 4px;">' .
        '<span class="dashicons dashicons-download" style="font-size: 16px; width: 16px; height: 16px; line-height: 16px;"></span> %s' .
        '</a>',
        esc_url( $export_url ),
        esc_html__( 'Exportar Pedidos (CSV)', 'woocommerce' )
    );
}
add_action( 'woocommerce_order_list_table_restrict_manage_orders', 'knowledgism_add_export_orders_button', 20 );
add_action( 'restrict_manage_posts', 'knowledgism_add_export_orders_button', 20 );

/**
 * Handle CSV Export action
 */
function knowledgism_handle_export_orders_csv(): void {
    check_admin_referer( 'knowledgism_export_orders' );

    if ( ! current_user_can( 'edit_shop_orders' ) && ! current_user_can( 'manage_woocommerce' ) ) {
        wp_die( __( 'No tienes permisos suficientes para realizar esta acción.', 'woocommerce' ) );
    }

    $query_args = array(
        'limit'    => -1,
        'paginate' => false,
        'type'     => 'shop_order',
        'return'   => 'objects',
    );

    if ( ! empty( $_GET['status'] ) && 'all' !== $_GET['status'] ) {
        $query_args['status'] = sanitize_text_field( $_GET['status'] );
    }

    if ( ! empty( $_GET['m'] ) ) {
        $m = sanitize_text_field( $_GET['m'] );
        if ( 6 === strlen( $m ) ) {
            $year  = substr( $m, 0, 4 );
            $month = substr( $m, 4, 2 );
            $query_args['date_created'] = $year . '-' . $month . '-01...' . date( 'Y-m-t', strtotime( "$year-$month-01" ) );
        }
    }

    if ( ! empty( $_GET['s'] ) ) {
        $query_args['s'] = sanitize_text_field( $_GET['s'] );
    }

    $orders = wc_get_orders( $query_args );

    $filename = 'pedidos-knowledgism-' . date( 'Y-m-d_H-i-s' ) . '.csv';

    header( 'Content-Type: text/csv; charset=UTF-8' );
    header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
    header( 'Pragma: no-cache' );
    header( 'Expires: 0' );

    $output = fopen( 'php://output', 'w' );

    // Output UTF-8 BOM for Microsoft Excel / Apple Numbers compatibility
    fputs( $output, "\xEF\xBB\xBF" );

    // CSV Header
    fputcsv( $output, array(
        'ID Pedido',
        'Número Pedido',
        'Fecha',
        'Estado',
        'Nombre Cliente',
        'Apellidos Cliente',
        'Email',
        'Teléfono',
        'Productos Comprados',
        'SKUs',
        'Cantidad Total Ítems',
        'Subtotal',
        'Descuento Total',
        'Impuestos',
        'Envío',
        'Total',
        'Moneda',
        'Método de Pago',
        'Dirección Facturación',
        'Ciudad',
        'Código Postal',
        'País',
    ) );

    if ( ! empty( $orders ) ) {
        foreach ( $orders as $order ) {
            $items = $order->get_items();
            $product_names  = array();
            $product_skus   = array();
            $total_qty      = 0;

            foreach ( $items as $item ) {
                $qty          = $item->get_quantity();
                $total_qty   += $qty;
                $name         = $item->get_name();
                $product      = $item->get_product();
                $sku          = $product ? $product->get_sku() : '';

                $product_names[] = $qty > 1 ? $qty . 'x ' . $name : $name;
                if ( ! empty( $sku ) ) {
                    $product_skus[] = $sku;
                }
            }

            $date_created = $order->get_date_created() ? $order->get_date_created()->date( 'Y-m-d H:i:s' ) : '';
            $status_name  = wc_get_order_status_name( $order->get_status() );

            fputcsv( $output, array(
                $order->get_id(),
                $order->get_order_number(),
                $date_created,
                $status_name,
                $order->get_billing_first_name(),
                $order->get_billing_last_name(),
                $order->get_billing_email(),
                $order->get_billing_phone(),
                implode( ' | ', $product_names ),
                implode( ' | ', $product_skus ),
                $total_qty,
                $order->get_subtotal(),
                $order->get_discount_total(),
                $order->get_total_tax(),
                $order->get_shipping_total(),
                $order->get_total(),
                $order->get_currency(),
                $order->get_payment_method_title(),
                trim( $order->get_billing_address_1() . ' ' . $order->get_billing_address_2() ),
                $order->get_billing_city(),
                $order->get_billing_postcode(),
                $order->get_billing_country(),
            ) );
        }
    }

    fclose( $output );
    exit;
}
add_action( 'admin_post_export_orders_csv', 'knowledgism_handle_export_orders_csv' );
