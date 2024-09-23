<?php

namespace App\Http\Controllers;

use App\Models\Font;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *    title="Fonts Management API",
 *    version="1.0.0",
 *    description="API Endpoints for managing fonts",
 * )
 * @OA\Server(
 *      url="https://font-backend.codingzonebd.com/api/v1",
 *      description="Development server"
 * ),
 * @OA\PathItem(path="/font")
 */
class FontController extends Controller
{
    /**
     * @OA\Get(
     *     path="/font",
     *     summary="Retrieve a list of fonts",
     *     tags={"Fonts"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="The page number to retrieve",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of fonts list",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="fonts", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="title", type="string", example="Example Font"),
     *                         @OA\Property(property="path", type="string", example="fonts/example-font.ttf"),
     *                         @OA\Property(property="font_url", type="string", example="http://example.com/fonts/example-font.ttf")
     *                     )
     *                 ),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=10),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="to", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=100)
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $fonts = Cache::remember('fonts_page_' . request('page', 1), 60, function () {
            $fonts = Font::latest()->paginate(10, ['id', 'title', 'slug', 'path']);
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

    /**
     * @OA\Post(
     *     path="/font/upload",
     *     summary="Upload a new font",
     *     tags={"Fonts"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="font",
     *                     description="The TTF font file",
     *                     type="string",
     *                     format="binary"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Font uploaded successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Font uploaded successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Font already exists",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Font already exists")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Upload failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Font upload failed")
     *         )
     *     )
     * )
     */
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
                return response()->json([
                    'success' => false,
                    'message' => 'Font already exists',
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

            return response()->json([
                'success' => true,
                'message' => 'Font uploaded successfully',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * @OA\Delete(
     *     path="/font/{id}",
     *     summary="Delete a font",
     *     tags={"Fonts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the font to be deleted",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Font deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Font deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Font not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Font not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An error occurred",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred")
     *         )
     *     )
     * )
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

            // Return the updated font list
            return response()->json([
                'success' => true,
                'message' => 'Font deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
