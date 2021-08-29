import axios from 'axios'
import miniToastr from 'mini-toastr'
import GLoader from '../components/GLoader'

const GConfig = {
  install: function (Vue, router) {
    // console.log('Modificado 03')
    // console.log('Router: ' + process.env.BASE_URL)
    // window.ENVI = process.env
    this.Vue = Vue
    this.router = router
    this.forceRoute = false
    // var self = this
    /* router.beforeEach((to, from, next) => {
      if (self.forceRoute) {
        self.forceRoute = false
        next(to)
      } else {
        next()
      }
    }) */

    Vue.gconfig = {
      forceRoute: false,
      _safeIdCount: 1,
      emulateJSON: true,
      safeId: function (ident) {
        ident = ident ? ident + '_' : ''
        return '_gs_' + ident + (this._safeIdCount++)
      },
      continueRoute: function (next) {
        this.forceRoute = false
        next()
      }
    }

    Vue.prototype.$gconfig = Vue.gconfig

    // configuracion de la conexión al server
    this.initHttp(Vue)
    this.initToast(Vue)
    this.initFilters(Vue)
    this.initLoader(Vue)
  },

  initHttp: function (Vue) {
    let config = {
      baseURL: process.env.VUE_APP_BASE_URL,
      headers: []
    }
    Vue.http = axios.create(config)
    Vue.http.config = config

    Vue.http.newSourceCancelToken = function () {
      return axios.CancelToken.source()
    }
    this.verificarToken()
    // this.loadTokens(Vue)
    // this.initializeInterceptors(Vue)

    Vue.prototype.$http = Vue.http
  },
  verificarToken: function () {
    var self = this
    let token = window.localStorage.getItem('token')
    let path = this.$route
    if (token) {
      self.router.push({path: path})
    } else {
      self.router.push({path: '/error'})
    }
  },

  initToast: function (Vue) {
    // inicializar
    miniToastr.init()

    // configurar iconos
    /*
    miniToastr.setIcon('error', 'i', {'class': 'fa fa-warning'})
    miniToastr.setIcon('info', 'i', {'class': 'fa fa-info-circle'})
    miniToastr.setIcon('success', 'i', {'class': 'fa fa-check-circle-o'})
    */
    miniToastr.setIcon('error', 'i', {'class': 'icon-close font22'})
    miniToastr.setIcon('info', 'i', {'class': 'icon-info font22'})
    miniToastr.setIcon('success', 'i', {'class': 'icon-check font22'})
    miniToastr.setIcon('warning', 'i', {'class': 'icon-exclamation font22'})

    // asignar a vue
    Vue.toast = miniToastr
    Vue.prototype.$toast = Vue.toast
  },

  initFilters: function (Vue) {
    Vue.filter('fileSize', this.fileSizeFilter)
  },

  fileSizeFilter: function (value) {
    value = typeof value === 'number' ? value : 0
    var units = ['B', 'KB', 'MB', 'GB', 'TB']
    var pos = 0
    while (value > 1024 && pos < (units.length - 1)) {
      value = value / 1024
      pos++
    }
    return value.toFixed(1) + ' ' + units[pos]
  },

  loadTokens: function (Vue) {
    console.log('loadTokens')
    var self = this
    try {
      this.getTokens(Vue).then(resp => {
        self.router.push({path: 'home'})
      }).catch(err => {
        if (err) {
          self.router.push({path: 'login'})
        }
      })
    } catch (err) {
      alert(err)
      self.router.push({path: 'login'})
    }
  },

  loadTokensOld: function (Vue) {
    var self = this
    try {
      this.getTokens(Vue).then(resp => {
      //this.refreshTokens(Vue).then(resp => {
        self.router.push({path: 'home'})
      }).catch(err => {
        if (err) {
          self.router.push({path: 'login'})
        }
      })
    } catch (err) {
      self.router.push({path: 'login'})
    }
  },

  getTokens: function (Vue) {
    console.log('getTokens')
    // let params = process.env.TOKEN_AUTENTICATE
    let token_json = window.localStorage.getItem('token')
    let frm = {
      token: token_json
    }
    // console.log('Params: get-tokens?' + params)
    return Vue.http.post('/ws/inicio/validar_token.php', frm).then(resp => {
      // console.log('getTokens: ' + JSON.stringify(resp.data))
      window.sesionStorage.setItem('token', resp.data.token)
      return resp
    }).catch(err => {
      console.log('Fallo obteniendo tokens')
      throw err
    })
  },

  refreshTokens: function (Vue) {
    console.log('refreshTokens')
    let refreshToken = window.localStorage.getItem('refreshToken')
    if (refreshToken) {
      let frm = {
        rt: refreshToken
      }
      return Vue.http.post('ws/usuario/refresco-tokens', frm, {
        __isRetryRequest: true
      }).then(resp => {
        // console.log('refreshTokens: ' + JSON.stringify(resp.data))
        window.localStorage.setItem('accessToken', resp.data.accessToken)
        window.localStorage.setItem('refreshToken', resp.data.refreshToken)
        // Vue.http.config.headers.Authorization = 'Bearer ' + resp.data.accessToken
        return resp
      }, err => {
        throw err
      })
    } else {
      throw new Error('Error')
    }
  },

  initializeInterceptors: function (Vue) {
    // Authorization Interceptor
    Vue.http.interceptors.request.use(function (config) {
      let token = window.localStorage.getItem('accessToken')
      if (token != null) {
        config.headers.Authorization = 'Bearer ' + token
      }
      return config
    }, function (err) {
      return Promise.reject(err)
    })

    //  Refresh Token Interceptor
    let self = this
    Vue.http.interceptors.response.use(undefined, function (err) {
      console.log('Error: ' + JSON.stringify(err))
      if (err.response && err.response.status === 401 && err.config && !err.config.__isRetryRequest) {
        return self.refreshTokens(Vue).then(success => {
          err.config.__isRetryRequest = true
          err.config.baseURL = null // bug de axios, que cuando baseURL es relativa, la repite
          err.config.headers.Authorization = Vue.http.config.headers.Authorization
          return axios(err.config)
        }, error => {
          console.log('refreshTokenInterceptor.error: ', error)
          self.goToNoSessionPage()
          throw error
        })
      }
      throw err.response
    })
  },

  goToNoSessionPage: function () {
    console.log('goToNoSessionPage')
    this.Vue.gconfig.forceRoute = true
    this.router.push({path: 'login'})
  },

  goToErrorPage: function (type) {
    console.log('goToErrorPage ' + type)
    this.Vue.gconfig.forceRoute = true
    this.router.push({path: 'login'})
  },

  initLoader: function (Vue) {
    // inicializar
    GLoader.init()

    // asignar a vue
    Vue.loader = GLoader
    Vue.prototype.$loader = Vue.loader
  }

}

export default GConfig
