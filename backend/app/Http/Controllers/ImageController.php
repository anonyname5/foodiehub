<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Restaurant;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    /**
     * Upload images for a restaurant, review, or user avatar.
     */
    public function upload(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:restaurant,review,user',
            'id' => 'required|integer',
            'images' => 'required|array|min:1|max:10',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
            'primary_index' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $type = $request->input('type');
        $id = $request->input('id');
        $primaryIndex = $request->input('primary_index', 0);

        // Find the model
        if ($type === 'restaurant') {
            $model = Restaurant::find($id);
        } elseif ($type === 'review') {
            $model = Review::find($id);
        } else { // user
            $model = User::find($id);
        }

        if (!$model) {
            return response()->json([
                'success' => false,
                'message' => ucfirst($type) . ' not found'
            ], 404);
        }

        $uploadedImages = [];
        $errors = [];

        // Handle user avatar uploads differently
        if ($type === 'user') {
            // For users, we only allow one avatar image
            $file = $request->file('images')[0]; // Take the first image only
            
            try {
                // Generate unique filename
                $extension = $file->getClientOriginalExtension();
                $filename = Str::uuid() . '.' . $extension;
                
                // Store the file in users folder
                $storedPath = $file->storeAs('public/users', $filename);
                // Storage::url() returns /storage/users/... 
                $url = Storage::url($storedPath);
                
                // Store just the relative path (without /storage/) in database
                // Format: "users/filename.png"
                $pathForDb = 'users/' . $filename;
                $model->update(['avatar' => $pathForDb]);
                
                // Return a consistent format with full URL for immediate use
                $uploadedImages[] = [
                    'id' => $model->id,
                    'url' => $url, // Full URL like /storage/users/... for immediate use
                    'path' => $pathForDb, // Relative path like users/... for database
                    'filename' => $filename,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'type' => 'user_avatar'
                ];
                
            } catch (\Exception $e) {
                $errors[] = [
                    'file' => $file->getClientOriginalName(),
                    'error' => $e->getMessage()
                ];
            }
        } else {
            // Handle restaurant and review images (existing logic)
            foreach ($request->file('images') as $index => $file) {
                try {
                    // Generate unique filename
                    $extension = $file->getClientOriginalExtension();
                    $filename = Str::uuid() . '.' . $extension;

                    // Store the file
                    $storedPath = $file->storeAs('public/' . $type . 's', $filename);

                    // Create image record
                    $image = Image::create([
                        'imageable_id' => $id,
                        'imageable_type' => $type === 'restaurant' ? Restaurant::class : Review::class,
                        'filename' => $filename,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'path' => $type . 's/' . $filename,
                        'url' => Storage::url($storedPath),
                        'is_primary' => $index === $primaryIndex,
                        'sort_order' => $index,
                    ]);

                    $uploadedImages[] = $image;
                } catch (\Exception $e) {
                    $errors[] = [
                        'file' => $file->getClientOriginalName(),
                        'error' => $e->getMessage()
                    ];
                }
            }
        }

        if (empty($uploadedImages)) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload any images',
                'errors' => $errors
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Images uploaded successfully',
            'images' => $uploadedImages,
            'errors' => $errors
        ]);
    }

    /**
     * Delete an image.
     */
    public function destroy(Request $request, $id)
    {
        $image = Image::find($id);

        if (!$image) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Image not found'
                ], 404);
            }
            return redirect()->back()->with('error', 'Image not found');
        }

        try {
            // Delete file from storage
            Storage::delete('public/' . $image->path);

            // Delete database record
            $image->delete();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Image deleted successfully'
                ]);
            }
            
            return redirect()->back()->with('success', 'Image deleted successfully');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete image',
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to delete image: ' . $e->getMessage());
        }
    }

    /**
     * Set primary image.
     */
    public function setPrimary($id): JsonResponse
    {
        $image = Image::find($id);

        if (!$image) {
            return response()->json([
                'success' => false,
                'message' => 'Image not found'
            ], 404);
        }

        try {
            // Remove primary from all other images of the same model
            Image::where('imageable_id', $image->imageable_id)
                ->where('imageable_type', $image->imageable_type)
                ->update(['is_primary' => false]);

            // Set this image as primary
            $image->update(['is_primary' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Primary image updated successfully',
                'data' => $image
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update primary image',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reorder images.
     */
    public function reorder(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'images' => 'required|array',
            'images.*.id' => 'required|integer|exists:images,id',
            'images.*.sort_order' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            foreach ($request->input('images') as $imageData) {
                Image::where('id', $imageData['id'])
                    ->update(['sort_order' => $imageData['sort_order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Images reordered successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder images',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

