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
     * @param $eventManager
     * @param $eventFactory
     * @throws \Dhii\Exception\InternalException
     */
    public function __construct($key, $containerFactory, $eventManager, $eventFactory)
    {
        $this->_initModule(
            $containerFactory,
            $key,
            ['wp_bookings_front_ui'],
            $this->_loadPhpConfigFile(RC_WP_BOOKINGS_SHORTCODE_MODULE_CONFIG)
        );

        $this->_initModuleEvents($eventManager, $eventFactory);
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
        add_shortcode($this->_getConfig()['shortcode_tag'], function ($attrs) use ($c) {
            $attrs = $attrs ? $attrs : [];
            return $c->get('wp_bookings_front_ui')->render($attrs);
        });

        $this->eventManager->attach('wp_enqueue_scripts', function () use ($c) {
            if (!is_a(get_post(), 'WP_Post') || !has_shortcode(get_post()->post_content, $this->_getConfig()['shortcode_tag'])) {
                return;
            }

            $c->get('wp_bookings_front_ui')->enqueueAssets();
        });
    }
}