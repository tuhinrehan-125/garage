<template>
  <v-container>
    <v-row>
      <v-col>
        <h3 class="text-center">{{ $t("collection_report") }}</h3>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12" sm="12" md="6">
        <v-card>
          <v-card-title>{{ $t("filter") }}</v-card-title>
          <v-card-text>
            <v-row>
              <v-col cols="12" sm="2" md="3">
                <p>Date Range:</p>
              </v-col>
              <v-col cols="12" sm="5" md="5">
                <date-range-picker
                  ref="picker"
                  opens="center"
                  v-model="dateRange"
                  style="min-width: 150px"
                >
                  <template v-slot:input="picker">
                    {{ picker.startDate | date }} - {{ picker.endDate | date }}
                  </template>
                </date-range-picker>
              </v-col>
              <v-col cols="12" sm="5" md="3">
                <v-btn
                  elevation="2"
                  medium
                  color="indigo"
                  @click="collectionSearch"
                  >Sumit</v-btn
                >
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12" sm="12" md="12">
        <v-card>
          <v-card-title>
            {{ $t("collection_report") }}
          </v-card-title>

          <v-card-text>
            <v-skeleton-loader
              class="mx-auto"
              type="table"
              v-if="isLoading"
            ></v-skeleton-loader>
            <v-data-table v-else :headers="headers" :items="allCollection">
            </v-data-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import DateRangePicker from "vue2-daterange-picker";
import "vue2-daterange-picker/dist/vue2-daterange-picker.css";
export default {
  name: "collectionReport",
  head: {
    title: "Collection Report",
  },
  components: { DateRangePicker },
  data() {
    let startDate = new Date();
    let endDate = new Date();
    startDate.setDate(endDate.getDate() - 30);
    return {
      isLoading: false,
      allCollection: [],
      dateRange: { startDate, endDate },
      search: {
        startDate: startDate.toISOString(),
        endDate: endDate.toISOString(),
        category: "",
      },
    };
  },
  filters: {
    date(val) {
      return val ? val.toLocaleDateString("en-GB") : "";
    },
  },
  computed: {
    headers() {
      return [
        {
          sortable: false,
          text: this.$t("invoice_no"),
          value: "invoice_no",
        },
        {
          sortable: false,
          text: this.$t("customer_name"),
          value: "customer_name",
        },
        {
          sortable: false,
          text: this.$t("payment_status"),
          value: "payment_status",
        },
        {
          sortable: false,
          text: this.$t("total"),
          value: "product_price",
        },
        {
          sortable: false,
          text: this.$t("total_paid"),
          value: "total_paid",
        },
        {
          sortable: false,
          text: this.$t("due"),
          value: "payment_due",
        },
        {
          sortable: false,
          text: this.$t("date"),
          value: "created_at",
        },
      ];
    },

    querySearch() {
      return Object.keys(this.search)
        .map((k) => `${k}=${this.search[k]}`)
        .join("&");
    },
  },
  async asyncData({ params, axios }) {},
  mounted() {
    this.collectionSearch();
  },
  methods: {
    async collectionSearch() {
      this.isLoading = true;
      await this.$axios
        .get(`/reports/collection?${this.querySearch}`)
        .then((res) => {
          this.isLoading = false;
          this.allCollection = res.data;
        });
    },
  },
  watch: {
    dateRange(val) {
      Object.entries(val).map((item) => {
        if (item[0] == "startDate") {
          this.search["startDate"] = item[1].toISOString();
        } else {
          this.search["endDate"] = item[1].toISOString();
        }
      });
    },
  },
};
</script>

<style>
</style>
