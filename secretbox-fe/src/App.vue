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
  color: #2c3e50;
}

a {
  font-weight: 500;
  color: #2c3e50;
}

a:hover,
.btn-link:hover {
  text-decoration: none;
  color: #e76123;
}

a.router-link-exact-active {
  color: #42b983;
}

.btn-primary {
  background-color: #e76123;
  border: #e76123;
  color: #fff;
  font-weight: 600;
}

.btn-primary:hover,
.btn-primary:active,
.btn-primary:visited {
    background-color: #343a40;
    border: #343a40;
}
</style>
