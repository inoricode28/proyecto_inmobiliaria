<div class="flex justify-center">
    <button 
        type="button"
        onclick="generarLaPreminuta()"
        class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded transition-colors duration-200"
    >
        Generar la Preminuta
    </button>
</div>

<script>
function generarLaPreminuta() {
    // Actualizar fecha_preminuta con la fecha actual
    const fechaPreminutaField = document.querySelector('[name="fecha_preminuta"]');
    if (fechaPreminutaField) {
        const today = new Date().toISOString().split('T')[0];
        fechaPreminutaField.value = today;
        fechaPreminutaField.dispatchEvent(new Event('change', { bubbles: true }));
    }
    
    alert('Preminuta generada exitosamente');
}
</script>