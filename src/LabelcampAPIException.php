<?php

declare(strict_types=1);

namespace LabelcampAPI;

class LabelcampAPIException extends \Exception {

    public const TOKEN_EXPIRED = 'The access token expired';

    /**
     * Returns whether the exception was thrown because of an expired access token.
     *
     * @return bool
     */
    public function hasExpiredToken(): bool {
        return $this->getMessage() === self::TOKEN_EXPIRED;
    }
}
