<template>
  <div class="contenedor">
    <b-button variant="primary" v-if="boton" v-bind:class="botonClases" @click="visualizarCalendario">
      <i class="icon-calendar"></i>
    </b-button>
    <div v-else>
      <div class="p-2 selector-fecha" @click="visualizarCalendario" style="display:inline-block;">
        <span v-if="fechaFiltro">{{fechaFiltro}}
        </span><span v-else>Seleccione una fecha</span>
      </div>
      <b-button variant="primary" size="sm" @click="reiniciarFiltro" style="display:inline-block;">
        <i class="icon-action-undo"></i>
      </b-button>
    </div>
    <div ref="contenedorCalendario" class="contenedor-calendario mt-1">
      <div id="calendario-encabezado" class="mt-3">
        <div id="flecha-izquierda" @click="actualizarMes(false)">
          <i class="icon-arrow-left"></i>
        </div>
        <div id="mes-seleccionado" class="text-center">
          <p class="color-texto-oscuro m-0">
            {{mesSeleccionado}}
          </p>
        </div>
        <div id="flecha-derecha" @click="actualizarMes(true)">
          <i class="icon-arrow-right"></i>
        </div>
      </div>
      <div id="calendario-cuerpo color-texto-oscuro">
        <div id="nombre-dias">
          <span v-for="(dia,index) in diasNombre" :key="index + '_' + dia">
            {{dia}}
          </span>
        </div>
        <div id="numero-dias">
          <div @click="actualizarFiltro('anterior_' + dia)" class="dia color-texto-deshabilitado" v-for="dia in diasAnteriores" :key="'anterior_' + dia">
            {{dia}}
          </div>
          <div @click="actualizarFiltro(dia)" class="dia" v-for="dia in numeroDias" :key="'actual_' + dia">
            {{dia}}
          </div>
          <div @click="actualizarFiltro('proximo_' + dia)" class="dia color-texto-deshabilitado" v-for="dia in diasProximos" :key="'proximo_' + dia">
            {{dia}}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: 'GCalendario',
  props: {
    botonClases: {
      type: String,
      default: ''
    },
    boton: {
      type: Boolean,
      default: true
    }
  },
  data: function () {
    return {
      fecha: new Date(),
      fechaFiltro: '',
      mesSeleccionado: '',
      mesNombre: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
      diasNombre: ['Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa', 'Do'],
      diasAnteriores: [],
      numeroDias: 30,
      diasProximos: []
    }
  },
  created: function () {
    if (this.fecha) {
      this.mesSeleccionado = this.mesNombre[this.fecha.getMonth()] + ' ' + this.fecha.getFullYear()
    } else {
      this.mesSeleccionado = this.mesNombre[new Date().getMonth()] + ' ' + new Date().getFullYear()
    }
    var fechaActual = new Date()
    this.numeroDias = new Date(fechaActual.getFullYear(), fechaActual.getMonth() + 1, 0).getDate()
    // Agregar dias sobrantes del mes anterior
    fechaActual.setDate(1)
    this.agregarDiasMesAnterior(fechaActual)
    // Agregar dias sobrantes del mes siguiente
    fechaActual = new Date()
    fechaActual = new Date(fechaActual.getFullYear(), fechaActual.getMonth() + 1, 0)
    this.agregarDiasMesProximo(fechaActual)
  },
  methods: {
    visualizarCalendario: function () {
      var elemento = this.$refs.contenedorCalendario
      if (elemento && elemento.style.display === 'block') {
        elemento.style.display = 'none'
      } else {
        elemento.style.display = 'block'
      }
    },
    actualizarMes: function (direccion) {
      if (direccion) {
        this.fecha.setMonth(this.fecha.getMonth() + 1)
      } else {
        this.fecha.setMonth(this.fecha.getMonth() - 1)
      }
      this.mesSeleccionado = this.mesNombre[this.fecha.getMonth()] + ' ' + this.fecha.getFullYear()
      this.numeroDias = new Date(this.fecha.getFullYear(), this.fecha.getMonth() + 1, 0).getDate()
      // Agregar dias sobrantes del mes anterior
      var fechaSobrantes = new Date(this.fecha.getFullYear(), this.fecha.getMonth(), 1)
      this.agregarDiasMesAnterior(fechaSobrantes)
      // Agregar dias sobrantes del mes siguiente
      fechaSobrantes = new Date(this.fecha.getFullYear(), this.fecha.getMonth() + 1, 0)
      this.agregarDiasMesProximo(fechaSobrantes)
    },
    reiniciarFiltro: function () {
      this.fechaFiltro = ''
      this.$emit('input', this.fechaFiltro)
      this.$emit('fecha-seleccionada')
    },
    agregarDiasMesAnterior: function (fecha) {
      var diaInicioMes = fecha.getDay()
      var diasMesAnterior = new Date(fecha.getFullYear(), fecha.getMonth(), 0).getDate()
      var i
      this.diasAnteriores = []
      if (diaInicioMes <= 0) {
        diaInicioMes = 7
      }
      for (i = 0; i < diaInicioMes - 1; i++) {
        this.diasAnteriores.unshift(diasMesAnterior)
        diasMesAnterior--
      }
    },
    agregarDiasMesProximo: function (fecha) {
      var i
      if (fecha) {
        var diaFinalMes = fecha.getDay()
        if (diaFinalMes <= 0) {
          diaFinalMes = 7
        }
        this.diasProximos = []
        for (i = 0; i < (7 - diaFinalMes); i++) {
          this.diasProximos.push(i + 1)
        }
      }
    },
    actualizarFiltro: function (diaFiltro) {
      if (diaFiltro) {
        var mesSeleccionado
        var diaSeleccionado
        var dia = String(diaFiltro).split('_')
        if (dia.length > 1) {
          if (dia[0] === 'anterior') {
            mesSeleccionado = this.fecha.getMonth() < 10 ? '0' + this.fecha.getMonth() : this.fecha.getMonth()
          } else {
            mesSeleccionado = ((this.fecha.getMonth() + 2) < 10 ? '0' + (this.fecha.getMonth() + 2) : (this.fecha.getMonth() + 2))
          }
          diaSeleccionado = (dia[1] < 10 ? '0' + dia[1] : dia[1])
        } else {
          mesSeleccionado = ((this.fecha.getMonth() + 1) < 10 ? '0' + (this.fecha.getMonth() + 1) : (this.fecha.getMonth() + 1))
          diaSeleccionado = (diaFiltro < 10 ? '0' + diaFiltro : diaFiltro)
        }
        this.fechaFiltro = this.fecha.getFullYear() + '-' + mesSeleccionado + '-' + diaSeleccionado
        var self = this
        this.$emit('input', self.fechaFiltro)
        this.$emit('fecha-seleccionada')
        this.visualizarCalendario()
      }
    }
  }
}
</script>
<style scoped>
  .contenedor-calendario {
    display: none;
    position:absolute;
    left:50%;
    transform:translate(-50%,0);
    z-index:1000;
    width:220px;
    background-color:white;
    border:1px solid rgb(224, 224, 224);
    -webkit-box-shadow: 0px 4px 25px -6px rgba(0,0,0,0.75);
    -moz-box-shadow: 0px 4px 25px -6px rgba(0,0,0,0.75);
    box-shadow: 0px 4px 25px -6px rgba(0,0,0,0.75);
  }
  .color-texto-oscuro {
    color:#2c3e50;
  }
  .color-texto-deshabilitado {
    color:#9e9e9e;
  }
  .contenedor {
    position:relative;
    display:inline-block;
  }
  .dia {
    width:28px;
    cursor:pointer;
    text-align:center;
    float:left;
    line-height: 28px;
    height:28px;
    font-size: 14px;
  }
  .dia:hover {
    background-color:#dfe6e9;
  }
  #calendario-encabezado > div{
    display:inline-block;
  }
  #flecha-derecha {
    float:right;
    width:10%;
    cursor: pointer;
  }
  #mes-seleccionado {
    width:80%;
  }
  #nombre-dias {
    font-weight:700;
    padding:10px;
  }
  #numero-dias {
    padding:10px;
  }
  #nombre-dias > span {
    width:28px;
    text-align:center;
    float:left;
    line-height: 28px;
    height:28px;
    font-size: 14px;
  }
  #flecha-izquierda {
    float:left;
    width:10%;
    cursor: pointer;
  }
  .selector-fecha {
    border:1px solid rgba(60, 60, 60, 0.26);
    border-radius:0.25rem;
    background-color:white;
    cursor:pointer;
    width:160px;
    color:#3e515b;
    text-align:center;
  }
</style>
