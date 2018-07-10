<?php

namespace RebelCode\Bookings\WordPress\Module\Handlers;

use Dhii\Collection\MapInterface;
use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Invocation\InvocableInterface;
use Dhii\Storage\Resource\SelectCapableInterface;
use Dhii\Transformer\TransformerInterface;
use Dhii\Util\Normalization\NormalizeArrayCapableTrait;
use Dhii\Util\Normalization\NormalizeIterableCapableTrait;
use Psr\EventManager\EventInterface;
use RebelCode\Expression\Builder\ExpressionBuilderInterface;

/**
 * Handler for transforming shortcode parameters in format required by the front ui application.
 *
 * @since [*next-version*]
 */
class ShortcodeParametersTransformHandler implements InvocableInterface
{
    /* @since [*next-version*] */
    use StringTranslatingTrait;

    /* @since [*next-version*] */
    use CreateInvalidArgumentExceptionCapableTrait;

    /* @since [*next-version*] */
    use NormalizeArrayCapableTrait;

    /* @since [*next-version*] */
    use NormalizeIterableCapableTrait;

    /**
     * Cart page ID.
     *
     * @since [*next-version*]
     *
     * @var int
     */
    protected $cartPageId;

    /**
     * Resource model for selecting services.
     *
     * @since [*next-version*]
     *
     * @var SelectCapableInterface
     */
    private $serviceSelectResourceModel;

    /**
     * Expression builder.
     *
     * @since [*next-version*]
     *
     * @var ExpressionBuilderInterface
     */
    private $expressionBuilder;

    /**
     * Service transformer.
     *
     * @since [*next-version*]
     *
     * @var TransformerInterface
     */
    private $serviceTransformer;

    /**
     * MainComponentHandler constructor.
     *
     * @since [*next-version*]
     *
     * @param int                        $cartPageId                 Cart page ID.
     * @param SelectCapableInterface     $serviceSelectResourceModel Resource model for selecting services.
     * @param ExpressionBuilderInterface $expressionBuilder          Expression builder.
     * @param TransformerInterface       $serviceTransformer         Service transformer.
     */
    public function __construct($cartPageId, $serviceSelectResourceModel, $expressionBuilder, $serviceTransformer)
    {
        $this->cartPageId                 = $cartPageId;
        $this->serviceSelectResourceModel = $serviceSelectResourceModel;
        $this->expressionBuilder          = $expressionBuilder;
        $this->serviceTransformer         = $serviceTransformer;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function __invoke()
    {
        /* @var $event EventInterface */
        $event = func_get_arg(0);

        if (!($event instanceof EventInterface)) {
            throw $this->_createInvalidArgumentException(
                $this->__('Argument is not an event instance'), null, null, $event
            );
        }

        $event->setParams($this->_handleParameters($event->getParams()));
    }

    /**
     * Render booking holder.
     *
     * @since [*next-version*]
     *
     * @param $params
     *
     * @return array
     */
    protected function _handleParameters($params = [])
    {
        $params['redirectUrl'] = $this->_getRedirectUrl();

        if (isset($params['service'])) {
            $service = $this->_getService((int) $params['service']);
            unset($params['service']);
            if ($service) {
                $params['service'] = $this->_normalizeArray($service);
            }
        }

        return $params;
    }

    /**
     * Get cart URL on which customer will be redirected after successfull booking creation.
     *
     * @since [*next-version*]
     *
     * @return string Cart URL to redirect user on.
     */
    protected function _getRedirectUrl()
    {
        return get_permalink($this->cartPageId);
    }

    /**
     * Get service by service ID.
     *
     * @since [*next-version*]
     *
     * @param int $serviceId Service ID.
     *
     * @return MapInterface|null Service data if service is found.
     */
    protected function _getService($serviceId)
    {
        $b = $this->expressionBuilder;

        $condition = $b->and(
            $b->eq(
                $b->ef('service', 'id'),
                $b->lit($serviceId)
            )
        );
        $services = $this->serviceSelectResourceModel->select($condition);

        return isset($services[0]) ? $this->_normalizeIterable($this->serviceTransformer->transform($services[0])) : null;
    }
}
