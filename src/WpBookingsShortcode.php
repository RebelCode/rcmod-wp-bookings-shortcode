<?php

namespace RebelCode\Bookings\WordPress\Module;

use Dhii\Data\Container\ContainerFactoryInterface;
use Dhii\Event\EventFactoryInterface;
use Psr\Container\ContainerInterface;
use Psr\EventManager\EventManagerInterface;
use RebelCode\Bookings\WordPress\Module\Handlers\ShortcodeParametersTransformHandler;
use RebelCode\Modular\Module\AbstractBaseModule;
use Dhii\Util\String\StringableInterface as Stringable;
use WP_Post;

/**
 * Handler for bookings shortcode that will insert client application
 * on page for booking appointments.
 *
 * @since [*next-version*]
 */
class WpBookingsShortcode extends AbstractBaseModule
{
    /**
     * The name of shortcode tag.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $shortcodeTag;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable         $key                  The module key.
     * @param string[]|Stringable[]     $dependencies         The module  dependencies.
     * @param ContainerFactoryInterface $configFactory        The config factory.
     * @param ContainerFactoryInterface $containerFactory     The container factory.
     * @param ContainerFactoryInterface $compContainerFactory The composite container factory.
     * @param EventManagerInterface     $eventManager         The event manager.
     * @param EventFactoryInterface     $eventFactory         The event factory.
     */
    public function __construct(
        $key,
        $dependencies,
        $configFactory,
        $containerFactory,
        $compContainerFactory,
        $eventManager,
        $eventFactory
    ) {
        $this->_initModule($key, $dependencies, $configFactory, $containerFactory, $compContainerFactory);
        $this->_initModuleEvents($eventManager, $eventFactory);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function setup()
    {
        return $this->_setupContainer($this->_loadPhpConfigFile(RC_WP_BOOKINGS_SHORTCODE_MODULE_CONFIG), [
            /*
             * Transform shortcode parameters before sending them to client.
             *
             * @since [*next-version*]
             */
            'eddbk_shortcode_parameters_transform_handler' => function (ContainerInterface $c) {
                return new ShortcodeParametersTransformHandler(
                    $c->get('eddbk_shortcode/edd_settings/purchase_page'),
                    $c->get('eddbk_services_select_rm'),
                    $c->get('sql_expression_builder'),
                    $c->get('eddbk_admin_edit_services_ui_state_transformer')
                );
            },
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function run(ContainerInterface $c = null)
    {
        $this->shortcodeTag = $c->get('eddbk_shortcode/shortcode_tag');

        $this->_attach('eddbk_shortcode_parameters_transform', $c->get('eddbk_shortcode_parameters_transform_handler'));

        add_shortcode($this->shortcodeTag, function ($attrs) {
            $attrs = $this->_trigger('eddbk_shortcode_parameters', $attrs ? $attrs : []);

            $attrs = $this->_trigger('eddbk_shortcode_parameters_transform', $attrs)->getParams();

            return $this->_trigger('eddbk_wizard_main_component', $attrs)->getParam('content');
        });

        $this->_attach('wp_enqueue_scripts', function () {
            if (!$this->_shouldRenderShortcodeContent()) {
                return;
            }
            $this->_trigger('eddbk_wizard_enqueue_assets');
            $this->_trigger('eddbk_wizard_enqueue_app_state');
        }, 999);

        $this->_attach('wp_footer', function () {
            if (!$this->_shouldRenderShortcodeContent()) {
                return;
            }
            echo $this->_trigger('eddbk_wizard_components_templates', [
                'content' => '',
            ])->getParam('content');
        });
    }

    /**
     * Check that shortcode's content should be rendered.
     *
     * @since [*next-version*]
     *
     * @return bool Is shortcode's content should be rendered.
     */
    protected function _shouldRenderShortcodeContent()
    {
        $post = get_post();

        return $post instanceof WP_Post && has_shortcode($post->post_content, $this->shortcodeTag);
    }
}
