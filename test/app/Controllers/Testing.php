<?php

namespace App\Controllers;

class Testing
{

    public function index()
    {
        echo "<h1>Testing Group Index Page</h1>";
    }

    public function path()
    {
        echo "<h1>Testing Group Path Page</h1>";
    }

    public function subPath()
    {
        echo "<h1>Testing Group Sub-Path Page</h1>";
    }

    public function id($id)
    {
        echo "<h1>Testing Group</h1><br/>";
        echo "<h2>ID : $id</h2>";
    }

    public function name($name)
    {
        echo "<h1>Testing Group</h1><br/>";
        echo "<h2>Name : $name</h2>";
    }

}
