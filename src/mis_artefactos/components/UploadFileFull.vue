<template>
<div class="example-full">
  <template v-if="arrayFilesExistentes != null"></template>
  <button type="button" class="btn btn-danger float-right btn-is-option" @click.prevent="isOption = !isOption" v-show="isOption">
    <i class="fa fa-cog" aria-hidden="true"></i>
    Opciones
  </button>
  <h6 v-if="title !== ''" id="example-title" class="example-title">{{title}}</h6>

  <div v-show="$refs.upload && $refs.upload.dropActive" class="drop-active">
    <h3>Arrastra archivos para subir</h3>
  </div>

  <b-button v-if="useCollapse === 'true'" size="sm" v-b-toggle="id" variant="primary" @click="viewBtnDropdown = !viewBtnDropdown">
    {{ viewBtnDropdown ? 'Ocultar' : 'Mostrar'}}
  </b-button>
  <b-collapse visible :id="id" class="mt-2">
    <div class="upload" v-show="!isOption">
      <div class="table-responsive" style="height: 200px;">
        <table class="table table-hover">
          <thead>
            <tr>
              <th style="text-align: center;">#</th>
              <th style="text-align: center;">Ver</th>
              <th style="text-align: center;">Nombre</th>
              <th style="text-align: center;">Tamaño</th>
              <!--<th>Velocidad</th>-->
              <th v-if="useRemove" style="text-align: center;">Remover</th>
              <!--<th>Estado</th>-->
            </tr>
          </thead>
          <tbody>
            <tr v-if="!files.length">
              <td colspan="7">
                <div class="text-center">
                  <h6><br>
                    Oprima "seleccionar archivos" para cargar archivos a la plataforma <br>
                  </h6>
                  <!--<label :for="name" class="btn btn-lg btn-primary" style="font-size: 0.875rem;cursor:  pointer;">
                    Selecciona archivos
                  </label>-->
                </div>
              </td>
            </tr>
            <tr v-for="(file, index) in files" :key="file.id">
              <td style="text-align: center;">{{index +1}}</td>
              <td class="hover-file" style="text-align: center;">
                <img v-if="file.thumb" :src="file.thumb" width="40" height="auto" @click="abrirModalImagen(file)"/>
                <img v-else src="static/img/icon_doc.png" width="33" height="auto" @click="abrirModalImagen(file)"/>
              </td>
              <td>
                <div class="filename">
                  {{file.name}}
                </div>
                <div class="progress" v-if="file.active || file.progress !== '0.00'">
                  <div :class="{'progress-bar': true, 'progress-bar-striped': false, 'bg-danger': file.error, 'bg-success': file.progress > 99, 'progress-bar-animated': file.active}"
                  role="progressbar"
                  :style="{width: file.progress + '%'}"
                  variant="success">
                  {{file.progress}} %
                  </div>
                </div>
              </td>
              <td style="text-align: center;">{{fileSize(file.size)}}</td>
              <!--<td>{{file.speed}}</td>-->
              <td v-if="useRemove" style="text-align: center;">
                 <button style="padding: 1px 6px;" type="button" class="btn btn-danger sm" @click.prevent="remover(file);">
                  <i class="fa fa-times" aria-hidden="true"></i>
                </button>
              </td>
              <!--
              <td v-if="file.error"><b-badge variant="danger">Error</b-badge></td>
              <td v-else-if="file.success"><b-badge variant="success">Enviado</b-badge></td>
              <td v-else-if="file.active"><b-badge variant="primary">Active</b-badge></td>
              <td v-else></td>-->
            </tr>
          </tbody>
        </table>
      </div>
      <div class="example-foorer">
        <div class="btn-group">
          <file-upload
            class="btn btn-primary dropdown-toggle"
            :maximum=parseInt(maxAmountFile)
            :post-action="postAction"
            :extensions="extensions"
            :accept="accept"
            :multiple="multiple"
            :directory="directory"
            :size="size || 0"
            :thread="thread < 1 ? 1 : (thread > 5 ? 5 : thread)"
            :headers="headers"
            :data="data"
            :drop="drop"
            :drop-directory="dropDirectory"
            :add-index="addIndex"
            v-model="files"
            @input-filter="inputFilter"
            @input-file="inputFile"
            ref="upload">
            <i class="fa fa-plus"></i>
            Seleccionar Archivos
          </file-upload>
          <div class="dropdown-menu">
            <label class="dropdown-item" :for="name">Agregar Archivos</label>
            <a class="dropdown-item" href="#" @click="onAddFolader">Agregar Carpeta</a>
            <!--<a class="dropdown-item" href="#" @click.prevent="addData.show = true">Agregar data</a>-->
          </div>
        </div>
        <!--<button type="button" class="btn btn-success" v-if="!$refs.upload || !$refs.upload.active" @click.prevent="$refs.upload.active = true">
          <i class="fa fa-arrow-up" aria-hidden="true"></i>
          Iniciar Carga de Archivos
        </button>
        <button type="button" class="btn btn-danger"  v-else @click.prevent="$refs.upload.active = false">
          <i class="fa fa-stop" aria-hidden="true"></i>
          Detener Carga de Archivos
        </button>--><!-- SUBIR ARCHIVOS DESDE ESTE COMPONENTE Y NO DESDE EL PADRE -->
      </div>
    </div>
  </b-collapse>

  <div class="option" v-show="isOption">
    <div class="form-group">
      <label for="accept">Accept:</label>
      <input type="text" id="accept" class="form-control" v-model="accept">
      <small class="form-text text-muted">Allow upload mime type</small>
    </div>
    <div class="form-group">
      <label for="extensions">Extensions:</label>
      <input type="text" id="extensions" class="form-control" v-model="extensions">
      <small class="form-text text-muted">Allow upload file extension</small>
    </div>
    <div class="form-group">
      <label>PUT Upload:</label>
      <div class="form-check">
        <label class="form-check-label">
          <input class="form-check-input" type="radio" name="put-action" id="put-action" value="" v-model="putAction"> Off
        </label>
      </div>
      <div class="form-check">
        <label class="form-check-label">
          <input class="form-check-input" type="radio" name="put-action" id="put-action" value="/upload/put" v-model="putAction"> On
        </label>
      </div>
      <small class="form-text text-muted">After the shutdown, use the POST method to upload</small>
    </div>
    <div class="form-group">
      <label for="thread">Thread:</label>
      <input type="number" max="5" min="1" id="thread" class="form-control" v-model.number="thread">
      <small class="form-text text-muted">Also upload the number of files at the same time (number of threads)</small>
    </div>
    <div class="form-group">
      <label for="size">Max size:</label>
      <input type="number" min="0" id="size" class="form-control" v-model.number="size">
    </div>
    <div class="form-group">
      <label for="minSize">Min size:</label>
      <input type="number" min="0" id="minSize" class="form-control" v-model.number="minSize">
    </div>
    <div class="form-group">
      <label for="autoCompress">Automatically compress:</label>
      <input type="number" min="0" id="autoCompress" class="form-control" v-model.number="autoCompress">
      <small class="form-text text-muted" v-if="autoCompress > 0">More than {{autoCompress}} files are automatically compressed</small>
      <small class="form-text text-muted" v-else>Set up automatic compression</small>
    </div>

    <div class="form-group">
      <div class="form-check">
        <label class="form-check-label">
          <input type="checkbox" id="add-index" class="form-check-input" v-model="addIndex"> Start position to add
        </label>
      </div>
      <small class="form-text text-muted">Add a file list to start the location to add</small>
    </div>

    <div class="form-group">
      <div class="form-check">
        <label class="form-check-label">
          <input type="checkbox" id="drop" class="form-check-input" v-model="drop"> Drop
        </label>
      </div>
      <small class="form-text text-muted">Drag and drop upload</small>
    </div>
    <div class="form-group">
      <div class="form-check">
        <label class="form-check-label">
          <input type="checkbox" id="drop-directory" class="form-check-input" v-model="dropDirectory"> Drop directory
        </label>
      </div>
      <small class="form-text text-muted">Not checked, filter the dragged folder</small>
    </div>
    <div class="form-group">
      <div class="form-check">
        <label class="form-check-label">
          <input type="checkbox" id="upload-auto" class="form-check-input" v-model="uploadAuto"> Auto start
        </label>
      </div>
      <small class="form-text text-muted">Automatically activate upload</small>
    </div>
    <div class="form-group">
      <button type="button" class="btn btn-primary btn-lg btn-block" @click.prevent="isOption = !isOption">Confirm</button>
    </div>
  </div>

  <div :class="{'modal-backdrop': true, 'fade': true, show: addData.show}"></div>
  <div :class="{modal: true, fade: true, show: addData.show}" id="modal-add-data" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add data</h5>
          <button type="button" class="close"  @click.prevent="addData.show = false">
            <span>&times;</span>
          </button>
        </div>
        <form @submit.prevent="onAddData">
          <div class="modal-body">
            <div class="form-group">
              <label for="name">Name:</label>
              <input type="text" class="form-control" required id="name"  placeholder="Please enter a file name" v-model="addData.name">
              <small class="form-text text-muted">Such as <code>filename.txt</code></small>
            </div>
            <div class="form-group">
              <label for="type">Type:</label>
              <input type="text" class="form-control" required id="type"  placeholder="Please enter the MIME type" v-model="addData.type">
              <small class="form-text text-muted">Such as <code>text/plain</code></small>
            </div>
            <div class="form-group">
              <label for="content">Content:</label>
              <textarea class="form-control" required id="content" rows="3" placeholder="Please enter the file contents" v-model="addData.content"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click.prevent="addData.show = false">Close</button>
            <button type="submit" class="btn btn-primary">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div :class="{'modal-backdrop': true, 'fade': true, show: editFile.show}"></div>
  <div :class="{modal: true, fade: true, show: editFile.show}" id="modal-edit-file" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit file</h5>
          <button type="button" class="close"  @click.prevent="editFile.show = false">
            <span>&times;</span>
          </button>
        </div>
        <form @submit.prevent="onEditorFile">
          <div class="modal-body">
            <div class="form-group">
              <label for="name">Name:</label>
              <input type="text" class="form-control" required id="name"  placeholder="Please enter a file name" v-model="editFile.name">
            </div>
            <div class="form-group" v-if="editFile.show && editFile.blob && editFile.type && editFile.type.substr(0, 6) === 'image/'">
              <label>Image: </label>
              <div class="edit-image">
                <img :src="editFile.blob" ref="editImage" />
              </div>

              <div class="edit-image-tool">
                <div class="btn-group" role="group">
                  <button type="button" class="btn btn-primary" @click="editFile.cropper.rotate(-90)" title="cropper.rotate(-90)"><i class="fa fa-undo" aria-hidden="true"></i></button>
                  <button type="button" class="btn btn-primary" @click="editFile.cropper.rotate(90)"  title="cropper.rotate(90)"><i class="fa fa-repeat" aria-hidden="true"></i></button>
                </div>
                <div class="btn-group" role="group">
                  <button type="button" class="btn btn-primary" @click="editFile.cropper.crop()" title="cropper.crop()"><i class="fa fa-check" aria-hidden="true"></i></button>
                  <button type="button" class="btn btn-primary" @click="editFile.cropper.clear()" title="cropper.clear()"><i class="fa fa-remove" aria-hidden="true"></i></button>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click.prevent="editFile.show = false">Close</button>
            <button type="submit" class="btn btn-primary">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</div>
