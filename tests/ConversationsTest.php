<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;

class ConversationsTest extends ApiTestCase
{
    use RecreateDatabaseTrait;

    public function testCreateConversation()
    {
        
        $client = static::createClient();
        $client->disableReboot();

        $client->request('POST', '/api/users', ['json' => [
            'email' => 'user1@test.com',
            'nickname' => 'User1',
            'plainPassword' => 'User1',
        ]]);
        $this->assertResponseIsSuccessful();

        $response = $client->request('POST', '/auth', ['json' => [
            'email' => 'user1@test.com',
            'password' => 'User1',
        ]]);
        $this->assertResponseIsSuccessful();

        $data = $response->toArray();
        $token = $data['token'];
        $client->request('POST', '/api/conversations', [
            'headers' => ['authorization' => 'Bearer ' . $token],
            'json' => [
                "tenant" => "/api/users/2"
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);
    }
    
}
