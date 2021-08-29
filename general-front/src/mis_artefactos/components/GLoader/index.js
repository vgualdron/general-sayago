
import Vue from 'vue'
import loaderVue from './loader.vue'

const LoaderComponent = Vue.extend(loaderVue)

const GLoader = {
  instance: null,

  defaults: function () {
    return {
      message: '',
      type: '',
      onOk: function () {},
      onCancel: function () {}
    }
  },

  init: function () {
    if (this.instance != null) {
      return
    }
    var elem = document.createElement('div')
    document.body.appendChild(elem)
    this.instance = new LoaderComponent({
      el: elem
    })
  },
  show: function (opts) {
    this.init()
    for (let prop in opts) {
      this.instance[prop] = opts[prop]
    }
    this.instance.show = true
  },
  open: function (opts) {
    var options = this.defaults()
    options.message = opts.message
    this.show(options)
  },
  close: function () {
    if (this.instance != null) {
      this.instance.show = false
    }
  }
}

export default GLoader
export { GLoader }
