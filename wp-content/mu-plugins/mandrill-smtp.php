<?php
/**
 * Plugin Name: Mandrill SMTP & Order Notification Mailer
 * Description: Envío mediante Mandrill SMTP y copias automáticas de pedidos WooCommerce a enrique@attikka.com y erika@attikka.com
 * Author: Attikka / Knowledgism
 * Version: 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('MANDRILL_API_KEY')) {
    define('MANDRILL_API_KEY', getenv('MANDRILL_API_KEY') ?: 'md-XtcRL4yJFNV6y_TL6zLVMg');
}

if (!defined('MANDRILL_HOST')) {
    define('MANDRILL_HOST', getenv('MANDRILL_HOST') ?: 'smtp.mandrillapp.com');
}

if (!defined('MANDRILL_PORT')) {
    define('MANDRILL_PORT', getenv('MANDRILL_PORT') ?: 587);
}

if (!defined('MANDRILL_SECURE')) {
    define('MANDRILL_SECURE', getenv('MANDRILL_SECURE') ?: 'tls');
}

if (!defined('MANDRILL_USERNAME')) {
    define('MANDRILL_USERNAME', getenv('MANDRILL_USERNAME') ?: '31645118');
}

// Dominio verificado y firmado en Mandrill (para evitar rechazo unsigned de Mandrill)
if (!defined('MANDRILL_FROM_EMAIL')) {
    define('MANDRILL_FROM_EMAIL', getenv('MANDRILL_FROM_EMAIL') ?: 'noreply@zonesoflife.com');
}

if (!defined('MANDRILL_FROM_NAME')) {
    define('MANDRILL_FROM_NAME', getenv('MANDRILL_FROM_NAME') ?: 'Knowledgism');
}

if (!defined('MANDRILL_REPLY_TO')) {
    define('MANDRILL_REPLY_TO', 'store@knowledgism.com');
}

// Configuración de PHPMailer con Mandrill
if (!function_exists('knowledgism_configure_mandrill_smtp')) {
    add_action('phpmailer_init', 'knowledgism_configure_mandrill_smtp', 20);
    function knowledgism_configure_mandrill_smtp($phpmailer)
    {
        $api_key = defined('MANDRILL_API_KEY') ? MANDRILL_API_KEY : 'md-XtcRL4yJFNV6y_TL6zLVMg';

        if (empty($api_key)) {
            return;
        }

        $phpmailer->isSMTP();
        $phpmailer->Host = MANDRILL_HOST;
        $phpmailer->SMTPAuth = true;
        $phpmailer->Port = (int) MANDRILL_PORT;
        $phpmailer->SMTPSecure = MANDRILL_SECURE;
        $phpmailer->Username = MANDRILL_USERNAME;
        $phpmailer->Password = $api_key;
        $phpmailer->Timeout = 20;

        $phpmailer->From = MANDRILL_FROM_EMAIL;
        $phpmailer->Sender = MANDRILL_FROM_EMAIL;
        $phpmailer->FromName = MANDRILL_FROM_NAME;

        // Añadir Reply-To si no tiene uno
        $reply_tos = $phpmailer->getReplyToAddresses();
        if (empty($reply_tos)) {
            $phpmailer->addReplyTo(MANDRILL_REPLY_TO, MANDRILL_FROM_NAME);
        }
    }
}

// Filtros para asegurar que el From sea el dominio verificado en Mandrill
add_filter('woocommerce_email_from_address', function($from_email) {
    return MANDRILL_FROM_EMAIL;
}, PHP_INT_MAX);

add_filter('woocommerce_email_from_name', function($from_name) {
    return MANDRILL_FROM_NAME;
}, PHP_INT_MAX);

add_filter('wp_mail_from', function($from_email) {
    return MANDRILL_FROM_EMAIL;
}, PHP_INT_MAX);

add_filter('wp_mail_from_name', function($from_name) {
    return MANDRILL_FROM_NAME;
}, PHP_INT_MAX);

// Interceptar wp_mail args para sanitizar headers From y asegurar Reply-To
add_filter('wp_mail', function($args) {
    $headers = $args['headers'];
    if (is_array($headers)) {
        $new_headers = array();
        $has_reply_to = false;
        foreach ($headers as $header) {
            if (stripos($header, 'From:') === 0) {
                // Reemplazar From con el verificado
                $new_headers[] = 'From: ' . MANDRILL_FROM_NAME . ' <' . MANDRILL_FROM_EMAIL . '>';
            } elseif (stripos($header, 'Reply-To:') === 0) {
                $has_reply_to = true;
                $new_headers[] = $header;
            } else {
                $new_headers[] = $header;
            }
        }
        if (!$has_reply_to) {
            $new_headers[] = 'Reply-To: ' . MANDRILL_FROM_NAME . ' <' . MANDRILL_REPLY_TO . '>';
        }
        $args['headers'] = $new_headers;
    } elseif (is_string($headers)) {
        $headers = preg_replace('/From:[^\r\n]*/i', 'From: ' . MANDRILL_FROM_NAME . ' <' . MANDRILL_FROM_EMAIL . '>', $headers);
        if (stripos($headers, 'Reply-To:') === false) {
            $headers .= "\r\nReply-To: " . MANDRILL_FROM_NAME . ' <' . MANDRILL_REPLY_TO . '>';
        }
        $args['headers'] = $headers;
    }
    return $args;
}, PHP_INT_MAX);

// Envío de copias (BCC) a enrique@attikka.com y erika@attikka.com en todos los correos de pedidos de WooCommerce
if (!function_exists('knowledgism_add_order_email_bccs')) {
    add_filter('woocommerce_email_headers', 'knowledgism_add_order_email_bccs', PHP_INT_MAX, 4);
    function knowledgism_add_order_email_bccs($headers, $email_id, $order, $email = null)
    {
        $order_email_ids = array(
            'customer_completed_order',
            'customer_processing_order',
            'customer_on_hold_order',
            'customer_invoice',
            'new_order'
        );

        if (in_array($email_id, $order_email_ids, true)) {
            $bccs = array(
                'enrique@attikka.com',
                'erika@attikka.com'
            );
            foreach ($bccs as $bcc) {
                if (stripos($headers, $bcc) === false) {
                    $headers .= "Bcc: " . $bcc . "\r\n";
                }
            }
        }

        return $headers;
    }
}

// Registro de errores de envío si ocurren
if (!function_exists('knowledgism_mandrill_mail_failed_logger')) {
    add_action('wp_mail_failed', 'knowledgism_mandrill_mail_failed_logger');
    function knowledgism_mandrill_mail_failed_logger($wp_error)
    {
        if (is_wp_error($wp_error)) {
            error_log('[Mandrill Mail Error] ' . $wp_error->get_error_message() . ' | Data: ' . print_r($wp_error->get_error_data(), true));
        }
    }
}
