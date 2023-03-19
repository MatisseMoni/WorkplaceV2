<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\PrivateMessage;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CreatePrivateMessageController extends AbstractController
{
    public function __construct() {}

    public function __invoke(PrivateMessage $privateMessage) : PrivateMessage
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new \Exception('You must be logged in to access this resource');
        }

        $conversation = $privateMessage->getConversation();
        if ($conversation->getOwner() !== $user && $conversation->getTenant() !== $user) {
            throw new \Exception('You cannot post a message in this conversation');
        }
        return $privateMessage;
    }
}