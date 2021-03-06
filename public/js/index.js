/*My functions*/
function number_format(number, decimals, dec_point, thousands_sep) {
    // http://kevin.vanzonneveld.net
    // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +     bugfix by: Michael White (http://crestidg.com)
    // +     bugfix by: Benjamin Lupton
    // +     bugfix by: Allan Jensen (http://www.winternet.no)
    // +    revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // *     example 1: number_format(1234.5678, 2, '.', '');
    // *     returns 1: 1234.57

    var n = number, c = isNaN(decimals = Math.abs(decimals)) ? 2 : decimals;
    var d = dec_point == undefined ? "," : dec_point;
    var t = thousands_sep == undefined ? "." : thousands_sep, s = n < 0 ? "-" : "";
    var i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;

    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}

Object.size = function (obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};

function make_slider_range_loan_amount_without_prepayment() {

    loan_amount_min = parseFloat($("#loan_amount").attr('data-loan_amount_min').trim());

    loan_amount_max = parseFloat($("#loan_amount").attr('data-loan_amount_max').trim());

    if ($("#loan_amount").val().trim().length > 0) {
        slider_range_loan_amount_without_prepayment_val = parseFloat($("#loan_amount").val());
    } else {
        slider_range_loan_amount_without_prepayment_val = loan_amount_min;
    }

    $(".slider_range_loan_amount_without_prepayment").slider({
        range: "min",
        min: loan_amount_min,
        max: loan_amount_max,
        step: 1,
        value: slider_range_loan_amount_without_prepayment_val,
        slide: function (event, ui) {
            $("#loan_amount").val(ui.value);
        }
    });
}

function make_number_not_more_than($elem) {

    allowed_length = $elem.attr('data-loan_amount_max').length;

    curr_length = $elem.val().length;

    if (parseInt($elem.val()) > parseInt($elem.attr('data-loan_amount_max'))) {

        if (curr_length == allowed_length) {
            allowed_val = parseInt($elem.attr('data-loan_amount_max'));
        }

        else {
            allowed_val = $elem.val().substr(0, allowed_length);
            console.log(allowed_val);
        }

        $elem.val(allowed_val);
    }

}

function reDrawDatatable($table, data) {
    $table.dataTable().fnClearTable();

    data_json = [];

    $.each(data, function (index, currProduct) {

        company_name = currProduct.companyInfo.name;

        product_name = currProduct.name;

        $.each(currProduct.variations, function (key, currProductCurrVariation) {

            curr_data_json = {
                0: company_name,
                1: product_name,
                2: currProductCurrVariation.percentage,
                3: currProductCurrVariation.require_payments,
                4: currProductCurrVariation.sum_payments,
                5: currProductCurrVariation.factual_percentage.toFixed(1),
            };

            data_json.push(curr_data_json);
        });
    });

    if (data_json.length > 0) {
        $table.dataTable().fnAddData(data_json);
    }
}

function reDrawByCompanyDatatable($table, data) {
    $table.dataTable().fnClearTable();

    data_json = [];

    $.each(data, function (index, currProductVariations) {

        company_name = currProductVariations[0].companyInfo.name;

        $.each(currProductVariations, function (key, currProductCurrVariation) {
            product_name = currProductCurrVariation.name

            curr_data_json = {
                0: company_name,
                1: product_name,
                2: currProductCurrVariation.percentage,
                3: currProductCurrVariation.require_payments,
                4: currProductCurrVariation.sum_payments,
                5: currProductCurrVariation.factual_percentage.toFixed(1),
            };

            data_json.push(curr_data_json);
        });

    });

    if (data_json.length > 0) {
        $table.dataTable().fnAddData(data_json);
    }

    // console.log(data_json);
}

function isObject(v) {
    return '[object Object]' === Object.prototype.toString.call(v);
};

function sortJson(obj, sort_by = 'min_factual_percentage') {
    my_arr = [];

    obj_keys = Object.keys(obj);

    $.each(obj, function (key, val) {
        // console.log(obj_keys);
        // console.log(key);
        // console.log(obj_keys.indexOf(key));
        // console.log(val);
        if (obj_keys.indexOf(key.toString()) != -1)
        // console.log(key);
        // console.log(val);
        // console.log(val);
            my_arr.push(val);
        // my_arr["'" + key + "'"] = val;
    });
    // console.log("obj_keys");
    // console.log(obj_keys);
    // console.log("my_arr");
    // console.log(my_arr);
    my_arr.sort(function (a, b) {
        return a[sort_by] - b[sort_by];
    });
    // my_arr.sort(function(a, b){
    //        return a.min_factual_percentage - b.min_factual_percentage;
    //    });
    // console.log("my_arr_final");
    // console.log(my_arr);

    return my_arr;
}

function filter_products(backend_url, page = 1, page_by_company = 1) {

    request_results_count = $("#request_results_count").val();

    filter_car_types = [];

    $(".filter_car_type").each(function () {

        if ($(this).prop('checked') == true) {
            filter_car_types.push($(this).attr('data-id'));
        }
    });

    filter_purpose_types = [];

    $(".filter_purpose_type").each(function () {

        if ($(this).prop('checked') == true) {
            filter_purpose_types.push($(this).attr('data-id'));
        }
    });

    filter_gold_assay_types = [];

    $(".filter_gold_assay_type").each(function () {

        if ($(this).prop('checked') == true) {
            filter_gold_assay_types.push($(this).attr('data-id'));
        }
    });

    filter_percentage_types = [];

    $(".filter_percentage_type").each(function () {

        if ($(this).prop('checked') == true) {
            filter_percentage_types.push($(this).attr('data-id'));
        }
    });

    filter_providing_types = [];

    $(".filter_providing_type").each(function () {

        if ($(this).prop('checked') == true) {
            filter_providing_types.push($(this).attr('data-id'));
        }
    });

    special_project_answers = [];

    $(".filter_special_project").each(function () {

        if ($(this).prop('checked') == true) {
            special_project_answers.push($(this).attr('data-id'));
        }
    });

    privileged_term_answers = [];

    $(".filter_privileged_term").each(function () {

        if ($(this).prop('checked') == true) {
            privileged_term_answers.push($(this).attr('data-id'));
        }
    });

    filter_repayment_types = [];

    $(".filter_repayment_type").each(function () {

        if ($(this).prop('checked') == true) {
            filter_repayment_types.push($(this).attr('data-id'));
        }
    });

    filter_security_types = [];

    $(".filter_security_type").each(function () {

        if ($(this).prop('checked') == true) {
            filter_security_types.push($(this).attr('data-id'));
        }
    });

    currency = $("#currency").val();

    repayment_loan_interval_type = $("#repayment_loan_interval_type").val();

    repayment_percent_interval_type = $("#repayment_percent_interval_type").val();

    time_type_search = $("#time_type_search").val();

    loan_term_search = $("#loan_term_search").val();

    car_cost_search = $("#car_cost_search").val();

    cost_search = $("#cost_search").val();

    prepayment_search = $("#prepayment_search").val();

    loan_amount_search = $("#loan_amount_search").val();


    if (request_results_count > 0) {

        $.ajax({
            type: 'get',

            url: backend_url,

            data: {
                'currency': currency,

                'time_type_search': time_type_search,

                'loan_term_search': loan_term_search,

                'cost': cost_search,

                'car_cost': car_cost_search,

                'prepayment': prepayment_search,

                'loan_amount': loan_amount_search,

                'car_types': filter_car_types,

                'purpose_types': filter_purpose_types,

                'gold_assay_types': filter_gold_assay_types,

                'percentage_types': filter_percentage_types,

                'providing_types': filter_providing_types,

                'special_project_answers': special_project_answers,

                'privileged_term_answers': privileged_term_answers,

                'repayment_types': filter_repayment_types,

                'security_types': filter_security_types,

                'repayment_loan_interval_type': repayment_loan_interval_type,

                'repayment_percent_interval_type': repayment_percent_interval_type,

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

                refresh_filters_single_counts(belonging_id, productsFilteredFiltersSingleCounts);

                makeProductsHtml(belonging_id, productsWithVariations, links, productsWithVariationsGroupByCompany, links_grouped_by_company, request_results_count, checked_variations);
            }
        });
    }
}

