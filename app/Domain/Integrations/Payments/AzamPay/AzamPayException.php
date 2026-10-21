<?php

namespace App\Domain\Integrations\Payments\AzamPay;

use RuntimeException;

/** AzamPay could not be reached or refused our credentials. Never carries a secret. */
class AzamPayException extends RuntimeException {}
