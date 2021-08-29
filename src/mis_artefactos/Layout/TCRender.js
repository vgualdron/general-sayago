class TCRender {
  static render (parametro) {
    return '<' + parametro.tipoEdicion.toLowerCase() +
            ' id="' + parametro.idCampoReporte + '"' +
            ' :manager="getManager()"' +
            ' />'
  }
}
export default TCRender
