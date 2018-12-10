Object.size = function (obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};


function filter_products(backend_url, page = 1, page_by_company = 1) {

    request_results_count = $("#request_results_count").val();

    filter_car_types = [];

    $(".filter_car_type").each(function () {

        if ($(this).prop('checked') == true) {
            filter_car_types.push($(this).attr('data-id'));
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

    repayment_loan_interval_type = $("#repayment_loan_interval_type").val();

    repayment_percent_interval_type = $("#repayment_percent_interval_type").val();

    time_type_search = $("#time_type_search").val();

    loan_term_search = $("#loan_term_search").val();

    car_cost_search = $("#car_cost_search").val();

    prepayment_search = $("#prepayment_search").val();

    if (request_results_count > 0) {

        $.ajax({
            type: 'get',

            url: backend_url,

            data: {
                'time_type_search': time_type_search,

                'loan_term_search': loan_term_search,

                'car_cost': car_cost_search,

                'prepayment': prepayment_search,

                'car_types': filter_car_types,

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

                makeProductsHtml(belonging_id, productsWithVariations, links, productsWithVariationsGroupByCompany, links_grouped_by_company, request_results_count, checked_variations);
            }
        });
    }
}

function drawProdGroupedByCompanyCurrMainVariationHtml(belonging_id, company_path, companyInfo, currVariation, other_suggestions, checked_variations) {

    if (checked_variations.indexOf(currVariation.unique_options) != -1) {
        compare_act_button_checked = "compare_act_button_checked";
    }
    else {
        compare_act_button_checked = "";
    }

    cost = $("#prod_cost").val();

    prepayment = isNaN(parseInt($("#prod_prepayment").val())) ? 0 : parseInt($("#prod_prepayment").val());

    term = $("#prod_loan_term_search_in_days").val();

    time_type_search = $("#time_type_search").val();

    loan_term_search = $("#loan_term_search").val();

    prod_page_path = $("#prod_page_path").val() + '/' + currVariation.unique_options + '/' + cost + '/' + prepayment + '/' + time_type_search + '/' + loan_term_search;


    curr_prod_variation_result =
        '<div class="table-wrapper"><div class="th"> ' +
        '<a target="_blank" href="' + company_path + '/' + companyInfo.id + '"><img src="' + backend_asset_path + 'savedImages/' + companyInfo.image + '" />' + '</a>' +
        '</div>' +

        '<div class="th"><span>' + currVariation.percentage + '</span></div>' +
        '<div class="th"><span>' + currVariation.require_payments + '</span></div>' +
        '<div class="th"><span>' + currVariation.sum_payments + '</span></div>' +
        '<div class="th"><span>' + currVariation.factual_percentage.toFixed(2) + '</span></div>' +
        '<div class="th flex-wrapper"><button class="btn btn-pink other_suggestions_open_close"><section>' + other_suggestions + '</section> <i class="icon icon-arrow-down"></i></button>' +
        '<button type="button" data-options="' + currVariation.unique_options + '" data-belongingid="' + belonging_id + '"  data-product-id="' + currVariation.product_id + '" class="btn btn_compare btn-white ' + compare_act_button_checked + '">' +
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

    cost = $("#prod_cost").val();

    prepayment = isNaN(parseInt($("#prod_prepayment").val())) ? 0 : parseInt($("#prod_prepayment").val());

    term = $("#prod_loan_term_search_in_days").val();

    time_type_search = $("#time_type_search").val();

    loan_term_search = $("#loan_term_search").val();

    prod_page_path = $("#prod_page_path").val() + '/' + currVariation.unique_options + '/' + cost + '/' + prepayment + '/' + time_type_search + '/' + loan_term_search;


    curr_prod_variation_result =
        '<div class="table-wrapper"><div class="th"> ' +
        '<a target="_blank" href="' + company_path + '/' + companyInfo.id + '"><img src="' + backend_asset_path + 'savedImages/' + companyInfo.image + '" />' + '</a>' +
        '</div>' +

        '<div class="th"><span>' + currVariation.percentage + '</span></div>' +
        '<div class="th"><span>' + currVariation.require_payments + '</span></div>' +
        '<div class="th"><span>' + currVariation.sum_payments + '</span></div>' +
        '<div class="th"><span>' + currVariation.factual_percentage.toFixed(2) + '</span></div>' +
        '<div class="th flex-wrapper"><button type="button"  data-options="' + currVariation.unique_options + '" data-belongingid="' + belonging_id + '"  data-product-id="' + currVariation.product_id + '" class="btn btn_compare btn-white ' + compare_act_button_checked + '">' +
        '<i class="icon icon-add icon-add-mini"></i></button><a href="' + prod_page_path + '" class="btn btn-more"><i class="icon icon-right icon-arrow-right"></i></a>' +
        '</div></div>';

    return curr_prod_variation_result;
}


function drawProdCurrMainVariationHtml(belonging_id, currProduct, company_path, companyInfo, currVariation, other_suggestions, checked_variations) {

    if (checked_variations.indexOf(currVariation.unique_options) != -1) {
        compare_act_button_checked = "compare_act_button_checked";
    }
    else {
        compare_act_button_checked = "";
    }

    cost = $("#prod_cost").val();

    prepayment = isNaN(parseInt($("#prod_prepayment").val())) ? 0 : parseInt($("#prod_prepayment").val());

    term = $("#prod_loan_term_search_in_days").val();

    time_type_search = $("#time_type_search").val();

    loan_term_search = $("#loan_term_search").val();

    prod_page_path = $("#prod_page_path").val() + '/' + currVariation.unique_options + '/' + cost + '/' + prepayment + '/' + time_type_search + '/' + loan_term_search;


    curr_prod_variation_result +=
        '<div class="listing-title"><div class="left"><div class="category-title">' + currProduct.name + '</div></div><div class="right">' +
        '<a target="_blank" href="' + company_path + '/' + companyInfo.id + '"  class="category-logo"><img style="max-width: 80px;" src="' + backend_asset_path + 'savedImages/' + companyInfo.image + '" />' + '</a>' +
        '</div></div>' +

        '<div class="table">' +
        '<div class="table-pise-wrapper">' +
        '<div class="table-pise"><div class="table-pise-title">Կազմակերպություն</div><div class="table-pise-text">' + companyInfo.name + '</div></div>' +
        '<div class="table-pise"><div class="table-pise-title">Անվանական տոկոսադրույք</div><div class="table-pise-text">' + currVariation.percentage + '</div></div>' +
        '<div class="table-pise"><div class="table-pise-title">Ընդամենը պարտադիր վճարներ</div><div class="table-pise-text">' + currVariation.require_payments + '</div></div>' +
        '</div>' +

        '<div class="table-pise-wrapper">' +
        '<div class="table-pise"><div class="table-pise-title">Ընդամենը հետ վճարվող գումար </div><div class="table-pise-text">' + currVariation.sum_payments + '</div></div>' +
        '<div class="table-pise"><div class="table-pise-title">Փաստացի տոկոսադրույք</div><div class="table-pise-text">' + currVariation.factual_percentage.toFixed(2) + '</div></div>' +
        '</div></div>' +

        '<div class="listing-title"><div class="left"><button type="button" data-options="' + currVariation.unique_options + '" data-belongingid="' + belonging_id + '"  data-product-id="' + currProduct.id + '" class="btn btn_compare btn-white ' + compare_act_button_checked + '" >' +
        '<i class="icon icon-left icon-add"></i><span>համեմատել</span></button><a href="' + prod_page_path + '" class="btn btn-more"><span>ավելին</span><i class="icon icon-right icon-arrow-right"></i></a></div>';

    if (other_suggestions >= 0) {
        curr_prod_variation_result += '<div class="right"><button type="button" class="btn btn-pink other_suggestions_open_close"><section>' + other_suggestions + '</section><span>այլ առաջարկ</span>' +
            '<i class="icon icon-arrow-down"></i></button></div>';
    }

    curr_prod_variation_result += '</div>';

    return curr_prod_variation_result;
}

function drawProdCurrOtherVariationHtml(belonging_id, currProduct, company_path, companyInfo, currVariation) {

    if (checked_variations.indexOf(currVariation.unique_options) != -1) {
        compare_act_button_checked = "compare_act_button_checked";
    }
    else {
        compare_act_button_checked = "";
    }

    cost = $("#prod_cost").val();

    prepayment = isNaN(parseInt($("#prod_prepayment").val())) ? 0 : parseInt($("#prod_prepayment").val());

    term = $("#prod_loan_term_search_in_days").val();

    time_type_search = $("#time_type_search").val();

    loan_term_search = $("#loan_term_search").val();

    prod_page_path = $("#prod_page_path").val() + '/' + currVariation.unique_options + '/' + cost + '/' + prepayment + '/' + time_type_search + '/' + loan_term_search;


    curr_prod_variation_result =
        '<div class="listing-title"><div class="left"><div class="category-title">' + currProduct.name + '</div></div><div class="right">' +
        '<a target="_blank" href="' + company_path + '/' + companyInfo.id + '" + class="category-logo"><img style="max-width: 80px;" src="' + backend_asset_path + 'savedImages/' + companyInfo.image + '" />' + '</a>' +
        '</div></div>' +

        '<div class="table">' +
        '<div class="table-pise-wrapper">' +
        '<div class="table-pise"><div class="table-pise-title">Կազմակերպություն</div><div class="table-pise-text">' + companyInfo.name + '</div></div>' +
        '<div class="table-pise"><div class="table-pise-title">Անվանական տոկոսադրույք</div><div class="table-pise-text">' + currVariation.percentage + '</div></div>' +
        '<div class="table-pise"><div class="table-pise-title">Ընդամենը պարտադիր վճարներ</div><div class="table-pise-text">' + currVariation.require_payments + '</div></div>' +
        '</div>' +

        '<div class="table-pise-wrapper">' +
        '<div class="table-pise"><div class="table-pise-title">Ընդամենը հետ վճարվող գումար </div><div class="table-pise-text">' + currVariation.sum_payments + '</div></div>' +
        '<div class="table-pise"><div class="table-pise-title">Փաստացի տոկոսադրույք</div><div class="table-pise-text">' + currVariation.factual_percentage.toFixed(2) + '</div></div>' +
        '</div></div>' +

        '<div class="listing-title"><div class="left"><button type="button" data-options="' + currVariation.unique_options + '" data-belongingid="' + belonging_id + '"  data-product-id="' + currProduct.id + '" class="btn btn_compare btn-white ' + compare_act_button_checked + '" >' +
        '<i class="icon icon-left icon-add"></i><span>համեմատել</span></button><a href="' + prod_page_path + '" class="btn btn-more"><span>ավելին</span><i class="icon icon-right icon-arrow-right"></i></a></div></div>';

    //curr_prod_variation_result += '</div>';

    return curr_prod_variation_result;
}

function makeProductsHtml(belonging_id, productsWithVariations, links, productsWithVariationsGroupByCompany, links_grouped_by_company, request_results_count, checked_variations) {

    backend_asset_path = $("#backend_asset_path").val();

    prod_page_path = $("#prod_page_path").val();

    company_path = $("#company_path").val();

    result_listing_title = $(".result_listing_title").clone();

    head_grouped_by_company = $(".head_grouped_by_company")[0].outerHTML;//$(".head_grouped_by_company").clone();

    product_variations_results_pagination = $(".product_variations_results_pagination").clone();

    $(".product_results").empty();

    $(".product_results").append(result_listing_title);


    product_results = '<div class="change_item change_item_product_variations_results">';

    data = productsWithVariations.data;

    $.each(data, function (index, currProduct) {

        companyInfo = currProduct.companyInfo;

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
    });

    $(".product_results").append(product_results);

    $(".product_results").append(product_variations_results_pagination);

    $('.product_variations_results_pagination').html(links);

    $(".change_item_product_variations_results").append(product_variations_results_pagination);


    data_grouped_by_company = productsWithVariationsGroupByCompany.data;

    product_results_grouped_by_company = '<div class="change_item change_item_product_variations_grouped_by_company_results">';

    product_results_grouped_by_company += head_grouped_by_company;

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

        product_results_grouped_by_company += '</div>';

        $(".product_results").append(product_results_grouped_by_company);
    });

    $(".result_listing_title .chenge").first().click();


    $(".count_searched_products").text(request_results_count);
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

$(document).ready(function () {
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


    $(document).on('click', '.other_suggestions_open_close', function (e) {
        $(this).children("i").toggleClass("active");

        $(this).parent().parent().nextAll(".hide-show").slideToggle(300);
        return false;
    });

    $(document).on('click', '.chenge', function (e) {
        $(".chenge").removeClass("active").eq($(this).index()).addClass("active");
        $(".change_item").hide().eq($(this).index()).show()
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

        /* building curr_data */
        curr_data = {};

        curr_data.product_id = product_id;

        curr_data.cost = cost;

        curr_data.prepayment = prepayment;

        curr_data.term = term;

        curr_data.curr_variation_options = curr_variation_options;

        /* building curr_data */

        if ($(this).hasClass('compare_act_button_checked')) {

            $(".btn_compare[data-options=" + curr_variation_options + "]").removeClass('compare_act_button_checked');

            curr_belonging_cookie = JSON.parse(curr_belonging_cookie);

            delete curr_belonging_cookie[curr_variation_options];
        }
        else {
            $(".btn_compare[data-options=" + curr_variation_options + "]").addClass('compare_act_button_checked');

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

        setCookie("belonging_" + curr_belonging_id, curr_belonging_cookie_stringify, exdays);

        compare_count = Object.keys(curr_belonging_cookie).length;

        $(".self-messeng-indicator").text(compare_count);
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

    if ($("#slider-range-mortgage").length > 0) {

        loan_amount_min = parseFloat($("#loan_amount_min").val().trim());

        loan_amount_max = parseFloat($("#loan_amount_max").val().trim());

        $("#slider-range-mortgage").slider({
            range: "min",
            min: loan_amount_min,
            max: loan_amount_max,
            step: 0.1,
            slide: function (event, ui) {
                $("#amount").val(ui.value);
            }
        });
    }

    $(".prepayment").keyup(function (e) {

        cost = parseFloat($("#cost").val().trim());

        prepayment = parseFloat($("#maximum").val().trim());

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

        prepayment = parseFloat($("#maximum").val().trim());

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

    if ($("#cost").length > 0 && $("#slider-range-max").length > 0) {

        cost = parseFloat($("#cost").val().trim());

        prepayment = parseFloat($("#maximum").val().trim());

        if (cost > 0) {
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

    $("#exportBranchesAtmsList").click(function () {

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