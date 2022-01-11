<?php declare(strict_types = 1);

namespace DDD\Infrastructure\Addon;

class AddonByTier implements \DDD\Domain\Organization\AddonRepository
{

	private \DDD\Domain\Organization\OrganizationRepository $organizationRepository;


	public function __construct(
		\DDD\Domain\Organization\OrganizationRepository $organizationRepository
	)
	{
		$this->organizationRepository = $organizationRepository;
	}


	public function availableAddonByTier(int $tier): \DDD\Domain\Addon\Addon
	{
		$data = $this->nodeMagicApi->fetchIdbyTier($tier);

		$organization = $this->organizationRepository->getByAddonId($data['id']);

		return new \DDD\Domain\Addon\Addon(
			$data['id'],
			$organization
		);
	}

}
