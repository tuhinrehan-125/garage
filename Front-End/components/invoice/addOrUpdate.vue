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
                    outlined
                  ></v-select>
                </v-col>
                <v-col cols="12" md="4" sm="12" xl="4">
                  <v-dialog
                    ref="dialog"
                    v-model="modal"
                    :return-value.sync="form.date"
                    persistent
                    width="290px"
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field
                        v-model="form.date"
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
                    <v-date-picker v-model="form.date" scrollable>
                      <v-spacer></v-spacer>
                      <v-btn text color="primary" @click="modal = false">
                        Cancel
                      </v-btn>
                      <v-btn
                        text
                        color="primary"
                        @click="$refs.dialog.save(form.date)"
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
                    v-model="vehicle_id"
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
              <invoice-table/>
              <v-row no-gutters>
                <v-col cols="12" md="4" sm="12" xl="4">
                  <v-text-field
                    label="Tax"
                    outlined
                    dense
                    required
                    v-model="form.invoice_tax"
                    :rules="[v => !!v || 'Tax is required']"
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
                <v-col cols="12" md="4" sm="12" xl="4">
                  <v-text-field
                    label="Paid Amount"
                    outlined
                    dense
                    required
                    v-model="form.paid_price"
                  ></v-text-field>
                </v-col>

<!--                <v-col cols="12" md="4" sm="12" xl="4" v-if="this.isEdit== true">-->
<!--                  <v-text-field-->
<!--                    label="Due Amount"-->
<!--                    outlined-->
<!--                    dense-->
<!--                    required-->
<!--                    v-model="form.due_price"-->
<!--                  ></v-text-field>-->
<!--                </v-col>-->
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
import invoiceTable from "~/components/invoice/invoiceTable";

export default {
  name: "addOrUpdate",
  middleware: "auth",

  components: {searchProduct, invoiceTable, searchInvoiceProduct},
  props: ["contacts", "data"],
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
      isEdit: false,
      // invoice_date: new Date().toISOString().substr(0, 10),
      categories: [],
      vehicles: [],
      vehicle_id:"",
      // form: {
      //   business_location_id: "",
      //   customer_id: "",
      //   sell_status: "",
      //   invoice_date: new Date().toISOString().substr(0, 10),
      //   sell_tax: "",
      //   invoice_tax:"",
      //   sell_discount: "",
      //   invoice_discount:"",
      //   shipping_cost: "",
      //   payment_amount: "",
      //   payment_method: "",
      //   payment_note: "",
      //   contact_id: "",
      //   category_id: "",
      //   vehicle_id: "",
      //   product_id:"",
      //   paid_amount:""
      // },
      form: {},
    };
  },
  computed: {

    grandTotal() {
      let grandtotal = this.$store.getters["product/invoiceTotalPrice"];
      return Math.round(grandtotal);
    },

    invoiceItems() {
      let products = this.$store.getters["product/getInvoiceItems"];
      return products;
    }
  },
  async asyncData({params, axios}) {
  },
  mounted() {
    this.getCategories();
    if (Object.keys(this.data).length > 1) {
      this.isEdit = true;
      this.form = this.data;

       this.$axios.get("/get-vehicles", {params: {contact_id:  this.form.contact_id}}).then(response => {
        this.vehicles = response.data;
        this.vehicle_id = this.data.items[0].vehicle_id;
      });
      this.$store.commit("product/SET_INVOICE_PRODUCTS", this.data.items)

    }
  },
  watch: {},
  methods: {
    addTax(val) {
      this.$store.dispatch("product/updateInvoiceItem", {
        invoice_tax: val,
        type: "invoiceTax"
      });
    },
    addDiscount(val) {
      this.$store.dispatch("product/updateInvoiceItem", {
        invoice_discount: val,
        type: "invoiceDiscount"
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

    async submitForm() {
      if (this.$refs.form.validate()) {
        if (this.invoiceItems.length < 1) {
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
        if (this.isEdit !== true) {
          await this.$axios
            .post("/invoice", {
              invoice_items: this.invoiceItems,
              contact_id: this.form.contact_id,
              vehicle_id: this.vehicle_id,
              date: this.form.date,
              vat: this.form.invoice_tax,
              discount: this.form.invoice_discount,
              paid_price: this.form.paid_price,
              // due_price: this.form.due_price,
              type: this.form.category_id
            })
            .then(response => {
              this.isLoading = false;
              let data = {alert: true, message: "Invoice Added Successfully", type: "success"};
              this.$store.commit("SET_ALERT", data);
              this.full_loading = false
              this.$router.push({name: "invoice-list"});

            });
        }
        else {
          await this.$axios
            .patch(`invoice/${this.form.id}`, {
              invoice_items: this.invoiceItems,
              contact_id: this.form.contact_id,
              vehicle_id: this.vehicle_id,
              date: this.form.date,
              vat: this.form.invoice_tax,
              discount: this.form.invoice_discount,
              paid_price: this.form.paid_price,
              // due_price: this.form.due_price,
              type: this.form.category_id
            })
            .then((res) => {
              this.isLoading = false;
              let data = {alert: true, message: "Invoice Updated Successfully", type: "success"};
              this.$store.commit("SET_ALERT", data);
              this.full_loading = false
              this.$router.push({name: "invoice-list"});
            });
        }

      }
    }
  }
}
</script>

<style scoped>

</style>
