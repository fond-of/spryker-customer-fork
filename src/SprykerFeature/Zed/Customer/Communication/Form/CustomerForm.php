<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Customer\Communication\Form;

use Generated\Shared\Transfer\CustomerTransfer;
use SprykerFeature\Zed\Customer\Persistence\Propel\Base\SpyCustomerAddressQuery;
use SprykerFeature\Zed\Customer\Persistence\Propel\SpyCustomerQuery;
use SprykerFeature\Zed\Customer\Persistence\Propel\Map\SpyCustomerTableMap;
use SprykerFeature\Zed\Gui\Communication\Form\AbstractForm;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Constraints\Required;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Callback;
use SprykerFeature\Zed\Customer\Business\Customer\Customer;
use Symfony\Component\Validator\Context\ExecutionContext;
use SprykerFeature\Zed\Customer\Business\CustomerFacade;

class CustomerForm extends AbstractForm
{

    const ADD = 'add';
    const UPDATE = 'update';
    const SALUTATION = 'salutation';
    const GENDER = 'gender';
    const ID_CUSTOMER = 'id_customer';

    /**
     * @var SpyCustomerQueryQuery
     */
    protected $customerQuery;

    /**
     * @var SpyCustomerAddressQuery
     */
    protected $customerAddressQuery;

    /**
     * @var
     */
    protected $type;

    /**
     * @param SpyCustomerQuery $customerQuery
     */
    public function __construct(SpyCustomerQuery $customerQuery, SpyCustomerAddressQuery $customerAddressQuery, $type)
    {
        $this->customerQuery = $customerQuery;
        $this->customerAddressQuery = $customerAddressQuery;
        $this->type = $type;
    }

    /**
     * @return $this
     */
    public function buildFormFields()
    {
        $emailConstraints = [
            new NotBlank(),
            new Required(),
            new Email(),
        ];

        if (self::ADD === $this->type) {
            $emailConstraints[] = new Callback([
                'methods' => [
                    function ($email, ExecutionContext $context) {
                        if ($this->getCustomerFacade()
                            ->hasEmail($email)
                        ) {
                            $context->addViolation('Email is already used');
                        }
                    },
                ],
            ]);
        }

        $emailParams = [
            'label' => 'Email',
            'constraints' => $emailConstraints,
        ];

        if (self::UPDATE == $this->type) {
            $emailParams['disabled'] = 'disabled';
        }

        $this->addHidden(self::ID_CUSTOMER, [
            'label' => 'Customer ID',
        ])
            ->addEmail('email', $emailParams)
            ->addChoice(self::SALUTATION, [
                'label' => 'Salutation',
                'placeholder' => 'Select one',
                'choices' => $this->getSalutationOptions(),
            ])
            ->addText('first_name', [
                'label' => 'First Name',
                'constraints' => [
                    new Required(),
                    new NotBlank(),
                    new Length(['max' => 100]),
                ],
            ])
            ->addText('last_name', [
                'label' => 'Last Name',
                'constraints' => [
                    new Required(),
                    new NotBlank(),
                    new Length(['max' => 100]),
                ],
            ])
            ->addChoice(self::GENDER, [
                'label' => 'Gender',
                'placeholder' => 'Select one',
                'choices' => $this->getGenderOptions(),
                'constraints' => [
                    new Required(),
                ],
            ])
        ;

        if (self::UPDATE === $this->type) {
            $this->addChoice('default_billing_address', [
                'label' => 'Billing Address',
                'placeholder' => 'Select one',
                'choices' => $this->getAddressOptions(),
            ])
                ->addChoice('default_shipping_address', [
                    'label' => 'Shipping Address',
                    'placeholder' => 'Select one',
                    'choices' => $this->getAddressOptions(),
                ])
            ;
        }

        $this->addSubmit('submit', [
            'label' => (self::UPDATE === $this->type ? 'Update' : 'Add'),
            'attr' => [
                'class' => 'btn btn-primary',
            ],
        ]);

        return $this;
    }

    /**
     * @return array
     */
    protected function populateFormFields()
    {
        $result = [];

        $idCustomer = $this->request->get(self::ID_CUSTOMER);

        if (false === is_null($idCustomer)) {
            $customerDetailEntity = $this->customerQuery->findOneByIdCustomer($idCustomer);

            if (false === is_null($customerDetailEntity)) {
                $result = $customerDetailEntity->toArray();
            }
        }

        if (false === empty($result[self::SALUTATION])) {
            $salutations = array_flip($this->getSalutationOptions());

            if (true === isset($salutations[$result[self::SALUTATION]])) {
                $result[self::SALUTATION] = $salutations[$result[self::SALUTATION]];
            }
        }

        if (false === empty($result[self::GENDER])) {
            $genders = array_flip($this->getGenderOptions());

            if (true === isset($genders[$result[self::GENDER]])) {
                $result[self::GENDER] = $genders[$result[self::GENDER]];
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getGenderOptions()
    {
        return [
            SpyCustomerTableMap::COL_GENDER_MALE,
            SpyCustomerTableMap::COL_GENDER_FEMALE,
        ];
    }

    /**
     * @return array
     */
    protected function getSalutationOptions()
    {
        return [
            SpyCustomerTableMap::COL_SALUTATION_MR,
            SpyCustomerTableMap::COL_SALUTATION_MRS,
            SpyCustomerTableMap::COL_SALUTATION_DR,
        ];
    }

    /**
     * @return array
     */
    protected function getAddressOptions()
    {
        $idCustomer = $this->request->get(self::ID_CUSTOMER);
        $addresses = $this->customerAddressQuery->findByFkCustomer($idCustomer);

        $result = [];
        if (false === empty($addresses)) {
            foreach ($addresses->getData() as $address) {
                $result[$address->getIdCustomerAddress()] = sprintf('%s %s (%s, %s %s)', $address->getFirstName(), $address->getLastName(), $address->getAddress1(), $address->getZipCode(), $address->getCity());
            }
        }

        return $result;
    }

    /**
     * @return CustomerFacade
     */
    protected function getCustomerFacade()
    {
        return $this->getLocator()
            ->customer()
            ->facade()
            ;
    }

}
