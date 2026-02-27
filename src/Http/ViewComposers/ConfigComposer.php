<?php

namespace Rapidez\QuickOrder\Http\ViewComposers;

use Illuminate\Support\Facades\Config;
use Illuminate\View\View;

class ConfigComposer
{
    public function compose(View $view)
    {
        Config::set('frontend.quick_order.translations', [
            'add' => __('Added to cart'),
            'errors' => [
                'exist' => __('Product does not exist'),
                'configurable' => __('Please choose a child product'),
            ]
        ]);
    }
}
