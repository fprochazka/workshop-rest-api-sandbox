<?php

/**
 * This file is part of the Kdyby (http://www.kdyby.org)
 * Copyright (c) 2008 Filip Procházka (filip@prochazka.su)
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace App\ApiModule\Presenters;

use Nette;
use Tracy\Debugger;



/**
 * @author Filip Procházka <filip@prochazka.su>
 */
abstract class BasePresenter extends \App\BasePresenter
{

	/**
	 * @persistent
	 */
	public $format = 'json';

	/**
	 * @var Nette\Application\Application
	 * @inject
	 */
	public $application;



	protected function startup()
	{
		Debugger::$productionMode = TRUE;
		$this->application->errorPresenter = 'Api:Error';
		$this->application->catchExceptions = TRUE;
		parent::startup();
	}

}
