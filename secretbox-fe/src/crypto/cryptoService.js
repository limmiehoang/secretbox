class CryptoService {
  ab2str(buf) {
    return String.fromCharCode.apply(null, new Uint8Array(buf));
  }
  async generateIdentityKey() {
    var pair = await window.crypto.subtle.generateKey(
      {
        name: "ECDH",
        namedCurve: "P-384"
      },
      true,
      ["deriveKey"]
    );
    let result = await crypto.subtle.exportKey(
      'spki',
      pair.publicKey
    );
    const exportedAsString = this.ab2str(result);
    return window.btoa(exportedAsString);
  }
}

export default new CryptoService();