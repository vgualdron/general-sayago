<template>
  <b-modal v-model="show" :title="title">
     <p v-html="message"></p>
     <template v-if="objeto != null">
       <p><strong>Nombre: </strong>{{objeto.name}}</p>
       <b-img v-if="!objeto.mime.indexOf('image')" :src="objeto.url" fluid alt="Imagen" />
       <div v-else>
          <button @click="page -= 1" v-if="page > 1"> < </button>
          <button @click="page -= 1" v-else disabled> < </button>
          <button @click="page += 1" v-if="page < numPages"> > </button>
          <button @click="page += 1" v-else disabled> > </button>
          {{page}} /{{numPages}}
          <div style="width: 99%">
              <div v-if="loadedRatio > 0 && loadedRatio < 1" style="background-color: green; color: white; text-align: center" :style="{ width: loadedRatio * 100 + '%' }">{{ Math.floor(loadedRatio * 100) }}%</div>
              <pdf v-if="show" ref="pdf" :src="objeto.url" :page="page" :rotate="rotate" @password="password" @progress="loadedRatio = $event" @num-pages="numPages = $event"></pdf>
          </div>
       </div>
     </template>

     <div slot="modal-footer" class="w-100">
       <b-btn v-if="objeto && objeto.mime && objeto.mime === 'application/pdf'" size="sm" variant="primary" class="float-left m-1" @click="descargarArchivo">
        Descargar
       </b-btn>
       <b-btn v-if="showCancelButton" size="sm" class="float-right m-1" variant="secondary" @click="cancelClick">
         Cerrar
       </b-btn>
     </div>
  </b-modal>
</template>
<script type="text/babel">
import Vue from 'vue'
import FileSaver from 'file-saver'
import BootstrapVue from 'bootstrap-vue'
import pdf from 'vue-pdf'

Vue.use(BootstrapVue)

export default {
  components: {
    pdf: pdf
  },
  data: function () {
    return {
      objeto: null,
      title: '',
      message: '',
      show: false,
      showCancelButton: false,
      showOkButton: false,
      textOkButton: 'Aceptar',
      textCancelButton: 'xxx',
      onOk: function () { console.log('visualizador.vue.onOk') },
      onCancel: function () { console.log('visualizador.vue.onCancel') },
      loadedRatio: 0,
      page: 1,
      numPages: 0,
      rotate: 0,
      password: ''
    }
  },
  methods: {
    descargarArchivo: function () {
      Vue.axios.get(this.objeto.url, {responseType: 'blob'}).then(resp => {
        FileSaver.saveAs(resp.data, this.objeto.name)
      })
    },
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
