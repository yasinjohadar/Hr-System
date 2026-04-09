<?php

declare(strict_types=1);

if (! function_exists('permission_label')) {
    /**
     * Arabic display label for a Spatie permission slug (see lang/ar/permissions.php).
     * Falls back to the raw name when no translation exists.
     */
    function permission_label(string $name): string
    {
        $key = 'permissions.'.$name;
        $translated = __($key, [], 'ar');

        return $translated === $key ? $name : $translated;
    }
}
