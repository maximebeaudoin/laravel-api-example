<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use Tests\TestCase;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends TestCase implements Context
{
    /**
     * The Guzzle HTTP Client.
     *
     * @var Client
     */
    protected $client;

    /**
     * The current resource
     */
    protected $resource;

    /**
     * The request payload
     */
    protected $requestPayload;

    /**
     * The Guzzle HTTP Response.
     */
    protected $response;

    /**
     * The decoded response object.
     */
    protected $responsePayload;

    /**
     * The current scope within the response payload
     * which conditions are asserted against.
     */
    protected $scope;

    /**
     * File to upload field
     *
     * @var array
     */
    protected $multipart = [];

    /**
     * Default access token
     *
     * @var string
     */
    protected $accessToken = '';

    /**
     * @var array
     */
    protected $defaultProperties = [];

    /**
     * @var array
     */
    protected $errorProperties = [
        'code',
        'message'
    ];

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        parent::__construct();
        parent::setUp();

        $this->client = new Client([
            'base_uri' => env('APP_URL'),
            'headers' => [
                'Accept' => 'application/vnd.com.example.api-v1+json',
                'Content-Type' => 'application/json'
            ]
        ]);
    }

    /**
     * @Given /^I have the payload:$/
     */
    public function iHaveThePayload(PyStringNode $requestPayload)
    {
        $this->requestPayload = $requestPayload;
    }

    /**
     * @When /^I request "(GET|PUT|PATCH|POST|DELETE) ([^"]*)"$/
     */
    public function iRequest($httpMethod, $resource)
    {
        $this->resource = $resource;

        try {
            switch ($httpMethod) {
                case 'PUT':
                case 'PATCH':
                case 'POST':
                case 'DELETE':
                    $this->response = $this
                        ->client
                        ->request(
                            $httpMethod,
                            $resource,
                            $this->buildRequestOptions()
                        );
                    break;

                default:
                    $this->response = $this
                        ->client
                        ->request(
                            $httpMethod,
                            $resource,
                            [
                                'headers' => [
                                    'Authorization' => 'Bearer ' . $this->accessToken
                                ]
                            ]
                        );
            }
        } catch (RequestException $e) {

            $response = $e->getResponse();

            // Sometimes the request will fail, at which point we have
            // no response at all. Let Guzzle give an error here, it's
            // pretty self-explanatory.
            if ($response === null) {
                throw $e;
            }

            $this->response = $e->getResponse();
        }
    }

    /**
     * @Then /^I get a "([^"]*)" response$/
     */
    public function iGetAResponse($statusCode)
    {
        $response = $this->getResponse();
        $contentType = $response->getHeaderLine('Content-Type');

        if ($contentType === 'application/json') {
            $bodyOutput = $response->getBody();
        } else {
            $bodyOutput = 'Output is ' . $contentType . ', which is not JSON and is therefore scary. Run the request manually.';
        }
        $this->assertSame((int)$statusCode, (int)$this->getResponse()->getStatusCode(), $bodyOutput);
    }

    /**
     * @Then the :property property equals to translation :translation in :locale
     */
    public function thePropertyEqualsToTranslation($property, $translation, $locale)
    {
        $payload = $this->getScopePayload();
        $actualValue = $this->arrayGet($payload, $property);

        $expectedValue = trans($translation, [], $locale);

        $this->assertEquals(
            $expectedValue,
            $actualValue,
            "Asserting the [$property] property in current scope equals [$expectedValue]: " . json_encode($payload)
        );
    }

    /**
     * @Given /^the "([^"]*)" property is null$/
     */
    public function thePropertyIsNull($property)
    {
        $payload = $this->getScopePayload();
        $actualValue = $this->arrayGet($payload, $property);

        $this->assertNull(
            $actualValue,
            "Asserting the [$property] property in current scope is null: " . json_encode($payload)
        );
    }

    /**
     * @Given /^the "([^"]*)" property is not null$/
     */
    public function thePropertyIsNotNull($property)
    {
        $payload = $this->getScopePayload();
        $actualValue = $this->arrayGet($payload, $property);

        $this->assertNotNull(
            $actualValue,
            "Asserting the [$property] property in current scope is null: " . json_encode($payload)
        );
    }

    /**
     * @Given /^the "([^"]*)" property equals "([^"]*)"$/
     */
    public function thePropertyEquals($property, $expectedValue)
    {
        $payload = $this->getScopePayload();
        $actualValue = $this->arrayGet($payload, $property);

        $this->assertEquals(
            $expectedValue,
            $actualValue,
            "Asserting the [$property] property in current scope equals [$expectedValue]: " . json_encode($payload)
        );
    }

    /**
     * @Given /^the "([^"]*)" property exists$/
     */
    public function thePropertyExists($property)
    {
        $payload = $this->getScopePayload();

        $message = sprintf(
            'Asserting the [%s] property exists in the scope [%s]: %s',
            $property,
            $this->scope,
            json_encode($payload)
        );

        if (is_object($payload)) {
            $this->assertTrue(array_key_exists($property, get_object_vars($payload)), $message);

        } else {
            $this->assertTrue(array_key_exists($property, $payload), $message);
        }
    }

    /**
     * @Given /^the "([^"]*)" property should not exists$/
     */
    public function thePropertyShouldNotExists($property)
    {
        $payload = $this->getScopePayload();

        $message = sprintf(
            'Asserting the [%s] property should not exists in the scope [%s]: %s',
            $property,
            $this->scope,
            json_encode($payload)
        );

        if (is_object($payload)) {
            $this->assertFalse(array_key_exists($property, get_object_vars($payload)), $message);

        } else {
            $this->assertFalse(array_key_exists($property, $payload), $message);
        }
    }

    /**
     * @Given /^the "([^"]*)" property is an array$/
     */
    public function thePropertyIsAnArray($property)
    {
        $payload = $this->getScopePayload();

        $actualValue = $this->arrayGet($payload, $property);

        $this->assertTrue(
            is_array($actualValue),
            "Asserting the [$property] property in current scope [{$this->scope}] is an array: " . json_encode($payload)
        );
    }

    /**
     * @Given /^the "([^"]*)" property is an object$/
     */
    public function thePropertyIsAnObject($property)
    {
        $payload = $this->getScopePayload();

        $actualValue = $this->arrayGet($payload, $property);

        $this->assertTrue(
            is_object($actualValue),
            "Asserting the [$property] property in current scope [{$this->scope}] is an object: " . json_encode($payload)
        );
    }

    /**
     * @Given /^the "([^"]*)" property is an empty array$/
     */
    public function thePropertyIsAnEmptyArray($property)
    {
        $payload = $this->getScopePayload();
        $scopePayload = $this->arrayGet($payload, $property);

        $this->assertTrue(
            is_array($scopePayload) and $scopePayload === [],
            "Asserting the [$property] property in current scope [{$this->scope}] is an empty array: " . json_encode($payload)
        );
    }

    /**
     * @Given /^the "([^"]*)" property contains (\d+) items$/
     */
    public function thePropertyContainsItems($property, $count)
    {
        $payload = $this->getScopePayload();

        $this->assertCount(
            $count,
            $this->arrayGet($payload, $property),
            "Asserting the [$property] property contains [$count] items: " . json_encode($payload)
        );
    }

    /**
     * @Given /^the "([^"]*)" property is an integer$/
     */
    public function thePropertyIsAnInteger($property)
    {
        $payload = $this->getScopePayload();

        $this->isType(
            'int',
            $this->arrayGet($payload, $property),
            "Asserting the [$property] property in current scope [{$this->scope}] is an integer: " . json_encode($payload)
        );
    }

    /**
     * @Given /^the "([^"]*)" property is a string$/
     */
    public function thePropertyIsAString($property)
    {
        $payload = $this->getScopePayload();

        $this->isType(
            'string',
            $this->arrayGet($payload, $property),
            "Asserting the [$property] property in current scope [{$this->scope}] is a string: " . json_encode($payload)
        );
    }

    /**
     * @Given /^the "([^"]*)" property is a string equalling "([^"]*)"$/
     */
    public function thePropertyIsAStringEqualling($property, $expectedValue)
    {
        $payload = $this->getScopePayload();

        $this->thePropertyIsAString($property);

        $actualValue = $this->arrayGet($payload, $property);

        $this->assertSame(
            $expectedValue,
            $actualValue,
            "Asserting the [$property] property in current scope [{$this->scope}] is a string equalling [$expectedValue]."
        );
    }

    /**
     * @Given /^the "([^"]*)" property is a string containing "([^"]*)"$/
     */
    public function thePropertyIsAStringContaining($property, $expectedValue)
    {
        $payload = $this->getScopePayload();

        $this->thePropertyIsAString($property);

        $actualValue = $this->arrayGet($payload, $property);

        $this->assertContains(
            $expectedValue,
            $actualValue,
            "Asserting the [$property] property in current scope [{$this->scope}] is a string containing [$expectedValue]."
        );
    }

    /**
     * @Given /^the "([^"]*)" property is a boolean$/
     */
    public function thePropertyIsABoolean($property)
    {
        $payload = $this->getScopePayload();

        $this->assertTrue(
            gettype($this->arrayGet($payload, $property)) === 'boolean',
            "Asserting the [$property] property in current scope [{$this->scope}] is a boolean."
        );
    }

    /**
     * @Given /^the "([^"]*)" property is a boolean equalling "([^"]*)"$/
     */
    public function thePropertyIsABooleanEqualling($property, $expectedValue)
    {
        $payload = $this->getScopePayload();
        $actualValue = $this->arrayGet($payload, $property);

        if (!in_array($expectedValue, ['true', 'false'])) {
            throw new \InvalidArgumentException("Testing for booleans must be represented by [true] or [false].");
        }

        $this->thePropertyIsABoolean($property);

        $this->assertSame(
            $expectedValue === 'true',
            $actualValue,
            "Asserting the [$property] property in current scope [{$this->scope}] is a boolean equalling [$expectedValue]."
        );
    }

    /**
     * @Given /^the "([^"]*)" property is a integer equalling "([^"]*)"$/
     */
    public function thePropertyIsAIntegerEqualling($property, $expectedValue)
    {
        $payload = $this->getScopePayload();
        $actualValue = $this->arrayGet($payload, $property);

        $this->thePropertyIsAnInteger($property);

        $this->assertSame(
            (int)$expectedValue,
            $actualValue,
            "Asserting the [$property] property in current scope [{$this->scope}] is an integer equalling [$expectedValue]."
        );
    }

    /**
     * @Given /^the "([^"]*)" property is either:$/
     */
    public function thePropertyIsEither($property, PyStringNode $options)
    {
        $payload = $this->getScopePayload();
        $actualValue = $this->arrayGet($payload, $property);

        $valid = explode("\n", (string)$options);

        $this->assertTrue(
            in_array($actualValue, $valid),
            sprintf(
                "Asserting the [%s] property in current scope [{$this->scope}] is in array of valid options [%s].",
                $property,
                implode(', ', $valid)
            )
        );
    }

    /**
     * @Given the :property property is a valid uuid
     */
    public function thePropertyIsAValidUuid($property)
    {
        $payload = $this->getScopePayload();
        $actualValue = $this->arrayGet($payload, $property);

        $this->assertTrue(
            Uuid::isValid($actualValue),
            "Asserting the [$property] property is a valid UUID."
        );
    }

    /**
     * @Given default properties exists
     */
    public function defaultPropertiesExists()
    {
        //Valid default properties exists
        foreach ($this->defaultProperties as $property) {
            $this->thePropertyExists($property);
        }

        //Id is a valid UUID
        if (in_array('id', $this->defaultProperties)) {
            $this->thePropertyIsAValidUuid('id');
        }
    }

    /**
     * @Given error properties exists
     */
    public function errorPropertiesExists()
    {
        //Valid default properties exists
        foreach ($this->errorProperties as $property) {
            $this->thePropertyExists($property);
        }
    }

    /**
     * @Given /^scope into the first "([^"]*)" property$/
     */
    public function scopeIntoTheFirstProperty($scope)
    {
        $this->scope = "{$scope}.0";
    }

    /**
     * @Given /^scope into the "([^"]*)" property$/
     */
    public function scopeIntoTheProperty($scope)
    {
        $this->scope = $scope;
    }

    /**
     * @Given /^the properties exist:$/
     */
    public function thePropertiesExist(PyStringNode $propertiesString)
    {
        foreach (explode("\n", (string)$propertiesString) as $property) {
            $this->thePropertyExists($property);
        }
    }

    /**
     * @Given the :property property is a file to upload
     */
    public function thePropertyIsAFileToUpload($property)
    {
        $payload = json_decode($this->requestPayload);

        $this->multipart[] = [
            'name' => $property,
            'contents' => fopen($payload->$property, 'r')
        ];
    }

    /**
     * @Given /^reset scope$/
     */
    public function resetScope()
    {
        $this->scope = null;
    }

    /**
     * @Transform /^(\d+)$/
     */
    public function castStringToNumber($string)
    {
        return intval($string);
    }

    /**
     * Checks the response exists and returns it.
     * @return Response
     * @throws Exception
     */
    protected function getResponse()
    {
        if (!$this->response) {
            throw new Exception("You must first make a request to check a response.");
        }

        return $this->response;
    }

    /**
     * Return the response payload from the current response.
     * @return mixed
     * @throws Exception
     */
    protected function getResponsePayload()
    {
        if (!$this->responsePayload) {
            $json = json_decode($this->getResponse()->getBody());

            if (json_last_error() !== JSON_ERROR_NONE) {
                $message = 'Failed to decode JSON body ';

                switch (json_last_error()) {
                    case JSON_ERROR_DEPTH:
                        $message .= '(Maximum stack depth exceeded).';
                        break;
                    case JSON_ERROR_STATE_MISMATCH:
                        $message .= '(Underflow or the modes mismatch).';
                        break;
                    case JSON_ERROR_CTRL_CHAR:
                        $message .= '(Unexpected control character found).';
                        break;
                    case JSON_ERROR_SYNTAX:
                        $message .= '(Syntax error, malformed JSON).';
                        break;
                    case JSON_ERROR_UTF8:
                        $message .= '(Malformed UTF-8 characters, possibly incorrectly encoded).';
                        break;
                    default:
                        $message .= '(Unknown error).';
                        break;
                }

                throw new Exception($message);
            }

            $this->responsePayload = $json;
        }

        return $this->responsePayload;
    }

    /**
     * Returns the payload from the current scope within
     * the response.
     *
     * @return mixed
     */
    protected function getScopePayload()
    {
        $payload = $this->getResponsePayload();

        if (!$this->scope) {
            return $payload;
        }

        return $this->arrayGet($payload, $this->scope);
    }

    /**
     * Get an item from an array using "dot" notation.
     *
     * @copyright   Taylor Otwell
     * @link        http://laravel.com/docs/helpers
     * @param       array $array
     * @param       string $key
     * @return mixed
     */
    protected function arrayGet($array, $key)
    {
        if (is_null($key)) {
            return $array;
        }

        foreach (explode('.', $key) as $segment) {

            if (is_object($array)) {
                if (!isset($array->{$segment})) {
                    return null;
                }
                $array = $array->{$segment};

            } elseif (is_array($array)) {
                if (!array_key_exists($segment, $array)) {
                    return null;
                }
                $array = $array[$segment];
            }
        }

        return $array;
    }

    /**
     * @return array
     */
    private function buildRequestOptions()
    {
        if (!empty($this->multipart)) {

            //Merge multipart and the request payload before sending the request
            //because we want to keep other field in the request
            $payload = json_decode($this->requestPayload, true);

            foreach ($payload as $key => $value) {
                $this->multipart[] = [
                    'name' => $key,
                    'contents' => $value
                ];
            }

            return [
                'multipart' => $this->multipart,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken
                ]
            ];
        }

        return [
            'body' => $this->requestPayload,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken
            ]
        ];
    }

    /**
     * @Given /^the header "([^"]*)" property equals "([^"]*)"$/
     */
    public function theHeaderPropertyEquals($property, $expectedValue)
    {
        $response = $this->getResponse();
        $actualValue = $response->getHeaderLine($property);

        $this->assertEquals(
            $actualValue,
            $expectedValue,
            "Asserting the header [$property] property in current scope equals [$expectedValue]: " . $actualValue
        );
    }
}
