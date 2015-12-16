<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Customer\Communication\Controller;

use Spryker\Zed\Application\Communication\Controller\AbstractController;
use Spryker\Zed\Customer\Business\CustomerFacade;
use Spryker\Zed\Customer\Communication\CustomerCommunicationFactory;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @method CustomerFacade getFacade()
 * @method CustomerCommunicationFactory getCommunicationFactory()
 */
class IndexController extends AbstractController
{

    /**
     * @return array
     */
    public function indexAction()
    {
        $table = $this->getCommunicationFactory()
            ->createCustomerTable();

        return $this->viewResponse([
            'customerTable' => $table->render(),
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function tableAction()
    {
        $table = $this->getCommunicationFactory()
            ->createCustomerTable();

        return $this->jsonResponse($table->fetchData());
    }

}
