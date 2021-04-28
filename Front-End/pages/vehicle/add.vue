<template>
  <v-container>
    <add-vehicle />
  </v-container>
</template>

<script>
import addVehicle from "../../components/vehicle/addVehicle";

export default {
  name: "addVehiclePage",
  middleware: "auth",
  head: {
    title: "Add Vehicle",
  },
  components: { addVehicle },
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
