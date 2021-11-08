<?php

namespace App\Http\Controllers\API;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

use App\Http\Controllers\Controller;
use App\Product;

class ProductController extends Controller
{
    /**
     * Download products tables pdf
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function download()
    {
        try {
            set_time_limit(600);

            http_response_code(200);

            (new Product())->download();

            return [
                'valid' => true,
                'message' => "Product data PDF downloaded successfully"
            ];

        } catch (Exception $e) {
            http_response_code(403);

            return [
                'valid' => false,
                'message' => "Unexpected error: " . $e->getMessage()
            ];
        }
    }

    /**
     * Refresh products tables
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function refresh()
    {
        try {
            set_time_limit(600);

            http_response_code(200);

            (new Product())->refresh();

            return [
                'valid' => true,
                'message' => "Product data refreshed successfully"
            ];

        } catch (Exception $e) {
            http_response_code(403);

            return [
                'valid' => false,
                'message' => "Unexpected error: " . $e->getMessage()
            ];
        }
    }
    /**
     * Verify products tables
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify()
    {
        try {
            set_time_limit(100);

            http_response_code(200);

            if ((new Product())->verify()) {

                return [
                    'valid' => true,
                    'message' => "Product data verified successfully"
                ];
            }

            return [
                'valid' => false,
                'message' => "An error was detected"
            ];
        } catch (Exception $e) {
            http_response_code(403);

            return [
                'valid' => false,
                'message' => "Unexpected error: " . $e->getMessage()
            ];
        }
    }

    /**
     * Search products tables
     *
     * @param $productCode
     * @return \Illuminate\Http\RedirectResponse
     */
    public function search($productCode)
    {
        try {
            http_response_code(200);

            return Product::search($productCode);

        } catch (Exception $e) {
            http_response_code(403);

            return [
                'valid' => false,
                'message' => "Unexpected error: " . $e->getMessage(),
                'original-productcode' => $productCode,
                'page-number' => ''
            ];
        }
    }
}
