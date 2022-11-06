<?php
declare(strict_types=1);

namespace Larsnieuwenhuizen\CiHelpers\Exception;

use Symfony\Component\Console\Exception\ExceptionInterface;

abstract class ExecutionException extends \InvalidArgumentException implements ExceptionInterface
{
}
