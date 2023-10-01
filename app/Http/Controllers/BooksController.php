<?php

namespace App\Http\Controllers;

use App\Models\mydetail;
use App\Models\mymaster;
use Illuminate\Http\Request;

session_start();
class BooksController extends Controller
{
    public function index(){
        return view("index");
    }
    
    //儲存亂數
    public function numRand(){
        $numRand=array();
        while(count($numRand)<3){
            $num=rand(0,9);
                if(!in_array($num,$numRand)){
                    $numRand[]=$num;
                }
        }
    
        if(session()->has('numRand')){
            session(['numRand' => $numRand]);
            return redirect()->route('result');
        }else{            
            session(['numRand' => $numRand]);
            return redirect()->route('showRandArr');
        }
    }

    //將亂數變成陣列
    public function showRandArr(){
        $numRand = session('numRand', []);
        
        //儲存結果
        if(!isset($_SESSION['ans'])){
            $_SESSION['ans']=array();
        }

        //計算已進行多少回
        if(!isset($_SESSION['bout'])){
            $_SESSION['bout']=0;
        }
        else{
            $_SESSION['bout']+=1;
        }

        $numsString= implode(',', $numRand);
        $_SESSION['ans'][] = $_SESSION['bout']."=".$numsString."<br>"; 

        if(isset($_SESSION['check'])){
            if($_SESSION['check']==1){
                // print_r($_SESSION['ans']);
                foreach($_SESSION['ans'] as $value){
                    echo $value;
                }
            }
            else{
                print_r($_SESSION['numRand']);
                echo "</br>";
            }
        }
        
        return view ('index');
    }

    public function result(){
        $numRand = session('numRand', []);
        $bout = session('bout',[]);
        
        if(!isset($_SESSION['check'])){
            $_SESSION['check']=0;
        }
        else{
            $_SESSION['check']=1;
        }

        $abs_AB=abs($_SESSION['numRand'][1]-$_SESSION['numRand'][0]);
        $abs_BC=abs($_SESSION['numRand'][2]-$_SESSION['numRand'][1]);

        if($_SESSION['bout']<9){
            if($abs_AB==$abs_BC){
                $ans.= '總共試了'.$_SESSION['bout'].'次或已找到數字了喔!';
                session(['ans'=>$ans]);
                return redirect()->route('insert');
                return view('index');
            }
        }
        else{
            $ans.= '總共試了'.$_SESSION['bout'].'次或已找到數字了喔!';
            session(['ans'=>$ans]);
            return redirect()->route('insert');
            return view('index');
        }
    }

    public function insert(){
        date_default_timezone_set('Asia/Taipei');
        $freq=$_SESSION['bout'];
        $data = date('YmdHis');
    
        $master = "INSERT INTO mymaster VALUES ('$data', ".($freq+1).")";
        $result = mysqli_query($link, $master);

        foreach($_SESSION['ansA'] as $i => $value){
            $num = explode("=", $value)[1];
            $tail = "INSERT INTO mydetail VALUES ('$data','".($i+1)."','$num')";
            $result = mysqli_query($link, $tail);
        }
        return view('index');
    }
    public function clear(){
         //清空session內所有資料
        session()->flush();
        //重新導回一開始的樣子
        return redirect()->route('start');
    }
}