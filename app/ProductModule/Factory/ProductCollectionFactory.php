<?php declare(strict_types = 1);

namespace App\ProductModule\Factory;


class ProductCollectionFactory implements \Spameri\Elastic\Factory\ICollectionFactory
{

	public function create(
		\Spameri\Elastic\Model\IService $service
		, array $elasticIds = NULL
		, \Spameri\Elastic\Entity\IElasticEntity ... $entityCollection
	) : \Spameri\Elastic\Entity\IElasticEntityCollection
	{
		return new \App\ProductModule\Entity\ProductCollection($service, $elasticIds, ... $entityCollection);
	}

}
