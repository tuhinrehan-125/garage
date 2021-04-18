<template>
  <v-container fluid>
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
      <v-col cols="12" sm="12" md="12">
        <base-material-card color="primary">
          <template v-slot:heading>
            <div class="body-3 font-weight-light">Settings</div>
          </template>
          <v-card-text>
            <v-row no-gutters justify="center">
              <v-col cols="12" md="8">
                <v-row justify="center">
                  <v-col cols="12">
                    <v-form>
                      <v-container class="py-0">
                        <v-row>
                          <v-col cols="12" md="6">
                            <v-text-field
                              v-model="form.arot_commission"
                              label="Arot Commission"
                              required
                            ></v-text-field>
                          </v-col>
                          <v-col cols="12" md="6">
                            <v-text-field
                              v-model="form.bazar_commission"
                              label="Bazar Commission"
                              required
                            ></v-text-field>
                          </v-col>

                          <v-col cols="12" class="text-right">
                            <v-btn
                              color="primary"
                              min-width="150"
                              @click="saveCommission()"
                            >
                              Save
                            </v-btn>
                          </v-col>
                        </v-row>
                      </v-container>
                    </v-form>
                  </v-col>
                </v-row>
              </v-col>
            </v-row>
          </v-card-text>
        </base-material-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  head: {
    title: "",
  },
  components: {},
  data() {
    return {
      message: "",
      direction: "top right",
      alert: false,
      form: {
        arot_commission: "",
        bazar_commission: "",
      },
    };
  },
  computed: {
    parsedDirection() {
      return this.direction.split(" ");
    },
  },
  async asyncData({ params, axios }) {},
  mounted() {
    this.settings();
  },
  methods: {
    async settings() {
      await this.$axios.get("/settings").then((res) => {
        this.form.arot_commission = res.data.data.arot_commission;
        this.form.bazar_commission = res.data.data.bazar_commission;
      });
    },
    async saveCommission() {
      await this.$axios.post("/update-settings", this.form).then((res) => {
        this.message = "Settings Updated successfully";
        this.alert = true;
        this.settings();
      });
    },
  },
};
</script>

<style>
</style>
