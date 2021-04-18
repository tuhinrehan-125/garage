<template>
  <v-container fluid grid-list-xl class="mt-5">
    <v-row justify="center">
      <add-unit  :items="items" @refresh="getUnits()" />
      <edit-unit :item="singleItem" :items="items" @refresh="getUnits()" />
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
        <v-btn tile color="indigo" class="float-right" @click="opendialog('add')">
          <v-icon left> mdi-plus </v-icon>
          {{ $t("Add Unit") }}
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
            {{ $t("Unit List") }}
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
                  @click="editUnit(item)"
                >
                  <v-icon dark> mdi-pencil </v-icon>
                </v-btn>
                <v-btn
                  class="mx-2"
                  dark
                  small
                  color="red"
                  @click="deleteUnit(item)"
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
import addUnit from "~/components/product/addUnit";
import editUnit from "~/components/product/editUnit";
export default {
  name: "AddUnit",
  middleware: "auth",
  head: {
    title: "Add Unit",
  },
  components: {addUnit,editUnit},
  data() {
    return {
      search: "",
      isLoading: false,
      confirmation: false,
      update: false,
      headline: this.$t("Unit Add"),
      nameRules: [(v) => !!v || this.$t("Unit name is required")],
      dialog: false,
      units: [],
      unitId: "",
      categories: [],
      items: [],
      singleItem:{},
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
          text: this.$t("Short Name"),
          value: "short_name",
        },

        {
          sortable: false,
          text: this.$t("Base Unit"),
          value: "parent_cat_name",
        },

        {
          sortable: false,
          text: this.$t("Operation Value"),
          value: "value",
        },
        {
          sortable: false,
          text: this.$t("action"),
          value: "actions",
        },
      ];
    },
  },
  async asyncData({ params, axios }) {},
  mounted() {
    this.getUnits();
  },
  methods: {
    opendialog(type) {
      this.$store.commit("SET_MODAL", {type:type,status:true});
    },

    async getUnits() {
      this.isLoading = true;
      await this.$axios.get("/unit").then((response) => {
        this.items = response.data;
        this.isLoading = false;
      });
    },
    deleteUnit(item) {
      this.confirmation = true;
      this.unitId = item.id;
    },
    async confirmDelete() {
      await this.$axios.delete(`unit/${this.unitId}`).then((res) => {
        this.alert = true;
        this.message = "Unit Deleted Successfully";
        this.confirmation = false;
        this.getUnits();
      });
    },

    editUnit(item) {
      this.$store.commit("SET_MODAL", {type:'edit',status:true});
      this.singleItem=item
    },

  },
};
</script>

<style>
tr {
  height: 85px;
}

.pd {
  background-color: #ffffff;
}
</style>
