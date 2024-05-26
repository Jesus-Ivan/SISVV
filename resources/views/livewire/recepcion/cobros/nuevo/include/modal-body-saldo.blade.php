<!-- Modal body -->
<div>
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">
                    RECIBO DE ORIGEN
                </th>
                <th scope="col" class="px-6 py-3">
                    SALDO GENERADO
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($this->saldoFavorDisponible as $registro)
                <tr
                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <td class="px-6 py-4">
                        {{ $registro->folio_recibo_origen }}
                    </td>
                    <td class="px-6 py-4">
                        ${{ $registro->saldo }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="font-medium text-gray-700 dark:text-white">
                <th scope="row" class="px-6 py-3 text-base">Total generado: </th>
                <th scope="row" class="px-6 py-3 text-base">
                    ${{ array_sum(array_column($this->saldoFavorDisponible->toArray(), 'saldo')) }}</th>
            </tr>
        </tfoot>
    </table>
</div>
