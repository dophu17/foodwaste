<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Storage;

class RestaurantController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $restaurant = $user->restaurant;

        if (!$restaurant) {
            return redirect()->route('restaurant.create');
        }

        return view('restaurant.show', compact('restaurant'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        if ($user->restaurant) {
            return redirect()->route('restaurant.index')
                ->with('info', 'Bạn đã có thông tin nhà hàng.');
        }

        return view('restaurant.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'description' => 'nullable|string',
            'cuisine_type' => 'required|string|max:100',
            'business_hours' => 'required|string|max:200',
            'capacity' => 'required|integer|min:1|max:1000',
        ]);

        $user = Auth::user();
        
        // Check if user already has a restaurant
        if ($user->restaurant) {
            return redirect()->route('restaurant.index')
                ->with('error', 'Bạn đã có thông tin nhà hàng.');
        }

        $restaurant = Restaurant::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'description' => $request->description,
            'cuisine_type' => $request->cuisine_type,
            'business_hours' => $request->business_hours,
            'capacity' => $request->capacity,
            'status' => 'active',
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Thông tin nhà hàng đã được tạo thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Restaurant $restaurant)
    {
        $user = Auth::user();
        
        if ($restaurant->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('restaurant.show', compact('restaurant'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Restaurant $restaurant)
    {
        $user = Auth::user();
        
        if ($restaurant->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('restaurant.edit', compact('restaurant'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Restaurant $restaurant)
    {
        $user = Auth::user();
        
        if ($restaurant->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'description' => 'nullable|string',
            'cuisine_type' => 'nullable|string|max:100',
            'business_hours' => 'required|string|max:200',
            'capacity' => 'required|integer|min:1|max:1000',
        ]);

        $restaurant->update($request->all());

        return redirect()->route('restaurant.show', $restaurant)
            ->with('success', 'Thông tin nhà hàng đã được cập nhật thành công!');
    }
}
