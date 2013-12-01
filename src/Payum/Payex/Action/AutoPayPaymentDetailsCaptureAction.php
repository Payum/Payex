<?php
namespace Payum\Payex\Action;

use Payum\Core\Action\PaymentAwareAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Request\CaptureRequest;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Payex\Request\Api\AutoPayAgreementRequest;

class AutoPayPaymentDetailsCaptureAction extends PaymentAwareAction
{
    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /** @var $request \Payum\Core\Request\CaptureRequest */
        if (false == $this->supports($request)) {
            throw RequestNotSupportedException::createActionNotSupported($this, $request);
        }
        
        $this->payment->execute(new AutoPayAgreementRequest($request->getModel()));
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        if (false == (
                $request instanceof CaptureRequest &&
                $request->getModel() instanceof \ArrayAccess
            )) {
            return false;
        }

        $model = ArrayObject::ensureArrayObject($request->getModel());

        //Make sure it is not recurring payment. There is an other capture action for recurring payments;
        if (true == $model['recurring']) {
            return false;
        }

        if ($model['autoPay']) {
            return true;
        }

        return false;
    }
}