function refresh_filters_single_counts(belonging_id, productsFilteredFiltersSingleCounts) {
    percentage_types = productsFilteredFiltersSingleCounts.percentage_types;

    providing_types = productsFilteredFiltersSingleCounts.providing_types;

    repayment_types = productsFilteredFiltersSingleCounts.repayment_types;

    $.each(percentage_types, function (percentage_type_index, percentage_type) {
        $("#percentage_type_" + percentage_type.id).parent().find('.single_filter_count').text(percentage_type.count);
    });

    $.each(providing_types, function (providing_type_index, providing_type) {
        $("#providing_type" + providing_type.id).parent().find('.single_filter_count').text(providing_type.count);
    });

    $.each(repayment_types, function (repayment_type_index, repayment_type) {
        $("#repayment_type_" + repayment_type.id).parent().find('.single_filter_count').text(repayment_type.count);
    });

    switch (belonging_id) {
        case 1:
            break;
        case 2:
            gold_assay_types = productsFilteredFiltersSingleCounts.gold_assay_types;

            $.each(gold_assay_types, function (gold_assay_type_index, gold_assay_type) {
                $("#gold_assay_type_" + gold_assay_type.id).parent().find('.single_filter_count').text(gold_assay_type.count);
            });

            privileged_term_answers = productsFilteredFiltersSingleCounts.privileged_term_answers;

            $.each(privileged_term_answers, function (privileged_term_answer_index, privileged_term_answer) {
                $("#privileged_term_answer_" + privileged_term_answer.id).parent().find('.single_filter_count').text(privileged_term_answer.count);
            });

            break;
        case 3:
            break;
        case 4:
            break;
    }
}

function drawProdGroupedByCompanyCurrMainVariationHtml(belonging_id, company_path, companyInfo, currVariation, other_suggestions, checked_variations) {

    if (checked_variations.indexOf(currVariation.unique_options) != -1) {
        compare_act_button_checked = "compare_act_button_checked";
    }
    else {
        compare_act_button_checked = "";
    }

    cost = $(".cost_search").val();//$("#prod_cost").val();

    prepayment = isNaN(parseInt($("#prod_prepayment").val())) ? 0 : parseInt($("#prod_prepayment").val());

    term = $("#prod_loan_term_search_in_days").val();

    time_type_search = $("#time_type_search").val();

    loan_term_search = $("#loan_term_search").val();

    if (time_type_search == 1 || time_type_search == "" || isNaN(time_type_search)) {
        loan_term_search_in_days = loan_term_search;
    } else if (time_type_search == 2) {

        loan_term_search_in_days = loan_term_search * 30;
    } else if (time_type_search == 3) {

        loan_term_search_in_days = loan_term_search * 365;
    }


    if (belonging_id == 1 || belonging_id == 3) {
        prod_page_path = $("#prod_page_path").val() + '/' + currVariation.unique_options + '/' + cost + '/' + prepayment + '/' + time_type_search + '/' + loan_term_search;
    }
    else {
        loan_amount = parseInt($("#loan_amount_search").val());

        prod_page_path = $("#prod_page_path").val() + '/' + currVariation.unique_options + '/' + loan_amount + '/' + time_type_search + '/' + loan_term_search;
    }

    if (belonging_id == 4 || belonging_id == 2 || belonging_id == 6 || belonging_id == 11 || belonging_id == 13) {
        compare_button = '<button type="button" data-options="' + currVariation.unique_options + '" data-belongingid="' + belonging_id + '"  data-product-id="' + currVariation.product_id + '" data-loan_amount="' + loan_amount + '" data-term="' + loan_term_search_in_days + '"    class="btn btn_compare btn-white ' + compare_act_button_checked + '">';
    }
    else if (belonging_id == 1 || belonging_id == 3) {
        compare_button = '<button type="button" data-options="' + currVariation.unique_options + '" data-belongingid="' + belonging_id + '"  data-product-id="' + currVariation.product_id + '"   data-cost="' + cost + '" data-prepayment="' + prepayment + '" data-term="' + loan_term_search_in_days + '" class="btn btn_compare btn-white ' + compare_act_button_checked + '">';
    }

    else if (belonging_id == 5) {
        currency_search = $("#currency_search").val();

        compare_button = '<button type="button" data-options="' + currVariation.unique_options + '" data-belongingid="' + belonging_id + '"  data-product-id="' + currVariation.product_id + '" data-loan_amount="' + loan_amount + '"  data-currency="' + currency_search + '"  data-term="' + loan_term_search_in_days + '" class="btn btn_compare btn-white ' + compare_act_button_checked + '">';
    }

    curr_prod_variation_result =
        '<div class="table-wrapper"><div class="th"> ' +
        '<a target="_blank" href="' + company_path + '/' + companyInfo.id + '"><img src="' + backend_asset_path + 'savedImages/' + companyInfo.image + '" />' + '</a>' +
        '</div>' +

        '<div class="th"><span>' + currVariation.percentage + '</span></div>' +
        '<div class="th"><span>' + currVariation.require_payments + '</span></div>' +
        '<div class="th"><span>' + currVariation.sum_payments + '</span></div>' +
        '<div class="th"><span>' + currVariation.factual_percentage.toFixed(1) + ' %</span></div>' +
        '<div class="th flex-wrapper"><button class="btn btn-pink other_suggestions_open_close"><section>' + other_suggestions + '</section> <i class="icon icon-arrow-down"></i></button>' +
        compare_button +
        '<i class="icon icon-add icon-add-mini"></i></button><a href="' + prod_page_path + '" class="btn btn-more"><i class="icon icon-right icon-arrow-right"></i></a>' +
        '</div></div>';

    return curr_prod_variation_result;
}


