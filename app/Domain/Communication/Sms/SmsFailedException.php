<?php

namespace App\Domain\Communication\Sms;

use RuntimeException;

/** A temporary failure (provider unreachable or erroring): the send is worth trying again. */
class SmsFailedException extends RuntimeException {}
