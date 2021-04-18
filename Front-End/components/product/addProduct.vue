<template>
  <div>
    <v-stepper v-model="curr" color="green" class="mb-70">
      <v-stepper-header class="overflow-x-auto">
        <v-stepper-step
          editable
          v-for="(step, n) in steps"
          :key="n"
          :complete="stepComplete(n + 1)"
          :step="n + 1"
          :rules="[value => !!step.valid]"
          :color="stepStatus(n + 1)"
        >
          {{ step.name }}
        </v-stepper-step>
      </v-stepper-header>
      <v-stepper-content v-for="(step, n) in steps" :step="n + 1" :key="n">
        <v-card>
          <v-card-text>
            <div v-if="step.name == 'Product Info'">
              <v-form :ref="'stepForm'" v-model="step.valid" lazy-validation>
                <v-layout row>
                  <v-flex xl2>
                    <v-subheader>Item Name</v-subheader>
                  </v-flex>
                  <v-flex xl4>
                    <v-text-field
                      outlined
                      dense
                      :rules="step.rules"
                      v-model="form.name"
                    ></v-text-field>
                  </v-flex>
                </v-layout>
                <v-layout row>
                  <v-flex xl2>
                    <v-subheader>SKU</v-subheader>
                  </v-flex>
                  <v-flex xl4>
                    <v-text-field
                    placeholder="optional"
                      outlined
                      dense
                      v-model="form.sku"
                    ></v-text-field>
                  </v-flex>
                </v-layout>
                <v-layout row>
                  <v-flex xl2>
                    <v-subheader>Category</v-subheader>
                  </v-flex>
                  <v-flex xl4>
                    <v-select
                      :rules="step.rules"
                      v-model="form.category_id"
                      :items="categories"
                      item-text="name"
                      item-value="id"
                      dense
                      outlined
                    ></v-select>
                  </v-flex>
                </v-layout>
                <v-layout row v-if="subcats.length > 0">
                  <v-flex xl2>
                    <v-subheader>Sub Category</v-subheader>
                  </v-flex>
                  <v-flex xl4>
                    <v-select
                      :rules="step.rules"
                      v-model="form.subcategory_id"
                      :items="subcats"
                      item-text="name"
                      item-value="id"
                      dense
                      outlined
                    ></v-select>
                  </v-flex>
                </v-layout>
                <v-layout row>
                  <v-flex xl2>
                    <v-subheader>Brand </v-subheader>
                  </v-flex>
                  <v-flex xl4>
                    <v-select
                      v-model="form.brand_id"
                      :items="brands"
                      item-text="name"
                      item-value="id"
                      dense
                      outlined
                    ></v-select>
                  </v-flex>
                </v-layout>
                <v-layout row>
                  <v-flex xl2>
                    <v-subheader>Unit </v-subheader>
                  </v-flex>
                  <v-flex xl4>
                    <v-select
                      :rules="step.rules"
                      v-model="form.unit_id"
                      :items="units"
                      item-text="name"
                      item-value="id"
                      dense
                      outlined
                    ></v-select>
                  </v-flex>
                </v-layout>
                <v-layout row>
                  <v-flex xl2>
                    <v-subheader>Barcode type</v-subheader>
                  </v-flex>
                  <v-flex xl4>
                    <v-select
                      v-model="form.barcode_type"
                      :items="btypes"
                      dense
                      outlined
                      :rules="step.rules"
                    ></v-select>
                  </v-flex>
                </v-layout>
                <v-layout row>
                  <v-flex xl2>
                    <v-subheader>Alert quantity </v-subheader>
                  </v-flex>
                  <v-flex xl4>
                    <v-text-field
                      outlined
                      dense
                      :rules="step.rules"
                      v-model="form.alert_quantity"
                    ></v-text-field>
                  </v-flex>
                </v-layout>
              </v-form>
            </div>

            <template v-if="step.name == 'Pricing'">
              <v-form :ref="'stepForm'" v-model="step.valid" lazy-validation>
                <v-layout row>
                  <v-flex xl2>
                    <v-subheader>Purchase Price</v-subheader>
                  </v-flex>
                  <v-flex xl4>
                    <v-text-field
                      v-model="form.purchase_price"
                      outlined
                      dense
                    ></v-text-field>
                  </v-flex>
                </v-layout>
                <v-layout row>
                  <v-flex xl2>
                    <v-subheader>Selling Price</v-subheader>
                  </v-flex>
                  <v-flex xl4>
                    <v-text-field
                      v-model="form.sell_price"
                      outlined
                      dense
                    ></v-text-field>
                  </v-flex>
                </v-layout>
                <v-layout row>
                  <v-flex xl2>
                    <v-subheader>Tax Method</v-subheader>
                  </v-flex>
                  <v-flex xl4>
                    <v-select
                      v-model="form.tax_method"
                      :items="tax_methods"
                      item-text="name"
                      item-value="id"
                      dense
                      outlined
                    ></v-select>
                  </v-flex>
                </v-layout>
                <v-layout row>
                  <v-flex xl2>
                    <v-subheader>Tax (In percentage)</v-subheader>
                  </v-flex>
                  <v-flex xl4>
                    <v-text-field
                      v-model="form.tax"
                      outlined
                      dense
                    ></v-text-field>
                  </v-flex>
                </v-layout>
              </v-form>
            </template>
            <template v-if="step.name == 'Variations'">
              <v-form :ref="'stepForm'" v-model="step.valid" lazy-validation>
                <v-layout row>
                  <v-flex xl2>
                    <v-subheader>Item Type: </v-subheader>
                  </v-flex>
                  <v-flex xl10>
                    <v-radio-group v-model="form.type" row>
                      <v-radio label="Single" value="single"></v-radio>
                      <v-radio label="Variation" value="variation"></v-radio>
                    </v-radio-group>
                  </v-flex>
                </v-layout>
                <div class="variation-form" v-if="form.type == 'variation'">
                  <h2 class="overline variation-title mt-5 mb-5">
                    Add variation
                  </h2>
                  <v-layout
                    row
                    v-for="(variation, index) in form.product_variation"
                    :key="index"
                    no-gutters
                  >
                    <v-col cols="12" sm="6" md="3">
                      <v-text-field
                        outlined
                        dense
                        label="name"
                        required
                        v-model="variation.name"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                      <v-text-field
                        outlined
                        dense
                        label="Purchase price"
                        required
                        v-model="variation.purchase_price"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                      <v-text-field
                        outlined
                        dense
                        label="Tax"
                        required
                        v-model="variation.sell_price"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                      <v-text-field
                        outlined
                        dense
                        label="Sell price"
                        required
                        v-model="variation.tax"
                      ></v-text-field>
                      <v-btn
                        flat
                        icon
                        dark
                        class="variation-remove"
                        color="red"
                        @click="removeVarition(index)"
                        ><v-icon small> mdi-trash-can </v-icon></v-btn
                      >
                    </v-col>
                  </v-layout>
                </div>
                <v-row v-if="form.type == 'variation'">
                  <v-col cols="12" md="12">
                    <v-btn dark small class="float-right" @click="addVariation"
                      ><v-icon dark> mdi-plus </v-icon> Add</v-btn
                    >
                  </v-col>
                </v-row>
              </v-form>
            </template>
            <template v-if="step.name == 'Images'">
              <v-form :ref="'stepForm'" v-model="step.valid" lazy-validation>
                <v-layout row>
                  <v-flex xl2>
                    <v-subheader>Select Image</v-subheader>
                  </v-flex>
                  <v-flex xl4>
                    <v-file-input
                      accept="image/*"
                      v-model="form.image"
                    ></v-file-input>
                  </v-flex>
                </v-layout>
              </v-form>
            </template>
            <template v-if="step.name == 'Opening Stock'">
              <v-form :ref="'stepForm'" v-model="step.valid" lazy-validation>
                <v-layout row>
                  <v-flex xl2>
                    <v-subheader>Opening Stock: </v-subheader>
                  </v-flex>
                  <v-flex xl10>
                    <v-radio-group v-model="form.add_opening_stock" row>
                      <v-radio
                        label="Doesn't have opening stock"
                        value="0"
                      ></v-radio>
                      <v-radio label="Add opening stock" value="1"></v-radio>
                    </v-radio-group>
                  </v-flex>
                </v-layout>
                <div v-if="form.add_opening_stock == '1'">
                  <v-card
                    class="mb-5"
                    v-for="(location, index) in form.opening_stocks"
                    :key="index"
                  >
                  <v-card-title>{{ location.name }}</v-card-title>
                  <v-card-subtitle>{{ location.address }}</v-card-subtitle>
                    <v-card-text>
                      <v-layout row>
                        <v-flex xl2>
                          <v-subheader>Quantity</v-subheader>
                        </v-flex>
                        <v-flex xl2>
                          <v-text-field
                            v-model="location.quantity"
                            outlined
                            dense
                          ></v-text-field>
                        </v-flex>
                      </v-layout>
                    </v-card-text>
                  </v-card>
                </div>
              </v-form>
            </template>
            <v-layout row>
              <v-flex xl2 mt-5>
                <v-btn
                  v-if="n + 1 < lastStep"
                  color="primary"
                  @click="validate(n)"
                  :disabled="!step.valid"
                  >Continue</v-btn
                >
                <v-btn v-else color="success" @click="done()" :loading="isLoading">Submit</v-btn>
                <v-btn text v-if="curr > 1" @click="curr = n">Back</v-btn>
              </v-flex>
            </v-layout>
          </v-card-text>
        </v-card>
      </v-stepper-content>
    </v-stepper>
  </div>