</template>
<style scoped>
.hover-file {
  cursor: pointer;
}
.modal-backdrop {
  position: unset !important;
}
.example-full .btn-group .dropdown-menu {
  display: block;
  visibility: hidden;
  transition: all .2s
}
.example-full .btn-group:hover > .dropdown-menu {
  visibility: visible;
}
.example-full label.dropdown-item {
  margin-bottom: 0;
}
.example-full .btn-group .dropdown-toggle {
  margin-right: .6rem
}
.example-full .filename {
  margin-bottom: .3rem
}
.example-full .btn-is-option {
  margin-top: 0.25rem;
}
.example-full .example-foorer {
  padding: .5rem 0;
  border-top: 1px solid #e9ecef;
  border-bottom: 1px solid #e9ecef;
}
.example-full .edit-image img {
  max-width: 100%;
}
.example-full .edit-image-tool {
  margin-top: .6rem;
}
.example-full .edit-image-tool .btn-group{
  margin-right: .6rem;
}
.example-full .footer-status {
  padding-top: .4rem;
}
.example-full .drop-active {
  top: 0;
  bottom: 0;
  right: 0;
  left: 0;
  position: fixed;
  z-index: 9999;
  opacity: .6;
  text-align: center;
  background: #000;
}
.example-full .drop-active h3 {
  margin: -.5em 0 0;
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  -webkit-transform: translateY(-50%);
  -ms-transform: translateY(-50%);
  transform: translateY(-50%);
  font-size: 40px;
  color: #fff;
  padding: 0;
}
.table th, .table td {
  padding: 5px;
}
.bg-primary, .bg-success, .bg-info, .bg-warning, .bg-danger, .bg-inverse {
  color: #151b1e;
}
</style>

