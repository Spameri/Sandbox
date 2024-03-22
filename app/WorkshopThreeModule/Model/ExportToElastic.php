<?php declare(strict_types=1);

namespace App\WorkshopThreeModule\Model;

class ExportToElastic extends \Spameri\Elastic\Import\Run
{

	public function __construct(
		string $logDir,
		\Spameri\Elastic\Import\Run\NullLoggerHandler $loggerHandler,
		\Spameri\Elastic\Import\Lock\NullLock $lock,
		\Spameri\Elastic\Import\RunHandler\NullHandler $runHandler,
		\App\WorkshopThreeModule\Model\ExportToElastic\DataProvider $dataProvider,
		\App\WorkshopThreeModule\Model\ExportToElastic\PrepareImportData $prepareImportData,
		\App\WorkshopThreeModule\Model\ExportToElastic\DataImport $dataImport,
		\Spameri\Elastic\Import\AfterImport\NullAfterImport $afterImport
	)
	{
		parent::__construct($logDir, $loggerHandler, $lock, $runHandler, $dataProvider, $prepareImportData, $dataImport, $afterImport);
	}


}
