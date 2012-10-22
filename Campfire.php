<?php

/*
 * This file is part of the RuudkCampfireExceptionBundle package.
 *
 * (c) Ruud Kamphuis <ruudk@mphuis.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ruudk\CampfireExceptionBundle;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Buzz\Message\Request AS BuzzRequest;
use Buzz\Message\Response AS BuzzResponse;
use Buzz\Client\Curl AS BuzzCurl;

class Campfire
{
    /**
     * @var string
     */
    protected $subdomain;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $room;

    /**
     * @param string $subdomain
     * @param string $token
     * @param string $room
     */
    public function __construct($subdomain, $token, $room)
    {
        $this->subdomain = $subdomain;
        $this->token = $token;
        $this->room = $room;
    }

    /**
     * @param \Exception $exception
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function notifyOnException(Exception $exception, Request $request)
    {
        $namespace = explode("\\", get_class($exception));
        $class = array_pop($namespace);

        $body = '%s on %s' . PHP_EOL;
        $body .= '%s' . PHP_EOL;
        $body .= 'On %s:%s' . PHP_EOL;
        $body .= '%s' . PHP_EOL;

        $message = array('message' => array(
            'type' => 'TextMessage',
            'body' => trim(sprintf($body,
                $class,
                $request->getHost(),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine(),
                $request->getUri()
            ))
        ));

        $request = new BuzzRequest('POST', '/room/' . $this->room . '/speak.json', 'https://' . $this->subdomain . '.campfirenow.com');
        $request->addHeader('Content-type: application/json');
        $request->addHeader('Authorization: Basic ' . base64_encode($this->token . ':x'));
        $request->setContent(json_encode($message));

        $response = new BuzzResponse;

        $client = new BuzzCurl;
        $client->send($request, $response);
    }
}