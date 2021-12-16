<?php

namespace App\Presenter;


final class AddonPresenter extends \App\Presenter\BasePresenter
{

	private \DDD\Domain\Organization\AddonRepository $addonRepository;


	public function __construct(
		\DDD\Domain\Organization\AddonRepository $addonRepository
	)
	{
		$this->addonRepository = $addonRepository;
	}


	public function renderDefault($tier)
	{
		$this->template->addon = $this->addonRepository->availableAddonByTier($tier);
	}
	
}
