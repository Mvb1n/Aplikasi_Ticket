<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::with('user')->latest()->paginate(15);
        return view('admin.articles.index', compact('articles'));
    }

    public function create()
    {
        return view('admin.articles.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255|unique:articles',
            'content' => 'required|string',
            'status' => 'required|in:published,draft',
        ]);

        Article::create([
            'user_id' => Auth::id(),
            'title' => $validatedData['title'],
            'content' => $validatedData['content'],
            'status' => $validatedData['status'],
        ]);

        return redirect()->route('admin.articles.index')->with('success', 'Artikel baru berhasil dibuat.');
    }

    public function edit(Article $article)
    {
        return view('admin.articles.edit', compact('article'));
    }

    public function update(Request $request, Article $article)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255|unique:articles,title,' . $article->id,
            'content' => 'required|string',
            'status' => 'required|in:published,draft',
        ]);

        $article->update([
            'title' => $validatedData['title'],
            'content' => $validatedData['content'],
            'status' => $validatedData['status'],
        ]);

        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil dihapus.');
    }
}
