<?php declare(strict_types=1);

namespace App\ProductModule\Model;

class WorkshopProductService extends \Spameri\Elastic\Model\BaseService
{

	/**
	 * @param \Spameri\ElasticQuery\ElasticQuery $elasticQuery
	 * @return \Spameri\Elastic\Entity\IElasticEntity&\App\ProductModule\Entity\WorkshopProduct
	 */
	public function getBy(\Spameri\ElasticQuery\ElasticQuery $elasticQuery): \Spameri\Elastic\Entity\IElasticEntity
	{
		return parent::getBy($elasticQuery);
	}

}
