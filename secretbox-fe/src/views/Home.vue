<template>
  <div class="home">
    <Sidebar />
    <div class="page-content">
      <Navbar :profile="profile"/>
      <!-- <GroupDetail /> -->
      <GroupCreate />
    </div>
  </div>
</template>

<script>
import Vue from "vue";
import Sidebar from "@/components/Sidebar.vue";
import Navbar from "@/components/Navbar.vue";
import GroupDetail from "@/components/GroupDetail.vue";
import GroupCreate from "@/components/GroupCreate.vue";

export default {
  name: "home",
  components: {
    Sidebar,
    Navbar,
    GroupDetail,
    GroupCreate
  },
  data() {
    return {
      profile: this.$auth.profile,
      files: [],
      messages: []
    }
  },
  async created() {
    this.msg = null;
    const accessToken = await this.$auth.getAccessToken();
    Vue.http.headers.common['Authorization'] = `Bearer ${accessToken}`;

    this.$http
      .get('api/files')
      .then(response => {
        this.files = response.body.files.data;
      },
      response => {
        this.messages.push(response.body.message);
      });
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

<style>
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