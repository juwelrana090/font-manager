<?php

namespace App\Http\Controllers;

use App\Models\Font;
use App\Models\FontGroup;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Font",
 *     type="object",
 *     title="Font",
 *     properties={
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="title", type="string", example="Arial"),
 *         @OA\Property(property="path", type="string", example="/path/to/font.ttf"),
 *         @OA\Property(property="font_url", type="string", example="http://example.com/path/to/font.ttf")
 *     }
 * )
 * @OA\Schema(
 *     schema="FontGroup",
 *     type="object",
 *     title="FontGroup",
 *     properties={
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="My Font Group"),
 *         @OA\Property(
 *             property="fonts",
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Font")
 *         )
 *     }
 * )
 */

class FontGroupController extends Controller
{
    /**
     * @OA\Get(
     *     path="/font-group",
     *     summary="Get list of font groups",
     *     tags={"Font Groups"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="font_groups",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/FontGroup")),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=10),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="to", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=100)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function index()
    {
        try {
            $fontGroups = Cache::remember('font_groups', 60, function () {
                return FontGroup::with(['fonts:id,title,path']) // Exclude font_group_id
                    ->paginate(10);
            });

            // Add font_url to each font using collection methods
            $fontGroups->getCollection()->transform(function ($fontGroup) {
                $fontGroup->fonts->transform(function ($font) {
                    $font->font_url = url($font->path); // Add font_url
                    return $font;
                });
                return $fontGroup;
            });

            // Customize the pagination structure
            $paginatedData = [
                'current_page' => $fontGroups->currentPage(),
                'data' => $fontGroups->items(),
                'from' => $fontGroups->firstItem(),
                'last_page' => $fontGroups->lastPage(),
                'per_page' => $fontGroups->perPage(),
                'to' => $fontGroups->lastItem(),
                'total' => $fontGroups->total(),
            ];

            return response()->json(['success' => true, 'font_groups' => $paginatedData], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/font-group",
     *     summary="Create a new font group",
     *     tags={"Font Groups"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="New Font Group"),
     *             @OA\Property(
     *                 property="fonts",
     *                 type="array",
     *                 @OA\Items(type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Font group created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/FontGroup")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            // Validate request input
            $request->validate([
                'name' => 'required|string|max:255',
                'fonts' => 'array|nullable', // Ensure 'fonts' is optional
                'fonts.*' => 'exists:fonts,id', // Validate each font ID exists
            ]);

            // Begin transaction
            DB::transaction(function () use ($request, &$fontGroup) {
                // Create the font group
                $fontGroup = FontGroup::create(['name' => $request->name]);

                // Attach fonts if provided
                if ($request->filled('fonts')) {
                    $fontGroup->fonts()->attach($request->fonts);
                }
            });

            return response()->json(['success' => true, 'font_group' => $fontGroup], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/font-group/{id}",
     *     summary="Get a font group by ID",
     *     tags={"Font Groups"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/FontGroup")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Font group not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function show(FontGroup $fontGroup)
    {
        try {
            // Eager load associated fonts
            $fontGroup->load('fonts');

            // Transform the output to exclude pivot data
            $formattedFonts = $fontGroup->fonts->map(function ($font) {
                return [
                    'id' => $font->id,
                    'title' => $font->title,
                    'path' => $font->path,
                    'font_url' => $font->font_url,
                ];
            });

            return response()->json([
                'success' => true,
                'font_group' => [
                    'id' => $fontGroup->id,
                    'name' => $fontGroup->name,
                    'fonts' => $formattedFonts,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/font-group/{id}",
     *     summary="Update a font group",
     *     tags={"Font Groups"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Updated Font Group"),
     *             @OA\Property(
     *                 property="fonts",
     *                 type="array",
     *                 @OA\Items(type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Font group updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/FontGroup")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Font group not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            // Validate request input
            $request->validate([
                'name' => 'required|string|max:255',
                'fonts' => 'array|nullable',
                'fonts.*' => 'exists:fonts,id',
            ], [
                'fonts.*.exists' => 'The selected font ID :input is invalid.',
            ]);

            // Find the font group with eager loading
            $fontGroup = FontGroup::with('fonts')->findOrFail($id);

            // Update the font group name
            $fontGroup->update(['name' => $request->name]);

            // Sync fonts with the font group
            if ($request->has('fonts')) {
                // Filter valid font IDs
                $validFontIds = Font::whereIn('id', $request->fonts)->pluck('id')->toArray();
                $fontGroup->fonts()->sync($validFontIds);
            } else {
                $fontGroup->fonts()->detach(); // Detach if no fonts are provided
            }

            return response()->json([
                'success' => true,
                'font_group' => $fontGroup->load('fonts'),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Font group not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/font-group/{id}",
     *     summary="Delete a font group",
     *     tags={"Font Groups"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Font group deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Font group deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Font group not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            // Find the font group or fail
            $fontGroup = FontGroup::findOrFail($id);

            // Begin transaction
            DB::transaction(function () use ($fontGroup) {
                // Detach all fonts from the font group
                $fontGroup->fonts()->detach();

                // Delete the font group
                $fontGroup->delete();
            });

            return response()->json(['success' => true, 'message' => 'Font group deleted successfully.'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Font group not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
