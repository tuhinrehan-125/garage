<template>
  <v-container>
    <v-overlay :value="full_loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>
    <v-row>
      <v-col>
        <v-card class="mb-70">
          <v-card-title>
            <h2 class="overline variation-title">Add Invoice</h2>
          </v-card-title>
          <v-card-text>
            <v-form
              ref="form"
              method="post"
              v-model="valid"
              lazy-validation
              v-on:submit="submitForm"
            >
              <v-row no-gutters>
                <v-col cols="12" md="4" sm="12" xl="4">
                  <v-select
                    label="Select Client"
                    v-model="form.contact_id"
                    :items="contacts"
                    item-text="name"
                    item-value="id"
                    dense
                    @change="getVehicles()"
                    :rules="[v => !!v || 'Contact is required']"
                    outlined
                  ></v-select>
                </v-col>
                <v-col cols="12" md="4" sm="12" xl="4">
                  <v-select
                    label="Select Category"
                    v-model="form.category_id"
                    :items="categories"
                    item-text="name"
                    item-value="name"
                    dense
                    :rules="[v => !!v || 'Category is required']"
                    outlined
                  ></v-select>
                </v-col>
                <v-col cols="12" md="4" sm="12" xl="4">
                  <v-dialog
                    ref="dialog"
                    v-model="modal"
                    :return-value.sync="form.invoice_date"
                    persistent
                    width="290px"
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field
                        v-model="form.invoice_date"
                        label="Invoice Date"
                        prepend-icon="mdi-calendar"
                        readonly
                        v-bind="attrs"
                        v-on="on"
                        dense
                        :rules="[v => !!v || 'Date is required']"
                        outlined
                      ></v-text-field>
                    </template>
                    <v-date-picker v-model="form.invoice_date" scrollable>
                      <v-spacer></v-spacer>
                      <v-btn text color="primary" @click="modal = false">
                        Cancel
                      </v-btn>
                      <v-btn
                        text
                        color="primary"
                        @click="$refs.dialog.save(form.invoice_date)"
                      >
                        OK
                      </v-btn>
                    </v-date-picker>
                  </v-dialog>
                </v-col>

                <v-col cols="12" md="4" sm="12" xl="4">
                  <v-select
                    label="Select Vehicles"
                    item-text="model"
                    item-value="id"
                    v-model="form.vehicle_id"
                    :items="vehicles"
                    dense
                    outlined
                  ></v-select>
                </v-col>
              </v-row>
              <h2 class="overline variation-title mb-2 text-center">
                Search Items
              </h2>
              <v-row no-gutters>
                <v-col>
                  <search-invoice-product :type="invoice" :category_type="form.category_id"/>
                </v-col>
              </v-row>
              <h2 class="overline variation-title mb-2 text-center">
                Invoice Items
              </h2>
              <invoice-table :units="units"/>
              <v-row no-gutters>
                <v-col cols="12" md="4" sm="12" xl="4">
                  <v-text-field
                    label="Tax"
                    outlined
                    dense
                    required
                    v-model="form.invoice_tax"
                    @keyup="addTax($event.target.value)"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="4" sm="12" xl="4">
                  <v-text-field
                    label="Discount"
                    outlined
                    dense
                    required
                    v-model="form.invoice_discount"
                    @keyup="addDiscount($event.target.value)"
                  ></v-text-field>
                </v-col>
<!--                <v-col cols="12" md="4" sm="12" xl="4">-->
<!--                  <v-text-field-->
<!--                    label="Shipping cost"-->
<!--                    outlined-->
<!--                    dense-->
<!--                    required-->
<!--                    v-model="form.shipping_cost"-->
<!--                    @keyup="addShippingCost($event.target.value)"-->
<!--                  ></v-text-field>-->
<!--                </v-col>-->
                <v-col cols="12" md="4" sm="12" xl="4">
                  <v-textarea
                    rows="2"
                    label="Note"
                    outlined
                    dense
                    required
                    v-model="form.note"
                  ></v-textarea>
                </v-col>
                <v-col cols="12">
                  <h2 class="text-right">Total: {{ grandTotal }}</h2>
                </v-col>
              </v-row>
              <v-row>
                <v-col cols="12">
                  <v-btn
                    class="float-right"
                    dark
                    color="success"
                    @click="submitForm"
                    :loading="isLoading"
                  >
                    <v-icon dark> mdi-plus</v-icon>
                    Submit
                  </v-btn>
                </v-col>
              </v-row>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import searchProduct from "../../components/product/searchProduct";
