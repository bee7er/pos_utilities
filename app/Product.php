<?php

namespace App;

use Exception;
use RuntimeException;

use Illuminate\Database\Eloquent\Model;
use Smalot\PdfParser\Parser;
use DB;

/**
 * Class Product
 * @package App
 */
class Product extends Model
{
    protected $guarded  = array('id');

    /**
     * Downloads the Products data file
     *
     * @throws RuntimeException
     */
    public function download()
    {
        $outfile = env('PROJECT_DIRECTORY') . DIRECTORY_SEPARATOR .
            env('PRODUCT_IMPORT_DIR') . DIRECTORY_SEPARATOR .
            env('PRODUCT_IMPORT_FILENAME');

        return $this->downloadDataFile('productsDataUrl', $outfile);
    }

    /**
     * Downloads the data file
     *
     * @param string $param
     * @param string $outfile
     * @return bool
     */
    private function downloadDataFile($param, $outfile)
    {
        // Get the URL for the data
        $params = request()->all();

        if (empty($params[$param])) {
            throw new RuntimeException("The url of the Products data file is required");
        }

        // Download the new data
        if (file_exists($outfile)) {
            // Rename the current file out of the way
            @rename($outfile, str_replace('.pdf', '', $outfile) . date('Ymd_His') . '.pdf');
        }

        // Output the new file data to the file system
        if (false === file_put_contents($outfile, fopen($params[$param], 'r'))) {
            throw new RuntimeException("Error writing output file of Products");
        }

        return true;
    }

    /**
     * Refresh products table
     * @return boolean
     */
    public function refresh()
    {
        $file = env('PROJECT_DIRECTORY') . DIRECTORY_SEPARATOR .
            env('PRODUCT_IMPORT_DIR') . DIRECTORY_SEPARATOR .
            env('PRODUCT_IMPORT_FILENAME');

        if (!file_exists($file)) {
            throw new RuntimeException("Could not find input file '$file'");
        } else {
//            self::query()->whereNotNull('date_active')->update(['date_active' => '']);

            // Delete all existing data
            self::truncate();

            // Open the PDF file and import the data
            $parser = new Parser();
            $pdf    = $parser->parseFile($file);

            $page_number = 1;
            $pages  = $pdf->getPages();
            foreach ($pages as $page) {

                $textArray = $page->getTextArray();
                $pageText = $page->getText();

                // Parse the data and write new Product record
                $productDataArray = self::parsePage($pageText, $textArray);
                if (null !== $productDataArray) {
                    $productDataArray['page_number'] = $page_number;
                    $product = new Product($productDataArray);

                    // Write out to the product table
                    $product->save();
                }

                $page_number++;
            }

        }

        return true;
    }

    /**
     * Parse product details from the page
     * @param $pageText
     * @param $textArray
     * @return array
     */
    private function parsePage($pageText, $textArray)
    {
        // NB The pound sign is a double byte character
        if ($textArray[0] === '£') {
            // A special case.  Looks like for Langlois is a corrupted entry.
            return self::parseLangloisDetails($pageText, $textArray);
        } elseif (substr($pageText, 0, 2) === '£' && count($textArray) > 8) {
            // Pages in this format are for multi-purchase products, ie wines
            return self::parseMultiPurchaseProductDetails($pageText, $textArray);
        }

        // All other products, ie beers, spirits, etc
        return self::parseSoleProductDetails($pageText, $textArray);
    }

    /**
     * Parse multi-purchase product details from the page
     * @param $pageText
     * @param $textArray
     * @return array
     */
    private function parseMultiPurchaseProductDetails($pageText, $textArray)
    {
        $count = count($textArray);
        $dateCode = $textArray[$count -2];  // Ignore last element whch is always blank

        $id = null;
        $product_code = substr($dateCode, -5);  // Last 5 characters
        // The description consists of the array elements between the price and the
        // date/code, ie ignore the first 6 elements, the date/code and the empty element
        // at the end, thus, we ignore 8
        // Here we also trim each element before imploding with a single space
        $description = implode(' ',
            array_map(
                'trim',
                array_slice($textArray, 6, $count - 8)
            )
        );
        $price = $textArray[5];
        $multi_purchase_price = $textArray[0];
        $date_active = substr($dateCode, 0, 8); // First 8 characters

        return [
            'id' => $id,
            'product_code' => $product_code,
            'description' => $description,
            'price' => $price,
            'multi_purchase_price' => $multi_purchase_price,
            'date_active' => $date_active
        ];
    }

