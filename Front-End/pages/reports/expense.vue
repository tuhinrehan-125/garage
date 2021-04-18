<template>
  <v-container>
    <v-row>
      <v-col>
        <h3 class="text-center">{{ $t("expense_report") }}</h3>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12" sm="12" md="10">
        <v-card>
          <v-card-title>{{ $t("filter") }}</v-card-title>
          <v-card-text>
            <v-row>
              <v-col cols="12" sm="2" md="2">
                <p>Date Range:</p>
              </v-col>
              <v-col cols="12" sm="3" md="3">
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
              <v-col cols="12" sm="2" md="2">
                <p>{{ $t("expense_category") }} :</p>
              </v-col>
              <v-col cols="8" sm="3" md="3">
                <v-select
                  :items="expenseCats"
                  label="Expense Category"
                  dense
                  outlined
                  required
                  item-text="name"
                  item-value="id"
                  v-model="search.category"
                ></v-select>
              </v-col>
              <v-col cols="12" sm="2" md="2">
                <v-btn
                  elevation="2"
                  medium
                  color="indigo"
                  @click="expenseSearch"
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
          <v-card-title>{{ $t("expense_report") }}</v-card-title>
          <v-card-text>
            <v-skeleton-loader
              class="mx-auto"
              type="table"
              v-if="isLoading"
            ></v-skeleton-loader>
            <v-data-table v-else :headers="headers" :items="allexpenses">
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
  name: "expenseReport",
  head: {
    title: "Expense Report",
  },
  components: { DateRangePicker },
  data() {
    let startDate = new Date();
    let endDate = new Date();
    startDate.setDate(endDate.getDate() - 30);
    return {
      isLoading: false,
      allexpenses: [],
      dateRange: { startDate, endDate },
      expenseCats: [],
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
          text: this.$t("expense_for"),
          value: "expense_for",
        },
        {
          sortable: false,
          text: this.$t("ref_no"),
          value: "ref_no",
        },
        {
          sortable: false,
          text: this.$t("expense_date"),
          value: "exp_date",
        },
        {
          sortable: false,
          text: this.$t("expense_category"),
          value: "exp_cat_name",
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
    this.expenseSearch();
    this.getExpCategories();
  },
  methods: {
    async getExpCategories() {
      await this.$axios.get("/expense-category").then((response) => {
        this.expenseCats = response.data;
      });
    },
    async expenseSearch() {
      this.isLoading = true;
      await this.$axios
        .get(`/reports/expense?${this.querySearch}`)
        .then((res) => {
          this.isLoading = false;
          this.allexpenses = res.data;
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
