<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Customer\Communication\Controller;

use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use SprykerFeature\Zed\Application\Communication\Controller\AbstractController;
use SprykerFeature\Zed\Customer\Business\CustomerFacade;
use SprykerFeature\Zed\Customer\Communication\CustomerDependencyContainer;
use SprykerFeature\Zed\Customer\Communication\Form\CustomerFormType;
use SprykerFeature\Zed\Customer\CustomerConfig;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method CustomerFacade getFacade()
 * @method CustomerDependencyContainer getDependencyContainer()
 */
class EditController extends AbstractController
{

    /**
     * @param Request $request
     *
     * @return array|RedirectResponse
     */
    public function indexAction(Request $request)
    {
        $idCustomer = $request->get(CustomerFormType::PARAM_ID_CUSTOMER);

        $form = $this->getDependencyContainer()
            ->createCustomerForm(CustomerFormType::UPDATE)
        ;

        $form->handleRequest($request);

        if ($form->isValid() === true) {
            $data = $form->getData();

            $customer = $this->createCustomerTransfer();
            $customer->fromArray($data, true);
            $this->getFacade()
                ->updateCustomer($customer)
            ;

            $defaultBilling = !empty($data[CustomerTransfer::DEFAULT_BILLING_ADDRESS]) ? $data[CustomerTransfer::DEFAULT_BILLING_ADDRESS] : false;
            if (empty($defaultBilling)) {
                $this->updateBillingAddress($idCustomer, $defaultBilling);
            }

            $defaultShipping = !empty($data[CustomerTransfer::DEFAULT_SHIPPING_ADDRESS]) ? $data[CustomerTransfer::DEFAULT_SHIPPING_ADDRESS] : false;
            if (empty($defaultShipping)) {
                $this->updateShippingAddress($idCustomer, $defaultShipping);
            }

            return $this->redirectResponse(sprintf('/customer/view/?%s=%d', CustomerConfig::PARAM_ID_CUSTOMER, $idCustomer));
        }

        return $this->viewResponse([
            'form' => $form->createView(),
            'idCustomer' => $idCustomer,
        ]);
    }

    /**
     * @return CustomerTransfer
     */
    protected function createCustomerTransfer()
    {
        return new CustomerTransfer();
    }

    /**
     * @return AddressTransfer
     */
    protected function createAddressTransfer()
    {
        return new AddressTransfer();
    }

    /**
     * @param int $idCustomer
     * @param int $defaultBillingAddress
     *
     * @return void
     */
    protected function updateBillingAddress($idCustomer, $defaultBillingAddress)
    {
        $addressTransfer = $this->createCustomAddressTransfer($idCustomer, $defaultBillingAddress);

        if ($this->isValidAddressTransfer($addressTransfer) === false) {
            return;
        }

        $this->getFacade()
            ->setDefaultBillingAddress($addressTransfer)
        ;
    }

    /**
     * @param AddressTransfer $addressTransfer
     *
     * @return bool
     */
    protected function isValidAddressTransfer(AddressTransfer $addressTransfer)
    {
        return (empty($addressTransfer->getIdCustomerAddress()) === false && $addressTransfer->getFkCustomer() !== null);
    }

    /**
     * @param int $idCustomer
     * @param int $defaultShippingAddress
     *
     * @return void
     */
    protected function updateShippingAddress($idCustomer, $defaultShippingAddress)
    {
        $addressTransfer = $this->createCustomAddressTransfer($idCustomer, $defaultShippingAddress);

        if ($this->isValidAddressTransfer($addressTransfer) === false) {
            return;
        }

        $this->getFacade()
            ->setDefaultShippingAddress($addressTransfer)
        ;
    }

    /**
     * @param int $idCustomer
     * @param int $billingAddress
     *
     * @return AddressTransfer
     */
    protected function createCustomAddressTransfer($idCustomer, $billingAddress)
    {
        $addressTransfer = $this->createAddressTransfer();

        $addressTransfer->setIdCustomerAddress($billingAddress);
        $addressTransfer->setFkCustomer($idCustomer);

        return $addressTransfer;
    }

}
