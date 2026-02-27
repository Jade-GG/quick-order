@extends('rapidez::layouts.app')

@section('content')
    <quick-order v-slot="quickOrder">
        <div class="container">
            <div class="flex flex-col gap-8" v-bind:key="quickOrder.productCount">
                <template v-for="product, productId in quickOrder.products">
                    @include('rapidez-quick-order::partials.product-line')
                </template>
                <div class="flex">
                    <x-rapidez::button.outline type="button" v-on:click="quickOrder.newProduct()">
                        @lang('New line')
                    </x-rapidez::button.outline>
                    <x-rapidez::button.conversion
                        type="button"
                        v-on:click="quickOrder.addAllToCart"
                        v-bind:class="{'button-loading': quickOrder.adding.length > 0}"
                        v-bind:disabled="quickOrder.adding.length > 0"
                    >
                        @lang('Add all to cart')
                    </x-rapidez::button.outline>
                </div>
            </div>
        </div>
    </quick-order>
@endsection
