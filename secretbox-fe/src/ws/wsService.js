export default {
  initWebSocket(url) {
    return new Promise(resolve => {
      const conn = new WebSocket(url);
      conn.onopen = () => {
        resolve(conn);
      };
    });
  },
  listenForResponse(conn) {
    return new Promise((resolve) => {
      conn.addEventListener(
        'message',
        msg => {
          resolve(msg.data)
        }, {
          once: true
        }
      );
    })
  }
}