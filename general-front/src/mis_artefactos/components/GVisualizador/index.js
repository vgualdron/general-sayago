
import Vue from 'vue'
import visualizadorVue from './visualizador.vue'

const VisualizadorComponent = Vue.extend(visualizadorVue)

const GVisualizador = {
  instance: null,

  defaults: function () {
    return {
      objeto: null,
      title: '',
      message: '',
      type: '',
      showCancelButton: true,
      showOkButton: true,
      textCancelButton: 'Cancelar',
      textOkButton: 'Aceptar',
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
    this.instance = new VisualizadorComponent({
      el: elem
    })
  },

  destroy: function () {
  },

  show: function (opts) {
    this.init()
    for (let prop in opts) {
      this.instance[prop] = opts[prop]
    }
    this.instance.show = true
  },

  alert: function (opts) {
    var options = this.defaults()
    options.message = opts.message
    options.title = opts.title || options.title
    options.showCancelButton = false
    options.onOk = opts.onOk || options.onOk
    this.show(options)
  },

  confirm: function (opts) {
    var options = this.defaults()
    options.objeto = opts.objeto
    options.message = opts.message
    options.title = opts.title || options.title
    options.onOk = opts.onOk || options.onOk
    options.onCancel = opts.onCancel || options.onCancel
    this.show(options)
  },

  modalMsg: function (opts) {
    var options = this.defaults()
    options.message = opts.message
    options.title = opts.title || options.title
    options.showOkButton = false
    options.showCancelButton = false
    this.show(options)
  },

  close: function () {
    if (this.instance != null) {
      this.instance.show = false
    }
  }
}

export default GVisualizador
export { GVisualizador }
