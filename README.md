# CoreShop Klarna CO Payum Connector
(CO is short for Checkout and should indicate that is uses the kco_rest package)

This Bundle activates the Klarna Checkout PaymentGateway in CoreShop.
It requires the [pringuin/payum-klarna-co](https://github.com/pringuin/payum-klarna-co) repository which will be installed automatically.

## Installation

#### 1. Composer
```json
    "coreshop/payum-klarna-co-bundle": "^1.0"
```

#### 2. Activate
Enable the Bundle in Pimcore Extension Manager

#### 3. Setup
Go to Coreshop -> PaymentProvider and add a new Provider. Choose `klarna-co` from `type` and fill out the required fields.

