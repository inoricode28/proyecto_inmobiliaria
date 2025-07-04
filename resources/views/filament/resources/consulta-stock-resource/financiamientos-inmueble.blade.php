
<div class="p-4 bg-white rounded-lg shadow">
    <h2 class="text-lg font-medium text-gray-900">Estado Financiamiento: {{ $edificio->nombre }}</h2>
    <div class="overflow-x-auto mt-4">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Departamento
                    </th>
                    @foreach($financingTypes as $financing)
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ $financing->nombre }}
                        </th>
                    @endforeach
                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Total
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($rows as $row)
                <tr class="{{ $loop->last ? 'bg-gray-100 font-bold' : '' }}">
                    <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $row['name'] }}
                    </td>

                    @foreach($financingTypes as $financing)
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-center text-gray-500">
                            {{ $row[$financing->id] ?? 0 }}
                        </td>
                    @endforeach

                    <td class="px-4 py-2 whitespace-nowrap text-sm text-center font-bold text-gray-900">
                        {{ $row['totals'] }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>