<script>
import Vue from 'vue'
import FileSaver from 'file-saver'
import Cropper from 'cropperjs'
import ImageCompressor from '@xkeshi/image-compressor'
import FileUpload from 'vue-upload-component'
import pdf from 'vue-pdf'
import GVisualizador from './GVisualizador'
export default {
  name: 'upload-file-full',
  props: {
    id: {
      type: String,
      default: null,
      required: true
    },
    maxAmountFile: {
      type: String,
      default: null,
      required: true
    },
    title: {
      type: String,
      default: 'Cargar archivos',
      required: false
    },
    maxSizeFile: {
      type: String,
      default: null,
      required: true
    },
    urlWsSubir: {
      type: String,
      default: null,
      required: true
    },
    urlWsEliminar: {
      type: String,
      default: null,
      required: true
    },
    formatAccept: {
      type: String,
      default: null
    },
    filterExtensions: {
      type: String,
      default: null
    },
    useCollapse: {
      type: String,
      default: false,
      required: true
    },
    arrayFilesExistentes: {
      type: Array,
      default: null
    },
    useRemove: {
      type: Boolean,
      default: true,
      required: false
    }
  },
  components: {
    FileUpload,
    pdf: pdf
  },
  created: function () {
    this.postAction = this.urlWsSubir
    this.putAction = this.urlWsSubir
    this.accept = this.formatAccept
    this.extensions = this.filterExtensions
    this.size = parseInt(this.maxSizeFile)
  },
  mounted: function () {
  },
  data: function () {
    return {
      files: [],
      accept: 'image/png,image/gif,image/jpeg,image/webp,application/pdf', //  defualt si no se setea valor desde el padre
      extensions: 'gif,jpg,jpeg,png,webp,pdf', //  defualt si no se setea valor desde el padre
      // extensions: ['gif', 'jpg', 'jpeg','png', 'webp'],
      // extensions: /\.(gif|jpe?g|png|webp)$/i,
      minSize: 1024,
      size: 1024 * 1024 * 2, // 2MB defualt si no se setea valor desde el padre
      multiple: true,
      directory: false,
      drop: true,
      dropDirectory: true,
      addIndex: false,
      thread: 3,
      name: 'file',
      postAction: '/upload/post', //  defualt si no se setea valor desde el padre
      putAction: '/upload/put', //  defualt si no se setea valor desde el padre
      headers: {
        'X-Csrf-Token': 'xxxx'
      },
      data: {
        '_csrf_token': 'xxxxxx'
      },
      autoCompress: 1024 * 1024,
      uploadAuto: false,
      isOption: false,
      addData: {
        show: false,
        name: '',
        type: '',
        content: ''
      },
      editFile: {
        show: false,
        name: ''
      },
      viewBtnDropdown: true,
      showModalVisualizador: false,
      srcImagenActual: '',
      srcPdfActual: '',
      fileActual: null,
      show: true,
      src: '',
      loadedRatio: 0,
      page: 1,
      numPages: 0,
      rotate: 0,
      password: '',
      fids: [],
      fids_remove: [],
      delete: false
    }
  },
  watch: {
    'editFile.show' (newValue, oldValue) {
      // 关闭了 自动删除 error
      if (!newValue && oldValue) {
        this.$refs.upload.update(this.editFile.id, { error: this.editFile.error || '' })
      }
      if (newValue) {
        this.$nextTick(function () {
          if (!this.$refs.editImage) {
            return
          }
          let cropper = new Cropper(this.$refs.editImage, {
            autoCrop: false
          })
          this.editFile = {
            ...this.editFile,
            cropper
          }
        })
      }
    },
    'addData.show' (show) {
      if (show) {
        this.addData.name = ''
        this.addData.type = ''
        this.addData.content = ''
      }
    }
  },
  methods: {
    remover: function (file) {
      this.delete = true
      this.$refs.upload.remove(file)
    },
    abrirModalImagen: function (file) {
      let object = {
        name: file.name,
        mime: file.file.type,
        url: file.blob
      }
      // Soporte para descarga de archivos excel (xls | xlsx)
      if (object.mime === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || object.mime === 'application/vnd.ms-excel') {
        Vue.axios.get(object.url, {responseType: 'blob'}).then(resp => {
          FileSaver.saveAs(resp.data, object.name)
        })
      } else {
        GVisualizador.confirm({
          message: '',
          objeto: object
        })
      }
      this.fileActual = file
    },
    fileSize: function (size) {
      let size_ = size / 1000000
      if (size_ < 1) {
        size = (size_ * 1000).toFixed(2) + ' KB'
      } else if (size_ >= 1) {
        size = size_.toFixed(2) + ' MB'
      }
      return size
    },
    inputFilter (newFile, oldFile, prevent) {
      var self = this
      if (newFile && !oldFile) {
        // Before adding a file
        // 添加文件前
        // Filter system files or hide files
        // 过滤系统文件 和隐藏文件
        if (/(\/|^)(Thumbs\.db|desktop\.ini|\..+)$/.test(newFile.name)) {
          return prevent()
        }
        // Filter php html js file
        // 过滤 php html js 文件
        if (/\.(php5?|html?|jsx?)$/i.test(newFile.name)) {
          return prevent()
        }
        if (newFile.size >= 0 && newFile.size > this.size) {
          this.$toast.error('Tamaño del archivo ' + newFile.name + ' no puede ser mayor a ' + this.fileSize(this.size), null, 6000, function () {})
          return prevent()
        }
        if (this.files.length > parseInt(this.maxAmountFile)) {
          this.$toast.error('La cantidad de archivos no puede ser mayor a ' + parseInt(this.maxAmountFile), null, 6000, function () {})
          return prevent()
        }
        // Automatic compression
        // 自动压缩
        if (newFile.file && newFile.type.substr(0, 6) === 'image/' && this.autoCompress > 0 && this.autoCompress < newFile.size) {
          newFile.error = 'compressing'
          const imageCompressor = new ImageCompressor(null, {
            convertSize: Infinity,
            maxWidth: 512,
            maxHeight: 512
          })
          imageCompressor.compress(newFile.file)
            .then((file) => {
              self.$refs.upload.update(newFile, { error: '', file, size: file.size, type: file.type })
            })
            .catch((err) => {
              self.$refs.upload.update(newFile, { error: err.message || 'compress' })
            })
        }
      }
      if (newFile && (!oldFile || newFile.file !== oldFile.file)) {
        // Create a blob field
        // 创建 blob 字段
        newFile.blob = ''
        let URL = window.URL || window.webkitURL
        if (URL && URL.createObjectURL) {
          newFile.blob = URL.createObjectURL(newFile.file)
        }
        // Thumbnails
        // 缩略图
        newFile.thumb = ''
        if (newFile.blob && newFile.type.substr(0, 6) === 'image/') {
          newFile.thumb = newFile.blob
        }
      }
    },
    // add, update, remove File Event
    inputFile (newFile, oldFile) {
      if (newFile && oldFile) {
        // update
        if (newFile.active && !oldFile.active) {
          // beforeSend
          // min size
          let errorSize = 'Tamaño del archivo ' + newFile.name + ' no puede ser mayor a ' + this.fileSize(this.size) + ' o inferior a ' + this.fileSize(this.minSize)
          if (newFile.size >= 0 && this.minSize > 0 && newFile.size < this.minSize) {
            this.$refs.upload.update(newFile, { error: errorSize })
          }
        }
        if (newFile.progress !== oldFile.progress) {
          // progress
          // alert('progress')
        }
        if (newFile.error && !oldFile.error) {
          // error
          this.$toast.error('Error ' + newFile.error, null, 4000, function () {
          })
        }
        if (newFile.success && !oldFile.success) {
          // success
          // alert('success')
        }
      }
      if (!newFile && oldFile) {
        // remove
        let self = this
        if ((oldFile.success && oldFile.response.fid) || oldFile.file.fid) {
          let fid = oldFile.response.fid
          if (oldFile.file.fid && this.delete) {
            this.delete = false
            fid = oldFile.file.fid
            self.fids_remove.push(fid)
          }
          /* this.$http.get(self.urlWsEliminar + fid).then(resp => {
            console.log('eliminar file ...')
          }) */
        }
      }
      // Automatically activate upload
      if (Boolean(newFile) !== Boolean(oldFile) || oldFile.error !== newFile.error) {
        if (this.uploadAuto && !this.$refs.upload.active) {
          this.$refs.upload.active = true
        }
      }
      this.fids = this.filtrarFid(this.$refs.upload.value)
      this.$emit('responseUpload', this.fids)
    },
    filtrarFid (array) {
      let resp = []
      array.forEach(function (obj) {
        if (obj.response.fid) {
          resp.push(obj.response)
        }
      })
      return resp
    },
    alert (message) {
      alert(message)
    },
    onEditFileShow (file) {
      this.editFile = { ...file, show: true }
      this.$refs.upload.update(file, { error: 'edit' })
    },
    onEditorFile () {
      if (!this.$refs.upload.features.html5) {
        this.alert('Your browser does not support')
        this.editFile.show = false
        return
      }
      let data = {
        name: this.editFile.name
      }
      if (this.editFile.cropper) {
        let binStr = atob(this.editFile.cropper.getCroppedCanvas().toDataURL(this.editFile.type).split(',')[1])
        let arr = new Uint8Array(binStr.length)
        for (let i = 0; i < binStr.length; i++) {
          arr[i] = binStr.charCodeAt(i)
        }
        data.file = new File([arr], data.name, { type: this.editFile.type })
        data.size = data.file.size
      }
      this.$refs.upload.update(this.editFile.id, data)
      this.editFile.error = ''
      this.editFile.show = false
    },
    // add folader
    onAddFolader () {
      if (!this.$refs.upload.features.directory) {
        this.alert('Your browser does not support')
        return
      }
      let input = this.$refs.upload.$el.querySelector('input')
      input.directory = true
      input.webkitdirectory = true
      this.directory = true
      input.onclick = null
      input.click()
      input.onclick = (e) => {
        this.directory = false
        input.directory = false
        input.webkitdirectory = false
      }
    },
    onAddData () {
      this.addData.show = false
      if (!this.$refs.upload.features.html5) {
        this.alert('Your browser does not support')
        return
      }
      let file = new window.File([this.addData.content], this.addData.name, {
        type: this.addData.type
      })
      this.$refs.upload.add(file)
    }
  }
}
</script>
