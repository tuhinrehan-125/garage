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
    placeholder="Search Product by Name/SKU"
    solo-inverted
    filled
    return-object
  ></v-autocomplete>
</template>

<script>
export default {
  name: "searchProduct",
  props: ["type"],
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
          tax: 0
        });
      } else if (this.type == "purchase") {
        this.$store.dispatch("product/addItemToPurchase", {
          name: val.name,
          id: val.id,
          purchase_quantity: 1,
          price: val.price,
          discount: 0,
          tax: 0
        });
      }
    },
    getProducts(val) {
      if (val) {
        this.loading = true;
        this.$axios
          .post("/product/search", { name: val, type: "Product" })
          .then(response => {
            this.products = [];
            this.loading = false;
            this.products = response.data;
            console.log(this.products);
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
