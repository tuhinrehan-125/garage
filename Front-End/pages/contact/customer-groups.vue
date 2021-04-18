<template>
  <v-container fluid grid-list-xl class="mt-5">
    <v-row justify="center">
      <v-dialog v-model="dialog" persistent max-width="600px">
        <v-card>
          <v-card-title>
            {{ headline }}
            <v-spacer></v-spacer>
            <v-btn text @click="dialog = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
          </v-card-title>
          <v-card-text>
            <v-container>
              <v-form ref="form" v-model="valid" lazy-validation>
                <v-row no-gutters>
                  <v-col cols="12" sm="6" md="12">
                    <v-text-field
                      outlined
                      dense
                      :label="$t('name')"
                      required
                      :rules="nameRules"
                      v-model="form.name"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="6" md="12">
                    <v-text-field
                      outlined
                      dense
                      :label="$t('percentage')"
                      required
                      v-model="form.percentage_value"
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
      <v-dialog v-model="confirmation" max-width="300" class="pd">
        <v-card class="pd">
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
          @click="dialog = true"
        >
          <v-icon left> mdi-plus </v-icon>
          {{ $t("add_customer_group") }}
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
            {{ $t("Customer Group List") }}
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
            <v-data-table :headers="headers" :items="customergroups">
              <template v-slot:item.actions="{ item }">
                <v-btn
                  class="mx-2"
                  dark
                  small
                  color="cyan"
                  @click="editCustomerGroup(item)"
                >
                  <v-icon dark> mdi-pencil </v-icon>
                </v-btn>
                <v-btn
                  class="mx-2"
                  dark
                  small
                   color="red"
                  @click="deleteCustomerGroup(item)"
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
export default {
  name: "customerGroup",
  middleware: "auth",
  head: {
    title: "Customer Group",
  },
  components: {},
  data() {
    return {
      search: "",
      isLoading: false,
      confirmation: false,
      update: false,
      headline: this.$t("add_customer_group"),
      alert: false,
      valid: true,
      message: "",
      nameRules: [(v) => !!v || this.$t("customer_grp_is_required")],
      dialog: false,
      form: {
        name: "",
        percentage_value: "",
      },
      cgID: "",
      customergroups: [],
    };
  },
  computed: {
    headers() {
      return [
        {
          sortable: false,
          text: this.$t("Customer Group Name"),
          value: "name",
        },
        {
          sortable: false,
          text: this.$t("Calculation Percentage(%)"),
          value: "percentage_value",
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
    this.getCustomerGroup();
  },
  methods: {

    async getCustomerGroup() {
      this.isLoading = true;
      await this.$axios.get("/customer-group").then((response) => {
        this.customergroups = response.data;
        this.isLoading = false;
      });
    },
    deleteCustomerGroup(item) {
      this.confirmation = true;
      this.cgID = item.id;
    },
    async confirmDelete() {
      await this.$axios.delete(`customer-group/${this.cgID}`).then((res) => {
        this.alert = true;
        this.message = "Customer Group Deleted Successfully";
        this.confirmation = false;
        this.getCustomerGroup();
      });
    },
    editCustomerGroup(item) {
      this.update = true;
      this.dialog = true;
      this.headline = this.$t("unit_edit");
      this.form.name = item.name;
      this.form.percentage_value=item.percentage_value
      this.cgID = item.id;
    },
    async submitForm() {
      if (this.$refs.form.validate()) {
        if (this.update == false) {
          await this.$axios.post("/customer-group", this.form).then((res) => {
            this.message = "Customer Group saved successfully";
            this.dialog = false;
            this.alert = true;
            this.getCustomerGroup();
          });
        } else {
          await this.$axios
            .patch(`customer-group/${this.cgID}`, this.form)
            .then((res) => {
              this.message = "Customer Group updated successfully";
              this.dialog = false;
              this.alert = true;
              this.getCustomerGroup();
            });
        }
      }
    },
  },
};
</script>

<style>
</style>
