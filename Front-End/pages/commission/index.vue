<template>
  <v-container fluid grid-list-xl class="mt-5">
    <v-row justify="center">
      <base-material-snackbar
        v-model="alert"
        type="success"
        v-bind="{
          [parsedDirection[0]]: true,
          [parsedDirection[1]]: true,
        }"
      >
        {{ message }}
      </base-material-snackbar>
      <v-dialog v-model="dialog" persistent max-width="600px">
        <v-card>
          <v-toolbar dark color="primary">
            <v-toolbar-title>{{ headline }}</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-toolbar-items>
              <v-btn dark text @click="dialog = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-toolbar-items>
          </v-toolbar>
          <v-card-text>
            <v-container>
              <v-form ref="form" v-model="valid" lazy-validation>
                <v-row>
                  <v-col cols="12" sm="12" md="12">
                    <v-text-field
                      :label="$t('percentage')"
                      outlined
                      dense
                      :rules="percentageRules"
                      v-model="form.commission"
                    ></v-text-field>
                  </v-col>
                </v-row>
              </v-form>
            </v-container>
            <small>{{ $t("indicates_required_field") }}</small>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="blue darken-1" text @click="dialog = false">
              {{ $t("close") }}
            </v-btn>
            <v-btn color="blue darken-1" text @click="submitForm">
              {{ $t("save") }}
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-row>

    <v-row>
      <v-col cols="12" md="12">
        <v-card>
          <v-card-title>
            {{ $t("commissions") }}
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
            <v-data-table :headers="headers" :items="customers">
              <template v-slot:item.actions="{ item }">
                <v-btn
                  class="mx-2"
                  dark
                  small
                  color="cyan"
                  @click="editCommission(item)"
                >
                  <v-icon dark> mdi-pencil </v-icon>
                </v-btn>
                <!-- <v-btn
                  class="mx-2"
                  dark
                  small
                  color="red"
                  @click="deleteCommission(item)"
                >
                  <v-icon dark> mdi-delete </v-icon>
                </v-btn> -->
              </template>
            </v-data-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  name: "Commission",
  middleware: "auth",
  head: {
    title: "Commission",
  },
  components: {},
  data() {
    return {
      form: {
        commission: 0,
        customer_id: "",
      },

      selectedCustomer: null,
      customers: [],
      customerLoading: false,
      confirmation: false,
      customerSearch: null,
      update: false,
      headline: this.$t("add_commission"),
      alert: false,
      valid: true,
      message: "",
      percentageRules: [(v) => !!v || this.$t("percentage_is_required")],
      dialog: false,
      comid: "",
      direction: "top right",
      categories: [],
      headers: [
        {
          sortable: false,
          text: this.$t("customer_name"),
          value: "name",
        },
        {
          sortable: false,
          text: this.$t("commission"),
          value: "commission",
        },
        {
          sortable: false,
          text: this.$t("action"),
          value: "actions",
        },
      ],
      items: [],
    };
  },
  computed: {
    parsedDirection() {
      return this.direction.split(" ");
    },
  },
  async asyncData({ params, axios }) {},
  mounted() {
    this.getCustomers();
  },
  methods: {
    async getCustomers() {
      await this.$axios.get("/customer").then((response) => {
        this.customers = response.data;
      });
    },
    // deleteCommission(item) {
    //   this.confirmation = true;
    //   this.form.customer_id = item.customer_id;
    // },
    // async confirmDelete() {
    //   await this.$axios.delete(`commission/${this.comid}`).then((res) => {
    //     this.alert = true;
    //     this.message = "Customer Commission Deleted";
    //     this.confirmation = false;
    //     this.getCustomers();
    //   });
    // },
    editCommission(item) {
      this.update = true;
      this.dialog = true;
      this.headline = this.$t("edit_commission");
      this.form.commission = item.commission;
      this.form.customer_id = item.id;
    },

    async submitForm() {
      if (this.$refs.form.validate()) {
        await this.$axios
          .patch(`customer/${this.form.customer_id}`, this.form)
          .then((res) => {
            this.message = "Comission Updated Successfully";
            this.dialog = false;
            this.alert = true;
            this.getCustomers();
          });
      }
    },
  },
  watch: {
    // customerSearch(val) {
    //   this.customerLoading = true;
    //   this.$axios
    //     .get("/customer-search?name=" + val)
    //     .then((res) => {
    //       this.customers = res.data;
    //       this.customerLoading = false;
    //     })
    //     .catch((err) => {
    //       console.log(err);
    //       this.customerLoading = false;
    //     });
    // },
    selectedCustomer(val) {
      this.form.customer_id = val.id;
    },
  },
};
</script>

<style>
</style>
