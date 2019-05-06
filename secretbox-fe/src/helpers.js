export default {
  getIdentityKey(profile) {
    let userMetadataKey = Object.keys(profile).find(a => a.includes("user_metadata"));
    var userMetadata = profile[userMetadataKey];
    return userMetadata ? userMetadata.identity_key : null;
  }
};