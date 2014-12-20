<?php
namespace Payum\Payex\Tests\Action;

use Payum\Core\PaymentInterface;
use Payum\Core\Request\Capture;
use Payum\Core\Request\GetHttpRequest;
use Payum\Payex\Action\PaymentDetailsCaptureAction;

class PaymentDetailsCaptureActionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeSubClassOfPaymentAwareAction()
    {
        $rc = new \ReflectionClass('Payum\Payex\Action\PaymentDetailsCaptureAction');

        $this->assertTrue($rc->isSubclassOf('Payum\Core\Action\PaymentAwareAction'));
    }

    /**
     * @test
     */
    public function couldBeConstructedWithoutAnyArguments()
    {
        new PaymentDetailsCaptureAction();
    }

    /**
     * @test
     */
    public function shouldSupportCaptureWithArrayAsModelIfAutoPayNotSet()
    {
        $action = new PaymentDetailsCaptureAction();

        $this->assertTrue($action->supports(new Capture(array())));
    }

    /**
     * @test
     */
    public function shouldNotSupportCaptureayAsModelIfAutoPaySet()
    {
        $action = new PaymentDetailsCaptureAction();

        $this->assertFalse($action->supports(new Capture(array(
            'autoPay' => true,
        ))));
    }

    /**
     * @test
     */
    public function shouldSupportCaptureCaptureelIfAutoPaySetToFalse()
    {
        $action = new PaymentDetailsCaptureAction();

        $this->assertTrue($action->supports(new Capture(array(
            'autoPay' => false,
        ))));
    }

    /**
     * @test
     */
    public function shouldSupportCaptureWithArrCaptureurringSetToTrueAndAutoPaySet()
    {
        $action = new PaymentDetailsCaptureAction();

        $this->assertTrue($action->supports(new Capture(array(
            'autoPay' => true,
            'recurring' => true,
        ))));
    }

    /**
     * @test
     */
    public function shouldNotSupportAnythingNotCapture()
    {
        $action = new PaymentDetailsCaptureAction();

        $this->assertFalse($action->supports(new \stdClass()));
    }

    /**
     * @test
     */
    public function shouldNotSupportCaptureWithNotArrayAccessModel()
    {
        $action = new PaymentDetailsCaptureAction();

        $this->assertFalse($action->supports(new Capture(new \stdClass())));
    }

    /**
     * @test
     *
     * @expectedException \Payum\Core\Exception\RequestNotSupportedException
     */
    public function throwIfNotSupportedRequestGivenAsArgumentForExecute()
    {
        $action = new PaymentDetailsCaptureAction();

        $action->execute(new \stdClass());
    }

    /**
     * @test
     */
    public function shouldDoSubExecuteInitializeOrderApiRequestIfOrderRefNotSet()
    {
        $paymentMock = $this->createPaymentMock();
        $paymentMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->isInstanceOf('Payum\Payex\Request\Api\InitializeOrder'))
        ;

        $action = new PaymentDetailsCaptureAction();
        $action->setPayment($paymentMock);

        $request = new Capture(array(
            'clientIPAddress' => 'anIp',
        ));

        $action->execute($request);
    }

    /**
     * @test
     */
    public function shouldDoSubExecuteCompleteOrderApiRequestIfOrderRefSet()
    {
        $paymentMock = $this->createPaymentMock();
        $paymentMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->isInstanceOf('Payum\Payex\Request\Api\CompleteOrder'))
        ;

        $action = new PaymentDetailsCaptureAction();
        $action->setPayment($paymentMock);

        $request = new Capture(array(
            'orderRef' => 'aRef',
            'clientIPAddress' => 'anIp',
        ));

        $action->execute($request);
    }

    /**
     * @test
     */
    public function shouldDoSubExecuteStartRecurringPaymentApiRequestIfRecurringSet()
    {
        $paymentMock = $this->createPaymentMock();
        $paymentMock
            ->expects($this->at(1))
            ->method('execute')
            ->with($this->isInstanceOf('Payum\Payex\Request\Api\StartRecurringPayment'))
        ;

        $action = new PaymentDetailsCaptureAction();
        $action->setPayment($paymentMock);

        $request = new Capture(array(
            'orderRef' => 'aRef',
            'recurring' => true,
            'clientIPAddress' => 'anIp',
        ));

        $action->execute($request);
    }

    /**
     * @test
     */
    public function shouldDoSubGetHttpRequestAndSetClientIpFromIt()
    {
        $paymentMock = $this->createPaymentMock();
        $paymentMock
            ->expects($this->at(0))
            ->method('execute')
            ->with($this->isInstanceOf('Payum\Core\Request\GetHttpRequest'))
            ->will($this->returnCallback(function (GetHttpRequest $request) {
                $request->clientIp = 'expectedClientIp';
            }))
        ;

        $action = new PaymentDetailsCaptureAction();
        $action->setPayment($paymentMock);

        $request = new Capture(array());

        $action->execute($request);

        $details = iterator_to_array($request->getModel());

        $this->assertArrayHasKey('clientIPAddress', $details);
        $this->assertEquals('expectedClientIp', $details['clientIPAddress']);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PaymentInterface
     */
    protected function createPaymentMock()
    {
        return $this->getMock('Payum\Core\PaymentInterface');
    }
}
