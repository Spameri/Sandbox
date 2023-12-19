<?php declare(strict_types = 1);

namespace App\WorkshopFiveModule\Entity;

class Recipe
{

    public const ID = 'id';
    public const TITLE = 'title';
    public const CZECH_DICTIONARY = 'czechDictionary';
    public const CATEGORIES = 'categories';
    public const INGREDIENTS = 'ingredients';
    public const TAGS = 'tags';
    public const CREATED = 'created';
    public const RATING = 'rating';
    public const VIEWS = 'views';
    public const COOK_TIME = 'cookTime';
    public const HAS_IMAGE = 'hasImage';
    public const HAS_VIDEO = 'hasVideo';
    public const HAS_COOKBOOK = 'hasCookbook';
    public const AUTHOR = 'author';

    public function __construct(
        public int $id,
        public string $title,
        public array $categories,
        public array $ingredients,
        public array $tags,
        public \DateTime $created,
        public float $rating,
        public int $views,
        public int $cookTime,
        public bool $hasImage,
        public bool $hasVideo,
        public bool $hasCookbook,
        public string $author,
    ) {}

}