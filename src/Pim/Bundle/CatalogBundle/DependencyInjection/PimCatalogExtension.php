<?php

namespace Pim\Bundle\CatalogBundle\DependencyInjection;

use Akeneo\Bundle\StorageUtilsBundle\DependencyInjection\AkeneoStorageUtilsExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Finder\Finder;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class PimCatalogExtension extends AkeneoStorageUtilsExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('doctrine.yml');
        $loader->load('context.yml');
        $loader->load('validators.yml');
        $loader->load('event_subscribers.yml');
        $loader->load('managers.yml');
        $loader->load('savers.yml');
        $loader->load('removers.yml');
        $loader->load('builders.yml');
        $loader->load('helpers.yml');
        $loader->load('attribute_types.yml');
        $loader->load('factories.yml');
        $loader->load('entities.yml');
        $loader->load('repositories.yml');
        $loader->load('query_builders.yml');
        $loader->load('updaters.yml');
        $loader->load('resolvers.yml');
        $loader->load('models.yml');

        $this->loadValidationFiles($container);
        $this->loadStorageDriver($container, __DIR__);
    }

    /**
     * Loads the validation files
     *
     * @param ContainerBuilder $container
     */
    protected function loadValidationFiles(ContainerBuilder $container)
    {
        // load validation files
        $dirs = array();
        foreach ($container->getParameter('kernel.bundles') as $bundle) {
            $reflection = new \ReflectionClass($bundle);
            $dir = dirname($reflection->getFileName()) . '/Resources/config/validation';
            if (is_dir($dir)) {
                $dirs[] = $dir;
            }
        }
        $finder = new Finder();
        $mappingFiles = array();
        foreach ($finder->files()->in($dirs) as $file) {
            $mappingFiles[$file->getBasename('.yml')] = $file->getRealPath();
        }
        $container->setParameter(
            'validator.mapping.loader.yaml_files_loader.mapping_files',
            array_merge(
                $container->getParameter('validator.mapping.loader.yaml_files_loader.mapping_files'),
                array_values($mappingFiles)
            )
        );
    }
}
