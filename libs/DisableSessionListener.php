<?php

namespace App\Api;

use Kdyby;
use Kdyby\Events\Subscriber;
use Nette;
use Nette\Application as NApp;
use Tracy;



/**
 * Listens on incoming requests and disables session on several modules.
 */
class DisableSessionListener extends Nette\Object implements Subscriber
{

	/**
	 * @var \Nette\Http\Session|Kdyby\FakeSession\Session
	 */
	private $session;



	/**
	 * @param Nette\Http\Session $session
	 */
	public function __construct(Nette\Http\Session $session)
	{
		$this->session = $session;
	}



	public function getSubscribedEvents()
	{
		return [
			'Nette\\Application\\Application::onRequest' => ['onRequest', 1000]
		];
	}



	/**
	 * @param NApp\Application $sender
	 * @param NApp\Request $request
	 */
	public function onRequest(NApp\Application $sender, NApp\Request $request)
	{
		$module = substr($request->getPresenterName(), 0, strpos($request->getPresenterName(), ':'));
		if (in_array($module, ['Api'], TRUE)) {
			try {
				$this->session->disableNative();
			} catch (\Exception $e) {
				Tracy\Debugger::log($e->getMessage(), 'error.session-storage');
			}
		}
	}

}
