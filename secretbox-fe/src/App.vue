<template>
  <div id="app">
    <div>
      <router-view :isAuthenticated="isAuthenticated" :key="$route.fullPath"></router-view>
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
  padding-left: 20px;
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

.home {
  background-color: #fff;
  display: flex;
  min-width: 500px;
}

.sidebar-header,
#navbar {
  height: 66px;
}

.sidebar-button,
.info-banner {
  padding: 20px 40px;
}

.page-content {
  flex: 3;
  height: 100vh;
}

.main-content {
  width: 100%;
  display: flex;
}

.presentation {
  flex: 2 0 0%;
  overflow: hidden;
  position: relative;
  display: flex;
  flex-direction: column;
  padding-left: 40px;
}

.presentation-breadcrumb {
  height: 40px;
  margin-bottom: 20px;
}

.info-banner {
  flex: 0 2 25%;
  max-width: 420px;
  min-width: 200px;
}

.info-banner button {
  width: 100%;
}
</style>
