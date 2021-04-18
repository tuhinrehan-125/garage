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
            <v-toolbar-title> {{ $t("add_advance") }}</v-toolbar-title>
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
                    <v-select
                      :items="clients"
                      :label="$t('select_client_name')"
                      dense
                      outlined
                      required
                      v-model="form.client_id"
                      item-text="name"
                      item-value="id"
                    ></v-select>
                  </v-col>
                  <v-col cols="12" sm="12" md="12">
                    <v-text-field
                      :label="$t('amount')"
                      outlined
                      dense
                      v-model="form.amount"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="12" md="12">
                    <v-select
                      :items="payment_method"
                      :label="$t('select_payment_method')"
                      dense
                      outlined
                      required
                      v-model="form.payment_method"
                    ></v-select>
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
      <v-dialog v-model="confirmation" max-width="300">
        <v-card>
          <v-card-title>
            Are you sure?
            <v-spacer />
            <v-icon aria-label="Close" @click="dialog3 = false">
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
      <v-col cols="12" md="12" sm="12">
        <v-tabs v-model="tab">
          <v-tab v-for="item in abc" :key="item">
            {{ item }}
          </v-tab>
        </v-tabs>
        <v-tabs-items v-model="tab">
          <v-tab-item v-for="item in abc" :key="item">
            <v-card flat>
              <v-card-text>
                <v-row v-if="tab == 0">
                  <v-col>
                    <v-btn
                      tile
                      color="indigo"
                      class="float-right"
                      @click="dialog = true"
                    >
                      <v-icon left> mdi-plus </v-icon>
                      {{ $t("add_advance") }}
                    </v-btn>
                  </v-col>
                </v-row>
                <v-row v-if="tab == 0">
                  <v-col cols="12" md="12">
                    <v-card-title>
                      {{ $t("advance_list") }}
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
                      <v-skeleton-loader
                        class="mx-auto"
                        type="table"
                        v-if="isLoading"
                      ></v-skeleton-loader>
                      <v-data-table
                        v-else
                        :headers="headers"
                        :items="advanceGiven"
                      >
                        <template v-slot:item.actions="{ item }">
                          <v-btn
                            class="mx-2"
                            dark
                            small
                            color="cyan"
                            @click="editAdvance(item)"
                          >
                            <v-icon dark> mdi-pencil </v-icon>
                          </v-btn>
                          <v-btn
                            class="mx-2"
                            dark
                            small
                            color="red"
                            @click="deleteAdvance(item)"
                          >
                            <v-icon dark> mdi-delete </v-icon>
                          </v-btn>
                        </template>
                      </v-data-table>
                    </v-card-text>
                  </v-col>
                </v-row>
                <v-row v-else>
                  <v-col cols="12" md="12" sm="12">
                    <v-card-title>
                      {{ $t("advance_list") }}
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
                      <v-skeleton-loader
                        class="mx-auto"
                        type="table"
                        v-if="isLoading"
                      ></v-skeleton-loader>
                      <v-data-table
                        v-else
                        :headers="headers"
                        :items="advanceTaken"
                      >
                      </v-data-table>
                    </v-card-text>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>
          </v-tab-item>
        </v-tabs-items>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  name: "Advance",
  middleware: "auth",
  head: {
    title: "Advance",
  },
  components: {},
  data() {
    return {
      tab: this.$t("advance_given"),
      abc: [this.$t("advance_given"), this.$t("advance_taken")],
      clients: [],
      confirmation: false,
      update: false,
      headline: this.$t("add_advance"),
      alert: false,
      valid: true,
      message: "",
      isLoading: false,
      nameRules: [(v) => !!v || "Name is required"],
      dialog: false,
      form: {},
      advanceid: "",
      direction: "top right",
      payment_method: ["bKash", "Bank", "Cash"],
      categories: [],
      headers: [
        {
          sortable: false,
          text: this.$t("client_name"),
          value: "clinet_name",
        },
        {
          sortable: false,
          text: this.$t("payment_type"),
          value: "payment_method",
        },
        {
          sortable: false,
          text: this.$t("amount"),
          value: "amount",
        },
        {
          sortable: false,
          text: this.$t("date"),
          value: "created_at",
        },
        {
          sortable: false,
          text: this.$t("action"),
          value: "actions",
        },
      ],
      advanceTaken: [],
      advanceGiven: [],
    };
  },
  computed: {
    parsedDirection() {
      return this.direction.split(" ");
    },
  },
  async asyncData({ params, axios }) {},
  mounted() {
    this.getClients();
    this.getAdvance();
  },
  methods: {
    async getClients() {
      await this.$axios.get("/client").then((response) => {
        this.clients = response.data;
      });
    },
    async getAdvance() {
      this.isLoading = true;
      await this.$axios.get("/advance").then((response) => {
        this.isLoading = false;
        let advanceTaken = response.data;
        advanceTaken.map((item) =>
          item.type == "given"
            ? this.advanceGiven.push(item)
            : this.advanceTaken.push(item)
        );
      });
    },
    deleteAdvance(item) {
      this.confirmation = true;
      this.advanceid = item.id;
    },
    async confirmDelete() {
      await this.$axios.delete(`advance/${this.advanceid}`).then((res) => {
        this.alert = true;
        this.message = "Advance Deleted Successfully";
        this.confirmation = false;
        this.getAdvance();
      });
    },
    editAdvance(item) {
      this.update = true;
      this.dialog = true;
      this.headline = this.$t("advance_edit");
      this.form.client_id = item.client_id;
      this.form.phone_no = item.phone_no;
      this.form.amount = item.amount;
      this.form.payment_method = item.payment_method;
      this.advanceid = item.id;
    },
    async submitForm() {
      if (this.$refs.form.validate()) {
        if (this.update == false) {
          await this.$axios.post("/advance", this.form).then((res) => {
            this.message = "Advance Added Successfully";
            this.dialog = false;
            this.alert = true;
            this.getAdvance();
          });
        } else {
          await this.$axios
            .patch(`advance/${this.advanceid}`, this.form)
            .then((res) => {
              this.message = "Advance Updated successfully";
              this.dialog = false;
              this.alert = true;
              this.getAdvance();
            });
        }
      }
    },
  },
};
</script>

<style>
</style>
