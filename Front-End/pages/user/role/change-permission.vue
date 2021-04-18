<template>
  <v-container>
  <v-container fluid>
        <h3>  {{ $t("Add Role") }} </h3>
    <v-card style="" class="">
      <v-card-title>
        <v-spacer></v-spacer>
      </v-card-title>
      <v-row class="d-flex mx-1">

        <v-col cols="12" md="6">
          <h3> Role Name</h3>
          <v-text-field
            class=""
            outlined
            dense
            required>
          </v-text-field>

        </v-col>

      </v-row>

      <v-row justify="" style="margin-top: 10px">

        <v-col cols="12" sm="12" md="12">
          <h3 class="pb-3 mt-n4 mx-5"> Role Permissions</h3>
          <v-row style="">
            <v-col cols="12" sm="6" md="6">
              <v-treeview
                style="width: 700px; font-size: 12px"
                selectable
                selected-color="#4bd7a7"
                open-on-click
                :items="items"
              ></v-treeview>
            </v-col>
            <v-col cols="12" sm="6" md="6">
              <v-treeview
                style="width: 700px; font-size: 12px"
                selectable
                selected-color="#4bd7a7"
                open-on-click
                :items="items2"
              ></v-treeview>
            </v-col>
          </v-row>

          <v-row style="" class="">
            <v-col cols="12" sm="6" md="6">
              <v-treeview
                style="width: 900px ;font-size: 12px"
                selectable
                selected-color="#4bd7a7"
                open-on-click
                :items="items3"
              ></v-treeview>
            </v-col>
            <v-col cols="12" sm="6" md="6">
              <v-treeview
                style="font-size: 12px; width: 700px"
                selectable
                selected-color="#4bd7a7"
                open-on-click
                :items="items4"
              ></v-treeview>
            </v-col>
          </v-row>


          <v-row justify="center" style="height: 150px" class="pb-16">
            <div>
              <v-btn color="info" @click="">
                {{ $t("save") }}
              </v-btn>
            </div>
          </v-row>


        </v-col>
      </v-row>
    </v-card>
  </v-container>
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
      items: [
        {
          // id: 1,
          id: "user",
          name: "User :",
          children: [
            {id: 2, name: "View"},
            {id: 3, name: "Add"},
            {id: 4, name: "Edit"},
            {id: 5, name: "Delete"},
          ],
        },
        {
          // id: 6,
          id: "vendor",
          name: "Vendor :",
          children: [
            {id: 7, name: "View"},
            {id: 8, name: "Add"},
            {id: 9, name: "Edit"},
            {id: 10, name: "Delete"},
          ],
        },
        {
          // id: 11,
          id: "customer",
          name: "Customer :",
          children: [
            {id: 12, name: "View"},
            {id: 13, name: "Add"},
            {id: 14, name: "Edit"},
            {id: 15, name: "Delete"},
          ],
        },
        {
          // id: 16,
          id: "product",
          name: "Product :",
          children: [
            {id: 17, name: "View"},
            {id: 18, name: "Add"},
            {id: 19, name: "Edit"},
            {id: 20, name: "Delete"},
            {id: 21, name: "Add Opening Stock"},
            {id: 22, name: "View Purchase Price"},
          ],
        },
        {
          // id: 23,
          id: "purchase",
          name: "Purchase :",
          children: [
            {id: 24, name: "View"},
            {id: 25, name: "Add"},
            {id: 26, name: "Edit"},
            {id: 27, name: "Delete"},
          ],
        },
        {
          // id: 28,
          id: "sell",
          name: "sell :",
          children: [
            {id: 29, name: "View"},
            {id: 30, name: "Add"},
            {id: 31, name: "Edit"},
            {id: 32, name: "Delete"},
          ],
        },
      ],
      items2: [
        {
          // id: 33,
          id: "expense",
          name: "Expense :",
          children: [
            {id: 34, name: "View"},
            {id: 35, name: "Add"},
            {id: 36, name: "Edit"},
            {id: 37, name: "Delete"},
          ],
        },
        // {
        //   id: 34,
        //   name: "Transfer :",
        //   children: [
        //     {id: 35, name: "View"},
        //     {id: 36, name: "Add"},
        //     {id: 37, name: "Edit"},
        //     {id: 38, name: "Delete"},
        //   ],
        // },
        {
          id: 39,
          name: "Sale Return :",
          children: [
            {id: 40, name: "View"},
            {id: 41, name: "Add"},
            {id: 42, name: "Edit"},
            {id: 43, name: "Delete"},
          ],
        },
        {
          id: 44,
          name: "Brand :",
          children: [
            {id: 45, name: "View"},
            {id: 46, name: "Add"},
            {id: 47, name: "Edit"},
            {id: 48, name: "Delete"},
          ],
        },
        {
          id: 49,
          name: "Unit :",
          children: [
            {id: 50, name: "View"},
            {id: 51, name: "Add"},
            {id: 52, name: "Edit"},
            {id: 53, name: "Delete"},
          ],
        },
        {
          id: 54,
          name: "Category :",
          children: [
            {id: 55, name: "View"},
            {id: 56, name: "Add"},
            {id: 57, name: "Edit"},
            {id: 58, name: "Delete"},
          ],
        },
      ],
      items3: [
        {
          id: 59,
          name: "Accounting :",
          children: [
            {id: 60, name: "Account"},
            {id: 61, name: "Money Transfer"},

          ],
        },
        {
          id: 64,
          name: "Reports :",
          children: [
            {id: 65, name: "Sales Report"},
            {id: 66, name: "Expense Report"},
            {id: 67, name: "Collection Report"},
            {id: 68, name: "Payment Report"},
          ],
        },

      ],


      items4: [

        {
          id: 69,
          name: "HRM :",
          children: [
            {id: 70, name: "Department"},
            {id: 71, name: "Attendance"},
          ],
        },
        {
          id: 74,
          name: "Settings :",
          children: [
            {id: 75, name: "Business Settings"},
            {id: 76, name: "Business Location"},
          ],
        },
      ],
    };
  },
  computed: {},
  async asyncData({params, axios}) {
  },
  mounted() {
  },
  methods: {

  },
};
</script>

<style>
.v-treeview-node__children {
  display: flex;
  flex-wrap: wrap;
}

v-container {
  height: 100vh;
}
</style>
