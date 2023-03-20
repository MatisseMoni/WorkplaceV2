<?php

namespace App\Controller;

use App\Entity\Conversation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetConversationsController extends AbstractController
{
    public function __construct() {}

    public function __invoke() : array
    {
        $user = $this->getUser();

        $OwnedConversations = $user->getOwnedConversations()->toArray();
        $tenantConversations = $user->getTenantConversations()->toArray();
        if (empty($OwnedConversations) && empty($tenantConversations)) {
            throw new NotFoundHttpException('You have no conversations');
        }
        return array_merge($OwnedConversations, $tenantConversations);
    }
}
