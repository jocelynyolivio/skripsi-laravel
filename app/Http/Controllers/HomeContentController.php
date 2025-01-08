<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\HomeContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeContentController extends Controller
{
    public function index()
    {
        $contents = HomeContent::all();
        return view('dashboard.home_content.index', compact('contents'));
    }

    public function create()
    {
        return view('dashboard.home_content.create');
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'carousel_image' => 'image|nullable',
        'carousel_text' => 'string|nullable',
        'welcome_title' => 'string|nullable',
        'welcome_message' => 'string|nullable',
        'about_text' => 'string|nullable',
        'about_image' => 'image|nullable',
        'services_text' => 'string|nullable',
    ]);

    // Handle file upload for carousel_image
    if ($request->hasFile('carousel_image')) {
        $validated['carousel_image'] = $request->file('carousel_image')->store('home-images', 'public');
    }

    // Handle file upload for about_image
    if ($request->hasFile('about_image')) {
        $validated['about_image'] = $request->file('about_image')->store('home-images', 'public');
    }

    HomeContent::create($validated);

    return redirect()->route('dashboard.home_content.index')->with('success', 'Content added successfully.');
}



public function edit(HomeContent $homeContent)
{
    return view('dashboard.home_content.edit', ['content' => $homeContent]);
}


public function update(Request $request, HomeContent $homeContent)
{
    $validated = $request->validate([
        'carousel_image' => 'image|nullable',
        'carousel_text' => 'string|nullable',
        'welcome_title' => 'string|nullable',
        'welcome_message' => 'string|nullable',
        'about_text' => 'string|nullable',
        'about_image' => 'image|nullable',
        'services_text' => 'string|nullable',
    ]);

    // Handle file upload for carousel_image
    if ($request->hasFile('carousel_image')) {
        if ($homeContent->carousel_image) {
            Storage::disk('public')->delete($homeContent->carousel_image);
        }
        $validated['carousel_image'] = $request->file('carousel_image')->store('home-images', 'public');
    }

    // Handle file upload for about_image
    if ($request->hasFile('about_image')) {
        if ($homeContent->about_image) {
            Storage::disk('public')->delete($homeContent->about_image);
        }
        $validated['about_image'] = $request->file('about_image')->store('home-images', 'public');
    }

    $homeContent->update($validated);

    return redirect()->route('dashboard.home_content.index')->with('success', 'Content updated successfully.');
}



    public function destroy(HomeContent $homeContent)
    {
        if ($homeContent->carousel_image) {
            Storage::disk('public')->delete($homeContent->carousel_image);
        }
        $homeContent->delete();

        return redirect()->route('dashboard.home_content.index')->with('success', 'Content deleted successfully.');
    }
}
