<?php declare(strict_types = 1);

namespace App\ProductModule\Model\ExportToElastic;

class DataImport implements \Spameri\Elastic\Import\DataImportInterface
{

	/**
	 * @var \App\ProductModule\Model\SimpleProductService
	 */
	private $productService;


	public function __construct(
		\App\ProductModule\Model\SimpleProductService $productService
	)
	{
		$this->productService = $productService;
	}


	/**
	 * @param \App\ProductModule\Entity\SimpleProduct $entity
	 */
	public function import(
		\Spameri\Elastic\Entity\AbstractImport $entity
	): \Spameri\Elastic\Import\ResponseInterface
	{
		$id = $this->productService->insert($entity);

		return new \Spameri\Elastic\Import\Response\SimpleResponse(
			$id,
			$entity
		);
	}

}
