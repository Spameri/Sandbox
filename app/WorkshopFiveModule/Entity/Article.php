<?php declare(strict_types = 1);

namespace App\WorkshopFiveModule\Entity;

class Article
{

    public const ID = 'id';
    public const TITLE = 'title';
    public const PEREX = 'perex';
    public const DESCRIPTION = 'description';
    public const CREATED = 'created';
    public const CATEGORIES = 'categories';
    public const HAS_IMAGE = 'hasImage';
    public const HAS_VIDEO = 'hasVideo';
    public const TAGS = 'tags';
    public const AUTHOR = 'author';

    public function __construct(
        public int $id,
        public string $title,
        public string $perex,
        public string $description,
        public \DateTime $created,
        public array $categories,
        public bool $hasImage,
        public bool $hasVideo,
        public array $tags,
        public string $author,
    ) {}
}