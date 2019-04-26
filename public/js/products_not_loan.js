function refresh_travel_filters_single_counts(belonging_id, productsFilteredFiltersSingleCounts) {
    console.log(productsFilteredFiltersSingleCounts);
    // return false;
    non_recoverable_expenses_answers = productsFilteredFiltersSingleCounts.non_recoverable_expenses_answers;

    term_inputs_quantities = productsFilteredFiltersSingleCounts.term_inputs_quantities;

    $.each(non_recoverable_expenses_answers, function (non_recoverable_expenses_answer_index, non_recoverable_expenses_answer) {
        $("#non_recoverable_expense_answer_" + non_recoverable_expenses_answer.id).parent().find('.single_filter_count').text(non_recoverable_expenses_answer.count);
    });

    $.each(term_inputs_quantities, function (term_inputs_quantity_index, term_inputs_quantity) {
        $("#term_inputs_quantity_" + term_inputs_quantity.id).parent().find('.single_filter_count').text(term_inputs_quantity.count);
    });
}

function filter_travel_insurances(backend_url, page = 1, page_by_company = 1) {

    request_results_count = $("#request_results_count").val();

    country_search = $("#country_search").val();

    age_search = $("#age_search").val();

    term_search = $("#term_search").val();

    filter_non_recoverable_expenses = [];

    $(".filter_non_recoverable_expense").each(function () {

        if ($(this).prop('checked') == true) {
            filter_non_recoverable_expenses.push($(this).attr('data-id'));
        }
    });

    filter_term_inputs_quantities = [];

    $(".filter_term_inputs_quantity").each(function () {

        if ($(this).prop('checked') == true) {
            filter_term_inputs_quantities.push($(this).attr('data-id'));
        }
    });

    // console.log(filter_non_recoverable_expenses,filter_term_inputs_quantities);
    // console.log(country_search, age_search, term_search);
    if (request_results_count > 0) {

        $.ajax({
            type: 'get',

            url: backend_url,

            data: {
                'country_search': country_search,

                'age_search': age_search,

                'term_search': term_search,

                'non_recoverable_expenses': filter_non_recoverable_expenses,

                'term_inputs_quantities': filter_term_inputs_quantities,

                'page': page,

                'page_by_company': page_by_company,
            },

            success: function (data) {

                belonging_id = data.belonging_id;

                productsWithVariations = data.productsWithVariations;

                links = data.links;

                productsWithVariationsGroupByCompany = data.productsWithVariationsGroupByCompany;

                links_grouped_by_company = data.links_grouped_by_company;

                request_results_count = data.request_results_count;

                checked_variations = data.checked_variations;

                productsFilteredFiltersSingleCounts = data.productsFilteredFiltersSingleCounts;

                refresh_travel_filters_single_counts(belonging_id, productsFilteredFiltersSingleCounts);

                makeTravelProductsHtml(belonging_id, productsWithVariations, links, productsWithVariationsGroupByCompany, links_grouped_by_company, request_results_count, checked_variations);
            }
        });
    }
}

