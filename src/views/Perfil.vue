<template>
  <b-container>
    <b-row>
      <b-col>
        <div class="card-header mt-3 centrado">
			<h3>Gestión de Perfiles</h3>
		</div>
      </b-col>
    </b-row>
    <b-row class="mt-3">
      <b-col>
        <b-btn class="ml-3" @click="agregar()" variant="primary">Agregar</b-btn>
      </b-col>
    </b-row>
    <template v-if="perfiles && perfiles.length > 0">
      <b-row class="mt-3 justify-content-center">
        <b-col class="contenedor-tabla">
          <b-table class="tabla borde-contenedor" :current-page="paginaActual" :per-page="porPagina" bordered :fields="encabezado" :items="perfiles">
             <template  slot="acciones" slot-scope="row">
                <div class="contenedor-botones">
                  <div class="espacio-botones">
                    <b-button size="sm" variant="primary" title="Editar" @click="editar(row.item)">
                      <i class="icon-pencil"></i>
                    </b-button>
                  </div>
                  <div class="espacio-botones">
                    <b-button size="sm" variant="danger" @click="eliminar(row.item)">
                      <i class="fa fa-trash"></i>
                    </b-button>
                  </div>
                </div>
              </template>
          </b-table>
        </b-col>
      </b-row>
      <b-row class="mb-5">
        <b-col>
          <b-pagination align="center" :total-rows="totalFilas" :per-page="porPagina" v-model="paginaActual" class="my-0" />
        </b-col>
      </b-row>
    </template>
    <b-row class="mt-3" v-else>
      <b-col>
        <b-alert show variant="danger">No se encontraron registros de perfiles</b-alert>
      </b-col>
    </b-row>
    <b-modal v-if="perfilSeleccionado" v-model="cargarFormulario">
        <div slot="modal-title">
          <p class="font-weight-light mb-1" style="font-size:20px;">
            {{operacion}}
          </p>
        </div>
        <b-container>
          <b-row class="mb-3">
            <b-col>
              <span class="font-weight-bold">Nombre: </span>
              <b-form-input v-if="!verInfo" v-model="perfilSeleccionado.nombre" type="text"></b-form-input>
              <span v-else>{{perfilSeleccionado.nombre}}</span>
            </b-col>
          </b-row>
          <b-row>
            <b-col>
              <span class="font-weight-bold">Estado: </span>
              <b-form-select v-if="!verInfo" v-model="perfilSeleccionado.estado" class="mb-3">
                <option v-for="estado in estados" :value="estado" :key="estado">{{estado}}</option>
              </b-form-select>
              <span v-else>{{perfilSeleccionado.estado}}</span>
            </b-col>
          </b-row>
        </b-container>
        <div slot="modal-footer" class="pull-right">
          <b-btn v-if="!verInfo && perfilSeleccionado.idPerfil" size="sm" variant="primary" @click="confirmarEditar">
            Guardar
          </b-btn>
          <b-btn v-else-if="!verInfo && !perfilSeleccionado.idPerfil" size="sm" variant="primary" @click="confirmarAgregar">
            Guardar
          </b-btn>
          <b-btn v-else size="sm" variant="danger" @click="confirmarEliminar">
            Eliminar
          </b-btn>
          <b-btn size="sm" variant="secondary" @click="cargarFormulario = false">
            Cerrar
          </b-btn>
        </div>
      </b-modal>
  </b-container>
