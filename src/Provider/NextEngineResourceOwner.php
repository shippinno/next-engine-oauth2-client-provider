<?php

namespace Shippinno\NextEngine\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class NextEngineResourceOwner implements ResourceOwnerInterface
{
    /**
     * Raw response
     *
     * @var array
     */
    protected $response;

    /**
     * @param array $response
     */
    public function __construct(array $response)
    {
        $this->response = $response;
    }

    /**
     * Returns the identifier of the authorized resource owner.
     *
     * @return string
     */
    public function getId()
    {
        return $this->response['uid'];
    }

    /**
     * Returns the shop name.
     *
     * @return string
     */
    public function getCompanyName()
    {
        return $this->response['company_name'];
    }

    /**
     * Returns the shop name.
     *
     * @return string
     */
    public function getCompanyNameKane()
    {
        return $this->response['company_kana'];
    }

    /**
     * Return all of the owner details available as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->response;
    }
}
