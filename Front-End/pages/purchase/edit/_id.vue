<template>
  <v-container fluid grid-list-xl class="mt-5">
    <add-or-update :data="data" :suppliers="suppliers" />
  </v-container>
</template>
<script>
import AddOrUpdate from "../../../components/purchase/addOrUpdate";
export default {
  name: "editpurchase",
  components: { AddOrUpdate },
  data() {
    return {};
  },
  computed: {},
  async asyncData({ params, $axios }) {
    const [data, suppliers] = await Promise.all([
      $axios.get(`purchase/${params.id}`),
      $axios.get("/get-suppliers")
    ]);
    return {
      data: data.data.data,
      suppliers: suppliers.data
    };
  },
  mounted() {},
  methods: {
    // async getSuppliers() {
    //   await this.$axios.get("/contact?type=supplier").then(response => {
    //     this.suppliers = response.data;
    //   });
    // }
    async getSuppliers() {
      await this.$axios.get("/get-suppliers").then(response => {
        this.suppliers = response.data;
        console.log(this.suppliers);
      });
    }
  }
};
</script>

<style scoped></style>
