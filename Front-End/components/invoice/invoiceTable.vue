<template>
  <v-row class="mb-10">
    <v-col cols="12">
      <v-data-table
        :headers="headers"
        :items="invoiceItems"
        class="elevation-1"
        fixed-header
        :hide-default-footer="true"
      >
        <template v-slot:[`item.invoice_quantity`]="{ item }">
          <v-text-field
            dense
            outlined
            class="shrink"
            type="number"
            :value="item.invoice_quantity"
            @keyup="qtyChange($event.target.value, invoiceItems.indexOf(item))"
          ></v-text-field>
        </template>

        <template v-slot:[`item.price`]="{ item }">
          <v-text-field
            dense
            outlined
            class="shrink"
            type="number"
            :value="item.price"
            @keyup="priceChange($event.target.value, invoiceItems.indexOf(item))"
          ></v-text-field>
        </template>
        <template v-slot:[`item.action`]="{ item }">
          <v-icon small @click="removeItem(item, invoiceItems.indexOf(item))">
            mdi-delete
          </v-icon>
        </template>
      </v-data-table>
    </v-col>
  </v-row>
</template>
<script>
export default {
  name: "invoiceTable",
  data() {
    return {
      headers: [
        { text: "Name", value: "name" },
        { text: "Quantity", value: "invoice_quantity" },
        { text: "Price", value: "price" },
        { text: "Subtotal", value: "subtotal" },
        { text: "Action", value: "action" }
      ]
    };
  },
  computed: {
    sellItems() {
      let products = this.$store.getters["product/getSellItems"];
      return products;
    },
    invoiceItems() {
      let products = this.$store.getters["product/getInvoiceItems"];
      return products;
    }
  },
  async asyncData({ params, axios }) {},
  methods: {
    qtyChange(val, index) {
      this.$store.dispatch("product/updateInvoiceItem", {
        invoice_quantity: val,
        index: index,
        type: "qtychange"
      });
    },
    priceChange(val, index) {
      this.$store.dispatch("product/updateInvoiceItem", {
        price: parseInt(val),
        index: index,
        type: "pricechange"
      });
    },

    removeItem(val,index){
      this.$store.commit("product/REMOVE_INVOICE_PRODUCT", {
        id: val.id,
        index: index,
      });
    }
  }
};
</script>

<style scoped>
.v-text-field {
  width: 100px;
  margin-top: 10px !important;
}
</style>
