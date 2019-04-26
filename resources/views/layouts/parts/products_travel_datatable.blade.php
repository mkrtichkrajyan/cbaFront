<div class="product_variations_datatable_part" style="margin-top: 200px;">

    <table id="product_variations_datatable_part_table" class="display nowrap"
           style="width:100%;margin-top: 600px;">
        <thead>
        <tr>
            <th>Կազմակերպություն</th>
            <th>Ծառայության անվանումը</th>
            <th>Ապահովագրավճար </th>
        </tr>
        </thead>
        <tbody>
        @if(!is_null($productsWithVariations))
            @foreach($productsWithVariations as $key=>$currProduct)
                @php( $currProductVariations = $currProduct["variations"])
                @foreach($currProductVariations as $currProductCurrVariation)
                    <tr>
                        <td>{{$currProduct["companyInfo"]->name}}</td>
                        <td>{{$currProduct["name"]}}</td>
                        <td>{{number_format(round($currProductCurrVariation["insurance_fee"]), 0, ",", " ") }}</td>
                    </tr>
                @endforeach
            @endforeach
        @endif
        </tbody>
        <tfoot>
        <tr>
            <th>Կազմակերպություն</th>
            <th>Ծառայության անվանումը</th>
            <th>Անվանական տոկոսադրույք</th>
        </tr>
        </tfoot>
    </table>
</div>
