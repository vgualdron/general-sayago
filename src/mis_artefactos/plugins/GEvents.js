const GEvents = {
  install: function (Vue, router) {
    Vue.prototype.$events = new Vue({})
    Vue.events = Vue.prototype.$events
  }
}

export default GEvents
