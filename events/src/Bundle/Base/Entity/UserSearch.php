<?php

namespace App\Bundle\Base\Entity;

/**
 * Class UserSearch
 * @package App\Bundle\Base\Entity
 */
class UserSearch
{

    private $search;

    private $since;

    private $thru;

    private $levels;

    private $payment;

    private $paymentDays;

    /**
     * @return mixed
     */
    public function getSince()
    {
        return $this->since;
    }

    /**
     * @param mixed $since
     */
    public function setSince($since): void
    {
        $this->since = $since;
    }

    /**
     * @return mixed
     */
    public function getThru()
    {
        return $this->thru;
    }

    /**
     * @return mixed
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @param mixed $search
     */
    public function setSearch($search): void
    {
        $this->search = $search;
    }

    /**
     * @param mixed $thru
     */
    public function setThru($thru): void
    {
        $this->thru = $thru;
    }

    /**
     * @return mixed
     */
    public function getLevels()
    {
        return $this->levels;
    }

    /**
     * @param mixed $levels
     */
    public function setLevels($levels): void
    {
        $this->levels = $levels;
    }

    /**
     * @return mixed
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @param mixed $payment
     */
    public function setPayment($payment): void
    {
        $this->payment = $payment;
    }

    /**
     * @return mixed
     */
    public function getPaymentDays()
    {
        return $this->paymentDays;
    }

    /**
     * @param mixed $paymentDays
     */
    public function setPaymentDays($paymentDays): void
    {
        $this->paymentDays = $paymentDays;
    }

    public function __toString(): string
    {
        return $this->thru;
    }
}
