import firebase from 'firebase'
var config = {
  apiKey: 'AIzaSyAE1Fd58Ob5W3eDGemNMckASmjs2eA5JCw',
  authDomain: 'gnosoft-pae.firebaseapp.com',
  databaseURL: 'https://gnosoft-pae.firebaseio.com',
  projectId: 'gnosoft-pae',
  storageBucket: 'gnosoft-pae.appspot.com',
  messagingSenderId: '240177487942'
}

firebase.initializeApp(config)

export default {
  messaging: firebase.messaging()
}
