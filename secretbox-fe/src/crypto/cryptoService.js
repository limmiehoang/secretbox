const b64 = require('base64-js');

function arrayToB64(array) {
  return b64
    .fromByteArray(array)
    .replace(/\+/g, '-')
    .replace(/\//g, '_')
    .replace(/=/g, '');
}

function exportPublicKey(key) {
  return new Promise((resolve, reject) => {
    window.crypto.subtle.exportKey(
      "spki",
      key
    ).then(exported => {
      resolve(arrayToB64(new Uint8Array(exported)));
    }, error => {
      reject(error);
    })
  });
}

function exportPrivateKey(key) {
  return new Promise((resolve, reject) => {
    window.crypto.subtle.exportKey(
      "pkcs8",
      key
    ).then(exported => {
      resolve(arrayToB64(new Uint8Array(exported)));
    }, error => {
      reject(error);
    })
  });
}

class CryptoService {
  async generateIdentityKey() {
    try {
      let pair = await window.crypto.subtle.generateKey(
        {
          name: "ECDH",
          namedCurve: "P-384"
        },
        true,
        ["deriveKey"]
      );
      // let privateKey = await exportPrivateKey(pair.privateKey);
      // @TODO: save privateKey to local
      return await exportPublicKey(pair.publicKey);
    }
    catch(e) {
      throw e;
    }
  }
  generateSecretKey() {
    var rawSecret = crypto.getRandomValues(new Uint8Array(32));
    return arrayToB64(rawSecret);
  }
}

export default new CryptoService();