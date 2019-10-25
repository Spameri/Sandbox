<?php declare(strict_types = 1);

namespace App\ProductModule\Model\ExportToElastic;

class PrepareImportData implements \Spameri\Elastic\Import\PrepareImportDataInterface
{

	/**
	 * @var \Dibi\Connection
	 */
	private $connection;

	/**
	 * @var \App\ProductModule\Model\SimpleProductService
	 */
	private $simpleProductService;


	public function __construct(
		\Dibi\Connection $connection,
		\App\ProductModule\Model\SimpleProductService $simpleProductService
	)
	{
		$this->connection = $connection;
		$this->simpleProductService = $simpleProductService;
	}


	public function prepare($entityData): \Spameri\Elastic\Entity\AbstractImport
	{
		try {
			$query = new \Spameri\ElasticQuery\ElasticQuery();
			$query->addMustQuery(
				new \Spameri\ElasticQuery\Query\Term(
					'databaseId',
					$entityData['id']
				)
			);
			$existingProduct = $this->simpleProductService->getBy($query);
			$elasticId = $existingProduct->id();

		} catch (\Spameri\Elastic\Exception\ElasticSearchException $exception) {
			$elasticId = new \Spameri\Elastic\Entity\Property\EmptyElasticId();
		}

		$split = \str_split((string) $entityData['library_id']);
		$imageSrc = 'https://cdn.benu.cz/images/img-small-product/'
					. \end($split) . '/'
					. $entityData['library_id'] . '.jpg'
		;

		$tags = [];
		if ($entityData['isNew']) {
			$tags[] = 'Novinka';
		}
		if ($entityData['isFreeTransport']) {
			$tags[] = 'Doprava zdarma';
		}
		if ($entityData['isAction']) {
			$tags[] = 'Akční cena';
		}

		$categories = $this->connection->select('product_parameter_value_text.value')
			->from('product_parameter_value_text')
			->join('product_x_parameter_value')
				->on('product_parameter_value_text.parameter_value_id = product_x_parameter_value.parameter_value_id')
			->where('product_x_parameter_value.product_id = %i', $entityData['id'])
			->where('product_x_parameter_value.parameter_id = %i', 13)
			->fetchPairs(NULL, 'value')
		;

		return new \App\ProductModule\Entity\SimpleProduct(
			$elasticId,
			$entityData['id'],
			$entityData['name'],
			$entityData['content_description'],
			$entityData['alias'],
			$imageSrc,
			$entityData['amount'],
			$entityData['availability_id'] === 1 ? 'Skladem' : 'Nedostupné',
			$tags,
			$categories
		);
	}

}
