<template>
  <v-app-bar id="app-bar" absolute app color="white" flat height="75">
    <v-btn class="mr-3" elevation="1" fab small @click="setDrawer(!drawer)">
      <v-icon v-if="value"> mdi-view-quilt </v-icon>
      <v-icon v-else> mdi-dots-vertical </v-icon>
    </v-btn>
    <v-toolbar-title
      class="hidden-sm-and-down font-weight-light"
      v-text="headline"
    />

    <v-spacer />

    <!-- <v-text-field
      label="Search"
      color="secondary"
      hide-details
      style="max-width: 165px"
    >
      <template v-if="$vuetify.breakpoint.mdAndUp" v-slot:append-outer>
        <v-btn class="mt-n2" elevation="1" fab small>
          <v-icon>mdi-magnify</v-icon>
        </v-btn>
      </template>
    </v-text-field> -->
    <!-- <NuxtLink to="/pos">
    <v-btn elevation="2" medium dark>POS</v-btn>
    </NuxtLink> -->

    <div class="mx-3" />
    <v-select
      style="max-width: 165px; top: 10px"
      :items="items"
      label="Language"
      item-text="name"
      item-value="code"
      dense
      solo
      v-model="getLang"
      @change="chnageLang"
    ></v-select>

    <div class="mx-3" />

    <v-menu
      bottom
      left
      offset-y
      origin="top right"
      transition="scale-transition"
    >
      <template v-slot:activator="{ attrs, on }">
        <v-btn class="ml-2" min-width="0" text v-bind="attrs" v-on="on">
          <v-icon>mdi-account</v-icon>
        </v-btn>
      </template>

      <v-list :tile="false" nav>
        <v-list-item-group color="primary">
          <v-list-item>
            <v-list-item-content>
              <v-list-item-title>My Profile</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item>
            <v-list-item-content>
              <v-list-item-title @click="logout">Logout</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </v-list-item-group>
      </v-list>
    </v-menu>
  </v-app-bar>
</template>

<script>
// Components
import { VHover, VListItem } from "vuetify";

// Utilities
import { mapState, mapMutations } from "vuex";

export default {
  name: "DashboardCoreAppBar",

  components: {
    AppBarItem: {
      render(h) {
        return h(VHover, {
          scopedSlots: {
            default: ({ hover }) => {
              return h(
                VListItem,
                {
                  attrs: this.$attrs,
                  class: {
                    "black--text": !hover,
                    "white--text secondary elevation-12": hover,
                  },
                  props: {
                    activeClass: "",
                    dark: hover,
                    link: true,
                    ...this.$attrs,
                  },
                },
                this.$slots.default
              );
            },
          },
        });
      },
    },
  },

  props: {
    value: {
      type: Boolean,
      default: false,
    },
  },

  data: () => ({
    items: [
      {
        code: "en",
        name: "English",
      },
      {
        code: "bn",
        name: "বাংলা",
      },
    ],
    getLang: "",
  }),
  mounted() {
    this.getLang = localStorage.getItem("lang")
      ? localStorage.getItem("lang")
      : "bn";
  },

  computed: {
    ...mapState(["drawer"]),
    headline() {
      return "";
      //return this.$store.getters['title/pagetitle'];
    },
  },

  methods: {
    ...mapMutations({
      setDrawer: "SET_DRAWER",
    }),
    async logout() {
      await this.$auth.logout();
    },
    chnageLang(value) {
      localStorage.setItem("lang", value)
      this.$store.commit('SET_LANG', value)
      this.$i18n.locale=value
    },
  },
};
</script>
<style scoped>
.v-application a {
    color: transparent !important;
}

</style>
