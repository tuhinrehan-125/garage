<template>
  <v-container grid-list-sm class="mt-5" v-if="invoiceItemId ==''">
    <v-overlay :value="full_loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>

    <v-row justify="center">
      <v-dialog v-model="confirmation" max-width="300">
        <v-card>
          <v-card-title>
            Are you sure?
            <v-spacer/>
            <v-icon aria-label="Close" @click="confirmation = false">
              mdi-close
            </v-icon>
          </v-card-title>
          <v-card-text class="pb-6 pt-12 text-center">
            <v-btn class="mr-3" text @click="confirmation = false"> No</v-btn>
            <v-btn color="success" text @click="confirmDelete()"> Yes</v-btn>
          </v-card-text>
        </v-card>
      </v-dialog>
    </v-row>


    <v-row>
      <v-col>
        <v-btn tile color="indigo" class="float-right" to="/invoice/create">
          <v-icon left> mdi-plus</v-icon>
          {{ $t("Add Invoice") }}
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
            {{ $t("Invoice List") }}
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
              :items="invoiceList"
              :search="search"
              :hide-default-footer="true"
            >
              <template v-slot:item.actions="{ item }">
                <v-menu open-on-hover top offset-y>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn color="primary" dark small v-bind="attrs" v-on="on">
                      <v-icon dark> mdi-dots-horizontal</v-icon>
                    </v-btn>
                  </template>
                  <v-list>
                    <v-list-item link @click="invoiceDetail(item)">
                      <v-list-item-title>Detail</v-list-item-title>
                    </v-list-item>
<!--                    <v-list-item link @click="editProduct(item)">-->
<!--                      <v-list-item-title>Edit</v-list-item-title>-->
<!--                    </v-list-item>-->

                    <v-list-item
                      link
                      :to="{
                        name: 'invoice-edit-id',
                        params: { id: item.id }
                      }"
                    >
                      <v-list-item-title>
                        Edit</v-list-item-title
                      >
                    </v-list-item>
                    <v-list-item link @click="deleteProduct(item)">
                      <v-list-item-title>Delete</v-list-item-title>
                    </v-list-item>
                  </v-list>
                </v-menu>
              </template>
            </v-data-table>

            <v-pagination
              class="pt-5"
              v-model="pagination.current"
              :length="pagination.total"
              @input="onPageChange"
            ></v-pagination>

          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

  </v-container>
  <div v-else>
    <invoice-detail @clicked="onClickChild" :invoiceId="invoiceItemId"/>
  </div>
</template>

<script>
import addPayment from "../../components/payment/addPayment";
import viewPayment from "../../components/payment/viewPayment";
import invoiceDetail from '../../components/invoice/invoiceDetail';

export default {
  name: "Invoice",
  middleware: "auth",
  head: {
    title: "Invoice List"
  },
  components: {addPayment, viewPayment, invoiceDetail},
  data() {
    return {
      full_loading: false,
      singleitem: {},
      paymentinfo: [],
      search: "",
      isLoading: false,
      update: false,
      alert: false,
      dialog: false,
      confirmation: false,
      message: "",
      valid: true,
      sellsList: [],
      invoiceList: [],
      invoiceId: '',
      invoiceItemId: '',
      pagination: {
        current: 1,
        total: 0
      },
    };
  },
  computed: {
    headers() {
      return [
        {
          sortable: false,
          text: this.$t("Invoice Number"),
          value: "invoice_number"
        },
        {
          sortable: false,
          text: this.$t("Client name"),
          value: "client_name"
        },
        {
          sortable: false,
          text: this.$t("Date"),
          value: "date"
        },
        {
          sortable: false,
          text: this.$t("Total Amount"),
          value: "total_amount"
        },
        {
          sortable: false,
          text: this.$t("Paid Amount"),
          value: "paid_price"
        },
        {
          sortable: false,
          text: this.$t("Due Amount"),
          // value: "due_amount"
          value: "due_price"
        },
        {
          sortable: false,
          text: this.$t("Vat"),
          value: "vat"
        },

        {
          sortable: false,
          text: this.$t("action"),
          value: "actions"
        }
      ];
    }
  },
  async asyncData({params, axios}) {
  },
  mounted() {
    this.getInvoiceList();
  },
  methods: {
    onPageChange() {
      this.getInvoiceList();
    },

    onClickChild (value) {
      this.invoiceItemId = value;
      this.getInvoiceList();
    },
    invoiceDetail(val) {
      this.invoiceItemId = val;
    },

    openAddPayment(item) {
      this.singleitem = item;
      this.$store.commit("SET_MODAL", {type: "addpayment", status: true});
    },
    async openViewPayment(item) {
      this.full_loading = true;
      await this.$axios.get("sell-payment?id=" + item.id).then(res => {
        this.paymentinfo = res.data.data;
        this.$store.commit("SET_MODAL", {type: "viewpayment", status: true});
        this.full_loading = false;
      });
    },
    deleteProduct(item) {
      this.confirmation = true;
      this.invoiceId = item.id;
    },
    async confirmDelete() {
      await this.$axios.delete(`invoice/${this.invoiceId}`).then(res => {
        this.alert = true;
        this.message = "Invoice Deleted Successfully";
        this.confirmation = false;
        this.getInvoiceList();
      });
    },

    // async getInvoiceList() {
    //   this.isLoading = true;
    //   await this.$axios.get("/invoice").then(response => {
    //     this.isLoading = false;
    //     this.invoiceList = response.data;
    //   });
    // },
    async getInvoiceList() {
      this.isLoading = true;
      await this.$axios.get('/invoice?page=' + this.pagination.current).then(response => {
        this.isLoading = false;
        this.invoiceList = response.data.data;
        this.pagination.current = response.data.meta.current_page;
        this.pagination.total = response.data.meta.last_page;
      });
    },

  }
};
</script>

<style lang="scss" scoped>
</style>