function makeTravelProductsHtml(belonging_id, productsWithVariations, links, productsWithVariationsGroupByCompany, links_grouped_by_company, request_results_count, checked_variations) {

    backend_asset_path = $("#backend_asset_path").val();

    prod_page_path = $("#prod_page_path").val();

    company_path = $("#company_path").val();

    result_listing_title = $(".result_listing_title").clone();

    head_grouped_by_company = $(".head_grouped_by_company_hidden")[0].outerHTML;//$(".head_grouped_by_company").clone();

    product_variations_results_pagination = $(".product_variations_results_pagination").clone();

    $(".product_results").empty();

    $(".product_results").append(result_listing_title);


    product_results = '<div class="change_item change_item_product_variations_results">';

    data = productsWithVariations.data;

    data = sortJson(data, 'min_insurance_fee');

    // reDrawDatatable($('#product_variations_datatable_part_table'), data);

    $.each(data, function (index, currProduct) {

        companyInfo = currProduct.companyInfo;

        if (currProduct.variations.length > 0) {
            firstObject = currProduct.variations[0]; //Object.values(valGroupedByCompany)[0];

            if (currProduct.variations.length > 1) {
                other_suggestions_exist = 1;
            }

            else {
                other_suggestions_exist = 0;
            }

            other_suggestions = currProduct.variations.length - 1; // Object.size(valGroupedByCompany) - 1;

            curr_prod_variation_result = '<div class="wrapper pading">'; // open wrapper pading div for current product with all variations

            curr_prod_variation_result += drawProdTravelCurrMainVariationHtml(belonging_id, currProduct, company_path, companyInfo, firstObject, other_suggestions, checked_variations);

            product_results += curr_prod_variation_result;


            product_results += '<section class="hide-show">'; // open hide-show section tag for variations

            if (other_suggestions_exist == 1) {

                firstKey = Object.keys(currProduct.variations)[0];

                $.each(currProduct.variations, function (key, currProductCurrVariation) {
                    if (key > firstKey) {

                        curr_prod_variation_result = '<div class="add-result pading">'; // open div tag current variation

                        curr_prod_variation_result += drawProdTravelCurrOtherVariationHtml(belonging_id, currProduct, company_path, companyInfo, currProductCurrVariation, checked_variations);

                        curr_prod_variation_result += '</div>';// close div tag current variation

                        product_results += curr_prod_variation_result;
                    }
                });
            }

            product_results += '</section>';    // close hide-show section tag for variations


            product_results += '</div>';    // close wrapper pading div for current product with all variations
        }
    });

    $(".product_results").append(product_results);

    $(".product_results").append(product_variations_results_pagination);

    $('.product_variations_results_pagination').html(links);

    $(".change_item_product_variations_results").append(product_variations_results_pagination);


    data_grouped_by_company = productsWithVariationsGroupByCompany.data;

    product_results_grouped_by_company = '<div class="change_item change_item_product_variations_grouped_by_company_results">';

    product_results_grouped_by_company += head_grouped_by_company;

    data_grouped_by_company = sortTravelGroupedByCompanyJson(data_grouped_by_company, 'insurance_fee');
    //reDrawByCompanyDatatable($('#product_variations_grouped_by_company_datatable_part_table'), data_grouped_by_company);

    $.each(data_grouped_by_company, function (index, currProductVariations) {

        companyInfo = currProductVariations[0].companyInfo;

        firstObject = currProductVariations[0];

        if (currProductVariations.length > 1) {
            other_suggestions_exist = 1;
        }

        else {
            other_suggestions_exist = 0;
        }

        other_suggestions = currProductVariations.length - 1;

        product_results_grouped_by_company += '<div class="wrapper min-pading">';

        curr_prod_variation_result = drawProdTravelGroupedByCompanyCurrMainVariationHtml(belonging_id, company_path, companyInfo, firstObject, other_suggestions, checked_variations);

        product_results_grouped_by_company += curr_prod_variation_result;

        product_results_grouped_by_company += '<div class="hide-show">';

        if (other_suggestions_exist == 1) {

            firstKey = Object.keys(currProductVariations)[0];

            $.each(currProductVariations, function (key, currProductCurrVariation) {
                if (key > firstKey) {

                    product_results_grouped_by_company += drawProdTravelGroupedByCompanyCurrOtherVariationHtml(belonging_id, company_path, companyInfo, currProductCurrVariation, checked_variations);
                }
            });
        }

        product_results_grouped_by_company += '</div></div>';
    });

    product_results_grouped_by_company += '</div>';

    $(".product_results").append(product_results_grouped_by_company);

    $(".result_listing_title .chenge").first().click();


    $addthis_toolbox_part_block = $(".addthis_toolbox_part_hidden").first().clone();

    $addthis_toolbox_part_block.removeClass('addthis_toolbox_part_hidden').addClass('addthis_toolbox_part');

    $addthis_toolbox_part_block.insertBefore(".product_results .change_item_product_variations_results");

    addthis.toolbox('.addthis_toolbox');

    $(".count_searched_products").text(request_results_count);

    addGoBackLinkToProductLink();
}

