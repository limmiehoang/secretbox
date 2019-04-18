<template>
  <div id="app">
    <div>
      <router-view :isAuthenticated="isAuthenticated"></router-view>
    </div>
  </div>
</template>

<script>
export default {
  name: "app",
  data() {
    return {
      isAuthenticated: false
    };
  },
  async created() {
    try {
      await this.$auth.renewTokens();
    } catch (e) {
      console.log(e);
    }
  },
  methods: {
    handleLoginEvent(data) {
      this.isAuthenticated = data.loggedIn;
      this.profile = data.profile;
      if (data.state.target) {
        this.$router.push(data.state.target || "/");
      }
    }
  }
};
</script>


<style>
ul {
  list-style: none;
}

#app {
  font-family: "Avenir", Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
}

a {
  font-weight: 500;
  color: #2c3e50;
}

a:hover {
  text-decoration: none;
}

a.router-link-exact-active {
  color: #42b983;
}
</style>
