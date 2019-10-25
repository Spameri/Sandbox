<?php declare(strict_types = 1);

namespace App\ProductModule\Entity;

class SimpleProductConfig implements \Spameri\Elastic\Settings\IndexConfigInterface
{

	/**
	 * @var string
	 */
	private $indexName;


	public function __construct(
		string $indexName
	)
	{
		$this->indexName = $indexName;
	}


	public function provide(): \Spameri\ElasticQuery\Mapping\Settings
	{
		$settings = new \Spameri\ElasticQuery\Mapping\Settings($this->indexName);
		$czechDictionary = new \Spameri\ElasticQuery\Mapping\Analyzer\Custom\CzechDictionary();
		$settings->addAnalyzer($czechDictionary);

		$lowerCase = new \Spameri\ElasticQuery\Mapping\Analyzer\Custom\Lowercase();
		$settings->addAnalyzer($lowerCase);

		$settings->addMappingField(
			new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
				'databaseId',
				\Spameri\Elastic\Model\ValidateMapping\AllowedValues::TYPE_KEYWORD
			)
		);
		$settings->addMappingField(
			new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
				'name',
				\Spameri\Elastic\Model\ValidateMapping\AllowedValues::TYPE_TEXT,
				$czechDictionary
			)
		);
		$settings->addMappingField(
			new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
				'content',
				\Spameri\Elastic\Model\ValidateMapping\AllowedValues::TYPE_TEXT,
				$czechDictionary
			)
		);
		$settings->addMappingField(
			new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
				'alias',
				\Spameri\Elastic\Model\ValidateMapping\AllowedValues::TYPE_KEYWORD
			)
		);
		$settings->addMappingField(
			new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
				'image',
				\Spameri\Elastic\Model\ValidateMapping\AllowedValues::TYPE_KEYWORD
			)
		);
		$settings->addMappingField(
			new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
				'price',
				\Spameri\Elastic\Model\ValidateMapping\AllowedValues::TYPE_DOUBLE
			)
		);
		$settings->addMappingField(
			new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
				'availability',
				\Spameri\Elastic\Model\ValidateMapping\AllowedValues::TYPE_TEXT,
				$lowerCase
			)
		);
		$settings->addMappingField(
			new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
				'tags',
				\Spameri\Elastic\Model\ValidateMapping\AllowedValues::TYPE_TEXT,
				$lowerCase
			)
		);
		$settings->addMappingField(
			new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
				'categories',
				\Spameri\Elastic\Model\ValidateMapping\AllowedValues::TYPE_TEXT,
				$lowerCase
			)
		);

		return $settings;
	}

}
