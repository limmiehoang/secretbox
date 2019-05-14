import Auth0Lock from 'auth0-lock';
import auth0 from 'auth0-js';
import EventEmitter from 'events';
import authConfig from '../../auth_config.json';
import cryptoService from '../crypto/cryptoService.js';

var options = {
  auth: {
    audience: authConfig.customAPIAudience,
    params: {
      scope: "openid profile email"
    },
    autoParseHash: true,
    redirectUrl: `${window.location.origin}/home`,
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

var lock = new Auth0Lock(
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

  lockLogin(customState) {
    this.checkSession().then(
      authResult => {
        authResult.appState = customState;
        this.localLogin(authResult);
      }
    ).catch(
      err => {
        console.log(err);
        lock.show();
      }
    )
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

      this.checkSession().then(
        authResult => {
          this.localLogin(authResult);
          resolve(authResult);
        }
      ).catch(
        err => {
          reject(err);
        }
      )
    });
  }

  checkSession() {
    return new Promise((resolve, reject) => {
      lock.checkSession({}, async (err, authResult) => {
        if (err) {
          localStorage.removeItem(localStorageKey);
          localStorage.removeItem("accessToken");
          return reject(err);
        }
        let idTokenPayload = authResult.idTokenPayload;
        let userMetadataKey = Object.keys(idTokenPayload).find(a => a.includes("user_metadata"));
        var userMetadata = authResult.idTokenPayload[userMetadataKey];
        
        if (!userMetadata || !(userMetadata.identity_key)) {
          lock.checkSession({
            audience: authConfig.userManagementAPIAudience,
            scope: "update:current_user_metadata"
          }, async (err, result) => {
            if (!err) {
              var identityKeyPair = await cryptoService.generateIdentityKey();
              var auth0Manage = new auth0.Management({
                domain: authConfig.domain,
                token: result.accessToken
              });
              var userId = result.idTokenPayload.sub;
              auth0Manage.patchUserMetadata(userId, {
                identity_key: identityKeyPair.publicKey
              }, (err, _) => {
                if (err) {
                  console.log(err);
                  return;
                }
                localStorage.setItem(userId, identityKeyPair.privateKey);
              });
            }
          });
        }
        resolve(authResult);
      });
    })
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

  getIdentityPublicKey() {
    let userMetadataKey = Object.keys(this.profile).find(a => a.includes("user_metadata"));
    var userMetadata = this.profile[userMetadataKey];
    return userMetadata ? userMetadata.identity_key : null;
  }

  getUserId() {
    return this.profile.sub;
  }

  getName() {
    return this.profile.name;
  }
}

export default new AuthService();