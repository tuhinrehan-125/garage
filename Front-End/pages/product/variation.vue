<template>
  <v-container fluid grid-list-xl class="mt-5">
    <v-row justify="center">
      <base-material-snackbar
        v-model="alert"
        type="success"
        v-bind="{
          [parsedDirection[0]]: true,
          [parsedDirection[1]]: true,
        }"
      >
        {{ message }}
      </base-material-snackbar>
      <v-dialog v-model="dialog" persistent max-width="600px">
        <v-card>
          <v-card-title>
            {{ headline }}
            <v-spacer></v-spacer>
            <v-btn text @click="dialog = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
          </v-card-title>

          <v-card-text>
            <v-container>
              <v-form ref="form" v-model="valid" lazy-validation>
                <v-row>
                  <v-col cols="12" sm="6" md="12">
                    <v-text-field
                      outlined
                      dense
                      :label="$t('variation_name')"
                      required
                      :rules="nameRules"
                      v-model="form.name"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="6" md="12">
                    <v-container fluid>
                      <v-layout wrap>
                        <v-flex xs12>
                          <v-combobox
                            multiple
                            v-model="form.values"
                            label="Tags"
                            append-icon
                            chips
                            deletable-chips
                            :search-input.sync="search"
                            @keyup.tab="updateTags"
                            @paste="updateTags"
                            placeholder="Press enter to add"
                          >
                          </v-combobox>
                        </v-flex>
                      </v-layout>
                    </v-container>
                  </v-col>
                </v-row>
              </v-form>
            </v-container>
            <small>{{ $t("indicates_required_field") }}</small>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="blue darken-1" text @click="dialog = false">
              {{ $t("close") }}
            </v-btn>
            <v-btn color="blue darken-1" text @click="submitForm">
              {{ $t("save") }}
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
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
      <v-col>
        <v-btn tile color="indigo" class="float-right" @click="dialog = true">
          <v-icon left> mdi-plus </v-icon>
          {{ $t("add_variation") }}
        </v-btn>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12" md="12">
        <v-card v-if="isLoading">
          <v-skeleton-loader class="mx-auto" type="table"></v-skeleton-loader>
        </v-card>
        <v-card v-else>
          <v-card-title>
            {{ $t("variation_list") }}
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
            <v-data-table :headers="headers" :items="items">
              <template v-slot:item.actions="{ item }">
                <v-btn
                  class="mx-2"
                  dark
                  small
                  color="cyan"
                  @click="editVariation(item)"
                >
                  <v-icon dark> mdi-pencil </v-icon>
                </v-btn>
                <v-btn
                  class="mx-2"
                  dark
                  small
                  color="red"
                  @click="deleteVariation(item)"
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
export default {
  name: "AddVariation",
  middleware: "auth",
  head: {
    title: "Add Variation",
  },
  components: {},
  data() {
    return {
      search: "",
      isLoading: false,
      confirmation: false,
      update: false,
      headline: this.$t("add_variation"),
      alert: false,
      valid: true,
      message: "",
      nameRules: [(v) => !!v || this.$t("variation_name_is_required")],
      dialog: false,
      form: {
        name: "",
        values: [],
      },
      varid: "",
      direction: "top right",
      categories: [],
      items: [],
    };
  },
  computed: {
    headers() {
      return [
        {
          sortable: false,
          text: this.$t("name"),
          value: "name",
        },
        {
          sortable: false,
          text: this.$t("value"),
          value: "value",
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
  },
  async asyncData({ params, axios }) {},
  mounted() {
    this.getVariations();
  },
  methods: {
    async getVariations() {
      this.isLoading = true;
      await this.$axios.get("/product-variation").then((response) => {
        this.items = response.data;
        this.isLoading = false;
      });
    },
    deleteVariation(item) {
      this.confirmation = true;
      this.varid = item.id;
    },
    async confirmDelete() {
      await this.$axios
        .delete(`product-variation/${this.varid}`)
        .then((res) => {
          this.alert = true;
          this.message = "Product Variation Deleted Successfully";
          this.confirmation = false;
          this.getVariations();
        });
    },
    editVariation(item) {
      this.update = true;
      this.dialog = true;
      this.headline = this.$t("variation_edit");
      this.form.name = item.name;
      this.form.values = item.values;
      this.varid = item.id;
    },
    async submitForm() {
      if (this.$refs.form.validate()) {
        if (this.update == false) {
          await this.$axios
            .post("/product-variation", this.form)
            .then((res) => {
              this.message = "Product Variation Addedd Successfully";
              this.dialog = false;
              this.alert = true;
              this.getVariations();
            });
        } else {
          await this.$axios
            .patch(`product-variation/${this.varid}`, this.form)
            .then((res) => {
              this.message = "Product Variation Updated successfully";
              this.dialog = false;
              this.alert = true;
              this.getVariations();
            });
        }
      }
    },
  },
};
</script>

<style>
.theme--light.v-chip:not(.v-chip--active) {
  width: auto;
}
</style>