function drawProdGroupedByCompanyCurrOtherVariationHtml(belonging_id, company_path, companyInfo, currVariation, checked_variations) {

    if (checked_variations.indexOf(currVariation.unique_options) != -1) {
        compare_act_button_checked = "compare_act_button_checked";
    }
    else {
        compare_act_button_checked = "";
    }

    cost = $(".cost_search").val();//$("#prod_cost").val();

    prepayment = isNaN(parseInt($("#prod_prepayment").val())) ? 0 : parseInt($("#prod_prepayment").val());

    term = $("#prod_loan_term_search_in_days").val();

    time_type_search = $("#time_type_search").val();

    loan_term_search = $("#loan_term_search").val();

    if (time_type_search == 1 || time_type_search == "" || isNaN(time_type_search)) {
        loan_term_search_in_days = loan_term_search;
    } else if (time_type_search == 2) {

        loan_term_search_in_days = loan_term_search * 30;
    } else if (time_type_search == 3) {

        loan_term_search_in_days = loan_term_search * 365;
    }

    if (belonging_id == 1 || belonging_id == 3) {
        prod_page_path = $("#prod_page_path").val() + '/' + currVariation.unique_options + '/' + cost + '/' + prepayment + '/' + time_type_search + '/' + loan_term_search;
    }
    else {
        loan_amount = parseInt($("#loan_amount_search").val());

        prod_page_path = $("#prod_page_path").val() + '/' + currVariation.unique_options + '/' + loan_amount + '/' + time_type_search + '/' + loan_term_search;
    }

    if (belonging_id == 4 || belonging_id == 2 || belonging_id == 6 || belonging_id == 11 || belonging_id == 13) {
        compare_button = '<button type="button" data-options="' + currVariation.unique_options + '" data-belongingid="' + belonging_id + '"  data-product-id="' + currVariation.product_id + '" data-loan_amount="' + loan_amount + '" data-term="' + loan_term_search_in_days + '"    class="btn btn_compare btn-white ' + compare_act_button_checked + '">';
    }
    else if (belonging_id == 1 || belonging_id == 3) {
        compare_button = '<button type="button" data-options="' + currVariation.unique_options + '" data-belongingid="' + belonging_id + '"  data-product-id="' + currVariation.product_id + '"   data-cost="' + cost + '" data-prepayment="' + prepayment + '" data-term="' + loan_term_search_in_days + '" class="btn btn_compare btn-white ' + compare_act_button_checked + '">';
    }

    else if (belonging_id == 5) {
        currency_search = $("#currency_search").val();

        compare_button = '<button type="button" data-options="' + currVariation.unique_options + '" data-belongingid="' + belonging_id + '"  data-product-id="' + currVariation.product_id + '" data-loan_amount="' + loan_amount + '"  data-currency="' + currency_search + '"  data-term="' + loan_term_search_in_days + '" class="btn btn_compare btn-white ' + compare_act_button_checked + '">';
    }

    curr_prod_variation_result =
        '<div class="table-wrapper"><div class="th"> ' +
        '<a target="_blank" href="' + company_path + '/' + companyInfo.id + '"><img src="' + backend_asset_path + 'savedImages/' + companyInfo.image + '" />' + '</a>' +
        '</div>' +

        '<div class="th"><span>' + currVariation.percentage + '</span></div>' +
        '<div class="th"><span>' + currVariation.require_payments + '</span></div>' +
        '<div class="th"><span>' + currVariation.sum_payments + '</span></div>' +
        '<div class="th"><span>' + currVariation.factual_percentage.toFixed(1) + ' %</span></div>' +
        '<div class="th flex-wrapper">' + compare_button +
        '<i class="icon icon-add icon-add-mini"></i></button><a href="' + prod_page_path + '" class="btn btn-more"><i class="icon icon-right icon-arrow-right"></i></a>' +
        '</div></div>';

    /*<button type="button"  data-options="' + currVariation.unique_options + '" data-belongingid="' + belonging_id + '"  data-product-id="' + currVariation.product_id + '" class="btn btn_compare btn-white ' + compare_act_button_checked + '">'*/
    return curr_prod_variation_result;
}


function drawProdCurrMainVariationHtml(belonging_id, currProduct, company_path, companyInfo, currVariation, other_suggestions, checked_variations) {

    icon_question = '<i class="icon icon-right  icon-question"></i>';

    if (checked_variations.indexOf(currVariation.unique_options) != -1) {
        compare_act_button_checked = "compare_act_button_checked";
    }
    else {
        compare_act_button_checked = "";
    }

    cost = $(".cost_search").val();//$("#prod_cost").val();

    prepayment = isNaN(parseInt($("#prod_prepayment").val())) ? 0 : parseInt($("#prod_prepayment").val());

    term = $("#prod_loan_term_search_in_days").val();

    time_type_search = $("#time_type_search").val();

    loan_term_search = $("#loan_term_search").val();

    if (time_type_search == 1 || time_type_search == "" || isNaN(time_type_search)) {
        loan_term_search_in_days = loan_term_search;
    } else if (time_type_search == 2) {

        loan_term_search_in_days = loan_term_search * 30;
    } else if (time_type_search == 3) {

        loan_term_search_in_days = loan_term_search * 365;
    }


    if (belonging_id == 1 || belonging_id == 3) {
        prod_page_path = $("#prod_page_path").val() + '/' + currVariation.unique_options + '/' + cost + '/' + prepayment + '/' + time_type_search + '/' + loan_term_search;
    }
    else {
        loan_amount = parseInt($("#loan_amount_search").val());

        prod_page_path = $("#prod_page_path").val() + '/' + currVariation.unique_options + '/' + loan_amount + '/' + time_type_search + '/' + loan_term_search;
    }

    if (belonging_id == 4 || belonging_id == 2 || belonging_id == 6 || belonging_id == 11 || belonging_id == 13) {
        compare_button =
            '<button type="button" data-options="' + currVariation.unique_options + '" data-belongingid="' + belonging_id + '" data-product-id="' + currProduct.id + '" data-term="' + loan_term_search_in_days + '" data-loan_amount="' + loan_amount + '" class="btn btn_compare btn-white ' + compare_act_button_checked + '">' +
            '<i class="icon icon-left icon-add"></i><span>համեմատել</span></button>';
    }
    else if (belonging_id == 1 || belonging_id == 3) {
        compare_button =
            '<button type="button" data-options="' + currVariation.unique_options + '" data-belongingid="' + belonging_id + '" data-product-id="' + currProduct.id + '"  data-cost="' + cost + '" data-prepayment="' + prepayment + '" data-term="' + loan_term_search_in_days + '"  class="btn btn_compare btn-white ' + compare_act_button_checked + '">' +
            '<i class="icon icon-left icon-add"></i><span>համեմատել</span></button>';
    }

    else if (belonging_id == 5) {
        currency_search = $("#currency_search").val();

        compare_button =
            '<button type="button" data-options="' + currVariation.unique_options + '" data-belongingid="' + belonging_id + '" data-product-id="' + currProduct.id + '" data-term="' + loan_term_search_in_days + '" data-loan_amount="' + loan_amount + '"  data-currency="' + currency_search + '" class="btn btn_compare btn-white ' + compare_act_button_checked + '">' +
            '<i class="icon icon-left icon-add"></i><span>համեմատել</span></button>';
    }

    curr_prod_variation_result +=
        '<div class="listing-title"><div class="left"><div class="category-title">' + '<a  class="prod_name_as_link" href="">' + currProduct.name + '</a></div></div><div class="right">' +
        '<a target="_blank" href="' + company_path + '/' + companyInfo.id + '"  class="category-logo"><img style="max-width: 80px;" src="' + backend_asset_path + 'savedImages/' + companyInfo.image + '" />' + '</a>' +
        '</div></div>' +

        '<div class="table">' +
        '<div class="table-pise-wrapper">' +
        '<div class="table-pise"><div class="table-pise-title">Կազմակերպություն</div><div class="table-pise-text">' + companyInfo.name + '</div></div>' +
        '<div class="table-pise"><div class="table-pise-title">Անվանական տոկոսադրույք</div><div class="table-pise-text">' + currVariation.percentage + '</div></div>' +
        '<div class="table-pise"><div class="table-pise-title">Ընդամենը վճարներ' + icon_question + ' </div><div class="table-pise-text">' + currVariation.require_payments + '</div></div>' +
        '</div>' +

        '<div class="table-pise-wrapper">' +
        '<div class="table-pise"><div class="table-pise-title">Հետ վճարվող գումար </div><div class="table-pise-text">' + currVariation.sum_payments + '</div></div>' +
        '<div class="table-pise"><div class="table-pise-title">Փաստացի տոկոսադրույք</div><div class="table-pise-text">' + currVariation.factual_percentage.toFixed(1) + ' %</div></div>' +
        '</div>' +
        '</div>' +

        '<div class="listing-title"><div class="left">' + compare_button + '<a href="' + prod_page_path + '" class="btn btn-more"><span>ավելին</span><i class="icon icon-right icon-arrow-right"></i></a></div>';

    if (other_suggestions >= 0) {
        curr_prod_variation_result += '<div class="right"><button type="button" class="btn btn-pink other_suggestions_open_close"><section>' + other_suggestions + '</section><span>այլ առաջարկ</span>' +
            '<i class="icon icon-arrow-down"></i></button></div>';
    }

    curr_prod_variation_result += '</div>';
    // curr_prod_variation_result += '</div></div>';

    return curr_prod_variation_result;
}

