<?php

namespace Shippinno\NextEngine\OAuth2\Client\Tests\Provider;

use GuzzleHttp\ClientInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Shippinno\NextEngine\OAuth2\Client\Provider\NextEngineProvider;
use GuzzleHttp\Psr7\Stream;
use InvalidArgumentException;

class NextEngineTest extends TestCase
{
    /**
     * @var NextEngineProvider
     */
    protected $provider;

    protected function setUp(): void
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
        $stream = $this->createMock(StreamInterface::class);
        $stream
            ->method('__toString')
            ->willReturn('{"access_token":"mock_access_token","scope":"email","token_type":"bearer"}');

        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getBody')->andReturn($stream);
        $response->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);

        $client = Mockery::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')->once()->andReturn($response);
        $this->provider->setHttpClient($client);

        $token = $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
        $this->assertEquals('mock_access_token', $token->getToken());
        $this->assertNull($token->getExpires());
        $this->assertNull($token->getRefreshToken());
        $this->assertNull($token->getResourceOwnerId());
    }

    /**
     * @expectException \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     * @expectedExceptionMessage ERROR_DESCRIPTION
     * @expectExceptionCode 400
     */
    public function testExceptionThrownWhenErrorObjectReceived()
    {
        $status = rand(400, 600);
        $stream = $this->createMock(StreamInterface::class);
        $stream
            ->method('__toString')
            ->willReturn('{"error_description":"ERROR_DESCRIPTION","error":"some_error"}');

        $postResponse = Mockery::mock(ResponseInterface::class);
        $postResponse->shouldReceive('getBody')->andReturn($stream);
        $postResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'application/json;charset=UTF-8']);
        $postResponse->shouldReceive('getStatusCode')->andReturn($status);
        $client = Mockery::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')->once()->andReturn($postResponse);
        $this->provider->setHttpClient($client);

        $this->expectException(InvalidArgumentException::class);
        $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
    }

    public function testUserData()
    {
       
    }
}
