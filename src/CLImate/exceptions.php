<?php
/**
 * Copyright 2012 Martin Srank (http://smasty.net)
 */

namespace CLImate;


/**
 * General CLImate exception.
 */
class CLImateException extends \Exception {}


/**
 * Exception thrown when an error occures on input.
 */
class InputException extends CLImateException {}


/**
 * CLImate\Application-specific exception.
 */
class ApplicationException extends CLImateException {}