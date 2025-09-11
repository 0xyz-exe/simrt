<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $activities = Activity::with([])
            ->orderBy('start_date', 'desc')
            ->orderBy('start_time', 'asc')
            ->paginate(10);

        return view('activities.index', compact('activities'));
    }

    /**
     * Public listing for landing page
     */
    public function publicIndex()
    {
        $activities = Activity::active()
            ->upcoming()
            ->orderBy('start_date', 'asc')
            ->take(6)
            ->get();

        return view('landing.activities', compact('activities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('activities.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'location' => 'nullable|string|max:255',
            'type' => 'required|in:social,religious,community,sports,education,health,environment,meeting,other',
            'status' => 'nullable|in:draft,published,cancelled,completed',
            'max_participants' => 'nullable|integer|min:1',
            'current_participants' => 'nullable|integer|min:0',
            'requirements' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'participation_status' => 'nullable|in:open,closed,full,by_invitation',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_featured' => 'boolean'
        ]);

        $data = $request->all();
        $data['status'] = $data['status'] ?? 'draft';
        $data['is_active'] = true;

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('activities', 'public');
        }

        $data['is_featured'] = $request->has('is_featured');

        Activity::create($data);

        if (request()->is('admin/*')) {
            return redirect()->route('admin.activities.index')
                ->with('success', 'Kegiatan berhasil ditambahkan.');
        }

        return redirect()->route('activities.index')
            ->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Activity $activity)
    {
        // Check if this is an admin request
        if (request()->is('admin/*')) {
            return view('admin.activities.show', compact('activity'));
        }

        // For public view
        return view('activities.show', compact('activity'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Activity $activity)
    {
        if (request()->is('admin/*')) {
            return view('admin.activities.edit', compact('activity'));
        }

        return view('activities.edit', compact('activity'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Activity $activity)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'location' => 'nullable|string|max:255',
            'type' => 'required|in:social,religious,community,sports,education,health,environment,meeting,other',
            'status' => 'required|in:draft,published,cancelled,completed',
            'max_participants' => 'nullable|integer|min:1',
            'current_participants' => 'nullable|integer|min:0',
            'requirements' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'participation_status' => 'nullable|in:open,closed,full,by_invitation',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'is_featured' => 'boolean'
        ]);

        $data = $request->all();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($activity->image) {
                Storage::disk('public')->delete($activity->image);
            }
            $data['image'] = $request->file('image')->store('activities', 'public');
        }

        $data['is_active'] = $request->has('is_active');
        $data['is_featured'] = $request->has('is_featured');

        $activity->update($data);

        if (request()->is('admin/*')) {
            $redirect = match($request->input('save_action')) {
                'save_and_continue' => redirect()->route('admin.activities.edit', $activity),
                default => redirect()->route('admin.activities.index')
            };

            return $redirect->with('success', 'Kegiatan berhasil diperbarui.');
        }

        return redirect()->route('activities.index')
            ->with('success', 'Kegiatan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Activity $activity)
    {
        // Delete image file
        if ($activity->image) {
            Storage::disk('public')->delete($activity->image);
        }

        $activity->delete();

        if (request()->is('admin/*')) {
            return redirect()->route('admin.activities.index')
                ->with('success', 'Kegiatan berhasil dihapus.');
        }

        return redirect()->route('activities.index')
            ->with('success', 'Kegiatan berhasil dihapus.');
    }

    /**
     * Toggle activity status
     */
    public function toggleStatus(Activity $activity)
    {
        $activity->update([
            'is_active' => !$activity->is_active
        ]);

        return back()->with('success', 'Status kegiatan berhasil diubah.');
    }

    /**
     * Feature/unfeature activity
     */
    public function toggleFeatured(Activity $activity)
    {
        $activity->update([
            'is_featured' => !$activity->is_featured
        ]);

        return back()->with('success', 'Status unggulan berhasil diubah.');
    }
}
