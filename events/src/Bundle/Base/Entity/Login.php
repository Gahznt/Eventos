<?php

namespace App\Bundle\Base\Entity;

/**
 * Class LoginForm
 * @package App\Bundle\Base\Entity
 */
class Login
{

    private $identifier;

    private $password;

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param null|string $identifier
     * @return Login
     */
    public function setIdentifier(?string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param null|string $password
     * @return Login
     */
    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function __toString(): string
    {
        return $this->identifier;
    }
}
