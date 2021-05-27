<template>
  <v-navigation-drawer
    v-model="drawer"
    app
    max-width="160"
    :dark="barColor !== ''"
  >
    <v-list dense nav>
      <NuxtLink to="/dashboard">
        <v-list-item>
          <!--          <img src="~/assets/image/e-shop.png" style="width: 100%" />-->
          <h1 class="text-center">Vehicle Management</h1>
        </v-list-item>
      </NuxtLink>
    </v-list>
    <v-list dense expand nav>
      <template v-for="(item, i) in columns">
        <v-list-item
          v-if="!item.children"
          color="white"
          :key="`subheader-${i}`"
          :to="item.to"
        >
          <v-list-item-icon>
            <v-icon small>{{ item.icon }}</v-icon>
          </v-list-item-icon>
          <v-list-item-title>{{ item.title }}</v-list-item-title>
        </v-list-item>
        <!-- else if it has children -->
        <v-list-group
          v-else
          :group="item.to"
          color="white"
          :key="`subheader-${i}`"
          :prepend-icon="item.icon"
        >
          <!-- this template is for the title of top-level items with children -->
          <template #activator>
            <v-list-item-content>
              <v-list-item-title>
                <!-- <v-icon>{{ item.icon }}</v-icon> -->
                {{ item.title }}
              </v-list-item-title>
            </v-list-item-content>
          </template>
          <!-- this template is for the children/sub-items (2nd level) -->
          <template v-for="(subItem, j) in item.children">
            <!-- another v-if to determine if there's a 3rd level -->
            <!-- if there is NOT a 3rd level -->
            <v-list-item
              v-if="!subItem.children"
              class="ml-5"
              :key="`subheader-${j}`"
              :to="item.to + subItem.to"
            >
              <v-list-item-icon class="mr-4">
                <v-icon v-text="subItem.icon" small />
              </v-list-item-icon>
              <v-list-item-title class="ml-0">
                {{ subItem.title }}
              </v-list-item-title>
            </v-list-item>
          </template>
        </v-list-group>
      </template>
    </v-list>
  </v-navigation-drawer>
</template>

<script>
// Utilities
import { mapState } from "vuex";
export default {
  name: "DashboardCoreDrawer",
  props: {
    expandOnHover: {
      type: Boolean,
      default: false
    },
    routeChange: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {};
  },
  computed: {
    ...mapState(["barColor", "barImage"]),
    drawer: {
      get() {
        return this.$store.state.drawer;
      },
      set(val) {
        this.$store.commit("SET_DRAWER", val);
      }
    },
    profile() {
      return {
        avatar: true,
        title: this.avatar
      };
    },

    columns() {
      return [
        {
          icon: "mdi-view-dashboard",
          title: this.$t("dashboard"),
          to: "/dashboard"
        },
        {
          icon: "mdi-account",
          title: this.$t("products"),
          to: "/product",
          children: [
            {
              icon: "mdi-shape",
              title: this.$t("Product List"),
              to: "/list"
            },
            {
              icon: "mdi-shape",
              title: this.$t("Add Product"),
              to: "/add"
            },
            {
              icon: "mdi-shape",
              title: this.$t("category"),
              to: "/category"
            },
            {
              icon: "mdi-shape",
              title: this.$t("Brand"),
              to: "/brand"
            },
            // {
            //   icon: "mdi-shape",
            //   title: this.$t("Unit"),
            //   to: "/unit"
            // },
            // {
            //   icon: "mdi-shape",
            //   title: this.$t("variation"),
            //   to: "/variation"
            // }
          ]
        },
        {
          icon: "mdi-account-box-multiple",
          title: this.$t("Contacts"),
          to: "/contact",
          children: [
            {
              icon: "mdi-account-arrow-left-outline",
              title: this.$t("supplier"),
              to: "/supplier"
            },
            {
              icon: "mdi-account-arrow-right-outline",
              title: this.$t("clients"),
              to: "/customers"
            },
            // {
            //   icon: "mdi-account-group-outline",
            //   title: this.$t("customer_group"),
            //   to: "/customer-groups"
            // }
          ]
        },

        {
          icon: "mdi-car",
          title: this.$t("Vehicles"),
          to: "/vehicle",
          children: [
            {
              icon: "mdi-shape",
              title: this.$t("Vehicle List"),
              to: "/list"
            },
            {
              icon: "mdi-shape",
              title: this.$t("Add Vehicle"),
              to: "/add"
            },
            {
              icon: "mdi-shape",
              title: this.$t("Vehicle Type"),
              to: "/vehicletype"
            },
            {
              icon: "mdi-shape",
              title: this.$t("Vehicle Color"),
              to: "/vehiclecolor"
            },
          ]
        },

        {
          icon: "mdi-link",
          title: this.$t("Services"),
          to: "/service",
        },
        {
          icon: "mdi-cart",
          title: this.$t("Purchase"),
          to: "/purchase",
          children: [
            {
              icon: "mdi-format-list-bulleted",
              title: this.$t("Purchase List"),
              to: "/list"
            },
            {
              icon: "mdi-plus-circle-outline",
              title: this.$t("Add Purchase"),
              to: "/add"
            }
          ]
        },
        // {
        //   icon: "mdi-inbox-arrow-up",
        //   title: this.$t("expense"),
        //   to: "/expense",
        //   children: [
        //     {
        //       icon: "mdi-inbox-arrow-up",
        //       title: this.$t("expense_list"),
        //       to: "/list"
        //     },
        //     {
        //       icon: "mdi-inbox-arrow-up",
        //       title: this.$t("add_expense"),
        //       to: "/add",
        //     },
        //     {
        //       icon: "mdi-shape",
        //       title: this.$t("expense_category"),
        //       to: "/category"
        //     }
        //   ]
        // },
        // {
        //   icon: "mdi-cart-arrow-right",
        //   title: this.$t("Invoice"),
        //   to: "/sell",
        //   children: [
        //     {
        //       icon: "mdi-format-list-bulleted",
        //       title: this.$t("All Invoices"),
        //       to: "/list"
        //     },
        //     {
        //       icon: "mdi-plus-circle-outline",
        //       title: this.$t("Add Invoice"),
        //       to: "/create"
        //     }
        //   ]
        // },
        {
          icon: "mdi-cart-arrow-right",
          title: this.$t("Invoice"),
          to: "/invoice",
          children: [
            {
              icon: "mdi-format-list-bulleted",
              title: this.$t("Invoice List"),
              to: "/list"
            },
            {
              icon: "mdi-plus-circle-outline",
              title: this.$t("Add Invoice"),
              to: "/create"
            }
          ]
        },

        {
          title: this.$t("settings"),
          icon: "mdi-cog-outline",
          to: "/settings",
          children: [
            {
              icon: "mdi-cart-arrow-right",
              title: this.$t("Colors"),
              to: "/business"
            },
            {
              icon: "mdi-inbox-arrow-up",
              title: this.$t("Types"),
              to: "/location"
            }
          ]
        }
      ];
    }
  },
  methods: {
    log() {
      console.log(this.$route.path);
    }
  }
};
</script>
<style scoped>
.theme--dark.v-navigation-drawer {
  background-color: #7fcec5;
  background-image: linear-gradient(315deg, #7fcec5 0%, #14557b 74%);
  /* background-color: #494ca2; */
}
</style>
