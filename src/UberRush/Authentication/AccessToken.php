<?php

namespace UberRush\Authentication;

/**
 * @package UberRush\Authentication
 */
class AccessToken
{
    /**
     * The access token value.
     *
     * @var string
     */
    protected $value = '';

    /**
     * Date when token expires.
     *
     * @var \DateTime|null
     */
    protected $expiresAt;

    /**
     * Create a new access token entity.
     *
     * @param string $accessToken
     * @param int    $expiresIn
     */
    public function __construct($accessToken, $expiresIn = 0)
    {
        $this->value = $accessToken;
        if ($expiresIn) {
            $this->setExpiresAtFromSeconds($expiresIn);
        }
    }

    /**
     * Getter for expiresAt.
     *
     * @return \DateTime|null
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * Checks the expiration of the access token.
     *
     * @return boolean|null
     */
    public function isExpired()
    {
        if ($this->getExpiresAt() instanceof \DateTime) {
            return $this->getExpiresAt()->getTimestamp() < time();
        }

        return null;
    }

    /**
     * Returns the access token as a string.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
    /**
     * Returns the access token as a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getValue();
    }

    /**
     * Setter for expiresAt.
     *
     * @param int $seconds
     */
    protected function setExpiresAtFromSeconds($seconds)
    {
        $dt = new \DateTime();
        $dt->add(new \DateInterval('PT'. $seconds .'S'));
        $this->expiresAt = $dt;
    }

}