<?php
use Psr\Container\ContainerInterface;
use \RebelCode\Bookings\WordPress\Module\WpBookingsShortcode;

define('RC_WP_BOOKINGS_SHORTCODE_MODULE_DIR', __DIR__);
define('RC_WP_BOOKINGS_SHORTCODE_MODULE_CONFIG', RC_WP_BOOKINGS_SHORTCODE_MODULE_DIR . '/config.php');
define('RC_WP_BOOKINGS_SHORTCODE_MODULE_KEY', 'wp_bookings_shortcode');

return function(ContainerInterface $c) {
    return new WpBookingsShortcode(
        RC_WP_BOOKINGS_SHORTCODE_MODULE_KEY,
        $c->get('container_factory')
    );
};