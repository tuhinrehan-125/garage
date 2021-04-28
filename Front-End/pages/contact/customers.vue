<template>
  <v-container grid-list-sm>
    <v-row justify="center">
      <add-customer @refresh="getcustomer()" />
      <edit-customer :item="singleItem" @refresh="getcustomer()" />
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
        <v-btn
          tile
          color="indigo"
          class="float-right"
          @click="opendialog('add')"
        >
          <v-icon left> mdi-plus </v-icon>
          {{ $t("Add Client") }}
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
            {{ $t("client_list") }}
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
            <v-data-table :headers="headers" :items="customer" :search="search">
              <template v-slot:item.actions="{ item }">
                <v-btn
                  class="mx-2"
                  dark
                  small
                  color="cyan"
                  @click="editcustomer(item)"
                >
                  <v-icon dark> mdi-pencil </v-icon>
                </v-btn>
                <v-btn
                  class="mx-2"
                  dark
                  small
                  color="red"
                  @click="deleteClient(item)"
                >
                  <v-icon dark> mdi-delete </v-icon>
                </v-btn>
              </template>
            </v-data-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import addCustomer from "../../components/contacts/addCustomer";
import editCustomer from "../../components/contacts/editCustomer";
export default {
  name: "customer",
  middleware: "auth",
   head() {
    return {
      title: "Customer",
    };
  },
  components: { addCustomer, editCustomer },
  data() {
    return {
      search: "",
      isLoading: false,
      headline: this.$t("add_customer"),
      update: false,
      clienttid: "",
      confirmation: false,
      customer: [],
      singleItem: {},
    };
  },
  computed: {
    headers() {
      return [
        {
          sortable: false,
          text: this.$t("Client name"),
          value: "name",
        },
        {
          sortable: false,
          text: this.$t("mobile"),
          value: "mobile",
        },
        {
          sortable: false,
          text: this.$t("email"),
          value: "email",
        },
        {
          sortable: false,
          text: this.$t("address"),
          value: "address",
        },
        {
          sortable: false,
          text: this.$t("action"),
          value: "actions",
        },
      ];
    },
  },
  async asyncData({ params, axios }) {},
  mounted() {
    this.getcustomer();
  },
  methods: {
    opendialog(type) {
      this.$store.commit("SET_MODAL", { type: type, status: true });
    },
    async getcustomer() {
      this.isLoading = true;
      await this.$axios
        .get("/contact?type=customer")
        .then((response) => {
          this.$store.commit("SET_ALERT", { alert: false, message: "" });
          this.isLoading = false;
          this.customer = response.data;
        })
        .catch((err) => {
          console.log("error");
        });
    },
    deleteClient(item) {
      this.confirmation = true;
      this.customerid = item.id;
    },
    editcustomer(item) {
      this.$store.commit("SET_MODAL", { type: "edit", status: true });
      this.singleItem = item;
    },
    async confirmDelete() {
      await this.$axios.delete(`contact/${this.customerid}`).then((res) => {
        let data = { alert: true, message: "Client Deleted Successfully",type:'success'};
        this.$store.commit("SET_ALERT", data);
        this.$store.commit("SET_MODAL", false);
        this.confirmation = false;
        this.getcustomer();
      });
    },
  },
  watch: {
    customer(val) {
      this.customer = val;
    },
  },
};
</script>

<style>
</style>
