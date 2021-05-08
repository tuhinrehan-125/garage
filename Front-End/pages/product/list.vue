<template>
  <v-container grid-list-sm>
    <v-row justify="center">
      <v-dialog v-model="confirmation" max-width="300">
        <v-card>
          <v-card-title>
            Are you sure?
            <v-spacer/>
            <v-icon aria-label="Close" @click="confirmation = false">
              mdi-close
            </v-icon>
          </v-card-title>
          <v-card-text class="pb-6 pt-12 text-center">
            <v-btn class="mr-3" text @click="confirmation = false"> No</v-btn>
            <v-btn color="success" text @click="confirmDelete()"> Yes</v-btn>
          </v-card-text>
        </v-card>
      </v-dialog>
    </v-row>
    <v-row  v-if="singleProductId ==''">
      <v-col cols="12" md="12">
        <v-card v-if="isLoading">
          <v-skeleton-loader class="mx-auto" type="table"></v-skeleton-loader>
        </v-card>
        <v-card class="mb-70" v-else>
          <v-card-title>
            {{ $t("product_list") }}
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
            <v-data-table
              :headers="headers"
              :items="productslist"
              :search="search"
            >
              <template v-slot:item.image="{ item }">
                <img
                  class="product-img"
                  :src="item.image"
                  style="width: 50px; height: 50px"
                />
              </template>

              <template v-slot:item.actions="{ item }">

                <v-btn
                  class="mx-2"
                  dark
                  small
                  color="cyan"
                  @click="editSingleProduct(item)"
                >
                  <v-icon dark> mdi-pencil</v-icon>
                </v-btn>
                <v-btn
                  class="mx-2"
                  dark
                  small
                  color="red"
                  @click="deleteProduct(item)"
                >
                  <v-icon dark> mdi-delete</v-icon>
                </v-btn>
              </template>
            </v-data-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <edit-single-product  @clicked="onClickChild" :productId="singleProductId" :productLists="productslist" @refresh="getProducts()" />
  </v-container>
</template>

<script>
import editSingleProduct from "~/components/product/editSingleProduct";

export default {
  name: "Products",
  middleware: "auth",
  head: {
    title: "Product List",
  },
  components: {editSingleProduct},
  data() {
    return {
      search: "",
      isLoading: false,
      update: false,
      dialog: false,
      confirmation: false,
      headline: this.$t("add_product"),
      valid: true,
      catRules: [(v) => !!v || this.$t("category_is_required")],
      nameRules: [(v) => !!v || this.$t("product_name_is_required")],
      direction: "top right",
      form: {
        category_id: "",
        subcategory_id: "",
        name: "",
        details: "",
        unit_id: "",
        weight: "",
        price: "",
        image: null,
      },
      singleItem: {},
      singleProductId:"",
      prodid: "",
      productslist: [],
      subcategories: [],
      units: [],
      items: [],
    };
  },
  computed: {

    headers() {
      return [
        {
          sortable: false,
          text: this.$t("image"),
          value: "image",
        },
        {
          sortable: false,
          text: this.$t("product_name"),
          value: "name",
        },
        {
          sortable: false,
          text: this.$t("Brand"),
          value: "brand",
        },
        {
          sortable: false,
          text: this.$t("category"),
          value: "category",
        },

        {
          sortable: false,
          text: this.$t("Buying Price"),
          value: "buying_price",
        },
        {
          sortable: false,
          text: this.$t("Selling Price"),
          value: "selling_price",
        },
        {
          sortable: false,
          text: this.$t("Quantity"),
          value: "quantity",
        },
        {
          sortable: false,
          text: this.$t("Status"),
          value: "status",
        },
        {
          sortable: false,
          text: this.$t("action"),
          value: "actions",
        },
      ];
    },
    parsedDirection() {
      return this.direction.split(" ");
    },
    alert: {
      get: function () {
        return this.$store.getters.alert;
      },
      set: function (newValue) {
      },
    },
    message() {
      return this.$store.getters.message;
    },
  },
  async asyncData({params, axios}) {
  },
  created() {
    this.getProducts();
  },



  methods: {
    opendialog(type) {
      this.$store.commit("SET_MODAL", {type: type, status: true});
    },

    editSingleProduct(val) {
      this.singleProductId = val;
    },


    onClickChild (value) {
      this.singleProductId = value;
      this.getProducts();
    },

    deleteProduct(item) {
      this.confirmation = true;
      this.prodid = item.id;
    },
    async confirmDelete() {
      await this.$axios.delete(`product/${this.prodid}`).then((res) => {
        this.alert = true;
        this.message = "Product Deleted Successfully";
        this.confirmation = false;
        this.getProducts();
      });
    },
    async getProducts() {
      this.isLoading = true;
      await this.$axios.get("/product").then((response) => {
        this.isLoading = false;
        this.productslist = response.data;
      });
    },
  },

};
</script>

<style scoped>
.product-img {
  margin: 5px;
  border-radius: 5px;
}
</style>