</template>

<script>
export default {
  data() {
    return {
      isLoading:false,
      curr: 1,
      lastStep: 5,
      steps: [
        {
          name: "Product Info",
          rules: [v => !!v || "Required."],
          valid: true
        },

        {
          name: "Pricing",
          rules: [v => (v && v.length >= 4) || "Enter at least 4 characters."],
          valid: true
        },
        { name: "Variations", rules: [v => !!v || "Required."], valid: true },

        {
          name: "Images",
          rules: [v => (v && v.length >= 4) || "Enter at least 4 characters."],
          valid: true
        },
        {
          name: "Opening Stock",
          rules: [v => (v && v.length >= 4) || "Enter at least 4 characters."],
          valid: true
        }
      ],
      tax_methods: ["Inclusive", "Exclusive"],
      valid: false,
      stepForm: [],
      checkbox: false,
      btypes: ["C39", "C128", "EAN13", "EAN8", "UPCA", "UPCE"],
      form: {
        name: "",
        type: "single",
        unit_id: "",
        brand_id: "",
        category_id: "",
        subcategory_id: "",
        barcode_type: "",
        enable_stock: false,
        alert_quantity: "",
        sku: "",
        product_description: "",
        weight: "",
        purchase_price: "",
        sell_price: "",
        tax_type: "",
        image: "",
        tax: "",
        add_opening_stock: "0",
        product_variation: [
          {
            name: "",
            purchase_price: "",
            sell_price: "",
            tax: ""
          }
        ],
        opening_stocks: [],
      },
      prodid: "",
      brands: [],
      categories: [],
      productslist: [],
      categories: [],
      subcategories: [],
      units: [],
      subunits: [],
      parents: [],
      variations: [],
      allCats: [],
      haveSubcat: false,
      subcats: [],
      items: [
        {
          name: "",
          quantity: 0,
          amount: 0,
          total: 0
        }
      ]
    };
  },
  computed: {
    user_business_location() {
      return this.$auth.user.data.user_business_location;
    }
  },
  async asyncData({ params, axios }) {},
  mounted() {
    this.getBrands();
    this.getCategories();
    this.getUnit();
    for (let index = 0; index < this.user_business_location.length; index++) {
      this.form.opening_stocks.push({
        location_id: this.user_business_location[index].id,
        quantity: "",
        name: this.user_business_location[index].name,
        address:
          this.user_business_location[index].state +
          "," +
          this.user_business_location[index].city +
          "," +
          this.user_business_location[index].country +
          "-" +
          this.user_business_location[index].zip_code
      });
    }

    // this.getVariations();
    // this.getProducts();
  },
  watch: {
    "form.category_id": function(val) {
      this.getSubCategories(val);
    }
  },
  methods: {
    addVariation() {
      this.form.product_variation.push({
        name: "",
        purchase_price: "",
        sell_price: "",
        tax: ""
      });
    },
    removeVarition(index) {
      this.form.product_variation.splice(index, 1);
    },
    imageUpload() {
      this.form.image = this.$refs.file.files[0];
    },
    stepComplete(step) {
      return this.curr > step;
    },
    stepStatus(step) {
      return this.curr > step ? "green" : "blue";
    },
    validate(n) {
      this.steps[n].valid = false;
      let v = this.$refs.stepForm[n].validate();
      if (v) {
        this.steps[n].valid = true;
        // continue to next
        this.curr = n + 2;
      }
    },
    async done() {
      this.isLoading=true
      let formData = new FormData();
      for (var key in this.form) {
        formData.append(key, this.form[key]);
      }
     formData.append('opening_stocks',JSON.stringify(this.form.opening_stocks));
      await this.$axios.post("product", formData, {
        headers: {
          "Content-Type": "multipart/form-data"
        }
      }).then(response => {
        this.isLoading=false
        let data = { alert: true,message: "Product Addedd Successfully",type:'success' };
        this.$store.commit("SET_ALERT", data);
        this.$store.commit("SET_MODAL", true);
        this.$router.push({name:'product-list'})
      });
    },
    async getBrands() {
      await this.$axios.get("/brand").then(response => {
        this.brands = response.data;
      });
    },
    async getUnit() {
      await this.$axios.get("/unit").then(response => {
        this.units = response.data;
      });
    },
    async getCategories() {
      await this.$axios.get("/category").then(response => {
        this.allCats = response.data;
        this.categories = response.data.filter(function(data) {
          return data.parent_id == 0;
        });
      });
    },
    getSubCategories(val) {
      this.subcats = [];
      this.allCats.map(data => {
        if (val == data.parent_id) {
          this.subcats.push(data);
        }
      });
    },
    async getVariations() {
      await this.$axios.get("/product-variation").then(response => {
        this.variations = response.data;
      });
    },
    async getChildOfUnit(val) {
      await this.$axios.get(`get-subunits/${val}`).then(response => {
        this.subunits = response.data;
      });
    }
  }
};
</script>

<style scoped>
.variation-title {
  text-align: center;
  font-size: 15px !important;
  font-weight: 600;
}
.variation-remove {
  float: right;
  margin-top: -25px;
}
</style>
