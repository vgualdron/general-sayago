<template>
	<b-container class="pt-3 pb-3 border-container">
		<b-row class="mb-3">
			<b-col>
				<b-form-input v-model="formato.nombre" size="lg" type="text" placeholder="Ingrese el titulo del formato"></b-form-input>
			</b-col>
		</b-row>
		<b-row class="mb-3">
			<b-col>
				<b-form-textarea v-model="formato.descripcion" class="font-weight-light" placeholder="Ingrese la descripción del formato" :rows="3" :max-rows="6"></b-form-textarea>
			</b-col>
		</b-row>
		<b-row>
			<b-col class="mb-3" cols="12" sm="6" v-if="tiposVisita && tiposVisita.length > 0">
				<b-form-select :disabled="!formato.esEditable" v-model="formato.idTipo">
					<option :value="null">
						Seleccione un Tipo de Visita
					</option>
					<option :value="tipoVisita.idTipoVisita" v-for="tipoVisita in tiposVisita" :key="tipoVisita.idTipoVisita">
						{{tipoVisita.nombre}}
					</option>
				</b-form-select>
			</b-col>
			<b-col class="mb-3" cols="12" sm="6" v-else>
				<b-alert variant="danger" show>No hay tipos de visita disponibles</b-alert>
			</b-col>
			<b-col class="mb-3" cols="12" sm="6" v-if="modalidades && modalidades.length > 0">
				<b-form-select :disabled="!formato.esEditable" v-model="formato.idModalidad">
					<option :value="null">
						Seleccione una Modalidad
					</option>
					<option :value="modalidad.idModalidad" v-for="modalidad in modalidades" :key="modalidad.idModalidad">
						{{modalidad.nombre}}
					</option>
				</b-form-select>
			</b-col>
			<b-col class="mb-3" cols="12" sm="6" v-else>
				<b-alert variant="danger" show>No hay modalidades disponibles</b-alert>
			</b-col>
		</b-row>
		<b-row>
			<b-col class="mb-3" cols="12" sm="6" v-if="estados && estados.length > 0">
				<b-form-select v-model="formato.estado">
					<option :value="null">
						Seleccione un estado
					</option>
					<option :value="estado" v-for="estado in estadosFormato" :key="estado">
						{{estado}}
					</option>
				</b-form-select>
			</b-col>
			<b-col class="mb-3" cols="12" sm="6" v-else>
				<b-alert variant="danger" show>No hay estados disponibles</b-alert>
			</b-col>
			<b-col class="mb-3" cols="12" sm="6" v-if="tiposFormato && tiposFormato.length > 0">
				<b-form-select :disabled="!formato.esEditable" v-model="formato.tipoFormato">
					<option :value="null">
						Seleccione un tipo
					</option>
					<option :value="tipo" v-for="tipo in tiposFormato" :key="tipo">
						{{tipo}}
					</option>
				</b-form-select>
			</b-col>
			<b-col class="mb-3" cols="12" sm="6" v-else>
				<b-alert variant="danger" show>No hay tipos de formato disponibles</b-alert>
			</b-col>
		</b-row>
		<b-row class="mb-3">
			<b-col cols="12">
				<span>
					¿Desea que el formato sea revisado?
				</span>
			</b-col>
			<b-col cols="12">
				<b-form-radio-group :disabled="!formato.esEditable" v-model="formato.esRevisable">
					<b-form-radio :value="true">Si</b-form-radio>
					<b-form-radio :value="false">No</b-form-radio>
				</b-form-radio-group>
			</b-col>
		</b-row>
		<b-row v-if="formato.esRevisable" class="mb-3">
			<b-col cols="12">
				<span>
					¿ El formato requiere ser firmado por los evaluadores ?
				</span>
			</b-col>
			<b-col cols="12">
				<b-form-radio-group :disabled="!formato.esEditable" v-model="formato.esFirmable">
					<b-form-radio :value="true">Si</b-form-radio>
					<b-form-radio :value="false">No</b-form-radio>
				</b-form-radio-group>
			</b-col>
		</b-row>
		<b-row>
			<b-col>
				<b-button size="sm" :disabled="!formato.esEditable" variant="primary" @click="agregarSeccion">
					Agregar Sección
				</b-button>
				<b-button size="sm" class="ml-3" :disabled="!formato.esEditable || !formato.secciones || formato.secciones.length <= 0" variant="danger" @click="confirmarEliminacionSeccion">
					Eliminar Sección
				</b-button>
			</b-col>
		</b-row>
		<b-row class="pt-3 pb-3" v-if="formato.secciones && formato.secciones.length > 0">
			<b-col>
				<b-card no-body>
					<b-tabs pills card v-model="seccionSeleccionada">
						<b-tab :title="(index + 1) + ''" v-for="(seccion, index) in formato.secciones" :key="index">
							<g-seccion :editable="formato.esEditable" :idSeccion="'seccion_' + (index + 1)" v-model="formato.secciones[index]"/>
						</b-tab>
					</b-tabs>
				</b-card>
			</b-col>
		</b-row>
		<b-row v-else>
			<b-col>
				<b-alert>No hay secciones gestionadas para el formato</b-alert>
			</b-col>
		</b-row>
	</b-container>
</template>
<script>
import GMsgBox from './GMsgBox'
export default {
  name: 'g-formato',
  props: ['value', 'estados', 'tipos'],
  data: function () {
    return {
      formato: this.value,
      estadosFormato: this.estados,
      tiposFormato: this.tipos,
      seccionSeleccionada: 0,
      colorSecciones: '#36a9e1',
      tiposVisita: [],
      modalidades: []
    }
  },
  created: function () {
    if (!this.formato) {
      this.formato = {
        nombre: '',
        descripcion: '',
        idTipo: null,
        idModalidad: null,
        esEditable: true,
        estado: null,
        tipoFormato: null,
        esRevisable: false,
        esFirmable: false,
        secciones: []
      }
    }
    this.listarTiposVisita()
    this.listarModalidades()
  },
  watch: {
    formato: function (nuevo, antiguo) {
      this.$emit('input', nuevo)
    }
  },
  methods: {
    agregarSeccion: function () {
      this.formato.secciones.push({
        nombre: '',
        preguntas: [],
        tieneHallazgo: false,
        esOperador: false
      })
      this.$nextTick(() => {
        this.seccionSeleccionada = this.formato.secciones.length - 1
      })
    },
    listarTiposVisita: function () {
      this.$http.get('ws/general/tipos_visita').then(resp => {
        this.tiposVisita = resp.data
      }).catch(resp => {})
    },
    listarModalidades: function () {
      this.$http.get('ws/general/lista_modalidades', {params: {modalidadTodos: true}}).then(resp => {
        this.modalidades = resp.data
      }).catch(resp => {})
    },
    confirmarEliminacionSeccion: function () {
      var seccion = this.formato.secciones[this.seccionSeleccionada]
      if (seccion) {
        GMsgBox.confirm({
          message: '¿ Está seguro de eliminar la sección ' + (this.seccionSeleccionada + 1) + (seccion.nombre ? '( ' + seccion.nombre + ') ?' : ' ?'),
          onOk: () => {
            this.eliminarSeccion()
          }
        })
      }
    },
    eliminarSeccion: function () {
      this.formato.secciones.splice(this.seccionSeleccionada, 1)

      if (this.formato.secciones.length > 0 && ((this.seccionSeleccionada - 1) > 0)) {
        this.seccionSeleccionada--
      } else {
        this.seccionSeleccionada = 0
      }
    }
  }
}
</script>
<style scoped>
	.border-container {
		border:1px solid rgb(224, 224, 224);
	}
</style>
