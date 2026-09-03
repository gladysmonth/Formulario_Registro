document.getElementById('formularioTicket').addEventListener('submit', function(event) {
    // Evita que la página se recargue en blanco al presionar el botón
    event.preventDefault();

    // Captura los datos ingresados
    const nombre = document.getElementById('nombre').value;
    const departamento = document.getElementById('departamento').value;
    const descripcion = document.getElementById('descripcion').value;

    // Verifica que seleccionó un departamento
    if (departamento === "") {
        alert("⚠️ Por favor, seleccione su departamento/área.");
        return;
    }

    // Muestra el mensaje de éxito al usuario
    alert(`✅ ¡Ticket registrado con éxito, ${nombre}!\n\nÁrea: ${departamento}\nProblema: ${descripcion}\n\nEl equipo de Sistemas de COSMOL lo atenderá pronto.`);

    // Limpia el formulario para el siguiente uso
    this.reset();
});