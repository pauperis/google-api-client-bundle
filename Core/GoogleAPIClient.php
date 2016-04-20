<?php

namespace PauPeris\GoogleAPIClientBundle\Core;

use Symfony\Component\DependencyInjection\ContainerInterface;

class GoogleAPIClient
{
    protected $container;

    protected $GACRootPath;

    protected $GACServicePath;

    protected $availableServices;

    protected $services = array();

    protected $client;

    protected $clientId;

    protected $clientSecret;

    protected $redirectUri;

    protected $scopes;

    protected $gso2;

    public function __construct( ContainerInterface $container, $services = array() )
    {
        $this->container        = $container;
        $this->GACRootPath      = sprintf( '%1$s%2$s..%2$s..%2$s..%2$s..%2$svendor%2$sgoogle%2$sapiclient%2$ssrc%2$sGoogle%2$s', dirname( __FILE__ ), DIRECTORY_SEPARATOR );
        $this->GACServicePath   = $this->GACRootPath . 'Service' . DIRECTORY_SEPARATOR;

        include_once $this->GACRootPath.'Client.php';

        $dc = scandir( $this->GACServicePath );
        $this->availableServices = array_slice( $dc, 2 );

        if( !empty( $services ) ) { $this->loadServices( $services ); }

        $this->client = new \Google_Client();
        $this->setClientId( $this->container->getParameter( 'google.api_client.client_id' ) );
        $this->setClientSecret( $this->container->getParameter( 'google.api_client.client_secret' ) );
        $this->setRedirectUri( $this->container->getParameter( 'google.api_client.redirect_uri' ) );

        $scopes = $this->container->getParameter( 'google.api_client.scopes' );
        foreach( $scopes as $scope ) { $this->addScope( $scope ); }
    }

    public function setClientId( $clientId )
    {
        $this->clientId = $clientId;
        $this->client->setClientId( $clientId );

        return $this;
    }

    public function setClientSecret( $clientSecret )
    {
        $this->clientSecret = $clientSecret;
        $this->client->setClientSecret( $clientSecret );

        return $this;
    }

    public function setRedirectUri( $redirectUri )
    {
        $this->redirectUri = $redirectUri;
        $this->client->setRedirectUri( $redirectUri );

        return $this;
    }

    public function addScope( $scope )
    {
        if( !empty( $this->getScopes() ) && in_array( $scope, $this->getScopes() ) ) { return false; }

        $this->scopes[] = $scope;
        $this->client->addScope( $scope );

        return $this;
    }

    public function getScopes()
    {
        return $this->scopes;
    }

    public function getClientId()
    {
        return $this->clientId;
    }

    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    public function getRedirectUri()
    {
        return $this->redirectUri;
    }

    public function getAvailableServices()
    {
        return $this->availableServices;
    }

    public function getServices()
    {
        return $this->services;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function getGoogleServiceOauth2()
    {
        $this->loadservices( array( 'Oauth2.php' ) );
        if( empty( $this->gso2 ) ) { $this->gso2 = new \Google_Service_Oauth2( $this->client ); }

        return $this->gso2;
    }

    public function loadServices( $services )
    {
        if( is_array( $services ) ) {
            //Check requested service(s) exists
            $serviceExists = count( array_intersect( $services, $this->availableServices ) ) == count( $services );
            if( $serviceExists ) {
                //Remove already loaded services from $services array
                if( count( array_intersect( $services, $this->services ) ) != 0 ) {
                    $services = array_diff( $services, $this->services );
                }

                //Update service(s)
                if( !empty( $services ) ) {
                    //Merge already loaded services with requested $services
                    if( !empty( $this->services ) ) {
                        $ts     = array_flip( $services );
                        $tts    = array_flip( $this->services );
                        $tts    = array_merge( $ts, $tts );
                        $this->services = array_keys( $tts );
                        $services = array_flip( $ts );
                    } else { $this->services = $services; }

                    foreach( $services as $service  ) { include_once $this->GACServicePath . $service; }
                    return true;
                }
            }
        }

        return false;
    }
}