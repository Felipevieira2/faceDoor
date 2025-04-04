<x-layout.admin.app title="Autorizações">


   
    <!-- Mantendo o componente Vue oculto para fins de diagnóstico -->
    <div id="app">
        <autorizacoes-index/>
    </div>
    
    @push('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof app !== 'undefined') {
                console.log('Vue App inicializado');
            } else {
                console.error('Aplicação Vue não encontrada');
            }
        });
    </script>
    @endpush

</x-layout.admin.app> 