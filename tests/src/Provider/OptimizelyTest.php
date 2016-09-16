<?php

namespace WiderFunnel\OAuth2\Client\Tests\Provider;

use Mockery as m;

/**
 * Class OptimizelyTest
 * @package WiderFunnel\OAuth2\Client\Tests\Provider
 */
class OptimizelyTest extends \PHPUnit_Framework_TestCase
{

    protected $provider;

    public function tearDown()
    {
        m::close();
        parent::tearDown();
    }

    protected function setUp()
    {
        $this->provider = new \WiderFunnel\OAuth2\Client\Provider\Optimizely([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none',
        ]);
    }

    /**
     * @test
     */
    public function it_can_build_the_authorization_url()
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);
        parse_str($uri['query'], $query);

        $this->assertArrayHasKey('client_id', $query);
        $this->assertArrayHasKey('redirect_uri', $query);
        $this->assertArrayHasKey('state', $query);
        $this->assertArrayHasKey('scopes', $query);
        $this->assertArrayHasKey('response_type', $query);
        $this->assertArrayHasKey('approval_prompt', $query);
        $this->assertNotNull($this->provider->getState());
    }

    /**
     * @test
     */
    public function it_can_return_the_authorization_url()
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);

        $this->assertEquals('/oauth2/authorize', $uri['path']);
    }

    /**
     * @test
     */
    public function it_can_get_base_access_token_url()
    {
        $params = [];

        $url = $this->provider->getBaseAccessTokenUrl($params);
        $uri = parse_url($url);

        $this->assertEquals('/oauth2/token', $uri['path']);
    }

    /**
     * @test
     */
    public function it_can_get_access_token()
    {
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getBody')->andReturn('{"access_token":"mock_access_token",
        "expires_in": 7200, "token_type":"bearer", "refresh_token":"mock_refresh_token"}');
        $response->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);
        $response->shouldReceive('getStatusCode')->andReturn(200);

        $client = m::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')->times(1)->andReturn($response);
        $this->provider->setHttpClient($client);

        $token = $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);

        $this->assertEquals('mock_access_token', $token->getToken());
        $this->assertNotNull($token->getExpires());
        $this->assertEquals('mock_refresh_token', $token->getRefreshToken());
        $this->assertNull($token->getResourceOwnerId());
    }

    /**
     * @test
     */
    public function it_can_fetch_projects_data()
    {
        $firstProjectId = rand(100000000, 999999999);
        $secondProjectId = rand(100000000, 999999999);

        $postResponse = m::mock('Psr\Http\Message\ResponseInterface');
        $postResponse->shouldReceive('getBody')->andReturn('access_token=mock_access_token&expires=3600&refresh_token=mock_refresh_token&otherKey={1234}');
        $postResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'application/x-www-form-urlencoded']);
        $postResponse->shouldReceive('getStatusCode')->andReturn(200);

        $userResponse = m::mock('Psr\Http\Message\ResponseInterface');
        $userResponse->shouldReceive('getBody')->andReturn(
            '[{"id": ' . $firstProjectId . '}, {"id": ' . $secondProjectId . '}]'
        );
        $userResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);
        $userResponse->shouldReceive('getStatusCode')->andReturn(200);

        $client = m::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')
            ->times(2)
            ->andReturn($postResponse, $userResponse);
        $this->provider->setHttpClient($client);

        $token = $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
        $project = $this->provider->getResourceOwner($token);

        $this->assertTrue(is_array($project->toArray()));
        $this->assertNull($project->getId());
        $this->assertEquals(2, count($project->toArray()));
        $this->assertArrayHasKey('id', $project->toArray()[0]);
        $this->assertEquals($firstProjectId, $project->toArray()[0]['id']);
        $this->assertArrayHasKey('id', $project->toArray()[1]);
        $this->assertEquals($secondProjectId, $project->toArray()[1]['id']);
    }

    /**
     * @test
     * @expectedException \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function it_throws_an_exception_when_error_object_is_received()
    {
        $message = uniqid();
        $status = rand(400, 600);

        $postResponse = m::mock('Psr\Http\Message\ResponseInterface');
        $postResponse->shouldReceive('getBody')->andReturn(' {"message":"' . $message . '"}');
        $postResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);
        $postResponse->shouldReceive('getStatusCode')->andReturn($status);

        $client = m::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')
            ->times(1)
            ->andReturn($postResponse);
        $this->provider->setHttpClient($client);

        $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
    }
}
