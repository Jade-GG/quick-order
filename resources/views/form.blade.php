@extends('rapidez::layouts.app')

@section('content')
    <quick-order v-slot="quickOrder">
        <div class="container">
            <div class="flex flex-col gap-4" v-bind:key="quickOrder.productCount">
                <template v-for="product, productId in quickOrder.products">
                    @include('rapidez-quick-order::partials.product-line')
                </template>
                <div class="flex">
                    <x-rapidez::button.outline v-on:click="quickOrder.newProduct()">
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
            <div class="mt-8 relative flex border rounded w-fit flex-col items-center gap-3 rounded p-4">
                <input
                    v-on:change="quickOrder.importCSV"
                    type="file"
                    class="absolute inset-0 cursor-pointer indent-[100%] opacity-0"
                />
                <div v-if="!quickOrder.fileName">@lang('Upload CSV')</div>
                <div v-else v-cloak>
                    @lang('File :file has been successfully uploaded', ['file' => '@{{ quickOrder.fileName }}'])
                </div>
            </div>
        </div>
    </quick-order>
@endsection
