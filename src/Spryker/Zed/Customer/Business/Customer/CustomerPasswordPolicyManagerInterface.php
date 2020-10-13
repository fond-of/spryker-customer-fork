<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Business\Customer;


use Generated\Shared\Transfer\CustomerPasswordPolicyResultTransfer;

interface CustomerPasswordPolicyManagerInterface
{
    public function check(): CustomerPasswordPolicyResultTransfer;
}