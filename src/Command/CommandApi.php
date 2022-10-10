<?php
namespace App\Command;

use App\Service\VkUsersService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CommandApi extends Command
{
    private HttpClientInterface $client;
    private ManagerRegistry $doctrine;

    protected static $defaultName = 'app:get-users';

    public function __construct(HttpClientInterface $client, ManagerRegistry $doctrine)
    {
        $this->client = $client;
        $this->doctrine = $doctrine;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Write VK users data records to database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $serviceOb = new VkUsersService($this->client, $this->doctrine);

        $parsedData = $serviceOb->getApiUsers();
        $count = count($parsedData);

        $recordsCounter = 0;
        if ($count > 0) {
            $recordsCounter = $serviceOb->writeUsersData($parsedData);
        }

        $io->success('Wrote records to db count is '. $recordsCounter);

        return 0;
    }
}