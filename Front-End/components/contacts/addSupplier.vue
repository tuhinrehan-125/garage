<template>
  <v-dialog v-model="dialog" persistent max-width="800px">
    <v-card>
      <v-card-title>
        Add supplier
        <v-spacer></v-spacer>
        <v-btn text @click="closedialog">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-card-title>
      <v-card-text>
        <v-container>
            <v-form ref="form" v-model="valid" lazy-validation>
          <v-row no-gutters>
            <v-col  cols="12" md="6">
              <v-text-field
                class="smalltext"
                :label="$t('supplier_name')"
                required
                outlined
                dense
                :rules="[v => !!v || 'name is required']"
                v-model="form.name"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                :label="$t('email')"
                v-model="form.email"
                outlined
                :rules="[v => !!v || 'email is required']"
                dense
              ></v-text-field>
            </v-col>

            <v-col cols="12" md="6">
              <v-text-field
                :label="$t('phone_no')"
                v-model="form.mobile"
                outlined
                dense
              ></v-text-field>
            </v-col>


            <v-col cols="12" md="6">
              <v-text-field
                :label="$t('address')"
                v-model="form.address"
                outlined
                :rules="[v => !!v || 'address is required']"
                dense
              ></v-text-field>
            </v-col>
          </v-row>
            </v-form>
        </v-container>
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
  head: {
    title: "",
  },
  components: {},
  data() {
    return {
      valid:false,
      form: {
        name: "",
        supplier_business_name:"",
        type:"supplier",
        email:"",
        mobile: "",
        address: "",

      },
    };
  },
  computed: {
    dialog() {
      return this.$store.getters.modaltype == "add"
        ? this.$store.getters.modal
        : false;
    },
  },
  async asyncData({ params, axios }) {},
  mounted() {},
  methods: {
    closedialog() {
      this.$store.commit("SET_MODAL", { type: "", status: false });
    },
    async submitForm() {
      if (this.$refs.form.validate()) {
        await this.$axios.post("/contact", this.form).then((res) => {
          this.$refs.form.reset();
          let data = { alert: true, message: "Supplier Added Successfully",type:'success' };
          this.$store.commit("SET_ALERT", data);
          this.$store.commit("SET_MODAL", false);
          this.$emit("refresh");
        });
      }
    },
  },
};
</script>

<style>
</style>
