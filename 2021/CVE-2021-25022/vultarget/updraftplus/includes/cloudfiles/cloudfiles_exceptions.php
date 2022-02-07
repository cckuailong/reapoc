<?php
/**
 * Custom Exceptions for the CloudFiles API
 *
 * Requres PHP 5.x (for Exceptions and OO syntax)
 *
 * See COPYING for license information.
 *
 * @author Eric "EJ" Johnson <ej@racklabs.com>
 * @copyright Copyright (c) 2008, Rackspace US, Inc.
 * @package php-cloudfiles-exceptions
 */

/**
 * Custom Exceptions for the CloudFiles API
 * @package php-cloudfiles-exceptions
 */
if (!class_exists('SyntaxException')) {
class SyntaxException extends Exception { }
}

if (!class_exists('AuthenticationException')) {
class AuthenticationException extends Exception { }
}

if (!class_exists('InvalidResponseException')) {
class InvalidResponseException extends Exception { }
}

if (!class_exists('NonEmptyContainerException')) {
class NonEmptyContainerException extends Exception { }
}

if (!class_exists('NoSuchObjectException')) {
class NoSuchObjectException extends Exception { }
}

if (!class_exists('NoSuchContainerException')) {
class NoSuchContainerException extends Exception { }
}

if (!class_exists('NoSuchAccountException')) {
class NoSuchAccountException extends Exception { }
}

if (!class_exists('MisMatchedChecksumException')) {
class MisMatchedChecksumException extends Exception { }
}

if (!class_exists('IOException')) {
class IOException extends Exception { }
}

if (!class_exists('CDNNotEnabledException')) {
class CDNNotEnabledException extends Exception { }
}

if (!class_exists('BadContentTypeException')) {
class BadContentTypeException extends Exception { }
}

if (!class_exists('InvalidUTF8Exception')) {
class InvalidUTF8Exception extends Exception { }
}

if (!class_exists('ConnectionNotOpenException')) {
class ConnectionNotOpenException extends Exception { }
}

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */
?>
