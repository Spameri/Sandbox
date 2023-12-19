<?php declare(strict_types = 1);

namespace App\WorkshopFiveModule\Entity;

class RecipeSettings
{

    public function __construct(
        public string $index,
    ) {}

    public function provide(): \Spameri\ElasticQuery\Mapping\Settings
    {
        $settings = new \Spameri\ElasticQuery\Mapping\Settings(
            indexName: $this->index,
        );

        // Analyzers
        $lowerCaseAnalyzer = new \Spameri\ElasticQuery\Mapping\Analyzer\Custom\Lowercase();
        $settings->addAnalyzer($lowerCaseAnalyzer);

        $czechDictionary = new \Spameri\ElasticQuery\Mapping\Analyzer\Custom\CzechDictionary(
            stopFilter: new \Spameri\ElasticQuery\Mapping\Filter\Stop\Czech(
                extraWords: [
                    \Spameri\ElasticQuery\Mapping\Analyzer\Stop\StopWords::ENGLISH,
                    'toprecepty',
                ]
            ),
        );
        $settings->addAnalyzer($czechDictionary);

        $commonGrams = new \Spameri\ElasticQuery\Mapping\Analyzer\Custom\CommonGrams(
            commonGramWords: [
                'lžíce',
                'ml',
                'g',
                'kg',
                'dcl',
                'l',
                'hrnek',
                'hrnky',
                'hrst',
            ],
        );
        $settings->addAnalyzer($commonGrams);

        $wordDelimiter = new \Spameri\ElasticQuery\Mapping\Analyzer\Custom\WordDelimiter();
        $settings->addAnalyzer($wordDelimiter);

        $synonymAnalyzer = new \Spameri\ElasticQuery\Mapping\Analyzer\Custom\Synonym\CzechSynonym(
            synonyms: [
                "houby" => "hřiby, žampiony",
                "cukr, cukr krupice, cukr mletý" => "sladké"
            ]
        );
        $settings->addAnalyzer($synonymAnalyzer);

        $edgeNgram = new \Spameri\ElasticQuery\Mapping\Analyzer\Custom\EdgeNgram(
            minGram: 2,
            maxGram: 6,
        );
        $settings->addAnalyzer($edgeNgram);

        // Mapping

        $settings->addMappingSubField(
            new \Spameri\ElasticQuery\Mapping\Settings\Mapping\SubFields(
                name: \App\WorkshopFiveModule\Entity\Recipe::TITLE,
                type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_TEXT,
                fields: new \Spameri\ElasticQuery\Mapping\Settings\Mapping\FieldCollection(
                    new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
                        name: \App\WorkshopFiveModule\Entity\Recipe::CZECH_DICTIONARY,
                        type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_TEXT,
                        analyzer: $czechDictionary,
                    ),
                    new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
                        name: 'commonGrams',
                        type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_TEXT,
                        analyzer: $commonGrams,
                    ),
                    new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
                        name: 'wordDelimiter',
                        type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_TEXT,
                        analyzer: $wordDelimiter,
                    ),
                    new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
                        name: 'synonym',
                        type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_TEXT,
                        analyzer: $synonymAnalyzer,
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
                name: \App\WorkshopFiveModule\Entity\Recipe::CATEGORIES,
                type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_TEXT,
                analyzer: $lowerCaseAnalyzer,
                fieldData: true,
            )
        );

        $settings->addMappingField(
            new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
                name: \App\WorkshopFiveModule\Entity\Recipe::INGREDIENTS,
                type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_TEXT,
                analyzer: $lowerCaseAnalyzer,
                fieldData: true,
            )
        );

        $settings->addMappingField(
            new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
                name: \App\WorkshopFiveModule\Entity\Recipe::TAGS,
                type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_TEXT,
                analyzer: $lowerCaseAnalyzer,
                fieldData: true,
            )
        );

        $settings->addMappingField(
            new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
                name: \App\WorkshopFiveModule\Entity\Recipe::CREATED,
                type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_DATE,
            )
        );

        $settings->addMappingField(
            new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
                name: \App\WorkshopFiveModule\Entity\Recipe::RATING,
                type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_FLOAT,
            )
        );

        $settings->addMappingField(
            new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
                name: \App\WorkshopFiveModule\Entity\Recipe::VIEWS,
                type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_INTEGER,
            )
        );

        $settings->addMappingField(
            new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
                name: \App\WorkshopFiveModule\Entity\Recipe::COOK_TIME,
                type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_INTEGER,
            )
        );

        $settings->addMappingField(
            new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
                name: \App\WorkshopFiveModule\Entity\Recipe::HAS_IMAGE,
                type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_BOOLEAN,
            )
        );

        $settings->addMappingField(
            new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
                name: \App\WorkshopFiveModule\Entity\Recipe::HAS_VIDEO,
                type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_BOOLEAN,
            )
        );

        $settings->addMappingField(
            new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
                name: \App\WorkshopFiveModule\Entity\Recipe::HAS_COOKBOOK,
                type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_BOOLEAN,
            )
        );

        $settings->addMappingField(
            new \Spameri\ElasticQuery\Mapping\Settings\Mapping\Field(
                name: \App\WorkshopFiveModule\Entity\Recipe::AUTHOR,
                type: \Spameri\ElasticQuery\Mapping\AllowedValues::TYPE_TEXT,
                analyzer: $lowerCaseAnalyzer,
            )
        );

        return $settings;
    }

}