    /**
     * Parse sole product details from the page
     * @param $pageText
     * @param $textArray
     * @return array
     */
    private function parseSoleProductDetails($pageText, $textArray)
    {
        $count = count($textArray);
        $dateCode = $textArray[$count -3];
        $id = null;
        $product_code = substr($dateCode, -5);  // Last 5 characters
        // We slice the first elements of the array as the description
        // Unfortunately this can include extra product details which we
        // don't really want, but the array details wre inconsistent, so
        // include everything to make sre we get the product title
        $description = implode(' ',
            array_map(
                'trim',
                array_slice($textArray, 0, $count - 3)
            )
        );
        $price = $textArray[$count -2];
        $multi_purchase_price = '';
        $date_active = substr($dateCode, 0, 8); // First 8 characters

        return [
            'id' => $id,
            'product_code' => $product_code,
            'description' => $description,
            'price' => $price,
            'multi_purchase_price' => $multi_purchase_price,
            'date_active' => $date_active
        ];
    }

    /**
     * Parse Langlois details from the page
     * This product page is unlike any of the others
     * @param $pageText
     * @param $textArray
     * @return array
     */
    private function parseLangloisDetails($pageText, $textArray)
    {
        $count = count($textArray);
        if ($count !== 12) {
            return null;
        }

        $dateCode = $textArray[11];
        $id = null;
        $product_code = substr($dateCode, -5);  // Last 5 characters
        $description = implode(' ',
            array_map(
                'trim',
                array_slice($textArray, 9, 2)
            )
        );
        $multi_purchase_price = implode('',
            array_map(
                'trim',
                array_slice($textArray, 0, 3)
            )
        );
        $price = $textArray[8];
        $date_active = substr($dateCode, 0, 8); // First 8 characters

        return [
            'id' => $id,
            'product_code' => $product_code,
            'description' => $description,
            'price' => $price,
            'multi_purchase_price' => $multi_purchase_price,
            'date_active' => $date_active
        ];
    }

    /**
     * Verify products table
     * @return bool
     * @throws Exception
     */
    public function verify()
    {
        $products = DB::select(DB::raw('select * from products where 1=1'));
        $productCount = count($products);
        if ($productCount <= 0) {
            throw new Exception("No products found. Please load the product price file. $productCount");
        }

        $products = DB::select(DB::raw('select * from products where id != page_number'));
        if (count($products) > 0) {
            foreach ($products as $product) {
                $errorCodes[] = $product->id;
            }

            sort($errorCodes);
            $errors = implode(', ', $errorCodes);

            throw new Exception('Error validating products. Please check the following products: ' . $errors);
        }

        return true;
    }

    /**
     * Search for the product
     *
     * @param $productCode
     * @return \Illuminate\Http\Response
     */
    public static function search($productCode)
    {
        try {
            // Find product by product code
            $products = DB::select(DB::raw("select * from products where product_code = '$productCode'"));
            if (count($products) <= 0) {
                throw new Exception("No product found for product code $productCode");
            } elseif (count($products) > 1) {
                throw new Exception("Multiple products found for product code $productCode");
            }

            return [
                'valid' => true,
                'message' => "Product found for code $productCode",
                'originalProductCode' => $productCode,
                'pageNumber' => $products[0]->page_number,
                'description' => $products[0]->description,
                'effectiveDate' => $products[0]->date_active
            ];

        } catch (Exception $e) {
            return [
                'valid' => false,
                'message' => "Error processing product code: " . $e->getMessage(),
                'originalProductCode' => $productCode,
                'pageNumber' => '',
                'description' => ''

            ];
        }
    }
}