function drawProdTravelCurrMainVariationHtml(belonging_id, currProduct, company_path, companyInfo, currVariation, other_suggestions, checked_variations) {

    backend_asset_path = $("#backend_asset_path").val();

    age_search = $("#age_search").val();

    term_search = $("#term_search").val();

    country_search = $("#country_search").val();

    prod_page_path = $("#prod_page_path").val() + '/' + currVariation.product_id + '/' + age_search + '/' + term_search + '/' + currVariation.currency + '/' + country_search;

    currVariationUniqueOptions = "product_" + currVariation.product_id + "_age_" + age_search + "_loan_term_" + term_search + "_currency_" + currVariation.currency + "_term_inputs_quantity_" + currVariation.term_inputs_quantity;

    if (checked_variations.indexOf(currVariationUniqueOptions) != -1) {
        compare_act_button_checked = "compare_act_button_checked";
    }
    else {
        compare_act_button_checked = "";
    }

    compare_button =
        '<button type="button" data-options="' + currVariationUniqueOptions + '" data-belongingid="' + belonging_id + '" data-product-id="' + currProduct.id + '" data-country="' + country_search + '" data-term="' + term_search + '" data-age="' + age_search + '" data-term_inputs_quantity="' + currVariation.term_inputs_quantity + '" class="btn btn_compare btn-white ' + compare_act_button_checked + '">' +
        '<i class="icon icon-left icon-add"></i><span>համեմատել</span></button>';

    currVariationInsuranceFee = number_format(currVariation.insurance_fee, 0, ",", " ");

    curr_prod_variation_result =
        '<div class="listing-title">' +
        '<div class="left"><div class="category-title">' + '<a  class="prod_name_as_link" href="">' + currProduct.name + '</a></div></div>' +
        '<div class="right"><a target="_blank" href="' + company_path + '/' + companyInfo.id + '"  class="category-logo"><img style="max-width: 80px;" src="' + backend_asset_path + 'savedImages/' + companyInfo.image + '" />' + '</a></div>' +
        '</div>' +

        '<div class="table">' +
        '<div class="table-pise-wrapper fill-available-width">' +
        '<div class="table-pise"><div class="table-pise-title">Կազմակերպություն</div><div class="table-pise-text">' + companyInfo.name + '</div></div>' +
        '<div class="table-pise"><div class="table-pise-title">Ապահովագրավճար</div><div class="table-pise-text">' + currVariationInsuranceFee + '</div></div>' +
        '</div></div>' +

        '<div class="listing-title">' +
        '<div class="left">' + compare_button + '<a href="' + prod_page_path + '" class="btn btn-more"><span>ավելին</span><i class="icon icon-right icon-arrow-right"></i></a></div>' +
        '<div class="right"><button type="button" class="btn btn-pink other_suggestions_open_close"><section>' + other_suggestions + '</section><span>այլ առաջարկ</span><i class="icon icon-arrow-down"></i></button></div>' +
        '</div>';

    return curr_prod_variation_result;
}

