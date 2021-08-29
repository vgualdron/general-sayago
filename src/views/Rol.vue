<template>
  <div>
    <b-row>
      <b-col cols="12">
        <b-card border-variant="primary" header-bg-variant="primary" :header="'<strong>Gestionar Rol</strong> (' + totalFilas + ')'" tag="article" class="m-3 mt-3">
          <b-row class="mt-1 mb-2">
            <b-col cols="12">
              <b-form-group>
                <b-input-group cols="9">
                  <b-form-input
                    type="text"
                    v-model="filtro"
                    placeholder="Filtrar Búsqueda"
                    autocomplete="text"
                  ></b-form-input>
                  <!-- Attach Right button -->
                  <b-input-group-append>
                    <b-button variant="primary" :disabled="!filtro" @click.stop="filtro = ''">x</b-button>
                    <b-btn
                      class="ml-3"
                      @click.stop="cargarFormulario(null,'Agregar')"
                      variant="primary"
                    >Nuevo</b-btn>
                  </b-input-group-append>
                </b-input-group>
              </b-form-group>
            </b-col>
          </b-row>

          <b-row align-h="center">
            <b-col class="contenedor-tabla">
              <b-table
                v-if="items && items.length > 0"
                stacked="md"
                striped
                hover
                class="columna-centrada"
                :fields="campos"
                :items="items"
                :current-page="paginaActual"
                :per-page="porPagina"
                :filter="filtro"
                @filtered="actualizarTabla">
                <template slot="acciones" slot-scope="row">
                  <b-button
                    style="margin: 1px;"
                    size="sm"
                    title="Ver"
                    @click.stop="cargarFormulario(row.item,'Ver')">
                    <i class="icon-eye"></i>
                  </b-button>
                  <b-button
                    style="margin: 1px;"
                    size="sm"
                    variant="primary"
                    title="Modificar"
                    @click.stop="cargarFormulario(row.item,'Modificar')">
                    <i class="icon-pencil"></i>
                  </b-button>
                  <b-button
                    style="margin: 1px;"
                    size="sm"
                    title="Eliminar"
                    variant="danger"
                    @click.stop="cargarFormulario(row.item,'Eliminar')">
                    <i class="fa fa-trash"></i>
                  </b-button>
                  <b-button
                    style="margin: 1px;"
                    size="sm"
                    variant="danger"
                    title="Asignar Funcionalidades a Rol"
                    @click.stop="abrirModalAsignarFuncionalidades(row.item,'Asignar')">
                    <i class="fa fa-check-square-o"></i>
                  </b-button>
                </template>
              </b-table>
              <b-alert v-else show>No se encontraron registros.</b-alert>
            </b-col>
          </b-row>

          <b-row class="mb-5">
            <b-col>
              <b-pagination
                align="center"
                :total-rows="totalFilas"
                :per-page="porPagina"
                v-model="paginaActual"
                class="my-0"
              />
            </b-col>
          </b-row>

          <b-modal v-if="objeto" centered v-model="showModal" :title="tipoOperacion">
            <b-container>
              <b-row class="mb-3">
                <b-col
                  class="text-left"
                  v-if="(tipoOperacion === 'Ver' || tipoOperacion === 'Eliminar')">
                  <span class="font-weight-bold">
                    <span style="color:red;">*</span>Nombre
                  </span>:
                  <br>
                  {{objeto.descripcion}}
                </b-col>
                <b-col class="text-left" v-else>
                  <span class="font-weight-bold">
                    <span style="color:red;">*</span>Nombre
                  </span>:
                  <b-form-input
                    type="text"
                    required
                    v-model="objeto.descripcion"
                    class="form-control"
                  />
                </b-col>
              </b-row>
              <b-row class="mb-3">
                <b-col
                  class="text-left"
                  v-if="(tipoOperacion === 'Ver' || tipoOperacion === 'Eliminar')">
                  <span class="font-weight-bold">Orden</span>:
                  <br>
                  {{objeto.orden}}
                </b-col>
                <b-col class="text-left" v-else>
                  <span class="font-weight-bold">Orden</span>:
                  <b-form-input type="number" required v-model="objeto.orden" class="form-control"/>
                </b-col>
              </b-row>
              <b-row class="mb-3">
                <b-col class="text-left">
                  <span class="font-weight-bold">
                    <span style="color:red;">*</span>Visible
                  </span>:
                  <b-form-group>
                    <b-form-radio-group
                      stacked
                      v-model="selectedVisble"
                      name="radioVisble"
                      :options="[{value: 'SI', text: 'SI'}, {value: 'NO', text: 'NO'}]"
                    ></b-form-radio-group>
                  </b-form-group>
                </b-col>
              </b-row>
            </b-container>
            <div slot="modal-footer" v-if="objeto !== null" class="pull-right">
              <b-btn
                v-if="tipoOperacion === 'Ver'"
                size="sm"
                variant="secondary"
                @click.stop="cerrarFormulario"
              >Cerrar</b-btn>
              <b-btn
                v-if="tipoOperacion === 'Agregar'"
                size="sm"
                variant="primary"
                @click.stop="guardar"
              >Guardar</b-btn>
              <b-btn
                v-if="tipoOperacion === 'Modificar'"
                size="sm"
                variant="primary"
                @click.stop="guardarCambios"
              >Guardar Cambios</b-btn>
              <b-btn
                v-if="tipoOperacion === 'Eliminar'"
                size="sm"
                variant="danger"
                @click.stop="eliminar"
              >Eliminar</b-btn>
            </div>
          </b-modal>


          <b-modal v-if="objeto" centered v-model="showModalAsignar" title="Asignar Funcionalidades a Rol">
            <b-container>
              <b-row class="mb-3">
                <b-col
                  class="text-left">
                  <span class="font-weight-bold">
                    <span style="color:red;">*</span>Nombre
                  </span>:
                  <br>
                  {{objeto.descripcion}}
                </b-col>
              </b-row>

              <b-row class="mb-3">
                <b-col class="text-left">
                  <span class="font-weight-bold">
                    <span style="color:red;">*</span>Aplicación
                  </span>:
                  <b-form-group>
                    <b-form-select v-model="objeto.idAplicacion" :options="aplicaciones" @change="listarFuncionalidades"/>
                  </b-form-group>
                </b-col>
              </b-row>

              <b-row class="mb-3">
                <b-col class="text-left">
                  <span class="font-weight-bold">
                    <span style="color:red;">*</span>Funcionalidades
                  </span>:
                  <b-form-group v-if="objeto.idAplicacion">
                    <b-form-checkbox-group
                      stacked
                      v-model="funcionalidadesSeleccionadas"
                      id="checkboxes1"
                      name="check-funcionalidades">
                      <b-form-checkbox :key="'func_' + f" v-for="(funcionalidad, f) in funcionalidades" :value="funcionalidad">{{funcionalidad.text}}</b-form-checkbox>
                    </b-form-checkbox-group>  
                  </b-form-group>
                  <b-alert v-else show variant="info">Seleccione la aplicación.</b-alert>
                </b-col>
              </b-row>
            </b-container>
            <div slot="modal-footer" v-if="objeto !== null" class="pull-right">
              <b-btn
                size="sm"
                variant="primary"
                @click.stop="guardarCambiosAsignar">Guardar Cambios</b-btn>
            </div>
          </b-modal>

        </b-card>
      </b-col>
    </b-row>
  </div>
