<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\SocialMedia;
use GuzzleHttp\Psr7\Message;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articles = Article::all();
        //select * from article

        return view ('index', ['articles' => $articles]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view ('create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request)->all();
        $article = new Article();

        $article ->tajuk = $request->input('tajuk');
        $article ->tarikh_publish = $request->input('tarikh_publish');
        $article ->penulis = $request->input('penulis');
        $article ->kategori = $request->input('kategori');
        $article ->content = $request->input('editordata');
        $article ->about = $request->input('about');

        $article->save();

        $this->reassignSocial($request, $article);

        return redirect()->route('index')->with([
            'message' => 'Rekod Berjaya disimpan'
        ]);
    }

    public function reassignSocial($request, $article): void
    {
        $url = $request['url'];
        foreach ($request['jenis'] as $key => $value)  {
            $social = new SocialMedia();

            $social->article_id = $article->id;
            $social->jenis = $value;
            $social->url = $url[$key];

            $social->save();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $article = Article::with(['socialMedia'])->where('id', $id)->first();
        //dd($article);
        return view('edit', ['article' => $article]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //dd($request->all());

        $article = Article::find($id);

        $article ->tajuk = $request->input('tajuk');
        $article ->tarikh_publish = $request->input('tarikh_publish');
        $article ->penulis = $request->input('penulis');
        $article ->kategori = $request->input('kategori');
        $article ->content = $request->input('editordata');
        $article ->about = $request->input('about');

        $article->update();
        /**
         * update social media record
         */
        $this->reassignSocialUpdate($request->all(), $article);

        return redirect()->route('index')->with([
            'message' => 'Rekod Berjaya dikemaskini'
        ]);
    }

    public function reassignSocialUpdate($request, $article): void
    {
        $url = $request['url'];
        foreach ($request['jenis'] as $key => $value) {
            $social = SocialMedia::where('article_id', $article->id)
                        ->get();
            
            if(empty($social)) continue;

            foreach ($social as $key => $media) {
                // $media->jenis = $value;
                $media->url = $url[$key];

                $media->update();    
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //dd($id);
        $article = Article::find($id);
        $article->delete();

        return redirect()->route('index')->with([
            'message' => 'Rekod Berjaya dihapus'
        ]);
    }

    public function landing()
    {
        $articles = Article::all();
        return view('welcome', ['articles' => $articles]);

    }


    public function read($id)
    {
        
        $article = Article::with(['socialMedia'])->where('id', $id)->first();
        
        return view('read', ['article' => $article]);

    }


}
