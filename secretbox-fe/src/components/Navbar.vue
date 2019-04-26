<template>
  <div id="navbar">
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
</template>

<script>
export default {
  props: {
    profile: {
      type: Object
    }
  },
  methods: {
    logout() {
      this.$auth.logOut();
    }
  }
}
</script>

<style>
#navbar {
  box-sizing: border-box;
  position: relative;
  z-index: 201;
  display: flex;
  align-items: center;
}

.profile {
  position: absolute;
  right: 32px;
}

.profile a {
  font-weight: 600;
  opacity: .84;
}

.avatar {
  vertical-align: middle;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  margin-left: 10px;
}
</style>
