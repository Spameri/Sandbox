<?php declare(strict_types=1);

namespace App\WorkshopThreeModule\Model\ExportToElastic;

class DataImport implements \Spameri\Elastic\Import\DataImportInterface
{

	public function __construct(
		private readonly \App\WorkshopThreeModule\Model\WorkshopProductService $workshopProductService
	) {}


	/**
	 * @param \App\WorkshopThreeModule\Entity\WorkshopProduct $entity
	 */
	public function import(
		\Spameri\Elastic\Entity\AbstractImport $entity
	): \Spameri\Elastic\Import\ResponseInterface
	{
		$id = $this->workshopProductService->insert($entity);

		return new \Spameri\Elastic\Import\Response\SimpleResponse(
			$id,
			$entity
		);
	}
}
