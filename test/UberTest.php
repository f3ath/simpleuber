<?php
namespace F3\SimpleUber;


class UberTest extends \PHPUnit_Framework_TestCase
{
    private $uber;

    public function setUp()
    {
        $this->uber = $this->getMock('F3\\SimpleUber\\Uber', ['get'], ['test_token']);
    }

    public function testGetProducts()
    {
        $response = new \stdClass();

        $this->uber->method('get')
            ->with('/products', ['latitude' => 12.34, 'longitude' => 56.78])
            ->willReturn($response);

        $this->assertEquals($response, $this->uber->getProducts('12.34', '56.78'));
    }

    public function testGetProduct()
    {
        $response = new \stdClass();

        $this->uber->method('get')
            ->with('/products/test+id')
            ->willReturn($response);

        $this->assertEquals($response, $this->uber->getProduct('test id'));
    }

    public function testGetPriceEstimates()
    {
        $response = new \stdClass();

        $this->uber->method('get')
            ->with('/estimates/price', [
                'start_latitude' => 12.34,
                'start_longitude' => 56.78,
                'end_latitude' => 11.22,
                'end_longitude' => 33.44,
            ])
            ->willReturn($response);

        $this->assertEquals($response, $this->uber->getPriceEstimates('12.34', '56.78', '11.22', '33.44'));
    }

    public function testGetTimeEstimates()
    {
        $response = new \stdClass();

        $this->uber->method('get')
            ->with('/estimates/time', [
                'start_latitude' => 12.34,
                'start_longitude' => 56.78,
                'customer_uuid' => 'test_client',
                'product_id' => 'xxx',
            ])
            ->willReturn($response);

        $this->assertEquals($response, $this->uber->getTimeEstimates('12.34', '56.78', 'test_client', 'xxx'));
    }

    public function testGetWithNoQuery()
    {
        $http = $this->createHttpMock('https://api.uber.com/v1/foo?a=b');
        $uber = new Uber('my_token', 'v1', Uber::PRODUCTION_API, $http);
        $this->assertEquals((object)['foo' => 'bar'], $uber->get('/foo', ['a' => 'b']));
    }

    public function testGetWithQuery()
    {
        $http = $this->createHttpMock('https://api.uber.com/v1/foo');
        $uber = new Uber('my_token', 'v1', Uber::PRODUCTION_API, $http);
        $this->assertEquals((object)['foo' => 'bar'], $uber->get('/foo'));
    }

    public function testGetWithException()
    {
        $error = [
            'message' => 'Test message',
            'code' => 'test_code',
            'fields' => ['field1' => 'error1']
        ];
        $http = $this->createHttpMock('https://api.uber.com/v1/foo', 555, json_encode($error));
        $uber = new Uber('my_token', 'v1', Uber::PRODUCTION_API, $http);
        try {
            $uber->get('/foo');
            $this->fail('No exception');
        } catch (ApiException $e) {
            $this->assertEquals('Test message', $e->getErrorMessage());
            $this->assertEquals('test_code', $e->getErrorCode());
            $this->assertEquals((object) ['field1' => 'error1'], $e->getFields());
            $this->assertEquals(555, $e->getHttpCode());
        }
    }

    /**
     * @param $url
     * @param int $code
     * @param string $json
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createHttpMock($url, $code = 200, $json = '{"foo": "bar"}')
    {
        $response = $this->getMockBuilder('PHPCurl\\CurlHttp\\HttpCient')
            ->disableOriginalConstructor()
            ->setMethods(['getCode', 'getBody'])
            ->getMock();
        $response->method('getCode')->willReturn($code);
        $response->method('getBody')->willReturn($json);

        $http = $this->getMock('PHPCurl\\CurlHttp\\HttpClient', ['get']);
        $http->method('get')
            ->with($url, ['Authorization: Token my_token'])
            ->willReturn($response);
        return $http;
    }
}
