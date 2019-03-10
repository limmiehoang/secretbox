import 'bootstrap'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'reset-css'

import Vue from "vue";
import App from "./App.vue";
import router from "./router";
import store from "./store";
import AuthPlugin from "./plugins/auth";
import BootstrapVue from 'bootstrap-vue';

Vue.use(AuthPlugin);
Vue.use(BootstrapVue);

Vue.config.productionTip = false;

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount("#app");
