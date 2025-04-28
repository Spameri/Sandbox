<?php declare(strict_types = 1);

namespace App\WorkshopFourModule\Entity;

class ProductConfig implements \App\WorkshopFourModule\Elastic\IndexConfigInterface
{

    public const INDEX = 'product';


    public function __construct(
        private string $index,
    ) {}

    public function provide(): \Spameri\ElasticQuery\Mapping\Settings
    {
        $settings = new \Spameri\ElasticQuery\Mapping\Settings($this->index);

        // Settings
        $lowerCaseAnalyzer = new \Spameri\ElasticQuery\Mapping\Analyzer\Custom\Lowercase();
        $settings->addAnalyzer($lowerCaseAnalyzer);

        $czechDictionary = new \Spameri\ElasticQuery\Mapping\Analyzer\Custom\CzechDictionary();
        $settings->addAnalyzer($czechDictionary);

        $slovakDictionary = new \Spameri\ElasticQuery\Mapping\Analyzer\Custom\SlovakDictionary();
        $settings->addAnalyzer($slovakDictionary);

        $hungarianDictionary = new \Spameri\ElasticQuery\Mapping\Analyzer\Custom\HungarianDictionary();
        $settings->addAnalyzer($hungarianDictionary);

        $comonGrams = new \Spameri\ElasticQuery\Mapping\Analyzer\Custom\CommonGrams([
            'gant',
            'vermont',
        ]);
        $settings->addAnalyzer($comonGrams);

        $wordDelimiter = new \Spameri\ElasticQuery\Mapping\Analyzer\Custom\WordDelimiter();
        $settings->addAnalyzer($wordDelimiter);

        $synonym = new \Spameri\ElasticQuery\Mapping\Analyzer\Custom\Synonym\CzechSynonym(
            synonyms: [
                'gant' => 'gant,vermont',
                'vermont' => 'gant,vermont',
                'kalhoty' => 'rifle,ryfle,gaťe',
            ],
            // OR
//            filePath: '/etc/elastic/config/synonym.txt',
        );
        $settings->addAnalyzer($synonym);

        $edgeNgram = new \Spameri\ElasticQuery\Mapping\Analyzer\Custom\EdgeNgram();
        $settings->addAnalyzer($edgeNgram);

        // Mapping

        $settings->addMappingSubField(
            new \Spameri\ElasticQuery\Mapping\Settings\Mapping\SubFields(
                name: \App\WorkshopFourModule\Entity\Product::TITLE,
                type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_TEXT,
                fields: new \Spameri\ElasticQuery\Mapping\Settings\Mapping\FieldCollection(
                    new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
                        name: 'czechDictionary',
                        type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_TEXT,
                        analyzer: $czechDictionary,
                    ),
                    new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
                        name: 'slovakDictionary',
                        type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_TEXT,
                        analyzer: $slovakDictionary,
                    ),
                    new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
                        name: 'hungarianDictionary',
                        type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_TEXT,
                        analyzer: $hungarianDictionary,
                    ),
                    new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
                        name: 'wordJoin',
                        type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_TEXT,
                        analyzer: $comonGrams,
                    ),
                    new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
                        name: 'wordSplit',
                        type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_TEXT,
                        analyzer: $wordDelimiter,
                    ),
                    new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
                        name: 'synonym',
                        type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_TEXT,
                        analyzer: $synonym,
                    ),
                    new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
                        name: 'edgeNgram',
                        type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_TEXT,
                        analyzer: $edgeNgram,
                    ),
                )
            )
        );

        $settings->addMappingField(
            new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
                name: \App\WorkshopFourModule\Entity\Product::DESCRIPTION,
                type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_TEXT,
                analyzer: $czechDictionary,
            )
        );

        $settings->addMappingField(
            new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
                name: \App\WorkshopFourModule\Entity\Product::PRICE,
                type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_FLOAT,
            )
        );

        $settings->addMappingField(
            new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
                name: \App\WorkshopFourModule\Entity\Product::PRICE_DISCOUNT,
                type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_FLOAT,
            )
        );

        $settings->addMappingField(
            new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
                name: \App\WorkshopFourModule\Entity\Product::IMAGE,
                type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_KEYWORD,
            )
        );

        $settings->addMappingField(
            new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
                name: \App\WorkshopFourModule\Entity\Product::COLOR,
                type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_KEYWORD,
            )
        );

        $settings->addMappingField(
            new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
                name: \App\WorkshopFourModule\Entity\Product::SIZE,
                type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_KEYWORD,
            )
        );

        $settings->addMappingField(
            new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
                name: \App\WorkshopFourModule\Entity\Product::CUT,
                type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_KEYWORD,
            )
        );

        $settings->addMappingField(
            new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
                name: \App\WorkshopFourModule\Entity\Product::MATERIAL,
                type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_KEYWORD,
            )
        );

        $settings->addMappingField(
            new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
                name: \App\WorkshopFourModule\Entity\Product::STICKERS,
                type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_KEYWORD,
            )
        );

        $settings->addMappingField(
            new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
                name: \App\WorkshopFourModule\Entity\Product::CATEGORY,
                type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_TEXT,
                analyzer: $czechDictionary,
                fieldData: true,
            )
        );

        $settings->addMappingField(
            new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
                name: \App\WorkshopFourModule\Entity\Product::PUBLISHED,
                type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_DATE,
            )
        );

        $settings->addMappingField(
            new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
                name: \App\WorkshopFourModule\Entity\Product::STOCK,
                type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_INTEGER,
            )
        );

        return $settings;
    }

}
