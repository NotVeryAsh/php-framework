<?php

namespace Core\Api;

use Core\Exception\ResponseHeadersSentException;

/**
 *  Response class
 */
class Response
{
    private bool $usingJsonMethod = false;
    private bool $outputSent = false;

    /**
     * Constructor for a json response
     * Allows you to output data
     *
     * @param int $responseCode
     * @param string|null $output
     * @param string $contentType
     * @param array|null $headers
     */
    public function __construct(
        string $output = null,
        int $responseCode = 200,
        string $contentType = 'application/json',
        array $headers = null
    ) {
        http_response_code($responseCode);
        header("Content-Type: $contentType");

        foreach ($headers as $header) {
            header($header);
        }

        if ($output) {
            $this->outputSent = true;
            echo $output;
        }
    }

    /**
     * Send a json encoding response from the provided data
     *
     * @param mixed $data
     */
    public function json(mixed $data)
    {
        $this->usingJsonMethod = true;

        echo json_encode($data);
    }

    /**
     * Set headers for the response
     * Cannot be called after calling json() or providing an output in the Response() constructor
     *
     * @param array|null $headers
     * @return Response
     * @throws ResponseHeadersSentException
     */
    public function headers(array $headers = null): static
    {
        $this->checkOutputNotSent();

        foreach ($headers as $header) {
            header($header);
        }

        return $this;
    }

    /**
     * Set response code header for the response
     * Cannot be called after calling json() or providing an output in the Response() constructor
     *
     * @param int $responseCode
     * @return Response
     * @throws ResponseHeadersSentException
     */
    public function responseCode(int $responseCode): static
    {
        $this->checkOutputNotSent();

        http_response_code($responseCode);

        return $this;
    }

    /**
     * Set content type header for the response
     * Cannot be called after calling json() or providing an output in the Response() constructor
     *
     * @param string $contentType
     * @return Response
     * @throws ResponseHeadersSentException
     */
    public function contentType(string $contentType): static
    {
        return $this->headers(["Content-Type: $contentType"]);
    }

    /**
     * Check if the output has been sent before attempting to set headers
     *
     * @throws ResponseHeadersSentException
     */
    private function checkOutputNotSent()
    {
        if ($this->outputSent === true) {
            throw new ResponseHeadersSentException(
                'You cannot call headers() after passing an output into Response()', 500
            );
        }

        if ($this->usingJsonMethod === true) {
            throw new ResponseHeadersSentException(
                'You cannot call headers() after returning a json response with json()', 500
            );
        }
    }
}
