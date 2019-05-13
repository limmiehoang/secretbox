<template>
  <div id="group-create" class="main-content">
    <div class="presentation">
      <div class="presentation-breadcrumb">
        <h5>Create new group</h5>
      </div>
      <div class="alert alert-danger" v-if="error_msg">{{ error_msg }}</div>
      <div class="form-group row">
        <label for="groupName" class="col-sm-2 col-form-label">Group name*</label>
        <div class="col-sm-10">
          <input
            type="text"
            name="group_name"
            id="groupName"
            class="form-control"
            placeholder="Enter your group name"
            v-model="groupName"
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
        <label for="initialFile" class="col-sm-2 col-form-label">Initial data*</label>
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
import Resumable from "resumablejs";

export default {
  data() {
    return {
      groupName: "",
      otherPeople: [],
      selectedUsers: [],
      initialFile: null,
      error_msg: ""
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
        const users = response.body;
        this.otherPeople = this.getOtherPeople(users, this.$auth.getUserId());
      });
  },
  methods: {
    getOtherPeople(allUsers, myUserId) {
      return allUsers.filter(user => {
        return user.user_id != myUserId;
      });
    },
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
    fileSuccess(resumable) {
      return new Promise(resolve => {
        resumable.on("fileSuccess", (file, message) => {
          resolve(file);
        });
      });
    },
    async handleCreate() {
      this.error_msg = "";

      if (!this.groupName) {
        this.error_msg = "Please enter group name.";
        return;
      }

      if (!this.initialFile) {
        this.error_msg = "Please add initial data.";
        return;
      }

      console.time("encrypt keys and metadata");

      let formData = new FormData();

      let metadata = {
        name: this.initialFile.name,
        size: this.initialFile.size,
        type: this.initialFile.type || "application/octet-stream"
      };

      let identityKey = this.$auth.getIdentityPublicKey();
      let secretKey = await this.$crypto.generateSecretKey();
      let encMetadata = await this.$crypto.encryptMetadata(metadata, secretKey);

      formData.append("name", this.groupName);
      formData.append("encMetadata", encMetadata);
      formData.append("identityKey", identityKey); // @TODO: this should be obtained by server side

      let privateKey = localStorage.getItem(this.$auth.profile.sub);

      for (let user of this.selectedUsers) {
        let publicKey = user.user_metadata.identity_key;
        let sharedSecretKey = await this.$crypto.deriveSharedSecretKey(
          privateKey,
          publicKey
        );
        let encKey = await this.$crypto.encryptKey(secretKey, sharedSecretKey);
        formData.append("users[]", user.user_id);
        formData.append("encKeys[]", encKey);
      }

      console.timeEnd("encrypt keys and metadata");

      const accessToken = await this.$auth.getAccessToken();

      var initialDataId, groupId;

      try {
        const response = await this.$http.post("api/groups", formData, {
          headers: {
            Authorization: `Bearer ${accessToken}`,
            "Content-Type": "multipart/form-data"
          }
        });

        initialDataId = response.body.data.initial_data.id;
        groupId = response.body.data.id;
      }
      catch(e) {
        this.error_msg = e.body.message;
        return;
      }

      const reader = new FileReader();
      reader.onload = async () => {
        let fileContent = reader.result;
        console.time("encrypt file content");
        let encFileContent = await this.$crypto.encryptFileContent(
          fileContent,
          secretKey
        );
        console.timeEnd("encrypt file content");

        var resumable = new Resumable({
          chunkSize: 2 * 1024 * 1024, // 1MB
          simultaneousUploads: 1,
          testChunks: false,
          throttleProgressCallbacks: 1,
          // Get the url from data-url tag
          target: "http://localhost:8000/api/upload",
          // Append token to the request - required for web routes
          query: { _token: accessToken }
        });

        var file = new File(
          [new Blob([new Uint8Array(encFileContent)])],
          `${initialDataId}`,
          {
            type: "override/mimetype"
          }
        );

        // Handle file add event
        resumable.on("fileAdded", function(file) {
          // Actually start the upload
          resumable.upload();
        });
        resumable.on("fileError", function(file, message) {
          console.log("file could not be uploaded");
        });
        resumable.on("fileProgress", function(file) {
          console.log(Math.floor(resumable.progress() * 100) + "%");
        });
        
        console.time("sending");
        resumable.addFile(file);

        const file = await this.fileSuccess(resumable);
        console.log("completed uploading " + file.uniqueIdentifier);
        console.timeEnd("sending");

        localStorage.setItem(groupId, secretKey);
        this.$store.dispatch("loadGroups");
      };
      reader.readAsArrayBuffer(this.initialFile);
    }
  }
};
</script>

<style>
</style>
