<template>
  <v-container>
        <v-card>
          <v-card-title>
            Add Expense
          </v-card-title>
          <v-card-text>
            <v-form ref="form" v-model="valid" lazy-validation>
              <v-row no-gutters>
                <v-col cols="12" md="6" sm="12" xl="6">
                  <v-select
                      label="Select expense category"
                      v-model="form.exp_cat_id"
                      :items="exp_category"
                      item-text="name"
                      item-value="id"
                      dense
                      outlined
                    ></v-select>
                </v-col>
                
                <v-col cols="12" md="6" sm="12" xl="6">
                  <v-select
                    label="Select business location"
                    v-model="form.business_location_id"
                    :items="user_business_location"
                    item-text="name"
                    item-value="id"
                    dense
                    outlined
                  ></v-select>
                </v-col>
                <v-col cols="12" md="4" sm="12" xl="4">
                  <v-text-field
                      :label="$t('expense_for')"
                      required
                      outlined
                      dense
                      v-model="form.expense_for"
                    ></v-text-field>
                </v-col>
                <v-col cols="12" md="4" sm="12" xl="4">
                    <v-text-field
                      :label="$t('expense_amount')"
                      outlined
                      dense
                      v-model="form.amount"
                    ></v-text-field>
                  </v-col>
                  
                <v-col cols="12" md="4" sm="12" xl="4">
                    <v-text-field
                      :label="$t('ref_no')"
                      outlined
                      dense
                      v-model="form.ref_no"
                    ></v-text-field>
                  </v-col>
                  
                <v-col cols="12" md="4" sm="12" xl="4">
                <v-dialog
                  ref="dialog"
                  v-model="modal"
                  :return-value.sync="form.exp_date"
                  persistent
                  width="290px"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      v-model="form.exp_date"
                      label="Purchase date"
                      prepend-icon="mdi-calendar"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                      dense
                      outlined
                    ></v-text-field>
                  </template>
                  <v-date-picker v-model="form.exp_date" scrollable>
                    <v-spacer></v-spacer>
                    <v-btn text color="primary" @click="modal = false">
                      Cancel
                    </v-btn>
                    <v-btn
                      text
                      color="primary"
                      @click="$refs.dialog.save(form.exp_date)"
                    >
                      OK
                    </v-btn>
                  </v-date-picker>
                </v-dialog>
              </v-col>
                <v-col cols="12" md="4" sm="12" xl="4">
                  <v-text-field
                      :label="$t('note')"
                      required
                      outlined
                      dense
                      v-model="form.note"
                    ></v-text-field>
                </v-col>
                <v-col cols="12" md="4" sm="12" xl="4">
                    <v-checkbox
                      v-model="form.is_monthly_expense"
                      :label="$t('Is Monthly Expense')"
                      color="info"
                      value="info"
                      hide-details
                    ></v-checkbox>
                  </v-col>
                
              </v-row>
            </v-form>
            
          </v-card-text>
          <div class="center">
            <v-btn class="saveBtn" color="success" @click="submitForm()" :loading="isLoading">save</v-btn>
          </div>
        </v-card>

    

      </v-container>
</template>

<script>

export default {
  name: "addPurchase",
  head: {
    title: "Add Purchase"
  },
  components: {},
  data() {
    return {
      checkbox: false,
      //for product search
      loading: false,
      // items: [],
      search: null,
      select: null,
      
      valid: true,
      nameRules: [(v) => !!v || this.$t("brand_name_is_required")],
      form: {
        exp_cat_id: "",
        business_location_id: "",
        expense_for: "",
        amount: "",
        ref_no: "",
        is_monthly_expense: false,
        exp_date: "",
        note: "",
      },
      exp_category: [],
    };
  },
  computed: {
     user_business_location() {
      return this.$auth.user.data.user_business_location;
    }
  },
  async asyncData({ params, axios }) {},
  mounted() {
    this.getExpenseCategory();
    
  },
  methods: {
    async getExpenseCategory() {
      await this.$axios.get("/expense-category").then(response => {
        this.exp_category = response.data;
      });
    },
    async submitForm() {
      if (this.$refs.form.validate()) {
        await this.$axios.post("/expense", this.form).then((res) => {
          this.$refs.form.reset();
          let data = { alert: true, message: "Expense Addedd successfully" };
          this.$store.commit("SET_ALERT", data);
          this.$store.commit("SET_MODAL", false);
          this.$emit("refresh");
        });
      }
    },
  }
};
</script>

<style lang="scss" scoped>
.v-input--selection-controls.v-input {
  margin-top:3px;
}

.center {
  display: flex;
  justify-content: center;
  align-items: center;
}

.saveBtn{
  margin-bottom: 5px;
}
</style>
