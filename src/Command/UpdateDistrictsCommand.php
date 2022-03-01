<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Repository\DistrictRepository;
use App\Service\WebsiteScrapper;

#[AsCommand(
    name: 'UpdateDistricts',
    description: 'Updates the list of districts',
)]
class UpdateDistrictsCommand extends Command
{
    private WebsiteScrapper $websiteScrapper;

    private DistrictRepository $districtRepository;

    public function __construct(
        WebsiteScrapper $websiteScrapper,
        DistrictRepository $districtRepository,
        string $name = null
    ) {
        parent::__construct($name);
        $this->websiteScrapper = $websiteScrapper;
        $this->districtRepository = $districtRepository;
    }

    protected function configure(): void
    {
    // configuration
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($this->districtRepository->countDistricts() > 0) {
            $allDistricts = $this->districtRepository->findAll();
            foreach ($allDistricts as $district) {
                $this->districtRepository->remove($district);
            }
        }

        $districts = $this->websiteScrapper->scrapper();
        foreach ($districts as $district) {
            $this->districtRepository->add($district);
        }

        $io->success('District data downloaded and saved.');

        return Command::SUCCESS;
    }
}
