<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Blog;
use App\Models\Comment;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of posts under a specific blog.
     */
    public function index($blogId)
    {
        $blog = Blog::find($blogId);

        if (!$blog) {
            return response()->json([
                'status' => 'error',
                'message' => 'Blog not found'
            ], 404);
        }

        $posts = $blog->posts;

        return response()->json([
            'status' => 'success',
            'data' => [
                'posts' => $posts
            ]
        ]);
    }

    /**
     * Store a newly created post under a specific blog.
     */
    public function store(Request $request, $blogId)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => implode(', ', $validator->errors()->all())
            ], 422);
        }

        // Ensure the blog exists
        $blog = Blog::find($blogId);
        if (!$blog) {
            return response()->json([
                'status' => 'error',
                'message' => 'Blog not found'
            ], 404);
        }

        // Handle file upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('images', 'public');
        }

        // Create post
        $post = $blog->posts()->create([
            'title' => $request->title,
            'content' => $request->content,
            'image_url' => $imagePath,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Post created successfully',
            'data' => $post
        ], 201);
    }

    /**
     * Display the specified post.
     */
    public function show($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'status' => 'error',
                'message' => 'Post not found'
            ], 404);
        }

        // Assuming you have likes and comments relationship set up
        $post->load('likes', 'comments');

        return response()->json([
            'status' => 'success',
            'data' => $post
        ]);
    }

    /**
     * Update the specified post.
     */
    public function update(Request $request, $id)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => implode(', ', $validator->errors()->all())
            ], 422);
        }

        // Find post
        $post = Post::find($id);
        if (!$post) {
            return response()->json([
                'status' => 'error',
                'message' => 'Post not found'
            ], 404);
        }

        // Handle file upload
        $imagePath = $post->image_url; 
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($imagePath && Storage::exists('public/' . $imagePath)) {
                Storage::delete('public/' . $imagePath);
            }

            $image = $request->file('image');
            $imagePath = $image->store('images', 'public');
        }

        // Update post
        $post->update([
            'title' => $request->title,
            'content' => $request->content,
            'image_url' => $imagePath,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Post updated successfully',
            'data' => $post
        ]);
    }

    /**
     * Remove the specified post from storage.
     */
    public function destroy($id)
    {
        // Find and delete post
        $post = Post::find($id);
        if (!$post) {
            return response()->json([
                'status' => 'error',
                'message' => 'Post not found'
            ], 404);
        }

        // Delete image if exists
        if ($post->image_url && Storage::exists('public/' . $post->image_url)) {
            Storage::delete('public/' . $post->image_url);
        }

        $post->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Post deleted successfully'
        ]);
    }


    /**
     * Comment on a specific post.
     */
    public function commentOnPost(Request $request, $postId)
    {
        $user = Auth::user(); 
        $post = Post::find($postId);

        if (!$post) {
            return response()->json([
                'status' => 'error',
                'message' => 'Post not found'
            ], 404);
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'content' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => implode(', ', $validator->errors()->all())
            ], 422);
        }

        // Create a new comment
        $comment = Comment::create([
            'post_id' => $postId,
            'user_id' => $user->id,
            'content' => $request->content
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Comment added successfully',
            'data' => $comment
        ], 201);
    }


    /**
     * Like a specific post.
     */
    public function likePost(Request $request, $postId)
    {
        $user = Auth::user(); 
        $post = Post::find($postId);

        if (!$post) {
            return response()->json([
                'status' => 'error',
                'message' => 'Post not found'
            ], 404);
        }

        // Check if the user has already liked the post
        $like = Like::where('post_id', $postId)
            ->where('user_id', $user->id)
            ->first();

        if ($like) {
            return response()->json([
                'status' => 'error',
                'message' => 'You have already liked this post'
            ], 400);
        }

        // Create a new like
        $like = Like::create([
            'post_id' => $postId,
            'user_id' => $user->id
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Post liked successfully',
            'data' => $like
        ], 201);
    }
}
