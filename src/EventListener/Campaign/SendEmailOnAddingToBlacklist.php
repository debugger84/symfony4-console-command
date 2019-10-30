<?php

namespace App\EventListener\Campaign;

use App\Entity\Publisher;
use App\Event\Campaign\PublisherAddedToBlacklistEvent;
use App\Service\EmailSender;
use Exception;

class SendEmailOnAddingToBlacklist
{
    /**
     * @var EmailSender
     */
    private $sender;

    /**
     * SendEmailOnAddingToBlacklist constructor.
     * @param EmailSender $sender
     */
    public function __construct(EmailSender $sender)
    {
        $this->sender = $sender;
    }

    /**
     * @param PublisherAddedToBlacklistEvent $event
     * @throws Exception
     */
    public function __invoke(PublisherAddedToBlacklistEvent $event)
    {
        $campaign = $event->getCampaign();
        $email = $this->getEmailOfPublisher($event->getPublisher());

        //actually you shouldn't send email directly.
        //According to you loads you can send them through a queue such as RabbitMQ queue or a queue in a database
        $this->sender->send(
            $email,
            'You have been added to the blacklist of the campaign #' . $campaign->getId(),
            $this->getText()
        );
    }

    private function getText()
    {
        // some code with using template engine to prepare the text of the message
        return 'Feel free to ask us how to get out from the blacklist.';
    }

    private function getEmailOfPublisher(Publisher $publisher)
    {
        $id = $publisher->getId();
        //some code to get email of current publisher
        $email = $id . '@test.com';

        return $email;
    }
}
