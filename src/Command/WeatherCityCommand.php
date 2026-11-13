<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\WeatherUtil;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'weather:city', description: 'Prognoza pogody dla kodu kraju i miejscowości.')]
class WeatherCityCommand extends Command
{
    public function __construct(private readonly WeatherUtil $weatherUtil)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('country', InputArgument::REQUIRED, 'Kod kraju')
            ->addArgument('city', InputArgument::REQUIRED, 'Nazwa miejscowości');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $country = (string) $input->getArgument('country');
        $city = (string) $input->getArgument('city');
        try {
            $measurements = $this->weatherUtil->getWeatherForCountryAndCity($country, $city);
        } catch (\InvalidArgumentException $exception) {
            $io->error($exception->getMessage());
            return Command::FAILURE;
        }
        $io->writeln(sprintf('Location: %s (%s)', $city, $country));
        $io->table(['Date', 'Temperature'], array_map(static fn ($m) => [
            $m->getDate()->format('Y-m-d'), $m->getCelsius(),
        ], $measurements));
        return Command::SUCCESS;
    }
}
