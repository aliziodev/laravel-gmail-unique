<?php

namespace Aliziodev\GmailUnique\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Aliziodev\GmailUnique\Services\GmailUniqueService;

/**
 * HasNormalizedEmail Trait
 *
 * A trait for Eloquent models that automatically normalizes and validates Gmail email addresses
 * to prevent duplicate registrations with variations of the same Gmail address.
 *
 * When applied to a model with an 'email' attribute, this trait will:
 * - Normalize Gmail addresses by removing dots and aliases during model creation/updating
 * - Validate that the normalized email is unique in the database
 * - Throw a ValidationException if a duplicate normalized email is detected
 *
 * @package Aliziodev\GmailUnique
 * @version 1.0.0
 */
trait HasNormalizedEmail
{
   /**
     * Boot the trait
     * 
     * Registers model event listeners for the saving event
     * to automatically validate email uniqueness with normalization.
     *
     * @return void
     */
    public static function bootHasNormalizedEmail()
    {
        static::saving(function (Model $model) {
            return static::handleEmailValidation($model);
        });
    }

    /**
     * Handle the email uniqueness validation
     *
     * This method is called during model saving to:
     * 1. Skip validation if the model has no email attribute
     * 2. Check if the normalized email already exists in the database
     * 3. Throw a ValidationException if a duplicate is found
     * 4. Preserve the original email format as entered by the user
     *
     * @param Model $model The model being saved
     * @return bool Returns true if validation passes
     * @throws ValidationException If a duplicate normalized email is found
     */
    protected static function handleEmailValidation(Model $model): bool
    {
        $normalizer = app(GmailUniqueService::class);

        $emailColumn = $normalizer->getEmailColumn();

        if (!isset($model->$emailColumn)) return true;

        $exists = $normalizer->isDuplicate($model->$emailColumn, $model::class, $model->id ?? null);

        if ($exists) {
            throw ValidationException::withMessages([
                $emailColumn => $normalizer->getErrorMessage()
            ]);
        }

        return true;
    }
}
