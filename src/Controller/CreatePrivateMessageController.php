<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\PrivateMessage;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


class CreatePrivateMessageController extends AbstractController
{
    public function __construct() {}

    public function __invoke(PrivateMessage $privateMessage) : PrivateMessage
    {
        $user = $this->getUser();

        $conversation = $privateMessage->getConversation();
        if ($conversation->getOwner() !== $user && $conversation->getTenant() !== $user) {
            throw new BadRequestHttpException('You cannot post a message in this conversation');
        }
        return $privateMessage;
    }
}