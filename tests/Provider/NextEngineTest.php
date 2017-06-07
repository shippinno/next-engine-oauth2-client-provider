<?php

namespace Shippinno\NextEngine\OAuth2\Client\Tests\Provider;

use GuzzleHttp\ClientInterface;
use League\OAuth2\Client\Token\AccessToken;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Shippinno\NextEngine\OAuth2\Client\Provider\NextEngineProvider;

class NextEngineTest extends TestCase
{
    /**
     * @var NextEngineProvider
     */
    protected $provider;

    protected function setUp()
    {
        $this->provider = new NextEngineProvider([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'mock_redirect_uri',
        ]);
    }

    public function testAuthorizationUrl()
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);
        parse_str($uri['query'], $query);

        $this->assertArrayHasKey('client_id', $query);
        $this->assertArrayHasKey('client_secret', $query);
        $this->assertArrayHasKey('redirect_uri', $query);
        $this->assertNull($this->provider->getState());
    }

    public function testResourceOwnerDetailsUrl()
    {
        $token = Mockery::mock(AccessToken::class);
        $this->assertEquals('', $this->provider->getResourceOwnerDetailsUrl($token));
    }

    public function testGetAccessToken()
    {
        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getBody')->andReturn('{"access_token":"mock_access_token", "token_type":"bearer"}');
        $response->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);
        $response->shouldReceive('getStatusCode')->andReturn(200);
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')->once()->andReturn($response);
        $this->provider->setHttpClient($client);
        $token = $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
        $this->assertEquals('mock_access_token', $token->getToken());
        $this->assertNull($token->getExpires());
        $this->assertNull($token->getRefreshToken());
        $this->assertNull($token->getResourceOwnerId());
    }

    /**
     * @expectedException League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function testExceptionThrownWhenErrorObjectReceived()
    {
        $message = uniqid();
        $status = rand(400, 600);
        $postResponse = Mockery::mock(ResponseInterface::class);
        $postResponse->shouldReceive('getBody')->andReturn(' {"error":"' . $message . '"}');
        $postResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);
        $postResponse->shouldReceive('getStatusCode')->andReturn($status);
        $client = Mockery::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')->once()->andReturn($postResponse);
        $this->provider->setHttpClient($client);
        $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
    }

    public function testUserData()
    {
       
    }
}
