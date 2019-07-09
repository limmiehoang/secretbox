import helpers from '../helpers.js';

export default {
  install(Vue) {
    Vue.prototype.$helpers = helpers;
  }
};