<?php


namespace App\Tests;


use App\Enum\UserRoles;
use DateTime;

class PlanTest extends AbstractTest
{

    public function testWithoutLoginResource()
    {
        $this->createClient()->request('GET', '/api/plans');
        $this->assertJsonContains(['message' => 'JWT Token not found']);
        $this->assertResponseStatusCodeSame('401');
    }

    public function testWithLoginAsWorker()
    {
        $this->defaultRole = UserRoles::WORKER;
        $this->createClientWithCredentials()->request('GET', '/api/plans');
        $this->assertResponseStatusCodeSame('200');
        $this->assertResponseIsSuccessful();


        $firstDay = rand(1, 29);
        $lastDay = $firstDay + rand(1, 29);
        $now = new DateTime();
        $now_copy = new DateTime();
        $data = [
            "vacationStartDate" => $now->modify("+$firstDay day")->format(DateTime::ATOM),
            "vacationEndDate" => $now_copy->modify("+$lastDay day")->format(DateTime::ATOM)
        ];

        $response = $this->createClientWithCredentials()->request('POST', '/api/plans', ['json' => $data]);
        $data = json_decode($response->getContent());
        $this->assertResponseStatusCodeSame('201');
        $this->assertResponseIsSuccessful();

        $this->createClientWithCredentials()->request('GET', '/api/plans/' . $data->id);
        $this->assertResponseStatusCodeSame('200');
        $this->assertResponseIsSuccessful();

    }


    public function testWithLoginAsManager()
    {
        $this->defaultRole = UserRoles::MANAGER;
        $response = $this->createClientWithCredentials()->request('GET', '/api/plans', ["query" => ["order[id]" => "desc"]]);
        $data = json_decode($response->getContent());
        $this->assertResponseStatusCodeSame('200');
        $this->assertResponseIsSuccessful();

        if (count($data->{'hydra:member'})) {

            $planId = $data->{'hydra:member'}[0]->id;
//            var_dump($data->{'hydra:member'}[0]);
//            var_dump($planId);
//            ob_flush();
            $this->createClientWithCredentials()->request('GET', '/api/plans/' . $planId);
            $this->assertResponseStatusCodeSame('200');
            $this->assertResponseIsSuccessful();

            $this->createClientWithCredentials()->request('PUT', '/api/plans/' . $planId, ['json' => ['status' => "rejected"]]);
            $this->assertResponseStatusCodeSame('200');
            $this->assertResponseIsSuccessful();

        }

    }

}