<?php declare(strict_types=1);

namespace App\WorkshopThreeModule\Entity;

class WorkshopProductConfig implements \Spameri\Elastic\Settings\IndexConfigInterface
{


	public function __construct(
		private readonly string $indexName
	) {}


	public function provide(): \Spameri\ElasticQuery\Mapping\Settings
	{
		$stopWords = [
			'angin',
			'ml',
			'l',
			'litr',
			'litry',
			'litrů',
			'ks',
			'pastilka',
			'pastilky',
			'pastilek',
			'kapsle',
			'kapslí',
			'tbl',
			'tob',
			'dr',
			'cps',
			'x',
			'g',
			'mg',
			'báze',
			'bázi',
			'e',
			'dp',
			'duo',
			'v',
			'a',
			'plus',
			'por',
			'nob',
			'zn',
			'la',
			'lr',
			'b6',
			'n',
		];
		$settings = new \Spameri\ElasticQuery\Mapping\Settings($this->indexName);

		$stopFilter = new \Spameri\ElasticQuery\Mapping\Filter\Stop\Czech($stopWords);
		$czechDictionary = new \Spameri\ElasticQuery\Mapping\Analyzer\Custom\CzechDictionary($stopFilter);
		$settings->addAnalyzer($czechDictionary);

		$lowerCaseAnalyzer = new \Spameri\ElasticQuery\Mapping\Analyzer\Custom\Lowercase();
		$settings->addAnalyzer($lowerCaseAnalyzer);

		$edgeNgramAnalyzer = new \Spameri\ElasticQuery\Mapping\Analyzer\Custom\EdgeNgram(
			2,
			6,
			$stopFilter
		);
		$settings->addAnalyzer($edgeNgramAnalyzer);

		$wordSplit = new \Spameri\ElasticQuery\Mapping\Analyzer\Custom\WordDelimiter($stopFilter);
		$settings->addAnalyzer($wordSplit);

		$wordJoin = new \Spameri\ElasticQuery\Mapping\Analyzer\Custom\CommonGrams($stopWords);
		$settings->addAnalyzer($wordJoin);

		$synonymAnalyzer = new \Spameri\ElasticQuery\Mapping\Analyzer\Custom\Synonym\CzechSynonym(
			new \Spameri\ElasticQuery\Mapping\Filter\Stop\None(),
			synonyms: [
				'Děti' => 'Dudlík',
			]
		);
		$settings->addAnalyzer($synonymAnalyzer);


		// FIELDS


		$settings->addMappingField(
			new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
				'databaseId',
				\Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_KEYWORD
			)
		);

		$settings->addMappingSubField(
			new \Spameri\ElasticQuery\Mapping\Settings\Mapping\SubFields(
				'name',
				\Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_TEXT,
				new \Spameri\ElasticQuery\Mapping\Settings\Mapping\FieldCollection(
					new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
						'dictionary',
						\Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_TEXT,
						$czechDictionary
					),
					new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
						'edgeNgram',
						\Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_TEXT,
						$edgeNgramAnalyzer
					),
					new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
						'wordJoin',
						\Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_TEXT,
						$wordJoin
					),
					new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
						'wordSplit',
						\Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_TEXT,
						$wordSplit
					),
					new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
						'synonym',
						\Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_TEXT,
						$synonymAnalyzer
					),
				)
			)
		);

		$settings->addMappingField(
			new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
				'content',
				\Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_TEXT,
				$czechDictionary
			)
		);

		$settings->addMappingField(
			new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
				'alias',
				\Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_KEYWORD
			)
		);

		$settings->addMappingField(
			new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
				'image',
				\Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_KEYWORD
			)
		);

		$settings->addMappingField(
			new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
				'price',
				\Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_DOUBLE
			)
		);

		$settings->addMappingField(
			new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
				'availability',
				\Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_KEYWORD
			)
		);
		$settings->addMappingField(
			new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
				'tags',
				\Spameri\Elastic\Model\ValidateMapping\AllowedValues::TYPE_KEYWORD
			)
		);

		$settings->addMappingField(
			new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
				'categories',
				\Spameri\Elastic\Model\ValidateMapping\AllowedValues::TYPE_KEYWORD
			)
		);

		$settings->addMappingField(
			new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
				'purpose',
				\Spameri\Elastic\Model\ValidateMapping\AllowedValues::TYPE_KEYWORD
			)
		);

		$settings->addMappingField(
			new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
				'venality',
				\Spameri\Elastic\Model\ValidateMapping\AllowedValues::TYPE_INTEGER
			)
		);

		$settings->addMappingField(
			new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
				'brand',
				\Spameri\Elastic\Model\ValidateMapping\AllowedValues::TYPE_TEXT,
				$czechDictionary,
				TRUE
			)
		);

		return $settings;
	}

}
