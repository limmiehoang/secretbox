import wsService from '../ws/wsService.js';

export default {
  install(Vue) {
    Vue.prototype.$ws = wsService;
  }
};