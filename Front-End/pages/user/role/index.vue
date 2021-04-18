<template>
  <v-container fluid grid-list-xl class="mt-5">
    <v-row justify="center">
      <v-dialog v-model="dialog" persistent max-width="600px">
        <v-card>
          <v-card-title>
            Add role
            <v-spacer></v-spacer>
            <v-btn text @click="dialog = false">
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>
          <v-card-text>
            <v-container>
              <v-form ref="form" v-model="valid" lazy-validation>
                <v-row no-gutters>
                  <v-col cols="12" sm="12" md="12">
                    <v-text-field
                      outlined
                      dense
                      :label="$t('Role Name')"
                      required
                      :rules="nameRules"
                      v-model="form.name"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="12" md="12">
                    <v-textarea
                      counter
                      label="Description"
                      outlined
                    ></v-textarea>
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
      <v-dialog v-model="confirmation" max-width="300" class="pd">
        <v-card class="pd">
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
          {{ $t("Add Role") }}
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
            {{ $t("All Users") }}
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
              <template v-slot:item.base_unit_id="{ item }">
                <p style="color: #333399">
                  {{ baseUnitName(item.base_unit_id) }}
                </p>
              </template>
              <template v-slot:item.actions="{ item }">
                <v-btn
                  class="mx-2"
                  dark
                  small
                  style="background-color: #4bd7a7"
                  @click="editUnit(item)"
                >
                  <v-icon dark> mdi-pencil </v-icon>
                </v-btn>
                <v-btn
                  class="mx-2"
                  dark
                  small
                  style="background-color: #ff699b"
                  @click="deleteUnit(item)"
                >
                  <v-icon dark> mdi-delete </v-icon>
                </v-btn>
                <v-btn
                  class="mx-2"
                  dark
                  small
                  style="background-color: #6244a6"
                  :to="{
                    name: 'userManagement-roleChange-permission',
                    params: { id: item.customer_id }
                  }"
                >
                  <v-icon dark> mdi-shield-lock-outline </v-icon>
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
  name: "RoleList",
  middleware: "auth",
  head: {
    title: "Role List"
  },

  data() {
    return {
      search: "",
      isLoading: false,
      confirmation: false,
      update: false,
      headline: this.$t("Add Role"),
      alert: false,
      valid: true,
      message: "",
      nameRules: [v => !!v || this.$t("Role name is required")],
      dialog: false,
      form: {
        name: "",
        short_code: "",
        base_unit: "",
        operator: "",
        operation_value: ""
      },
      units: [],
      parents: [],
      catid: "",
      direction: "top right",
      categories: [],
      items: []
    };
  },
  computed: {
    headers() {
      return [
        {
          sortable: false,
          text: this.$t("Roles"),
          value: "name"
        },
        {
          sortable: false,
          text: this.$t("Description"),
          value: "description"
        },
        {
          sortable: false,
          text: this.$t("action"),
          value: "actions"
        }
      ];
    },
    parsedDirection() {
      return this.direction.split(" ");
    }
  },
  async asyncData({ params, axios }) {},
  mounted() {
    this.getUnits();
  },
  methods: {
    baseUnitName(id) {
      if (id) {
        console.log(id);
        let base_unit = this.items.find(item => {
          return id == item.id;
        });
        console.log(base_unit);
        return base_unit.name;
      } else {
        return "N/A";
      }
    },
    async getUnits() {
      this.isLoading = true;
      await this.$axios.get("/unit").then(response => {
        this.items = response.data.units;
        this.parents = response.data.parents;
        this.isLoading = false;
      });
    },
    deleteUnit(item) {
      this.confirmation = true;
      this.catid = item.id;
    },
    async confirmDelete() {
      await this.$axios.delete(`unit/${this.catid}`).then(res => {
        this.alert = true;
        this.message = "Unit Deleted Successfully";
        this.confirmation = false;
        this.getUnits();
      });
    },
    editUnit(item) {
      this.update = true;
      this.dialog = true;
      this.headline = this.$t("unit_edit");
      this.form.name = item.name;
      this.form.short_code = item.short_code;
      this.form.base_unit = item.base_unit;
      this.form.operator = item.operator;
      this.form.operation_value = item.operation_value;
      this.catid = item.id;
    },
    async submitForm() {
      if (this.$refs.form.validate()) {
        if (this.update == false) {
          await this.$axios.post("/unit", this.form).then(res => {
            this.message = "Unit Addedd Successfully";
            this.dialog = false;
            this.alert = true;
            this.getUnits();
          });
        } else {
          await this.$axios.patch(`unit/${this.catid}`, this.form).then(res => {
            this.message = "Unit Updated successfully";
            this.dialog = false;
            this.alert = true;
            this.getUnits();
          });
        }
      }
    }
  }
};
</script>

<style>

</style>



