<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Post::where('status', 'published')
            ->latest('published_at')
            ->with('author', 'categories')
            ->paginate(9);

        $featuredPost = Post::where('status', 'published')
            ->latest('published_at')
            ->first();

        return view('blog.index', [
            'posts' => $posts,
            'featuredPost' => $featuredPost,
        ]);
    }

    public function show(Post $post)
    {
        if ($post->status !== 'published' && !(auth()->check() && auth()->user()->is_admin)) {
            abort(404);
        }

        $post->load('author', 'categories');

        // Получаем похожие посты (например, 3 случайных из той же категории)
        $relatedPosts = collect();
        if ($post->categories->isNotEmpty()) {
            $relatedPosts = Post::whereHas('categories', function ($query) use ($post) {
                $query->where('id', $post->categories->first()->id);
            })
                ->where('id', '!=', $post->id)
                ->where('status', 'published')
                ->with('author', 'categories')
                ->limit(3)
                ->get();
        }

        return view('blog.show', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
        ]);
    }
}

