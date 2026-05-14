<?php

namespace App\Http\Controllers;

use App\Models\Pimpinan;
use App\Contracts\HasRole;
use Illuminate\Support\Facades\Auth;

class PimpinanBaseController extends Controller
{
    protected function authorizePimpinan(): Pimpinan
    {
        /** @var \App\Models\Pimpinan $user */
        $user = Auth::user();
        $role = $user instanceof HasRole ? $user->getRole() : null;
        abort_unless($user && $role === 'pimpinan', 403);

        return $user;
    }
}

