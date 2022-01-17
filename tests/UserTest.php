<?php


namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Enum\UserRoles;

class UserTest extends AbstractTest
{
    public function testWithoutLoginResource()
    {
        $this->createClient()->request('GET', '/api/users');
        $this->assertJsonContains(['message' => 'JWT Token not found']);
        $this->assertResponseStatusCodeSame('401');
    }

    public function testWithLoginAsAdmin()
    {
        $this->defaultRole = UserRoles::ADMIN;
        $this->createClientWithCredentials()->request('GET', '/api/users');
        $this->assertResponseStatusCodeSame('200');
        $this->assertResponseIsSuccessful();

        $this->createClientWithCredentials()->request('GET', '/api/users/current');
        $this->assertResponseStatusCodeSame('200');
        $this->assertResponseIsSuccessful();
    }

    public function testWithLoginAsManager()
    {
        $this->defaultRole = UserRoles::MANAGER;
        $this->createClientWithCredentials()->request('GET', '/api/users');
        $this->assertResponseStatusCodeSame('200');
        $this->assertResponseIsSuccessful();

        $this->createClientWithCredentials()->request('GET', '/api/users/current');
        $this->assertResponseStatusCodeSame('200');
        $this->assertResponseIsSuccessful();
    }

    public function testWithLoginAsWorker()
    {
        $this->defaultRole = UserRoles::WORKER;
        $this->createClientWithCredentials()->request('GET', '/api/users');
        $this->assertResponseStatusCodeSame('403');

        $this->createClientWithCredentials()->request('GET', '/api/users/current');
        $this->assertResponseStatusCodeSame('200');
        $this->assertResponseIsSuccessful();
    }
}