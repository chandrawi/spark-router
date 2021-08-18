<?php

namespace SparkLib\App\Controllers;

class Index
{

    public function index()
    {
        echo "<h1>Index Page</h1>";
    }

    public function data($data)
    {
        echo "<h1>Data : $data</h1>";
    }

}
