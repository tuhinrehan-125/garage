<template>
  <v-container grid-list-sm>
    <v-row justify="center">
      <edit-product :item="singleItem"  :items="productslist" @refresh="getProducts()"/>
      <v-dialog v-model="confirmation" max-width="300">
        <v-card>
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
<!--              <template v-slot:item.image="{ item }">-->
<!--                <img-->
<!--                  class="product-img"-->
<!--                  :src="item.image"-->
<!--                  style="width: 50px; height: 50px"-->
<!--                />-->
<!--              </template>-->

              <template v-slot:item.actions="{ item }">
                <v-btn
                  class="mx-2"
                  dark
                  small
                  color="cyan"
                  @click="editProduct(item)"
                >
                  <v-icon dark> mdi-pencil </v-icon>
                </v-btn>
                <v-btn
                  class="mx-2"
                  dark
                  small
                  color="red"
                  @click="deleteProduct(item)"
                >
                  <v-icon dark> mdi-delete </v-icon>
                </v-btn>
              </template>
            </v-data-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import editProduct from "~/components/product/editProduct";
export default {
  name: "Products",
  middleware: "auth",
  head: {
    title: "Product List",
  },
  components: { editProduct},
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
      prodid: "",
      // categories: [],
      productslist: [],
      // categories: [],
      subcategories: [],
      units: [],
      items: [],
    };
  },
  computed: {
    headers() {
      return [
        // {
        //   sortable: false,
        //   text: this.$t("image"),
        //   value: "image",
        // },
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
      set: function (newValue) {},
    },
    message() {
      return this.$store.getters.message;
    },
  },
  async asyncData({ params, axios }) {},
  created() {
    this.getProducts();
  },
  methods: {
    opendialog(type) {
      this.$store.commit("SET_MODAL", { type: type, status: true });
    },
    editProduct(item) {
      // this.$store.commit('changeDataState',item);
      this.$store.commit("SET_MODAL", { type: "edit", status: true });
      this.singleItem = item;
      // this.$router.push({ path: '/product/edit' ,params: {
      //     items: item
      //   }});
      // this.singleItem = item;
      // this.update = true;
      // this.dialog = true;
      // this.headline = this.$t("edit_product");
      // this.form.name = item.name;
      // this.form.details = item.details;
      // this.form.category_id = item.category_id;
      // this.form.subcategory_id = item.subcategory_id;
      // this.form.unit_id = item.unit_id;
      // this.form.weight = item.weight;
      // this.form.price = item.price;
      // this.prodid = item.id;
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
  // watch: {
  //   "form.category_id": function (val) {
  //     this.getChildOfCategory(val);
  //   },
  // },
};
</script>

<style  scoped>
.product-img {
  margin: 5px;
  border-radius: 5px;
}
</style>

