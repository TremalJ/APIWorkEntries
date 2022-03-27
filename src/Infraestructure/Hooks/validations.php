<?php

declare(strict_types=1);

namespace App\Infraestructure\Hooks;

use DateTime;

class validations
{
    /**
     * Check if a given string is a valid UUID.
     *
     * @param string $uuid The string to check
     *
     * @return bool
     */
    public function isValidUuid(string $uuid)
    {
        if (1 !== preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid)) {
            return false;
        }

        return true;
    }

    public function setTimezone(): string
    {
        date_default_timezone_set("Europe/Madrid");
        return date('yy-m-d H:i:s');
    }

}
