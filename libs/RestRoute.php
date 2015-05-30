<?php

namespace App\Api\Routers;

use Nette\Application\Routers\Route;
use Nette\Application;
use Nette\Http\IRequest;
use Nette\Utils\Json;
use Nette;
use Nette\Utils\Strings;



class RestRoute extends Route
{

	const HTTP_HEADER_OVERRIDE = 'X-HTTP-Method-Override';
	const QUERY_PARAM_OVERRIDE = '__method';

	const FLAG_SINGULAR_RESOURCE = 16; // 2^n integer high enough for forward compatibility with Nette\Application\IRouter flags

	/**
	 * @var array
	 */
	private $actionMap = [
		IRequest::POST => 'create',
		IRequest::GET => 'read',
		IRequest::PUT => 'update',
		IRequest::DELETE => 'delete',
		'PATCH' => 'patch',
	];



	/**
	 * @param Nette\Http\Request $httpRequest
	 * @return \Nette\Application\Request|NULL
	 */
	public function match(IRequest $httpRequest)
	{
		$request = parent::match($httpRequest);
		if (!$request) {
			return NULL;
		}

		$params = $request->getParameters();
		$params['action'] = $this->detectAction($httpRequest);

		if (!isset($params['format'])) {
			$params['format'] = $this->detectFormat($httpRequest);
		}

		if (empty($params['id'])) {
			if ($params['action'] === 'read' && !($this->flags & self::FLAG_SINGULAR_RESOURCE)) {
				$params['action'] = 'readAll';
			}
			if ($params['action'] === 'patch' && !($this->flags & self::FLAG_SINGULAR_RESOURCE)) {
				$params['action'] = 'patchAll';
			}
		}

		$request->setParameters($params);

		$post = $httpRequest->getRawBody();
		if (!empty($post) && isset($params['format']) && $params['format'] == 'json') {
			try {
				$request->setPost(Json::decode($post, Json::FORCE_ARRAY));
			} catch (Nette\Utils\JsonException $e) {
				throw new Application\BadRequestException($e->getMessage(), Nette\Http\IResponse::S400_BAD_REQUEST, $e);
			}
		} elseif (!empty($post)) {
			$request->setPost(is_array($post) ? $post : ['post' => $post]);
		}

		return $request;
	}



	public function constructUrl(Application\Request $appRequest, Nette\Http\Url $refUrl)
	{
		$appRequest = clone $appRequest;
		$params = $appRequest->getParameters();
		unset($params['action']);
		$appRequest->setParameters($params);

		return parent::constructUrl($appRequest, $refUrl);
	}



	/**
	 * @param IRequest $httpRequest
	 * @return string
	 * @throws \Nette\InvalidStateException
	 */
	protected function detectAction(IRequest $httpRequest)
	{
		$method = $this->detectMethod($httpRequest);

		return !empty($this->actionMap[$method]) ? $this->actionMap[$method] : $method;
	}



	/**
	 * @param IRequest $request
	 * @return string
	 */
	protected function detectMethod(IRequest $request)
	{
		$method = $request->getQuery(self::QUERY_PARAM_OVERRIDE);
		if (!empty($method)) {
			return $method;
		}

		$method = $request->getHeader(self::HTTP_HEADER_OVERRIDE);
		if (!empty($method)) {
			return $method;
		}

		return $request->getMethod();
	}



	/**
	 * @param IRequest $httpRequest
	 * @return string
	 */
	private function detectFormat(IRequest $httpRequest)
	{
		$contentType = Strings::lower($httpRequest->getHeader('Content-Type'));
		foreach (['application/json', 'application/hal+json'] as $jsonPrefix) {
			if (Strings::startsWith($contentType, $jsonPrefix)) {
				return 'json';
			}
		}

		return 'plain';
	}

}
