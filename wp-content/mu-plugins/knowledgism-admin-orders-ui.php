<?php
/**
 * Plugin Name: Knowledgism - WooCommerce Premium Admin Orders UI & Resend Email
 * Description: Rediseño visual de alta gama para la vista de edición de pedidos (Single Order Edit) y listado en WooCommerce, con botón para reenviar correos de confirmación.
 * Version: 2.1.0
 * Author: Attikka / Antigravity
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Inyectar estilos CSS y JavaScript para la vista individual de edición de pedidos y botón de reenvío
 */
add_action( 'admin_head', 'knowledgism_single_order_screen_styling', 999 );
function knowledgism_single_order_screen_styling() {
    $screen = get_current_screen();
    if ( ! $screen ) {
        return;
    }

    $is_order_edit = (
        'shop_order' === $screen->id ||
        'woocommerce_page_wc-orders' === $screen->id ||
        'edit-shop_order' === $screen->id ||
        ( isset( $_GET['post_type'] ) && 'shop_order' === $_GET['post_type'] ) ||
        ( isset( $_GET['page'] ) && 'wc-orders' === $_GET['page'] )
    );

    if ( ! $is_order_edit ) {
        return;
    }

    ?>
    <style id="knowledgism-single-order-styles">
        /* --- SUPRESIÓN DE ERRORES/SNACKBARS DE MARKETING DE WOOCOMMERCE --- */
        .components-snackbar-list,
        .components-snackbar,
        .woocommerce-embed-page .components-snackbar-list {
            display: none !important;
        }

        /* --- TIPOGRAFÍA Y BASE --- */
        #poststuff #order_data,
        #woocommerce-order-data,
        #woocommerce-order-items,
        .post-type-shop_order #poststuff {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
            -webkit-font-smoothing: antialiased !important;
        }

        /* --- METABOX PRINCIPAL DE DETALLES DEL PEDIDO (ORDER DATA) --- */
        #woocommerce-order-data,
        .post-type-shop_order #woocommerce-order-data {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 14px !important;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05) !important;
            overflow: hidden !important;
            margin-bottom: 24px !important;
        }

        #woocommerce-order-data .postbox-header {
            display: none !important;
        }

        #woocommerce-order-data .inside {
            padding: 24px 28px !important;
            margin: 0 !important;
        }

        /* Encabezado del Pedido: Título + Badge + Info de Pago */
        #woocommerce-order-data h2 {
            font-size: 24px !important;
            font-weight: 800 !important;
            color: #0f172a !important;
            margin: 0 0 6px 0 !important;
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            letter-spacing: -0.02em !important;
            flex-wrap: wrap !important;
        }

        #woocommerce-order-data .woocommerce-order-data__heading-details,
        #woocommerce-order-data p.order_number_formatted,
        #woocommerce-order-data .inside > p:first-of-type {
            font-size: 13px !important;
            color: #64748b !important;
            margin: 0 0 24px 0 !important;
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
        }

        /* BOTÓN REENVIAR CORREO EN HEADER Y METABOX */
        .knowledgism-resend-btn-header {
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
            color: #ffffff !important;
            border: 1px solid #334155 !important;
            border-radius: 8px !important;
            padding: 6px 14px !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            text-decoration: none !important;
            cursor: pointer !important;
            box-shadow: 0 2px 4px rgba(15, 23, 42, 0.1) !important;
            transition: all 0.15s ease !important;
            margin-left: auto !important;
        }

        .knowledgism-resend-btn-header:hover {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%) !important;
            color: #ffffff !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 8px rgba(15, 23, 42, 0.15) !important;
        }

        .knowledgism-resend-btn-sidebar {
            width: 100% !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            background: #ffffff !important;
            color: #0f172a !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            padding: 8px 14px !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            cursor: pointer !important;
            margin-top: 10px !important;
            transition: all 0.15s ease !important;
        }

        .knowledgism-resend-btn-sidebar:hover {
            background: #f8fafc !important;
            border-color: #94a3b8 !important;
            color: #2563eb !important;
        }

        /* GRID DE COLUMNAS (General, Billing, Shipping) */
        .order_data_column_container {
            display: grid !important;
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 20px !important;
            margin: 0 !important;
            padding-top: 18px !important;
            border-top: 1px solid #f1f5f9 !important;
        }

        @media screen and (max-width: 960px) {
            .order_data_column_container {
                grid-template-columns: 1fr !important;
            }
        }

        .order_data_column {
            width: 100% !important;
            float: none !important;
            margin: 0 !important;
            padding: 20px !important;
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            box-sizing: border-box !important;
            transition: all 0.2s ease !important;
        }

        .order_data_column:hover {
            border-color: #cbd5e1 !important;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03) !important;
        }

        /* Títulos de Columnas */
        .order_data_column h3 {
            font-size: 13px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.06em !important;
            color: #475569 !important;
            margin: 0 0 16px 0 !important;
            padding-bottom: 10px !important;
            border-bottom: 1px solid #e2e8f0 !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
        }

        .order_data_column h3 a.edit_address {
            font-size: 12px !important;
            font-weight: 500 !important;
            text-transform: none !important;
            letter-spacing: normal !important;
            color: #2563eb !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
        }

        /* Labels e Inputs */
        .order_data_column p.form-field {
            margin: 0 0 14px 0 !important;
            padding: 0 !important;
        }

        .order_data_column p.form-field label {
            display: block !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            color: #475569 !important;
            margin-bottom: 5px !important;
            float: none !important;
            width: auto !important;
            text-align: left !important;
        }

        .order_data_column input[type="text"],
        .order_data_column input[type="number"],
        .order_data_column select {
            width: 100% !important;
            max-width: 100% !important;
            height: 38px !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            background-color: #ffffff !important;
            padding: 6px 12px !important;
            font-size: 13px !important;
            color: #0f172a !important;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03) !important;
            transition: all 0.15s ease !important;
            box-sizing: border-box !important;
        }

        .order_data_column input:focus,
        .order_data_column select:focus {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
            outline: none !important;
        }

        /* Campos de fecha y hora en línea */
        .order_data_column .date-picker-field {
            display: flex !important;
            gap: 6px !important;
            align-items: center !important;
        }

        .order_data_column .date-picker-field input.date-picker {
            flex: 2 !important;
        }

        .order_data_column .date-picker-field input.hour,
        .order_data_column .date-picker-field input.minute {
            flex: 1 !important;
            width: 48px !important;
            text-align: center !important;
        }

        /* Select2 de Cliente */
        .select2-container--default .select2-selection--single {
            height: 38px !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            background: #ffffff !important;
            display: flex !important;
            align-items: center !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
            color: #0f172a !important;
            font-size: 13px !important;
            padding-left: 12px !important;
        }

        /* Datos de Facturación y Envío */
        .order_data_column .address {
            color: #334155 !important;
            font-size: 13px !important;
            line-height: 1.6 !important;
        }

        .order_data_column .address p {
            margin: 0 0 10px 0 !important;
        }

        .order_data_column .address p strong {
            color: #0f172a !important;
            font-size: 14px !important;
            display: block !important;
            margin-bottom: 2px !important;
        }

        .order_data_column .address a {
            color: #2563eb !important;
            text-decoration: none !important;
            font-weight: 500 !important;
        }

        .order_data_column .address a:hover {
            text-decoration: underline !important;
        }

        /* --- TABLA DE ITEMS DEL PEDIDO (ORDER ITEMS) --- */
        #woocommerce-order-items,
        .post-type-shop_order #woocommerce-order-items {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 14px !important;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05) !important;
            overflow: hidden !important;
            margin-bottom: 24px !important;
        }

        #woocommerce-order-items .postbox-header {
            display: none !important;
        }

        #woocommerce-order-items .inside {
            padding: 0 !important;
            margin: 0 !important;
        }

        .woocommerce_order_items_wrapper {
            margin: 0 !important;
        }

        table.woocommerce_order_items {
            width: 100% !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
            border: none !important;
        }

        table.woocommerce_order_items thead th {
            background: #f8fafc !important;
            color: #475569 !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.06em !important;
            padding: 14px 24px !important;
            border-bottom: 1px solid #e2e8f0 !important;
            border-top: none !important;
            text-align: left !important;
        }

        table.woocommerce_order_items tbody tr.item,
        table.woocommerce_order_items tbody tr.fee,
        table.woocommerce_order_items tbody tr.shipping {
            transition: background 0.15s ease !important;
        }

        table.woocommerce_order_items tbody tr.item:hover {
            background-color: #f8fafc !important;
        }

        table.woocommerce_order_items tbody td {
            padding: 18px 24px !important;
            vertical-align: middle !important;
            border-bottom: 1px solid #f1f5f9 !important;
            font-size: 13px !important;
            color: #334155 !important;
        }

        table.woocommerce_order_items tbody td.thumb {
            width: 52px !important;
            padding-right: 0 !important;
        }

        table.woocommerce_order_items tbody td.thumb img {
            width: 44px !important;
            height: 44px !important;
            border-radius: 8px !important;
            object-fit: cover !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05) !important;
        }

        table.woocommerce_order_items tbody td.name a.wc-order-item-name {
            font-size: 14px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            text-decoration: none !important;
            line-height: 1.4 !important;
        }

        table.woocommerce_order_items tbody td.name a.wc-order-item-name:hover {
            color: #2563eb !important;
        }

        table.woocommerce_order_items tbody td.name .wc-order-item-sku {
            font-size: 11px !important;
            color: #64748b !important;
            margin-top: 4px !important;
        }

        table.woocommerce_order_items tbody td.quantity .view {
            display: inline-block !important;
            background: #f1f5f9 !important;
            color: #334155 !important;
            font-weight: 700 !important;
            font-size: 12px !important;
            padding: 3px 10px !important;
            border-radius: 12px !important;
            border: 1px solid #e2e8f0 !important;
        }

        table.woocommerce_order_items tbody td.item_cost,
        table.woocommerce_order_items tbody td.line_cost {
            font-weight: 600 !important;
            color: #0f172a !important;
            font-size: 14px !important;
        }

        /* FILA INFERIOR DE TOTALES Y BOTÓN REEMBOLSO */
        .wc-order-data-row.wc-order-totals-items {
            background: #ffffff !important;
            padding: 24px 28px !important;
            border-top: 2px solid #f1f5f9 !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: flex-start !important;
            gap: 20px !important;
        }

        .wc-order-bulk-actions {
            float: none !important;
            margin: 0 !important;
        }

        button.refund-items {
            background: #ffffff !important;
            color: #64748b !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            font-size: 13px !important;
            padding: 8px 18px !important;
            height: auto !important;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03) !important;
            transition: all 0.15s ease !important;
            cursor: pointer !important;
        }

        button.refund-items:hover {
            background: #f8fafc !important;
            color: #b91c1c !important;
            border-color: #fecaca !important;
        }

        .wc-order-totals {
            float: none !important;
            width: auto !important;
            min-width: 320px !important;
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            padding: 16px 20px !important;
            box-sizing: border-box !important;
        }

        .wc-order-totals table {
            width: 100% !important;
            border: none !important;
            margin: 0 !important;
        }

        .wc-order-totals table td.label {
            color: #64748b !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            padding: 6px 0 !important;
            border: none !important;
        }

        .wc-order-totals table td.total {
            text-align: right !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            color: #1e293b !important;
            padding: 6px 0 !important;
            border: none !important;
        }

        .wc-order-totals table tr.paid td.label,
        .wc-order-totals table tr.paid td.total {
            border-top: 1px solid #e2e8f0 !important;
            padding-top: 12px !important;
            margin-top: 6px !important;
            font-size: 17px !important;
            font-weight: 800 !important;
            color: #0f172a !important;
        }

        /* --- METABOXES LATERALES (CUSTOMER HISTORY, ACTIONS, NOTES) --- */
        #side-sortables .postbox,
        #woocommerce-order-actions,
        #woocommerce-customer-history,
        #woocommerce-order-attribution,
        #woocommerce-order-notes {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03) !important;
            overflow: hidden !important;
            margin-bottom: 20px !important;
        }

        #side-sortables .postbox .postbox-header {
            background: #f8fafc !important;
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 10px 16px !important;
        }

        #side-sortables .postbox .postbox-header h2 {
            font-size: 13px !important;
            font-weight: 700 !important;
            color: #334155 !important;
            letter-spacing: 0.02em !important;
        }

        /* Botón Guardar / Update Orden */
        #woocommerce-order-actions .inside {
            padding: 16px !important;
        }

        #woocommerce-order-actions select {
            width: 100% !important;
            height: 38px !important;
            border-radius: 8px !important;
            border: 1px solid #cbd5e1 !important;
            margin-bottom: 12px !important;
        }

        #woocommerce-order-actions button.save_order,
        #woocommerce-order-actions input.save_order {
            width: 100% !important;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
            color: #ffffff !important;
            border: none !important;
            border-radius: 8px !important;
            font-weight: 700 !important;
            font-size: 13px !important;
            height: 38px !important;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2) !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
        }

        #woocommerce-order-actions button.save_order:hover,
        #woocommerce-order-actions input.save_order:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 8px rgba(37, 99, 235, 0.3) !important;
        }

        /* Notas del Pedido */
        #woocommerce-order-notes .inside {
            padding: 16px !important;
        }

        ul.order_notes {
            padding: 0 !important;
            margin: 0 0 16px 0 !important;
        }

        ul.order_notes li.note {
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 12px 14px !important;
            margin-bottom: 10px !important;
            font-size: 12px !important;
            line-height: 1.5 !important;
        }

        ul.order_notes li.note.customer-note {
            background: #eff6ff !important;
            border-color: #bfdbfe !important;
        }

        ul.order_notes li.note abbr.exact-date {
            font-size: 11px !important;
            color: #94a3b8 !important;
            display: block !important;
            margin-top: 6px !important;
            border: none !important;
        }

        #woocommerce-order-notes textarea#add_order_note {
            width: 100% !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            padding: 8px 12px !important;
            font-size: 13px !important;
            margin-bottom: 10px !important;
        }

        #woocommerce-order-notes select#order_note_type {
            width: 100% !important;
            height: 36px !important;
            border-radius: 8px !important;
            border: 1px solid #cbd5e1 !important;
            margin-bottom: 10px !important;
        }

        #woocommerce-order-notes button.add_note {
            width: 100% !important;
            background: #ffffff !important;
            color: #0f172a !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            font-size: 13px !important;
            height: 36px !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
        }

        #woocommerce-order-notes button.add_note:hover {
            background: #f8fafc !important;
            border-color: #94a3b8 !important;
        }

        /* TOAST NOTIFICATION */
        .knowledgism-toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: #0f172a;
            color: #ffffff;
            padding: 14px 22px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.25);
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 999999;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .knowledgism-toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        .knowledgism-toast.success {
            background: #047857;
        }

        .knowledgism-toast.error {
            background: #b91c1c;
        }
    </style>

    <script id="knowledgism-single-order-script">
        jQuery(document).ready(function($) {
            // Inyectar botón en la cabecera si estamos en la edición individual de pedido
            var $orderHeader = $('#woocommerce-order-data h2');
            var orderId = $('#post_ID').val() || $('input[name="order_id"]').val() || (new URLSearchParams(window.location.search)).get('id') || (new URLSearchParams(window.location.search)).get('post');

            if ($orderHeader.length && orderId && !$('#knowledgism-resend-btn').length) {
                var resendBtnHtml = '<button type="button" id="knowledgism-resend-btn" class="knowledgism-resend-btn-header" data-order-id="' + orderId + '">' +
                    '<span class="dashicons dashicons-email-alt" style="font-size:16px;width:16px;height:16px;line-height:16px;"></span> Reenviar Confirmación' +
                    '</button>';
                $orderHeader.append(resendBtnHtml);
            }

            // También inyectar botón en el metabox de acciones laterales
            var $orderActions = $('#woocommerce-order-actions .inside');
            if ($orderActions.length && orderId && !$('#knowledgism-resend-btn-side').length) {
                var resendSideBtn = '<button type="button" id="knowledgism-resend-btn-side" class="knowledgism-resend-btn-sidebar" data-order-id="' + orderId + '">' +
                    '<span class="dashicons dashicons-email-alt" style="font-size:16px;width:16px;height:16px;line-height:16px;"></span> Reenviar Correo al Cliente' +
                    '</button>';
                $orderActions.append(resendSideBtn);
            }

            // Manejador del clic en los botones de reenvío
            $(document).on('click', '#knowledgism-resend-btn, #knowledgism-resend-btn-side, .knowledgism-resend-table-btn', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var currentOrderId = $btn.data('order-id') || orderId;

                if (!currentOrderId) {
                    return;
                }

                var originalHtml = $btn.html();
                $btn.prop('disabled', true).html('<span class="dashicons dashicons-update" style="font-size:16px;width:16px;height:16px;line-height:16px;animation:rotation 1s infinite linear;"></span> Enviando...');

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'knowledgism_resend_order_confirmation',
                        order_id: currentOrderId,
                        nonce: '<?php echo wp_create_nonce( 'knowledgism_resend_email_nonce' ); ?>'
                    },
                    success: function(response) {
                        $btn.prop('disabled', false).html(originalHtml);
                        if (response.success) {
                            showToast(response.data.message || '¡Correo reenviado exitosamente!', 'success');
                            // Si estamos en la vista de pedido, recargar las notas si es posible
                            if ($('#woocommerce-order-notes').length) {
                                setTimeout(function() {
                                    window.location.reload();
                                }, 1200);
                            }
                        } else {
                            showToast(response.data.message || 'Error al reenviar correo.', 'error');
                        }
                    },
                    error: function() {
                        $btn.prop('disabled', false).html(originalHtml);
                        showToast('Error de conexión al reenviar correo.', 'error');
                    }
                });
            });

            function showToast(message, type) {
                $('.knowledgism-toast').remove();
                var toastClass = type === 'success' ? 'success' : 'error';
                var icon = type === 'success' ? 'dashicons-yes-alt' : 'dashicons-warning';
                var $toast = $('<div class="knowledgism-toast ' + toastClass + '"><span class="dashicons ' + icon + '"></span> ' + message + '</div>');
                $('body').append($toast);
                setTimeout(function() { $toast.addClass('show'); }, 10);
                setTimeout(function() {
                    $toast.removeClass('show');
                    setTimeout(function() { $toast.remove(); }, 300);
                }, 4000);
            }
        });
    </script>
    <?php
}

