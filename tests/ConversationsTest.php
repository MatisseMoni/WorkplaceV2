<?php

namespace App\Tests;

use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;

class ConversationsTest extends AbstractTest
{

    /* public function testGetConversations()
    {
        $response = static::createClient()->request('GET', '/api/conversations');
        $this->assertResponseStatusCodeSame(200);
        $this->assertCount(3, $response->toArray()['hydra:member']);
    } */

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
