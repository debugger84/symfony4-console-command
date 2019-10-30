<?php

namespace App\EventListener\Campaign;

use App\Entity\Publisher;
use App\Event\Campaign\PublisherRemovedFromBlacklistEvent;
use App\Service\EmailSender;

class SendEmailOnRemovingFromBlacklist
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
     * @param PublisherRemovedFromBlacklistEvent $event
     */
    public function __invoke(PublisherRemovedFromBlacklistEvent $event)
    {
        $campaign = $event->getCampaign();
        $email = $this->getEmailOfPublisher($event->getPublisher());

        $this->sender->send(
            $email,
            'You have been removed from the blacklist of the campaign #' . $campaign->getId(),
            $this->getText()
        );
    }

    private function getText()
    {
        // some code with using template engine to prepare the text of the message
        return 'Cheers, you are free to do anything.';
    }

    private function getEmailOfPublisher(Publisher $publisher)
    {
        $id = $publisher->getId();
        //some code to get email of current publisher
        $email = $id . '@test.com';

        return $email;
    }
}
