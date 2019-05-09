<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;

class PagesController extends Controller
{
        public function index(){
            $title = 'Welcome to Larevel!';
            // return view('pages.index', compact('title'));
            return view('pages.index') ->with('title', $title);
        }

        public function about(){
            $title = 'About Us';
            // return view('pages.about') ->with('title', $title);
            // return view('pages.about', compact('title'));
            return view('pages.about') ->with('title', $title);
        }

        public function employee(){
            $data = array(
                'title' => 'Services',
                'services' => ['Web Design', 'Programming', 'SEO']
            );
            return view('pages.employee') ->with($data);
        }

        //  public function addEmployee(){
           
        //     return view('pages.addemployee');
        // }

     
}
