# Get it started.

In this chapter we are going to talk about the most common task: purchasing a product using [Payex](http://www.payexpim.com/).
We assume you already read [payum's get it started documentation](https://github.com/Payum/Payum/blob/master/docs/get-it-started.md).
Here we just extend it and describe [Payex](http://www.payexpim.com/) specific details.

## Installation

The preferred way to install the library is using [composer](http://getcomposer.org/).
Run composer require to add dependencies to _composer.json_:

```bash
php composer.phar require "payum/payex:*@stable"
```

Now you have all codes prepared and ready to be used.

## Configuration

First we have modify `config.php` a bit.
We need to add payment factory and payment details storage.

```php
<?php

use Buzz\Client\Curl;

use Payum\Payex\Api\OrderApi;
use Payum\Payex\Api\SoapClientFactory;
use Payum\Payex\PaymentFactory;

//config.php

// ...

$payexOrderApi = new OrderApi(new SoapClientFactory(), array(
   'accountNumber' => 'REPLACE IT',
   'encryptionKey' => 'REPLACE IT',
   'sandbox' => true
));

$storages = array(
    'payex' => array(
        $detailsClass => new FilesystemStorage('/path/to/storage', $detailsClass)
    )
);

$payments = array(
    'payex' => PaymentFactory::create($payexOrderApi)
);
```

## Prepare payment

```php
<?php
// prepare.php

use Payum\Payex\Api\OrderApi;

include 'config.php';

$storage = $registry->getStorageForClass($detailsClass, 'payex');

$paymentDetails = $storage->createModel();
$paymentDetails['price'] = 10000 //10 EUR
$paymentDetails['currency'] = 'EUR',
$paymentDetails['vat'] = 0,
$paymentDetails['orderId'] = 123,
$paymentDetails['productNumber'] = 123,
$paymentDetails['purchaseOperation'] = OrderApi::PURCHASEOPERATION_AUTHORIZATION,
$paymentDetails['view'] = OrderApi::VIEW_CREDITCARD,
$paymentDetails['description'] = 'a desc',
$paymentDetails['clientIPAddress'] = '127.0.0.1',
$paymentDetails['clientLanguage'] = 'en-US'
$paymentDetails['clientIdentifier'] = '',
$paymentDetails['additionalValues'] = '',
$paymentDetails['priceArgList'] = '',
$paymentDetails['agreementRef'] = '',
$storage->updateModel($paymentDetails);

$captureToken = $tokenFactory->createCaptureToken('payex', $paymentDetails, 'done.php');

header("Location: ".$captureToken->getTargetUrl());
```

That's it. As you see we configured Payex payment `config.php` and set details `prepare.php`.
`capture.php` and `done.php` scripts remain same.

Back to [index](index.md).