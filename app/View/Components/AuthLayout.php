<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AuthLayout extends Component
{
    public ?string $bg = null;

    public function __construct()
    {
        $dir = public_path('img/auth');
        $files = [];

        if (is_dir($dir)) {
            foreach (scandir($dir) as $f) {
                if (preg_match('/\.(jpe?g|png|webp|avif)$/i', $f)) {
                    $files[] = 'img/auth/'.$f;
                }
            }
        }

        $this->bg = $files ? $files[array_rand($files)] : null;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.auth-layout');
    }
}