function drawProdCurrOtherVariationHtml(belonging_id, currProduct, company_path, companyInfo, currVariation) {

    if (checked_variations.indexOf(currVariation.unique_options) != -1) {
        compare_act_button_checked = "compare_act_button_checked";
    }
    else {
        compare_act_button_checked = "";
    }

    cost = $(".cost_search").val();//$("#prod_cost").val();

    prepayment = isNaN(parseInt($("#prod_prepayment").val())) ? 0 : parseInt($("#prod_prepayment").val());

    term = $("#prod_loan_term_search_in_days").val();

    time_type_search = $("#time_type_search").val();

    loan_term_search = $("#loan_term_search").val();

    if (time_type_search == 1 || time_type_search == "" || isNaN(time_type_search)) {
        loan_term_search_in_days = loan_term_search;
    } else if (time_type_search == 2) {

        loan_term_search_in_days = loan_term_search * 30;
    } else if (time_type_search == 3) {

        loan_term_search_in_days = loan_term_search * 365;
    }

    if (belonging_id == 1 || belonging_id == 3) {
        prod_page_path = $("#prod_page_path").val() + '/' + currVariation.unique_options + '/' + cost + '/' + prepayment + '/' + time_type_search + '/' + loan_term_search;
    }
    else {
        loan_amount = parseInt($("#loan_amount_search").val());

        prod_page_path = $("#prod_page_path").val() + '/' + currVariation.unique_options + '/' + loan_amount + '/' + time_type_search + '/' + loan_term_search;
    }

    if (belonging_id == 4 || belonging_id == 2 || belonging_id == 6 || belonging_id == 11 || belonging_id == 13) {
        compare_button =
            '<button type="button" data-options="' + currVariation.unique_options + '" data-belongingid="' + belonging_id + '" data-product-id="' + currProduct.id + '" data-term="' + loan_term_search_in_days + '" data-loan_amount="' + loan_amount + '" class="btn btn_compare btn-white ' + compare_act_button_checked + '">' +
            '<i class="icon icon-left icon-add"></i><span>համեմատել</span></button>';
    }
    else if (belonging_id == 1 || belonging_id == 3) {
        compare_button =
            '<button type="button" data-options="' + currVariation.unique_options + '" data-belongingid="' + belonging_id + '" data-product-id="' + currProduct.id + '"  data-cost="' + cost + '" data-prepayment="' + prepayment + '" data-term="' + loan_term_search_in_days + '"  class="btn btn_compare btn-white ' + compare_act_button_checked + '">' +
            '<i class="icon icon-left icon-add"></i><span>համեմատել</span></button>';
    }

    else if (belonging_id == 5) {
        currency_search = $("#currency_search").val();

        compare_button =
            '<button type="button" data-options="' + currVariation.unique_options + '" data-belongingid="' + belonging_id + '" data-product-id="' + currProduct.id + '" data-term="' + loan_term_search_in_days + '" data-loan_amount="' + loan_amount + '"  data-currency="' + currency_search + '" class="btn btn_compare btn-white ' + compare_act_button_checked + '">' +
            '<i class="icon icon-left icon-add"></i><span>համեմատել</span></button>';
    }

    curr_prod_variation_result =
        '<div class="listing-title"><div class="left"><div class="category-title">' + '<a  class="prod_name_as_link" href="">' + currProduct.name + '</a></div></div><div class="right">' +
        '<a target="_blank" href="' + company_path + '/' + companyInfo.id + '" + class="category-logo"><img style="max-width: 80px;" src="' + backend_asset_path + 'savedImages/' + companyInfo.image + '" />' + '</a>' +
        '</div></div>' +

        '<div class="table">' +
        '<div class="table-pise-wrapper">' +
        '<div class="table-pise"><div class="table-pise-title">Կազմակերպություն</div><div class="table-pise-text">' + companyInfo.name + '</div></div>' +
        '<div class="table-pise"><div class="table-pise-title">Անվանական տոկոսադրույք</div><div class="table-pise-text">' + currVariation.percentage + '</div></div>' +
        '<div class="table-pise"><div class="table-pise-title">Ընդամենը վճարներ</div><div class="table-pise-text">' + currVariation.require_payments + '</div></div>' +
        '</div>' +

        '<div class="table-pise-wrapper">' +
        '<div class="table-pise"><div class="table-pise-title">Հետ վճարվող գումար </div><div class="table-pise-text">' + currVariation.sum_payments + '</div></div>' +
        '<div class="table-pise"><div class="table-pise-title">Փաստացի տոկոսադրույք</div><div class="table-pise-text">' + currVariation.factual_percentage.toFixed(1) + ' %</div></div>' +
        '</div></div>' +

        '<div class="listing-title"><div class="left">' + compare_button + '<a href="' + prod_page_path + '" class="btn btn-more"><span>ավելին</span><i class="icon icon-right icon-arrow-right"></i></a></div></div>';

    //curr_prod_variation_result += '</div>';

    return curr_prod_variation_result;
}

