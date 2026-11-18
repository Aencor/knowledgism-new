<?php
/**
 * Plugin Name: Knowledgism - WooCommerce Premium Email Styling
 * Description: Estilos visuales modernos, limpios y premium para todos los correos transaccionales de WooCommerce.
 * Version: 1.0.0
 * Author: Attikka / Knowledgism
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Inyectar estilos CSS avanzados y modernos en los emails de WooCommerce
 */
add_filter('woocommerce_email_styles', 'knowledgism_custom_woocommerce_email_styles', 999, 2);
function knowledgism_custom_woocommerce_email_styles($css, $email = null) {
    $custom_css = "
        /* --- ESTILOS GENERALES Y TIPOGRAFÍA --- */
        body, table, td, p, a, li, blockquote {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        body {
            background-color: #f1f5f9 !important;
            margin: 0 !important;
            padding: 24px 0 !important;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif !important;
            -webkit-font-smoothing: antialiased !important;
            color: #334155 !important;
        }

        #wrapper {
            background-color: #f1f5f9 !important;
            margin: 0 auto !important;
            padding: 20px 0 !important;
            width: 100% !important;
        }

        #template_container {
            background-color: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 14px !important;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.06), 0 8px 10px -6px rgba(15, 23, 42, 0.04) !important;
            overflow: hidden !important;
            max-width: 620px !important;
            margin: 0 auto !important;
        }

        /* --- CABECERA --- */
        #template_header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
            border-bottom: 2px solid #334155 !important;
            border-radius: 14px 14px 0 0 !important;
            color: #ffffff !important;
            padding: 32px 30px 28px !important;
            text-align: center !important;
        }

        #template_header_image {
            text-align: center !important;
            padding-bottom: 12px !important;
        }

        #template_header_image img {
            max-width: 200px !important;
            height: auto !important;
            display: inline-block !important;
        }

        #template_header h1 {
            color: #ffffff !important;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif !important;
            font-size: 22px !important;
            font-weight: 700 !important;
            line-height: 1.35 !important;
            margin: 0 !important;
            text-shadow: none !important;
            letter-spacing: -0.02em !important;
        }

        /* --- CUERPO --- */
        #template_body {
            padding: 32px 30px !important;
            background-color: #ffffff !important;
        }

        #body_content {
            background-color: #ffffff !important;
        }

        #body_content table td {
            padding: 0 !important;
        }

        #body_content_inner {
            color: #334155 !important;
            font-size: 14px !important;
            line-height: 1.6 !important;
            text-align: left !important;
        }

        #body_content_inner p {
            margin: 0 0 16px !important;
            color: #475569 !important;
            font-size: 15px !important;
            line-height: 1.6 !important;
        }

        h2 {
            color: #0f172a !important;
            display: block !important;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif !important;
            font-size: 18px !important;
            font-weight: 700 !important;
            line-height: 1.4 !important;
            margin: 28px 0 16px !important;
            padding-bottom: 8px !important;
            border-bottom: 1px solid #f1f5f9 !important;
            letter-spacing: -0.01em !important;
        }

        h3 {
            color: #1e293b !important;
            font-size: 15px !important;
            font-weight: 600 !important;
            margin: 20px 0 10px !important;
        }

        /* --- TABLA DE DETALLES DEL PEDIDO --- */
        .order_details,
        table.td {
            width: 100% !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            overflow: hidden !important;
            margin-bottom: 24px !important;
        }

        .order_details th,
        table.td th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-size: 12px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            padding: 12px 16px !important;
            border-bottom: 1px solid #e2e8f0 !important;
            border-top: none !important;
            border-left: none !important;
            border-right: none !important;
            text-align: left !important;
        }

        .order_details td,
        table.td td {
            padding: 14px 16px !important;
            border-bottom: 1px solid #f1f5f9 !important;
            border-top: none !important;
            border-left: none !important;
            border-right: none !important;
            color: #334155 !important;
            font-size: 14px !important;
            vertical-align: middle !important;
        }

        .order_details tr:last-child td {
            border-bottom: none !important;
        }

        .order_item .product-name {
            font-weight: 600 !important;
            color: #0f172a !important;
        }

        .order_item .product-quantity {
            display: inline-block !important;
            background-color: #f1f5f9 !important;
            color: #475569 !important;
            padding: 2px 8px !important;
            border-radius: 12px !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            margin-left: 6px !important;
        }

        /* Fila de Totales */
        .order_details tfoot tr:first-child th,
        .order_details tfoot tr:first-child td {
            border-top: 2px solid #e2e8f0 !important;
        }

        .order_details tfoot th {
            background-color: #ffffff !important;
            text-align: right !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            color: #64748b !important;
            text-transform: none !important;
            padding: 10px 16px !important;
        }

        .order_details tfoot td {
            background-color: #ffffff !important;
            text-align: right !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            color: #1e293b !important;
            padding: 10px 16px !important;
        }

        .order_details tfoot tr:last-child th,
        .order_details tfoot tr:last-child td {
            background-color: #f8fafc !important;
            color: #0f172a !important;
            font-size: 16px !important;
            font-weight: 800 !important;
            padding: 14px 16px !important;
            border-top: 1px solid #e2e8f0 !important;
        }

        /* --- TARJETAS DE DIRECCIÓN Y CLIENTE --- */
        #addresses {
            margin-top: 24px !important;
            margin-bottom: 24px !important;
        }

        #addresses td {
            padding: 0 10px 0 0 !important;
        }

        #addresses address {
            background-color: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 16px 18px !important;
            color: #475569 !important;
            font-size: 13px !important;
            line-height: 1.6 !important;
            font-style: normal !important;
        }

        /* --- BOTONES DE ACCIÓN --- */
        .btn-knowledgism,
        a.button,
        .button {
            background-color: #2563eb !important;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
            border: none !important;
            border-radius: 8px !important;
            color: #ffffff !important;
            display: inline-block !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            padding: 12px 24px !important;
            text-align: center !important;
            text-decoration: none !important;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2) !important;
            margin: 12px 0 !important;
        }

        /* --- PIE DE PÁGINA --- */
        #template_footer {
            background-color: #f8fafc !important;
            border-top: 1px solid #e2e8f0 !important;
            border-radius: 0 0 14px 14px !important;
            padding: 24px 30px !important;
            text-align: center !important;
        }

        #template_footer #credit {
            color: #64748b !important;
            font-size: 12px !important;
            line-height: 1.6 !important;
            margin: 0 !important;
        }

        #template_footer a {
            color: #2563eb !important;
            text-decoration: none !important;
            font-weight: 500 !important;
        }

        #template_footer a:hover {
            text-decoration: underline !important;
        }

        /* Responsive */
        @media screen and (max-width: 600px) {
            #template_container {
                width: 100% !important;
                border-radius: 0 !important;
                border: none !important;
            }
            #template_header,
            #template_body,
            #template_footer {
                padding: 20px 16px !important;
            }
        }
    ";

    return $css . "\n" . $custom_css;
}

/**
 * Añadir texto y pie de página profesional para Knowledgism
 */
add_filter('woocommerce_email_footer_text', 'knowledgism_custom_email_footer_text', 999);
function knowledgism_custom_email_footer_text($text) {
    return '<strong>Knowledgism</strong> &bull; ' . sprintf(
        __('¿Necesitas ayuda con tu pedido? Escríbenos a %s', 'woocommerce'),
        '<a href="mailto:store@knowledgism.com">store@knowledgism.com</a>'
    ) . '<br><span style="font-size: 11px; color: #94a3b8;">&copy; ' . date('Y') . ' Knowledgism. Todos los derechos reservados.</span>';
}
