// Validación del formulario de votación
document.querySelector('form').addEventListener('submit', function (e) {
    const selectedPlanilla = document.querySelector('input[name="planilla_id"]:checked');
    if (!selectedPlanilla) {
        alert('Por favor, selecciona una planilla para votar.');
        e.preventDefault();
    }
});