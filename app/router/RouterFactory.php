<?php

namespace App;


final class RouterFactory
{
	use \Nette\StaticClass;

	public static function createRouter(): \Nette\Routing\Router
	{
		$router = new \Nette\Application\Routers\RouteList;
		$router[] = new \Nette\Application\Routers\Route('<presenter>/<action>', 'Homepage:default');
		$router[] = new \Nette\Application\Routers\Route('<module>/<presenter>/<action>', 'Product:ProductList:default');
		return $router;
	}

}
