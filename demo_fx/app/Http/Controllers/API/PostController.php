<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index()
    {
        return response()->json([
            'Posts: '=>Post::get()
        ]);
    }


    public function create(Request $request)
    {
    // Check if the user is authenticated
    if (Auth::guard('api')->check()) {
        // Get the authenticated user
        $user = Auth::guard('api')->user();

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:255',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'error' => $validator->messages()
            ], 422);
        }

        // Create a new post instance
        $post = new Post();
        $post->title = $request->title;
        $post->description = $request->description;
        $post->category = $request->category;
        $post->user_id = $user->id; // Assign the authenticated user's id

        // Save the post to the database
        $post->save();

        // Return success response
        return response()->json([
            'message' => 'Post Created Successfully',
            'status' => 'success',
            'data' => $post
        ], 201); // HTTP status code for Created
    } else {
        // If the user is not authenticated, return unauthorized response
        return response()->json([
            'message' => 'User not authenticated',
            'status' => 'error'
        ], 401); // Unauthorized status code
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return response() -> json(['post' => $post]);
    }

    /**
     * Update the specified resource in storage.
     */


    public function update(Request $request, Post $post)
    {
    // Check if the user is authenticated
    if (Auth::guard('api')->check()) {
        // Get the authenticated user
        $user = Auth::guard('api')->user();

        // Check if the authenticated user owns the post
        if ($post->user_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized',
                'status' => 'error'
            ], 403); // Forbidden status code
        }

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:255',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'error' => $validator->messages()
            ], 422);
        }

        // Update the post attributes
        $post->title = $request->title;
        $post->description = $request->description;
        $post->category = $request->category;

        // Save the updated post to the database
        $post->save();

        // Return success response
        return response()->json([
            'message' => 'Post Updated Successfully',
            'status' => 'success',
            'data' => $post
        ]);
    } else {
        // If the user is not authenticated, return unauthorized response
        return response()->json([
            'message' => 'User not authenticated',
            'status' => 'error'
        ], 401); // Unauthorized status code
        }
    }
//get message by user_id
    public function getPostByUserId($userId)
    {
    // Retrieve posts/messages associated with the provided user ID
    $post = Post::where('user_id', $userId)->get();

    // Check if any messages are found
    if ($post->isEmpty()) {
        // Return response for no messages found
        return response()->json([
            'message' => 'No post found for the provided user ID',
            'status' => 'success'
        ]);
    }

    // Return the posts/messages in the JSON response
    return response()->json([
        'post' => $post,
        'status' => 'success'
    ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post -> delete();
        return response() -> json([
            'message' => 'Post removed Successfully',
            'status' => 'success'
        ]);
    }
}
