<?php declare(strict_types = 1);

namespace DDD\Domain\Organization;

interface OrganizationRepository
{

	public function getByAddonId(int $id): Organization;

}
