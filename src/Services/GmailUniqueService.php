<?php

namespace Aliziodev\GmailUnique\Services;

/**
 * Gmail Unique Service
 *
 * A service class for normalizing and validating Gmail addresses to ensure uniqueness.
 * Gmail allows variations of the same address (with dots or aliases) that all route to the same inbox.
 * This service helps prevent duplicate registrations by normalizing these variations.
 *
 * Features:
 * - Normalizes Gmail addresses by removing dots and aliases
 * - Checks for duplicate emails in the database using normalized versions
 * - Supports multiple Gmail domains configured in gmail-unique.php config file
 *
 * @package Aliziodev\GmailUnique
 * @version 1.0.0
 */
class GmailUniqueService
{
    /**
     * List of Gmail domains that should be normalized
     *
     * @var array
     */
    protected array $domains;

    /**
     * Domain lookup cache for faster checking
     *
     * @var array
     */
    protected array $domainLookup = [];

    /**
     * Default email column name used for database queries
     *
     * @var string
     */
    protected string $emailColumn;

    /**
     * Initialize the service with allowed Gmail domains and email column
     *
     * Sets up the service with the domains that should be treated as Gmail addresses
     * and the column name to use for email validation.
     *
     * @param array $domains List of domains to be treated as Gmail addresses
     * @param string|null $emailColumn The column name to use for email validation
     */
    public function __construct(?array $domains = null, ?string $emailColumn = null)
    {
        $this->domains = $domains ?? config('gmail-unique.domains', ['gmail.com', 'googlemail.com']);
        $this->emailColumn = $emailColumn ?? config('gmail-unique.email_column', 'email');
        foreach ($this->domains as $domain) {
            $this->domainLookup[strtolower($domain)] = true;
        }
    }

    /**
     * Normalize the Gmail address by removing dots and aliases
     *
     * Gmail treats the following as identical:
     * - Dots in the local part (john.doe@gmail.com = johndoe@gmail.com)
     * - Anything after a plus sign (john.doe+work@gmail.com = john.doe@gmail.com)
     *
     * This method standardizes these variations to ensure uniqueness checks work correctly.
     *
     * @param string $email The email address to normalize
     * @return string The normalized email address
     * @throws \InvalidArgumentException If the email format is invalid
     */
    public function normalize(string $email): string
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email; // Return as is if not a valid email
        }

        $atPos = strrpos($email, '@');
        if ($atPos === false) {
            return $email; // Return as is if no @ symbol
        }

        $local = substr($email, 0, $atPos);
        $domain = strtolower(substr($email, $atPos + 1));
        $local = strtolower($local);

        if (isset($this->domainLookup[$domain])) {
            $local = preg_replace('/\+.*/', '', $local);
            $local = str_replace('.', '', $local);
        }

        return "$local@$domain";
    }

    /**
     * Check if a normalized version of the email already exists in the database
     *
     * Normalizes the provided email and checks if it exists in the specified model table.
     * Optionally excludes a specific record by ID (useful for update operations).
     *
     * @param string $email The email address to check for duplicates
     * @param string $modelClass The fully qualified class name of the model to check against (e.g., 'App\Models\User')
     * @param int|null $excludeId Optional ID to exclude from the duplicate check (for update scenarios)
     * @return bool True if a duplicate exists, false otherwise
     */
    public function isDuplicate(string $email, string $modelClass, $excludeId = null): bool
    {
        $normalized = $this->normalize($email);

        $query = $modelClass::whereRaw("LOWER({$this->emailColumn}) = ?", [$normalized]);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Get the email column name used for database queries
     *
     * @return string The email column name
     */
    public function getEmailColumn(): string
    {
        return $this->emailColumn;
    }

    /**
     * Get the error message for duplicate emails
     *
     * @return string The error message from configuration
     */
    public function getErrorMessage(): string
    {
        return config('gmail-unique.error_message', 'This email is already taken (normalized Gmail detected).');
    }
}
