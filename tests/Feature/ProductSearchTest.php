<?php

namespace Tests\Feature;

use Illuminate\Testing\TestResponse;
use Tests\TestCase;

use App\AccountValidatorManager;

class ProductSearchTest extends TestCase
{
    /**
     * Search for a valid product code
     * @return void
     */
    public function testExample1()
    {
        // A valid product code
        $response = $this->get('/api/search/37168');

        $this->checkResult($response, 200, true);
    }

    /**
     * Search for an invalid product code
     * @return void
     */
    public function testExample2()
    {
        // A valid product code
        $response = $this->get('/api/search/99999');

        $this->checkResult($response, 200, false);
    }

    /**
     * Check the result of a test
     *
     * @param TestResponse $response
     * @param int $statusCode
     * @param bool $isValid
     */
    private function checkResult($response, $statusCode, $isValid)
    {
        $response->assertStatus($statusCode);

        $content = json_decode($response->content(), true);

        $isValid ? $this->assertTrue($content['valid']) : $this->assertFalse($content['valid']);
    }
}
