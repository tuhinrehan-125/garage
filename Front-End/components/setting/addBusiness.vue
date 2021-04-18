<template>
  <v-container>
    <v-form ref="form" v-model="valid" lazy-validation>
      <v-row>
        <v-col cols="12" sm="6" md="4">
          <p><b> Business Name:*</b></p>
          <v-text-field
            v-model="name"
            :counter="10"
            :rules="nameRules"
            label="Name"
            required
            dense
            outlined
          ></v-text-field>
        </v-col>
        <v-col cols="12" sm="6" md="4">
          <p><b> Start Date:</b></p>
          <v-dialog
            ref="dialog"
            v-model="modal"
            :return-value.sync="date"
            persistent
            width="290px"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-text-field
                v-model="date"
                label="Picker in dialog"
                prepend-icon="mdi-calendar"
                readonly
                v-bind="attrs"
                v-on="on"
                dense
                outlined
              ></v-text-field>
            </template>
            <v-date-picker v-model="date" scrollable>
              <v-spacer></v-spacer>
              <v-btn text color="primary" @click="modal = false">
                Cancel
              </v-btn>
              <v-btn text color="primary" @click="$refs.dialog.save(date)">
                OK
              </v-btn>
            </v-date-picker>
          </v-dialog>
        </v-col>

        <v-col cols="12" sm="6" md="4">
          <p><b> Default Profit Percent:*</b></p>
          <v-text-field value="25" required dense outlined></v-text-field>
        </v-col>
      </v-row>

      <v-row>
        <v-col cols="12" sm="6" md="4">
          <p><b> Currency:</b></p>
          <v-select
            v-model="select"
            :items="items"
            label="Item"
            dense
            outlined
          ></v-select>
        </v-col>
        <v-col cols="12" sm="6" md="4">
          <p><b>Time Zone:</b></p>
          <v-select
            v-model="select"
            :items="items"
            label="Item"
            dense
            outlined
          ></v-select>
        </v-col>

        <v-col cols="12" sm="6" md="4">
          <p><b>Upload Logo:</b></p>
          <v-file-input
            label="File input"
            prepend-icon="mdi-camera"
            dense
            outlined
          ></v-file-input>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" sm="6" md="6">
          <p><b>Financial year start month:</b></p>

          <v-select
            :items="[
              'January',
              'February',
              'March',
              'April',
              'May',
              'June',
              'July',
            ]"
            label="Months"
            dense
            outlined
          ></v-select>
        </v-col>

        <v-col cols="12" sm="6" md="6">
          <p><b>Stock Accounting Method:* </b></p>
          <v-select
            :items="['FIFO(First In First Out)', 'LIFO(Last In First Out)']"
            label="Methods"
            dense
            outlined
          ></v-select>
        </v-col>
      </v-row>

      <v-row>
        <v-col cols="12" sm="6" md="4">
          <p><b>Transaction Edit Days:*</b></p>
          <v-text-field value="30" required dense outlined></v-text-field>
        </v-col>
        <v-col cols="12" sm="6" md="4">
          <p><b>Date Formet:* </b></p>
          <v-select
            :items="['dd-mm-yyyy', 'mm-dd-yyyy', 'dd/mm/yyy', 'mm/dd/yyyy']"
            label="Formet"
            dense
            outlined
          ></v-select>
        </v-col>
        <v-col cols="12" sm="6" md="4">
          <p><b>Time Formet:* </b></p>
          <v-select
            :items="['12 Hour', '24 Hour']"
            label="Formet"
            dense
            outlined
          ></v-select>
        </v-col>
      </v-row>

      <v-btn :disabled="!valid" color="success" class="mr-4" @click="validate">
        Validate
      </v-btn>

      <v-btn color="error" class="mr-4" @click="reset"> Reset Form </v-btn>
    </v-form>
  </v-container>
</template>

<script>
export default {
  head: {
    title: "",
  },
  components: {},
  data: () => ({
    valid: true,
    name: "",
    nameRules: [
      (v) => !!v || "Name is required",
      (v) => (v && v.length <= 10) || "Name must be less than 10 characters",
    ],
    select: null,
    items: ["Item 1", "Item 2", "Item 3", "Item 4"],
    checkbox: false,

    date: new Date().toISOString().substr(0, 10),
    modal: false,
  }),
  computed: {},
  async asyncData({ params, axios }) {},
  mounted() {},
  methods: {
    validate() {
      this.$refs.form.validate();
    },
    reset() {
      this.$refs.form.reset();
    },
  },
};
</script>

<style>
</style>