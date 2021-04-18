<template>
  <v-container id="dashboard" fluid tag="section" grid-list-xl>
    <v-row>
      <v-col class="text-right">
        <v-btn-toggle v-model="reportfor" borderless>
          <v-btn value="daily"> Daily </v-btn>
          <v-btn value="weekly"> Weekly </v-btn>
          <v-btn value="monthly"> Monthly </v-btn>
          <v-btn value="yearly"> Yearly </v-btn>
        </v-btn-toggle>
      </v-col>
    </v-row>
    <h1 class="text-center">Welcome to Vehicle Management</h1>
    <!-- <v-layout row wrap>
      <v-flex xs6 sm4 md3 xl2 class="lg5-custom">
        <base-material-stats-card
          color="info"
          :text="$t('sell')"
          :title="$t('total_income')"
          :value="dashboardinfo.totalcollection"
        />
      </v-flex>

      <v-flex xs6 sm4 md3 xl2 class="lg5-custom">
        <base-material-stats-card
          color="success"
          :text="$t('profit')"
          :title="$t('total_profit')"
          value="$75.521"
        />
      </v-flex>

      <v-flex xs6 sm4 md3 xl2 class="lg5-custom">
        <base-material-stats-card
          color="error"
          :text="$t('spend')"
          :title="$t('total_spent')"
          value="$34,245"
        />
      </v-flex>

      <v-flex xs6 sm4 md3 xl2 class="lg5-custom">
        <base-material-stats-card
          color="orange"
          :text="$t('client')"
          :title="$t('total_client')"
          :value="dashboardinfo.totalclient"
        />
      </v-flex>
      <v-flex xs6 sm4 md3 xl2 class="lg5-custom">
        <base-material-stats-card
          color="secondary"
          :text="$t('customer')"
          :title="$t('total_customer')"
          :value="dashboardinfo.totalcustomer"
        />
      </v-flex>
    </v-layout>
    <v-row class="pb-15">
      <v-col cols="12" md="8">
        <base-material-chart-card
          :data="emailsSubscriptionChart.data"
          :options="emailsSubscriptionChart.options"
          hover-reveal
          :title="$t('leatest_transactions')"
          color="info"
          type="Line"
        >
        </base-material-chart-card>
      </v-col>

      <v-col cols="12" md="4">
        <base-material-card class="px-5 py-3">
          <template v-slot:heading>
            <div class="body-3 font-weight-thin">
              {{ $t("regular_customer") }}
            </div>
          </template>

          <v-list>
            <div class="d-flex justify-space-between" style="padding: 0px 16px">
              <p class="body-2 grey--text">{{ $t("customer_name") }}</p>
              <p class="body-2 grey--text">{{ $t("transaction_amount") }}</p>
            </div>
            <v-divider />
            <v-list-item-group>
              <v-list-item
                v-for="(item, i) in dashboardinfo.regular_customers"
                :key="i"
              >
                <v-list-item-avatar>
                  <img :src="item.customer_img" alt="" />
                </v-list-item-avatar>
                <v-list-item-content>
                  <v-list-item-title v-text="item.name"></v-list-item-title>
                </v-list-item-content>
                <v-list-item-action>
                  <v-list-item-action-text
                    style="font-size: 16px"
                    class="black--text"
                    v-text="item.total_amount"
                  ></v-list-item-action-text>
                </v-list-item-action>
              </v-list-item>
            </v-list-item-group>
          </v-list>
        </base-material-card>
      </v-col>
      <v-col cols="12" md="4">
        <base-material-card color="warning">
          <template v-slot:heading>
            <div class="body-3 font-weight-light">
              {{ $t("cash_payment") }}
            </div>
          </template>
          <v-card-text>
            <v-data-table
              :headers="paymentheaders"
              :items="dashboardinfo.latest_payment"
            />
          </v-card-text>
        </base-material-card>
      </v-col>
      <v-col cols="12" md="4">
        <base-material-card color="accent">
          <template v-slot:heading>
            <div class="body-3 font-weight-light">
              {{ $t("commissions") }}
            </div>
          </template>
          <v-card-text>
            <v-list>
              <v-list-item-group>
                <v-list-item>
                  <v-list-item-content>
                    <v-list-item-title>
                      {{ $t("arot_commission") }}</v-list-item-title
                    >
                    <p class="arot-commission">{{dashboardinfo.commissions.arot_commission}}%</p>
                  </v-list-item-content>

                </v-list-item>
              </v-list-item-group>
              <v-list-item-group>
                <v-list-item>
                  <v-list-item-content>
                    <v-list-item-title>
                      {{ $t("market_commission") }}</v-list-item-title
                    >
                    <p class="market-commission">{{dashboardinfo.commissions.bazar_commission}}%</p>
                  </v-list-item-content>
                </v-list-item>
              </v-list-item-group>
            </v-list>
          </v-card-text>
        </base-material-card>
      </v-col>
      <v-col cols="12" md="4">
        <base-material-card class="px-5 py-3">
          <template v-slot:heading>
            <div class="body-3 font-weight-thin">
              {{ $t("popular_fish") }}
            </div>
          </template>

          <v-list>
            <div class="d-flex justify-space-between" style="padding: 0px 16px">
              <p class="body-2 grey--text">{{ $t("fish_name") }}</p>
              <p class="body-2 grey--text">{{ $t("sell_percentage") }}</p>
            </div>
            <v-divider />
            <v-list-item-group>
              <v-list-item
                v-for="(item, i) in dashboardinfo.popular_fish"
                :key="i"
              >
                <v-list-item-avatar>
                  <img :src="item.image" alt="" />
                </v-list-item-avatar>
                <v-list-item-content>
                  <v-list-item-title v-text="item.name"></v-list-item-title>
                  <v-list-item-subtitle
                    v-text="item.total_amount"
                  ></v-list-item-subtitle>
                </v-list-item-content>
                <v-list-item-action style="display: inline !important">
                  <v-list-item-action-text
                    class="black--text"
                    v-text="getPercentage(item.total_amount)"
                    style="font-size: 16px"
                  ></v-list-item-action-text>
                  <v-icon class="green lighten-1" dark> mdi-arrow-up </v-icon>
                </v-list-item-action>
              </v-list-item>
            </v-list-item-group>
          </v-list>
        </base-material-card>
      </v-col>
    </v-row> -->
  </v-container>
