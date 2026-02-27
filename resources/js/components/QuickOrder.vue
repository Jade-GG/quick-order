<script>
import { mask, refreshMask } from 'Vendor/rapidez/core/resources/js/stores/useMask'

export default {
    data() {
        return {
            productCount: 0,
            products: {},
            productMatches: {},
            enteredOptions: {},
            adding: [],
            fetching: false,
            fileName: '',
        }
    },

    mounted() {
        for (let i = 0; i < 5; i++) {
            this.newProduct()
        }
    },

    render() {
        return this.$scopedSlots.default(this)
    },

    methods: {
        newProduct(sku = null, quantity = null, options = {}) {
            let id = this.newId()
            this.products[id] = { sku: sku, quantity: quantity }
            this.enteredOptions[id] = options
            this.productCount++

            return id
        },

        async updateProduct(id, sku) {
            this.products[id].sku = sku
            this.enteredOptions[id] = {}
            this.productMatches = await this.getProducts(this.products)
        },

        newId() {
            return Math.random().toString(16).substring(2, 10)
        },

        deleteProduct(id) {
            if ((!id in this.products)) {
                return
            }

            delete this.products[id]
            delete this.enteredOptions[id]
            this.productCount--
        },

        async importCSV(event) {
            let csv = await event.target.files[0].text();
            this.fileName = event.target.files[0].name;

            this.products = {}
            this.enteredOptions = {}
            this.productCount = 0

            await Promise.all(
                csv.split(/\r?\n/).map(async (line) => {
                    if (!line) {
                        return;
                    }

                    let columns = line.split(/[;,]/).map(this.stripQuotes);

                    if(columns.length < 2 || isNaN(columns[1])) {
                        return;
                    }

                    this.newProduct(
                        columns[0],
                        columns[1],
                        { ...columns.slice(2) },
                    );
                })
            );

            this.productMatches = await this.getProducts(this.products)

            event.target.value = "";
            Notify(window.config.translations.orderlist.csv_success, 'success');
        },

        stripQuotes(string) {
            return (string ?? '').replace(/['"]+/g, "");
        },

        errors(id) {
            if (this.fetching) {
                return []
            }

            let errors = []

            if (!(id in this.products) || (this.products[id].sku && !(id in this.productMatches))) {
                errors.push(window.config.quick_order.translations.errors.exist)
            }

            if (this.products[id].sku && id in this.productMatches) {
                if (this.productMatches[id].__typename === 'ConfigurableProduct') {
                    errors.push(window.config.quick_order.translations.errors.configurable)
                }
            }

            return errors
        },

        async getProducts(products) {
            this.fetching = true
            let skus = Object.values(products).map(product => product.sku)
            let response = await window.magentoGraphQL(
                `query Products {
                    products(
                        filter: { sku: { in: [${skus.map(sku => `"${sku}"`).join(',')}] } }
                        pageSize: 999
                        currentPage: 1
                    ) {
                        items {
                            sku
                            image { url }
                            __typename
                            ... on CustomizableProductInterface { options {
                                __typename
                                required
                                sort_order
                                title
                                uid
                                ... on CustomizableFieldOption { value { max_characters uid } }
                            } }
                        }
                    }
                }`
            )

            this.fetching = false
            return Object.fromEntries(Object.entries(products).map(([id, product]) => {
                let match = response.data.products.items.find(item => item.sku == product.sku)
                return match ? [id, match] : null
            }).filter(Boolean))
        },

        getProductOptions(id) {
            return this.productMatches?.[id]?.options?.toSorted((a, b) => a.sort_order - b.sort_order) ?? null;
        },

        getAddToCartOptions(id) {
            return {
                entered_options: Object.entries(this.enteredOptions[id])
                    .map(([optionId, value]) => ({ uid: this.getUid(optionId, id), value: value }))
            }
        },

        getUid(optionId, productId) {
            return this.getProductOptions(productId)?.[optionId]?.uid ?? null
        },

        async addAllToCart() {
            await this.addToCart(this.products)
        },

        async addOneToCart(id) {
            await this.addToCart({ [id]: this.products[id] })
        },

        async addToCart(products) {
            let filteredProducts = Object.entries(products)
                .filter(([id, product]) => product.sku)
                .filter(([id, product]) => !this.errors(id).length)

            if (!filteredProducts.length) {
                return
            }

            this.adding = filteredProducts.map(([id, product]) => id)

            if (!mask.value) {
                await refreshMask()
            }

            try {
                let response = await window.magentoGraphQL(
                    `mutation ($cartId: String!, $cartItems: [CartItemInput!]!) {
                        addProductsToCart(cartId: $cartId, cartItems: $cartItems)
                        { cart { ...cart } user_errors { code message } }
                    }
                    ` + config.fragments.cart,
                    {
                        cartId: mask.value,
                        cartItems: filteredProducts.map(([id, product]) => ({
                            ...product,
                            ...this.getAddToCartOptions(id),
                        })),
                    },
                )
                await this.updateCart({}, response)

                Notify(window.config.quick_order.translations.add, 'success', [], window.url('/cart'))
            } catch(error) {
                Notify(error.message, 'error')
            }

            this.adding = []
        },
    },
}
</script>