function makeProductsHtml(belonging_id, productsWithVariations, links, productsWithVariationsGroupByCompany, links_grouped_by_company, request_results_count, checked_variations) {

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

    data = sortJson(data, 'min_factual_percentage');

    reDrawDatatable($('#product_variations_datatable_part_table'), data);

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

            curr_prod_variation_result = '<div class="wrapper pading">';

            curr_prod_variation_result += drawProdCurrMainVariationHtml(belonging_id, currProduct, company_path, companyInfo, firstObject, other_suggestions, checked_variations);

            product_results += curr_prod_variation_result;

            product_results += '<section class="hide-show">';

            if (other_suggestions_exist == 1) {

                firstKey = Object.keys(currProduct.variations)[0];

                $.each(currProduct.variations, function (key, currProductCurrVariation) {
                    if (key > firstKey) {

                        curr_prod_variation_result = '<div class="add-result pading">';

                        curr_prod_variation_result += drawProdCurrOtherVariationHtml(belonging_id, currProduct, company_path, companyInfo, currProductCurrVariation, checked_variations);

                        curr_prod_variation_result += '</div>';

                        product_results += curr_prod_variation_result;
                    }
                });
            }

            product_results += '</section></div>';

            product_results += '</div>';
        }
    });

    $(".product_results").append(product_results);

    $(".product_results").append(product_variations_results_pagination);

    $('.product_variations_results_pagination').html(links);

    $(".change_item_product_variations_results").append(product_variations_results_pagination);


    data_grouped_by_company = productsWithVariationsGroupByCompany.data;

    // console.log(data_grouped_by_company);return false;

    product_results_grouped_by_company = '<div class="change_item change_item_product_variations_grouped_by_company_results">';

    product_results_grouped_by_company += head_grouped_by_company;

    reDrawByCompanyDatatable($('#product_variations_grouped_by_company_datatable_part_table'), data_grouped_by_company);

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

        curr_prod_variation_result = drawProdGroupedByCompanyCurrMainVariationHtml(belonging_id, company_path, companyInfo, firstObject, other_suggestions, checked_variations);

        product_results_grouped_by_company += curr_prod_variation_result;

        product_results_grouped_by_company += '<div class="hide-show">';

        if (other_suggestions_exist == 1) {

            firstKey = Object.keys(currProductVariations)[0];

            $.each(currProductVariations, function (key, currProductCurrVariation) {
                if (key > firstKey) {

                    product_results_grouped_by_company += drawProdGroupedByCompanyCurrOtherVariationHtml(belonging_id, company_path, companyInfo, currProductCurrVariation, checked_variations);
                }
            });
        }

        product_results_grouped_by_company += '</div></div>';

        // $(".product_results").append(product_results_grouped_by_company);
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


function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function loanAmountAutomatic() {

    var cost = parseFloat($("#cost").val().trim());

    var prepayment = parseFloat($("#maximum").val().trim());

    if ($("#maximum").val().trim().length == 0 && $("#cost").val().trim().length > 0) {

        loan_amount_automatic = cost;
    }
    else if ($("#maximum").val().trim().length > 0 && $("#cost").val().trim().length > 0) {

        prepayment = parseFloat($("#maximum").val().trim());

        loan_amount_automatic = cost - prepayment;
    }
    else {
        loan_amount_automatic = "";

        $("#slider-range-max").slider("destroy");
    }

    $("#loan_amount").val(loan_amount_automatic);
}

function getGoBackLinkFromProductLink() {
    url = decodeURIComponent(location.search.split('go_back=')[1]);

    url = url.substring(1, url.length);

    url.substring(0, url.length - 1);

    console.log(url);

    if ($(".listing-title .left .product-come-back").length > 0) {
        $(".listing-title .left .product-come-back").attr('href', url);
    }
}

function addGoBackLinkToProductLink() {
    if ($("#seachProductForm").length > 0 && $(".listing-title .btn-more").length > 0) {
        curr_url = window.location.href;

        $(".listing-title .btn-more").each(function () {
            curr_prod_variation_url = $(this).attr('href') + '?go_back=' + '"' + curr_url + '"';

            $(this).attr('href', curr_prod_variation_url);

            // $(this).parents('.add-result').find('.listing-title').find('.category-title').find('.prod_name_as_link').attr('href', curr_prod_variation_url);
        });

        $(".table-wrapper .flex-wrapper .btn-more").each(function () {
            curr_prod_variation_url = $(this).attr('href') + '?go_back=' + '"' + curr_url + '"';

            $(this).attr('href', curr_prod_variation_url);
        });

        $(".prod_name_as_link").each(function () {
            $(this).attr('href', $(this).parent().parent().parent().parent().find('.btn-more').attr('href'));
        });

    }
}


/*My functions end*/

