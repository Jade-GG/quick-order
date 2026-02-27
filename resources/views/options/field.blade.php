<label v-else-if="option.__typename == 'CustomizableFieldOption'">
    <x-rapidez::label>@{{ option.title }}</x-rapidez::label>
    <x-rapidez::input
        v-bind:maxlength="option.value.max_characters"
        v-bind:required="option.required"
        v-model.lazy="quickOrder.enteredOptions[productId][optionId]"
    />
</label>
