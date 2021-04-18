<template>
  <v-container fluid grid-list-xl class="mt-5">
    <base-material-snackbar
      v-model="alert"
      :type="type"
      v-bind="{
        [parsedDirection[0]]: true,
        [parsedDirection[1]]: true,
      }"
    >
      {{ message }}
    </base-material-snackbar>
    <v-row>
      <div id="printMe">
        <Invoice
          :client="selectedClient"
          :invoiceno="invoice_no"
          :products="cartItems"
          :subTotalPrice="subTotal"
          :commission="commission"
          :totalPrice="totalPrice"
          :grandTotal="grandTotal"
          :advance_payout="advance_payout"
        />
      </div>

      <v-col cols="12" sm="3" md="3">
        <v-autocomplete
          v-model="selectedProduct"
          :items="products"
          :loading="prductLoading"
          :search-input.sync="productSearch"
          solo
          clearable
          hide-no-data
          hide-selected
          item-text="name"
          item-value="id"
          label="Type To Search Product"
          placeholder="Type To Search Product"
          return-object
        ></v-autocomplete>
      </v-col>
      <v-col cols="12" sm="3" md="3">
        <v-select
          :items="clients"
          label="Select client name"
          solo
          required
          ref="client"
          v-model="client_id"
          item-text="name"
          item-value="id"
        ></v-select>
      </v-col>
    </v-row>
    <v-row style="margin-top: -40px" class="fullTable">
      <v-col cols="12" sm="8" md="8">
        <base-material-card color="primary">
          <template v-slot:heading>
            <div class="body-3 font-weight-light">
              {{ $t("product_list") }}
            </div>
          </template>
          <v-card-text class="pos-table">
            <v-data-table
              :headers="headers"
              :items="cartItems"
              fixed-header
              height="400px"
            >
              <template v-slot:item.price="{ item }">
                <v-text-field
                  solo
                  :value="item.price"
                  size="small"
                  @keyup="
                    priceChange(
                      $event.target.value,
                      item,
                      cartItems.indexOf(item)
                    )
                  "
                >
                </v-text-field>
              </template>

              <template v-slot:item.qty="{ item }">
                <v-select
                  :style="item.qtymethod == 'KG' ? baseStyles : ''"
                  @change="qtyMethodChange($event, cartItems.indexOf(item))"
                  :value="item.qtymethod"
                  :items="units"
                  :label="$t('unit')"
                  solo
                  item-text="name"
                  item-value="name"
                ></v-select>
                <v-text-field
                  style="height: 50px"
                  v-if="item.qtymethod == 'KG'"
                  solo
                  size="small"
                  :label="$t('quantity')"
                  type="number"
                  :value="item.qty"
                  @change="qtyChange($event, cartItems.indexOf(item))"
                ></v-text-field>
              </template>
              <template v-slot:item.customer_name="{ item }">
                <v-select
                  :items="customers"
                  label="Select customer name"
                  solo
                  @change="customerChange($event, cartItems.indexOf(item))"
                  item-text="name"
                  item-value="id"
                  style="margin-top: 14px"
                ></v-select>
              </template>

              <template v-slot:item.status="{ item }" style="margin-left: 20%">
                <v-radio-group
                  @change="paymentMethodChange($event, cartItems.indexOf(item))"
                  row
                  :value="item.ptype"
                >
                  <v-radio label="Nogod" value="Nogod"></v-radio>
                  <v-radio label="Bokeya" value="Bokeya"></v-radio>
                </v-radio-group>
              </template>

              <template v-slot:item.actions="{ item }">
                <!-- <v-icon small class="mr-2" @click="editItem(item)">
                  mdi-pencil
                </v-icon> -->

                <v-icon
                  small
                  @click="removeItem(item, cartItems.indexOf(item))"
                >
                  mdi-delete
                </v-icon>
              </template>
            </v-data-table>
          </v-card-text>
        </base-material-card>

        <v-spacer />

        <v-card class="mx-auto" max-width="500">
          <v-row>
            <v-col cols="5" md="5" sm="5">
              <v-list-item class="float-right">
                <v-list-item-content>
                  <v-list-item-title class="body-2">{{
                    $t("sub_total")
                  }}</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-col>
            <v-col cols="2" md="2" sm="2">
              <v-list-item>
                <v-list-item-content>
                  <v-list-item-title>=</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-col>
            <v-col cols="5" md="5" sm="5">
              <v-list-item>
                <v-list-item-content>
                  <v-list-item-title>{{ subTotal }}</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-col>
          </v-row>
          <v-divider></v-divider>
          <v-row>
            <v-col cols="5" md="5" sm="5">
              <v-list-item class="float-right">
                <v-list-item-content>
                  <v-list-item-title class="body-2">{{
                    $t("commission")
                  }}</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-col>
            <v-col cols="2" md="2" sm="2">
              <v-list-item>
                <v-list-item-content>
                  <v-list-item-title>=</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-col>
            <v-col cols="5" md="5" sm="5" class="float-right">
              <v-list-item>
                <v-list-item-content>
                  <v-list-item-title>
                    <v-text-field
                      v-model="purchasecommission"
                      hide-details
                      single-line
                      outlined
                      dense
                      style="width: 100px"
                      type="number"
                      min="0"
                      append-icon="mdi-percent"
                    />
                  </v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-col>
          </v-row>
          <v-divider></v-divider>
          <v-row>
            <v-col cols="5" md="5" sm="5">
              <v-list-item class="float-right">
                <v-list-item-content>
                  <v-list-item-title class="body-2">{{
                    $t("total")
                  }}</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-col>
            <v-col cols="2" md="2" sm="2">
              <v-list-item>
                <v-list-item-content>
                  <v-list-item-title>=</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-col>
            <v-col cols="5" md="5" sm="5" class="float-right">
              <v-list-item>
                <v-list-item-content>
                  <v-list-item-title>{{ totalPrice }}</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-col>
          </v-row>
          <v-divider></v-divider>
          <v-row>
            <v-col cols="5" md="5" sm="5">
              <v-list-item class="float-right">
                <v-list-item-content>
                  <v-list-item-title class="body-2">{{
                    $t("dadan")
                  }}</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-col>
            <v-col cols="2" md="2" sm="2">
              <v-list-item>
                <v-list-item-content>
                  <v-list-item-title>=</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-col>
            <v-col cols="5" md="5" sm="5" class="float-right">
              <v-list-item>
                <v-list-item-content>
                  <v-list-item-title>{{ advancetaken }}</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-col>
          </v-row>
          <v-divider></v-divider>
          <v-row>
            <v-col cols="5" md="5" sm="5">
              <v-list-item class="float-right">
                <v-list-item-content>
                  <v-list-item-title class="body-2">{{
                    $t("dadan_payout")
                  }}</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-col>
            <v-col cols="2" md="2" sm="2">
              <v-list-item>
                <v-list-item-content>
                  <v-list-item-title>=</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-col>
            <v-col cols="5" md="5" sm="5" class="float-right">
              <v-list-item>
                <v-list-item-content>
                  <v-list-item-title>
                    <v-text-field
                      v-model="advance_payout"
                      hide-details
                      single-line
                      outlined
                      dense
                      style="width: 100px"
                      type="number"
                      min="0"
                    />
                  </v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-col>
          </v-row>
          <v-divider></v-divider>
          <v-row>
            <v-col cols="5" md="5" sm="5">
              <v-list-item class="float-right">
                <v-list-item-content>
                  <v-list-item-title class="body-2">{{
                    $t("in_total")
                  }}</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-col>
            <v-col cols="2" md="2" sm="2">
              <v-list-item>
                <v-list-item-content>
                  <v-list-item-title>=</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-col>
            <v-col cols="5" md="5" sm="5" class="float-right">
              <v-list-item>
                <v-list-item-content>
                  <v-list-item-title>{{ grandTotal }}</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-col>
          </v-row>
        </v-card>
      </v-col>
      <v-col cols="12" sm="4" md="4">
        <v-row>
          <v-col cols="12" md="12">
            <v-card>
              <v-tabs dark background-color="primary" style="margin-top: -18px">
                <v-tabs-slider
                  color="primary lighten-3"
                  elevation="2"
                ></v-tabs-slider>

                <v-tab v-for="(cat, i) in items" :key="i" :href="'#tab-' + i">
                  {{ cat.category }}
                </v-tab>

                <v-tab-item
                  v-for="(amc, index) in items"
                  :key="index"
                  :value="'tab-' + index"
                >
                  <v-container fluid>
                    <v-row>
                      <v-col
                        v-for="(pro, m) in amc.products"
                        :key="m"
                        cols="3"
                        md="3"
                      >
                        <v-item v-slot="{ active, toggle }">
                          <v-card
                            :color="active ? 'primary' : ''"
                            class="d-flex align-center"
                            light
                            height="100"
                            width="140"
                            @click="addtoCart(pro)"
                          >
                            <v-img :src="pro.image"></v-img>
                          </v-card>
                        </v-item>
                        <p class="body-2">{{ pro.name }}</p>
                      </v-col>
                    </v-row>
                  </v-container>
                </v-tab-item>
              </v-tabs>
            </v-card>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
    <v-row>
      <v-col>
        <v-btn tile color="success" class="float-right" @click="makeSell">
          <v-icon left> mdi-cart-arrow-up </v-icon>
          {{ $t("make_sale") }}
        </v-btn>
        <v-btn tile color="primary" class="float-right" @click="makeInvoice">
          <v-icon left> mdi-file-document </v-icon>
          {{ $t("make_invoice") }}
        </v-btn>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import Vue from "vue";