</template>

<script>
export default {
  name: "AppDashboard",
  middleware: 'auth',
  head() {
    return {
      title: "Dashboard",
    };
  },

  data() {
    return {
      dashboardinfo: {},
      reportfor: "yearly",
      emailsSubscriptionChart: {
        data: {
          labels: [
            "Ja",
            "Fe",
            "Ma",
            "Ap",
            "Mai",
            "Ju",
            "Jul",
            "Au",
            "Se",
            "Oc",
            "No",
            "De",
          ],
          series: [
            [542, 443, 320, 780, 553, 453, 326, 434, 568, 610, 756, 895],
          ],
        },
        options: {
          axisX: {
            offset: 20,
            showGrid: false,
            position: "end",
          },
          axisY: {
            position: "start",
            scaleMinSpace: 20,
          },
          seriesBarDistance: 5,
          chartPadding: {
            top: 15,
            right: 15,
            bottom: 5,
            left: 10,
          },
          fullWidth: true,
        },
        responsiveOptions: [
          [
            "screen and (max-width: 640px)",
            {
              seriesBarDistance: 10,
              axisX: {
                labelInterpolationFnc: function (value) {
                  return value[0];
                },
              },
            },
          ],
        ],
      },

      paymentheaders: [
        {
          sortable: false,
          text: this.$t("payment_type"),
          value: "select_mode",
        },
        {
          sortable: false,
          text: this.$t("payment_via"),
          value: "bank_name",
        },
        {
          sortable: false,
          text: this.$t("amount"),
          value: "payment_amount",
          align: "right",
        },
        {
          sortable: false,
          text: this.$t("date"),
          value: "created_at",
          align: "right",
        },
      ],
    };
  },
  mounted() {
    //this.$store.commit("title/SET_TITLE", this.$t("dashboard"));
    //this.getDashboardData();
  },
  computed: {
    headline() {
      return this.$store.getters["title/pagetitle"];
    },
  },
  methods: {
    getPercentage(amount) {
      const sum = this.dashboardinfo.popular_fish
        .map((item) => item.total_amount)
        .reduce((prev, curr) => prev + curr, 0);
      return ((amount / sum) * 100).toFixed(2) + "%";
    },
    getDashboardData() {
      this.$axios.get("/dashboard-data").then((res) => {
        this.dashboardinfo = res.data.data;
      });
    },
    complete(index) {
      this.list[index] = !this.list[index];
    },
  },
  watch: {
    reportfor(val) {
      this.$axios.get("/dashboard-data?reportfor="+val).then((res) => {
        this.dashboardinfo = res.data.data;
      });
    },
  },
};
</script>
<style>
@media (min-width: 1264px) and (max-width: 2000px) {
  .flex.lg5-custom {
    width: 20%;
    max-width: 20%;
    flex-basis: 20%;
  }
}
.v-application .title {
  line-height: 1rem !important;
}
@media (min-width: 950px) {
  .dash-card-margin {
    margin-top: -100px;
  }
}
.arot-commission {
  font-size: 2.5rem !important;
  color: #303f9f;
}
.market-commission {
  font-size: 2.5rem !important;
  color: #388e3c;
}
.v-btn--active {
  background: #4caf50 !important;
}
</style>
