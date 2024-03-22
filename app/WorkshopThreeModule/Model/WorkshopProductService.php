<?php declare(strict_types=1);

namespace App\WorkshopThreeModule\Model;

class WorkshopProductService extends \Spameri\Elastic\Model\AbstractBaseService
{

	public function __construct(
		\App\WorkshopThreeModule\Entity\WorkshopProductConfig $workshopProductConfig,
		\App\WorkshopProductThreeModule\Factory\WorkshopProductFactory $entityFactory,
		\App\WorkshopThreeModule\Factory\WorkshopProductCollectionFactory $collectionFactory,
		\Spameri\Elastic\Model\Insert $insert,
		\Spameri\Elastic\Model\Get $get,
		\Spameri\Elastic\Model\GetBy $getBy,
		\Spameri\Elastic\Model\GetAllBy $getAllBy,
		\Spameri\Elastic\Model\Delete $delete,
		\Spameri\Elastic\Model\Aggregate $aggregate
	)
	{
		parent::__construct(
			$workshopProductConfig->provide()->indexName(),
			$entityFactory,
			$collectionFactory,
			$insert,
			$get,
			$getBy,
			$getAllBy,
			$delete,
			$aggregate
		);
	}


	/**
	 * @return \Spameri\Elastic\Entity\ElasticEntityInterface&\App\WorkshopThreeModule\Entity\WorkshopProduct
	 */
	public function getBy(\Spameri\ElasticQuery\ElasticQuery $elasticQuery): \Spameri\Elastic\Entity\ElasticEntityInterface
	{
		return parent::getBy($elasticQuery);
	}

}
