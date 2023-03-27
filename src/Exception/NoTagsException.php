<?php
declare(strict_types=1);

namespace Larsnieuwenhuizen\CiHelpers\Exception;

class NoTagsException extends ExecutionException
{

    protected $message = "No tags found in this repo";
}
