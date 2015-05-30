<?php

namespace App\Api;

use Kdyby\Events\Subscriber;
use Nette\Application\Application;
use Nette\Application\Request;
use Tracy\Debugger;
use Nette\Http\Request as HttpRequest;
use Nette\Http\Response as HttpResponse;
use Nette\Object;
use Nette\Utils\Strings;



class ErrorPresenterListener extends Object implements Subscriber
{

	/**
	 * @var \Nette\Http\Request
	 */
	private $httpRequest;

	/**
	 * @var \Nette\Http\Response
	 */
	private $httpResponse;



	public function __construct(HttpRequest $httpRequest, HttpResponse $httpResponse)
	{
		$this->httpRequest = $httpRequest;
		$this->httpResponse = $httpResponse;
	}



	/**
	 * Returns an array of events this subscriber wants to listen to.
	 *
	 * @return array
	 */
	public function getSubscribedEvents()
	{
		return [
			'Nette\Application\Application::onStartup' => 'appStartup',
			'Nette\Application\Application::onRequest' => 'appRequest',
		];
	}



	public function appStartup(Application $app)
	{
		$url = $this->httpRequest->getUrl();

		if (!Strings::startsWith($url->path, '/api/')) {
			return; // ignore
		}

		Debugger::$productionMode = TRUE; // enforce
		$app->catchExceptions = TRUE; // always
		$app->errorPresenter = 'Api:Error';
	}



	public function appRequest(Application $app, Request $request)
	{
		if (!Strings::startsWith($request->getPresenterName(), 'Api:')) {
			return; // ignore
		}

		Debugger::$productionMode = TRUE; // enforce
		$app->catchExceptions = TRUE; // always
		$app->errorPresenter = 'Api:Error';
	}

}
