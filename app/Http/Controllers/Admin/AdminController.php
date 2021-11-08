<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Product;
use Exception;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use RuntimeException;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Clear all messages
        Session::forget(['success', 'fail']);

        return view('admin', [
            'productsDataUrl' => env('PRODUCT_IMPORT_PDF_URL')
        ]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function action()
    {
        try {
            $params = request()->all();

            switch (true) {
                case isset($params['downloadPdf']):
                    if ($this->downloadPdf()) {

                        request()->session()->flash('message.level', 'success');
                        request()->session()->flash('message.content', 'Products data downloaded successfully');

                        return Response::redirectTo('/admin');
                    }

                    request()->session()->flash('message.level', 'danger');
                    request()->session()->flash('message.content', 'Error downloading Products data');

                    break;
                case isset($params['refreshProducts']):
                    if ($this->refreshProducts()) {

                        request()->session()->flash('message.level', 'success');
                        request()->session()->flash('message.content', 'Products data refreshed successfully');

                        return Response::redirectTo('/admin');
                    }

                    request()->session()->flash('message.level', 'danger');
                    request()->session()->flash('message.content', 'Error refreshing Products data');

                    break;
                case isset($params['verifyProducts']):
                    if ($this->verifyProducts()) {

                        request()->session()->flash('message.level', 'success');
                        request()->session()->flash('message.content', 'Products data verified successfully');

                        return Response::redirectTo('/admin');
                    }

                    request()->session()->flash('message.level', 'danger');
                    request()->session()->flash('message.content', 'Unknown error verifying Products data.');

                    break;
                default:

                    request()->session()->flash('message.level', 'danger');
                    request()->session()->flash('message.content', 'Unexpected request: ' . print_r($params, true));
            }
        } catch (Exception $e) {

            request()->session()->flash('message.level', 'danger');
            request()->session()->flash('message.content', 'Error: ' . $e->getMessage());

            return Response::redirectTo("/admin");
        }

        return view('admin', [
            'productsDataUrl' => env('PRODUCT_IMPORT_PDF_URL')
        ]);
    }

    /**
     * Download the products PDF and save to the filesystem file
     *
     * @throws RuntimeException
     */
    public function downloadPdf()
    {
        set_time_limit(200);

        (new Product())->download();

        return true;
    }

    /**
     * Refreshes the products data from the filesystem file
     *
     * @throws RuntimeException
     */
    public function refreshProducts()
    {
        set_time_limit(600);

        (new Product())->refresh();

        return true;
    }

    /**
     * Verifies the products data and returns an array of error codes
     *
     * @throws RuntimeException
     * @return boolean
     */
    public function verifyProducts()
    {
        return (new Product())->verify();
    }
}
