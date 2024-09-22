<?php

namespace App\Http\Controllers;

use App\Models\Font;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class FontController extends Controller
{
    public function index()
    {
        $fonts = Cache::remember('fonts_page_' . request('page', 1), 60, function () {
            $fonts = Font::latest()->paginate(10, ['id', 'title', 'path']);
            $fonts->getCollection()->transform(function ($font) {
                $font->font_url = url($font->path);
                return $font;
            });
            return $fonts;
        });

        // Customize the pagination structure
        $paginatedData = [
            'current_page' => $fonts->currentPage(),
            'data' => $fonts->items(),
            'from' => $fonts->firstItem(),
            'last_page' => $fonts->lastPage(),
            'per_page' => $fonts->perPage(),
            'to' => $fonts->lastItem(),
            'total' => $fonts->total(),
        ];

        return [
            'status' => true,
            'fonts' => $paginatedData
        ];
    }

    public function store(Request $request)
    {
        try {
            // Validate the incoming request without MIME type check for better performance
            $request->validate([
                'font' => 'required|file|max:2048',
            ]);

            // Check if file exists
            if (!$request->hasFile('font')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Font upload failed',
                ], 500);
            }

            $file = $request->file('font');

            // Check if the file extension is 'ttf' (case insensitive)
            if (strtolower($file->getClientOriginalExtension()) !== 'ttf') {
                return response()->json([
                    'success' => false,
                    'message' => 'The uploaded file must be a .ttf file.',
                ]);
            }

            $fileName = $file->getClientOriginalName();
            $fileSlug = Str::slug(pathinfo($fileName, PATHINFO_FILENAME)) . '.ttf';

            // Check if a font with the same slug already exists
            $fontExists = Font::where('slug', $fileSlug)->exists();
            if ($fontExists) {
                $fonts = Cache::remember('fonts_page_' . request('page', 1), 60, function () {
                    $fonts = Font::latest()->paginate(10, ['id', 'title', 'path']);
                    $fonts->getCollection()->transform(function ($font) {
                        $font->font_url = url($font->path);
                        return $font;
                    });
                    return $fonts;
                });

                // Customize the pagination structure
                $paginatedData = [
                    'current_page' => $fonts->currentPage(),
                    'data' => $fonts->items(),
                    'from' => $fonts->firstItem(),
                    'last_page' => $fonts->lastPage(),
                    'per_page' => $fonts->perPage(),
                    'to' => $fonts->lastItem(),
                    'total' => $fonts->total(),
                ];

                return response()->json([
                    'success' => false,
                    'message' => 'Font already exists',
                    'fonts' => $paginatedData,
                ], 409);
            }

            // Create directory if it doesn't exist
            $path = public_path('fonts');
            if (!File::isDirectory($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            // Move the file to the designated location
            $filePath = $file->move($path, $fileSlug);
            $fileLocation = 'fonts/' . $fileSlug;

            // Save font details to the database
            $fontModel = new Font();
            $fontModel->title = pathinfo($fileName, PATHINFO_FILENAME);
            $fontModel->slug = $fileSlug;
            $fontModel->path = $fileLocation;
            $fontModel->save();

            $fonts = Cache::remember('fonts_page_' . request('page', 1), 60, function () {
                $fonts = Font::latest()->paginate(10, ['id', 'title', 'path']);
                $fonts->getCollection()->transform(function ($font) {
                    $font->font_url = url($font->path);
                    return $font;
                });
                return $fonts;
            });

            // Customize the pagination structure
            $paginatedData = [
                'current_page' => $fonts->currentPage(),
                'data' => $fonts->items(),
                'from' => $fonts->firstItem(),
                'last_page' => $fonts->lastPage(),
                'per_page' => $fonts->perPage(),
                'to' => $fonts->lastItem(),
                'total' => $fonts->total(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Font uploaded successfully',
                'fonts' => $paginatedData,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $font = Font::find($id);

            if (!$font) {
                return response()->json([
                    'success' => false,
                    'message' => 'Font not found',
                ], 404);
            }

            // Directly delete the file without checking if it exists
            $path = public_path($font->path);
            @File::delete($path); // Suppressing errors with @ can save a few microseconds

            // Delete the font record
            $font->delete();

            $fonts = Cache::remember('fonts_page_' . request('page', 1), 60, function () {
                $fonts = Font::latest()->paginate(10, ['id', 'title', 'path']);
                $fonts->getCollection()->transform(function ($font) {
                    $font->font_url = url($font->path);
                    return $font;
                });
                return $fonts;
            });

            // Customize the pagination structure
            $paginatedData = [
                'current_page' => $fonts->currentPage(),
                'data' => $fonts->items(),
                'from' => $fonts->firstItem(),
                'last_page' => $fonts->lastPage(),
                'per_page' => $fonts->perPage(),
                'to' => $fonts->lastItem(),
                'total' => $fonts->total(),
            ];

            // Return the updated font list
            return response()->json([
                'success' => true,
                'message' => 'Font deleted successfully',
                'fonts' => $paginatedData,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
