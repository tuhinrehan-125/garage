<template>
  <v-dialog v-model="dialog" persistent max-width="1000px">
    <v-card>
      <v-card-title>
        {{ $t("edit_product") }}<v-spacer />
        <v-icon aria-label="Close" @click="closedialog"> mdi-close </v-icon>
      </v-card-title>
      <v-card-text>
        <v-container>
          <v-form ref="form" v-model="valid" lazy-validation>
            <v-row no-gutters>
              <v-col cols="12" md="4" sm="12" xl="4">
                <v-text-field
                  label="Product Name"
                  outlined
                  dense

                  v-model="form.name"
                ></v-text-field>
                <p  v-if="error" class="text-danger" style="color: red">{{error}}</p>
              </v-col>

              <v-col cols="12" md="4" sm="12" xl="4">
                <v-text-field
                  label="Buying price"
                  outlined
                  dense
                  type="number"
                  v-model="form.buying_price"
                ></v-text-field>
              </v-col>

              <v-col cols="12" md="4" sm="12" xl="4">
                <v-text-field
                  label="Selling price"
                  outlined
                  dense
                  v-model="form.selling_price"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="4" sm="12" xl="4">
                <v-text-field
                  label="Brand"
                  outlined
                  dense
                  v-model="form.brand"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="4" sm="12" xl="4">
                <v-text-field
                  label="Quantity"
                  outlined
                  dense
                  type="number"
                  v-model="form.quantity"

                ></v-text-field>
              </v-col>

              <v-col cols="12" md="4" sm="12" xl="4">
                <v-file-input
                  label="Image"
                  type="file"
                  id="file"
                  v-model="form.image"
                ></v-file-input>
              </v-col>
              <v-col cols="12" md="4" sm="12" xl="4">
                <v-select
                  label="Category"
                  v-model="form.category_id"
                  :items="categories"
                  item-text="name"
                  item-value="id"
                  outlined
                  dense

                ></v-select>
              </v-col>
              <v-col cols="12" md="4" sm="12" xl="4">
                <v-select
                  label="Status"
                  v-model="form.status"
                  :items="statuses"
                  item-text="name"
                  item-value="id"
                  outlined
                  dense

                ></v-select>
              </v-col>
            </v-row>
          </v-form>
        </v-container>
        <small>{{ $t("indicates_required_field") }}</small>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="blue darken-1" text @click="closedialog">
          {{ $t("close") }}
        </v-btn>
        <v-btn color="blue darken-1" text @click="submitForm">
          {{ $t("save") }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  props: ["items", "item"],
  components: {},
  data() {
    return {
      valid: true,
      error:null,
      form: {},
      categories: [],
      statuses: ["active", "inactive"],
    };
  },
  computed: {
    dialog() {
      return this.$store.getters.modaltype == "edit"
        ? this.$store.getters.modal
        : false;
    },
    modaltype() {
      return this.$store.getters.modaltype;
    },
  },
  async asyncData({ params, axios }) {},
  mounted() {
    this.getCategories();
  },
  methods: {
    closedialog() {
      this.$store.commit("SET_MODAL", { type: "", status: false });
    },
    async submitForm() {
      if (this.$refs.form.validate()) {
        try {
          // let formData = new FormData();
          // for (var key in this.form) {
          //   formData.append(key, this.form[key]);
          // }
          //
          // console.log(this.form.id);
          // await this.$axios.patch(`product/${this.form.id}`, this.form, {
          //   headers: {
          //     "Content-Type": "multipart/form-data"
          //   }
          // })
          await this.$axios.patch(`product/${this.form.id}`, this.form)
            .then((res) => {
              this.$refs.form.reset();
              let data = {
                alert: true,
                message: "Product Updated Successfully",
              };
              this.$store.commit("SET_ALERT", data);
              this.$store.commit("SET_MODAL", false);
              this.$emit("refresh");
            });
        }
        catch (e) {
          if(e.response.status==422){
            this.error='The name must be unique'
          }
        }
      }
    },
    async getCategories()
    {
      await this.$axios.get("/getAllCategories").then((response) => {
        this.categories = response.data;
      });
    },
  },
  watch: {
    item(val) {
      this.form = val;
    },
  },
};
</script>

<style>
</style>
