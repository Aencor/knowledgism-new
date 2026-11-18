<?php
/**
 * Plugin Name: Knowledgism - WooCommerce Frontend Orders Dashboard
 * Description: Front-end dashboard for WooCommerce orders with real-time search, status/date filters, order detail drawer, CSV exporter, key bypass, and inline WP login modal.
 * Version: 1.2.1
 * Author: Antigravity
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Knowledgism_Frontend_Orders_Dashboard {

    public static function init() {
        // Register shortcode
        add_shortcode( 'knowledgism_orders_dashboard', array( __CLASS__, 'render_shortcode' ) );

        // AJAX handlers for login
        add_action( 'wp_ajax_knowledgism_fed_ajax_login', array( __CLASS__, 'ajax_login' ) );
        add_action( 'wp_ajax_nopriv_knowledgism_fed_ajax_login', array( __CLASS__, 'ajax_login' ) );

        // AJAX handlers for orders (both logged in and nopriv for key bypass)
        add_action( 'wp_ajax_knowledgism_fed_get_orders', array( __CLASS__, 'ajax_get_orders' ) );
        add_action( 'wp_ajax_nopriv_knowledgism_fed_get_orders', array( __CLASS__, 'ajax_get_orders' ) );

        add_action( 'wp_ajax_knowledgism_fed_get_order_details', array( __CLASS__, 'ajax_get_order_details' ) );
        add_action( 'wp_ajax_nopriv_knowledgism_fed_get_order_details', array( __CLASS__, 'ajax_get_order_details' ) );

        // CSV Export Handler
        add_action( 'admin_post_knowledgism_fed_export_csv', array( __CLASS__, 'handle_csv_export' ) );
        add_action( 'admin_post_nopriv_knowledgism_fed_export_csv', array( __CLASS__, 'handle_csv_export' ) );

        // Key Logout Handler
        add_action( 'admin_post_knowledgism_fed_logout_key', array( __CLASS__, 'handle_key_logout' ) );
        add_action( 'admin_post_nopriv_knowledgism_fed_logout_key', array( __CLASS__, 'handle_key_logout' ) );

        // WP Admin Redirect & Restriction for non-admins
        add_filter( 'login_redirect', array( __CLASS__, 'custom_login_redirect' ), 10, 3 );
        add_action( 'admin_init', array( __CLASS__, 'restrict_admin_access' ) );

        // Check key submission early on init to set cookie
        add_action( 'init', array( __CLASS__, 'check_key_submission' ) );
    }

    /**
     * AJAX Login Handler (In-Line Login without going to wp-login.php)
     */
    public static function ajax_login() {
        check_ajax_referer( 'knowledgism_fed_nonce', 'nonce' );

        $username = isset( $_POST['username'] ) ? sanitize_user( $_POST['username'] ) : '';
        $password = isset( $_POST['password'] ) ? $_POST['password'] : '';
        $remember = ! empty( $_POST['remember'] );

        if ( empty( $username ) || empty( $password ) ) {
            wp_send_json_error( 'Por favor ingresa tu usuario/email y contraseña.' );
        }

        $creds = array(
            'user_login'    => $username,
            'user_password' => $password,
            'remember'      => $remember,
        );

        $user = wp_signon( $creds, is_ssl() );

        if ( is_wp_error( $user ) ) {
            $error_msg = wp_strip_all_tags( $user->get_error_message() );
            wp_send_json_error( $error_msg );
        }

        wp_send_json_success( array( 'redirect' => remove_query_arg( array( 'key', 'kfed_error', 'logout', 'logout_key' ) ) ) );
    }

    /**
     * Get secret access key for bypass
     */
    public static function get_secret_key() : string {
        if ( defined( 'KFED_SECRET_KEY' ) && ! empty( KFED_SECRET_KEY ) ) {
            return KFED_SECRET_KEY;
        }
        return (string) apply_filters( 'knowledgism_fed_secret_key', 'knowledgism2026' );
    }

    /**
     * Verify if user has valid access key or cookie
     */
    public static function verify_access_key() : bool {
        if ( isset( $_GET['logout'] ) || isset( $_GET['logout_key'] ) ) {
            return false;
        }

        $secret = self::get_secret_key();
        if ( empty( $secret ) ) {
            return false;
        }

        $expected_hash = hash( 'sha256', 'kfed_' . $secret );

        $provided_key = '';
        if ( ! empty( $_REQUEST['key'] ) ) {
            $provided_key = sanitize_text_field( $_REQUEST['key'] );
        } elseif ( ! empty( $_POST['kfed_access_pass'] ) ) {
            $provided_key = sanitize_text_field( $_POST['kfed_access_pass'] );
        }

        if ( ! empty( $provided_key ) && $provided_key === $secret ) {
            return true;
        }

        if ( isset( $_COOKIE['kfed_token'] ) && hash_equals( $expected_hash, $_COOKIE['kfed_token'] ) ) {
            return true;
        }

        return false;
    }

    /**
     * Early check for key submission via POST or GET to set cookie
     */
    public static function check_key_submission() {
        if ( isset( $_GET['logout'] ) || isset( $_GET['logout_key'] ) ) {
            if ( ! headers_sent() ) {
                setcookie( 'kfed_token', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
            }
            unset( $_COOKIE['kfed_token'] );
            return;
        }

        $secret = self::get_secret_key();
        if ( empty( $secret ) ) {
            return;
        }

        $expected_hash = hash( 'sha256', 'kfed_' . $secret );

        if ( isset( $_POST['kfed_access_pass'] ) && sanitize_text_field( $_POST['kfed_access_pass'] ) === $secret ) {
            if ( ! headers_sent() ) {
                setcookie( 'kfed_token', $expected_hash, time() + ( 30 * DAY_IN_SECONDS ), COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
            }
            $_COOKIE['kfed_token'] = $expected_hash;
            wp_safe_redirect( remove_query_arg( array( 'key', 'kfed_error', 'logout', 'logout_key' ) ) );
            exit;
        }

        if ( isset( $_GET['key'] ) && sanitize_text_field( $_GET['key'] ) === $secret ) {
            if ( ! headers_sent() ) {
                setcookie( 'kfed_token', $expected_hash, time() + ( 30 * DAY_IN_SECONDS ), COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
            }
            $_COOKIE['kfed_token'] = $expected_hash;
        }
    }

    /**
     * Handle key logout (clear cookie)
     */
    public static function handle_key_logout() {
        if ( ! headers_sent() ) {
            setcookie( 'kfed_token', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
        }
        unset( $_COOKIE['kfed_token'] );
        $redirect = isset( $_SERVER['HTTP_REFERER'] ) ? remove_query_arg( array( 'key', 'logout', 'logout_key' ), $_SERVER['HTTP_REFERER'] ) : home_url();
        wp_safe_redirect( $redirect );
        exit;
    }

    /**
     * Check if user can access orders
     */
    public static function user_can_access() : bool {
        if ( isset( $_GET['logout'] ) || isset( $_GET['logout_key'] ) ) {
            return false;
        }

        if ( self::verify_access_key() ) {
            return true;
        }

        if ( is_user_logged_in() ) {
            $can_access = current_user_can( 'edit_shop_orders' ) || current_user_can( 'manage_woocommerce' ) || current_user_can( 'administrator' );
            if ( apply_filters( 'knowledgism_fed_user_can_access', $can_access, get_current_user_id() ) ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Redirect non-administrators upon login to the frontend orders dashboard page
     */
    public static function custom_login_redirect( $redirect_to, $request, $user ) {
        if ( isset( $user->roles ) && is_array( $user->roles ) ) {
            if ( in_array( 'administrator', $user->roles, true ) ) {
                return $redirect_to;
            }
            if ( user_can( $user, 'edit_shop_orders' ) || user_can( $user, 'manage_woocommerce' ) ) {
                return home_url( '/dashboard-pedidos/' );
            }
        }
        return $redirect_to;
    }

    /**
     * Restrict WP-Admin access for shop managers and redirect to frontend page
     */
    public static function restrict_admin_access() {
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            return;
        }

        if ( is_user_logged_in() && ! current_user_can( 'administrator' ) ) {
            $pagenow = $GLOBALS['pagenow'] ?? '';
            if ( 'admin-post.php' === $pagenow || 'admin-ajax.php' === $pagenow ) {
                return;
            }

            if ( current_user_can( 'edit_shop_orders' ) || current_user_can( 'manage_woocommerce' ) ) {
                wp_safe_redirect( home_url( '/dashboard-pedidos/' ) );
                exit;
            }
        }
    }

    /**
     * Shortcode renderer [knowledgism_orders_dashboard]
     */
    public static function render_shortcode( $atts ) {
        if ( ! class_exists( 'WooCommerce' ) ) {
            return '<div class="kfed-alert kfed-alert-error">WooCommerce debe estar instalado y activo.</div>';
        }

        // If explicitly requested logout via query parameter ?logout=1
        if ( isset( $_GET['logout'] ) || isset( $_GET['logout_key'] ) ) {
            if ( ! headers_sent() ) {
                setcookie( 'kfed_token', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
            }
            unset( $_COOKIE['kfed_token'] );
            return self::render_access_prompt();
        }

        if ( ! self::user_can_access() ) {
            return self::render_access_prompt();
        }

        $nonce = wp_create_nonce( 'knowledgism_fed_nonce' );
        $secret_key = self::get_secret_key();
        $is_key_active = self::verify_access_key();
        $active_key_str = $is_key_active ? $secret_key : '';

        $export_url = wp_nonce_url( admin_url( 'admin-post.php?action=knowledgism_fed_export_csv' ), 'knowledgism_fed_export' );
        if ( $is_key_active ) {
            $export_url = add_query_arg( 'key', $secret_key, $export_url );
        }

        $logout_key_url = admin_url( 'admin-post.php?action=knowledgism_fed_logout_key' );
        $wp_logout_url = wp_logout_url( get_permalink() );
        $ajax_url = admin_url( 'admin-ajax.php' );

        ob_start();
        ?>
        <div id="knowledgism-orders-dashboard" class="kfed-wrapper">
            <!-- Header Section -->
            <div class="kfed-header">
                <div class="kfed-title-area">
                    <h2><span class="dashicons dashicons-cart"></span> Panel de Pedidos</h2>
                    <p class="kfed-subtitle">Gestiona y consulta los pedidos de la tienda sin ingresar al panel de administración.</p>
                </div>
                <div class="kfed-header-actions">
                    <?php if ( is_user_logged_in() ) : ?>
                        <a href="<?php echo esc_url( $wp_logout_url ); ?>" class="kfed-btn kfed-btn-secondary" style="color:#dc2626 !important;" title="Cerrar sesión de WordPress">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            Cerrar Sesión (WP)
                        </a>
                    <?php elseif ( $is_key_active ) : ?>
                        <a href="<?php echo esc_url( $logout_key_url ); ?>" class="kfed-btn kfed-btn-secondary" style="color:#dc2626 !important;" title="Cerrar sesión de clave">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            Salir (Clave)
                        </a>
                    <?php endif; ?>
                    <button type="button" id="kfed-btn-refresh" class="kfed-btn kfed-btn-secondary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.5 2v6h-6M2.13 15.57a10 10 0 1 0 3.93-11.23L2.5 6"/></svg>
                        Actualizar
                    </button>
                    <a id="kfed-btn-export" href="<?php echo esc_url( $export_url ); ?>" class="kfed-btn kfed-btn-primary" target="_blank">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                        Exportar CSV
                    </a>
                </div>
            </div>

            <!-- Stats Bar -->
            <div class="kfed-stats-grid">
                <div class="kfed-stat-card">
                    <div class="kfed-stat-icon icon-blue">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4zM3 6h18M16 10a4 4 0 0 1-8 0"/></svg>
                    </div>
                    <div class="kfed-stat-info">
                        <span class="kfed-stat-label">Total Pedidos</span>
                        <span class="kfed-stat-value" id="kfed-stat-total-orders">-</span>
                    </div>
                </div>

                <div class="kfed-stat-card">
                    <div class="kfed-stat-icon icon-green">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </div>
                    <div class="kfed-stat-info">
                        <span class="kfed-stat-label">Ventas Totales</span>
                        <span class="kfed-stat-value" id="kfed-stat-total-sales">-</span>
                    </div>
                </div>

                <div class="kfed-stat-card">
                    <div class="kfed-stat-icon icon-amber">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div class="kfed-stat-info">
                        <span class="kfed-stat-label">En Proceso / Pendientes</span>
                        <span class="kfed-stat-value" id="kfed-stat-processing">-</span>
                    </div>
                </div>

                <div class="kfed-stat-card">
                    <div class="kfed-stat-icon icon-emerald">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                    <div class="kfed-stat-info">
                        <span class="kfed-stat-label">Completados</span>
                        <span class="kfed-stat-value" id="kfed-stat-completed">-</span>
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="kfed-filters-bar">
                <div class="kfed-search-box">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" id="kfed-input-search" placeholder="Buscar por ID, cliente, email o producto..." />
                    <button type="button" id="kfed-btn-clear-search" style="display:none;" title="Limpiar búsqueda">&times;</button>
                </div>

                <div class="kfed-select-group">
                    <select id="kfed-select-status" class="kfed-select">
                        <option value="all">Todos los estados</option>
                        <option value="processing">Procesando</option>
                        <option value="completed">Completados</option>
                        <option value="on-hold">En espera</option>
                        <option value="pending">Pendientes de pago</option>
                        <option value="cancelled">Cancelados</option>
                        <option value="refunded">Reembolsados</option>
                        <option value="failed">Fallidos</option>
                    </select>

                    <select id="kfed-select-date" class="kfed-select">
                        <option value="all">Todas las fechas</option>
                        <option value="today">Hoy</option>
                        <option value="7days">Últimos 7 días</option>
                        <option value="this_month">Este mes</option>
                        <option value="last_month">Mes pasado</option>
                    </select>

                    <select id="kfed-select-limit" class="kfed-select">
                        <option value="10">10 por página</option>
                        <option value="25" selected>25 por página</option>
                        <option value="50">50 por página</option>
                        <option value="100">100 por página</option>
                    </select>
                </div>
            </div>

            <!-- Orders Table Container -->
            <div class="kfed-table-container">
                <div id="kfed-loading-overlay" class="kfed-loading-overlay">
                    <div class="kfed-spinner"></div>
                    <span>Cargando pedidos...</span>
                </div>

                <table class="kfed-table">
                    <thead>
                        <tr>
                            <th>Pedido</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Cliente</th>
                            <th>Productos</th>
                            <th class="text-right">Total</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="kfed-table-body">
                        <!-- Rendered dynamically -->
                    </tbody>
                </table>

                <div id="kfed-empty-state" class="kfed-empty-state" style="display:none;">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                    <h4>No se encontraron pedidos</h4>
                    <p>Intenta ajustar los criterios de búsqueda o filtros seleccionados.</p>
                </div>
            </div>

            <!-- Pagination Bar -->
            <div class="kfed-pagination-bar">
                <div class="kfed-pagination-info" id="kfed-pagination-info">
                    Mostrando 0 de 0 pedidos
                </div>
                <div class="kfed-pagination-controls" id="kfed-pagination-controls">
                    <!-- Buttons inserted dynamically -->
                </div>
            </div>

            <!-- Order Detail Modal -->
            <div id="kfed-modal" class="kfed-modal-backdrop" style="display:none;">
                <div class="kfed-modal-dialog">
                    <div class="kfed-modal-header">
                        <div>
                            <h3 id="kfed-modal-title">Pedido #----</h3>
                            <span id="kfed-modal-date" class="kfed-modal-subdate">--</span>
                        </div>
                        <button type="button" class="kfed-modal-close" id="kfed-modal-close-btn">&times;</button>
                    </div>
                    <div class="kfed-modal-body" id="kfed-modal-body">
                        <div class="kfed-spinner-center"><div class="kfed-spinner"></div></div>
                    </div>
                    <div class="kfed-modal-footer">
                        <button type="button" class="kfed-btn kfed-btn-secondary" id="kfed-modal-close-footer">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inline Styles -->
        <style>
        .kfed-wrapper {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            padding: 24px;
            margin: 20px 0;
            color: #1e293b;
            box-sizing: border-box;
        }
        .kfed-wrapper * { box-sizing: border-box; }
        .kfed-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f1f5f9;
        }
        .kfed-title-area h2 {
            margin: 0 0 4px 0;
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .kfed-subtitle {
            margin: 0;
            font-size: 14px;
            color: #64748b;
        }
        .kfed-header-actions {
            display: flex;
            gap: 12px;
        }
        .kfed-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none !important;
            border: 1px solid transparent;
        }
        .kfed-btn-primary {
            background: #2563eb;
            color: #ffffff !important;
        }
        .kfed-btn-primary:hover {
            background: #1d4ed8;
            box-shadow: 0 4px 12px rgba(37,99,235,0.25);
        }
        .kfed-btn-secondary {
            background: #f8fafc;
            color: #334155 !important;
            border-color: #cbd5e1;
        }
        .kfed-btn-secondary:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
        }

        /* Stats Grid */
        .kfed-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        .kfed-stat-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .kfed-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.04);
        }
        .kfed-stat-icon {
            width: 46px;
            height: 46px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .icon-blue { background: #dbeafe; color: #2563eb; }
        .icon-green { background: #dcfce7; color: #16a34a; }
        .icon-amber { background: #fef3c7; color: #d97706; }
        .icon-emerald { background: #ccfbf1; color: #0d9488; }

        .kfed-stat-info { display: flex; flex-direction: column; }
        .kfed-stat-label { font-size: 12px; font-weight: 600; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; }
        .kfed-stat-value { font-size: 20px; font-weight: 700; color: #0f172a; margin-top: 2px; }

        /* Filters Bar */
        .kfed-filters-bar {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
            background: #f1f5f9;
            padding: 14px;
            border-radius: 10px;
        }
        .kfed-search-box {
            position: relative;
            flex: 1;
            min-width: 260px;
            display: flex;
            align-items: center;
        }
        .kfed-search-box svg {
            position: absolute;
            left: 12px;
            color: #94a3b8;
            pointer-events: none;
        }
        .kfed-search-box input {
            width: 100%;
            padding: 9px 36px 9px 38px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-size: 14px;
            background: #ffffff;
            outline: none;
            transition: border-color 0.2s;
        }
        .kfed-search-box input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.15);
        }
        #kfed-btn-clear-search {
            position: absolute;
            right: 10px;
            background: transparent;
            border: none;
            font-size: 18px;
            color: #94a3b8;
            cursor: pointer;
            padding: 0;
            line-height: 1;
        }

        .kfed-select-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .kfed-select {
            padding: 9px 14px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-size: 14px;
            background: #ffffff;
            color: #334155;
            outline: none;
            cursor: pointer;
        }
        .kfed-select:focus { border-color: #2563eb; }

        /* Table Container */
        .kfed-table-container {
            position: relative;
            overflow-x: auto;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            min-height: 300px;
            background: #ffffff;
        }
        .kfed-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }
        .kfed-table th {
            background: #f8fafc;
            padding: 12px 16px;
            font-weight: 600;
            color: #475569;
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;
        }
        .kfed-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: middle;
        }
        .kfed-table tr:hover td { background: #f8fafc; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* Status Pills */
        .kfed-status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
            white-space: nowrap;
        }
        .status-completed { background: #dcfce7; color: #15803d; }
        .status-processing { background: #dbeafe; color: #1d4ed8; }
        .status-on-hold { background: #fef3c7; color: #b45309; }
        .status-pending { background: #e0e7ff; color: #4338ca; }
        .status-cancelled, .status-failed { background: #fee2e2; color: #b91c1c; }
        .status-refunded { background: #f3e8ff; color: #6b21a8; }

        /* Products column snippet */
        .kfed-prod-list {
            margin: 0;
            padding: 0;
            list-style: none;
            font-size: 13px;
            max-width: 280px;
        }
        .kfed-prod-item {
            color: #475569;
            line-height: 1.4;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .kfed-prod-qty { font-weight: 700; color: #0f172a; margin-right: 4px; }

        /* Actions */
        .kfed-btn-view {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 6px;
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #bfdbfe;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .kfed-btn-view:hover {
            background: #2563eb;
            color: #ffffff;
        }

        /* Empty State & Overlay */
        .kfed-loading-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255,255,255,0.85);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            z-index: 10;
            backdrop-filter: blur(2px);
        }
        .kfed-spinner {
            width: 32px;
            height: 32px;
            border: 3px solid #e2e8f0;
            border-top-color: #2563eb;
            border-radius: 50%;
            animation: kfed-spin 0.8s linear infinite;
        }
        @keyframes kfed-spin { to { transform: rotate(360deg); } }

        .kfed-empty-state {
            padding: 60px 20px;
            text-align: center;
            color: #64748b;
        }
        .kfed-empty-state svg { color: #cbd5e1; margin-bottom: 12px; }
        .kfed-empty-state h4 { margin: 0 0 6px 0; color: #334155; font-size: 16px; }
        .kfed-empty-state p { margin: 0; font-size: 14px; }

        /* Pagination Bar */
        .kfed-pagination-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid #f1f5f9;
        }
        .kfed-pagination-info { font-size: 14px; color: #64748b; }
        .kfed-pagination-controls { display: flex; gap: 6px; }
        .kfed-page-btn {
            min-width: 34px;
            height: 34px;
            padding: 0 10px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #334155;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .kfed-page-btn.active {
            background: #2563eb;
            color: #ffffff;
            border-color: #2563eb;
        }
        .kfed-page-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Modal / Drawer */
        .kfed-modal-backdrop {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(4px);
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .kfed-modal-dialog {
            background: #ffffff;
            width: 100%;
            max-width: 750px;
            max-height: 90vh;
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            animation: kfed-modal-in 0.2s ease-out;
        }
        @keyframes kfed-modal-in {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .kfed-modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
        }
        .kfed-modal-header h3 { margin: 0; font-size: 18px; font-weight: 700; color: #0f172a; }
        .kfed-modal-subdate { font-size: 13px; color: #64748b; margin-top: 2px; display: block; }
        .kfed-modal-close {
            background: transparent;
            border: none;
            font-size: 24px;
            color: #94a3b8;
            cursor: pointer;
            padding: 0;
            line-height: 1;
        }
        .kfed-modal-body {
            padding: 24px;
            overflow-y: auto;
            flex: 1;
        }
        .kfed-modal-footer {
            padding: 16px 24px;
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
            display: flex;
            justify-content: flex-end;
        }
        .kfed-spinner-center { display: flex; justify-content: center; padding: 40px; }

        /* Order Details Layout inside Modal */
        .kfed-detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
            background: #f8fafc;
            padding: 16px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .kfed-detail-box h5 { margin: 0 0 6px 0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b; }
        .kfed-detail-box p { margin: 0; font-size: 14px; color: #334155; line-height: 1.5; }

        .kfed-items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
            font-size: 14px;
        }
        .kfed-items-table th {
            text-align: left;
            padding: 10px 12px;
            background: #f1f5f9;
            color: #475569;
            font-weight: 600;
        }
        .kfed-items-table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        .kfed-totals-summary {
            margin-top: 20px;
            margin-left: auto;
            width: 100%;
            max-width: 320px;
        }
        .kfed-totals-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 14px;
            color: #475569;
        }
        .kfed-totals-row.final {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            border-top: 2px solid #e2e8f0;
            padding-top: 10px;
            margin-top: 4px;
        }
        </style>

        <!-- Inline JavaScript -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ajaxUrl = '<?php echo esc_js( $ajax_url ); ?>';
            const nonce = '<?php echo esc_js( $nonce ); ?>';
            const exportBaseUrl = '<?php echo esc_js( $export_url ); ?>';
            const activeKey = '<?php echo esc_js( $active_key_str ); ?>';

            // Ensure cookie is set in browser immediately if key is active
            if (activeKey) {
                const expectedHash = '<?php echo esc_js( hash( "sha256", "kfed_" . $secret_key ) ); ?>';
                document.cookie = "kfed_token=" + expectedHash + "; path=/; max-age=2592000; SameSite=Lax";
            }

            let state = {
                page: 1,
                limit: 25,
                search: '',
                status: 'all',
                date: 'all',
                orders: [],
                totalPages: 1,
                totalOrders: 0
            };

            // DOM Elements
            const inputSearch = document.getElementById('kfed-input-search');
            const btnClearSearch = document.getElementById('kfed-btn-clear-search');
            const selectStatus = document.getElementById('kfed-select-status');
            const selectDate = document.getElementById('kfed-select-date');
            const selectLimit = document.getElementById('kfed-select-limit');
            const btnRefresh = document.getElementById('kfed-btn-refresh');
            const btnExport = document.getElementById('kfed-btn-export');
            
            const tableBody = document.getElementById('kfed-table-body');
            const loadingOverlay = document.getElementById('kfed-loading-overlay');
            const emptyState = document.getElementById('kfed-empty-state');
            const paginationInfo = document.getElementById('kfed-pagination-info');
            const paginationControls = document.getElementById('kfed-pagination-controls');

            const modal = document.getElementById('kfed-modal');
            const modalBody = document.getElementById('kfed-modal-body');
            const modalTitle = document.getElementById('kfed-modal-title');
            const modalSubdate = document.getElementById('kfed-modal-subdate');
            const modalCloseBtns = [
                document.getElementById('kfed-modal-close-btn'),
                document.getElementById('kfed-modal-close-footer')
            ];

            let searchDebounceTimer = null;

            // Initialize
            fetchOrders();

            // Event Listeners
            inputSearch.addEventListener('input', function(e) {
                const val = e.target.value.trim();
                btnClearSearch.style.display = val ? 'block' : 'none';
                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(() => {
                    state.search = val;
                    state.page = 1;
                    fetchOrders();
                }, 350);
            });

            btnClearSearch.addEventListener('click', function() {
                inputSearch.value = '';
                btnClearSearch.style.display = 'none';
                state.search = '';
                state.page = 1;
                fetchOrders();
            });

            selectStatus.addEventListener('change', function(e) {
                state.status = e.target.value;
                state.page = 1;
                fetchOrders();
            });

            selectDate.addEventListener('change', function(e) {
                state.date = e.target.value;
                state.page = 1;
                fetchOrders();
            });

            selectLimit.addEventListener('change', function(e) {
                state.limit = parseInt(e.target.value, 10);
                state.page = 1;
                fetchOrders();
            });

            btnRefresh.addEventListener('click', function() {
                fetchOrders();
            });

            modalCloseBtns.forEach(btn => {
                if (btn) {
                    btn.addEventListener('click', () => {
                        modal.style.display = 'none';
                    });
                }
            });

            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });

            function updateExportUrl() {
                let url = new URL(exportBaseUrl);
                if (activeKey) url.searchParams.set('key', activeKey);
                if (state.status !== 'all') url.searchParams.set('status', state.status);
                if (state.date !== 'all') url.searchParams.set('date', state.date);
                if (state.search) url.searchParams.set('s', state.search);
                btnExport.href = url.toString();
            }

            function fetchOrders() {
                loadingOverlay.style.display = 'flex';
                updateExportUrl();

                const formData = new FormData();
                formData.append('action', 'knowledgism_fed_get_orders');
                formData.append('nonce', nonce);
                if (activeKey) {
                    formData.append('key', activeKey);
                }
                formData.append('page', state.page);
                formData.append('limit', state.limit);
                formData.append('status', state.status);
                formData.append('date', state.date);
                formData.append('search', state.search);

                fetch(ajaxUrl, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(res => {
                    loadingOverlay.style.display = 'none';
                    if (res.success) {
                        renderDashboard(res.data);
                    } else {
                        alert(res.data || 'Error al obtener los pedidos');
                    }
                })
                .catch(err => {
                    loadingOverlay.style.display = 'none';
                    console.error('AJAX Error:', err);
                });
            }

            function renderDashboard(data) {
                state.totalOrders = data.total_orders || 0;
                state.totalPages = data.total_pages || 1;

                // Update Stats
                document.getElementById('kfed-stat-total-orders').textContent = data.stats.total_count;
                document.getElementById('kfed-stat-total-sales').innerHTML = data.stats.formatted_total_sales;
                document.getElementById('kfed-stat-processing').textContent = data.stats.processing_count;
                document.getElementById('kfed-stat-completed').textContent = data.stats.completed_count;

                // Table
                tableBody.innerHTML = '';
                if (!data.orders || data.orders.length === 0) {
                    emptyState.style.display = 'block';
                } else {
                    emptyState.style.display = 'none';
                    data.orders.forEach(order => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>
                                <strong style="color:#0f172a;">#${order.order_number}</strong>
                            </td>
                            <td style="white-space:nowrap;">${order.date_created}</td>
                            <td>
                                <span class="kfed-status-badge status-${order.status}">${order.status_name}</span>
                            </td>
                            <td>
                                <div style="font-weight:600; color:#0f172a;">${order.customer_name}</div>
                                <div style="font-size:12px; color:#64748b;">${order.customer_email}</div>
                            </td>
                            <td>
                                <ul class="kfed-prod-list">
                                    ${order.items.map(item => `
                                        <li class="kfed-prod-item">
                                            <span class="kfed-prod-qty">${item.qty}x</span> ${item.name}
                                        </li>
                                    `).join('')}
                                </ul>
                            </td>
                            <td class="text-right" style="font-weight:700; color:#0f172a; white-space:nowrap;">
                                ${order.formatted_total}
                            </td>
                            <td class="text-center">
                                <button type="button" class="kfed-btn-view" data-id="${order.id}">Ver Detalle</button>
                            </td>
                        `;
                        tableBody.appendChild(tr);
                    });

                    // Attach click handlers to "Ver Detalle"
                    document.querySelectorAll('.kfed-btn-view').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const orderId = this.getAttribute('data-id');
                            openOrderDetail(orderId);
                        });
                    });
                }

                // Render Pagination Info & Controls
                const start = state.totalOrders === 0 ? 0 : (state.page - 1) * state.limit + 1;
                const end = Math.min(state.page * state.limit, state.totalOrders);
                paginationInfo.textContent = `Mostrando ${start} - ${end} de ${state.totalOrders} pedidos`;

                renderPaginationControls();
            }

            function renderPaginationControls() {
                paginationControls.innerHTML = '';
                if (state.totalPages <= 1) return;

                // Previous
                const prevBtn = document.createElement('button');
                prevBtn.className = 'kfed-page-btn';
                prevBtn.textContent = '«';
                prevBtn.disabled = state.page === 1;
                prevBtn.addEventListener('click', () => {
                    if (state.page > 1) {
                        state.page--;
                        fetchOrders();
                    }
                });
                paginationControls.appendChild(prevBtn);

                // Page Numbers
                for (let i = 1; i <= state.totalPages; i++) {
                    if (i === 1 || i === state.totalPages || (i >= state.page - 2 && i <= state.page + 2)) {
                        const btn = document.createElement('button');
                        btn.className = `kfed-page-btn ${i === state.page ? 'active' : ''}`;
                        btn.textContent = i;
                        btn.addEventListener('click', () => {
                            state.page = i;
                            fetchOrders();
                        });
                        paginationControls.appendChild(btn);
                    } else if (i === state.page - 3 || i === state.page + 3) {
                        const dots = document.createElement('span');
                        dots.style.padding = '0 4px';
                        dots.textContent = '...';
                        paginationControls.appendChild(dots);
                    }
                }

                // Next
                const nextBtn = document.createElement('button');
                nextBtn.className = 'kfed-page-btn';
                nextBtn.textContent = '»';
                nextBtn.disabled = state.page === state.totalPages;
                nextBtn.addEventListener('click', () => {
                    if (state.page < state.totalPages) {
                        state.page++;
                        fetchOrders();
                    }
                });
                paginationControls.appendChild(nextBtn);
            }

            function openOrderDetail(orderId) {
                modal.style.display = 'flex';
                modalTitle.textContent = `Pedido #${orderId}`;
                modalSubdate.textContent = 'Cargando detalles...';
                modalBody.innerHTML = '<div class="kfed-spinner-center"><div class="kfed-spinner"></div></div>';

                const formData = new FormData();
                formData.append('action', 'knowledgism_fed_get_order_details');
                formData.append('nonce', nonce);
                if (activeKey) {
                    formData.append('key', activeKey);
                }
                formData.append('order_id', orderId);

                fetch(ajaxUrl, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        renderOrderDetailModal(res.data);
                    } else {
                        modalBody.innerHTML = `<div class="kfed-alert kfed-alert-error">${res.data || 'Error al obtener detalles.'}</div>`;
                    }
                })
                .catch(err => {
                    modalBody.innerHTML = '<div class="kfed-alert kfed-alert-error">Error al conectar con el servidor.</div>';
                });
            }

            function renderOrderDetailModal(d) {
                modalTitle.textContent = `Pedido #${d.order_number}`;
                modalSubdate.textContent = `Fecha: ${d.date_created} | Estado: ${d.status_name}`;

                modalBody.innerHTML = `
                    <div class="kfed-detail-grid">
                        <div class="kfed-detail-box">
                            <h5>Cliente</h5>
                            <p><strong>${d.customer_name}</strong></p>
                            <p><a href="mailto:${d.customer_email}" style="color:#2563eb;">${d.customer_email}</a></p>
                            <p>${d.customer_phone || '-'}</p>
                        </div>
                        <div class="kfed-detail-box">
                            <h5>Facturación y Dirección</h5>
                            <p>${d.billing_address || '-'}</p>
                            <p>${d.billing_city} ${d.billing_postcode}</p>
                            <p>${d.billing_country}</p>
                        </div>
                        <div class="kfed-detail-box">
                            <h5>Método de Pago</h5>
                            <p><strong>${d.payment_method_title || 'N/A'}</strong></p>
                        </div>
                    </div>

                    ${d.customer_note ? `
                        <div style="background:#fffbebf0; border:1px solid #fde68a; padding:12px 16px; border-radius:8px; margin-bottom:20px;">
                            <strong style="color:#92400e; font-size:12px; text-transform:uppercase;">Nota del Cliente:</strong>
                            <p style="margin:4px 0 0 0; color:#78350f; font-size:14px;">${d.customer_note}</p>
                        </div>
                    ` : ''}

                    <h4 style="margin:20px 0 10px 0; font-size:16px; color:#0f172a;">Productos Solicitados</h4>
                    <table class="kfed-items-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>SKU</th>
                                <th class="text-center">Cant.</th>
                                <th class="text-right">Precio Unit.</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${d.items.map(item => `
                                <tr>
                                    <td><strong>${item.name}</strong></td>
                                    <td style="color:#64748b; font-size:12px;">${item.sku || '-'}</td>
                                    <td class="text-center"><strong>${item.qty}</strong></td>
                                    <td class="text-right">${item.formatted_unit_price}</td>
                                    <td class="text-right"><strong>${item.formatted_total}</strong></td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>

                    <div class="kfed-totals-summary">
                        <div class="kfed-totals-row">
                            <span>Subtotal:</span>
                            <span>${d.formatted_subtotal}</span>
                        </div>
                        ${parseFloat(d.discount_total) > 0 ? `
                            <div class="kfed-totals-row" style="color:#16a34a;">
                                <span>Descuento:</span>
                                <span>-${d.formatted_discount_total}</span>
                            </div>
                        ` : ''}
                        ${parseFloat(d.shipping_total) > 0 ? `
                            <div class="kfed-totals-row">
                                <span>Envío:</span>
                                <span>${d.formatted_shipping_total}</span>
                            </div>
                        ` : ''}
                        ${parseFloat(d.total_tax) > 0 ? `
                            <div class="kfed-totals-row">
                                <span>Impuestos:</span>
                                <span>${d.formatted_total_tax}</span>
                            </div>
                        ` : ''}
                        <div class="kfed-totals-row final">
                            <span>Total:</span>
                            <span>${d.formatted_total}</span>
                        </div>
                    </div>
                `;
            }
        });
        </script>
        <?php
        return ob_get_clean();
    }

    /**
     * Render Access Prompt with In-Line WordPress Login Form + Key Bypass
     */
    private static function render_access_prompt() {
        $ajax_url = admin_url( 'admin-ajax.php' );
        $nonce = wp_create_nonce( 'knowledgism_fed_nonce' );
        ob_start();
        ?>
        <div style="background:#ffffff; border-radius:16px; box-shadow:0 10px 30px rgba(0,0,0,0.08); padding:40px 32px; max-width:480px; margin:40px auto; font-family:-apple-system,BlinkMacSystemFont,sans-serif; text-align:center; color:#1e293b;">
            <div style="width:56px; height:56px; background:#dbeafe; color:#2563eb; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; margin-bottom:16px;">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </div>
            
            <h3 style="margin:0 0 6px 0; color:#0f172a; font-size:22px; font-weight:700;">Acceso al Panel de Pedidos</h3>
            <p style="margin:0 0 24px 0; color:#64748b; font-size:14px; line-height:1.5;">Ingresa con tus credenciales de usuario o utiliza la Clave de Acceso Rápida.</p>

            <!-- Access Type Tabs -->
            <div style="display:flex; background:#f1f5f9; padding:4px; border-radius:10px; margin-bottom:24px;">
                <button type="button" id="kfed-tab-wp" style="flex:1; padding:8px; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; background:#ffffff; color:#0f172a; shadow:0 2px 4px rgba(0,0,0,0.04);">Usuario WordPress</button>
                <button type="button" id="kfed-tab-key" style="flex:1; padding:8px; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; background:transparent; color:#64748b;">Clave Rápida</button>
            </div>

            <!-- WordPress In-Line Login Form -->
            <form id="kfed-wp-login-form" style="display:block; text-align:left;">
                <div id="kfed-login-error" style="display:none; background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:10px 14px; border-radius:8px; font-size:13px; margin-bottom:16px;"></div>

                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:12px; font-weight:700; text-transform:uppercase; color:#475569; margin-bottom:6px;">Usuario o Correo Electrónico</label>
                    <input type="text" id="kfed-login-username" required placeholder="tu_usuario@email.com" style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:14px; outline:none; box-sizing:border-box;" />
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:12px; font-weight:700; text-transform:uppercase; color:#475569; margin-bottom:6px;">Contraseña</label>
                    <input type="password" id="kfed-login-password" required placeholder="••••••••" style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:14px; outline:none; box-sizing:border-box;" />
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                    <label style="font-size:13px; color:#475569; display:flex; align-items:center; gap:6px; cursor:pointer;">
                        <input type="checkbox" id="kfed-login-remember" style="border-radius:4px;" /> Recordarme
                    </label>
                </div>

                <button type="submit" id="kfed-login-submit-btn" style="width:100%; background:#2563eb; color:#ffffff; border:none; padding:12px; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer; transition:background 0.2s;">
                    Iniciar Sesión
                </button>
            </form>

            <!-- Key Bypass Form -->
            <form id="kfed-key-login-form" method="post" action="" style="display:none; text-align:left;">
                <label style="display:block; font-size:12px; font-weight:700; text-transform:uppercase; color:#475569; margin-bottom:6px;">Clave de Acceso Rápida</label>
                <div style="display:flex; gap:8px;">
                    <input type="password" name="kfed_access_pass" placeholder="Ingresa la clave..." required style="flex:1; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:14px; outline:none;" />
                    <button type="submit" style="background:#2563eb; color:#ffffff; border:none; padding:10px 18px; border-radius:8px; font-weight:600; cursor:pointer; font-size:14px;">Entrar</button>
                </div>
                <p style="margin:12px 0 0 0; font-size:12px; color:#64748b;">Clave predeterminada: <code>knowledgism2026</code></p>
            </form>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabWp = document.getElementById('kfed-tab-wp');
            const tabKey = document.getElementById('kfed-tab-key');
            const formWp = document.getElementById('kfed-wp-login-form');
            const formKey = document.getElementById('kfed-key-login-form');
            const loginError = document.getElementById('kfed-login-error');
            const submitBtn = document.getElementById('kfed-login-submit-btn');

            if (tabWp && tabKey) {
                tabWp.addEventListener('click', function() {
                    tabWp.style.background = '#ffffff';
                    tabWp.style.color = '#0f172a';
                    tabKey.style.background = 'transparent';
                    tabKey.style.color = '#64748b';
                    formWp.style.display = 'block';
                    formKey.style.display = 'none';
                });

                tabKey.addEventListener('click', function() {
                    tabKey.style.background = '#ffffff';
                    tabKey.style.color = '#0f172a';
                    tabWp.style.background = 'transparent';
                    tabWp.style.color = '#64748b';
                    formKey.style.display = 'block';
                    formWp.style.display = 'none';
                });
            }

            if (formWp) {
                formWp.addEventListener('submit', function(e) {
                    e.preventDefault();
                    loginError.style.display = 'none';
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Autenticando...';

                    const username = document.getElementById('kfed-login-username').value;
                    const password = document.getElementById('kfed-login-password').value;
                    const remember = document.getElementById('kfed-login-remember').checked;

                    const formData = new FormData();
                    formData.append('action', 'knowledgism_fed_ajax_login');
                    formData.append('nonce', '<?php echo esc_js( $nonce ); ?>');
                    formData.append('username', username);
                    formData.append('password', password);
                    if (remember) formData.append('remember', '1');

                    fetch('<?php echo esc_js( $ajax_url ); ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            window.location.reload();
                        } else {
                            submitBtn.disabled = false;
                            submitBtn.textContent = 'Iniciar Sesión';
                            loginError.textContent = res.data || 'Error al iniciar sesión.';
                            loginError.style.display = 'block';
                        }
                    })
                    .catch(err => {
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Iniciar Sesión';
                        loginError.textContent = 'Error al conectar con el servidor.';
                        loginError.style.display = 'block';
                    });
                });
            }
        });
        </script>
        <?php
        return ob_get_clean();
    }

    /**
     * AJAX handler to fetch filtered orders
     */
    public static function ajax_get_orders() {
        if ( ! self::user_can_access() ) {
            wp_send_json_error( 'No tienes permisos para acceder.' );
        }

        $page   = isset( $_POST['page'] ) ? max( 1, intval( $_POST['page'] ) ) : 1;
        $limit  = isset( $_POST['limit'] ) ? intval( $_POST['limit'] ) : 25;
        $status = isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : 'all';
        $date   = isset( $_POST['date'] ) ? sanitize_text_field( $_POST['date'] ) : 'all';
        $search = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';

        $query_args = array(
            'page'     => $page,
            'limit'    => $limit,
            'paginate' => true,
            'return'   => 'objects',
            'type'     => 'shop_order',
        );

        if ( 'all' !== $status ) {
            $query_args['status'] = $status;
        } else {
            $query_args['status'] = array_keys( wc_get_order_statuses() );
        }

        if ( 'all' !== $date ) {
            $today = date( 'Y-m-d' );
            if ( 'today' === $date ) {
                $query_args['date_created'] = $today;
            } elseif ( '7days' === $date ) {
                $query_args['date_created'] = date( 'Y-m-d', strtotime( '-7 days' ) ) . '...' . $today;
            } elseif ( 'this_month' === $date ) {
                $query_args['date_created'] = date( 'Y-m-01' ) . '...' . date( 'Y-m-t' );
            } elseif ( 'last_month' === $date ) {
                $first_day = date( 'Y-m-01', strtotime( 'first day of last month' ) );
                $last_day  = date( 'Y-m-t', strtotime( 'last day of last month' ) );
                $query_args['date_created'] = $first_day . '...' . $last_day;
            }
        }

        if ( ! empty( $search ) ) {
            $query_args['s'] = $search;
        }

        $results = wc_get_orders( $query_args );

        $orders = array();
        if ( ! empty( $results->orders ) ) {
            foreach ( $results->orders as $order ) {
                $items = array();
                foreach ( $order->get_items() as $item ) {
                    $items[] = array(
                        'name' => $item->get_name(),
                        'qty'  => $item->get_quantity(),
                    );
                }

                $orders[] = array(
                    'id'              => $order->get_id(),
                    'order_number'    => $order->get_order_number(),
                    'date_created'    => $order->get_date_created() ? $order->get_date_created()->date( 'd/m/Y H:i' ) : '',
                    'status'          => $order->get_status(),
                    'status_name'     => wc_get_order_status_name( $order->get_status() ),
                    'customer_name'   => trim( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ),
                    'customer_email'  => $order->get_billing_email(),
                    'formatted_total' => $order->get_formatted_order_total(),
                    'items'           => $items,
                );
            }
        }

        // Stats calculation
        $stats_total_count = 0;
        $stats_sales_amount = 0.0;
        $stats_processing_count = 0;
        $stats_completed_count = 0;

        $all_counts = wc_get_order_statuses();
        foreach ( array_keys( $all_counts ) as $st_key ) {
            $st_slug = str_replace( 'wc-', '', $st_key );
            $cnt = wc_orders_count( $st_slug );
            $stats_total_count += $cnt;
            if ( in_array( $st_slug, array( 'processing', 'pending', 'on-hold' ), true ) ) {
                $stats_processing_count += $cnt;
            } elseif ( 'completed' === $st_slug ) {
                $stats_completed_count += $cnt;
            }
        }

        $total_sales_query = wc_get_orders( array(
            'status' => array( 'completed', 'processing' ),
            'limit'  => -1,
            'return' => 'objects',
        ) );
        foreach ( $total_sales_query as $so ) {
            $stats_sales_amount += (float) $so->get_total();
        }

        wp_send_json_success( array(
            'orders'       => $orders,
            'total_orders' => $results->total,
            'total_pages'  => $results->max_num_pages,
            'stats'        => array(
                'total_count'           => number_format_i18n( $stats_total_count ),
                'formatted_total_sales' => wc_price( $stats_sales_amount ),
                'processing_count'      => number_format_i18n( $stats_processing_count ),
                'completed_count'       => number_format_i18n( $stats_completed_count ),
            ),
        ) );
    }

    /**
     * AJAX handler to get complete order details
     */
    public static function ajax_get_order_details() {
        if ( ! self::user_can_access() ) {
            wp_send_json_error( 'No tienes permisos para acceder.' );
        }

        $order_id = isset( $_POST['order_id'] ) ? intval( $_POST['order_id'] ) : 0;
        $order = wc_get_order( $order_id );

        if ( ! $order ) {
            wp_send_json_error( 'Pedido no encontrado.' );
        }

        $items = array();
        foreach ( $order->get_items() as $item ) {
            $product = $item->get_product();
            $items[] = array(
                'name'                 => $item->get_name(),
                'qty'                  => $item->get_quantity(),
                'sku'                  => $product ? $product->get_sku() : '',
                'unit_price'           => $order->get_item_subtotal( $item, false, true ),
                'formatted_unit_price' => wc_price( $order->get_item_subtotal( $item, false, true ) ),
                'total'                => $item->get_total(),
                'formatted_total'      => wc_price( $item->get_total() ),
            );
        }

        $data = array(
            'id'                       => $order->get_id(),
            'order_number'             => $order->get_order_number(),
            'date_created'             => $order->get_date_created() ? $order->get_date_created()->date( 'd/m/Y H:i' ) : '',
            'status'                   => $order->get_status(),
            'status_name'              => wc_get_order_status_name( $order->get_status() ),
            'customer_name'            => trim( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ),
            'customer_email'           => $order->get_billing_email(),
            'customer_phone'           => $order->get_billing_phone(),
            'billing_address'          => trim( $order->get_billing_address_1() . ' ' . $order->get_billing_address_2() ),
            'billing_city'             => $order->get_billing_city(),
            'billing_postcode'         => $order->get_billing_postcode(),
            'billing_country'          => $order->get_billing_country(),
            'payment_method_title'     => $order->get_payment_method_title(),
            'customer_note'            => $order->get_customer_note(),
            'subtotal'                 => $order->get_subtotal(),
            'formatted_subtotal'       => wc_price( $order->get_subtotal() ),
            'discount_total'           => $order->get_discount_total(),
            'formatted_discount_total' => wc_price( $order->get_discount_total() ),
            'shipping_total'           => $order->get_shipping_total(),
            'formatted_shipping_total' => wc_price( $order->get_shipping_total() ),
            'total_tax'                => $order->get_total_tax(),
            'formatted_total_tax'      => wc_price( $order->get_total_tax() ),
            'total'                    => $order->get_total(),
            'formatted_total'          => wc_price( $order->get_total() ),
            'items'                    => $items,
        );

        wp_send_json_success( $data );
    }

    /**
     * CSV Export Handler
     */
    public static function handle_csv_export() {
        if ( ! self::user_can_access() ) {
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
        } else {
            $query_args['status'] = array_keys( wc_get_order_statuses() );
        }

        if ( ! empty( $_GET['date'] ) && 'all' !== $_GET['date'] ) {
            $date  = sanitize_text_field( $_GET['date'] );
            $today = date( 'Y-m-d' );
            if ( 'today' === $date ) {
                $query_args['date_created'] = $today;
            } elseif ( '7days' === $date ) {
                $query_args['date_created'] = date( 'Y-m-d', strtotime( '-7 days' ) ) . '...' . $today;
            } elseif ( 'this_month' === $date ) {
                $query_args['date_created'] = date( 'Y-m-01' ) . '...' . date( 'Y-m-t' );
            } elseif ( 'last_month' === $date ) {
                $first_day = date( 'Y-m-01', strtotime( 'first day of last month' ) );
                $last_day  = date( 'Y-m-t', strtotime( 'last day of last month' ) );
                $query_args['date_created'] = $first_day . '...' . $last_day;
            }
        }

        if ( ! empty( $_GET['s'] ) ) {
            $query_args['s'] = sanitize_text_field( $_GET['s'] );
        }

        $orders = wc_get_orders( $query_args );

        $filename = 'pedidos-frontend-' . date( 'Y-m-d_H-i-s' ) . '.csv';

        header( 'Content-Type: text/csv; charset=UTF-8' );
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
        header( 'Pragma: no-cache' );
        header( 'Expires: 0' );

        $output = fopen( 'php://output', 'w' );
        fputs( $output, "\xEF\xBB\xBF" ); // UTF-8 BOM

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
            'Cantidad Total',
            'Subtotal',
            'Descuento',
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
                $items         = $order->get_items();
                $product_names = array();
                $product_skus  = array();
                $total_qty     = 0;

                foreach ( $items as $item ) {
                    $qty            = $item->get_quantity();
                    $total_qty     += $qty;
                    $name           = $item->get_name();
                    $product        = $item->get_product();
                    $sku            = $product ? $product->get_sku() : '';
                    $product_names[] = $qty > 1 ? $qty . 'x ' . $name : $name;
                    if ( ! empty( $sku ) ) {
                        $product_skus[] = $sku;
                    }
                }

                $date_created = $order->get_date_created() ? $order->get_date_created()->date( 'Y-m-d H:i:s' ) : '';

                fputcsv( $output, array(
                    $order->get_id(),
                    $order->get_order_number(),
                    $date_created,
                    wc_get_order_status_name( $order->get_status() ),
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
}

Knowledgism_Frontend_Orders_Dashboard::init();
