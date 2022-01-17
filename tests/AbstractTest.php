<?php


namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Enum\UserRoles;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;

/**
 * Class AbstractTest
 * @package App\Tests
 */
abstract class AbstractTest extends ApiTestCase
{

    private $token;

    protected $users = [
        UserRoles::ADMIN =>
            [
                'email' => 'admin@admin.com',
                'password' => 'admin',
            ],
        UserRoles::MANAGER =>
            [
                'email' => 'manager@manager.com',
                'password' => 'manager',
            ],
        UserRoles::WORKER =>
            [
                'email' => 'worker@worker.com',
                'password' => 'worker',
            ]
    ];

    protected $defaultRole = UserRoles::ADMIN;

    public function setUp(): void
    {
        self::bootKernel();
    }

    /**
     * @param null $token
     * @return Client
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function createClientWithCredentials($token = null): Client
    {
        $token = $token ?: $this->getToken();
        return static::createClient([], ['headers' => ['authorization' => 'Bearer ' . $token]]);
    }

    /**
     * @param array $body
     * @return string
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function getToken($body = []): string
    {
        if ($this->token) {
            return $this->token;
        }

        $response = static::createClient()->request('POST', '/authentication_token', ['json' => $body ?: $this->users[$this->defaultRole]]);
        $this->assertMatchesJsonSchema(['token', 'refreshToken']);
        $this->assertResponseStatusCodeSame(200);
        $data = json_decode($response->getContent());

        $response = static::createClient()->request('POST', '/refresh_token', ['json' => $body ?: [
            'refresh_token' => $data->refresh_token,
        ]]);
        $this->assertMatchesJsonSchema(['token', 'refreshToken']);
        $this->assertResponseStatusCodeSame(200);

        $data = json_decode($response->getContent());
        $this->token = $data->token;
        $this->assertResponseIsSuccessful();
        return $data->token;
    }

}