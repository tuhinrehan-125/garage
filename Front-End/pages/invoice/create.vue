<template>
  <v-container>
<!--    <add-or-update :contacts="contacts" :categories="categories" :vehicles="vehicles" :products ="products" />-->
    <add-or-update :contacts="contacts" :categories="categories"  />
  </v-container>
</template>

<script>
import addOrUpdate from "~/components/invoice/addOrUpdate";

export default {
  name: "create",
  middleware: "auth",
  head: {
    title: "Add invoice"
  },

  components: {addOrUpdate},
  data() {
    return {
      contacts: [],
      categories: [],
      vehicles: [],
      products: [],
    };
  },
  computed: {

  },
  async asyncData({params, axios}) {
  },
  mounted() {
    this.getCustomers();
    this.getCategories();
    // this.$store.commit("product/INIT_CART_ITEMS");
  },
  watch: {},
  methods: {

    async getCustomers() {
      await this.$axios.get("/get-clients").then(response => {
        this.contacts = response.data;
      });
    },

    async getVehicles() {

      await this.$axios.get("/get-vehicles", {params: {contact_id: this.form.contact_id}}).then(response => {
        this.vehicles = response.data;
      });
    },
    async getCategories() {
      await this.$axios.get("/get-categories").then(response => {
        this.categories = response.data;
      });
    },

    // async getProducts() {
    //   await this.$axios.get("/get-products", {params: {contact_id: this.form.contact_id}}).then(response => {
    //     this.categories = response.data;
    //   });
    // },

  }
};
</script>

<style scoped></style>
