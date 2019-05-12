<template>
  <div id="sidebar">
    <div class="sidebar-header">
        <router-link to="/">
          <img
            src="../assets/logo.png"
            alt="SecretBox">
          <span>SecretBox</span>
        </router-link>
      </div>
      <div class="sidebar">
        <div class="sidebar-button">
          <router-link to="/group/new">
            <button class="btn btn-outline-dark btn-sm">New Group</button>
          </router-link>
        </div>
        <div class="sidebar-nav">
          <div class="sidebar-nav-item">
            <router-link to="/home">
              Dashboard
            </router-link>
          </div>
          <div class="sidebar-nav-item">
            <a href="">My groups</a>
            <div class="sidebar-group-list">
              <ul aria-label="Group list">
                <li class="sidebar-group-list-item" v-for="group in groups" :key="group.id">
                  <router-link :to="`/group/${group.id}`">{{ group.name }}</router-link>
                </li>
                <li class="sidebar-group-list-item">
                  <a href="">Group 2</a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      groups: [],
      messages: []
    }
  },
  async created() {
    const accessToken = await this.$auth.getAccessToken();
    this.$http
      .get('api/groups', {
        headers: {
          Authorization: `Bearer ${accessToken}`
        }
      })
      .then(response => {
        if (response.body.success) {
          this.groups = response.body.data;
        } else {
          this.messages.push(response.body.message);
        }
      },
      response => {
        this.messages.push(response.body.message);
      });
  }
}
</script>

<style>
#sidebar {
  flex: 0 0 20%;
  max-width: 420px;
  min-width: 240px;
  height: 100vh;
  background-color: #F7F9FA;
}

.sidebar-header,
.sidebar-nav {
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
  text-align: left;
  justify-content: flex-start;
  padding: 16px 40px;
  width: 100%;
}

.sidebar-header a {
  line-height: 34px;
  display: flex;
}

.sidebar-header span {
  line-height: 34px;
  display: inline-block;
  font-size: 20px;
  font-weight: 600;
  opacity: .84;
  margin-left: 15px;
  color: #333;
}

.sidebar-header img {
  width: 34px;
  height: 34px;
}

.sidebar {
  overflow-y: hidden;
  position: relative;
}

.sidebar-button button {
  width: 100%;
  font-weight: 600;
}

.sidebar-nav-item>a {
  font-size: 16px;
  line-height: 26px;
}

.sidebar-group-list-item {
  position: relative;
  display: flex;
}

.sidebar-group-list-item a {
  font-size: 15px;
  line-height: 24px;
}
</style>
