<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;


class AbilitiesController extends Controller
{
    public function index()
    {
        $permissions = auth()->user();

        if (!Gate::allows('update-post', 'editar_livro')) {
            return abort(403);
        } 

        return $permissions;
    }
}
