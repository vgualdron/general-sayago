import Vue from 'vue'
import Vuex from 'vuex'
import './plugins/axios'
import BootstrapVue from 'bootstrap-vue'
import App from './App.vue'
import router from './router'
import Vuelidate from 'vuelidate'
import store from './store'
// import './register-service-worker'
import GConfig from './gnosoft/plugins/GConfig'
import GEvents from './gnosoft/plugins/GEvents'
import VueFormWizard from 'vue-form-wizard'
import GCalendario from './gnosoft/components/GCalendario'
import VueSignaturePad from 'vue-signature-pad'
// import Upload from './gnosoft/components/UploadFileFull'
import 'vue-form-wizard/dist/vue-form-wizard.min.css'

import vbMsgBox from 'bootstrap-vue-msgbox'
import VueAlertify from 'vue-alertify';

import vSelect from 'vue-select'
Vue.component('v-select', vSelect)

Vue.config.productionTip = false
Vue.use(Vuex)

Vue.use(VueFormWizard)
Vue.use(BootstrapVue)
Vue.use(GConfig, router)
Vue.use(Vuelidate)
Vue.use(GEvents)
Vue.use(VueSignaturePad)
Vue.component('g-calendario', GCalendario) 
Vue.use(vbMsgBox)
Vue.use(VueAlertify)

new Vue({
  router,
  store,
  data: {
    user: {
      token: null
    }
  },
  render: h => h(App)
}).$mount('#app')
