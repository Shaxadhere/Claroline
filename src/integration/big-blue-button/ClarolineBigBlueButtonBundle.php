<?php

/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Claroline\BigBlueButtonBundle;

use Claroline\BigBlueButtonBundle\Installation\AdditionalInstaller;
use Claroline\CoreBundle\Library\DistributionPluginBundle;

class ClarolineBigBlueButtonBundle extends DistributionPluginBundle
{
    public function getAdditionalInstaller()
    {
        return new AdditionalInstaller();
    }

    public function hasMigrations()
    {
        return true;
    }
}
