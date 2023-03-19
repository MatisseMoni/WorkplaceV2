<?php

namespace App\Controller;

use App\Entity\Conversation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;

class GetConversationsController extends AbstractController
{
    public function __construct() {}

    public function __invoke() : array
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new \Exception('You must be logged in to access this resource');
        }
        $OwnedConversations = $user->getOwnedConversations()->toArray();
        $tenantConversations = $user->getTenantConversations()->toArray();
        if (empty($OwnedConversations) && empty($tenantConversations)) {
            throw new \Exception('You have no conversations');
        }
        return array_merge($OwnedConversations, $tenantConversations);
    }
}
