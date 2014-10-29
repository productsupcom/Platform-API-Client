<?php

namespace Productsup;
use Productsup\Exception as Exception;

class Exception extends \Exception
{
    const E_REFERENCE_TO_SITE = 100;
    const E_INVALID_JSON_FOR_OBJECT = 101;
    const E_INVALID_REFERENCE_KEY = 102;
    const E_MISSING_REFERENCE = 103;
    const E_INSERT_REQUEST_FAILED = 104;
    const E_FAILED_TO_CREATE_PROJECT = 105;
    const E_DELETE_REQUEST_FAILED = 106;
    const E_FAILED_TO_DELETE_PROJECT = 107;
    const E_GET_REQUEST_FAILED = 108;
    const E_FAILED_TO_GET_LIST = 109;
    const E_FAILED_TO_CREATE_SITE = 110;
    const E_FAILED_TO_CREATE_TAG = 111;

    public function __construct($code = 0, $userInfo = null) {

        switch ($code) {
            case self::E_REFERENCE_TO_SITE:
                $message = 'References to other sites not allowed';
                break;
            case self::E_INVALID_JSON_FOR_OBJECT:
                $message = 'Invalid JSON for Project Object';
                break;
            case self::E_INVALID_REFERENCE_KEY:
                $message = 'Invalid Reference Key ([a-z0-9_])';
                break;
            case self::E_MISSING_REFERENCE:
                $message = 'Missing Reference';
                break;
            case self::E_INSERT_REQUEST_FAILED:
                $message = 'Insert Request Failed';
                break;
            case self::E_FAILED_TO_CREATE_PROJECT:
                $message = 'Failed to create project';
                break;
            case self::E_DELETE_REQUEST_FAILED:
                $message = 'Delete Request Failed';
                break;
            case self::E_FAILED_TO_DELETE_PROJECT:
                $message = 'Failed to delete project';
                break;
            case self::E_GET_REQUEST_FAILED:
                $message = 'Get Request Failed';
                break;
            case self::E_FAILED_TO_GET_LIST:
                $message = 'Failed to delete project';
                break;
            case self::E_FAILED_TO_CREATE_SITE:
                $message = 'Failed to create site';
                break;
            case self::E_FAILED_TO_CREATE_TAG:
                $message = 'Failed to create tag';
                break;
            default:
                $message = 'Unknown Error';
        }

        parent::__construct($message, $code);
    }
}
