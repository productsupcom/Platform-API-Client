<?php
/**
 * Server Exceptions indicate that during runtime an exception at the server happened.
 * This might be a temporary problem at the api server and is most likely not caused by your script.
 *
 * Please read Exception message for further information
 * User: Chris Sachs
 */
namespace Productsup\Exceptions;
class ServerException extends \Exception{}