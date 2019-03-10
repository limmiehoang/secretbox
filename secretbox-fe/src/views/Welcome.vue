<template>
  <div>
    <nav id="nav">
      <div>
        <router-link to="/">SecretBox</router-link>
      </div>
      <ul>
        <li>
          <router-link to="/home">Home</router-link>
        </li>
        <li v-if="!isAuthenticated">
          <a href="" @click.prevent="login">Login</a>
        </li>
        <li v-if="isAuthenticated">
          <router-link to="/profile">Profile</router-link>
        </li>
        <li v-if="isAuthenticated">
          <a href="" @click.prevent="logout">Log out</a>
        </li>
      </ul>
    </nav>
    <img alt="Vue logo" src="../assets/logo.png" />
  </div>
</template>

<script>
export default {
  name: "welcome",
  props: {
    isAuthenticated: Boolean
  },
  methods: {
    login() {
      this.$auth.login();
    },
    logout() {
      this.$auth.logOut();
    }
  }
};
</script>