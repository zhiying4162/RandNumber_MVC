<?php

namespace App\Http\Controllers;

use App\Models\mydetail;
use App\Models\mymaster;
use Illuminate\Http\Request;

session_start();
class BooksController extends Controller
{
    public function index()
    {
        return view("index");
    }

    //亂數
    public function numRand()
    {
        $numRand = [];
        while (count($numRand) < 3) {
            $num = rand(0, 9);
            if (!in_array($num, $numRand)) {
                $numRand[] = $num;
            }
        }

        session(['numRand' => $numRand]);  // 使用 session 函數來存存數據

        if (session()->has('numRand')) {
            return redirect()->route('result');
        } else {
            return redirect()->route('showRandArr');
        }
    }

    public function showRandArr()
    {
        $numRand = session('numRand', []);

        if (!session()->has('ans')) {
            session(['ans' => []]);
        }

        if (!session()->has('bout')) {
            session(['bout' => 0]);
        } else {
            session(['bout' => session('bout') + 1]);
        }

        $numsString = implode(',', $numRand);
        session()->push('ans', session('bout') . "=" . $numsString . "<br>");

        if (session()->has('check')) {
            if (session('check') == 1) {
                foreach (session('ans') as $value) {
                    echo $value;
                }
            } else {
                print_r(session('numRand'));
                echo "<br>";
            }
        }

        return view('index');
    }

    public function result()
    {
        $numRand = session('numRand', []);
        $bout = session('bout', 0);

        if (!session()->has('check')) {
            session(['check' => 0]);
        } else {
            session(['check' => 1]);
        }

        $abs_AB = abs($numRand[1] - $numRand[0]);
        $abs_BC = abs($numRand[2] - $numRand[1]);

        if ($bout < 9) {
            if ($abs_AB == $abs_BC) {
                $ans = '總共試了' . $bout . '次或已找到數字了喔!';
                session(['ans' => $ans]);
                return redirect()->route('insert');
            }
        } else {
            $ans = '總共試了' . $bout . '次或已找到數字了喔!';
            session(['ans' => $ans]);
            return redirect()->route('insert');
        }
    }

    public function insert()
    {
        date_default_timezone_set('Asia/Taipei');
        $freq = session('bout', 0);
        $data = date('YmdHis');

        $master = new Mymaster();  // 創建主紀錄
        $master->id = $data;
        $master->freq = $freq + 1;
        $master->save();

        foreach (session('ans') as $i => $value) {
            $num = explode("=", $value)[1];
            $detail = new Mydetail();  // 創建詳細記錄
            $detail->id = $data;
            $detail->turn = $i + 1;
            $detail->rec = $num;
            $detail->save();
        }

        return view('index');
    }

    public function clear(Request $request)
    {
        $request->session()->flush();  // 清空會話中的所有數據
        return redirect()->route('start');
    }
}
