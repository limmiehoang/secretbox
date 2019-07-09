<template>
  <div id="group-detail">
    <div class="main-content" v-if="groupInfo">
      <div class="presentation">
        <div class="presentation-breadcrumb">
          <h5>Group: {{ groupInfo.name }}</h5>
        </div>
        <table class="table" v-if="!uploadFile">
          <thead>
            <tr>
              <th>Name</th>
              <th>Owner</th>
              <th>Size</th>
              <th>Options</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="file in files" :key="file.id">
              <td>{{ file.name }}</td>
              <td>{{ owner(file.owner) }}</td>
              <td>{{ size(file.size) }}</td>
              <td>
                <button
                  class="btn btn-outline-dark btn-sm"
                  @click.prevent="handleDownload(file)"
                  v-show="!isLoading(file.id)"
                >Download</button>
                <div class="spinner-border" role="status" v-show="isLoading(file.id)">
                  <span class="sr-only">Loading...</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
        <div v-if="uploadFile">
          <p>{{ uploadFile.name }}</p>
          <p>{{ size(uploadFile.size) }}</p>
          <button class="btn btn-outline-dark btn-sm" @click.prevent="handleUpload()">Upload</button>
        </div>
      </div>
      <div class="info-banner" v-if="!uploadFile">
        <input type="file" ref="file" style="display: none" @change="handleFileChoose">
        <button class="btn btn-primary btn-sm" @click="$refs.file.click()">Upload</button>
      </div>
    </div>
    <div class="main-content" v-show="!groupInfo">
      <div class="presentation">Loading group info...</div>
    </div>
  </div>
</template>

<script>
import Resumable from "resumablejs";
const download = require("downloadjs");

export default {
  data() {
    return {
      files: [],
      groupInfo: null,
      loadingFile: "",
      uploadFile: null
    };
  },
  async mounted() {
    await this.loadFiles();
  },
  methods: {
    async checkForSecretKey() {
      if (localStorage.getItem(this.groupInfo.id)) {
        return;
      }
      console.log("no secret key");
      
      let privKey = localStorage.getItem(this.$auth.getUserId());
      console.log("privKey: " + privKey);
      
      let pubKey = this.groupInfo.identity_key;
      console.log("pubKey: " + pubKey);
      
      let sharedKey = await this.$crypto.deriveSharedSecretKey(privKey, pubKey);
      console.log(sharedKey);
      let ciphertext = this.groupInfo.key_info.enc_key;
      console.log("ciphertext: " + ciphertext);
      
      let secretKey = await this.$crypto.decryptKey(ciphertext, sharedKey);
      console.log("secretKey: " + secretKey);
      localStorage.setItem(this.groupInfo.id, secretKey);
      secretKey = sharedKey = privKey = null;
    },
    resetComponent() {
      this.files = [];
      this.groupInfo = null;
      this.loadingFile = "";
      this.uploadFile = null;
    },
    async loadFiles() {
      try {
        this.files = [];
        const accessToken = await this.$auth.getAccessToken();

        this.$http
          .get(`api/groups/${this.$route.params.id}`, {
            headers: {
              Authorization: `Bearer ${accessToken}`
            }
          })
          .then(async response => {
            if (response.body.success) {
              this.groupInfo = response.body.data;
              await this.checkForSecretKey();
              for (let encFile of this.groupInfo.enc_files) {
                let metadata = await this.$crypto.decryptMetadata(
                  encFile.enc_metadata,
                  localStorage.getItem(this.groupInfo.id)
                );
                metadata.id = encFile.id;
                this.files.push(metadata);
              }
            }
          });
      } catch (e) {
        console.log(e);
      }
    },
    owner(owner) {
      if (owner.id == this.$auth.getUserId()) {
        return "me";
      }
      return owner.name;
    },
    size(size) {
      // return `${size} bytes`;
      return this.$helpers.bytesToSize(size);
    },
    isLoading(fileId) {
      return fileId == this.loadingFile;
    },
    async handleDownload(file) {
      try {
        this.loadingFile = file.id;

        const accessToken = await this.$auth.getAccessToken();

        this.$http
          .get(`api/download/${file.id}`, {
            responseType: "arraybuffer",
            headers: {
              Authorization: `Bearer ${accessToken}`
            }
          })
          .then(async response => {
            const ciphertext = response.body;
            this.checkForSecretKey();
            const plaintext = await this.$crypto.decryptFileContent(
              ciphertext,
              localStorage.getItem(this.groupInfo.id)
            );
            this.loadingFile = "";
            download(plaintext, file.name, file.type);
          }, err => {
            this.loadingFile = "";
          });
      } catch (e) {
        this.loadingFile = "";
        console.log(e);
      }
    },
    handleFileChoose(e) {
      var $this = e.target;

      const stream = $this.files[0];
      if (stream) {
        this.uploadFile = stream;
      }
    },
    fileSuccess(resumable) {
      return new Promise(resolve => {
        resumable.on("fileSuccess", (file, message) => {
          resolve(file);
        });
      });
    },
    async handleUpload() {
      let formData = new FormData();

      let metadata = {
        name: this.uploadFile.name,
        size: this.uploadFile.size,
        type: this.uploadFile.type || "application/octet-stream",
        owner: {
          id: this.$auth.getUserId(),
          name: this.$auth.getName()
        }
      };

      this.checkForSecretKey();

      let encMetadata = await this.$crypto.encryptMetadata(
        metadata,
        localStorage.getItem(this.groupInfo.id)
      );

      formData.append("encMetadata", encMetadata);
      formData.append("groupId", this.groupInfo.id);

      const accessToken = await this.$auth.getAccessToken();

      var fileId;

      try {
        const response = await this.$http.post("api/enc-files", formData, {
          headers: {
            Authorization: `Bearer ${accessToken}`,
            "Content-Type": "multipart/form-data"
          }
        });

        fileId = response.body.data.id;
      } catch (e) {
        console.log(e);
        return;
      }

      const reader = new FileReader();
      reader.onload = async () => {
        let fileContent = reader.result;
        console.time("encrypt file content");
        let encFileContent = await this.$crypto.encryptFileContent(
          fileContent,
          localStorage.getItem(this.groupInfo.id)
        );
        console.timeEnd("encrypt file content");

        var resumable = new Resumable({
          chunkSize: 2 * 1024 * 1024, // 1MB
          simultaneousUploads: 1,
          testChunks: false,
          throttleProgressCallbacks: 1,
          // Get the url from data-url tag
          target: "https://be-secretbox.io/api/upload",
          // Append token to the request - required for web routes
          query: { _token: accessToken }
        });

        var uploadData = new File(
          [new Blob([new Uint8Array(encFileContent)])],
          `${fileId}`,
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
        resumable.addFile(uploadData);

        const file = await this.fileSuccess(resumable);
        console.log("completed uploading " + file.uniqueIdentifier);
        console.timeEnd("sending");

        this.uploadFile = null;
        this.loadFiles();
      };
      reader.readAsArrayBuffer(this.uploadFile);
    }
  }
};
</script>

<style>
</style>
