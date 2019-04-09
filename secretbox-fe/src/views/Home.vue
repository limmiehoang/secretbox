<template>
  <div class="home">
    <div class="sidebar-wrapper">
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
          <button class="btn btn-outline-dark btn-sm">New Group</button>
        </div>
        <div class="sidebar-nav">
          <div class="sidebar-nav-item">
            <a href="">My files</a>
          </div>
          <div class="sidebar-nav-item">
            <a href="">My groups</a>
            <div class="group-list">
              <ul aria-label="Group list">
                <li class="group-item">
                  <a href="">Group 1</a>
                </li>
                <li class="group-item">
                  <a href="">Group 2</a>
                </li>
              </ul>
            </div>
          </div>
          
        </div>
      </div>
    </div>
    <div class="page-content">
      <div class="nav-bar">
        <div class="profile">
          <b-dropdown id="ddown-right" right variant="link" size="sm" no-caret>
            <template slot="button-content">
              <a href="">{{ profile.name }}</a>
              <img :src="profile.picture" alt="Avatar" class="avatar" onError="this.onerror=null;this.src='https://i.pinimg.com/236x/40/41/fa/4041faa0a8989e5787f9b164ca3b2650--occupational-therapist-physical-therapist.jpg';">
            </template>
            <b-dropdown-item @click.prevent="logout">Logout</b-dropdown-item>
          </b-dropdown>
        </div>
      </div>
      <div class="main-content">
        <div class="presentation">
          <div class="presentation-breadcrumb">

          </div>
          <table class="table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Owner</th>
                <th>Group</th>
                <th>Created at</th>
                <th>Options</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="file in files" :key="file.id">
                <td>{{ `${file.name}.${file.extension}` }}</td>
                <td>{{ file.user_id }}</td>
                <td>{{ file.group_id }}</td>
                <td>{{ file.created_at }}</td>
                <td>
                  <button class="btn btn-outline-dark btn-sm">...</button>
                </td>
              </tr>
              <tr>
                <td>Auth0.png</td>
                <td>me</td>
                <td>Con en</td>
                <td>5 mins ago</td>
                <td>
                  <button class="btn btn-outline-dark btn-sm">...</button>
                </td>
              </tr>
            </tbody>
          </table>
          <p v-show="msg">{{ msg }}</p>
        </div>
        <div class="info-banner">
          <button class="btn btn-sm">Upload</button>
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
      profile: this.$auth.profile,
      files: null,
      msg: null
    }
  },
  async created() {
    this.msg = null;
    const accessToken = await this.$auth.getAccessToken();

    try {
      // this.$http.get('http://localhost:8000/api/files', {
      //   headers: {
      //     Authorization: `Bearer ${accessToken}`
      //   }        
      // }).then(response => {
      //   this.files = response.body.data.data;
      // });

      const { data } = await this.$http.get('http://localhost:8000/api/files', {
        headers: {
          Authorization: `Bearer ${accessToken}`
        }        
      });
      
      this.files = data.files.data;
    } catch (e) {
      this.msg = `Error: the server responded with '${e}'`;
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
  flex: 0 0 20%;
  max-width: 420px;
  min-width: 240px;
  height: 100vh;
  background-color: #F7F9FA;
}

.sidebar-header,
.nav-bar {
  height: 66px;
}

.sidebar-wrapper .sidebar-header,
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

.sidebar-wrapper .sidebar {
  overflow-y: hidden;
  position: relative;
}

.sidebar-button,
.info-banner {
  padding: 20px 40px;
}

.sidebar-button button {
  width: 100%;
  font-weight: 600;
}

.sidebar-nav-item>a {
  font-size: 16px;
  line-height: 26px;
}

.group-list {
  padding-left: 24px;
}

.group-item {
  position: relative;
  display: flex;
}

.group-item a {
  font-size: 15px;
  line-height: 24px;
}

.page-content {
  flex: 3;
  height: 100vh;
}

.page-content .nav-bar {
  box-sizing: border-box;
  position: relative;
  z-index: 201;
}

.nav-bar .profile {
  position: absolute;
  right: 32px;
  top: 0;
  bottom: 0;
  display: flex;
}

.profile a {
  font-weight: 600;
  opacity: .84;
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
  padding-left: 40px;
}

.presentation-breadcrumb {
  height: 40px;
}

.main-content .info-banner {
  flex: 0 2 25%;
  max-width: 420px;
  min-width: 200px;
}

.info-banner button {
  width: 100%;
  background-color: #e76123;
  color: #fff;
  font-weight: 600;
}

.avatar {
  vertical-align: middle;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  margin-left: 10px;
}
</style>