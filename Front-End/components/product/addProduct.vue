<template>
  <v-container fluid>
    <v-row justify="center">
<!--      <base-material-snackbar-->
<!--        v-model="alert"-->
<!--        type="success"-->
<!--        v-bind="{-->
<!--          [parsedDirection[0]]: true,-->
<!--          [parsedDirection[1]]: true,-->
<!--        }"-->
<!--      >-->
<!--        {{ message }}-->
<!--      </base-material-snackbar>-->

      <v-col cols="12" sm="12" md="12">
        <v-card>
          <v-card-title>
            {{ $t("Add Product") }}
          </v-card-title>
          <v-card-text>
            <v-form
              ref="form"
              method="post"
              v-model="valid"
              lazy-validation
              v-on:submit="submitForm"
            >
              <v-row no-gutters>
                <v-col cols="12" md="4" sm="12" xl="4">
                  <v-text-field
                    label="Product Name"
                    outlined
                    dense
                    required
                    :rules="[v => !!v || 'Name is required']"
                    v-model="form.name"
                  ></v-text-field>
                    <p  v-if="error" class="text-danger" style="color: red">{{error}}</p>
                </v-col>
                <v-col cols="12" md="4" sm="12" xl="4">
                  <v-select
                    label="Category"
                    :items="categories"
                    item-text="name"
                    item-value="id"
                    outlined
                    dense
                    required
                    :rules="[v => !!v || 'Category is required']"
                    v-model="form.category_id"
                  ></v-select>
                </v-col>
                <v-col cols="12" md="4" sm="12" xl="4">
                  <v-select
                    label="Brand"
                    :items="brands"
                    item-text="name"
                    item-value="id"
                    outlined
                    dense
                    required
                    v-model="form.brand_id"
                  ></v-select>


                </v-col>
                <v-col cols="12" md="4" sm="12" xl="4">
                  <v-text-field
                    label="Buying price"
                    outlined
                    dense
                    required
                    type="number"
                    :rules="[v => !!v || 'Buying price is required']"
                    v-model="form.buying_price"
                  ></v-text-field>
                </v-col>

                <v-col cols="12" md="4" sm="12" xl="4">
                  <v-text-field
                    label="Selling price"
                    outlined
                    dense
                    required
                    v-model="form.selling_price"
                    :rules="[v => !!v || 'Selling price is required']"
                  ></v-text-field>
                </v-col>

                <v-col cols="12" md="4" sm="12" xl="4">
                  <v-text-field
                    label="Quantity"
                    outlined
                    dense
                    required
                    type="number"
                    v-model="form.quantity"
                    :rules="[v => !!v || 'Quantity is required']"
                  ></v-text-field>
                </v-col>

                <v-col cols="12" md="4" sm="12" xl="4">
                  <v-select
                    label="Status"
                    :items="statuses"
                    item-text="name"
                    item-value="id"
                    outlined
                    dense
                    :rules="[v => !!v || 'Status is required']"
                    v-model="form.status"
                  ></v-select>
                </v-col>

                <v-col cols="12" md="4" sm="12" xl="4">
                  <v-file-input
                    label="Image"
                    type="file"
                    id="file"
                    v-model="form.image"
                  ></v-file-input>
                </v-col>


              </v-row>
              <v-row>
                <v-col cols="12" class="">
                  <v-btn
                    class="float-right"
                    dark
                    color="success"
                    @click="submitForm"
                    :loading="isLoading"
                  >
                    <v-icon dark> mdi-plus </v-icon>Submit
                  </v-btn>
                </v-col>
              </v-row>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import editProduct from "~/components/product/editProduct";
export default {
  name: "Add Product",
  middleware: "auth",
  head: {
    title: "Add Product",
  },
  components: {editProduct},
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
    error:null,
    form: {
      name: "",
      buying_price: "",
      selling_price: "",
      brand: "",
      quantity: "",
      category_id: "",
      image: "",
      status:"",
      brand_id:"",
    },
    direction: "top right",
    categories: [],
    brands: [],
  }),
  computed: {

    parsedDirection() {
      return this.direction.split(" ");
    },
  },
  async asyncData({params, axios}) {
  },
  mounted() {
    this.getCategories();
    this.getBrands();
  },
  methods: {
    reserve() {
      this.loading = true;
      setTimeout(() => (this.loading = false), 2000);
    },

    async getCategories()
    {
      await this.$axios.get("/getAllCategories").then((response) => {
          this.categories = response.data;
      });
    },
    async getBrands()
    {
      await this.$axios.get("/getAllBrands").then((response) => {
        this.brands = response.data;
      });
    },

    async submitForm() {
      this.error = null;
      if (this.$refs.form.validate()) {

        try {
          let formData = new FormData();
          for (var key in this.form) {
            formData.append(key, this.form[key]);
          }

          await this.$axios
            .post("/product", formData, {
              headers: {
                "Content-Type": "multipart/form-data"
              }
            })
            .then(response => {
              this.isLoading = false;
              let data = {alert: true, message: "Product Added Successfully", type: 'success'};
              this.$store.commit("SET_ALERT", data);
              this.$store.commit("SET_MODAL", true);
              this.$router.push('/product/list')
            });
        }

        catch (e) {
          if(e.response.status==422){
            this.error='The name must be unique'
          }
        }
      }
    },
    reset() {
      this.$refs.form.reset();
    },
    resetValidation() {
      this.$refs.form.resetValidation();
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
