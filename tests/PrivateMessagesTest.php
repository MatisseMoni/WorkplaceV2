<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;

class PrivateMessagesTest extends ApiTestCase
{
    use RecreateDatabaseTrait;

    public function testPostPrivateMessage()
    {
        $client = static::createClient();
        $client->disableReboot();

        $client->request('POST', '/api/users', ['json' => [
            'email' => 'user1@test.com',
            'nickname' => 'User1',
            'plainPassword' => 'User1',
        ]]);
        $this->assertResponseIsSuccessful();

        $responseUser2 = $client->request('POST', '/api/users', ['json' => [
            'email' => 'user2@test.com',
            'nickname' => 'User2',
            'plainPassword' => 'User2',
        ]]);
        $this->assertResponseIsSuccessful();
        $iriUser2 = $responseUser2->toArray()['@id'];

        $response = $client->request('POST', '/auth', ['json' => [
            'email' => 'user1@test.com',
            'password' => 'User1',
        ]]);
        $this->assertResponseIsSuccessful();
        $data = $response->toArray();
        $token = $data['token'];

        $responseConversation = $client->request('POST', '/api/conversations', [
            'headers' => ['authorization' => 'Bearer ' . $token],
            'json' => [
                "tenant" => $iriUser2
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);
        $iriConversation = $responseConversation->toArray()['@id'];

        $response = $client->request('POST', '/api/private_messages', [
            'headers' => ['authorization' => 'Bearer ' . $token],
            'json' => [
                "content" => "This is a test message",
                "conversation" => $iriConversation
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);
    }

    public function testPostPrivateMessageFail()
    {
        $client = static::createClient();
        $client->disableReboot();

        $client->request('POST', '/api/users', ['json' => [
            'email' => 'user1@test.com',
            'nickname' => 'User1',
            'plainPassword' => 'User1',
        ]]);
        $this->assertResponseIsSuccessful();

        $responseUser2 = $client->request('POST', '/api/users', ['json' => [
            'email' => 'user2@test.com',
            'nickname' => 'User2',
            'plainPassword' => 'User2',
        ]]);
        $this->assertResponseIsSuccessful();
        $iriUser2 = $responseUser2->toArray()['@id'];

        $responseUser3 = $client->request('POST', '/api/users', ['json' => [
            'email' => 'user3@test.com',
            'nickname' => 'User3',
            'plainPassword' => 'User3',
        ]]);
        $this->assertResponseIsSuccessful();
        $iriUser3 = $responseUser3->toArray()['@id'];

        $responseToken1 = $client->request('POST', '/auth', ['json' => [
            'email' => 'user1@test.com',
            'password' => 'User1',
        ]]);
        $this->assertResponseIsSuccessful();
        $dataToken1 = $responseToken1->toArray();
        $token1 = $dataToken1['token'];

        $responseToken2 = $client->request('POST', '/auth', ['json' => [
            'email' => 'user2@test.com',
            'password' => 'User2',
        ]]);
        $this->assertResponseIsSuccessful();
        $dataToken2 = $responseToken2->toArray();
        $token2 = $dataToken2['token'];

        $responseConversation = $client->request('POST', '/api/conversations', [
            'headers' => ['authorization' => 'Bearer ' . $token2],
            'json' => [
                "tenant" => $iriUser3
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);
        $iriConversation = $responseConversation->toArray()['@id'];

        $response = $client->request('POST', '/api/private_messages', [
            'headers' => ['authorization' => 'Bearer ' . $token1],
            'json' => [
                "content" => "This is a test message",
                "conversation" => $iriConversation
            ]
        ]);
        $this->assertResponseStatusCodeSame(400);
    }
}
