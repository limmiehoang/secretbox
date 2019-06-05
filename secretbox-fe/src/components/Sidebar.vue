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
            <div>
              My groups
            </div>
            <div class="sidebar-group-list">
              <ul aria-label="Group list">
                <li class="sidebar-group-list-item new-group" v-for="group in newGroups" :key="group.id">
                  <router-link :to="`/group/${group.id}`">{{ group.name }}</router-link>
                </li>
                <li class="sidebar-group-list-item" v-for="group in joinedGroups" :key="group.id">
                  <router-link :to="`/group/${group.id}`">{{ group.name }}</router-link>
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

    }
  },
  computed: {
    joinedGroups () {
      return this.$store.state.joinedGroups
    },
    newGroups () {
      return this.$store.state.newGroups
    }
  },
  async created() {
    await this.$store.dispatch('loadGroups');
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

.new-group a {
  font-weight: 700;
}
</style>
