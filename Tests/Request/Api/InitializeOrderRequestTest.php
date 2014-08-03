<?php
namespace Payum\Payex\Tests\Request\Api;

class InitializeOrderRequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeSubClassOfBaseModelAware()
    {
        $rc = new \ReflectionClass('Payum\Payex\Request\Api\InitializeOrderRequest');

        $this->assertTrue($rc->isSubclassOf('Payum\Core\Request\BaseModelAware'));
    }
}