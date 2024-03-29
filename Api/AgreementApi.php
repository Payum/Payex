<?php

namespace Payum\Payex\Api;

class AgreementApi extends BaseApi
{
    public const AGREEMENTSTATUS_NOTVERIFIED = 0;

    public const AGREEMENTSTATUS_VERIFIED = 1;

    public const AGREEMENTSTATUS_DELETED = 2;

    /**
     * @return array
     */
    public function create(array $parameters)
    {
        $parameters['accountNumber'] = $this->options['account_number'];

        //Deprecated, set to blank.
        $parameters['notifyUrl'] = '';

        $parameters['hash'] = $this->calculateHash($parameters, [
            'accountNumber',
            'merchantRef',
            'description',
            'purchaseOperation',
            'maxAmount',
            'notifyUrl',
            'startDate',
            'stopDate',
        ]);

        return $this->call('CreateAgreement3', $parameters, $this->getPxAgreementWsdl());
    }

    /**
     * @return array
     */
    public function check(array $parameters)
    {
        $parameters['accountNumber'] = $this->options['account_number'];

        $parameters['hash'] = $this->calculateHash($parameters, [
            'accountNumber',
            'agreementRef',
        ]);

        return $this->call('Check', $parameters, $this->getPxAgreementWsdl());
    }

    /**
     * @return array
     */
    public function delete(array $parameters)
    {
        $parameters['accountNumber'] = $this->options['account_number'];

        $parameters['hash'] = $this->calculateHash($parameters, [
            'accountNumber',
            'agreementRef',
        ]);

        return $this->call('DeleteAgreement', $parameters, $this->getPxAgreementWsdl());
    }

    /**
     * @return array
     */
    public function autoPay(array $parameters)
    {
        $parameters['accountNumber'] = $this->options['account_number'];

        $parameters['hash'] = $this->calculateHash($parameters, [
            'accountNumber',
            'agreementRef',
            'price',
            'productNumber',
            'description',
            'orderId',
            'purchaseOperation',
            'currency',
        ]);

        return $this->call('AutoPay3', $parameters, $this->getPxAgreementWsdl());
    }

    protected function getPxAgreementWsdl()
    {
        return $this->options['sandbox'] ?
            'https://test-external.payex.com/pxagreement/pxagreement.asmx?wsdl' :
            'https://external.payex.com/pxagreement/pxagreement.asmx?wsdl'
        ;
    }
}
