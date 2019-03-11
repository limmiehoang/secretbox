<template>
  <div class="home">
    <div class="sidebar-wrapper">
      <div class="sidebar-header">
        <a href="">SecretBox</a>
      </div>
      <div class="sidebar">
        <div class="" aria-label="Groups">
          <ul aria-label="Group list">
            <li class="group-item">Item 1</li>
          </ul>
        </div>
      </div>
    </div>
    <div class="page-content">
      <div class="nav-bar">

      </div>
      <div class="main-content">
        <div class="presentation">

        </div>
        <div class="info-banner">

        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "home",
  data() {
    return {
      profile: this.$auth.profile
    }
  },
  methods: {
    handleLoginEvent(data) {
      this.profile = data.profile;
    },
    logout() {
      this.$auth.logOut();
    }
  }
};
</script>

<style scoped>
.home {
  background-color: #fff;
  display: flex;
  font-size: 14px;
  min-width: 500px;
}

.sidebar-wrapper {
  flex: 0 0 25%;
  max-width: 420px;
  min-width: 240px;
  height: 100vh;
  background-color: #F7F9FA;
}

.sidebar-wrapper .sidebar-header {
  box-sizing: border-box;
  height: 50px;
  display: flex;
  justify-content: space-between;
  padding: 8px;
  width: 100%;
}

.sidebar-wrapper .sidebar {
  overflow-y: hidden;
  position: relative;
}

.sidebar-wrapper .group-item {
  height: 64px;
  position: relative;
  display: flex;
}

.page-content {
  flex: 3;
  height: 100vh;
}

.page-content .nav-bar {
  box-sizing: border-box;
  height: 50px;
  padding: 8px;
  position: relative;
  z-index: 201;
}

.page-content .main-content {
  display: flex;
  flex: 1 1 0%;
}

.main-content .presentation {
  flex: 2 0 0%;
  overflow: hidden;
  position: relative;
  display: flex;
  flex-direction: column;
}

.main-content .info-banner {
  flex: 0 2 33.33%;
  max-width: 420px;
  min-width: 200px;
}

.avatar {
  vertical-align: middle;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  margin-right: 10px;
}
</style>