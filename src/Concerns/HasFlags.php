<?php

namespace Clearlyip\LaravelFlagsmith\Concerns;

use Flagsmith\Flagsmith;
use Flagsmith\Models\Identity;
use Flagsmith\Models\IdentityTrait;
use Flagsmith\Utils\Collections\FlagModelsList;
use Illuminate\Support\Facades\App;

trait HasFlags
{
    private Flagsmith $flagsmith;
    private array $traits = [];

    /**
     * {@inheritDoc}
     */
    public function getFlagsmith(): Flagsmith
    {
        if (!isset($this->flagsmith)) {
            $this->flagsmith = App::make(Flagsmith::class);
        }
        return $this->flagsmith;
    }

    /**
     * {@inheritDoc}
     */
    public function getFlags(): FlagModelsList
    {
        $identity = $this->getFlagIdentity();
        return $this->getFlagsmith()
            ->getIdentityFlags(
                $identity->getId(),
                (object) $identity->getTraits(),
            )
            ->getFlags();
    }

    /**
     * {@inheritDoc}
     */
    public function skipFlagCache(bool $disable = true): static
    {
        //Since Flagsmith is immutable, we need to replace the instance
        $this->flagsmith = $this->getFlagsmith()->withSkipCache($disable);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function isFlagEnabled(string $name, bool $default = false): bool
    {
        $flags = $this->getFlags();
        if (!isset($flags[$name])) {
            return $default;
        }
        $flag = $flags[$name];
        if (!($flag instanceof \Flagsmith\Models\Flag)) {
            return $default;
        }
        return $flag->getEnabled();
    }

    /**
     * {@inheritDoc}
     */
    public function getFlagValue(string $name, $default = null): mixed
    {
        $flags = $this->getFlags();
        if (!isset($flags[$name])) {
            return $default;
        }
        $flag = $flags[$name];
        if (!($flag instanceof \Flagsmith\Models\Flag)) {
            return $default;
        }
        return $flag->getValue();
    }

    /**
     * {@inheritDoc}
     */
    public function getFlagIdentityId(): string
    {
        $key = config('flagsmith.identity.identifier');
        return $this->getRawOriginal($key);
    }

    /**
     * Get the Traits to send to the API for this Identity
     *
     * @return array
     */
    public function getFlagTraits(): array
    {
        return array_reduce(
            config('flagsmith.identity.traits'),
            function ($carry, $attribute) {
                $carry[$attribute] = $this->getRawOriginal($attribute);
                return $carry;
            },
            [],
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getFlagIdentity(): Identity
    {
        $identity = new Identity($this->getFlagIdentityId());
        foreach ($this->getFlagTraits() as $key => $value) {
            $identity = $identity->withTrait(
                (new IdentityTrait($key))->withValue($value),
            );
        }

        return $identity;
    }
}