import Invoice from "@/components/print/invoice";
import VueHtmlToPaper from "vue-html-to-paper";
const options = {
  name: "_blank",
  specs: ["fullscreen=yes", "titlebar=yes", "scrollbars=yes"],
  styles: [
    "https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css",
  ],
};

Vue.use(VueHtmlToPaper, options);

export default {
  name: "pos",
  middleware: "auth",
  head: {
    title: "POS",
  },
  components: { Invoice },
  data() {
    return {
      baseStyles: {
        marginTop: "20px",
        height: "50px",
      },
      units: [{ name: "KG" }, { name: "Thika" }],
      advancetaken: 0.0,
      purchasecommission: 0,
      advance_payout: 0.0,
      qty: 1,
      amount: "",
      type: "success",
      active: "",
      tab: "",
      message: "",
      alert: false,
      client_id: null,
      form: {},
      items: [],
      prductLoading: false,
      selectedProduct: null,
      selectedCustomer: null,
      selectedClient: null,
      productSearch: null,
      clientSearch: null,
      products: [],
      customerLoading: false,
      clientLoading: false,
      customerSearch: null,
      clientSearch: null,
      direction: "top right",
      customers: [],
      clients: [],
      form: {},
      invoice_no: "",
    };
  },
  computed: {
    headers() {
      return [
        {
          sortable: false,
          text: this.$t("item_name"),
          value: "product",
          align: "left",
          width: "120px",
        },
        {
          sortable: false,
          text: this.$t("price") + " (" + this.$t("in_mon") + ")",
          value: "price",
          align: "center",
          width: "100px",
        },
        {
          sortable: false,
          text: this.$t("quantity"),
          value: "qty",
          align: "center",
          width: "110px",
        },
        {
          sortable: false,
          text: this.$t("total"),
          value: "total",
          align: "center",
          width: "110px",
        },
        {
          sortable: false,
          text: this.$t("customer_name"),
          value: "customer_name",
          align: "center",
          width: "200px",
        },
        {
          sortable: false,
          text: this.$t("status"),
          align: "center",
          value: "status",
          width: "150px",
        },
        {
          sortable: false,
          text: this.$t("action"),
          value: "actions",
          width: "100px",
        },
      ];
    },
    parsedDirection() {
      return this.direction.split(" ");
    },
    cartItems() {
      let products = this.$store.getters["cart/getCartProducts"];
      return products;
    },
    subTotal() {
      return this.$store.getters["cart/subTotalPrice"];
    },
    commission() {
      return this.$store.getters["cart/commission"];
    },
    advancepayout() {
      return this.$store.getters["cart/advancepayout"];
    },
    totalPrice() {
      return Math.round(this.$store.getters["cart/totalPrice"]);
    },
    grandTotal() {
      let grandtotal = this.$store.getters["cart/grandTotal"];
      return Math.round(grandtotal);
    },
  },
  async asyncData({ params, axios }) {},
  mounted() {
    this.getPosProducts();
    this.getCustomers();
    this.getClients();
    this.getSettings();
  },

  methods: {
    makeInvoice() {
      if (this.client_id == null) {
        this.type = "error";
        this.alert = true;
        this.message = "Please Select a client";
        return;
      }
      this.$axios
        .post("/order", {
          products: this.cartItems,
          commission: this.commission,
          advance_payout: this.advancepayout,
          sub_total: this.subTotal,
          total_price: this.totalPrice,
          grand_total: this.grandTotal,
          client: this.client_id,
          type: "invoice",
        })
        .then((res) => {
          this.type = "success";
          this.alert = true;
          this.message = "Products saved into invoice";
          this.$htmlToPaper("printMe");
          this.advancetaken = 0;
          this.client_id = "";
          this.advance_payout = 0;
          this.purchasecommission = 0;
          this.$store.commit("cart/SET_CART_PRODUCTS", []);
          this.$store.commit("cart/SET_ADVANCE_PAYOUT", 0);
        })
        .catch((err) => {
          this.type = "error";
          this.alert = true;
          this.message = "Server Error";
        });
    },
    makeSell() {
      if (this.client_id == null) {
        this.type = "error";
        this.alert = true;
        this.message = "Please Select a client";
        return;
      }
      this.$axios
        .post("/order", {
          products: this.cartItems,
          commission: this.commission,
          advance_payout: this.advancepayout,
          sub_total: this.subTotal,
          total_price: this.totalPrice,
          grand_total: this.grandTotal,
          client: this.client_id,
        })
        .then((res) => {
          this.type = "success";
          this.alert = true;
          this.message = "Products saved into invoice";
          this.$htmlToPaper("printMe");
          this.advancetaken = 0;
          this.client_id = "";
          this.advance_payout = 0;
          this.purchasecommission = 0;
          this.$store.commit("cart/SET_CART_PRODUCTS", []);
          this.$store.commit("cart/SET_ADVANCE_PAYOUT", 0);
        })
        .catch((err) => {
          this.type = "error";
          this.alert = true;
          this.message = "Server Error";
        });
    },
    qtyMethodChange(val, index) {
      this.$store.dispatch("cart/updateCartItem", {
        qtymethod: val,
        index: index,
        type: "qtyMchnage",
      });
    },
    paymentMethodChange(val, index) {
      this.$store.dispatch("cart/updateCartItem", {
        ptype: val,
        index: index,
        type: "paymentChange",
      });
    },
    customerChange(val, index) {
      let customerInfo = this.customers.find((item) => {
        return val == item.id;
      });
      this.$store.dispatch("cart/updateCartItem", {
        customer_id: val,
        customer_name: customerInfo.name,
        index: index,
        type: "customerchange",
      });
    },
    qtyChange(val, index) {
      this.$store.dispatch("cart/updateCartItem", {
        qty: val,
        index: index,
        type: "qtychange",
      });
    },
    priceChange(val, item, index) {
      this.$store.dispatch("cart/updateCartItem", {
        product: item,
        price: parseInt(val),
        index: index,
        type: "pricechange",
      });
    },
    async getCustomers() {
      await this.$axios.get("/customer").then((response) => {
        this.customers = response.data;
      });
    },
    async getClients() {
      await this.$axios.get("/client").then((response) => {
        this.clients = response.data;
      });
    },
    getPosProducts() {
      this.$axios.get("/pos-prodcuts").then((res) => {
        this.items = res.data.data;
      });
    },
    async getSettings() {
      await this.$axios.get("/settings").then((res) => {
        this.invoice_no = res.data.invoice_no;
      });
    },
    editItem(item) {},
    removeItem(item, index) {
      this.$store.dispatch("cart/removeCartItem", {
        product: item,
        index: index,
      });
      this.type = "success";
      this.alert = true;
      this.message = "Product Removed";
    },

    addtoCart(item) {
      this.$store.dispatch("cart/addToCart", {
        product: item.name,
        product_id: item.id,
        price: "",
        quantity: "",
        customer_id: "",
        customer_name: "",
        ptype: "Bokeya",
        qtymethod: "KG",
      });
      this.type = "success";
      this.alert = true;
      this.message = "Product added into list";
    },
  },
  watch: {
    purchasecommission(val) {
      let commission = val;
      this.$store.commit("cart/SET_COMMISSION", commission);
    },
    selectedCustomer(val) {
      this.purchasecommission = val.commission;
    },
    selectedProduct(val) {
      this.$store.dispatch("cart/addToCart", {
        product: val.name,
        product_id: val.id,
        price: "",
        quantity: "",
        customer_id: "",
        customer_name: "",
        ptype: "Bokeya",
        qtymethod: "KG",
      });
      this.type = "success";
      this.alert = true;
      this.message = "Product added into list";
    },
    productSearch(val) {
      this.prductLoading = true;
      this.$axios
        .get("/product-search?name=" + val)
        .then((res) => {
          this.products = res.data;
          this.prductLoading = false;
        })
        .catch((err) => {
          console.log(err);
          this.prductLoading = false;
        });
    },
    clientSearch(val) {
      this.clientLoading = true;
      this.$axios
        .get("/client-search?name=" + val)
        .then((res) => {
          this.clients = res.data;
          this.clientLoading = false;
        })
        .catch((err) => {
          console.log(err);
          this.clientLoading = false;
        });
    },
    customerSearch(val) {
      this.customerLoading = true;
      this.$axios
        .get("/customer-search?name=" + val)
        .then((res) => {
          this.customers = res.data;
          console.log(res.data.commission);
          this.customerLoading = false;
        })
        .catch((err) => {
          console.log(err);
          this.customerLoading = false;
        });
    },
    client_id(val) {
      let client = this.clients.find((item) => {
        return val == item.id;
      });
      this.purchasecommission = client.commission_rate;
      this.selectedClient = client;
      this.advancetaken = client.due;
      this.$store.commit("cart/SET_ADVANCE", this.advancetaken);
    },
    advance_payout(val) {
      this.$store.commit("cart/SET_ADVANCE_PAYOUT", val);
    },
  },
};
</script>

<style>
.pos-table {
  padding: 0px;
}
.number-input--inline {
  width: 105px;
}
#printMe {
  display: none;
}
.v-text-field.v-text-field--solo .v-input__control {
  min-height: 10px;
}
</style>
