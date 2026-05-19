<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * ============================================
 * CATATAN UNTUK PEMULA:
 * Ini adalah BASE CONTROLLER.
 * Semua controller lain extends dari sini.
 * 
 * Tidak perlu diubah karena hanya menyediakan
 * trait-trait dasar dari Laravel.
 * ============================================
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}