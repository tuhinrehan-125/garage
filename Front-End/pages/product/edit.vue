<template>

  <v-container fluid>
    <v-row justify="center">


      <v-col cols="12" sm="12" md="12">
        <v-card>
          <v-card-title>
            {{ $t("Add Product") }}
          </v-card-title>
          <v-card-text>
            <v-form
              ref="form"
              method="post"

              lazy-validation

            >
              <v-row no-gutters>
                <v-col cols="12" md="4" sm="12" xl="4">
                  <v-text-field
                    label="Product Name"
                    outlined
                    dense
                    required
                    :rules="[v => !!v || 'Name is required']"

                  ></v-text-field>

                </v-col>


              </v-row>
              <v-row>
                <v-col cols="12" class="">
                  <v-btn
                    class="float-right"
                    dark
                    color="success"

                  >
                    <v-icon dark> mdi-plus </v-icon>Submit
                  </v-btn>
                </v-col>
              </v-row>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  name: "edit",
  props: ["items"],
  data() {
    return {
      valid: true,
      form: {},
    };
  },
  created () {
    console.log('');

  },

  methods: {

    async submitForm() {
      if (this.$refs.form.validate()) {
        console.log(this.form);
        await this.$axios
          .patch(`product/${this.form.id}`, this.form)
          .then((res) => {
            this.$refs.form.reset();
            let data = {
              alert: true,
              message: "Brand Updated Successfully",
            };

          });
      }
    },
  },
  watch: {
    items(val) {
      this.form = val;
    },
  },
}

</script>

<style scoped>

</style>
