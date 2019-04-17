import auth0 from 'auth0-js';
import Auth0Lock from 'auth0-lock';
import EventEmitter from 'events';
import authConfig from '../../auth_config.json';

const webAuth = new auth0.WebAuth({
  domain: authConfig.domain,
  redirectUri: `${window.location.origin}/callback`,
  clientID: authConfig.clientId,
  audience: authConfig.audience,
  responseType: 'token id_token',
  scope: 'openid profile email'
});

var options = {
  auth: {
    audience: authConfig.audience,
    params: {
      scope: "openid profile email"
    },
    autoParseHash: false,
    redirectUrl: `${window.location.origin}/callback`,
    responseType: "token id_token",
    sso: true
  },
  allowedConnections: ['Username-Password-Authentication'],
  allowAutocomplete: true,
  allowShowPassword: true,
  allowForgotPassword: false,
  autoclose: true,
  theme: {
    logo: 'https://i.ibb.co/7rRHQWJ/logo.png',
    primaryColor: '#e76123'
  },
  languageDictionary: {
    title: "SecretBox"
  },
  avatar: null
};

const lock = new Auth0Lock(
  authConfig.clientId,
  authConfig.domain,
  options
);

const localStorageKey = 'loggedIn';
const loginEvent = 'loginEvent';

class AuthService extends EventEmitter {
  idToken = null;
  profile = null;
  tokenExpiry = null;

  accessToken = null;
  accessTokenExpiry = null;

  // Starts the user login flow
  login(customState) {
    webAuth.authorize({
      appState: customState
    });
  }
  
  lockLogin(customState) {
    lock.checkSession({}, (err, authResult) => {
      if (err) {
        if (!customState) {
          customState = {
            target: "/home"
          }
        }
        lock.show({
          auth: {
            params: {
              appState: customState
            }
          }
        });
        return;
      }
      authResult.appState = customState;
      this.localLogin(authResult);
    });
    
    lock.on("authenticated", authResult => {
      this.localLogin(authResult);
    });
  }

  // Handles the callback request from Auth0
  handleAuthentication() {
    return new Promise((resolve, reject) => {
      lock.resumeAuth(window.location.hash, (err, authResult) => {
        if (err) {
          alert("Could not parse hash");
          reject(err);
        } else {
          this.localLogin(authResult);
          resolve(authResult.idToken);
        }
      });
    });
  }

  localLogin(authResult) {
    this.idToken = authResult.idToken;
    this.profile = authResult.idTokenPayload;

    // Convert the JWT expiry time from seconds to milliseconds
    this.tokenExpiry = new Date(this.profile.exp * 1000);

    this.accessToken = authResult.accessToken;
    this.accessTokenExpiry = new Date(Date.now() + authResult.expiresIn * 1000);

    localStorage.setItem(localStorageKey, 'true');
    localStorage.setItem("accessToken", this.accessToken);

    this.emit(loginEvent, {
      loggedIn: true,
      profile: authResult.idTokenPayload,
      state: authResult.appState || {}
    });
  }

  renewTokens() {
    return new Promise((resolve, reject) => {
      if (localStorage.getItem(localStorageKey) !== "true") {
        return reject("Not logged in");
      }

      lock.checkSession({}, (err, authResult) => {
        if (err) {
          reject(err);
        } else {
          this.localLogin(authResult);
          resolve(authResult);
        }
      });
    });
  }

  logOut() {
    localStorage.removeItem(localStorageKey);
    localStorage.removeItem("accessToken");

    this.idToken = null;
    this.tokenExpiry = null;
    this.profile = null;

    lock.logout({
      returnTo: window.location.origin
    });

    this.emit(loginEvent, { loggedIn: false });
  }

  isAuthenticated() {
    return (
      Date.now() < this.tokenExpiry &&
      localStorage.getItem(localStorageKey) == 'true'
    );
  }

  isAccessTokenValid() {
    return (
      this.accessToken &&
      this.accessTokenExpiry &&
      Date.now() < this.accessTokenExpiry
    );
  }

  getAccessToken() {
    return new Promise((resolve, reject) => {
      if (this.isAccessTokenValid()) {
        resolve(this.accessToken);
      } else {
        this.renewTokens().then(authResult => {
          resolve(authResult.accessToken);
        }, reject);
      }
    });
  }
}

export default new AuthService();