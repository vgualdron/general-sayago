<template>
  <div>
    <b-row>
      <b-col cols="12">
        <b-card border-variant="primary" header-bg-variant="primary" :header="'<strong>Gestionar Cuentas</strong> (' + totalFilas + ')'" tag="article" class="m-3 mt-3">
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
                class="columna-centrada"
                v-if="items && items.length > 0"
                stacked="md"
                striped
                hover
                :fields="campos"
                :items="items"
                :current-page="paginaActual"
                :per-page="porPagina"
                :filter="filtro"
                @filtered="actualizarTabla"
              >
                <template slot="acciones" slot-scope="row">
                  <b-button
                    style="margin: 1px;"
                    size="sm"
                    @click.stop="cargarFormulario(row.item,'Ver')"
                  >
                    <i class="icon-eye"></i>
                  </b-button>
                  <b-button
                    style="margin: 1px;"
                    size="sm"
                    variant="primary"
                    @click.stop="cargarFormulario(row.item,'Modificar')"
                  >
                    <i class="icon-pencil"></i>
                  </b-button>
                  <b-button
                    style="margin: 1px;"
                    size="sm"
                    variant="danger"
                    @click.stop="cargarFormulario(row.item,'Eliminar')"
                  >
                    <i class="fa fa-trash"></i>
                  </b-button>
                  <b-button
                    style="margin: 1px;"
                    size="sm"
                    variant="danger"
                    title="Asignar Roles a Usuario"
                    @click.stop="abrirModalAsignarRoles(row.item,'Asignar')">
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
                  class="text-left">
                  <span class="font-weight-bold">
                    <span style="color:red;"> </span>Nombre de la persona
                  </span>:<br>
                  <span>
                    {{objeto.primernombre}} {{objeto.segundonombre}} {{objeto.primerapellido}} {{objeto.segundoapellido}}
                  </span>
                </b-col>
              </b-row>
              <b-row class="mb-3">
                <b-col
                  class="text-left">
                  <span class="font-weight-bold">
                    <span style="color:red;">* </span>Cuenta
                  </span>:<br>
                  <span v-if="(tipoOperacion === 'Ver' || tipoOperacion === 'Eliminar')">
                    {{objeto.cuenta}}
                  </span>
                  <b-form-input
                    v-else
                    type="text"
                    required
                    v-model="objeto.cuenta"
                    class="form-control"/>
                </b-col>
              </b-row>
              <b-row class="mb-3">
                <b-col
                  class="text-left">
                  <span class="font-weight-bold">
                    <span style="color:red;">* </span>Clave
                  </span>:<br>
                  <span v-if="(tipoOperacion === 'Ver' || tipoOperacion === 'Eliminar')">
                    {{objeto.clave}}
                  </span>
                  <b-form-input
                    v-else
                    type="password"
                    required
                    v-model="objeto.clave"
                    class="form-control"/>
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


          <b-modal v-if="objeto" centered v-model="showModalAsignar" title="Asignar Rol a Usuario">
            <b-container>
              <b-row class="mb-3">
                <b-col
                  class="text-left">
                  <span class="font-weight-bold">
                    <span style="color:red;"></span>Cuenta / Persona
                  </span>:
                  <br>
                  {{objeto.cuenta}} / {{objeto.primernombre}} {{objeto.segundornombre}} {{objeto.primerapellido}} {{objeto.segundoapellido}}
                </b-col>
              </b-row>


              <b-row class="mb-3">
                <b-col class="text-left">
                  <span class="font-weight-bold">
                    <span style="color:red;">*</span>Roles
                  </span>:
                  <b-form-group>
                    <b-form-checkbox-group
                      stacked
                      v-model="rolesSeleccionados"
                      id="checkboxes1"
                      name="check-roles">
                      <b-form-checkbox :key="'rol_' + r" v-for="(rol, r) in roles" :value="rol">{{rol.descripcion}}</b-form-checkbox>
                    </b-form-checkbox-group>  
                  </b-form-group>
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
  name: "Usuario",
  data: function() {
    return {
      campos: [
        {
          key: "documento",
          label: "Documento",
          sortable: true,
          thStyle: "text-align:center;",
          tdClass: "columna-centrada"
        },
        {
          key: "cuenta",
          label: "Cuenta",
          sortable: true,
          thStyle: "text-align:center;",
          tdClass: "columna-centrada"
        },
        {
          key: "primernombre",
          label: "Primer Nombre",
          sortable: true,
          thStyle: "text-align:center;",
          tdClass: "columna-centrada"
        },
        {
          key: "primerapellido",
          label: "Primer Apellido",
          sortable: true,
          thStyle: "text-align:center;"
        },
        {
          key: "segundoapellido",
          label: "Segundo Apellido",
          sortable: true,
          thStyle: "text-align:center;"
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
      totalFilas: 0,
      filtro: "",
      porPagina: 20,
      paginaActual: 1,
      roles: [],
      rolesSeleccionados: []
    };
  },
  methods: {
    cargarFormulario: function(obj, operacion) {
      this.tipoOperacion = operacion;
      this.objeto = obj;
      if (obj === null) {
        this.objeto = {
          id: null,
          cuenta: null,
          clave: null,
          idpersonageneral: null,
          primernombre: null,
          segundonombre: null,
          primerapellido: null,
          segundoapellido: null
        };
      } else {
        this.objeto = {
          id: obj.id,
          cuenta: obj.cuenta,
          clave: obj.clave,
          idpersonageneral: obj.idpersonageneral,
          primernombre: obj.primernombre,
          segundonombre: obj.segundonombre,
          primerapellido: obj.primerapellido,
          segundoapellido: obj.segundoapellido
        };
      }
      this.showModal = true;
    },
    abrirModalAsignarRoles: function(obj, operacion) {
      this.tipoOperacion = operacion;
      this.objeto = obj;
      this.listarRolesSeleccionados(this.objeto.id)
      this.showModalAsignar = true;
    },
    validarCampos: function() {
      if (!this.objeto.cuenta || this.objeto.cuenta.trim().length < 1) {
        this.$toast.error("Debe escribir la cuenta");
        return false;
      }
      if (!this.objeto.clave || this.objeto.clave.trim().length < 1) {
        this.$toast.error("Debe de escribir la clave");
        return false;
      }
      return true;
    },
    listarRoles: function() {
      this.$loader.open({ message: "Cargando ..." });
      var self = this;
      this.$http.get("ws/rol/").then(resp => {
          self.roles = resp.data
          self.$loader.close();
        })
        .catch(resp => {
          self.$loader.close();
          if (resp.data && resp.data.mensaje) {
            self.$toast.error(resp.data.mensaje);
          } else {
            self.$toast.error("No se pudo obtener los items de roles");
          }
        });
    },
    listarRolesSeleccionados: function(idUsuario) {
      this.$loader.open({ message: "Cargando ..." });
      var self = this;
      this.$http.get("ws/rol/?idUsuario=" + idUsuario).then(resp => {
          self.rolesSeleccionados = resp.data
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
      this.$http.get("ws/cuentausuario/", frm).then(resp => {
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
      self.$loader.open({ message: "Guardando ..." });
      self.$http.post("ws/cuentausuario/", self.objeto).then(resp => {
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
    guardarCambios: function() {
      if(!this.validarCampos()) {
        return false
      }
      var self = this;
      self.$set(self.objeto, "token", window.localStorage.getItem("token"));
      this.$alertify
        .confirmWithTitle(
          "Modificar",
          "Seguro que desea modificar el registro?",
          function() {
            self.showModal = false;
            self.$loader.open({ message: "Guardando Cambios ..." });
            self.$http.put("ws/cuentausuario/", self.objeto).then(resp => {
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
      self.$set(self.objeto, "token", window.localStorage.getItem("token"));
      this.$alertify
        .confirmWithTitle(
          "Eliminar",
          "Seguro que desea eliminar el registro?",
          function() {
            self.showModal = false;
            self.$loader.open({ message: "Eliminando ..." });
            self.$http.delete("ws/cuentausuario/", {params: {id: self.objeto.id, token: token}}).then(resp => {
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
    guardarCambiosAsignar: function() {
      var self = this;
      this.$alertify
        .confirmWithTitle(
          "Modificar",
          "Seguro que desea modificar el registro?",
          function() {
            self.showModalAsignar = false;
            self.$loader.open({ message: "Guardando Cambios ..." });
            let frm = {
              idusuario: self.objeto.id,
              roles: self.rolesSeleccionados,
              token: window.localStorage.getItem("token")
            }
            self.$http.post("ws/usuariorol/", frm).then(resp => {
                self.$loader.close();
                self.listar();
                self.$toast.success(resp.data.mensaje);
              }).catch(resp => {
                self.$loader.close();
                self.showModalAsignar = true;
                if (resp.data && resp.data.mensaje) {
                  self.$toast.error(resp.data.mensaje);
                } else {
                  self.$toast.error("error modificando roles");
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
    }
  },
  created: function() {
    this.listar();
    this.listarRoles();
  },
  mounted: function() {
    this.$loader.close();
  }
};
</script>