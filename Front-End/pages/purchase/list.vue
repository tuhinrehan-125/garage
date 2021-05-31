<template>
  <v-container grid-list-sm class="mt-5">
    <v-overlay :value="full_loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>
    <add-payment :item="singleitem" type="purchase" />
    <view-payment :data="paymentinfo" />
    <return-purchase :item="singleitem" :ItemList="purchaseItems" />
    <v-row justify="center">
      <v-dialog v-model="confirmation" max-width="300">
        <v-card>
          <v-card-title>
            Are you sure?
            <v-spacer />
            <v-icon aria-label="Close" @click="confirmation = false">
              mdi-close
            </v-icon>
          </v-card-title>
          <v-card-text class="pb-6 pt-12 text-center">
            <v-btn class="mr-3" text @click="confirmation = false"> No </v-btn>
            <v-btn color="success" text @click="confirmDelete()"> Yes </v-btn>
          </v-card-text>
        </v-card>
      </v-dialog>
    </v-row>
    <v-row>
      <v-col>
        <v-btn tile color="indigo" class="float-right" to="/purchase/add">
          <v-icon left> mdi-plus </v-icon>
          {{ $t("Add Purchase") }}
        </v-btn>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12" md="12">
        <v-card v-if="isLoading">
          <v-skeleton-loader class="mx-auto" type="table"></v-skeleton-loader>
        </v-card>
        <v-card v-else>
          <v-card-title>
            {{ $t("Purchase List") }}
            <v-spacer></v-spacer>
            <v-text-field
              v-model="search"
              append-icon="mdi-magnify"
              label="Search"
              single-line
              hide-details
            ></v-text-field>
          </v-card-title>
          <v-card-text>
            <v-data-table
              :headers="headers"
              :items="purchaselist"
              :search="search"
            >
              <template v-slot:item.image="{ item }">
                <img
                  class="product-img"
                  :src="item.image"
                  style="width: 50px; height: 50px"
                />
              </template>

              <template v-slot:item.actions="{ item }">
                <v-menu open-on-hover top offset-y>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn color="primary" dark small v-bind="attrs" v-on="on">
                      <v-icon dark> mdi-dots-horizontal </v-icon>
                    </v-btn>
                  </template>
                  <v-list>
                    <v-list-item
                      link
                      @click="openAddPayment(item)"
                      v-if="item.total_due != 0"
                    >
                      <v-list-item-title>Add Payment</v-list-item-title>
                    </v-list-item>
                    <v-list-item link @click="openViewPayment(item)">
                      <v-list-item-title>View Payment</v-list-item-title>
                    </v-list-item>
                    <v-list-item link @click="openReturnPurchase(item)">
                      <v-list-item-title>Return Purchase</v-list-item-title>
                    </v-list-item>
                    <v-list-item
                      link
                      :to="{
                        name: 'purchase-edit-id',
                        params: { id: item.id }
                      }"
                    >
                      <v-list-item-title> Edit</v-list-item-title>
                    </v-list-item>
                    <v-list-item link @click="deleteProduct(item)">
                      <v-list-item-title>Delete</v-list-item-title>
                    </v-list-item>
                  </v-list>
                </v-menu>
              </template>
            </v-data-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import addPayment from "../../components/payment/addPayment";
import viewPayment from "../../components/payment/viewPayment";
import returnPurchase from "../../components/purchase/returnPurchase";
export default {
  name: "Purchase",
  middleware: "auth",
  head: {
    title: "Purchase List"
  },
  components: { addPayment, viewPayment, returnPurchase },
  data() {
    return {
      full_loading: false,
      purchaseItems: [],
      singleitem: {},
      paymentinfo: [],
      search: "",
      isLoading: false,
      update: false,
      alert: false,
      dialog: false,
      confirmation: false,
      message: "",
      headline: this.$t("Add Purchase"),
      valid: true,
      nameRules: [v => !!v || this.$t("purchase_name_is_required")],
      form: {
        date: "",
        reference: "",
        supplier: "",
        purchase_status: "",
        grand_total: "",
        paid: "",
        due: "",
        payment_status: ""
      },
      prodid: "",
      categories: [],
      purchaselist: []
    };
  },
  computed: {
    headers() {
      return [
        {
          sortable: false,
          text: this.$t("Purchase Number"),
          value: "purchase_number"
        },
        {
          sortable: false,
          text: this.$t("Date"),
          value: "purchase_date"
        },
        {
          sortable: false,
          text: this.$t("Supplier"),
          value: "supplier"
        },
        {
          sortable: false,
          text: this.$t("Purchase Stutas"),
          value: "purchase_status"
        },

        {
          sortable: false,
          text: this.$t("Total Amount"),
          value: "total_cost"
        },
        {
          sortable: false,
          text: this.$t("Paid Amount"),
          value: "paid_amount"
        },
        {
          sortable: false,
          text: this.$t("Due Amount"),
          value: "due_amount"
        },
        {
          sortable: false,
          text: this.$t("Payment Status"),
          value: "payment_status"
        },
        {
          sortable: false,
          text: this.$t("action"),
          value: "actions"
        }
      ];
    }
  },
  async asyncData({ params, axios }) {},
  mounted() {
    this.getPurchaseList();
  },
  methods: {
    openAddPayment(item) {
      this.singleitem = item;
      this.$store.commit("SET_MODAL", { type: "addpayment", status: true });
    },
    async openViewPayment(item) {
      this.full_loading = true;
      await this.$axios.get("purchase-payment?id=" + item.id).then(res => {
        this.paymentinfo = res.data.data;
        this.$store.commit("SET_MODAL", { type: "viewpayment", status: true });
        this.full_loading = false;
      });
    },
    async openReturnPurchase(item) {
      this.full_loading = true;
      await this.$axios
        .get("purchase-items?purchase_id=" + item.id)
        .then(res => {
          this.purchaseItems = res.data;
          this.singleitem = item;
          this.$store.commit("SET_MODAL", {
            type: "returnPurchase",
            status: true
          });
          this.full_loading = false;
        });
    },
    deleteProduct(item) {
      this.confirmation = true;
      this.prodid = item.id;
    },
    async confirmDelete() {
      await this.$axios.delete(`product/${this.prodid}`).then(res => {
        this.alert = true;
        this.message = "Purchase Deleted Successfully";
        this.confirmation = false;
        this.getProducts();
      });
    },
    async getPurchaseList() {
      // this.isLoading = true;
      await this.$axios.get("/purchase").then(response => {
        // this.isLoading = false;
        this.purchaselist = response.data;
        console.log(this.purchaselist);
      });
    }
  }
};
</script>

<style lang="scss" scoped></style>
