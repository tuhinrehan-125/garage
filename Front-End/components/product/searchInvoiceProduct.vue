<template>
  <v-autocomplete
    v-model="item"
    :loading="loading"
    :items="products"
    :search-input.sync="getProducts"
    ref="pro"
    clearable
    hide-no-data
    hide-selected
    item-text="name"
    item-value="id"
    placeholder="Search Product by Name"
    solo-inverted
    filled
    return-object
  ></v-autocomplete>
</template>

<script>
export default {
  name: "searchInvoiceProduct",
  props: ["category_type","location_id","type"],
  components: {},
  data() {
    return {
      item: "",
      loading: false,
      products: [],
      getProducts: null
    };
  },
  computed: {},
  async asyncData({ params, axios }) {},
  mounted() {},
  methods: {},
  watch: {
    item(val) {
      if (this.type == "invoice") {
        this.$store.dispatch("product/addItemToInvoice", {
          name: val.name,
          id: val.id,
          invoice_quantity: 1,
          price: val.price,
          discount: 0,
          tax: 0,
          category_type: val.category_type
        });
      } else if (this.type == "purchase") {
        this.$store.dispatch("product/addItemToPurchase", {
          product: val.product,
          product_id: val.product_id,
          variation_id: val.variation_id,
          purchase_quantity: 1,
          purchase_price: val.purchase_price,
          discount: 0,
          tax: val.tax
        });
      }

    },
    getProducts(val) {
      if (val) {
        this.loading = true;
        this.$axios
          .post("/product/search", { name: val, type:this.category_type })
          .then(response => {
            this.products = [];
            this.loading = false;
            this.products = response.data;
            console.log(this.products)
          })
          .catch(err => {
            this.loading = false;
          });
      }
    }
  }
};
</script>

<style scoped></style>
