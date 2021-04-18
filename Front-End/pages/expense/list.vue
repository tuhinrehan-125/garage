<template>
  <v-container fluid grid-list-xl class="mt-5">
    <v-row justify="center">
      <base-material-snackbar
        v-model="alert"
        type="success"
        v-bind="{
          [parsedDirection[0]]: true,
          [parsedDirection[1]]: true
        }"
      >
        {{ message }}
      </base-material-snackbar>

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
          {{ $t("Add Expense") }}
        </v-btn>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12" md="12">
        <v-card>
          <v-card-title>
            {{ $t("expense_list") }}
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
            <v-skeleton-loader
              class="mx-auto"
              type="table"
              v-if="isLoading"
            ></v-skeleton-loader>
            <v-data-table v-else :headers="headers" :items="expenses">
              <template v-slot:item.actions="{ item }">
                <v-btn
                  class="mx-2"
                  dark
                  small
                  color="cyan"
                  @click="editExpense(item)"
                >
                  <v-icon dark> mdi-pencil </v-icon>
                </v-btn>
                <v-btn
                  class="mx-2"
                  dark
                  small
                  color="red"
                  @click="deleteExpense(item)"
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
import addExpense from "../../components/expense/addExpense.vue";
export default {
  name: "ExpenseList",
  middleware: "auth",
  head: {
    title: "Clients"
  },
  components: { addExpense},
  data() {
    return {
      isLoading: false,
      alert: false,
      dateMenu: false,
      dateValue: null,
      headline: this.$t("add_expense"),
      dialog: false,
      update: false,
      message: "",
      expid: "",
      confirmation: false,
      direction: "top right",
      expenseCats: [],
      form: {},
      headers: [
        {
          sortable: false,
          text: this.$t("Is Monthly"),
          value: "is_monthly_expense"
        },
        {
          sortable: false,
          text: this.$t("amount"),
          value: "amount"
        },
        {
          sortable: false,
          text: this.$t("expense_date"),
          value: "exp_date"
        },
        {
          sortable: false,
          text: this.$t("expense_category"),
          value: "exp_cat_name"
        },
        {
          sortable: false,
          text: this.$t("action"),
          value: "actions"
        }
      ],
      expenses: []
    };
  },
  computed: {
    parsedDirection() {
      return this.direction.split(" ");
    }
    
    
  },
  async asyncData({ params, axios }) {},
  mounted() {
    this.getExpCategories();
    this.getExpense();
  },
  methods: {
    opendialog(type) {
      this.$store.commit("SET_MODAL", { type: type, status: true });
    },

    async getExpCategories() {
      await this.$axios.get("/expense-category").then(response => {
        this.expenseCats = response.data;
      });
    },
    async getExpense() {
      this.isLoading = true;
      await this.$axios.get("/expense").then(response => {
        this.isLoading = false;
        this.expenses = response.data;
      });
    },
    editExpense(item) {
      this.update = true;
      this.dialog = true;
      this.headline = this.$t("edit_expense");
      this.form.exp_cat_id = item.exp_cat_id;
      this.form.expense_for = item.expense_for;
      this.form.amount = item.amount;
      this.form.ref_no = item.ref_no;
      this.form.exp_date = item.exp_date;
      this.form.note = item.note;
      this.expid = item.id;
    },
    deleteExpense(item) {
      this.confirmation = true;
      this.expid = item.id;
    },
    async confirmDelete() {
      await this.$axios.delete(`expense/${this.expid}`).then(res => {
        this.alert = true;
        this.message = "Expense Deleted Successfully";
        this.confirmation = false;
        this.getExpense();
      });
    },
    async addExpense() {
      if (this.update == false) {
        await this.$axios.post("expense", this.form).then(res => {
          this.message = "Expense Addedd successfully";
          this.dialog = false;
          this.alert = true;
          this.getExpense();
        });
      } else {
        await this.$axios
          .patch(`expense/${this.expid}`, this.form)
          .then(res => {
            this.message = "Expense Updated successfully";
            this.dialog = false;
            this.alert = true;
            this.getExpense();
          });
      }
    }
  }
};
</script>

<style></style>
