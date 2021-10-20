<?php
namespace Clearlyip\LaravelFlagsmith\Concerns;

use Flagsmith\Flagsmith;
use Flagsmith\Models\Identity;
use Flagsmith\Models\IdentityTrait;
use Illuminate\Support\Facades\App;

trait HasFeatures
{
    private Flagsmith $flagsmith;
    private array $traits = [];

    /**
     * Get Flagsmith instance for this user
     *
     * @return Flagsmith
     */
    public function getFlagsmith(): Flagsmith
    {
        if (!isset($this->flagsmith)) {
            $this->flagsmith = App::make(Flagsmith::class);
        }
        return $this->flagsmith;
    }

    /**
     * Check if features are in cache for this User
     *
     * @return boolean
     */
    public function featuresInCache(): bool
    {
        return $this->getFlagsmith()->hasIdentityInCache(
            $this->getFeatureIdentity()
        );
    }

    /**
     * Skip Cache
     *
     * @param boolean $disable
     * @return self
     */
    public function skipFeatureCache(bool $disable = true): self
    {
        //Since Flagsmith is immutable, we need to replace the instance
        $this->flagsmith = $this->getFlagsmith()->withSkipCache($disable);
        return $this;
    }

    /**
     * Check if Feature is Enabled against this user
     *
     * @param string $name
     * @param boolean $default
     * @return boolean
     */
    public function isFeatureEnabled(string $name, bool $default = false): bool
    {
        return $this->getFlagsmith()->isFeatureEnabledByIdentity(
            $this->getFeatureIdentity(),
            $name,
            $default
        );
    }

    /**
     * Get feature value against this user
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getFeatureValue(string $name, $default = null)
    {
        return $this->getFlagsmith()->getFeatureValueByIdentity(
            $this->getFeatureIdentity(),
            $name,
            $default
        );
    }

    /**
     * Get All Features (Flags) for this user
     *
     * @return array
     */
    public function getFeatures(): array
    {
        return $this->getFlagsmith()->getFlagsByIdentity(
            $this->getFeatureIdentity()
        );
    }

    /**
     * Get the Identity used for the Flagsmith API
     *
     * @return string
     */
    protected function getFeatureIdentityId(): string
    {
        $key = config('flagsmith.identity.identifier');
        return (string) $this->{$key};
    }

    /**
     * Get the Traits to send to the API for this Identity
     *
     * @return array
     */
    protected function getFeatureTraits(): array
    {
        return array_reduce(
            config('flagsmith.identity.traits'),
            function ($carry, $attribute) {
                $carry[$attribute] = $this->{$attribute};
                return $carry;
            },
            []
        );
    }

    /**
     * Get Identity Class
     *
     * @return Identity
     */
    protected function getFeatureIdentity(): Identity
    {
        $identity = new Identity($this->getFeatureIdentityId());
        foreach ($this->getFeatureTraits() as $key => $value) {
            $identity = $identity->withTrait(
                (new IdentityTrait($key))->withValue($value)
            );
        }

        return $identity;
    }
}
