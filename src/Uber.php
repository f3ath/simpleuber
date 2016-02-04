<?php
namespace F3\SimpleUber;

use PHPCurl\CurlHttp\HttpClient;

class Uber
{
    const PRODUCTION_API = 'https://api.uber.com';
    const SANDBOX_API = 'https://sandbox-api.uber.com';

    /**
     * @var HttpClient
     */
    private $http;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $token;

    /**
     * Uber constructor.
     *
     * @param string $token
     * @param string $version
     * @param string $url
     * @param HttpClient $http
     */
    public function __construct($token, $version = 'v1', $url = self::PRODUCTION_API, HttpClient $http = null)
    {
        $this->http = $http ?: new HttpClient();
        $this->url = sprintf('%s/%s', $url, $version);
        $this->token = $token;
    }

    /**
     * The Products endpoint returns information about the Uber products offered at a given location.
     * The response includes the display name and other details about each product,
     * and lists the products in the proper display order.
     * Some Products, such as experiments or promotions such as UberPOOL and UberFRESH,
     * will not be returned by this endpoint.
     *
     * @see https://developer.uber.com/docs/v1-products
     *
     * @param float $lat Latitude component of location.
     * @param float $lon Longitude component of location.
     *
     * @return object
     */
    public function getProducts($lat, $lon)
    {
        return $this->get(
            '/products',
            array(
                'latitude' => (float) $lat,
                'longitude' => (float) $lon,
                )
        );
    }

    /**
     * Product details
     *
     * @see https://developer.uber.com/docs/v1-products-details
     *
     * @param string $productID Unique identifier representing a specific product for a given latitude & longitude.
     *                              For example, uberX in San Francisco will have a different product_id than uberX in Los Angeles.
     * @return object Response object
     */
    public function getProduct($productID)
    {
        return $this->get('/products/'.urlencode($productID));
    }

    /**
     * The Price Estimates endpoint returns an estimated price range for each product
     * offered at a given location. The price estimate is provided as a formatted string
     * with the full price range and the localized currency symbol.
     * The response also includes low and high estimates,
     * and the ISO 4217 currency code for situations requiring currency conversion.
     * When surge is active for a particular product, its surge_multiplier will be greater than 1,
     * but the price estimate already factors in this multiplier.
     *
     * @see https://developer.uber.com/docs/v1-estimates-price
     *
     * @param float $startLat Latitude component of start location.
     * @param float $startLon Longitude component of start location.
     * @param float $endLat Latitude component of end location.
     * @param float $endLon Longitude component of end location.

     * @return object
     */
    public function getPriceEstimates($startLat, $startLon, $endLat, $endLon)
    {
        return $this->get(
            '/estimates/price',
            array(
                'start_latitude' => (float) $startLat,
                'start_longitude' => (float) $startLon,
                'end_latitude' => (float) $endLat,
                'end_longitude' => (float) $endLon,
            )
        );
    }

    /**
     * The Time Estimates endpoint returns ETAs for all products offered at a given location,
     * with the responses expressed as integers in seconds.
     * We recommend that this endpoint be called every minute to provide the most accurate, up-to-date ETAs.
     *
     * @see https://developer.uber.com/docs/v1-estimates-time
     *
     * @param float $startLat Latitude component.
     * @param float $startLon Longitude component.
     * @param string $customerUUID Unique customer identifier to be used for experience customization.
     * @param string $productID Unique identifier representing a specific product for a given latitude & longitude.
     *
     * @return object
     */
    public function getTimeEstimates($startLat, $startLon, $customerUUID = null, $productID = null)
    {
        $query = array(
            'start_latitude' => (float) $startLat,
            'start_longitude' => (float) $startLon,
        );

        if ($customerUUID !== null) {
            $query['customer_uuid'] = $customerUUID;
        }

        if ($productID !== null) {
            $query['product_id'] = $productID;
        }

        return $this->get('/estimates/time', $query);
    }

    /**
     * @param string $uri
     * @param array $query
     * @return object
     * @throws ApiException
     */
    public function get($uri, array $query = array())
    {
        $fullUrl = $this->url . $uri;
        if ($query) {
            $fullUrl .= '?' . http_build_query($query);
        }
        $response = $this->http->get($fullUrl, array(sprintf('Authorization: Token %s', $this->token)));

        $json = json_decode($response->getBody());
        if ($response->getCode() !== 200) {
            throw ApiException::create($response->getCode(), $json);
        }
        return $json;
    }
}
