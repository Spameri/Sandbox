<?php declare(strict_types=1);

namespace App\WorkshopThreeModule\Command;

class ExportToElastic extends \Symfony\Component\Console\Command\Command
{

	public function __construct(
		private readonly \App\WorkshopThreeModule\Model\ExportToElastic $exportToElastic
	) {
		parent::__construct();
	}


	public function configure(): void
	{
		$this->setName('workshopThree:exportToElastic');
	}


	public function execute(
		\Symfony\Component\Console\Input\InputInterface $input,
		\Symfony\Component\Console\Output\OutputInterface $output
	): int
	{
		$options = new \Spameri\Elastic\Import\Run\Options(30);

		$this->exportToElastic->setOutput($output);

		$this->exportToElastic->execute($options);

		return 0;
	}

}
