<div>
    <div v-for="error in quickOrder.errors(productId)" class="text-danger">
        @{{ error }}
    </div>
    <div class="flex gap-2">
        <label>
            <x-rapidez::label>@lang('SKU')</x-rapidez::label>
            <x-rapidez::input
                name="sku"
                required
                v-bind:value="product.sku"
                v-bind:class="{'text-danger': quickOrder.errors(productId).length}"
                v-on:change="(event) => quickOrder.updateProduct(productId, event.target.value)"
            />
        </label>
        <template v-if="quickOrder.productMatches?.[productId]?.image?.url">
            <img
                class="size-12 mt-auto"
                v-bind:src="resizedPath(quickOrder.productMatches[productId].image.url + '.webp', '80x80')"
            >
        </template>
        <label>
            <x-rapidez::label>@lang('Quantity')</x-rapidez::label>
            <x-rapidez::input type="number" name="quantity" v-model.lazy="product.quantity" required/>
        </label>
        <template v-for="option, optionId in quickOrder.getProductOptions(productId)">
            <template v-if="false"></template>
            @include('rapidez-quick-order::options.field')

            @unless (app()->environment('production'))
                <label v-else>
                    <x-rapidez::label>@{{ option.title }}</x-rapidez::label>
                    <div class="text-danger">@{{ option.__typename }}</div>
                </label>
            @endunless
        </template>
        <x-rapidez::button.conversion
            class="mt-auto"
            type="button"
            v-on:click="quickOrder.addOneToCart(productId)"
            v-bind:class="{'button-loading': quickOrder.adding.includes(productId)}"
            v-bind:disabled="quickOrder.errors(productId).length > 0 || quickOrder.adding.includes(productId)"
        >
            @lang('Add to cart')
        </x-rapidez::button.conversion>
    </div>
</div>
