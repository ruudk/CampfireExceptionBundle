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
     * @var string
     */
    protected $application;

    /**
     * @param string $subdomain
     * @param string $token
     * @param string $room
     * @param string $application
     */
    public function __construct($subdomain, $token, $room, $application)
    {
        $this->subdomain = $subdomain;
        $this->token = $token;
        $this->room = $room;
        $this->application = $application;
    }

    /**
     * @param \Exception $exception
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function notifyOnException(Exception $exception, Request $request = null)
    {
        $namespace = explode("\\", get_class($exception));
        $class = array_pop($namespace);

        $body = '%s on %s' . PHP_EOL;
        $body .= '%s' . PHP_EOL;
        $body .= 'On %s:%s' . PHP_EOL;
        $body .= '%s' . PHP_EOL;

        $message = array('message' => array(
            'type' => 'PasteMessage',
            'body' => trim(sprintf($body,
                $class,
                $this->application,
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine(),
                $request ? $request->getUri() : null
            ))
        ));

        $buzzRequest = new BuzzRequest('POST', '/room/' . $this->room . '/speak.json', 'https://' . $this->subdomain . '.campfirenow.com');
        $buzzRequest->addHeader('Content-type: application/json');
        $buzzRequest->addHeader('Authorization: Basic ' . base64_encode($this->token . ':x'));
        $buzzRequest->setContent(json_encode($message));

        $response = new BuzzResponse;

        $client = new BuzzCurl;
        $client->send($buzzRequest, $response);
    }
}
