<?php

declare(strict_types=1);

namespace Core\Exception;

use Exception;

/**
 * Exception when headers() is called after an output is sent in the Response class
 */
class ResponseHeadersSentException extends Exception
{

}
