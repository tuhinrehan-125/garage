<template>
  <v-container>
    <v-row>
      <v-col>
        <h3 class="text-center font-weight-bold">Sales Report</h3>
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
                <v-btn elevation="2" medium color="indigo" @click="salesReport"
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
            {{ $t("sales_report") }}
          </v-card-title>
          <v-card-text>
            <v-skeleton-loader
              class="mx-auto"
              type="table"
              v-if="isLoading"
            ></v-skeleton-loader>
            <v-data-table v-else :headers="headers" :items="allsales">
              <template v-slot:item.quantity="{ item }">
                <p v-if="item.quantity_name == 'Thika'">
                  {{ item.quantity_name }}
                </p>
                <p v-else>{{ item.quantity }} {{ item.quantity_name }}</p>
              </template>
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
  name: "salesReport",
  head: {
    title: "Sales Report",
  },
  components: { DateRangePicker },
  data() {
    let startDate = new Date();
    startDate.setHours(0, 0, 0, 0);
    const endDate = new Date(startDate);
    endDate.setDate(endDate.getDate() + 1);
    return {
      isLoading: false,
      allsales: [],
      dateRange: { startDate, endDate },
      expenseCats: [],
      search: {
        startDate: startDate.toISOString(),
        endDate: endDate.toISOString(),
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
          text: this.$t("product"),
          value: "product",
        },
        {
          sortable: false,
          text: this.$t("customers_name"),
          value: "customer",
        },
        {
          sortable: false,
          text: this.$t("quantity"),
          value: "quantity",
        },
        {
          sortable: false,
          text: this.$t("status"),
          value: "payment_type",
        },
        {
          sortable: false,
          text: this.$t("amount"),
          value: "total_price",
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
    this.salesReport();
  },
  methods: {
    async salesReport() {
      this.isLoading = true;
      await this.$axios
        .get(`/reports/sales?${this.querySearch}`)
        .then((res) => {
          this.isLoading = false;
          this.allsales = res.data;
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
