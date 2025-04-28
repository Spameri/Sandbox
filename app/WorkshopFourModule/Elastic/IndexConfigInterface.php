<?php declare(strict_types = 1);

namespace App\WorkshopFourModule\Elastic;

interface IndexConfigInterface
{

    public function provide(): \Spameri\ElasticQuery\Mapping\Settings;

}
