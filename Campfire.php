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
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Output\Output;
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
        /**
         * Display the URL where the exception is thrown
         */
        if($request) {
            $body = sprintf(
                "%s: %s\r\n\r\n",
                $this->application,
                $request->getUri()
            );
        } else {
            $body = sprintf(
                "%s\r\n\r\n",
                $this->application
            );
        }

        /**
         * Display the exception
         */
        $class = explode("\\", get_class($exception));
        $body .= sprintf("%s: %s\r\nOn %s:%s\r\n\r\n",
            end($class),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );

        /**
         * Display the previous exceptions
         */
        $previous = $exception;
        while(null !== $previous = $previous->getPrevious()) {
            $class = explode("\\", get_class($previous));
            $body .= sprintf("%s: %s\r\nOn %s:%s\r\n\r\n",
                end($class),
                $previous->getMessage(),
                $previous->getFile(),
                $previous->getLine()
            );
        }

        /**
         * Display the trace
         */
        $body .= $exception->getTraceAsString();

        $this->speak($body, 'PasteMessage');
    }

    /**
     * @param \Symfony\Component\Console\Input\Input    $input
     * @param \Symfony\Component\Console\Output\Output  $output
     * @param \Exception                                $exception
     * @param int                                       $exitCode
     */
    public function notifyOnConsoleException(Input $input, Output $output, Exception $exception, $exitCode)
    {
        $body = sprintf(
            "%s: Command: %s\r\n\r\n",
            $this->application,
            (string) $input
        );

        /**
         * Display the exception
         */
        $class = explode("\\", get_class($exception));
        $body .= sprintf("%s: %s\r\nOn %s:%s\r\n\r\n",
            end($class),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );

        /**
         * Display the previous exceptions
         */
        $previous = $exception;
        while(null !== $previous = $previous->getPrevious()) {
            $class = explode("\\", get_class($previous));
            $body .= sprintf("%s: %s\r\nOn %s:%s\r\n\r\n",
                end($class),
                $previous->getMessage(),
                $previous->getFile(),
                $previous->getLine()
            );
        }

        /**
         * Display the trace
         */
        $body .= $exception->getTraceAsString();

        $this->speak($body, 'PasteMessage');
    }

    /**
     * @param string $body
     * @param string $type
     */
    public function speak($body, $type = 'TextMessage')
    {
        $buzzRequest = new BuzzRequest('POST', '/room/' . $this->room . '/speak.json', 'https://' . $this->subdomain . '.campfirenow.com');
        $buzzRequest->addHeader('Content-type: application/json');
        $buzzRequest->addHeader('Authorization: Basic ' . base64_encode($this->token . ':x'));
        $buzzRequest->setContent(json_encode(array(
            'message' => array(
                'type' => $type,
                'body' => $body
            )
        )));

        $response = new BuzzResponse;

        $client = new BuzzCurl;
        $client->send($buzzRequest, $response);
    }
}
