<?php

namespace Payum\Payex\Tests\Action\Api;

use Payum\Payex\Action\Api\CompleteOrderAction;
use Payum\Payex\Request\Api\CompleteOrder;

class CompleteOrderActionTest extends \PHPUnit\Framework\TestCase
{
    protected $requiredFields = [
        'orderRef' => 'aRef',
    ];

    public function provideRequiredFields()
    {
        $fields = [];

        foreach ($this->requiredFields as $name => $value) {
            $fields[] = [$name];
        }

        return $fields;
    }

    public function testShouldImplementActionInterface()
    {
        $rc = new \ReflectionClass(\Payum\Payex\Action\Api\CompleteOrderAction::class);

        $this->assertTrue($rc->isSubclassOf(\Payum\Core\Action\ActionInterface::class));
    }

    public function testShouldImplementApiAwareInterface()
    {
        $rc = new \ReflectionClass(\Payum\Payex\Action\Api\CompleteOrderAction::class);

        $this->assertTrue($rc->isSubclassOf(\Payum\Core\ApiAwareInterface::class));
    }

    public function testThrowOnTryingSetNotOrderApiAsApi()
    {
        $this->expectException(\Payum\Core\Exception\UnsupportedApiException::class);
        $this->expectExceptionMessage('Not supported api given. It must be an instance of Payum\Payex\Api\OrderApi');
        $action = new CompleteOrderAction();

        $action->setApi(new \stdClass());
    }

    public function testShouldSupportCompleteOrderRequestWithArrayAccessAsModel()
    {
        $action = new CompleteOrderAction();

        $this->assertTrue($action->supports(new CompleteOrder($this->createMock(\ArrayAccess::class))));
    }

    public function testShouldNotSupportAnythingNotCompleteOrderRequest()
    {
        $action = new CompleteOrderAction();

        $this->assertFalse($action->supports(new \stdClass()));
    }

    public function testShouldNotSupportCompleteOrderRequestWithNotArrayAccessModel()
    {
        $action = new CompleteOrderAction();

        $this->assertFalse($action->supports(new CompleteOrder(new \stdClass())));
    }

    public function testThrowIfNotSupportedRequestGivenAsArgumentForExecute()
    {
        $this->expectException(\Payum\Core\Exception\RequestNotSupportedException::class);
        $action = new CompleteOrderAction($this->createApiMock());

        $action->execute(new \stdClass());
    }

    /**
     * @dataProvider provideRequiredFields
     */
    public function testThrowIfTryInitializeWithRequiredFieldNotPresent($requiredField)
    {
        $this->expectException(\Payum\Core\Exception\LogicException::class);
        unset($this->requiredFields[$requiredField]);

        $action = new CompleteOrderAction();

        $action->execute(new CompleteOrder($this->requiredFields));
    }

    public function testShouldCompletePayment()
    {
        $apiMock = $this->createApiMock();
        $apiMock
            ->expects($this->once())
            ->method('complete')
            ->with($this->requiredFields)
            ->willReturn([
                'transactionRef' => 'theRef',
            ]);

        $action = new CompleteOrderAction();
        $action->setApi($apiMock);

        $request = new CompleteOrder($this->requiredFields);

        $action->execute($request);

        $model = $request->getModel();
        $this->assertSame('theRef', $model['transactionRef']);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Payum\Payex\Api\OrderApi
     */
    protected function createApiMock()
    {
        return $this->createMock(\Payum\Payex\Api\OrderApi::class, [], [], '', false);
    }
}
