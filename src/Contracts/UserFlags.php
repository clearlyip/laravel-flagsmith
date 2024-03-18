<?php

namespace Clearlyip\LaravelFlagsmith\Contracts;

use Flagsmith\Flagsmith;
use Flagsmith\Models\Identity;
use Flagsmith\Utils\Collections\FlagModelsList;

interface UserFlags
{
    /**
     * Get Flagsmith instance for this user
     *
     * @return Flagsmith
     */
    public function getFlagsmith(): Flagsmith;

    /**
     * Retrieve the flags associated with the provided Identity.
     *
     * @return FlagModelsList The flags associated with the provided Identity
     */
    public function getFlags(): FlagModelsList;

    /**
     * Skip Cache
     *
     * @param boolean $disable
     * @return static
     */
    public function skipFlagCache(bool $disable = true): static;

    /**
     * Check if Flag is Enabled against this user
     *
     * @param string $name
     * @param boolean $default
     * @return boolean
     */
    public function isFlagEnabled(string $name, bool $default = false): bool;

    /**
     * Get Flag value against this user
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getFlagValue(string $name, $default = null): mixed;

    /**
     * Get the Identity used for the Flagsmith API
     *
     * @return string
     */
    public function getFlagIdentityId(): ?string;

    /**
     * Get the Traits to send to the API for this Identity
     *
     * @return array
     */
    public function getFlagTraits(): array;

    /**
     * Get Identity Class
     *
     * @return Identity
     */
    public function getFlagIdentity(): Identity;
}
