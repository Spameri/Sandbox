<?php declare(strict_types = 1);

namespace App\ProductModule\Model;

class ExportToElastic extends \Spameri\Elastic\Import\Run
{

	public function __construct(
		\Spameri\Elastic\Import\Run\NullLoggerHandler $loggerHandler,
		\Spameri\Elastic\Import\Lock\NullLock $lock,
		\Spameri\Elastic\Import\RunHandler\ConsoleHandler $runHandler,

		\App\ProductModule\Model\ExportToElastic\DataProvider $dataProvider,
		\App\ProductModule\Model\ExportToElastic\PrepareImportData $prepareImportData,
		\App\ProductModule\Model\ExportToElastic\DataImport $dataImport,

		\Spameri\Elastic\Import\AfterImport\NullAfterImport $afterImport,
		string $logDir = 'log',
	)
	{
		parent::__construct($logDir, $loggerHandler, $lock, $runHandler, $dataProvider, $prepareImportData, $dataImport, $afterImport);
	}

}
