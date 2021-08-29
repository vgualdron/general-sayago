<template>
	<b-container>
		<b-row class="mb-3">
			<b-col>
				<b-form-input :disabled="!editable" v-model="seccion.nombre" size="lg" type="text" placeholder="Ingrese el titulo de la sección"></b-form-input>
			</b-col>
		</b-row>
		<b-row class="mb-3">
			<b-col>
				<b-form-checkbox :disabled="!editable" v-model="seccion.tieneHallazgo">
					¿ Tiene Hallazgo ?
				</b-form-checkbox>
			</b-col>
		</b-row>
		<b-row class="mb-3">
			<b-col>
				<b-form-checkbox :disabled="!editable" v-model="seccion.esOperador">
					¿ Sección del Operador ?
				</b-form-checkbox>
			</b-col>
		</b-row>
		<b-row class="mb-4">
			<b-col>
				<template v-for="(pregunta, i) in seccion.preguntas">
					<b-row align-h="center" class="encabezado_pregunta mb-2" v-if="seccion.preguntas[i].esVisible" :key="'encabezado_' + i">
						<b-col class="text-center">
							<div style="margin:auto;">
								<span class="font-weight-bold">{{pregunta.orden}}</span>
								|
							<b-button :disabled="!editable" @click="eliminarPregunta(i)" size="sm" variant="danger">		<i class="fa fa-trash"></i>
							</b-button>
							</div>

						</b-col>
					</b-row>
					<b-row v-if="seccion.preguntas[i].esVisible" class="mb-3" :key="i">
						<b-col cols="1" class="pregunta-contenedores">
							<span class="font-weight-bold">{{pregunta.orden}}</span>
						</b-col>
						<b-col cols="12" sm="10">
							<g-pregunta :editable="editable" :idPregunta="id + '_pregunta_' + i" v-model="seccion.preguntas[i]" :key="i"/>
						</b-col>
						<b-col cols="1" class="pregunta-contenedores">
							<b-button :disabled="!editable" @click="eliminarPregunta(i)" style="margin-left:10px !important;" size="sm" variant="danger"><i class="fa fa-trash"></i></b-button>
						</b-col>
					</b-row>
				</template>
			</b-col>
		</b-row>
		<b-row>
			<b-col class="text-center">
				<b-button size="sm" :disabled="!editable" variant="primary" @click="agregarPregunta(seccion)">Agregar Pregunta</b-button>
			</b-col>
		</b-row>
	</b-container>
</template>
<script>
export default {
  name: '',
  props: ['value', 'idSeccion', 'editable'],
  data: function () {
    return {
      id: this.idSeccion,
      seccion: this.value
    }
  },
  created: function () {
    if (!this.seccion) {
      this.seccion = {
        nombre: '',
        tieneHallazgo: false,
        esOperador: false,
        preguntas: []
      }
    }
  },
  watch: {
    seccion: function (nuevo, antiguo) {
      this.$emit('input', nuevo)
    }
  },
  methods: {
    agregarPregunta: function (seccion) {
      seccion.preguntas.push({
        orden: this.obtenerOrdenPregunta(),
        descripcion: '',
        tipoPregunta: '',
        esVisible: true,
        tieneHallazgo: false,
        estrategiaHallazgo: '',
        escalaFormato: null
      })
    },
    eliminarPregunta: function (indice) {
      this.seccion.preguntas[indice].esVisible = false
      this.actualizarOrdenPreguntas()
    },
    hayPreguntasVisibles: function () {
      if (this.seccion.preguntas.length > 0) {
        this.seccion.preguntas.forEach(function (pregunta) {
          if (pregunta.esVisible) {
            return true
          }
        })
      }
      return false
    },
    actualizarOrdenPreguntas: function () {
      var i = 1
      this.seccion.preguntas.forEach(function (pregunta) {
        if (pregunta.esVisible) {
          pregunta.orden = i
          i++
        } else {
          pregunta.orden = -1
        }
      })
    },
    obtenerOrdenPregunta: function () {
      var preguntasFiltradas = this.seccion.preguntas.filter(function (pregunta) {
        return pregunta.esVisible === true
      })
      if (preguntasFiltradas.length > 0) {
        return preguntasFiltradas.length + 1
      }
      return 1
    }
  }
}
</script>
<style>
	@media (max-width:576px) {
		.encabezado_pregunta {
			display:block !important;
		}
		.pregunta-contenedores {
			display:none !important;
		}
	}
	.encabezado_pregunta {
		display:none;
	}
</style>