function drawProdTravelCurrOtherVariationHtml(belonging_id, currProduct, company_path, companyInfo, currVariation) {

    backend_asset_path = $("#backend_asset_path").val();

    age_search = $("#age_search").val();

    term_search = $("#term_search").val();

    country_search = $("#country_search").val();

    prod_page_path = $("#prod_page_path").val() + '/' + currVariation.product_id + '/' + age_search + '/' + term_search + '/' + currVariation.currency + '/' + country_search;

    currVariationUniqueOptions = "product_" + currVariation.product_id + "_age_" + age_search + "_loan_term_" + term_search + "_currency_" + currVariation.currency + "_term_inputs_quantity_" + currVariation.term_inputs_quantity;

    if (checked_variations.indexOf(currVariationUniqueOptions) != -1) {
        compare_act_button_checked = "compare_act_button_checked";
    }
    else {
        compare_act_button_checked = "";
    }

    compare_button =
        '<button type="button" data-options="' + currVariationUniqueOptions + '" data-belongingid="' + belonging_id + '" data-product-id="' + currProduct.id + '" data-country="' + country_search + '" data-term="' + term_search + '" data-age="' + age_search + '" data-term_inputs_quantity="' + currVariation.term_inputs_quantity + '" class="btn btn_compare btn-white ' + compare_act_button_checked + '">' +
        '<i class="icon icon-left icon-add"></i><span>համեմատել</span></button>';

    currVariationInsuranceFee = number_format(currVariation.insurance_fee, 0, ",", " ");

    curr_prod_variation_result =
        '<div class="listing-title">' +
        '<div class="left"><div class="category-title">' + '<a  class="prod_name_as_link" href="">' + currProduct.name + '</a></div></div>' +
        '<div class="right"><a target="_blank" href="' + company_path + '/' + companyInfo.id + '"  class="category-logo"><img style="max-width: 80px;" src="' + backend_asset_path + 'savedImages/' + companyInfo.image + '" />' + '</a></div>' +
        '</div>' +

        '<div class="table">' +
        '<div class="table-pise-wrapper fill-available-width">' +
        '<div class="table-pise"><div class="table-pise-title">Կազմակերպություն</div><div class="table-pise-text">' + companyInfo.name + '</div></div>' +
        '<div class="table-pise"><div class="table-pise-title">Ապահովագրավճար</div><div class="table-pise-text">' + currVariationInsuranceFee + '</div></div>' +
        '</div></div>' +

        '<div class="listing-title">' +
        '<div class="left">' + compare_button + '<a href="' + prod_page_path + '" class="btn btn-more"><span>ավելին</span><i class="icon icon-right icon-arrow-right"></i></a></div>' +
        '</div>';

    return curr_prod_variation_result;
}

function drawProdTravelGroupedByCompanyCurrMainVariationHtml(belonging_id, company_path, companyInfo, currVariation, other_suggestions, checked_variations) {

    backend_asset_path = $("#backend_asset_path").val();

    age_search = $("#age_search").val();

    term_search = $("#term_search").val();

    country_search = $("#country_search").val();

    prod_page_path = $("#prod_page_path").val() + '/' + currVariation.product_id + '/' + age_search + '/' + term_search + '/' + currVariation.currency + '/' + country_search;

    currVariationUniqueOptions = "product_" + currVariation.product_id + "_age_" + age_search + "_loan_term_" + term_search + "_currency_" + currVariation.currency + "_term_inputs_quantity_" + currVariation.term_inputs_quantity;

    if (checked_variations.indexOf(currVariationUniqueOptions) != -1) {
        compare_act_button_checked = "compare_act_button_checked";
    }
    else {
        compare_act_button_checked = "";
    }

    compare_button =
        '<button type="button" data-options="' + currVariationUniqueOptions + '" data-belongingid="' + belonging_id + '" data-product-id="' + currVariation.product_id + '" data-country="' + country_search + '" data-term="' + term_search + '" data-age="' + age_search + '" data-term_inputs_quantity="' + currVariation.term_inputs_quantity + '" class="btn btn_compare btn-white ' + compare_act_button_checked + '">' +
        '<i class="icon icon-add icon-add-mini"></i></button>';

    curr_prod_variation_result =
        '<div class="table-wrapper">' +

        '<div class="th fill-available-width"><a target="_blank" href="' + company_path + '/' + companyInfo.id + '"><img src="' + backend_asset_path + 'savedImages/' + companyInfo.image + '" />' + '</a></div>' +
        '<div class="th fill-available-width"><span>' + currVariation.insurance_fee + '</span></div>' +

        '<div class="th flex-wrapper fill-available-width"><button class="btn btn-pink other_suggestions_open_close"><section>' + other_suggestions + '</section> <i class="icon icon-arrow-down"></i></button>' +
        compare_button + '<a href="' + prod_page_path + '" class="btn btn-more"><i class="icon icon-right icon-arrow-right"></i></a></div>' +

        '</div>';

    return curr_prod_variation_result;
}