</template>
<style scoped>
.columna-centrada {
  text-align: center;
}
</style>

<script>
var self = this;
export default {
  name: "Rol",
  data: function() {
    return {
      campos: [
        {
          key: "descripcion",
          label: "Nombre",
          sortable: true,
          thStyle: "text-align:center;",
          tdClass: "columna-centrada"
        },
        {
          key: "orden",
          label: "Orden",
          sortable: true,
          thStyle: "text-align:center;",
          tdClass: "columna-centrada"
        },
        {
          key: "visible",
          label: "Visble",
          sortable: true,
          thStyle: "text-align:center;",
          tdClass: "columna-centrada"
        },
        {
          key: "acciones",
          label: "Acciones",
          sortable: false,
          thStyle: "text-align:center;",
          tdClass: "columna-centrada"
        }
      ],
      items: [],
      objeto: null,
      showModal: false,
      showModalAsignar: false,
      tipoOperacion: "",
      totalFilas: 1,
      filtro: "",
      porPagina: 20,
      paginaActual: 1,
      selectedVisble: 'SI',
      aplicaciones: [],
      funcionalidades: [],
      funcionalidadesSeleccionadas: []
    };
  },
  methods: {
    cargarFormulario: function(obj, operacion) {
      this.tipoOperacion = operacion;
      this.objeto = obj;
      if (obj === null) {
        this.objeto = {
          id: null,
          orden: null,
          descripcion: null,
          visible: null
        };
      } else {
        this.selectedVisble = obj.visible
        this.objeto = {
          id: obj.id,
          orden: obj.orden,
          descripcion: obj.descripcion,
          visible: obj.visible
        };
      }
      this.showModal = true;
    },
    abrirModalAsignarFuncionalidades: function(obj, operacion) {
      this.tipoOperacion = operacion;
      this.objeto = obj;
      this.showModalAsignar = true;
    },
    validarCampos: function() {
      if (!this.objeto.descripcion || this.objeto.descripcion.trim().length < 1) {
        this.$toast.error("Debe de escribir el nombre");
        return false;
      } else if (this.selectedVisble.length < 1) {
        this.$toast.error("Debe de seleccionar si es visible o no");
        return false;
      }

      return true;
    },
    listarAplicaciones: function() {
      this.$loader.open({ message: "Cargando ..." });
      var self = this;
      var frm = {};
      this.$http
        .get("ws/aplicacion/", frm)
        .then(resp => {
          self.aplicaciones = resp.data;
          self.$loader.close();
        })
        .catch(resp => {
          self.$loader.close();
          if (resp.data && resp.data.mensaje) {
            self.$toast.error(resp.data.mensaje);
          } else {
            self.$toast.error("No se pudo obtener los items de aplicaciones");
          }
        });
    },
    listarFuncionalidades: function(idAplicacion) {
      this.listarFuncionalidadesSeleccionadas(this.objeto.id, idAplicacion)
      this.$loader.open({ message: "Cargando ..." });
      var self = this;
      this.$http.get("ws/funcionalidad/?idAplicacion=" + idAplicacion).then(resp => {
          self.funcionalidades = resp.data
          self.$loader.close();
        })
        .catch(resp => {
          self.$loader.close();
          if (resp.data && resp.data.mensaje) {
            self.$toast.error(resp.data.mensaje);
          } else {
            self.$toast.error("No se pudo obtener los items de funcionalidades padres");
          }
        });
    },
    listarFuncionalidadesSeleccionadas: function(idRol, idAplicacion) {
      this.$loader.open({ message: "Cargando ..." });
      var self = this;
      this.$http.get("ws/funcionalidad/?idRol=" + idRol + '&idAplicacion=' + idAplicacion).then(resp => {
          self.funcionalidadesSeleccionadas = resp.data
          self.$loader.close();
        })
        .catch(resp => {
          self.$loader.close();
          if (resp.data && resp.data.mensaje) {
            self.$toast.error(resp.data.mensaje);
          } else {
            self.$toast.error("No se pudo obtener los items de funcionalidades seleccionadas");
          }
        });
    },
    listar: function() {
      this.$loader.open({ message: "Cargando ..." });
      var self = this;
      var frm = {};
      this.$http.get("ws/rol/", frm).then(resp => {
          self.items = resp.data;
          if (self.items && self.items.length > 0) {
            self.totalFilas = self.items.length;
          }
          self.$loader.close();
        })
        .catch(resp => {
          self.$loader.close();
          if (resp.data && resp.data.mensaje) {
            self.$toast.error(resp.data.mensaje);
          } else {
            self.$toast.error("No se pudo obtener los items");
          }
        });
    },
    guardar: function() {
      if(!this.validarCampos()) {
        return false
      }
      var self = this;
      self.$set(self.objeto, "token", window.localStorage.getItem("token"));
      self.objeto.visible = self.selectedVisble;
      this.$alertify
        .confirmWithTitle(
          "Guardar",
          "Seguro que desea guardar el nuevo registro?",
          function() {
            self.$loader.open({ message: "Guardando ..." });
            self.$http.post("ws/rol/", self.objeto).then(resp => {
                self.$loader.close();
                self.listar();
                self.$toast.success(resp.data.mensaje);
                self.showModal = false;
              })
              .catch(resp => {
                self.$loader.close();
                if (resp.data && resp.data.Error) {
                  self.$toast.error(resp.data.Error);
                } else {
                  self.$toast.error("error registrando");
                }
              });
          },
          function() {}
        )
        .set("labels", { ok: "Aceptar", cancel: "Cancelar" });
    },
    guardarCambios: function() {
      if(!this.validarCampos()) {
        return false
      }
      var self = this;
      self.$set(self.objeto, "token", window.localStorage.getItem("token"));
      self.objeto.visible = self.selectedVisble;
      this.$alertify
        .confirmWithTitle(
          "Modificar",
          "Seguro que desea modificar el registro?",
          function() {
            self.showModal = false;
            self.$loader.open({ message: "Guardando Cambios ..." });
            self.$http.put("ws/rol/", self.objeto).then(resp => {
                self.$loader.close();
                self.listar();
                self.$toast.success(resp.data.mensaje);
              })
              .catch(resp => {
                self.$loader.close();
                self.showModal = true;
                if (resp.data && resp.data.mensaje) {
                  self.$toast.error(resp.data.mensaje);
                } else {
                  self.$toast.error("error modificando");
                }
              });
          },
          function() {}
        )
        .set("labels", { ok: "Aceptar", cancel: "Cancelar" });
    },
    eliminar: function() {
      var self = this;
      let token = window.localStorage.getItem("token");
      self.$set(self.objeto, "token", token);
      this.$alertify
        .confirmWithTitle(
          "Eliminar",
          "Seguro que desea eliminar el registro?",
          function() {
            self.showModal = false;
            self.$loader.open({ message: "Eliminando ..." });
            self.$http.delete("ws/rol/", {params: {id: self.objeto.id, token: token}}).then(resp => {
                self.$loader.close();
                self.listar();
                self.$toast.success(resp.data.mensaje);
              })
              .catch(resp => {
                self.$loader.close();
                self.showModal = true;
                if (resp.data && resp.data.mensaje) {
                  self.$toast.error(resp.data.mensaje);
                } else {
                  self.$toast.error("error eliminando");
                }
              });
          },
          function() {}
        )
        .set("labels", { ok: "Aceptar", cancel: "Cancelar" });
    },
    cerrarFormulario: function() {
      this.showModal = false;
    },
    actualizarTabla: function(itemsFiltrados) {
      this.totalFilas = itemsFiltrados.length;
      this.paginaActual = 1;
    },
    guardarCambiosAsignar: function() {
      if (!this.objeto || !this.objeto.idAplicacion) {
        this.$toast.error("Debe seleccionar la aplicacion");
        return false
      }
      var self = this;
      self.objeto.visible = self.selectedVisble;
      this.$alertify
        .confirmWithTitle(
          "Modificar",
          "Seguro que desea modificar el registro?",
          function() {
            self.showModalAsignar = false;
            self.$loader.open({ message: "Guardando Cambios ..." });
            let frm = {
              idRol: self.objeto.id,
              funcionalidades: self.funcionalidadesSeleccionadas,
              idAplicacion: self.objeto.idAplicacion,
              token: window.localStorage.getItem("token")
            }
            self.$http.post("ws/rolfuncionalidad/", frm).then(resp => {
                self.$loader.close();
                self.listar();
                self.$toast.success(resp.data.mensaje);
              }).catch(resp => {
                self.$loader.close();
                self.showModalAsignar = true;
                if (resp.data && resp.data.mensaje) {
                  self.$toast.error(resp.data.mensaje);
                } else {
                  self.$toast.error("error modificando");
                }
              });
          },
          function() {}
        )
        .set("labels", { ok: "Aceptar", cancel: "Cancelar" });
    }
  },
  created: function() {
    this.listar();
    this.listarAplicaciones();
  },
  mounted: function() {
    this.$loader.close();
  }
};
</script>
