<?php

namespace Aliziodev\GmailUnique\Facades;

use Illuminate\Support\Facades\Facade;
use Aliziodev\GmailUnique\Services\GmailUniqueService;

/**
 * Facade for GmailUniqueService.
 *
 * This class provides static access to GmailUniqueService functionality,
 * allowing easy normalization and validation of Gmail addresses.
 *
 * Usage examples:
 * - GmailUnique::normalize('john.doe@gmail.com')
 * - GmailUnique::isDuplicate('john.doe+alias@gmail.com', User::class)
 *
 * @method static string normalize(string $email)
 * @method static bool isDuplicate(string $email, string $modelClass, $excludeId = null)
 *
 * @package Aliziodev\GmailUnique\Facades
 * @version 1.0.0
 */
class GmailUnique extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * This method returns the binding key for the GmailUniqueService
     * in the Laravel service container.
     *
     * @return string The fully qualified class name of the service
     */
    protected static function getFacadeAccessor(): string
    {
        return GmailUniqueService::class;
    }
}
