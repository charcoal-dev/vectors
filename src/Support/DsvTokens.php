<?php
/**
 * Part of the "charcoal-dev/vectors" package.
 * @link https://github.com/charcoal-dev/vectors
 */

declare(strict_types=1);

namespace Charcoal\Vectors\Support;

use Charcoal\Vectors\Strings\AbstractTokensVector;

/**
 * Represents a specialized collection for handling delimiter-separated value (DSV) tokens.
 * Extends the functionality of AbstractTokensVector to manage token addition and deletion operations.
 */
final class DsvTokens extends AbstractTokensVector
{
    /**
     * @param string ...$tokens
     * @return self
     */
    public function add(string ...$tokens): self
    {
        return $this->addTokens(...$tokens);
    }

    /**
     * @param string $token
     * @return bool
     */
    public function delete(string $token): bool
    {
        return $this->deleteToken($token);
    }
}