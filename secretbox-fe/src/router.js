import Vue from "vue";
import Router from "vue-router";
import Welcome from "./views/Welcome.vue";
import Home from "./views/Home.vue";
import NewGroup from "./views/NewGroup.vue";
import Profile from "./views/Profile.vue";
import auth from "./auth/authService";
import ExternalApiView from "./views/ExternalApi.vue";

Vue.use(Router);

const router = new Router({
  mode: "history",
  base: process.env.BASE_URL,
  routes: [
    {
      path: "/",
      name: "welcome",
      component: Welcome
    },
    {
      path: "/home",
      name: "home",
      component: Home
    },
    {
      path: "/new",
      name: "new-group",
      component: NewGroup
    },
    {
      path: "/profile",
      name: "profile",
      component: Profile
    },
    {
      path: "/external-api",
      name: "external-api",
      component: ExternalApiView
    }
  ]
});

router.beforeEach((to, from, next) => {
  if (to.path === '/' || auth.isAuthenticated()) {
    return next();
  }

  // Specify the current path as the customState parameter, meaning it
  // will be returned to the application after auth
  auth.lockLogin({ target: to.path });
});

export default router;
