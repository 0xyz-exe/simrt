<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BusinessController extends Controller
{
    public function index(Request $request)
    {
        $query = Business::with('resident');

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('owner_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $businesses = $query->orderBy('featured', 'desc')
                           ->orderBy('created_at', 'desc')
                           ->paginate(15);

        $categories = Business::getCategories();

        return view('admin.businesses.index', compact('businesses', 'categories'));
    }

    public function create()
    {
        $categories = Business::getCategories();
        $residents = Resident::with('household')->orderBy('nama')->get();

        return view('admin.businesses.create', compact('categories', 'residents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'owner_name' => 'required|string|max:255',
            'category' => 'required|string|in:' . implode(',', array_keys(Business::getCategories())),
            'address' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'instagram' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'operating_hours' => 'nullable|array',
            'operating_hours.*' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'featured' => 'boolean',
            'resident_id' => 'nullable|exists:residents,id'
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('businesses', 'public');
        }

        Business::create($validated);

        return redirect()->route('admin.businesses.index')
            ->with('success', 'UMKM berhasil ditambahkan.');
    }

    public function show(Business $business)
    {
        $business->load('resident');
        return view('admin.businesses.show', compact('business'));
    }

    public function edit(Business $business)
    {
        $categories = Business::getCategories();
        $residents = Resident::with('household')->orderBy('nama')->get();

        return view('admin.businesses.edit', compact('business', 'categories', 'residents'));
    }

    public function update(Request $request, Business $business)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'owner_name' => 'required|string|max:255',
            'category' => 'required|string|in:' . implode(',', array_keys(Business::getCategories())),
            'address' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'instagram' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'operating_hours' => 'nullable|array',
            'operating_hours.*' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'featured' => 'boolean',
            'resident_id' => 'nullable|exists:residents,id'
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($business->image) {
                Storage::disk('public')->delete($business->image);
            }
            $validated['image'] = $request->file('image')->store('businesses', 'public');
        }

        $business->update($validated);

        return redirect()->route('admin.businesses.index')
            ->with('success', 'UMKM berhasil diperbarui.');
    }

    public function destroy(Business $business)
    {
        if ($business->image) {
            Storage::disk('public')->delete($business->image);
        }

        $business->delete();

        return redirect()->route('admin.businesses.index')
            ->with('success', 'UMKM berhasil dihapus.');
    }

    public function toggleStatus(Business $business)
    {
        $business->update(['is_active' => !$business->is_active]);

        $status = $business->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "UMKM berhasil {$status}.");
    }

    public function toggleFeatured(Business $business)
    {
        $business->update(['featured' => !$business->featured]);

        $status = $business->featured ? 'ditampilkan' : 'disembunyikan';

        return back()->with('success', "UMKM berhasil {$status} dari unggulan.");
    }
}
