<?php declare(strict_types = 1);

namespace App\WorkshopFourModule\Entity;

class Product
{

    public const ID = 'id';
    public const TITLE = 'title';
    public const DESCRIPTION = 'description';
    public const PRICE = 'price';
    public const PRICE_DISCOUNT = 'priceDiscount';
    public const IMAGE = 'image';
    public const COLOR = 'color';
    public const SIZE = 'size';
    public const CUT = 'cut';
    public const MATERIAL = 'material';
    public const STICKERS = 'stickers';
    public const CATEGORY = 'category';
    public const PUBLISHED = 'published';
    public const STOCK = 'stock';


    public function __construct(
        public \Spameri\Elastic\Entity\Property\ElasticIdInterface $id,
        public string $title,
        public string $description,
        public float $price,
        public float $priceDiscount,
        public string $image,
        public string $color,
        public array $size,
        public string $cut,
        public array $material,
        public array $stickers,
        public string $category,
        public \DateTime $published,
        public int $stock,
    ) {}


    public function entityVariables(): array
    {
        $vars = \get_object_vars($this);
        $vars[self::ID] = $this->id->value();
        $vars[self::PUBLISHED] = $this->published->format(
            \Spameri\Elastic\Entity\Property\DateTime::FORMAT
        );

        return $vars;
    }

}
