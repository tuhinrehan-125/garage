<template>
  <v-container grid-list-sm class="mt-5">
    <v-row justify="center">
      <add-category :items="items" @refresh="getCategories()" />
      <edit-category
        :item="singleItem"
        :items="items"
        @refresh="getCategories()"
      />
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
        <v-btn
          tile
          color="indigo"
          class="float-right"
          @click="opendialog('add')"
        >
          <v-icon left> mdi-plus </v-icon>
          {{ $t("add_category") }}
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
            {{ $t("category_list") }}
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
                  @click="editCategory(item)"
                >
                  <v-icon dark> mdi-pencil </v-icon>
                </v-btn>
                <v-btn
                  class="mx-2"
                  dark
                  small
                  color="red"
                  @click="deleteCategory(item)"
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
import addCategory from "../../components/product/addCategory.vue";
import editCategory from "../../components/product/editCategory.vue";
export default {
  name: "Category",
  middleware: "auth",
  head: {
    title: "Category"
  },
  components: { addCategory, editCategory },
  data() {
    return {
      search: "",
      isLoading: false,
      confirmation: false,
      update: false,
      headline: this.$t("category_add"),
      dialog: false,
      catid: "",
      categories: [],
      items: [],
      singleItem: {}
    };
  },
  computed: {
    headers() {
      return [
        {
          sortable: false,
          text: this.$t("name"),
          value: "name"
        },
        {
          sortable: false,
          text: this.$t("Parent category"),
          value: "parent_cat_name"
        },
        {
          sortable: false,
          text: this.$t("Short code"),
          value: "short_code"
        },
        {
          sortable: false,
          text: this.$t("action"),
          value: "actions"
        }
      ];
    }
  },

  mounted() {
    this.getCategories();
  },
  methods: {
    opendialog(type) {
      this.$store.commit("SET_MODAL", { type: type, status: true });
    },
    async getCategories() {
      this.isLoading = true;
      await this.$axios.get("/category").then(response => {
        this.items = response.data;
        this.isLoading = false;
      });
    },
    deleteCategory(item) {
      this.confirmation = true;
      this.catid = item.id;
    },
    async confirmDelete() {
      await this.$axios.delete(`category/${this.catid}`).then(res => {
        let data = {
          alert: true,
          message: "Category Deleted Successfully",
          type: "success"
        };
        this.$store.commit("SET_ALERT", data);
        this.$store.commit("SET_MODAL", false);
        this.confirmation = false;
        this.getCategories();
      });
    },
    editCategory(item) {
      this.$store.commit("SET_MODAL", { type: "edit", status: true });
      this.singleItem = item;
    }
  }
};
</script>

<style></style>
