<template>
  <div id="group-create" class="main-content">
    <div class="presentation">
      <div class="presentation-breadcrumb">
        <h5>Create new group</h5>
      </div>
      <div class="form-group row">
        <label for="groupName" class="col-sm-2 col-form-label">Group name</label>
        <div class="col-sm-10">
          <input
            type="text"
            name="group_name"
            id="groupName"
            class="form-control"
            placeholder="Enter your group name"
          >
        </div>
      </div>
      <div class="form-group row">
        <label class="col-sm-2 col-form-label">People</label>
        <div class="col-sm-10">
          <button class="btn btn-light" v-b-modal.addPeopleModal>Add more people</button>
          <b-modal id="addPeopleModal" title="Add more people" ref="addPeopleModal" hide-footer>
            <b-form-group>
              <b-form-checkbox-group v-model="selectedUsers">
                <b-form-checkbox
                  v-for="user in otherPeople"
                  :key="user.user_id"
                  :value="user"
                >{{ user.email }}</b-form-checkbox>
              </b-form-checkbox-group>
            </b-form-group>
            <div class="row justify-content-center">
              <button class="btn btn-light" @click.prevent="handleReset">Reset</button>
              <button class="btn btn-primary" @click.prevent="handleOk">OK</button>
            </div>
          </b-modal>
        </div>
      </div>
      <div class="form-group row">
        <label for="initialFile" class="col-sm-2 col-form-label">Initial data</label>
        <div class="col-sm-10">
          <div class="custom-file">
            <input
              type="file"
              class="custom-file-input"
              id="initialFile"
              @change="handleFileChange"
            >
            <label class="custom-file-label" for="initialFile">Choose file</label>
          </div>
          <small v-if="initialFile">{{ fileSize }}</small>
        </div>
      </div>
      <div class="row justify-content-center">
        <button class="btn btn-light" @click.prevent="cancel">Cancel</button>
        <button class="btn btn-primary" @click.prevent="handleCreate">Create</button>
      </div>
    </div>
    <div class="info-banner"></div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      otherPeople: [],
      selectedUsers: [],
      initialFile: null
    };
  },
  computed: {
    fileSize() {
      return `${this.initialFile.size} bytes`;
    }
  },
  async created() {
    this.resetComponent();
    const response = await this.$http.post(
      "https://limmie.auth0.com/oauth/token",
      {
        client_id: "6bnWfXmVr7PbQLX906kPQqVbOzP3IZUD",
        client_secret:
          "vz4qHn8AeaYtfDwbe14Xi_suMd-jNnveume_aLGzLFwzxEl-DMlGYcgqIucXG91p",
        audience: "https://limmie.auth0.com/api/v2/",
        grant_type: "client_credentials"
      },
      { emulateJSON: true }
    );
    const accessToken = response.body.access_token;
    this.$http
      .get("https://limmie.auth0.com/api/v2/users", {
        headers: {
          Authorization: `Bearer ${accessToken}`
        }
      })
      .then(response => {
        this.otherPeople = response.body;
        this.otherPeople.shift();
      });
  },
  methods: {
    resetComponent() {
      this.selectedUsers = [];
    },
    handleFileChange(e) {
      var $this = e.target;

      var fileName = $this.value.split("\\").pop();
      $this.nextElementSibling.classList.add("selected");
      $this.nextElementSibling.innerHTML = fileName;

      const stream = $this.files[0];
      if (stream) {
        this.initialFile = stream;
      }
    },
    handleReset() {
      this.selectedUsers = [];
    },
    handleOk() {
      this.$nextTick(() => {
        this.$refs.addPeopleModal.hide();
      });
    },
    async handleCreate() {
      console.time("crypto");

      let users = this.selectedUsers.map(a => a.user_id);
      let encKeys = [];
      
      let metadata = {
        name: this.initialFile.name,
        size: this.initialFile.size,
        type: this.initialFile.type || 'application/octet-stream'
      };

      let identityKey = this.$helpers.getIdentityKey(this.$auth.profile);
      let secretKey = await this.$crypto.generateSecretKey();
      let encMetadata = await this.$crypto.encryptMetadata(metadata, secretKey);

      let privateKey = localStorage.getItem(this.$auth.profile.sub);
      console.log(secretKey);

      for (let user of this.selectedUsers) {
        let publicKey = user.user_metadata.identity_key;
        let sharedSecretKey = await this.$crypto.deriveSharedSecretKey(privateKey, publicKey);
        let encKey = await this.$crypto.encryptKey(secretKey, sharedSecretKey);
        encKeys.push(encKey);
      };
      
      const reader = new FileReader();
      reader.onload = async () => {
        let fileContent = reader.result;
        let encFileContent = await this.$crypto.encryptFileContent(fileContent, secretKey);
        console.timeEnd("crypto");
      };
      reader.readAsArrayBuffer(this.initialFile);
    }
  }
};
</script>

<style>
</style>