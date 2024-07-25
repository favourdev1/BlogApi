<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = auth()->id();
        $blogs = Blog::where('user_id', $userId)->get();
        return response()->json([
            'status' => 'success',
            'data' => [
                'blogs' => $blogs
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate request using validator
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048', // Add validation for image
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' =>  implode(', ', $validator->errors()->all())
            ], 422);
        }

        $userId = Auth::user()->id;

        $imagePath = null;

        // Handle file upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('images', 'public'); // Store the image in the 'public/images' directory
        }

        // Create blog
        $blog = Blog::create([
            'title' => $request->title,
            'description' => $request->description,
            'image_url' => $imagePath, // Save the image path
            'user_id' => $userId
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Blog created successfully',
            'data' => [
                'blog' => $blog
            ]
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Validate request using validator
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|exists:blogs,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' =>  implode(', ', $validator->errors()->all())
            ], 422);
        }

       
        $blog = Blog::find($id);

        // Check if the authenticeated user is the owner 
        //enable this  yoru want the user to be the only one to view the content of a particular blog 
        // if (auth()->user()->id !== $blog->user_id) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'You do not have permission to view this blog post'
        //     ], 403);
        // }

   
        return response()->json([
            'status' => 'success',
            'message' => 'Blog retrieved successfully',
            'data' => [
                'blog' => $blog
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $userId = auth()->id();
    
        // Validate request using validator
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'image_url' => 'nullable|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' =>  implode(', ', $validator->errors()->all())
            ], 422);
        }
    
        // Find the blog that belongs to the user
        $blog = Blog::where('id', $id)->where('user_id', $userId)->first();
        if (!$blog) {
            return response()->json([
                'status' => 'error',
                'message' => 'Blog not found or you do not have permission to update it'
            ], 404);
        }
    
        // Update only the fields that are present in the request
        $blog->fill($request->only(['title', 'description', 'image_url']));
        $blog->save();
    
        return response()->json([
            'status' => 'success',
            'message' => 'Blog updated successfully',
            'data' => [
                'blog' => $blog
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $userId = auth()->id();

        // Validate request using validator
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|exists:blogs,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' =>  implode(', ', $validator->errors()->all())
            ], 422);
        }

        $blog = Blog::where('id', $id)->where('user_id', $userId)->first();
        if (!$blog) {
            return response()->json([
                'status' => 'error',
                'message' => 'Blog not found or you do not have permission to delete it'
            ], 404);
        }

        $blog->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Blog deleted successfully'
        ]);
    }
}