function drawProdTravelGroupedByCompanyCurrOtherVariationHtml(belonging_id, company_path, companyInfo, currVariation, checked_variations) {

    backend_asset_path = $("#backend_asset_path").val();

    age_search = $("#age_search").val();

    term_search = $("#term_search").val();

    country_search = $("#country_search").val();

    prod_page_path = $("#prod_page_path").val() + '/' + currVariation.product_id + '/' + age_search + '/' + term_search + '/' + currVariation.currency + '/' + country_search;

    currVariationUniqueOptions = "product_" + currVariation.product_id + "_age_" + age_search + "_loan_term_" + term_search + "_currency_" + currVariation.currency + "_term_inputs_quantity_" + currVariation.term_inputs_quantity;

    if (checked_variations.indexOf(currVariationUniqueOptions) != -1) {
        compare_act_button_checked = "compare_act_button_checked";
    }
    else {
        compare_act_button_checked = "";
    }

    compare_button =
        '<button type="button" data-options="' + currVariationUniqueOptions + '" data-belongingid="' + belonging_id + '" data-product-id="' + currVariation.product_id + '" data-country="' + country_search + '" data-term="' + term_search + '" data-age="' + age_search + '" data-term_inputs_quantity="' + currVariation.term_inputs_quantity + '" class="btn btn_compare btn-white ' + compare_act_button_checked + '">' +
        '<i class="icon icon-add icon-add-mini"></i></button>';

    curr_prod_variation_result =
        '<div class="table-wrapper">' +

        '<div class="th fill-available-width"><a target="_blank" href="' + company_path + '/' + companyInfo.id + '"><img src="' + backend_asset_path + 'savedImages/' + companyInfo.image + '" />' + '</a></div>' +
        '<div class="th fill-available-width"><span>' + currVariation.insurance_fee + '</span></div>' +

        '<div class="th flex-wrapper fill-available-width">' +
        compare_button + '<a href="' + prod_page_path + '" class="btn btn-more"><i class="icon icon-right icon-arrow-right"></i></a></div>' +

        '</div>';

    return curr_prod_variation_result;
}

function sortTravelGroupedByCompanyJson(obj, sort_by = 'min_factual_percentage') {
    my_group_sorted_arr = [];

    my_group_keys_with_sort_by_min_arr = [];

    my_group_arr = [];

    obj_keys = Object.keys(obj);

    $.each(obj, function (key, val) {

        if (obj_keys.indexOf(key.toString()) != -1) {
            curr_min_val = val[0][sort_by];

            $.each(val, function (key_inner, val_inner) {
                if (val_inner[sort_by] <= curr_min_val) {
                    curr_min_val = val_inner[sort_by];
                }
            });

            my_group_keys_with_sort_by_min_arr.push({index: key, value: curr_min_val});
            // my_group_keys_with_sort_by_min_arr[key] = {index: key, value: curr_min_val};

            my_group_arr[key] = val;
        }
    });

    my_group_keys_with_sort_by_min_arr.sort(function (a, b) {
        return a.value - b.value;
    });

    $.each(my_group_keys_with_sort_by_min_arr, function (key_sort_by_min, index_value_sort_by_min) {

        my_group_sorted_arr.push(my_group_arr[index_value_sort_by_min.index]);
    });

    return my_group_sorted_arr;
}

$(document).ready(function () {
    $(document).on('click', '.travel_export_excel', function (e) {
        e.preventDefault();

        $(".product_variations_datatable_part").find('.buttons-excel').click();
    });

    $(document).on('click', '.travel_print_results', function (e) {
        e.preventDefault();

        $(".product_variations_datatable_part").find('.buttons-print').click();
    });
});