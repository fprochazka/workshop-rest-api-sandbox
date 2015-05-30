<?php

/**
 * This file is part of the Kdyby (http://www.kdyby.org)
 * Copyright (c) 2008 Filip Procházka (filip@prochazka.su)
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace App\ApiModule\Presenters;

use Nette;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Tracy\Debugger;



/**
 * @author Filip Procházka <filip@prochazka.su>
 */
abstract class BasePresenter extends \App\BasePresenter
{

	const HTTP_HEADER_ALLOW = "Allow";
	const HTTP_HEADER_AUTHENTICATION_SIMPLE = "X-Authentication-Simple";

	/**
	 * @var Nette\Application\Application
	 * @inject
	 */
	public $application;

	/**
	 * @var string[]
	 */
	private static $actionMap = [
		'read' => IRequest::GET,
		'readAll' => IRequest::GET,
		'create' => IRequest::POST,
		'update' => IRequest::PUT,
		'delete' => IRequest::DELETE,
	];



	protected function startup()
	{
		Debugger::$productionMode = TRUE;
		$this->application->errorPresenter = 'Api:Error';
		$this->application->catchExceptions = TRUE;
		$this->autoCanonicalize = FALSE;

		parent::startup();

		if (!$this->isMethodAllowed($this->getAction())) {
			$this->getHttpResponse()->addHeader(self::HTTP_HEADER_ALLOW, implode(", ", $this->getAllowedMethods()));
			$this->error("Method '{$this->getAction()}' not allowed", IResponse::S405_METHOD_NOT_ALLOWED);
		}
	}



	/**
	 * Returns TRUE if given action is supported by current presenter.
	 *
	 * @param string $action
	 * @return bool
	 */
	private function isMethodAllowed($action)
	{
		return $this->reflection->hasMethod($this->formatActionMethod($action))
		|| $this->reflection->hasMethod($this->formatRenderMethod($action));
	}



	/**
	 * Returns array of allowed methods by current presenter.
	 *
	 * @return string[]
	 */
	private function getAllowedMethods()
	{
		$allowedMethods = [];
		foreach (self::$actionMap as $action => $method) {
			if ($this->isMethodAllowed($action)) {
				$allowedMethods[] = $method;
			}
		}

		return array_unique($allowedMethods);
	}

}