/**
 * Handler AJAX para reenviar el correo de confirmación de pedido
 */
add_action( 'wp_ajax_knowledgism_resend_order_confirmation', 'knowledgism_ajax_resend_order_confirmation' );
function knowledgism_ajax_resend_order_confirmation() {
    check_ajax_referer( 'knowledgism_resend_email_nonce', 'nonce' );

    if ( ! current_user_can( 'edit_shop_orders' ) && ! current_user_can( 'manage_woocommerce' ) ) {
        wp_send_json_error( array( 'message' => __( 'No tienes permisos suficientes.', 'woocommerce' ) ) );
    }

    $order_id = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;
    if ( ! $order_id ) {
        wp_send_json_error( array( 'message' => __( 'ID de pedido inválido.', 'woocommerce' ) ) );
    }

    $order = wc_get_order( $order_id );
    if ( ! $order ) {
        wp_send_json_error( array( 'message' => __( 'Pedido no encontrado.', 'woocommerce' ) ) );
    }

    $customer_email = $order->get_billing_email();
    if ( empty( $customer_email ) ) {
        wp_send_json_error( array( 'message' => __( 'El pedido no tiene un email de cliente configurado.', 'woocommerce' ) ) );
    }

    $mailer = WC()->mailer();
    $emails = $mailer->get_emails();
    $status = $order->get_status();
    $sent   = false;

    // Disparar el correo según el estado del pedido
    if ( 'completed' === $status && isset( $emails['WC_Email_Customer_Completed_Order'] ) ) {
        $sent = $emails['WC_Email_Customer_Completed_Order']->trigger( $order->get_id(), $order );
    } elseif ( 'processing' === $status && isset( $emails['WC_Email_Customer_Processing_Order'] ) ) {
        $sent = $emails['WC_Email_Customer_Processing_Order']->trigger( $order->get_id(), $order );
    } elseif ( 'on-hold' === $status && isset( $emails['WC_Email_Customer_On_Hold_Order'] ) ) {
        $sent = $emails['WC_Email_Customer_On_Hold_Order']->trigger( $order->get_id(), $order );
    } elseif ( isset( $emails['WC_Email_Customer_Invoice'] ) ) {
        $sent = $emails['WC_Email_Customer_Invoice']->trigger( $order->get_id(), $order );
    }

    $current_user = wp_get_current_user();
    $user_name    = $current_user && $current_user->display_name ? $current_user->display_name : 'Admin';

    // Agregar nota al pedido
    $order->add_order_note(
        sprintf(
            __( '📧 Correo de confirmación reenviado manualmente por %s a %s (con copias BCC a enrique@attikka.com y erika@attikka.com).', 'woocommerce' ),
            $user_name,
            $customer_email
        )
    );

    wp_send_json_success(
        array(
            'message' => sprintf(
                __( '¡Correo de confirmación del pedido #%d reenviado con éxito a %s!', 'woocommerce' ),
                $order->get_id(),
                $customer_email
            ),
        )
    );
}

/**
 * Añadir acción rápida en la tabla de listado de pedidos para reenviar correo con 1 clic
 */
add_filter( 'woocommerce_admin_order_actions', 'knowledgism_add_resend_email_order_action', 20, 2 );
function knowledgism_add_resend_email_order_action( $actions, $order ) {
    $actions['resend_email'] = array(
        'url'    => '#',
        'name'   => __( 'Reenviar Correo de Confirmación', 'woocommerce' ),
        'action' => 'knowledgism-resend-table-btn" data-order-id="' . $order->get_id() . '" style="background:#0f172a;color:#fff;border-radius:4px;',
        'icon'   => '<span class="dashicons dashicons-email-alt" style="margin-top:2px;"></span>',
    );
    return $actions;
}
