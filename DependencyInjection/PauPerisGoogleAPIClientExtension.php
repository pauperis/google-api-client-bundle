<?php

namespace PauPeris\GoogleAPIClientBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class PauPerisGoogleAPIClientExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {

        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration( $configuration, $configs );

        if( !empty( $config ) ) {
            $path   = sprintf( '%1$s%2$s..%2$sResources%2$sconfig%2$s', __DIR__, DIRECTORY_SEPARATOR );
            $loader = new  YamlFileLoader( $container, new FileLocator( array( $path ) ) );
            $files = array( 'services.yml' );
            foreach( $files as $file )
            {
                $filePath = $path . $file;
                if( file_exists( $filePath ) ) { $loader->load( $file ); }
            }

            foreach( $config as $k => $v )
            {
                $container->setParameter( sprintf( 'google.api_client.%s', $k ), $v );
            }
        }
    }
}
