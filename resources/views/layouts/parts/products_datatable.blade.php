<div class="product_variations_datatable_part" style="margin-top: 200px;">

    <table id="product_variations_datatable_part_table" class="display nowrap"
           style="width:100%;margin-top: 600px;">
        <thead>
        <tr>
            <th>Կազմակերպություն</th>
            <th>Ծառայության անվանումը</th>
            <th>Անվանական տոկոսադրույք</th>
            <th>Ընդամենը վճարներ</th>
            <th>Հետ վճարվող գումար</th>
            <th>Փաստացի տոկոսադրույք</th>
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
                        <td>{{$currProductCurrVariation["percentage"]}}</td>
                        <td>{{$currProductCurrVariation["require_payments"] }}</td>
                        <td> {{$currProductCurrVariation["sum_payments"] }}</td>
                        <td> {{round($currProductCurrVariation["factual_percentage"], 1) }}</td>
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
            <th>Ընդամենը վճարներ</th>
            <th>Հետ վճարվող գումար</th>
            <th>Փաստացի տոկոսադրույք</th>
        </tr>
        </tfoot>
    </table>
</div>

<div class="product_variations_grouped_by_company_datatable_part" style="margin-top: 200px;">
    <table id="product_variations_grouped_by_company_datatable_part_table" class="display nowrap"
           style="width:100%;margin-top: 600px;">
        <thead>
        <tr>
            <th>Կազմակերպություն</th>
            <th>Ծառայության անվանումը</th>
            <th>Անվանական տոկոսադրույք</th>
            <th>Ընդամենը վճարներ</th>
            <th>Հետ վճարվող գումար</th>
            <th>Փաստացի տոկոսադրույք</th>
        </tr>
        </thead>
        <tbody>
        @if(!is_null($productsWithVariations))
            @foreach($productsWithVariationsGroupByCompany as $productsWithVariationsGroupByCompanyCurr)

                @php( $productsWithVariationsGroupByCompanyCurr =   $productsWithVariationsGroupByCompanyCurr->toArray())
                @foreach($productsWithVariationsGroupByCompanyCurr as $productsWithVariationsGroupByCompanyCurrVariationCurr)
                    <tr>
                        <td>
                            <span class="print_company_name">{{$productsWithVariationsGroupByCompanyCurr[0]["companyInfo"]->name}}</span>
                        </td>
                        <td>
                            <span class="print_product_name">{{$products->find($productsWithVariationsGroupByCompanyCurrVariationCurr["product_id"])->name}}</span>
                        </td>
                        <td>{{$productsWithVariationsGroupByCompanyCurrVariationCurr["percentage"]}}</td>
                        <td>{{$productsWithVariationsGroupByCompanyCurrVariationCurr["require_payments"]}}</td>
                        <td> {{$productsWithVariationsGroupByCompanyCurrVariationCurr["sum_payments"]}}</td>
                        <td> {{round($productsWithVariationsGroupByCompanyCurrVariationCurr["factual_percentage"], 1) }}</td>
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
            <th>Ընդամենը վճարներ</th>
            <th>Հետ վճարվող գումար</th>
            <th>Փաստացի տոկոսադրույք</th>
        </tr>
        </tfoot>
    </table>
</div>
