<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Content;
use App\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return array
     */
    public function index(Request $request)
    {
        $contents = Content::with('user')->orderBy('data','DESC')->paginate(5);
        $user = $request->user();

        foreach ($contents as $content){
            $content->total_likes = $content->likes()->count();
            $content->comments = $content->comments()->with('user')->get();
            $like = $user->likes()->find($content->id);
            $content->like_content = $like ? true : false;
        }

        return [ 'status' => true, 'contents' => $contents ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param User $user
     * @param Request $request
     * @return array
     */
    public function page(User $user, Request $request)
    {
        if ($user){
            $contents = $user->contents()->with('user')->orderBy('data','DESC')->paginate(5);
            $userLogado = $request->user();

            foreach ($contents as $content){
                $content->total_likes = $content->likes()->count();
                $content->comments = $content->comments()->with('user')->get();
                $like = $userLogado->likes()->find($content->id);
                $content->like_content = $like ? true : false;
            }

            return [ 'status' => true, 'contents' => $contents,'userPage'=> $user, 'logado'=> $userLogado ];
        }else{
            return [ 'status' => false, 'error' => 'Usuario não existe' ];
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $user = $request->user();

        //validate
        $validate = Validator::make($data, [
            'title' => 'required',
            'text' => 'required'
        ]);

        if($validate->fails()){
            return [
                "status" => false,
                "validate" => true,
                "errors" => $validate->errors()
            ];
        }

        $content = new Content();
        $content->title = $data['title'];
        $content->text = $data['text'];
        $content->image = $data['image'] ? $data['image'] : '#';
        $content->link = $data['link'] ? $data['link'] : '#';
        $content->data = date('Y-m-d H:i:s');

        $user->contents()->save($content);

        $contents = Content::with('user')->orderBy('data','DESC')->paginate(5);

        return [ 'status' => true, 'contents' => $contents ];
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Content $content
     */
    public function like(Request $request, Content $content)
    {
        if ($content){
            $user = $request->user();
            $user->likes()->toggle($content->id);
            return [
                'status' => true,
                'likes' => $content->likes()->count(),
                'list' => $this->index($request)
                ];
        }else{
            return [ 'status' => false, 'error' => 'Conteudo não existe' ];
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param Content $content
     * @return array
     */
    public function comments(Request $request,Content $content)
    {
        $data = $request->all();
        $user = $request->user();


        if ($content){
            $comment = new Comment();
            $comment->user_id = $user->id;
            $comment->content_id = $content->id;
            $comment->text = $data['texto'];
            $comment->data = date('Y-m-d H:i:s');
            $comment->save();
            return [
                'status' => true,
                'list' => $this->index($request)
            ];
        }else{
            return [ 'status' => false, 'error' => 'Conteudo não existe' ];
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Content $content)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function destroy(Content $content)
    {
        //
    }
}
