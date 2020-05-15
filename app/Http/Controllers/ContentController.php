<?php

namespace App\Http\Controllers;

use App\Content;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return array
     */
    public function index()
    {
        $contents = Content::with('user')->orderBy('data','DESC')->paginate(5);

        return [ 'status' => true, 'contents' => $contents ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            return [ 'status' => true, 'likes' => $content->likes()->count() ];
        }else{
            return [ 'status' => false, 'error' => 'Conteudo não existe' ];
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function edit(Content $content)
    {
        //
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
