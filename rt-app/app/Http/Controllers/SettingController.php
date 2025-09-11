<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settingGroups = Setting::orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        return view('settings.index', compact('settingGroups'));
    }

    public function update(Request $request)
    {
        $settings = $request->except(['_token', '_method']);

        foreach ($settings as $key => $value) {
            $setting = Setting::where('key', $key)->first();

            if ($setting) {
                // Handle file upload for image type
                if ($setting->type === 'image' && $request->hasFile($key)) {
                    // Delete old image
                    if ($setting->value) {
                        Storage::disk('public')->delete($setting->value);
                    }

                    $value = $request->file($key)->store('settings', 'public');
                }

                // Handle boolean conversion
                if ($setting->type === 'boolean') {
                    $value = $request->has($key) ? '1' : '0';
                }

                $setting->update(['value' => $value]);
            }
        }

        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan berhasil diperbarui.');
    }

    public function create()
    {
        return view('settings.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|unique:settings,key',
            'label' => 'required|string',
            'type' => 'required|in:text,number,boolean,image,rich_text',
            'group' => 'required|string',
            'value' => 'nullable',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_public' => 'boolean'
        ]);

        $data = $request->all();
        $data['is_public'] = $request->has('is_public');

        // Handle file upload for image type
        if ($request->input('type') === 'image' && $request->hasFile('value')) {
            $data['value'] = $request->file('value')->store('settings', 'public');
        }

        Setting::create($data);

        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan berhasil ditambahkan.');
    }

    public function destroy(Setting $setting)
    {
        // Delete image file if exists
        if ($setting->type === 'image' && $setting->value) {
            Storage::disk('public')->delete($setting->value);
        }

        $setting->delete();

        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan berhasil dihapus.');
    }
}
