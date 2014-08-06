<?php

namespace Pim\Bundle\WebServiceBundle\Controller\Rest;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Pim\Bundle\CatalogBundle\Model\ProductInterface;

/**
 * Product API controller
 *
 * @author    Filips Alpe <filips@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @RouteResource("product")
 * @NamePrefix("oro_api_")
 */
class ProductController extends FOSRestController
{
    /**
     * Get a single product
     *
     * @param Request $request
     * @param string  $identifier
     *
     * @ApiDoc(
     *      description="Get a single product",
     *      resource=true
     * )
     * @return Response
     */
    public function getAction(Request $request, $identifier)
    {
        $channels = $this->prepareChannels($request);
        $locales = $this->prepareLocales($request);

        return $this->handleGetRequest($identifier, $channels, $locales);
    }

    /**
     * @return string[]
     */
    protected function prepareChannels(Request $request)
    {
        $channels = $request->get('channels', $request->get('channel', null));
        $userContext       = $this->get('pim_user.context.user');
        $availableChannels = array_keys($userContext->getChannelChoicesWithUserChannel());

        if ($channels !== null) {
            $channels = explode(',', $channels);

            foreach ($channels as $channel) {
                if (!in_array($channel, $availableChannels)) {
                    return new Response(sprintf('Channel "%s" does not exist or is not available', $channel), 403);
                }
            }
        } else {
            $channels = $availableChannels;
        }

        return $channels;
    }

    /**
     * @return string[]
     */
    protected function prepareLocales(Request $request)
    {
        $locales = $request->get('locales', $request->get('locale', null));
        $userContext = $this->get('pim_user.context.user');
        $availableLocales  = $userContext->getUserLocaleCodes();

        if ($locales !== null) {
            $locales = explode(',', $locales);

            foreach ($locales as $locale) {
                if (!in_array($locale, $availableLocales)) {
                    return new Response(sprintf('Locale "%s" does not exist or is not available', $locale), 403);
                }
            }
        } else {
            $locales = $availableLocales;
        }

        return $locales;
    }

    /**
     * Return a single product
     *
     * @param string   $identifier
     * @param string[] $channels
     * @param string[] $locales
     *
     * @return Response
     */
    protected function handleGetRequest($identifier, $channels, $locales)
    {
        $manager = $this->get('pim_catalog.manager.product');
        $product = $manager->findByIdentifier($identifier);

        if (!$product) {
            return new Response(sprintf('Product "%s" not found', $identifier), 404);
        }

        $serializedData = $this->serializeProduct($product, $channels, $locales);

        return new Response($serializedData);
    }

    /**
     * Serialize a single product
     *
     * @param ProductInterface $product
     * @param string[]         $channels
     * @param string[]         $locales
     *
     * @return array
     */
    protected function serializeProduct(ProductInterface $product, $channels, $locales)
    {
        $url = $this->generateUrl(
            'oro_api_get_product',
            array(
                'identifier' => $product->getIdentifier()->getData()
            ),
            true
        );
        $handler = $this->get('pim_webservice.handler.rest.product');
        $data = $handler->get($product, $channels, $locales, $url);

        return $data;
    }
}