</template>
<script>
import GMsgBox from '../gnosoft/components/GMsgBox'
export default {
  name: 'Perfil',
  data: function () {
    return {
      encabezado: [],
      perfiles: [],
      estados: [],
      porPagina: 10,
      paginaActual: 1,
      perfilSeleccionado: null,
      verInfo: false,
      operacion: '',
      totalFilas: 1,
      cargarFormulario: false
    }
  },
  created: function () {
    this.listarPerfiles()
  },
  methods: {
    listarPerfiles: function () {
      	this.$http.get('/ws/general/perfiles').then(resp => {
        this.perfiles = resp.data.perfiles
        this.encabezado = resp.data.encabezado
        this.estados = resp.data.estados
        this.totalFilas = this.perfiles.length
      }).catch(resp => {
        if (resp.data && resp.data.error) {
          this.$toast.error(resp.data.error)
        } else {
          this.$toast.error('Ocurrió un error al listar los perfiles')
        }
      })
    },
    agregar: function () {
      this.perfilSeleccionado = {
        nombre: '',
        estado: ''
      }
      this.cargarFormulario = true
      this.verInfo = false
      this.operacion = 'Agregar'
    },
    editar: function (item) {
      this.perfilSeleccionado = JSON.parse(JSON.stringify(item))
      this.cargarFormulario = true
      this.verInfo = false
      this.operacion = 'Editar'
    },
    confirmarEditar: function () {
      if (!this.validarCamposPerfil()) {
        return
      }
      this.cargarFormulario = false
      var self = this
      GMsgBox.confirm({
        message: '¿Esta seguro de realizar la operación?',
        onOk: function () {
          self.$loader.open({message: 'Actualizando Perfil...'})
          self.$http.post('ws/general/gestionar_perfil', self.perfilSeleccionado).then(resp => {
            var indice =
                self.perfiles.splice(self.perfiles.findIndex(e => e.idPerfil === self.perfilSeleccionado.idPerfil), 1, self.perfilSeleccionado)
            self.perfilSeleccionado = null
            self.$loader.close()
            if (resp.data && resp.data.mensaje) {
              self.$toast.success(resp.data.mensaje)
            } else {
              self.$toast.success('Se actualizó el perfil satisfactoriamente')
            }
          }).catch(resp => {
            self.$loader.close()
            if (resp.data && resp.data.error) {
              self.$toast.error(resp.data.error)
            } else {
              self.$toast.error('Ocurrió un error al actualizar el perfil')
            }
          })
        },
        onCancel: function () {
          self.cargarFormulario = true
          this.close()
        }
      })
    },
    validarCamposPerfil: function () {
      if (!this.perfilSeleccionado.nombre) {
        this.$toast.error('Debe ingresar el nombre del perfil')
        return false
      }
      if (!this.perfilSeleccionado.estado) {
        this.$toast.error('Debe asignar un estado al perfil')
        return false
      }
      return true
    },
    confirmarAgregar: function () {
      if (!this.validarCamposPerfil()) {
        return
      }
      this.cargarFormulario = false
      var self = this
      GMsgBox.confirm({
        message: '¿Esta seguro de realizar la operación?',
        onOk: function () {
          self.$loader.open({message: 'Agregando Perfil...'})
          self.$http.post('ws/general/gestionar_perfil', self.perfilSeleccionado).then(resp => {
            self.perfilSeleccionado.idPerfil = resp.data.idPerfil
            self.perfiles.push(self.perfilSeleccionado)
            self.perfilSeleccionado = null
            self.totalFilas = self.perfiles.length
            self.$loader.close()
            if (resp.data && resp.data.mensaje) {
              self.$toast.success(resp.data.mensaje)
            } else {
              self.$toast.success('Se agregó el perfil satisfactoriamente')
            }
          }).catch(resp => {
            self.$loader.close()
            if (resp.data && resp.data.error) {
              self.$toast.error(resp.data.error)
            } else {
              self.$toast.error('Ocurrió un error al agregar el perfil')
            }
          })
        },
        onCancel: function () {
          self.cargarFormulario = true
          this.close()
        }
      })
    },
    eliminar: function (item) {
      this.perfilSeleccionado = JSON.parse(JSON.stringify(item))
      this.cargarFormulario = true
      this.verInfo = true
      this.operacion = 'Eliminar'
    },
    confirmarEliminar: function () {
      	this.cargarFormulario = false
      var self = this
      GMsgBox.confirm({
        message: '¿Esta seguro de realizar la operación?',
        onOk: function () {
          self.$loader.open({message: 'Eliminando Perfil...'})
          var frm = {
            idPerfil: self.perfilSeleccionado.idPerfil
          }
          self.$http.post('ws/general/eliminar_perfil', frm).then(resp => {
            self.perfiles.splice(self.perfiles.findIndex(e => e.idPerfil === frm.idPerfil), 1)
            self.totalFilas = self.perfiles.length
            self.perfilSeleccionado = null
            self.$loader.close()
            if (resp.data && resp.data.mensaje) {
              self.$toast.success(resp.data.mensaje)
            } else {
              self.$toast.success('Se eliminó el perfil satisfactoriamente')
            }
          }).catch(resp => {
            self.$loader.close()
            if (resp.data && resp.data.error) {
              self.$toast.error(resp.data.error)
            } else {
              self.$toast.error('Ocurrió un error al eliminar el perfil')
            }
          })
        },
        onCancel: function () {
          self.cargarFormulario = true
          this.close()
        }
      })
    }
  }
}
</script>
<style scoped>
  @media screen and (max-width:576px){
    .centrado{
      text-align:center;
    }
  }
  @media (min-width: 768px) and (max-width: 1200px) {
		.espacio-botones{
			margin-left:10px !important;
			margin-right:10px !important;
			margin-top:0px !important;
			margin-bottom:0px !important;
		}
	}

	@media (max-width: 768px) {
		.espacio-botones{
			padding-left:0px !important;
			padding-right:0px !important;
			padding-top:3px !important;
			padding-bottom:3px !important;
		}
		.contenedor-botones{
			display: block !important;
		}
	}
  .espacio-botones{
    margin-left:10px;
    margin-right:10px;
  }
  .contenedor-botones{
    display: flex;
  }
  .contenedor-tabla{
    overflow-x:auto;
  }
  .tabla{
    min-width: 500px;
    overflow-x: auto;
  }
</style>
