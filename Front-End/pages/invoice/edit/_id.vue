<template>
  <v-container fluid grid-list-xl class="mt-5">
    <add-or-update :data="data"  :contacts="contacts" />
  </v-container>
</template>

<script>
import addOrUpdate from "~/components/invoice/addOrUpdate";
export default {
  name: "_id",
  components: { addOrUpdate },
  data() {
    return {};
  },
  computed: {},
  async asyncData({ params, $axios }) {
    const [data, contacts] = await Promise.all([
      $axios.get(`invoice/${params.id}`),
      $axios.get("/get-clients"),
    ]);
    return {
      data: data.data.data,
      contacts: contacts.data,
    };
  },
  mounted() {},
  methods: {

    async getCustomers() {
      await this.$axios.get("/get-clients").then(response => {
        this.contacts = response.data;
      });
    },
  }
}
</script>

<style scoped>

</style>
