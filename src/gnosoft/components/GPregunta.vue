<template>
	<b-container class="pt-3 pb-3 border-container">
		<b-row class="mb-3">
			<b-col>
				<b-form-input :disabled="!editable" v-model="pregunta.descripcion" type="text" placeholder="Pregunta"></b-form-input>
			</b-col>
		</b-row>
		<b-row class="mb-3" align-v="center">
			<b-col>
				<b-form-checkbox :disabled="!editable" v-model="pregunta.tieneHallazgo">
					<span class="font-weight-bold">¿ Tiene hallazgo ?</span>
				</b-form-checkbox>
			</b-col>
		</b-row>
		<b-row class="mb-3" align-v="center">
			<b-col cols="12" md="3">
				<span class="font-weight-bold">Estrategia para hallazgo</span>
			</b-col>
			<b-col cols="12" md="6">
				<b-form-input :disabled="!editable" v-model="pregunta.estrategiaHallazgo" type="text" placeholder="Ej: co.com.gnosoft.pae.estrategia ..."></b-form-input>
			</b-col>
		</b-row>
		<b-row class="mb-3" align-v="center">
			<b-col cols="12" md="3">
				<span class="font-weight-bold">Tipo de Respuesta</span>
			</b-col>
			<b-col cols="12" md="6">
				<b-form-select :disabled="!editable" v-model="pregunta.tipo">
					<option v-for="tipo in tiposPregunta" :key="tipo.valor" :value="tipo.valor">{{tipo.nombre}}</option>
				</b-form-select>
			</b-col>
		</b-row>
		<template v-if="pregunta.tipo && pregunta.tipo === 'CERRADA'">

			<template class="mt-3 mb-3" v-if="pregunta.escalaFormato">
				<g-opcion :editable="editable" :grupo="id + '_opcion_' + i" :tipo="pregunta.escalaFormato.tipo" v-for="(opcion, i) in pregunta.escalaFormato.opciones" :key="i" v-model="pregunta.escalaFormato.opciones[i]"/>
			</template>

			<b-row class="mb-3" v-else-if="mensajeError">
				<b-col>
					<b-alert show variant="danger">{{mensajeError}}</b-alert>
				</b-col>
			</b-row>
			<b-row>
				<b-col class="text-center">
					<b-button :disabled="!editable" variant="primary" size="sm" @click="cargarEscalas">{{botonEscala}}</b-button>
				</b-col>
			</b-row>
		</template>

		<!-- Formulario para seleccion de escalas -->
		<b-modal v-model="formularioEscalas">
			<div slot="modal-title">
              <p class="font-weight-light mb-1" style="font-size:20px;">
                Escalas
              </p>
            </div>
            <b-container>
            	<b-row class="mb-3">
            		<b-col>
            			<b-form-select v-model="pregunta.escalaFormato" @change="cargarOpciones">
            				<option :value="null">Selecciona una escala de la lista</option>
            				<option :value="escala" v-for="escala in escalasFormato" :key="escala.idEscalaFormato">{{escala.nombre}}</option>
            			</b-form-select>
            		</b-col>
            	</b-row>
            	<template v-if="pregunta.escalaFormato">
            		<b-row class="mb-2">
	            		<b-col>
	            			<span class="font-weight-bold">Tipo: </span>{{pregunta.escalaFormato.tipo}}
	            		</b-col>
            		</b-row>
            		<b-row class="mb-2">
	            		<b-col>
	            			<span class="font-weight-bold">¿Crea Caps?: </span>
	            			<span v-if="pregunta.escalaFormato.tieneCaps">SI</span>
	            			<span v-else>NO</span>
	            		</b-col>
            		</b-row>
            		<b-row v-if="pregunta.escalaFormato.opciones && pregunta.escalaFormato.opciones.length > 0">
            			<b-col>
            				<div v-if="pregunta.escalaFormato.tipo === 'UNICA'">
            					<template v-for="(opcion, i) in pregunta.escalaFormato.opciones">
            						<label class="container-radio">{{opcion.descripcion}} (<span class="font-weight-bold">Valor: </span> {{opcion.valor}})
								  	<input type="radio" name="radio" class="text-justify" :value="opcion.idOpcion">
									<span class="checkmark-radio"></span>
								</label>
            					</template>
            				</div>
            				<div v-else>
            					<template v-for="(opcion, i) in pregunta.escalaFormato.opciones">
            						<label class="container-checkbox">{{opcion.descripcion}} (<span class="font-weight-bold">Valor: </span> {{opcion.valor}})
								  	<input type="checkbox" name="checkbox" class="text-justify" :value="opcion.idOpcion">
									<span class="checkmark-checkbox"></span>
								</label>
            					</template>
            				</div>
            			</b-col>
            		</b-row>
            		<b-row v-else>
            			<b-col>
            				<b-alert show variant="danger">No tiene opciones configuradas.</b-alert>
            			</b-col>
            		</b-row>
            	</template>
            </b-container>
            <div slot="modal-footer" class="pull-right">
	          <b-btn size="sm" variant="secondary" @click="formularioEscalas = false">
	            Cerrar
	          </b-btn>
        	</div>
		</b-modal>

	</b-container>
