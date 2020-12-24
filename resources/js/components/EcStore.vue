<template>
    <div class="pt-5">

        <!--Title area-->
        <div class="text-h4 font-weight-black">Products</div>

        <div>
            <v-row v-if="products.length">
                <v-col cols="12" md="3" sm="12" v-for="(product, index) in products" :key="index">
                    <v-card>
                        <v-img v-if="product.images.length" :src="product.images[0].image"/>
                        <v-card-text>
                            <v-layout>
                                <v-flex sm6 md6>
                                    <div class="text-h5 font-weight-black">{{ product.name }}</div>
                                </v-flex>
                                <v-flex>
                                    <div class="text-right">
                                        <v-btn @click="purchase(product)" :class="`purchase-${product.id}`" color="success" depressed>Purchase</v-btn>
                                    </div>
                                </v-flex>
                            </v-layout>
                            <p class="mt-3">{{product.description}}</p>
                            <p class="mt-3 font-weight-bold">{{ `${product.items_available} in-stock` }}</p>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </div>

    </div>
</template>

<script>
import {mapGetters} from 'vuex';

export default {
    computed: {
        ...mapGetters(["fully_authenticated"])
    },
    data() {
        return {
            products: [],
            pagination: {
                per_page: 10,
                page: 1,
                meta: null
            }
        }
    },
    methods:{
        getProducts() {
            this.$http.get('/api/products').then(response => {
                this.products = response.data.data.data;
                this.pagination.per_page = response.data.data.per_page;
                this.pagination.page = response.data.data.current_page;
            });
        },
        purchase(item) {
            if (!this.fully_authenticated) {
                this.$eventBus.$emit('alert', {
                    status: 'error',
                    message: 'You need to be logged in to purchase this item.'
                });
                this.$router.push('/login');
                return;
            }

            this.$http.post(`/api/products/${item.id}?purchase`).then(response => {
                this.getProducts();
            })
        }
    },
    mounted() {
        this.getProducts();
    }
}
</script>

