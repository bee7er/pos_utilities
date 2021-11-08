<?php

namespace App\Http\Controllers;

use App\AccountValidatorManager;
use App\Product;
use Exception;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use RuntimeException;

/**
 * Class HomeController
 * @package App\Http\Controllers
 */
class HomeController extends Controller
{
	/**
	 * The Guard implementation.
	 *
	 * @var Guard
	 */
	protected $auth;

	/**
	 * Create a new filter instance.
	 *
	 * @param  Guard  $auth
	 * @return void
	 */
	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
	}

	/**
	 * Show the application dashboard to the user.
	 *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function index()
	{
		$loggedIn = false;
		if ($this->auth->check()) {
			$loggedIn = true;
		}

		return view('welcome', compact('loggedIn'));
	}

	/**
	 * Find the page number for multiple product codes and build up a string
	 * of page numbers, which can be used for printing
	 *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function printing()
	{
		return view('printing');
	}

	/**
	 * Find the page number for a product code
	 *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function finding()
	{
		return view('finding');
	}
}
