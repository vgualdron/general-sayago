import TCRender from './TCRender'

class Layout {
  getLayout = function (campo) {
    if (campo === null) {
      return null
    }
    if (campo.configuracion == null) {
      return 'simple'
    } else if (campo.configuracion.layout == null) {
      return 'simple'
    }
    return campo.configuracion.layout
  }
  getEditableEstadoEn = function (campo) {
    if (campo === null) {
      return null
    }
    if (campo.configuracion == null) {
      return null
    } else if (campo.configuracion.editableEn == null) {
      return null
    }
    return campo.configuracion.editableEn
  }
  getEditableFuncionalidadEn = function (campo) {
    if (campo === null) {
      return null
    }
    if (campo.configuracion == null) {
      return null
    } else if (campo.configuracion.editableFuncionalidadEn == null) {
      return null
    }
    return campo.configuracion.editableFuncionalidadEn
  }
  doLayout = function (reporte) {
    var templ = ''
    if (reporte.campos) {
      templ = '<div>'
      templ += '<h4 class="">' + reporte.nombre + '</h4>'
      templ += '<b-card>'
      for (let j = 0; j < reporte.campos.length; j++) {
        let campo = reporte.campos[j]
        let sig = reporte.campos.length > (j + 1) ? reporte.campos[j + 1] : null
        let layoutCampo = this.getLayout(campo)
        let sigLayout = this.getLayout(sig)

        if (layoutCampo === 'doble') {
          templ += '<div class="row mb-2">'
          templ += '<div class="col-sm-2"><strong>' + campo.etiqueta + '</strong></div>'
          templ += '<div class="col-sm-10">' + TCRender.render(campo, j) + '</div>'
          templ += '</div>'
        } else if (layoutCampo === 'extra') {
          templ += '<div class="row mb-2">'
          templ += '<div class="col-sm-12"><strong>' + campo.etiqueta + '</strong></div>'
          templ += '<div class="col-sm-12">' + TCRender.render(campo, j) + '</div>'
          templ += '</div>'
        } else if (sig == null || sigLayout !== 'simple') {
          templ += '<div class="row mb-2">'
          templ += '<div class="col-sm-2"><strong>' + campo.etiqueta + '</strong></div>'
          templ += '<div class="col-sm-4">' + TCRender.render(campo, j) + '</div>'
          templ += '</div>'
        } else if (sig != null) {
          templ += '<div class="row mb-2">'
          templ += '<div class="col-sm-2"><strong>' + campo.etiqueta + '</strong></div>'
          templ += '<div class="col-sm-4">' + TCRender.render(campo, j) + '</div>'
          templ += '<div class="col-sm-2"><strong>' + sig.etiqueta + '</strong></div>'
          templ += '<div class="col-sm-4">' + TCRender.render(sig, j + 1) + '</div>'
          templ += '</div>'
          j++
        }
      }
      templ += '</b-card>'

      templ += '  <br>'

      templ += '</div>'
    }
    return templ
  }
}

export default Layout
