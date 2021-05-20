<template>
  <v-container fluid style="min-height:700px" v-if="invoiceId !==''">
    <v-row>

      <v-col>
        <v-btn tile color="indigo" class="float-right" @click="goBack">
          {{ $t("Back") }}
        </v-btn>

<!--        <v-btn tile color="indigo" class="float-right ml-3" @click="printIt">-->
<!--          {{ $t("Print") }}-->
<!--        </v-btn>-->
      </v-col>
    </v-row>



    <v-row justify="center" class="">
      <v-col cols="12" sm="12" md="12">
        <v-card height="100%">
          <v-card-title class="justify-center">
            {{ $t("Invoice Details") }}
          </v-card-title>

          <v-card-text>

<!--              &lt;!&ndash; SOURCE &ndash;&gt;-->
<!--              <div id="printMe">-->
<!--                <h1>Print me!</h1>-->

<!--              &lt;!&ndash; OUTPUT &ndash;&gt;-->
<!--&lt;!&ndash;              <button class="btn-block" @click="print"></button>&ndash;&gt;-->

<!--              <v-btn   tile color="indigo" @click="print">Button</v-btn>-->
<!--              </div>-->

            <v-row>
              <v-col cols="12" sm="12" md="4">
                <h3>Billing To</h3>

                <p>Name: {{ this.invoiceId.client_name }}</p>
                <p>Phone: {{ this.invoiceId.client_phone }}</p>
              </v-col>

              <v-col cols="12" sm="12" md="4">
                <h3>Vehicle Details</h3>
                <p>Model: {{ this.invoiceItems[0].vehicle_name }}</p>
                <p>Registration No: {{ this.invoiceItems[0].reg_no }}</p>
                <p>Chassis No: {{ this.invoiceItems[0].chassis_no }}</p>
              </v-col>

              <v-col cols="12" sm="12" md="4">
                <h3>Invoice Information</h3>
                <p>Invoice Number: {{ this.invoiceId.invoice_number }}</p>
                <p>Date of Invoice: {{ this.invoiceId.date }}</p>
                <p>Discount: {{ this.invoiceId.discount }}</p>
                <p>Vat: {{ this.invoiceId.vat }}</p>
                <p>Total Amount: {{ this.invoiceId.total_amount }}</p>
                <p>Paid Amount: {{ this.invoiceId.paid_amount }}</p>
                <p>Due Amount: {{ this.invoiceId.due_amount }}</p>
              </v-col>
            </v-row>
            <v-row class="pb-4">
              <v-col cols="12" sm="12" md="12">
                <h3 class="text-center">Order Summary</h3>

                <v-data-table
                  :headers="headers"
                  :items="invoiceItems"
                  hide-default-footer
                  class="elevation-1 pb-4"
                ></v-data-table>

              </v-col>
            </v-row>


<!--            <v-row class="pt-4">-->
<!--              <v-col cols="12" sm="12" md="6">-->
<!--                <h4 class="text-center">Customer's Signature</h4>-->
<!--              </v-col>-->
<!--              <v-col cols="12" sm="12" md="6">-->
<!--                <h4 class="text-center">Cashier's Signature</h4>-->
<!--              </v-col>-->
<!--            </v-row>-->



          </v-card-text>
        </v-card>
      </v-col>
    </v-row>


  </v-container>
</template>
<script>


export default {
  name: "Invoice Detail",
  middleware: "auth",
  head: {
    title: "Invoice Detail",
  },

  props: ["invoiceId"],
  data: () => ({
    isLoading: false,
    valid: true,
    selection: 1,
    reveal: false,
    alert: false,
    dialog: false,
    confirmation: false,
    message: "",
    statuses: ["Active", "Inactive"],
    error: null,
    form: {
      id: ''
    },
    direction: "top right",
    categories: [],
    brands: [],
    invoiceItemId: '',
    invoiceItems: [],

  }),
  computed: {
    headers() {
      return [
        {
          sortable: false,
          text: this.$t("Item Name"),
          value: "item_name"
        },
        {
          sortable: false,
          text: this.$t("Item Quantity"),
          value: "item_quantity"
        },
        {
          sortable: false,
          text: this.$t("Item Price"),
          value: "item_price"
        },
        {
          sortable: false,
          text: this.$t("Total"),
          value: "total_price"
        },

      ];
    },
    parsedDirection() {
      return this.direction.split(" ");
    },
  },

  async asyncData({params, axios}) {
  },

  created() {
    this.getInvoiceInfos();
  },
  methods: {
    reserve() {
      this.loading = true;
      setTimeout(() => (this.loading = false), 2000);
    },

    async goBack()
    {
      this.invoiceId = '';
      this.$emit('clicked', this.invoiceId)
    },


    async print () {
      // Pass the element id here
      await this.$htmlToPaper('printMe');
    },
    async getInvoiceInfos() {

      this.form.id = this.invoiceId.id;
      await this.$axios.post("/get-invoice-details", this.form).then(response => {
        this.invoiceItems = response.data;
      });

    },

    async getCategories() {
      await this.$axios.get("/get-categories").then(response => {
        this.categories = response.data;
      });
    },
    async getBrands() {
      await this.$axios.get("/getAllBrands").then((response) => {
        this.brands = response.data;
      });
    },


  },
};
</script>

<style scoped>
::v-deep .v-application--is-ltr .v-text-field__suffix {
  background-color: rgb(12, 113, 138);
  cursor: pointer;
  width: 115px;
  height: 30px;
  padding: 5px;
  color: white;
  border-radius: 5px;
  padding-left: 8px;
}

::v-deep .v-card--reveal {
  bottom: -15px;
  opacity: 1 !important;
  position: absolute;
  width: 100%;
}

::v-deep .v-card > .v-card__progress + :not(.v-btn):not(.v-chip),
.v-card > :first-child:not(.v-btn):not(.v-chip) {
  margin-top: -6px;
  width: 100%;
}

::v-deep .v-input input:active,
.v-input input,
.v-input textarea:active,
.v-input textarea {
  width: 500px;
}

::v-deep
.v-sheet
button.v-btn.v-size--default:not(.v-btn--icon):not(.v-btn--fab) {
  margin-left: 20px;
}
</style>
