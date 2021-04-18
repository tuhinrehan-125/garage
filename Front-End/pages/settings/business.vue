<template>
  <v-container>
    <!-- ---------------------------- -->

    <v-card>
      <v-toolbar flat color="cyan" dark>
        <v-toolbar-title>Business Settings</v-toolbar-title>
      </v-toolbar>
      <v-tabs vertical>
        <v-tab> Business </v-tab>
        <v-tab> Product </v-tab>
        <v-tab> Sale </v-tab>
        <v-tab> Email Settings </v-tab>
        <v-tab> SMS Settings </v-tab>

        <v-tab-item>
          <v-card flat>
            <add-business />
          </v-card>
        </v-tab-item>
        <v-tab-item>
          <v-card flat>
            <product-setting />
          </v-card>
        </v-tab-item>
        <v-tab-item>
          <v-card flat>
            <add-sale />
          </v-card>
        </v-tab-item>
        <v-tab-item>
          <v-card flat>
            <add-email />
          </v-card>
        </v-tab-item>
        <v-tab-item>
          <v-card flat>
            <add-sms />
          </v-card>
        </v-tab-item>
      </v-tabs>
    </v-card>

    <!-- ---------------------------- -->
  </v-container>
</template>

<script>
import addBusiness from "@/components/setting/addBusiness";
import addSale from "@/components/setting/addSale";
import addEmail from "@/components/setting/addEmail";
import addSMS from "@/components/setting/addSMS";
import ProductSetting from '../../components/setting/productSetting.vue';
export default {
  head: {
    title: "",
  },
  components: { addBusiness, productSetting, addSale, addEmail, addSMS },
  data() {},
  async asyncData({ params, axios }) {},
  mounted() {},
  methods: {},
};
</script>

<style>
.v-tabs-slider-wrapper {
  color: #00bcd4;
}
.v-item-group > * {
  background-color: #ffffff;
  border-right: 1px solid #00bcd4;
}
.v-tabs--vertical > .v-tabs-bar .v-tab {
  color: #00bcd4;
}

.v-tabs--vertical > .v-tabs-bar .v-tab {
  border-bottom: 1px solid #00bcd4;
}
.theme--light.v-tabs > .v-tabs-bar .v-tab--active {
  background: #00bcd4;
  color: white;
}
</style>
