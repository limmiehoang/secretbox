const b64 = require('base64-js');
const encoder = new TextEncoder();
const decoder = new TextDecoder();

function arrayToB64(array) {
  return b64
    .fromByteArray(array)
    .replace(/\+/g, '-')
    .replace(/\//g, '_')
    .replace(/=/g, '');
}

function b64ToArray(str) {
  return b64.toByteArray(str + '==='.slice((str.length + 3) % 4));
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

function importPublicKey(keyB64) {
  return window.crypto.subtle.importKey(
    "spki",
    b64ToArray(keyB64),
    {
      name: "ECDH",
      namedCurve: "P-384"
    },
    false,
    []
  );
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

function importPrivateKey(keyB64) {
  return window.crypto.subtle.importKey(
    "pkcs8",
    b64ToArray(keyB64),
    {
      name: "ECDH",
      namedCurve: "P-384"
    },
    false,
    ["deriveKey"]
  );
}

function importKeyForHKDF(rawKey) {
  return window.crypto.subtle.importKey(
    'raw',
    rawKey,
    'HKDF',
    false,
    ['deriveKey']
  )
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
      return {
        privateKey: await exportPrivateKey(pair.privateKey),
        publicKey: await exportPublicKey(pair.publicKey)
      }
    }
    catch(e) {
      throw e;
    }
  }
  generateSecretKey() {
    var rawSecret = crypto.getRandomValues(new Uint8Array(32));
    return arrayToB64(rawSecret);
  }
  async generateMetaKey(secretKeyB64) {
    var secretKey = await importKeyForHKDF(b64ToArray(secretKeyB64));
    return await crypto.subtle.deriveKey(
      {
        name: 'HKDF',
        salt: new Uint8Array(),
        info: encoder.encode('metadata'),
        hash: 'SHA-256'
      },
      secretKey,
      {
        name: 'AES-GCM',
        length: 256
      },
      false,
      ['encrypt', 'decrypt']
    );
  }
  async generateContentKey(secretKeyB64) {
    var secretKey = await importKeyForHKDF(b64ToArray(secretKeyB64));
    return await crypto.subtle.deriveKey(
      {
        name: 'HKDF',
        salt: new Uint8Array(),
        info: encoder.encode('Content-Encoding: aes256gcm\0'),
        hash: 'SHA-256'
      },
      secretKey,
      {
        name: 'AES-GCM',
        length: 256
      },
      false,
      ['encrypt', 'decrypt']
    );
  }
  async encryptMetadata(metadata, secretKeyB64) {
    let metaKey = await this.generateMetaKey(secretKeyB64);
    const ciphertext = await crypto.subtle.encrypt(
      {
        name: 'AES-GCM',
        iv: new Uint8Array(12),
        tagLength: 128
      },
      metaKey,
      encoder.encode(
        JSON.stringify(metadata)
      )
    );
    return arrayToB64(new Uint8Array(ciphertext));
  }
  async decryptMetadata(ciphertext, secretKeyB64) {
    let metaKey = await this.generateMetaKey(secretKeyB64);
    const plaintext = await crypto.subtle.decrypt(
      {
        name: 'AES-GCM',
        iv: new Uint8Array(12),
        tagLength: 128
      },
      metaKey,
      b64ToArray(ciphertext)
    );
    return JSON.parse(decoder.decode(plaintext));
  }
  async encryptKey(keyB64, pairwiseSharedKey) {
    const ciphertext = await crypto.subtle.encrypt(
      {
        name: 'AES-GCM',
        iv: new Uint8Array(12),
        tagLength: 128
      },
      pairwiseSharedKey,
      b64ToArray(keyB64)
    );
    return arrayToB64(new Uint8Array(ciphertext))
  }
  async decryptKey(ciphertext, pairwiseSharedKey) {
    const plaintext = await crypto.subtle.decrypt(
      {
        name: 'AES-GCM',
        iv: new Uint8Array(12),
        tagLength: 128
      },
      pairwiseSharedKey,
      b64ToArray(ciphertext)
    );
    return arrayToB64(new Uint8Array(plaintext));
  }
  async encryptFileContent(plaintext, secretKeyB64) {
    try {
      let secretKey = await this.generateContentKey(secretKeyB64);
      const ciphertext = await crypto.subtle.encrypt(
        {
          name: 'AES-GCM',
          iv: new Uint8Array(12),
          tagLength: 128
        },
        secretKey,
        plaintext
      );
      return ciphertext;
    } catch(e) {
      throw e;
    }
  }
  async decryptFileContent(ciphertext, secretKeyB64) {
    let secretKey = await this.generateContentKey(secretKeyB64);
    const plaintext = await crypto.subtle.decrypt(
      {
        name: 'AES-GCM',
        iv: new Uint8Array(12),
        tagLength: 128
      },
      secretKey,
      ciphertext
    );
    return plaintext;
  }
  async deriveSharedSecretKey(privKeyB64, pubKeyB64) {
    let privKey = await importPrivateKey(privKeyB64);
    let pubKey = await importPublicKey(pubKeyB64);
    
    return await crypto.subtle.deriveKey(
      {
        name: "ECDH",
        namedCurve: "P-384",
        public: pubKey
      },
      privKey,
      {
        name: "AES-GCM",
        length: 256
      },
      false,
      ["encrypt", "decrypt"]
    );
  }
}

export default new CryptoService();