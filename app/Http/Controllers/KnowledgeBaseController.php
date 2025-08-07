<?php
namespace App\Http\Controllers;
use App\Models\Article;
use Illuminate\Http\Request;

class KnowledgeBaseController extends Controller
{
    // Menampilkan halaman daftar semua artikel yang sudah dipublikasikan
    public function index()
    {
        $articles = Article::where('status', 'published')->latest()->paginate(10);
        return view('kb.index', compact('articles'));
    }

    // Menampilkan satu artikel
    public function show(Article $article)
    {
        // Pastikan hanya artikel yang sudah 'published' yang bisa dilihat
        if ($article->status !== 'published') {
            abort(404);
        }
        return view('kb.show', compact('article'));
    }
}