import searchInvoiceProduct from "~/components/product/searchInvoiceProduct";
import sellTable from "../../components/sell/sellTable";
import invoiceTable from "~/components/invoice/invoiceTable";

export default {
  name: "addsell",
  middleware: "auth",
  head: {
    title: "Add invoice"
  },

  components: {searchProduct, invoiceTable, searchInvoiceProduct},
  data() {
    return {
      full_loading: false,
      valid: false,
      isLoading: false,
      customer: [],
      sell_statuses: ["Final", "Pending"],
      modal: false,
      invoice: "invoice",
      units: [],
      contacts: [],
      categories: [],
      vehicles: [],
      products: [],
      form: {
        business_location_id: "",
        customer_id: "",
        sell_status: "",
        invoice_date: new Date().toISOString().substr(0, 10),
        sell_tax: "",
        invoice_tax:"",
        sell_discount: "",
        invoice_discount:"",
        shipping_cost: "",
        payment_amount: "",
        payment_method: "",
        payment_note: "",
        contact_id: "",
        category_id: "",
        vehicle_id: "",
        product_id:"",
      },
      methods: ["Cash", "Card", "Bank Transfer"]
    };
  },
  computed: {
    user_business_location() {
      return this.$auth.user.data.user_business_location;
    },
    grandTotal() {
      let grandtotal = this.$store.getters["product/invoiceTotalPrice"];
      // alert(grandtotal)
      return Math.round(grandtotal);
    },
    sellItems() {
      let products = this.$store.getters["product/getSellItems"];
      return products;
    },
    invoiceItems() {
      let products = this.$store.getters["product/getInvoiceItems"];
      return products;
    }
  },
  async asyncData({params, axios}) {
  },
  mounted() {
    this.getCustomers();
    this.getCategories();
  },
  watch: {},
  methods: {
    addTax(val) {
      this.$store.dispatch("product/updateSellItem", {
        tax: val,
        type: "selltax"
      });
    },
    addDiscount(val) {
      this.$store.dispatch("product/updateSellItem", {
        discount: val,
        type: "selldiscount"
      });
    },
    addShippingCost(val) {
      this.$store.dispatch("product/updateSellItem", {
        shipping_cost: val,
        type: "shippingcost"
      });
    },
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

    async getProducts() {
      await this.$axios.get("/get-products", {params: {contact_id: this.form.contact_id}}).then(response => {
        this.categories = response.data;
      });
    },


    async submitForm() {
      if (this.$refs.form.validate()) {
        if (this.sellItems.length < 1) {
          this.type = "error";
          this.alert = true;
          this.message = {msg: ["Please Select Product"]};
          return;
        }
        if (this.grandTotal < 1) {
          this.type = "error";
          this.alert = true;
          this.message = {msg: ["Total price can not be 0"]};
          return;
        }
        this.full_loading = true
        await this.$axios
          .post("/sell", {
            sell_items: this.sellItems,
            business_location_id: this.form.business_location_id,
            customer_id: this.form.customer_id,
            sell_status: this.form.sell_status,
            sell_date: this.form.sell_date,
            sell_status: this.form.sell_status,
            sell_tax: this.form.sell_tax,
            invoice_tax: this.form.invoice_tax,
            sell_discount: this.form.sell_discount,
            invoice_discount: this.form.invoice_discount,
            shipping_cost: this.form.shipping_cost,
            payment_amount: this.form.payment_amount,
            payment_method: this.form.payment_method,
            payment_note: this.form.payment_note
          })
          .then(response => {
            this.isLoading = false;
            let data = {alert: true, message: "sell Addedd Successfully", type: "success"};
            this.$store.commit("SET_ALERT", data);
            this.$store.commit("SET_MODAL", true);
            this.full_loading = false
            this.$router.push({name: "sell-list"});
          });
      }
    }
  }
};
</script>

<style scoped></style>
