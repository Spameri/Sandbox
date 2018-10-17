<?php declare(strict_types = 1);

namespace App\ProductModule\Model;


class ProductService extends \Spameri\Elastic\Model\BaseService
{

	/**
	 * @param \Spameri\Elastic\Entity\IElasticEntity|\App\ProductModule\Entity\Product $entity
	 * @return string
	 */
	public function insert(
		\Spameri\Elastic\Entity\IElasticEntity $entity
	) : string
	{
		return parent::insert($entity);
	}


	/**
	 * @param \Spameri\Elastic\Entity\Property\ElasticId $id
	 * @return \Spameri\Elastic\Entity\IElasticEntity|\App\ProductModule\Entity\Product
	 */
	public function get(
		\Spameri\Elastic\Entity\Property\ElasticId $id
	) : \Spameri\Elastic\Entity\IElasticEntity
	{
		return parent::get($id);
	}


	/**
	 * @param \Spameri\ElasticQuery\ElasticQuery $elasticQuery
	 * @return \Spameri\Elastic\Entity\IElasticEntity|\App\ProductModule\Entity\Product
	 */
	public function getBy(
		\Spameri\ElasticQuery\ElasticQuery $elasticQuery
	) : \Spameri\Elastic\Entity\IElasticEntity
	{
		return parent::getBy($elasticQuery);
	}


	/**
	 * @param \Spameri\ElasticQuery\ElasticQuery $elasticQuery
	 * @return \Spameri\Elastic\Entity\IElasticEntityCollection|\App\ProductModule\Entity\ProductCollection
	 */
	public function getAllBy(\Spameri\ElasticQuery\ElasticQuery $elasticQuery
	) : \Spameri\Elastic\Entity\IElasticEntityCollection
	{
		return parent::getAllBy($elasticQuery);
	}

}
