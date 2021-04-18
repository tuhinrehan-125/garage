<template>
  <v-container fluid grid-list-sm class="mt-5">
    <v-row justify="center">
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
      <v-col cols="12" md="12">
        <v-card v-if="isLoading">
          <v-skeleton-loader class="mx-auto" type="table"></v-skeleton-loader>
        </v-card>
        <v-card v-else>
          <v-card-title>
            {{ $t("Stock Transfer List") }}
            <v-spacer></v-spacer>
            <v-text-field
              style="width:100px;"
              v-model="search"
              append-icon="mdi-magnify"
              label="Search"
              single-line
              hide-details
            ></v-text-field>
          </v-card-title>
          <v-card-text>
            <v-data-table :headers="headers" :items="stockTransfers">
              <template v-slot:item.actions="{ item }">
                <v-menu open-on-hover top offset-y>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn color="primary" dark small v-bind="attrs" v-on="on">
                      <v-icon dark> mdi-dots-horizontal </v-icon>
                    </v-btn>
                  </template>
                  <v-list>
                    <v-list-item link>
                      <v-list-item-title>Edit</v-list-item-title>
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
export default {
  name: "StockTransferList",
  middleware: "auth",
  head: {
    title: "Stock Transfer List"
  },
  components: {},
  data() {
    return {
      search: "",
      isLoading: false,
      confirmation: false,
      update: false,
      stockTransfers: [],
      alert: false,
      valid: true
    };
  },
  computed: {
    headers() {
      return [
        {
          sortable: false,
          text: this.$t("From Location"),
          value: "from_business_location"
        },
        {
          sortable: false,
          text: this.$t("To Location"),
          value: "to_business_location"
        },
        {
          sortable: false,
          text: this.$t("Ref No"),
          value: "ref_no"
        },
        {
          sortable: false,
          text: this.$t("Status"),
          value: "status"
        },
        {
          sortable: false,
          text: this.$t("Shipping Cost"),
          value: "shipping_cost"
        },
        {
          sortable: false,
          text: this.$t("Total"),
          value: "total"
        },
        {
          sortable: false,
          text: this.$t("action"),
          value: "actions"
        }
      ];
    },
    parsedDirection() {
      return this.direction.split(" ");
    }
  },
  async asyncData({ params, axios }) {},
  mounted() {
    this.getStockTransfers();
  },
  methods: {
    async getStockTransfers() {
      this.isLoading = true;
      await this.$axios.get("/stock-transfer").then(response => {
         this.isLoading = false;
        this.stockTransfers = response.data;
      });
    },
    deleteUnit(item) {
      this.confirmation = true;
      this.catid = item.id;
    },
    async confirmDelete() {
      await this.$axios.delete(`unit/${this.catid}`).then(res => {
        this.alert = true;
        this.message = "Unit Deleted Successfully";
        this.confirmation = false;
        this.getUnits();
      });
    }
  }
};
</script>

<style>

</style>
