<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Menu::with('restaurant')
            ->when(Auth::check(), function ($query) {
                return $query->whereHas('restaurant', function ($q) {
                    $q->where('user_id', Auth::id());
                });
            });

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('category', 'like', "%{$searchTerm}%")
                  ->orWhere('valid_from', 'like', "%{$searchTerm}%")
                  ->orWhere('valid_until', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $menus = $query->orderBy('created_at', 'desc')->paginate(10);

        // Get unique categories for filter dropdown
        $categories = Menu::when(Auth::check(), function ($query) {
                return $query->whereHas('restaurant', function ($q) {
                    $q->where('user_id', Auth::id());
                });
            })
            ->distinct()
            ->pluck('category')
            ->filter()
            ->sort()
            ->values();

        return view('menus.index', compact('menus', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Lấy nhà hàng đầu tiên của user đang đăng nhập
        $restaurant = Restaurant::where('user_id', Auth::id())->first();
        
        if (!$restaurant) {
            return redirect()->route('restaurant.create')
                ->with('error', __('messages.Please create a restaurant first'));
        }

        return view('menus.create', compact('restaurant'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Lấy nhà hàng của user đang đăng nhập
        $restaurant = Restaurant::where('user_id', Auth::id())->first();
        if (!$restaurant) {
            abort(403, __('messages.Restaurant not found'));
        }

        // Chuẩn bị dữ liệu để tạo menu
        $menuData = $request->all();
        $menuData['restaurant_id'] = $restaurant->id;
        
        // Xử lý ngày hiệu lực - nếu không nhập thì để null (hiệu lực mãi mãi)
        if (empty($menuData['valid_from'])) {
            $menuData['valid_from'] = null;
        }
        if (empty($menuData['valid_until'])) {
            $menuData['valid_until'] = null;
        }

        $menu = Menu::create($menuData);

        return redirect()->route('menus.show', $menu)
            ->with('success', __('messages.Menu created successfully!'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Menu $menu)
    {
        // Kiểm tra quyền truy cập - chỉ cho phép chủ nhà hàng
        if (Auth::check()) {
            if ($menu->restaurant->user_id !== Auth::id()) {
                abort(403, __('messages.Unauthorized access to this menu.'));
            }
        }

        $menu->load(['restaurant', 'foodItems']);

        return view('menus.show', compact('menu'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Menu $menu)
    {
        // Kiểm tra quyền truy cập - chỉ cho phép chủ nhà hàng
        if (Auth::check()) {
            if ($menu->restaurant->user_id !== Auth::id()) {
                abort(403, __('messages.Unauthorized access to this menu.'));
            }
        }

        return view('menus.edit', compact('menu'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        // Kiểm tra quyền truy cập - chỉ cho phép chủ nhà hàng
        if (Auth::check()) {
            if ($menu->restaurant->user_id !== Auth::id()) {
                abort(403, __('messages.Unauthorized access to this menu.'));
            }
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Chuẩn bị dữ liệu để cập nhật menu
        $menuData = $request->all();
        
        // Xử lý ngày hiệu lực - nếu không nhập thì để null (hiệu lực mãi mãi)
        if (empty($menuData['valid_from'])) {
            $menuData['valid_from'] = null;
        }
        if (empty($menuData['valid_until'])) {
            $menuData['valid_until'] = null;
        }

        $menu->update($menuData);

        return redirect()->route('menus.show', $menu)
            ->with('success', __('messages.Menu updated successfully!'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        // Kiểm tra quyền truy cập - chỉ cho phép chủ nhà hàng
        if (Auth::check()) {
            if ($menu->restaurant->user_id !== Auth::id()) {
                abort(403, __('messages.Unauthorized access to this menu.'));
            }
        }

        $menuName = $menu->name;
        $menu->delete();

        return redirect()->route('menus.index')
            ->with('success', __('messages.Menu deleted successfully!', ['name' => $menuName]));
    }

    /**
     * Toggle menu active status
     */
    public function toggleStatus(Menu $menu)
    {
        // Kiểm tra quyền truy cập - chỉ cho phép chủ nhà hàng
        if (Auth::check()) {
            if ($menu->restaurant->user_id !== Auth::id()) {
                abort(403, __('messages.Unauthorized access to this menu.'));
            }
        }

        $menu->update(['is_active' => !$menu->is_active]);

        $status = $menu->is_active ? __('messages.activated') : __('messages.deactivated');
        return redirect()->back()
            ->with('success', __('messages.Menu status changed successfully!', ['status' => $status]));
    }

    /**
     * Get menus by restaurant
     */
    public function getByRestaurant(Restaurant $restaurant)
    {
        $menus = $restaurant->menus()
            ->with('foodItems')
            ->active()
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return view('menus.restaurant', compact('restaurant', 'menus'));
    }

    /**
     * Get menus by category
     */
    public function getByCategory($category)
    {
        $menus = Menu::with(['restaurant', 'foodItems'])
            ->byCategory($category)
            ->active()
            ->orderBy('restaurant_id')
            ->orderBy('name')
            ->paginate(15);

        return view('menus.category', compact('menus', 'category'));
    }
}
