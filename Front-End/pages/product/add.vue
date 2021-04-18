<template>
  <v-container>
    <add-product />
  </v-container>
</template>

<script>
import addProduct from "../../components/product/addProduct";

export default {
  name: "addProductPage",
  middleware: "auth",
  head: {
    title: "Add Product",
  },
  components: { addProduct },
  data() {
    return {};
  },
  computed: {},
  async asyncData({ params, axios }) {},
  mounted() {},
  methods: {},
};
</script>

<style lang="scss" scoped>
</style>
