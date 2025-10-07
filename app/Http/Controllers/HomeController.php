<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Pallet;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $data = [];

        // For admin and staff users, show order statistics
        if (in_array($user->role, ['admin', 'staff'])) {
            $data['totalOrders'] = Order::count();
            $data['unverifiedOrders'] = Order::where('verified', false)->count();
        }

        return view('home', $data);
    }
}
