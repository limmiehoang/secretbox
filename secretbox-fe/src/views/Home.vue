<template>
  <div class="home">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <a href="" class="navbar-brand">SecretBox</a>
      <b-dropdown id="ddown-left" variant="link" size="sm" no-caret>
          <template slot="button-content">
            <img :src="avatar" alt="Avatar" class="avatar">
            <a>{{ full_name }}</a>
          </template>
          <b-dropdown-item @click.prevent="logout">Logout</b-dropdown-item>
        </b-dropdown>
    </nav>
  </div>
</template>

<script>
export default {
  name: "home",
  data() {
    return {
      avatar: "https://dl-web.dropbox.com/account_photo/get/dbaphid%3AAAAM-kT2NHvmSBOBwtHzG_iEfGxi8JH6Juw?circle_crop=1&size=128x128&vers=1465988420421",
      full_name: "Khanh Hoang"
    }
  },
  methods: {
    logout() {
      this.$auth.logOut();
    }
  }
};
</script>

<style scoped>

</style>