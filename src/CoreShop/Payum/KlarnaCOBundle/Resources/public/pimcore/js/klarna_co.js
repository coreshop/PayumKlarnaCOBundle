/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.provider.gateways.klarna_co');
coreshop.provider.gateways.klarna_co = Class.create(coreshop.provider.gateways.abstract, {
    getLayout: function (config) {
        return [
            {
                xtype: 'textfield',
                fieldLabel: t('klarna_co_merchant_id'),
                name: 'gatewayConfig.config.merchant_id',
                length: 255,
                value: config.merchant_id ? config.merchant_id : ""
            },
            {
                xtype: 'textfield',
                fieldLabel: t('klarna_co_secret'),
                name: 'gatewayConfig.config.secret',
                length: 255,
                value: config.secret ? config.secret : ""
            },
            {
                xtype: 'textfield',
                fieldLabel: t('klarna_co_terms_uri'),
                name: 'gatewayConfig.config.terms_uri',
                length: 255,
                value: config.terms_uri ? config.terms_uri : ""
            },
            {
                xtype: 'checkbox',
                fieldLabel: t('klarna_co_sandbox'),
                name: 'gatewayConfig.config.sandbox',
                length: 255,
                value: config.sandbox ? config.sandbox : true
            },
            {
                xtype: 'hidden',
                name: 'gatewayConfig.config.use_authorize',
                length: 255,
                value: true
            }
        ];
    }
});
