<template>
  <b-modal v-model="show" :title="title">
     <p v-html="message"></p>
     <div slot="modal-footer" class="w-100">
       <b-btn v-if="showOkButton" size="sm" class="float-right m-1" variant="primary" @click="okClick">
         {{ textOkButton }}
       </b-btn>
       <b-btn v-if="showCancelButton" size="sm" class="float-right m-1" variant="secundary" @click="cancelClick">
         {{ textCancelButton }}
       </b-btn>
     </div>
  </b-modal>
</template>
<script type="text/babel">
import Vue from 'vue'
import BootstrapVue from 'bootstrap-vue'

Vue.use(BootstrapVue)

export default {
  data: function () {
    return {
      title: '',
      message: '',
      show: false,
      showCancelButton: true,
      showOkButton: true,
      textOkButton: 'Aceptar',
      textCancelButton: 'Cancelar',
      onOk: function () { console.log('msgbox.vue.onOk') },
      onCancel: function () { console.log('msgbox.vue.onCancel') }
    }
  },
  methods: {
    cancelClick: function () {
      this.show = false
      if (this.onCancel) {
        this.onCancel()
      }
    },
    okClick: function () {
      this.show = false
      if (this.onOk) {
        this.onOk()
      }
    },
    close: function () {
      this.show = false
    }
  }
}
</script>
