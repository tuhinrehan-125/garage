<template>
  <v-app id="main">
    <nuxt />
  </v-app>
</template>

<script>
export default {
  head: {
    title: "",
  },
  components: {},
  data() {
    return {};
  },
  computed: {},
  async asyncData({ params, axios }) {},
  mounted() {},
  methods: {},
};
</script>

<style>
#main {
  background: #f2f5f8;
  color: rgba(0,0,0,.87);
  height: 100vh;
}
</style>