</template>
<script>
export default {
  name: 'g-pregunta',
  props: ['value', 'idPregunta', 'editable'],
  data: function () {
    return {
      id: this.idPregunta,
      pregunta: this.value,
      mensajeError: '',
      botonEscala: '',
      tiposPregunta: [],
      escalasFormato: [],
      formularioEscalas: false
    }
  },
  watch: {
    pregunta: function (nuevo, antiguo) {
      this.$emit('input', nuevo)
    }
  },
  created: function () {
    if (!this.pregunta) {
      this.pregunta = {
        descripcion: '',
        tipo: '',
        tieneHallazgo: false,
        estrategiaHallazgo: '',
        escalaFormato: null
      }
    }
    this.listarTiposPregunta()
    this.botonEscala = this.pregunta.escalaFormato ? 'Cambiar Opciones' : 'Agregar Opciones'
    this.listarEscalasFormato()
  },
  methods: {
    listarEscalasFormato: function () {
      this.$loader.open({message: 'Cargando Escalas...'})
      this.$http.get('ws/general/lista_escalas_formato').then(resp => {
        this.$loader.close()
        this.escalasFormato = resp.data
      }).catch(resp => {
        this.$loader.close()
        if (resp.data && resp.data.error) {
		              this.mensajeError = resp.data.error
		            } else {
		              this.mensajeError = 'Ocurrió un error al listar las escalas.'
		            }
      })
    },
    listarTiposPregunta: function () {
      this.$http.get('ws/general/lista_tipos_pregunta').then(resp => {
        this.tiposPregunta = resp.data
      }).catch(resp => {
        if (resp.data && resp.data.error) {
          				this.$toast.error(resp.data.error)
        			} else {
          				this.$toast.error('Error al listar los tipos de pregunta')
        			}
      })
    },
    cargarEscalas: function () {
      this.formularioEscalas = true
    },
    cargarOpciones: function () {
      this.$nextTick(() => {
        if (this.pregunta.escalaFormato) {
          this.botonEscala = 'Cambiar Opciones'
          if (!this.pregunta.escalaFormato.opciones) {
            this.listarOpciones(this.pregunta.escalaFormato.idEscalaFormato)
          }
        } else {
          this.botonEscala = 'Agregar Opciones'
        }
      })
    },
    listarOpciones: function (idEscalaFormato) {
      this.$loader.open('Cargando Escalas...')
      this.$http.get('ws/general/lista_opciones', {
        params: {
          idEscalaFormato: idEscalaFormato
        }
      }).then(resp => {
        this.$loader.close()
        this.$set(this.pregunta.escalaFormato, 'opciones', resp.data)
        this.formularioEscalas = true
      }).catch(resp => {
        this.$loader.close()
        this.formularioEscalas = true
      })
    }
  }
}
</script>
<style scoped>
	.border-container {
		border:1px solid rgb(224, 224, 224);
	}
	  /* The container */
	.container-radio {
		text-align: left;
		display: block;
		position: relative;
		padding-left: 35px;
		margin-bottom: 12px;
		cursor: pointer;
		font-size: 15px;
		-webkit-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;
	}

	/* Hide the browser's default radio button */
	.container-radio input {
		position: absolute;
		opacity: 0;
		cursor: pointer;
	}

	/* Create a custom radio button */
	.checkmark-radio {
		margin-top: 3px;
		position: absolute;
		top: 0;
		left: 0;
		height: 16px;
		width: 16px;
		background-color: #ccc;
		border-radius: 50%;
	}

	/* On mouse-over, add a grey background color */
	.container-radio:hover input ~ .checkmark-radio {
		background-color: rgba(136, 128, 128, 0.411);
	}

	/* When the radio button is checked, add a blue background */
	.container-radio input:checked ~ .checkmark-radio {
		background-color: #2196F3;
	}

	/* Create the indicator (the dot/circle - hidden when not checked) */
	.checkmark-radio:after {
		content: "";
		position: absolute;
		display: none;
	}

	/* Show the indicator (dot/circle) when checked */
	.container-radio input:checked ~ .checkmark-radio:after {
		display: block;
	}

	/* Style the indicator (dot/circle) */
	.container-radio .checkmark-radio:after {
	 	top: 5px;
		left: 5px;
		width: 6px;
		height: 6px;
		border-radius: 50%;
		background: white;
	}

	/* The container */
	.container-checkbox {
		text-align: left;
		display: block;
		position: relative;
		padding-left: 35px;
		margin-bottom: 12px;
		cursor: pointer;
		font-size: 15px;
		-webkit-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;
	}

	/* Hide the browser's default checkbox */
	.container-checkbox input {
		position: absolute;
		opacity: 0;
		cursor: pointer;
	}

	/* Create a custom checkbox */
	.checkmark-checkbox {
		margin-top: 3px;
		position: absolute;
		top: 0;
		left: 0;
		height: 15px;
		width: 15px;
		background-color: #ccc;
		border-radius: 10%;
	}

	/* On mouse-over, add a grey background color */
	.container-checkbox:hover input ~ .checkmark-checkbox {
		background-color: rgba(136, 128, 128, 0.411);
	}

	/* When the checkbox is checked, add a blue background */
	.container-checkbox input:checked ~ .checkmark-checkbox {
		background-color: #2196F3;
	}

	/* Create the checkmark/indicator (hidden when not checked) */
	.checkmark-checkbox:after {
		content: "";
		position: absolute;
		display: none;
	}

	/* Show the checkmark when checked */
	.container-checkbox input:checked ~ .checkmark-checkbox:after {
		display: block;
	}

	/* Style the checkmark/indicator */
	.container-checkbox .checkmark-checkbox:after {
		left: 5px;
		top: 2px;
		width: 5px;
		height: 9px;
		border: solid white;
		border-width: 0 3px 3px 0;
		-webkit-transform: rotate(45deg);
		-ms-transform: rotate(45deg);
		transform: rotate(45deg);
	}
	.descripcion-cap {
		margin-bottom:15px;
		display: none;
	}
</style>
