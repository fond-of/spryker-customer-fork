<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Business\Executor;

use Generated\Shared\Transfer\CustomerTransfer;

class CustomerPluginExecutor implements CustomerPluginExecutorInterface
{
    /**
     * @var list<\Spryker\Zed\CustomerExtension\Dependency\Plugin\PostCustomerRegistrationPluginInterface>
     */
    protected array $postCustomerRegistrationPlugins;

    /**
     * @var list<\Spryker\Zed\CustomerExtension\Dependency\Plugin\CustomerPostDeletePluginInterface>
     */
    protected array $customerPostDeletePlugins;

    /**
     * @var list<\Spryker\Zed\CustomerExtension\Dependency\Plugin\CustomerPreAddPluginInterface>
     */
    protected array $customerPreAddPlugins;

    /**
     * @var list<\Spryker\Zed\CustomerExtension\Dependency\Plugin\CustomerPreUpdatePluginInterface>
     */
    protected array $customerPreUpdatePlugins;

    /**
     * @var list<\Spryker\Zed\CustomerExtension\Dependency\Plugin\CustomerPostUpdatePluginInterface>
     */
    protected array $customerPostUpdatePlugins;

    /**
     * @param list<\Spryker\Zed\CustomerExtension\Dependency\Plugin\PostCustomerRegistrationPluginInterface> $postCustomerRegistrationPlugins
     * @param list<\Spryker\Zed\CustomerExtension\Dependency\Plugin\CustomerPostDeletePluginInterface> $customerPostDeletePlugins
     * @param list<\Spryker\Zed\CustomerExtension\Dependency\Plugin\CustomerPreAddPluginInterface> $customerPreAddPlugins
     * @param list<\Spryker\Zed\CustomerExtension\Dependency\Plugin\CustomerPreUpdatePluginInterface> $customerPreUpdatePlugins
     * @param list<\Spryker\Zed\CustomerExtension\Dependency\Plugin\CustomerPostUpdatePluginInterface> $customerPostUpdatePlugins
     */
    public function __construct(
        array $postCustomerRegistrationPlugins = [],
        array $customerPostDeletePlugins = [],
        array $customerPreAddPlugins = [],
        array $customerPreUpdatePlugins = [],
        array $customerPostUpdatePlugins = []
    ) {
        $this->postCustomerRegistrationPlugins = $postCustomerRegistrationPlugins;
        $this->customerPostDeletePlugins = $customerPostDeletePlugins;
        $this->customerPreAddPlugins = $customerPreAddPlugins;
        $this->customerPreUpdatePlugins = $customerPreUpdatePlugins;
        $this->customerPostUpdatePlugins = $customerPostUpdatePlugins;
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return void
     */
    public function executePostCustomerRegistrationPlugins(CustomerTransfer $customerTransfer): void
    {
        foreach ($this->postCustomerRegistrationPlugins as $postCustomerRegistrationPlugin) {
            $postCustomerRegistrationPlugin->execute($customerTransfer);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return void
     */
    public function executeCustomerPostDeletePlugins(CustomerTransfer $customerTransfer): void
    {
        foreach ($this->customerPostDeletePlugins as $customerPostDeletePlugin) {
            $customerPostDeletePlugin->execute($customerTransfer);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return void
     */
    public function executePreCustomerAddPlugins(CustomerTransfer $customerTransfer): void
    {
        foreach ($this->customerPreAddPlugins as $customerPreAddPlugin) {
            $customerPreAddPlugin->execute($customerTransfer);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return void
     */
    public function executePreUpdateCustomerPlugins(CustomerTransfer $customerTransfer): void
    {
        foreach ($this->customerPreUpdatePlugins as $customerPreUpdatePlugin) {
            $customerPreUpdatePlugin->execute($customerTransfer);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return void
     */
    public function executePostUpdateCustomerPlugins(CustomerTransfer $customerTransfer): void
    {
        foreach ($this->customerPostUpdatePlugins as $customerPostUpdatePlugin) {
            $customerPostUpdatePlugin->execute($customerTransfer);
        }
    }
}
