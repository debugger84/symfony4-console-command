<?php

namespace App\Command;

use App\Entity\Actor;
use App\Parse\Parser;
use App\Repository\ActorRepository;
use App\Repository\CampaignRepository;
use App\Service\CampaignPublisherBlacklistUpdater;
use App\Services\ImageDownLoader;
use App\Services\Picture\Config;
use Psr\Log\LoggerInterface;
use Rb\Specification\Doctrine\Exception\LogicException;
use Rb\Specification\Doctrine\Specification;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

class OptimizationJob extends Command
{
    protected static $defaultName = 'app:campaign:optimization';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CampaignRepository
     */
    private $campaignRepository;

    /**
     * @var CampaignPublisherBlacklistUpdater
     */
    private $updater;

    /**
     * OptimizationJob constructor.
     * @param LoggerInterface $logger
     * @param CampaignRepository $campaignRepository
     * @param CampaignPublisherBlacklistUpdater $updater
     */
    public function __construct(LoggerInterface $logger, CampaignRepository $campaignRepository, CampaignPublisherBlacklistUpdater $updater)
    {
        $this->logger = $logger;
        $this->campaignRepository = $campaignRepository;
        $this->updater = $updater;

        parent::__construct();
    }


    protected function configure()
    {
        $this->setDescription('Optimizes your campaigns, adding publishers to blacklist');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws LogicException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $spec = new Specification([]);
        $campaigns = $this->campaignRepository->findAllBySpecification($spec);

        foreach ($campaigns as $campaign) {
            try {
                $this->updater->updatePublishers($campaign);
                $io->writeln('A blacklist is updated for the campaign #' . $campaign->getId());
            } catch (Throwable $e) {
                $message = 'We have an error during processing of campaign\' blacklist. 
                    Campaign #' . $campaign->getId();
                $this->logger->error($message);
                $io->writeln($message);
            }
        }

        $io->success('Publishers checked.');
    }
}
