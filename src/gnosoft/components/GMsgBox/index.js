
import Vue from 'vue'
import msgboxVue from './msgbox.vue'

const MsgBoxComponent = Vue.extend(msgboxVue)

const GMsgBox = {
  instance: null,

  defaults: function () {
    return {
      title: 'PAE',
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
    this.instance = new MsgBoxComponent({
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
    options.textCancelButton = opts.textCancelButton || options.textCancelButton
    options.textOkButton = opts.textOkButton || options.textOkButton
    this.show(options)
  },

  confirm: function (opts) {
    var options = this.defaults()
    options.message = opts.message
    options.title = opts.title || options.title
    options.onOk = opts.onOk || options.onOk
    options.onCancel = opts.onCancel || options.onCancel
    options.textCancelButton = opts.textCancelButton || options.textCancelButton
    options.textOkButton = opts.textOkButton || options.textOkButton
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

export default GMsgBox
export { GMsgBox }
