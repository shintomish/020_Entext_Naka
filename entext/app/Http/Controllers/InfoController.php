<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Image; // intervention/imageライブラリの読み込み

class InfoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // ログイン情報
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $resize01 = $this->resize01();
        // $resize02 = $this->resize02();
        // $resize03 = $this->resize03();
        // $resize04 = $this->resize04();
        return view('info.index');
    }

    // shopp01写真を読み込み加工する
    public function resize01()
    {
        // 読み込み
        $path = public_path('images_sample/shopp/shopp03.jpg');
        $img = Image::make($path);

        $img->resize(500,500); // 写真をリサイズする
        // $img->fit(500,500); // 写真をリサイズする
        // $img->resize(500, 500, function ($constraint) {
        //     $constraint->aspectRatio();
        // });
        // $img->flip();   // 写真を反転させる

        //保存
        $filename = "shopp03_flip.jpg";
        $save_path = public_path("images_sample/shopp/". $filename);
        $img->save($save_path);

    }

    // shopp02写真を読み込み加工する
    public function resize02()
    {
        // 読み込み
        $path = public_path('images_sample/shopp/shopp02.png');
        $img = Image::make($path);

        // $img->resize(500,500); // 写真をリサイズする
        $img->fit(500,500); // 写真をリサイズする
        // $img->resize(500, 500, function ($constraint) {
        //     $constraint->aspectRatio();
        // });
        // $img->flip();   // 写真を反転させる

        //保存
        $save_path = public_path("images_sample/shopp/shopp02_flip.png");
        $img->save($save_path);

    }

    // event03_flip写真を読み込み加工する パプリカ
    public function resize03()
    {
        // 読み込み
        $path = public_path('images_sample/event/event06.jpg');
        $img = Image::make($path);

        // $img->resize(500,500); // 写真をリサイズする
        $img->fit(500,500); // 写真をリサイズする
        // $img->resize(500, 500, function ($constraint) {
        //     $constraint->aspectRatio();
        // });
        // $img->flip();   // 写真を反転させる

        //保存
        $save_path = public_path("images_sample/event/event06_flip.jpg");
        $img->save($save_path);

    }

    // top01_flip写真を読み込み加工する 刺身
    public function resize04()
    {
        // 読み込み
        $path = public_path('images_sample/top/top03.jpg');
        $img = Image::make($path);

        // $img->resize(500,500); // 写真をリサイズする
        $img->fit(500,150); // 写真をリサイズする
        // $img->resize(500, 500, function ($constraint) {
        //     $constraint->aspectRatio();
        // });
        // $img->flip();   // 写真を反転させる

        //保存
        $save_path = public_path("images_sample/top/top03_flip.jpg");
        $img->save($save_path);

    }


}
