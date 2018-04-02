<?php

namespace RebelCode\Bookings\WordPress\Module;

use Psr\Container\ContainerInterface;
use RebelCode\Modular\Module\AbstractBaseModule;

class WpBookingsShortcode extends AbstractBaseModule
{
    /**
     * WpBookingsFrontUi constructor.
     *
     * @since [*next-version*]
     *
     * @param $key
     * @param $containerFactory
     *
     * @throws \Dhii\Exception\InternalException
     */
    public function __construct($key, $containerFactory)
    {
        $this->_initModule(
            $containerFactory,
            $key,
            ['wp_bookings_front_ui'],
            $this->_loadPhpConfigFile(RC_WP_BOOKINGS_SHORTCODE_MODULE_CONFIG)
        );
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function setup()
    {
        return $this->_createContainer();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function run(ContainerInterface $c = null)
    {
        add_shortcode($this->_getConfig()['shortcode_tag'], function () use ($c) {
            return $c->get('wp_bookings_front_ui')->render();
        });

        $this->eventManager->attach('wp_enqueue_scripts', function () use ($c) {
            return $c->get('wp_bookings_front_ui')->enqueueAssets();
        });
    }
}