<?php
/**
 * Part of the "charcoal-dev/vectors" package.
 * @link https://github.com/charcoal-dev/vectors
 */

declare(strict_types=1);

namespace Charcoal\Vectors\Strings;

use Charcoal\Contracts\Vectors\StringVectorInterface;
use Charcoal\Vectors\Strings\Traits\StringVectorTrait;

/**
 * Abstract class representing a vector of tokens with various utility methods
 * for token manipulation and normalization.
 */
abstract class AbstractTokensVector implements StringVectorInterface
{
    use StringVectorTrait;

    public function __construct(
        public readonly bool $changeCase = true,
        public readonly bool $uniqueTokensOnly = true,
    )
    {
        $this->strings = [];
    }

    /**
     * @param string ...$values
     * @return $this
     */
    final protected function addTokens(string ...$values): static
    {
        $added = 0;
        foreach ($values as $value) {
            $normalized = $this->normalizeStringValue($value);
            if ($normalized) {
                $this->strings[] = $normalized;
                $added++;
            }
        }

        return $this->uniqueTokensOnly && $added > 0 ?
            $this->filterUnique() : $this;
    }

    /**
     * @param string $token
     * @return bool
     */
    final protected function has(string $token): bool
    {
        $token = trim($token);
        if ($token === "") {
            return false;
        }

        if ($this->changeCase) {
            return in_array($this->toLowerCase($token), $this->strings, true);
        }

        $token = $this->toLowerCase($token);
        foreach ($this->strings as $value) {
            if ($this->toLowerCase($value) === $token) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $token
     * @return bool
     */
    final protected function deleteToken(string $token): bool
    {
        $token = trim($token);
        if ($token === "") {
            return false;
        }

        $deleted = false;
        $token = $this->changeCase ? $this->toLowerCase($token) : $token;
        foreach ($this->strings as $index => $value) {
            $value = $this->changeCase ? $this->toLowerCase($value) : $value;
            if ($value === $token) {
                unset($this->strings[$index]);
                $deleted = true;
            }
        }

        if ($deleted) {
            $this->strings = array_values($this->strings);
        }

        return $deleted;
    }

    /**
     * @return $this
     */
    final protected function filterUnique(): static
    {
        if ($this->changeCase) {
            $this->strings = array_values(array_unique($this->strings, SORT_STRING));
            return $this;
        }

        $seen = [];
        $result = [];
        foreach ($this->strings as $value) {
            $lowercase = $this->toLowerCase($value);
            if (!isset($seen[$lowercase])) {
                $seen[$lowercase] = true;
                $result[] = $value;
            }
        }

        $this->strings = $result;
        return $this;
    }

    /**
     * @param string $value
     * @return string|null
     */
    final protected function normalizeStringValue(string $value): ?string
    {
        $value = trim($value);
        if ($value === "") {
            return null;
        }

        return $this->changeCase ? $this->toLowerCase($value) : $value;
    }

    /**
     * @param string $value
     * @return string
     */
    protected function toLowerCase(string $value): string
    {
        return strtolower($value);
    }
}