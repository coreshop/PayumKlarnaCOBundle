<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Payum\KlarnaCOBundle\Extension;

use CoreShop\Component\Core\Model\OrderItemInterface;
use CoreShop\Component\Core\Model\PaymentInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Taxation\Model\TaxItemInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Extension\Context;
use Payum\Core\Extension\ExtensionInterface;
use Payum\Core\Request\Authorize;
use Pimcore\Model\DataObject\Fieldcollection;
use Symfony\Component\Routing\RouterInterface;

final class AuthorizeExtension implements ExtensionInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param Context $context
     */
    public function onPostExecute(Context $context)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function onPreExecute(Context $context)
    {
        $request = $context->getRequest();

        if (!$request instanceof Authorize) {
            return;
        }

        if (count($context->getPrevious()) === 0) {
            return;
        }

        /** @var PaymentInterface $payment */
        $payment = $request->getFirstModel();

        if (false === $payment instanceof PaymentInterface) {
            return;
        }

        $order = $payment->getOrder();
        $model = ArrayObject::ensureArrayObject($request->getModel());

        $model['merchant_urls'] = [
            'checkout' => $this->router->generate('coreshop_checkout', ['stepIdentifier' => 'cart'], RouterInterface::ABSOLUTE_URL),
            'terms' => $model['terms_uri']
        ];

        /**
         * @var StoreInterface $store
         */
        $store = $order->getStore();
        $items = [];

        /**
         * @var OrderItemInterface $item
         */
        foreach ($order->getItems() as $item) {
            $itemTaxes = [];

            if ($item->getTaxes() instanceof Fieldcollection) {
                $itemTaxes = $item->getTaxes()->getItems();
            }

            $rates = array_map(function(TaxItemInterface $taxItem) { return $taxItem->getRate(); }, $itemTaxes);
            $rate = (int)round((array_sum($rates) / count($rates)) * 100, 0);

            $items[] = [
                'type' => 'physical',
                'reference' => $item->getId(),
                'name' => $item->getName(),
                'quantity' => $item->getQuantity(),
                'unit_price' => $item->getItemPrice(),
                'tax_rate' => $rate,
                'total_amount' => $item->getTotal(),
                'total_tax_amount' => $item->getTotalTax(),
                'total_discount_amount' => $item->getItemDiscount()
            ];
        }
//
//        foreach ($order->getPriceRules() as $priceRuleItem) {
//
//        }

        if ($order->getShipping() > 0) {
            $items[] = [
                'type' => 'shipping_fee',
                'reference' => 'shipping',
                'name' => $order->getCarrier() ? $order->getCarrier()->getTitle($order->getLocaleCode()) : '',
                'quantity' => 1,
                'unit_price' => $order->getShipping(),
                'tax_rate' => (int)round($order->getShippingTaxRate() * 100),
                'total_amount' => $order->getShipping(),
                'total_tax_amount' => $order->getShippingTax()
            ];
        }

        $model['auto_capture'] = true;
        $model['order_lines'] = $items;
        $model['order_amount'] = $order->getTotal();
        $model['order_tax_amount'] = $order->getTotalTax();
        $model['purchase_country'] = $store->getBaseCountry()->getIsoCode();
        $model['purchase_currency'] = $order->getCurrency()->getIsoCode();
        $model['locale'] = $order->getLocaleCode();
    }

    /**
     * {@inheritdoc}
     */
    public function onExecute(Context $context)
    {
    }
}
