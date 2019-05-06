import 'bootstrap'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

import Vue from "vue";
import App from "./App.vue";
import router from "./router";
import store from "./store";
import VueResource from "vue-resource"
import AuthPlugin from "./plugins/auth";
import CryptoPlugin from "./plugins/crypto";
import HelperPlugin from "./plugins/helpers";
import BootstrapVue from 'bootstrap-vue';

Vue.use(VueResource);
Vue.use(BootstrapVue);

Vue.use(AuthPlugin);
Vue.use(CryptoPlugin);
Vue.use(HelperPlugin);

Vue.config.productionTip = false;
Vue.http.options.root = 'http://localhost:8000';

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount("#app");
