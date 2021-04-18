<template>
  <v-container>
    <v-row>
      <v-col>
        <h3 class="text-center">{{ $t("payment_report") }}</h3>
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
                  @click="paymentSearch"
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
            {{ $t("payment_report") }}
          </v-card-title>

          <v-card-text>
            <v-skeleton-loader
              class="mx-auto"
              type="table"
              v-if="isLoading"
            ></v-skeleton-loader>
            <v-data-table v-else :headers="headers" :items="allPayments">
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
  name: "paymentReport",
  head: {
    title: "Payment Report",
  },
  components: { DateRangePicker },
  data() {
    let startDate = new Date();
    let endDate = new Date();
    startDate.setDate(endDate.getDate() - 30);
    return {
      isLoading: false,
      allPayments: [],
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
          text: this.$t("client_name"),
          value: "client_name",
        },
        {
          sortable: false,
          text: this.$t("payment_status"),
          value: "payment_status",
        },
        {
          sortable: false,
          text: this.$t("total"),
          value: "total",
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
  mounted() {
    this.paymentSearch();
  },
  methods: {
    async paymentSearch() {
      this.isLoading = true;
      await this.$axios
        .get(`/reports/payment?${this.querySearch}`)
        .then((res) => {
          this.isLoading = false;
          this.allPayments = res.data;
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