$(document).ready(function () {
    //
    // $(".mortgage_currency").parent().find('.select-items div').bind("DOMSubtreeModified", function () {
    //
    //
    //     console.log($(".mortgage_currency").val());
    //     // console.log($('#currency option[value="' + $("#currency").val() + '"]')[0]);
    // });

    $(".mortgage_currency").parent().find('.select-items div').click(function () {
        console.log($('.mortgage_currency option[value="' + $(".mortgage_currency").val() + '"]').attr('data-code'));
    });

    $(document).on('click', '.other_suggestions_open_close_global', function (e) {
        other_suggestions_open_close_global_open_text = $("#other_suggestions_open_close_global_open_text").val();

        other_suggestions_open_close_global_close_text = $("#other_suggestions_open_close_global_close_text").val();

        // $(".other_suggestions_open_close").click();

        if ($(this).attr('data-open') == "0") {
            $(this).attr('data-open', 1);

            $(this).text(other_suggestions_open_close_global_close_text);

            $(".other_suggestions_open_close").each(function () {
                if (!$(this).find('i').hasClass('active')) {
                    $(this).click();
                }
            });
        }
        else {
            $(this).attr('data-open', 0);

            $(this).text(other_suggestions_open_close_global_open_text);

            $(".other_suggestions_open_close").each(function () {
                if ($(this).find('i').hasClass('active')) {
                    $(this).click();
                }
            });
        }
    });

    $(document).on('click', '.come-back-from-company', function (e) {
        e.preventDefault();

        currUrl = window.location.href;

        oldURL = document.referrer;

        if (currUrl != oldURL && oldURL.length > 1) {
            // window.location.href = oldURL;
            console.log(currUrl);
            console.log(oldURL);
        }

    });

    if ($(".change_item_product_variations_grouped_by_company_results_ref").length > 0) {
        $(".change_item_product_variations_grouped_by_company_results_ref").click();
    }
    /*jquery datatable*/
    $(document).on('click', '.export_excel', function (e) {
        e.preventDefault();

        if ($(".change_item_product_variations_results_ref").hasClass('active')) {
            $(".product_variations_datatable_part").find('.buttons-excel').click();
        }
        else {
            $(".product_variations_grouped_by_company_datatable_part").find('.buttons-excel').click();
        }
    });

    $(document).on('click', '.print_results', function (e) {
        e.preventDefault();

        if ($(".change_item_product_variations_results_ref").hasClass('active')) {
            $(".product_variations_datatable_part").find('.buttons-print').click();
        }
        else {
            $(".product_variations_grouped_by_company_datatable_part").find('.buttons-print').click();
        }
    });

    if ($('#product_variations_datatable_part_table').length > 0) {
        $('#product_variations_datatable_part_table').DataTable({
            dom: 'Bfrtip',
            "order": [],
            buttons: [
                {
                    extend: 'print',
                    title: '',

                    customize: function (win) {
                        $(win.document.body).find('table   td').css('max-width', '165px');
                        $(win.document.body).find('table   td').css('white-space', 'normal');

                        $(win.document.body)
                            .css('font-size', '15px');

                        $(win.document.body).find('table')
                            .css('font-size', '15px');

                        autoPrint: true;
                    },
                    messageTop: $(".come-back span").text(),
                    bom: true
                },
                {
                    extend: 'csv',
                    text: 'Export csv',
                    charset: 'utf-8',
                    extension: '.csv',
                    fieldSeparator: ';',
                    fieldBoundary: '',
                    filename: 'export',
                    bom: true
                }, {
                    extend: 'excel',
                    text: 'Export excel',
                    title: null,
                    charset: 'utf-8',
                    extension: '.xlsx',
                    // fieldSeparator: ';',
                    // fieldBoundary: '',
                    filename: 'export_excel',
                    bom: true
                }
            ]
            // buttons: [
            //     'copy', 'csv', 'excel', 'pdf', 'print'
            // ]
        });

        $('#product_variations_grouped_by_company_datatable_part_table').DataTable({
            dom: 'Bfrtip',
            "order": [],
            buttons: [
                {
                    extend: 'print',
                    title: '',

                    customize: function (win) {
                        $(win.document.body).find('table   td').css('max-width', '165px');
                        $(win.document.body).find('table   td').css('white-space', 'normal');

                        $(win.document.body)
                            .css('font-size', '15px');

                        $(win.document.body).find('table')
                            .css('font-size', '15px');

                        autoPrint: true;
                    },
                    messageTop: $(".come-back span").text(),
                    bom: true
                },
                {
                    extend: 'csv',
                    text: 'Export csv',
                    charset: 'utf-8',
                    extension: '.csv',
                    fieldSeparator: ';',
                    fieldBoundary: '',
                    filename: 'export',
                    bom: true
                }, {
                    extend: 'excel',
                    text: 'Export excel',
                    title: null,

                    charset: 'utf-8',
                    extension: '.xlsx',
                    // fieldSeparator: ';',
                    // fieldBoundary: '',
                    filename: 'export_excel',
                    bom: true
                }
            ]
        });
    }
    /*jquery datatable*/

    $(".change_item_req").not(":first").hide();

    /* require payments blok chenge_req */
    $(".chenge_req").click(function () {
        $(".chenge_req").removeClass("active").eq($(this).index()).addClass("active");
        $(".change_item_req").hide().eq($(this).index()).show()
    }).eq(0).addClass("active");

    $(".chenge_req").click(function () {
        $(this).parent().find(".chenge_req").removeClass("active").eq($(this).index()).addClass("active");

        $(this).parent().find(".change_item_req").hide().eq($(this).index()).show()
    }).eq(0).addClass("active");
    /* require payments blok chenge_req */

    addGoBackLinkToProductLink();
    getGoBackLinkFromProductLink();
    //
    // $(document).on('click', '.prod_name_as_link', function (e) {
    //     e.preventDefault();
    //
    //     $(this).parent().parent().parent().parent().find('.btn-more').click();
    //     // console.log($(this).parent().parent().parent().parent().find('.btn-more')[0]);
    //     // console.log($(this).parent().parent().parent().parent().find('.btn-more').length);
    //     // console.log($(this).parent().parent().parent().parent()[0]);
    // });

    $(document).on('click', '.other_suggestions_open_close', function (e) {
        $(this).children("i").toggleClass("active");

        $(this).parent().parent().nextAll(".hide-show").slideToggle(300);
        return false;
    });

    $(document).on('click', '.chenge', function (e) {
        $(".chenge").removeClass("active").eq($(this).index()).addClass("active");
        if ($(this).hasClass('change_item_product_variations_results_ref') || $(this).hasClass('change_item_product_variations_grouped_by_company_results_ref')) {
            if ($(this).index() == 0) {
                change_item_product_variations_index = 1;
            }
            else {
                change_item_product_variations_index = 0;
            }
            $(".change_item").hide().eq(change_item_product_variations_index).show();
            return false;
        }
        else {
            $(".change_item").hide().eq($(this).index()).show();
        }
    }).eq(0).addClass("active");

    $(document).on('click', '.chenge', function (e) {
        $(this).parent().find(".chenge").removeClass("active").eq($(this).index()).addClass("active");

        $(this).parent().find(".change_item").hide().eq($(this).index()).show()
    }).eq(0).addClass("active");

    $("#seachProductFormSubmitCheck").click(function () {

        $("#seachProductForm").submit();
    });

    if ($(".multiple_select").length > 0) {
        $(".multiple_select").dropdown();
    }

    /* no negative value*/
    $(".no_negative_value").keydown(function (e) {

        // console.log(e.key);

        if (e.key == "-") {
            return false;
        }
    });
    /* no negative value*/

    /* no plus allow */
    $('.no_plus_allow').keydown(function (e) {
        if (e.key == "+") {

            return false;
        }
    });
    /* no plus allow */

    /*only_numbers... for input type number*/
    $('.only_numbers').keydown(function (e) {

        if (e.key == "+" || e.key == "-" || e.key == ".") {
            return false;
        }
    });
    /*only_numbers... for input type number*/

    /*number more than 0... can't start with 0*/
    $('.more_than_zero').on('keydown', function (e) {

        if ($(this).val().trim().length == 0 && e.key == 0) {
            return false;
        }
    });
    $('.more_than_zero').on('paste', function (e) {
        pasted = e.originalEvent.clipboardData.getData('text/plain');

        if ($(this).val().trim().length == 0 && parseInt(pasted) == 0) {
            console.log(pasted);

            return false;
        }

    });
    /*number more than 0... can't start with 0*/

    /* number not more than element max attribute */
    $('.number_not_more_than').keyup(function (e) {

        max = parseInt($(this).attr('max'));

        max_length = $(this).attr('max').length;

        if ($(this).val() > max) {

            allowed_val = $(this).val().substr(0, max_length);

            if (allowed_val > max) {
                allowed_val = $(this).val().substr(0, max_length - 1);
            }

            $(this).val(allowed_val);
        }
    });
    /* number not more than element max attribute */

    $("#deposit_type").parent().find('.select-selected').bind("DOMSubtreeModified", function () {

        if ($("#deposit_type").val() == 1) {
            $(".condition_from_deposit_type_term_part").css('display', "block");
        }
        else {
            $(".condition_from_deposit_type_term_part").css('display', "none");
        }
        //console.log($("#fileAtmOrBranch").val());
        console.log($("#deposit_type").val());
    });

    /* btn_compare */
    $(document).on('click', '.btn_compare', function () {

        exdays = 2;

        curr_variation_options = $(this).attr('data-options');

        //curr_variation_options_and_search_params = $(this).attr('data-options_and_search_params');

        curr_belonging_id = $(this).attr('data-belongingId');

        curr_belonging_cookie = getCookie("belonging_" + curr_belonging_id);

        product_id = $(this).attr('data-product-id');

        /* building curr_data */
        curr_data = {};

        curr_data.product_id = product_id;

        curr_data.curr_variation_options = curr_variation_options;

        if (curr_belonging_id == 12) {

            country = $(this).attr('data-country');

            age = $(this).attr('data-age');

            term = $(this).attr('data-term');

            currency = $(this).attr('data-currency');

            curr_data.country = country;

            curr_data.age = age;

            curr_data.term = term;

            curr_data.currency = currency;
        }

        else if (curr_belonging_id == 1) {
            if ($("#prod_cost").length > 0) {

                cost = $("#prod_cost").val();

                prepayment = $("#prod_prepayment").val();

                term = $("#prod_loan_term_search_in_days").val();
            }
            else {
                cost = $(this).attr('data-cost');

                prepayment = $(this).attr('data-prepayment');

                term = $(this).attr('data-term');
            }

            curr_data.cost = cost;

            curr_data.prepayment = prepayment;

            curr_data.term = term;
        }

        else if (curr_belonging_id == 2) {

            term = $(this).attr('data-term');

            loan_amount = $(this).attr('data-loan_amount');

            gold_pledge_type = $("#gold_pledge_type_search").val();

            curr_data.loan_amount = loan_amount;

            curr_data.term = term;

            curr_data.gold_pledge_type = gold_pledge_type;
        }

        else if (curr_belonging_id == 3) {

            cost = $(this).attr('data-cost');

            prepayment = $(this).attr('data-prepayment');

            term = $(this).attr('data-term');

            curr_data.cost = cost;

            curr_data.prepayment = prepayment;

            curr_data.term = term;
        }

        else if (curr_belonging_id == 8) {

            purpose_type = $("#purpose_type_search").val();

            currency = $(this).attr('data-currency');

            term = $(this).attr('data-term');

            cost = $(this).attr('data-cost');

            prepayment = $(this).attr('data-prepayment');

            loan_amount = $(this).attr('data-loan_amount');

            curr_data.cost = cost;

            curr_data.prepayment = prepayment;

            curr_data.loan_amount = loan_amount;

            curr_data.term = term;

            curr_data.currency = currency;

            curr_data.purpose_type = purpose_type;
        }

        else if (curr_belonging_id == 4) {

            term = $(this).attr('data-term');

            loan_amount = $(this).attr('data-loan_amount');

            curr_data.loan_amount = loan_amount;

            curr_data.term = term;
        }

        else if (curr_belonging_id == 5) {

            term = $(this).attr('data-term');

            loan_amount = $(this).attr('data-loan_amount');

            currency = $("#currency_search").val();

            curr_data.loan_amount = loan_amount;

            curr_data.term = term;

            curr_data.currency = currency;
        }

        else if (curr_belonging_id == 6) {

            term = $(this).attr('data-term');

            loan_amount = $(this).attr('data-loan_amount');

            curr_data.loan_amount = loan_amount;

            curr_data.term = term;
        }

        else if (curr_belonging_id == 11) {

            term = $(this).attr('data-term');

            loan_amount = $(this).attr('data-loan_amount');

            curr_data.loan_amount = loan_amount;

            curr_data.term = term;
        }

        else if (curr_belonging_id == 13) {

            term = $(this).attr('data-term');

            loan_amount = $(this).attr('data-loan_amount');

            curr_data.loan_amount = loan_amount;

            curr_data.term = term;
        }

        /* building curr_data */

        if ($(this).hasClass('compare_act_button_checked')) {
            console.log(1);
            $(".btn_compare[data-options=" + curr_variation_options + "]").removeClass('compare_act_button_checked');

            curr_belonging_cookie = JSON.parse(curr_belonging_cookie);

            delete curr_belonging_cookie[curr_variation_options];
        }
        else {
            $(".btn_compare[data-options=" + curr_variation_options + "]").addClass('compare_act_button_checked');

            // console.log(getCookie("belonging_1"));
            // console.log(getCookie("belonging_2"));
            // console.log(getCookie("belonging_8"));
            // console.log(getCookie("belonging_" + curr_belonging_id));

            // return false;
            if (curr_belonging_cookie.length == 0) {

                curr_belonging_cookie = {};

                curr_belonging_cookie[curr_variation_options] = curr_data;
            }
            else {
                curr_belonging_cookie = JSON.parse(curr_belonging_cookie);

                curr_belonging_cookie[curr_variation_options] = curr_data;
            }
        }

        curr_belonging_cookie_stringify = JSON.stringify(curr_belonging_cookie);
        //
        // console.log("curr_length= " + curr_belonging_cookie_stringify.length);
        // console.log("curr_length= " + window.btoa(curr_belonging_cookie_stringify).length);

        setCookie("belonging_" + curr_belonging_id, curr_belonging_cookie_stringify, exdays);

        compare_count = Object.keys(curr_belonging_cookie).length;

        $(".self-messeng-indicator").text(compare_count);

        if ($(".compare-info-popup-menu [data-belonging-id=" + curr_belonging_id + "]").css('display') == "none") {
            $(".compare-info-popup-menu [data-belonging-id=" + curr_belonging_id + "]").css('display', 'flex');
        }
        // console.log($(".compare-info-popup-menu [data-belonging-id=" + curr_belonging_id + "]").length);
        // console.log(curr_belonging_id);
    });
    /* btn_compare */


    if ($("#slider-range-money-transfer").length > 0) {

        money_transfer_amount_min = parseFloat($("#money_transfer_amount_min").val().trim());

        money_transfer_amount_max = parseFloat($("#money_transfer_amount_max").val().trim());

        $("#slider-range-money-transfer").slider({
            range: "min",
            min: money_transfer_amount_min,
            max: money_transfer_amount_max,
            step: 0.1,
            slide: function (event, ui) {
                $("#transfer_amount").val(ui.value);
            }
        });
    }
    //
    // if ($("#cost").length > 0 && $("#slider-range-mortgage").length > 0) {
    //     cost = parseFloat($("#cost").val().trim());
    //
    //     prepayment = parseFloat($("#maximum").val().trim());
    //
    //     if (isNaN(prepayment)) {
    //         prepayment = 0;
    //     }
    //
    //     loan_amount_min = parseFloat($("#loan_amount_min").val().trim());
    //
    //     loan_amount_max = parseFloat($("#loan_amount_max").val().trim());
    //
    //     $("#slider-range-mortgage").slider({
    //         range: "min",
    //         min: loan_amount_min,
    //         max: loan_amount_max,
    //         step: 0.1,
    //         slide: function (event, ui) {
    //             $("#amount").val(ui.value);
    //         }
    //     });
    // }

    $(".prepayment").keyup(function (e) {

        cost = parseFloat($("#cost").val().trim());

        prepayment = parseFloat($("#maximum").val().trim());

        if (isNaN(prepayment)) {
            prepayment = 0;
        }

        $("#slider-range-max").slider({
            range: "min",
            min: 0,
            max: cost,
            step: 1,
            value: prepayment,
            slide: function (event, ui) {
                $("#maximum").val(ui.value);

                loanAmountAutomatic();
            }
        });

        loanAmountAutomatic();
    });


    $("#cost").keyup(function (e) {

        cost = parseFloat($("#cost").val().trim());

        //cost_search = parseFloat($(".cost_search").val().trim());

        prepayment = parseFloat($("#maximum").val().trim());

        if (isNaN(prepayment)) {
            prepayment = 0;
        }

        prepayment = 0;

        $("#maximum").val('');

        $("#maximum").attr('max', cost);


        $("#slider-range-max").slider({
            range: "min",
            min: 0,
            max: cost,
            step: 1,
            value: prepayment,
            slide: function (event, ui) {
                $("#maximum").val(ui.value);
                //$("#maximum").val(0);

                loanAmountAutomatic();
            }
        });

        loanAmountAutomatic();
    });

    if ($("#cost").length > 0 && $("#slider-range-max").length > 0) {

        cost = parseFloat($("#cost").val().trim());

        prepayment = parseFloat($("#maximum").val().trim());

        if (isNaN(prepayment)) {
            prepayment = 0;
        }

        if (cost > 0) {
            console.log(prepayment);
            $("#slider-range-max").slider({
                range: "min",
                min: 0,
                max: cost,
                step: 1,
                value: prepayment,
                slide: function (event, ui) {
                    $("#maximum").val(ui.value);

                    loanAmountAutomatic();
                }
            });

            loanAmountAutomatic();
        }
    }

    /*Gold Loan*/
    if ($(".slider_range_loan_amount_without_prepayment").length > 0) {
        make_slider_range_loan_amount_without_prepayment();
    }

    $(".loan_amount_without_prepayment").keydown(function (e) {
        // if (parseInt($(this).val()) < parseInt($(this).attr('data-loan_amount_min')) && e.keyCode == 8) {
        //     return false;
        // }
        // console.log(e.target.value);
    });

    $(".loan_amount_without_prepayment").keyup(function (e) {

        make_number_not_more_than($(this));

        make_slider_range_loan_amount_without_prepayment();
    });

    /*Gold Loan*/


    $(".no_plus_symbol").keydown(function (e) {

        console.log(e.key);

        if (e.key == "+") {

            return false;
        }
    });

    $(".curr_all_checkboxes_check_uncheck").click(function () {
        console.log($(this).prop('checked'));

        if ($(this).prop('checked') == true) {
            $(this).parent().parent().find('input').each(function () {
                $(this).prop('checked', true);

                $(this).attr('checked', 'checked');
            });
        }
        else {
            $(this).parent().parent().find('input').each(function () {
                $(this).prop('checked', false);

                $(this).attr('checked', false);
            });
        }
    });

    $(".time_type_select").click(function () {

        $("#time_type").val($(this).attr('data-type'));
    });

    if ($(".map-chenge-branches".length > 0)) {

        if (localStorage.getItem('branches_or_bankomats_for_user') == "bankomat") {

            $("#branches_or_bankomats_for_user").val("bankomat");

            setTimeout(
                function () {
                    $(".map-chenge-bankomats").click();
                }, 1000);
        }
        else {
            localStorage.setItem("branches_or_bankomats_for_user", "branch");

            $(".map-chenge-branches").click();
        }
    }

    $("#exportBranchesAtmsList").click(function (e) {
        e.preventDefault()

        if (localStorage.getItem('branches_or_bankomats_for_user') == "bankomat") {
            exportType = "bankomats";
        }
        else {
            exportType = "branches";
        }

        if (exportType == "branches") {
            $("#branchesOrAtmsExporting").attr('action', $("#branchesOrAtmsExporting").attr('data-branch-action'));
        }
        else {
            $("#branchesOrAtmsExporting").attr('action', $("#branchesOrAtmsExporting").attr('data-bankomat-action'));
        }
        console.log(exportType);
        // return false;
        $("#branchesOrAtmsExporting").submit();
    });

    $(".map-chenge-branches").click(function () {

        $("#branches_or_bankomats_for_user").val("branch");

        localStorage.setItem("branches_or_bankomats_for_user", "branch");

        $("#branch_or_bankomat_concret_one").val(0);
    });

    $(".map-chenge-bankomats").click(function () {

        $("#branches_or_bankomats_for_user").val("bankomat");

        localStorage.setItem("branches_or_bankomats_for_user", "bankomat");

        $("#branch_or_bankomat_concret_one").val(0);
    });

    $(document).on('click', '.share_global', function (e) {
        e.preventDefault();

        console.log(1);

        $(".addthis_toolbox_part").toggle('slow');
    });

    $(".branch_bankomat_show_address").click(function () {

        //console.log($(this).attr('type'));

        curr_type = $(this).attr('type');

        lat = $(this).attr('lat');

        lng = $(this).attr('lng');

        curr_name = $(this).attr('data-name');

        curr_address = $(this).attr('data-address');

        $("#branch_or_bankomat_concret_one").val(1);

        $("#branch_or_bankomat_concret_one").attr('lat', lat);

        $("#branch_or_bankomat_concret_one").attr('lng', lng);

        $("#branch_or_bankomat_concret_one").attr('data-type', curr_type);

        $("#branch_or_bankomat_concret_one").attr('data-name', curr_name);

        $("#branch_or_bankomat_concret_one").attr('data-address', curr_address);

        // return false;
        $(".chenge").removeClass("active");

        $(".change_item").hide();

        $(".change_item_map").show();

        initMap();
    });

    $("#mapSwitcher").click(function () {

        $(".chenge").removeClass("active");

        $(".change_item").hide();

        $(".change_item_map").show();

        initMap();
    });
})
;