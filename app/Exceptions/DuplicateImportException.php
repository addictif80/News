<?php

namespace App\Exceptions;

use App\Models\Article;
use RuntimeException;

class DuplicateImportException extends RuntimeException
{
    public function __construct(public readonly Article $existingArticle, string $message)
    {
        parent::__construct($message);
    }
}
