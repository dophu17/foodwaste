<?php

namespace App\Http\Controllers;

use App\Models\FoodItem;
use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class FoodItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = FoodItem::with(['menu.restaurant'])
            ->when(Auth::check(), function ($query) {
                return $query->whereHas('menu.restaurant', function ($q) {
                    $q->where('user_id', Auth::id());
                });
            });

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('category', 'like', "%{$searchTerm}%")
                  ->orWhere('cuisine_style', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by availability
        if ($request->filled('availability')) {
            if ($request->availability === 'available') {
                $query->where('is_available', true);
            } elseif ($request->availability === 'unavailable') {
                $query->where('is_available', false);
            }
        }

        // Filter by menu
        if ($request->filled('menu_id')) {
            $query->where('menu_id', $request->menu_id);
        }

        $foodItems = $query->orderBy('created_at', 'desc')->paginate(9);

        // Get unique categories for filter dropdown
        $categories = FoodItem::when(Auth::check(), function ($query) {
                return $query->whereHas('menu.restaurant', function ($q) {
                    $q->where('user_id', Auth::id());
                });
            })
            ->distinct()
            ->pluck('category')
            ->filter()
            ->sort()
            ->values();

        // Get menus for filter dropdown
        $menus = Menu::when(Auth::check(), function ($query) {
                return $query->whereHas('restaurant', function ($q) {
                    $q->where('user_id', Auth::id());
                });
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('food-items.index', compact('foodItems', 'categories', 'menus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Lấy nhà hàng của user đang đăng nhập
        $restaurant = Restaurant::where('user_id', Auth::id())->first();
        
        if (!$restaurant) {
            return redirect()->route('restaurant.create')
                ->with('error', __('messages.Please create a restaurant first'));
        }

        // Lấy danh sách menu đang hoạt động
        $menus = Menu::where('restaurant_id', $restaurant->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        if ($menus->isEmpty()) {
            return redirect()->route('menus.create')
                ->with('error', __('messages.Please create a menu first'));
        }

        // Nếu có menu_id được truyền từ query parameter, kiểm tra xem có hợp lệ không
        $selectedMenuId = $request->query('menu_id');
        if ($selectedMenuId) {
            $selectedMenu = $menus->where('id', $selectedMenuId)->first();
            if (!$selectedMenu) {
                $selectedMenuId = null; // Reset nếu menu_id không hợp lệ
            }
        }
        return view('food-items.create', compact('menus', 'restaurant', 'selectedMenuId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'menu_id' => 'required|exists:menus,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'cuisine_style' => 'nullable|string|max:100',
            'ingredients' => 'nullable|string',
            'allergens' => 'nullable|string',
            'preparation_time' => 'required|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'boolean',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Kiểm tra quyền truy cập menu
        $menu = Menu::findOrFail($request->menu_id);
        if (!$menu->canEditByUser(Auth::user())) {
            return redirect()->back()
                ->with('error', __('messages.Unauthorized access'));
        }

        $data = $request->all();
        $data['is_available'] = $request->has('is_available');

        // Xử lý upload ảnh
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('food-items', 'public');
            $data['image_path'] = $imagePath;
        }

        FoodItem::create($data);

        return redirect()->route('food-item.index')
            ->with('success', __('messages.Food item created successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $foodItem = FoodItem::with(['menu.restaurant', 'wasteRecords'])
            ->findOrFail($id);

        // Kiểm tra quyền truy cập
        if (!$foodItem->menu->canEditByUser(Auth::user())) {
            return redirect()->route('food-item.index')
                ->with('error', __('messages.Unauthorized access'));
        }

        return view('food-items.show', compact('foodItem'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $foodItem = FoodItem::with('menu.restaurant')->findOrFail($id);

        // Kiểm tra quyền truy cập
        if (!$foodItem->menu->canEditByUser(Auth::user())) {
            return redirect()->route('food-item.index')
                ->with('error', __('messages.Unauthorized access'));
        }

        // Lấy danh sách menu đang hoạt động
        $menus = Menu::where('restaurant_id', $foodItem->menu->restaurant_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('food-items.edit', compact('foodItem', 'menus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $foodItem = FoodItem::with('menu')->findOrFail($id);

        // Kiểm tra quyền truy cập
        if (!$foodItem->menu->canEditByUser(Auth::user())) {
            return redirect()->route('food-item.index')
                ->with('error', __('messages.Unauthorized access'));
        }

        $validator = Validator::make($request->all(), [
            'menu_id' => 'required|exists:menus,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'cuisine_style' => 'nullable|string|max:100',
            'ingredients' => 'nullable|string',
            'allergens' => 'nullable|string',
            'preparation_time' => 'required|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'boolean',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['is_available'] = $request->has('is_available');

        // Xử lý upload ảnh mới
        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu có
            if ($foodItem->image_path) {
                Storage::disk('public')->delete($foodItem->image_path);
            }
            
            $imagePath = $request->file('image')->store('food-items', 'public');
            $data['image_path'] = $imagePath;
        }

        $foodItem->update($data);

        return redirect()->route('food-item.index')
            ->with('success', __('messages.Food item updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $foodItem = FoodItem::with('menu')->findOrFail($id);

        // Kiểm tra quyền truy cập
        if (!$foodItem->menu->canEditByUser(Auth::user())) {
            return redirect()->route('food-item.index')
                ->with('error', __('messages.Unauthorized access'));
        }

        // Xóa ảnh nếu có
        if ($foodItem->image_path) {
            Storage::disk('public')->delete($foodItem->image_path);
        }

        $foodItem->delete();

        return redirect()->route('food-item.index')
            ->with('success', __('messages.Food item deleted successfully'));
    }

    /**
     * Toggle availability status of food item.
     */
    public function toggleAvailability(string $id)
    {
        $foodItem = FoodItem::with('menu')->findOrFail($id);

        // Kiểm tra quyền truy cập
        if (!$foodItem->menu->canEditByUser(Auth::user())) {
            return redirect()->back()
                ->with('error', __('messages.Unauthorized access'));
        }

        $foodItem->update([
            'is_available' => !$foodItem->is_available
        ]);

        $status = $foodItem->is_available ? 'available' : 'unavailable';
        
        return redirect()->back()
            ->with('success', __('messages.Food item status updated to :status', ['status' => $status]));
    }

    /**
     * Get food items by menu.
     */
    public function getByMenu(string $menuId)
    {
        $menu = Menu::with('restaurant')->findOrFail($menuId);

        // Kiểm tra quyền truy cập
        if (!$menu->canEditByUser(Auth::user())) {
            return redirect()->route('food-item.index')
                ->with('error', __('messages.Unauthorized access'));
        }

        $foodItems = FoodItem::where('menu_id', $menuId)
            ->orderBy('category')
            ->orderBy('name')
            ->paginate(15);

        return view('food-items.index', compact('foodItems', 'menu'));
    }

    /**
     * Get food items by category.
     */
    public function getByCategory(string $category)
    {
        $query = FoodItem::with(['menu.restaurant'])
            ->where('category', $category)
            ->when(Auth::check(), function ($query) {
                return $query->whereHas('menu.restaurant', function ($q) {
                    $q->where('user_id', Auth::id());
                });
            });

        $foodItems = $query->orderBy('name')->paginate(15);

        return view('food-items.index', compact('foodItems', 'category'));
    }

    /**
     * Update stock quantity.
     */
    public function updateStock(Request $request, string $id)
    {
        $foodItem = FoodItem::with('menu')->findOrFail($id);

        // Kiểm tra quyền truy cập
        if (!$foodItem->menu->canEditByUser(Auth::user())) {
            return redirect()->back()
                ->with('error', __('messages.Unauthorized access'));
        }

        $validator = Validator::make($request->all(), [
            'stock_quantity' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $foodItem->update([
            'stock_quantity' => $request->stock_quantity
        ]);

        return redirect()->back()
            ->with('success', __('messages.Stock quantity updated successfully'));
    }
}
