<?php

namespace Claroline\KernelBundle\Routing;

use Symfony\Component\Config\Loader\Loader as Loader;
use Symfony\Component\Routing\RouteCollection;
use Claroline\KernelBundle\Manager\BundleManager;
use Claroline\KernelBundle\Bundle\ConfigurationBuilder;

class DynamicLoader extends Loader
{
    public function load($resource, $type = null)
    {
        $collection = new RouteCollection();

        foreach (BundleManager::getInstance()->getActiveBundles() as $bundle) {
            foreach ($bundle[BundleManager::BUNDLE_CONFIG]->getRoutingResources() as $resource) {
                $collection->addCollection(
                    $this->import(
                        $resource[ConfigurationBuilder::RESOURCE_OBJECT],
                        $resource[ConfigurationBuilder::RESOURCE_TYPE]
                    )
                );
            }
        }

        return $collection;
    }

    public function supports($resource, $type = null)
    {
        return $type === 'dynamic';
    }
}
