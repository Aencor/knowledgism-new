<?php
/**
 * Class HomeCarousel
 *
 * Encapsulates the business logic, data retrieval, and fallbacks for the
 * Home Carousel / Zone Assessment CTA block in compliance with SOLID and DRY.
 *
 * @package CodeByMonk
 */

namespace CodeByMonk;

if (!defined('ABSPATH')) {
	exit;
}

require_once get_template_directory() . '/../knowledgism-new/classes/HomeCarousel.php';
