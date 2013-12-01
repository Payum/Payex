<?php
namespace Payum\Payex\Tests\Functional\Api;

use Payum\Payex\Api\OrderApi;
use Payum\Payex\Api\SoapClientFactory;

class OrderApiTest extends \PHPUnit_Framework_TestCase 
{
    /**
     * @var OrderApi
     */
    protected $orderApi;
    
    public static function setUpBeforeClass()
    {
        if (empty($GLOBALS['__PAYUM_PAYEX_ACCOUNT_NUMBER'])) {
            throw new \PHPUnit_Framework_SkippedTestError('Please configure __PAYUM_PAYEX_ACCOUNT_NUMBER in your phpunit.xml');
        }
        if (empty($GLOBALS['__PAYUM_PAYEX_ENCRYPTION_KEY'])) {
            throw new \PHPUnit_Framework_SkippedTestError('Please configure __PAYUM_PAYEX_ENCRYPTION_KEY in your phpunit.xml');
        }
    }
    
    public function setUp()
    {
        $this->orderApi = new OrderApi(
            new SoapClientFactory,
            array(
                'encryptionKey' => $GLOBALS['__PAYUM_PAYEX_ENCRYPTION_KEY'],
                'accountNumber' => $GLOBALS['__PAYUM_PAYEX_ACCOUNT_NUMBER'],
                'sandbox' => true
            )
        );
    }

    /**
     * @test
     *
     * @expectedException \SoapFault
     * @expectedExceptionMessage SOAP-ERROR: Encoding: object has no 'price' property
     */
    public function throwIfTryInitializeWithoutPrice()
    {
        $this->orderApi->initialize(array());
    }


    /**
     * @test
     *
     * @expectedException \SoapFault
     * @expectedExceptionMessage SOAP-ERROR: Encoding: object has no 'vat' property
     */
    public function throwIfTryInitializeWithoutVat()
    {
        $this->orderApi->initialize(array(
            'price' => 1000,
        ));
    }

    /**
     * @test
     */
    public function shouldFailedInitializeIfRequiredParametersMissing()
    {
        $result = $this->orderApi->initialize(array(
            'price' => 1000,
            'priceArgList' => '',
            'vat' => 0,
            'currency' => 'NOK',
        ));

        $this->assertInternalType('array', $result);
        $this->assertArrayNotHasKey('orderRef', $result);
        $this->assertArrayNotHasKey('sessionRef', $result);
        $this->assertArrayNotHasKey('redirectUrl', $result);
        
        $this->assertInternalType('array', $result);

        $this->assertArrayHasKey('errorCode', $result);
        $this->assertNotEmpty($result['errorCode']);
        $this->assertNotEquals(OrderApi::ERRORCODE_OK, $result['errorCode']);

        $this->assertArrayHasKey('errorDescription', $result);
        $this->assertNotEmpty($result['errorDescription']);
        $this->assertNotEquals(OrderApi::ERRORCODE_OK, $result['errorDescription']);

        $this->assertArrayHasKey('errorCode', $result);
        $this->assertNotEmpty($result['errorCode']);
        $this->assertNotEquals(OrderApi::ERRORCODE_OK, $result['errorCode']);
    }
    
    /**
     * @test
     */
    public function shouldSuccessfullyInitializeIfAllRequiredParametersSet()
    {
        $result = $this->orderApi->initialize(array(
            'price' => 1000,
            'priceArgList' => '',
            'vat' => 0,
            'currency' => 'NOK',
            'orderId' => 123,
            'productNumber' => 123,
            'purchaseOperation' => OrderApi::PURCHASEOPERATION_AUTHORIZATION,
            'view' => OrderApi::VIEW_CREDITCARD,
            'description' => 'a description',
            'additionalValues' => '',
            'returnUrl' => 'http://example.com/a_return_url',
            'cancelUrl' => 'http://example.com/a_cancel_url',
            'externalID' => '',
            'clientIPAddress' => '127.0.0.1',
            'clientIdentifier' => 'USER-AGENT=cli-php',
            'agreementRef' => '',
            'clientLanguage' => 'en-US',
        ));

        $this->assertInternalType('array', $result);

        $this->assertArrayHasKey('orderRef', $result);
        $this->assertNotEmpty($result['orderRef']);

        $this->assertArrayHasKey('redirectUrl', $result);
        $this->assertNotEmpty($result['redirectUrl']);

        $this->assertInternalType('array', $result);
        
        $this->assertArrayHasKey('errorCode', $result);
        $this->assertSame(OrderApi::ERRORCODE_OK, $result['errorCode']);

        $this->assertArrayHasKey('errorDescription', $result);
        $this->assertSame(OrderApi::ERRORCODE_OK, $result['errorDescription']);

        $this->assertArrayHasKey('errorCode', $result);
        $this->assertSame(OrderApi::ERRORCODE_OK, $result['errorCode']);   
    }

    /**
     * @test
     */
    public function shouldFailedCompleteIfRequiredParametersMissing()
    {
        $result = $this->orderApi->complete(array());

        $this->assertInternalType('array', $result);
        $this->assertArrayNotHasKey('transactionStatus', $result);
        $this->assertArrayNotHasKey('transactionNumber', $result);
        $this->assertArrayNotHasKey('orderStatus', $result);

        $this->assertInternalType('array', $result);

        $this->assertArrayHasKey('errorCode', $result);
        $this->assertNotEmpty($result['errorCode']);
        $this->assertNotEquals(OrderApi::ERRORCODE_OK, $result['errorCode']);

        $this->assertArrayHasKey('errorDescription', $result);
        $this->assertNotEmpty($result['errorDescription']);
        $this->assertNotEquals(OrderApi::ERRORCODE_OK, $result['errorDescription']);

        $this->assertArrayHasKey('errorCode', $result);
        $this->assertNotEmpty($result['errorCode']);
        $this->assertNotEquals(OrderApi::ERRORCODE_OK, $result['errorCode']);
    }

    /**
     * @test
     * 
     * @expectedException \SoapFault
     * @expectedExceptionMessage SOAP-ERROR: Encoding: object has no 'transactionNumber' property
     */
    public function throwIfTryCheckWithoutTransactionNumber()
    {
        $this->orderApi->check(array());
    }

    /**
     * @test
     */
    public function shouldFailedCheckIfTransactionNumberInvalid()
    {
        $result = $this->orderApi->check(array(
            'transactionNumber' => 'invalidTransNumber'
        ));

        $this->assertInternalType('array', $result);
        $this->assertArrayNotHasKey('transactionStatus', $result);
        $this->assertArrayNotHasKey('transactionNumber', $result);
        $this->assertArrayNotHasKey('orderStatus', $result);

        $this->assertInternalType('array', $result);

        $this->assertArrayHasKey('errorCode', $result);
        $this->assertNotEmpty($result['errorCode']);
        $this->assertNotEquals(OrderApi::ERRORCODE_OK, $result['errorCode']);

        $this->assertArrayHasKey('errorDescription', $result);
        $this->assertNotEmpty($result['errorDescription']);
        $this->assertNotEquals(OrderApi::ERRORCODE_OK, $result['errorDescription']);

        $this->assertArrayHasKey('errorCode', $result);
        $this->assertNotEmpty($result['errorCode']);
        $this->assertNotEquals(OrderApi::ERRORCODE_OK, $result['errorCode']);
    }
}