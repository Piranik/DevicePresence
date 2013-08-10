<?php
namespace App\Lookup;

/**
 * Lookup a MAC address and return the vendor
 *
 * @author Tim de Pater <code@trafex.nl>
 */
class MacAddress
{
    const APIHOST = 'http://www.macvendorlookup.com';

    /**
     * The API key
     *
     * @var string
     */
    private $apiKey;

    /**
     * Constructor
     *
     * @param string $apiKey
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Lookup a MAC address and return the vendor
     *
     * @param mixed $macAddress
     * @return string;
     */
    public function getVendorForMacAddress($macAddress)
    {
        if (null === $this->apiKey) {
            // Usage of the API is disabled
            return null;
        }
        $response = $this->fetchFromApi($macAddress);
        if (!$response || !is_array(json_decode($response))) {
            return null;
        }
        return current(json_decode($response))->company;
    }

    /**
     * Fetch the API data for the given MAC address
     *
     * @param string $macAddress
     * @return string|false
     */
    public function fetchFromApi($macAddress)
    {
        return file_get_contents(
            sprintf(
                '%s/api/%s/%s',
                self::APIHOST,
                $this->apiKey,
                $macAddress
            )
        );
    }
}
