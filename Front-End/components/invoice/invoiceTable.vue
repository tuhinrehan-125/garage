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


<!--        <template v-slot:[`item.unit`]="{ item }">-->
<!--          <v-select-->
<!--            :value="item.unit"-->
<!--            :items="units"-->
<!--            item-text="name"-->
<!--            item-value="id"-->
<!--            dense-->
<!--            outlined-->
<!--            class="shrink"-->
<!--          ></v-select>-->
<!--        </template>-->
<!--        <template v-slot:[`item.discount`]="{ item }">-->
<!--          <v-text-field-->
<!--            dense-->
<!--            outlined-->
<!--            class="shrink"-->
<!--            type="number"-->
<!--            :value="item.discount"-->
<!--            @keyup="-->
<!--              discountChange($event.target.value, invoiceItems.indexOf(item))-->
<!--            "-->
<!--          ></v-text-field>-->
<!--        </template>-->
<!--        <template v-slot:[`item.tax`]="{ item }">-->
<!--          <v-text-field-->
<!--            dense-->
<!--            outlined-->
<!--            class="shrink"-->
<!--            type="number"-->
<!--            :value="item.tax"-->
<!--            @keyup="taxChange($event.target.value, invoiceItems.indexOf(item))"-->
<!--          ></v-text-field>-->
<!--        </template>-->
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
  props:['units'],
  data() {
    return {
      headers: [
        { text: "Name", value: "name" },
        { text: "Quantity", value: "invoice_quantity" },
        // { text: "Unit", value: "unit" },
        // { text: "Discount", value: "discount" },
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
        // sell_quantity: val,
        invoice_quantity: val,
        index: index,
        type: "qtychange"
      });
    },
    priceChange(val, index) {
      this.$store.dispatch("product/updateInvoiceItem", {
        // purchase_price: parseInt(val),
        // invoice_price: parseInt(val),
        price: parseInt(val),
        index: index,
        type: "pricechange"
      });
    },
    // discountChange(val, index) {
    //   this.$store.dispatch("product/updateSellItem", {
    //     discount: parseInt(val),
    //     index: index,
    //     type: "discountchange"
    //   });
    // },
    // taxChange(val, index) {
    //   this.$store.dispatch("product/updateSellItem", {
    //     tax: parseInt(val),
    //     index: index,
    //     type: "taxchange"
    //   });
    // },
    removeItem(val,index){
      this.$store.commit("product/REMOVE_INVOICE_PRODUCT", {
      // this.$store.commit("product/REMOVE_PRODUCT", {
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
