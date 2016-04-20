<?php

namespace PauPeris\GoogleAPIClientBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('pauperis_google_api_client');

        $rootNode
            ->children()
                ->scalarNode('client_id')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('client_secret')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('redirect_uri')
                    ->defaultNull()
                ->end()
                ->scalarNode('application_name')
                    ->defaultNull()
                ->end()
                ->arrayNode('scopes')
                    ->prototype('scalar')
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
