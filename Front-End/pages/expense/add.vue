<template>
  <v-container>
    <add-expense />
    <!-- <add-expense :items="items" @refresh="getExpense()" /> -->
  </v-container>
</template>

<script>
import addExpense from "../../components/expense/addExpense";

export default {
  name: "addProductPage",
  head: {
    title: "Add Expense",
  },
  components: { addExpense },
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
