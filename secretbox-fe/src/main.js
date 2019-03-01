import Vue from "vue";
import App from "./App.vue";
import router from "./router";
import store from "./store";
import AuthPlugin from "./plugins/auth";

Vue.use(AuthPlugin);

Vue.config.productionTip = false;

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount("#app");
