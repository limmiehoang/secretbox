import cryptoService from '../crypto/cryptoService.js';

export default {
  install(Vue) {
    Vue.prototype.$crypto = cryptoService;
  }
};