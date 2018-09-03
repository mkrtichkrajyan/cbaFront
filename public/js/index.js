Object.size = function (obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};


function filter_products(backend_url) {
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

    car_cost = $("#car_cost").val();

    prepayment = $("#prepayment").val();

    //console.log(backend_url);
    if (request_results_count > 0) {
        //alert(1);
        $.ajax({

            type: 'get',

            url: backend_url,

            data: {
                'time_type_search': time_type_search,

                'loan_term_search': loan_term_search,

                'car_cost': car_cost,

                'prepayment': prepayment,

                'car_types': filter_car_types,

                'percentage_types': filter_percentage_types,

                'providing_types': filter_providing_types,

                'special_project_answers': special_project_answers,

                'privileged_term_answers': privileged_term_answers,

                'repayment_types': filter_repayment_types,

                'security_types': filter_security_types,

                'repayment_loan_interval_type': repayment_loan_interval_type,

                'repayment_percent_interval_type': repayment_percent_interval_type,
            },

            success: function (data) {
                data = JSON.parse(data);

                backend_asset_path = $("#backend_asset_path").val();

                $(".product_results").empty();

                result_listing_title = '<div class="listing-title">' +
                    '<div class="left"> Գտնվել է <span id="count_searched_products"></span> առաջարկ</div>' +
                    '<div class="right"><div class="listing-icon"><div class="add-function">' +
                    '<a href="" download=""> <i class="icon icon-right  icon-download"></i></a> <a href=""> <i class="icon icon-right  icon-more"></i></a>' +
                    '<a href=""> <i class="icon icon-right icon-print"></i></a></div>' +
                    '<div class="btn-icon-blue"><i class="chenge icon icon-right icon-list"></i><i class="chenge icon icon-right icon-list-tow active"></i>' +
                    '</div></div></div></div>';

                $(".product_results").append(result_listing_title);

                count_searched_products = 0;

                product_results = '<div class="change_item">';

                $.each(data, function (index, valGroupedByCompany) {
                    //console.log(typeof valGroupedByCompany);

                    count_searched_products = count_searched_products + Object.size(valGroupedByCompany);

                    if (Object.size(valGroupedByCompany) > 1) {
                        //console.log(Object.values(valGroupedByCompany));

                        firstObject = Object.values(valGroupedByCompany)[0];

                        other_suggestions_exist = 1;
                    }

                    else {
                        firstObject = valGroupedByCompany[0];

                        other_suggestions_exist = 0;
                    }

                    other_suggestions = Object.size(valGroupedByCompany) - 1;

                    product_results +=
                        '<div class="wrapper pading">' +
                        '<div class="listing-title"><div class="left"><div class="category-title">' + firstObject.name + '</div></div><div class="right"><div class="category-logo"><img style="max-width: 80px;" src="' + backend_asset_path + 'savedImages/' + firstObject.company_info.image + '"></div></div></div>' +

                        '<div class="table">' +

                        '<div class="table-pise-wrapper">' +
                        '<div class="table-pise"><div class="table-pise-title">Անվանական տոկոսադրույք</div><div class="table-pise-text"> 98% </div></div>' +
                        '<div class="table-pise"><div class="table-pise-title">Պարտադիր ճարներ<i class="icon icon-right  icon-question"></i></div><div class="table-pise-text"> 2 000 000 </div></div>' +
                        '</div>' +

                        '<div class="table-pise-wrapper">' +
                        '<div class="table-pise"><div class="table-pise-title">Հետ վճարվող գումար </div><div class="table-pise-text"> 2 000 000 <i class="icons "></i></div></div>' +
                        '<div class="table-pise"><div class="table-pise-title">Անվանական տոկոսադրույք</div><div class="table-pise-text"> 98%</div></div>' +
                        '</div>' +

                        '<div class="table-pise-wrapper"><div class="table-pise"><div class="table-pise-title">Անվանական</div><div class="table-pise-text">98%</div></div></div></div>' +

                        '<div class="listing-title"><div class="left"><button type="button" class="btn btn-red"><i class="icon icon-left  icon-add"></i><span>համեմատել</span></button>' +
                        '<a href="?p=prod-page" class="btn btn-more"><span>ավելին</span><i class="icon icon-right  icon-arrow-right"></i></a></div>' +
                        '<div class="right"><button type="button" class="btn btn-pink other_suggestions_open_close"><section>' + other_suggestions + '</section><span>այլ առաջարկ</span><i class="icon icon-arrow-down"></i></button></div></div>' +
                        '</div>';

                    product_results += '<section className="hide-show">';

                    if (other_suggestions_exist == 1) {

                        $.each(valGroupedByCompany, function (key, groupedByCompanyOtherProduct) {
                            console.log(groupedByCompanyOtherProduct);
                            console.log(groupedByCompanyOtherProduct);

                        });

                    }

                    product_results += '</section>';

                });


                console.log(product_results);

                $(".product_results").append(product_results);

                $("#count_searched_products").text(count_searched_products);

            }
        });
    }
}

$(document).ready(function () {

    $(".no_negative_value").keydown(function (e) {

        console.log(e.key);

        if (e.key == "-") {

            return false;
        }
    });

    $(".prepayment").keyup(function (e) {

        car_cost = parseFloat($("#car_cost").val().trim());

        prepayment = parseFloat($("#maximum").val().trim());

        $("#slider-range-max").slider({
            range: "min",
            min: 0,
            max: car_cost,
            step: 0.1,
            slide: function (event, ui) {
                $("#maximum").val(ui.value);
            }
        });

        if ($("#maximum").val().trim().length == 0 && $("#car_cost").val().trim().length > 0) {

            loan_amount_automatic = car_cost;
        }
        else if ($("#maximum").val().trim().length > 0 && $("#car_cost").val().trim().length > 0) {

            loan_amount_automatic = car_cost - prepayment;
        }
        else {
            loan_amount_automatic = "";

            $("#slider-range-max").slider("destroy");
        }

        $("#loan_amount").val(loan_amount_automatic);
    });

    $("#car_cost").keyup(function (e) {

        car_cost = parseFloat($("#car_cost").val().trim());

        prepayment = parseFloat($("#maximum").val().trim());

        $("#slider-range-max").slider({
            range: "min",
            min: 0,
            max: car_cost,
            step: 0.1,
            slide: function (event, ui) {
                $("#maximum").val(ui.value);
            }
        });

        if ($("#maximum").val().trim().length == 0 && $("#car_cost").val().trim().length > 0) {

            loan_amount_automatic = car_cost;
        }
        else if ($("#maximum").val().trim().length > 0 && $("#car_cost").val().trim().length > 0) {

            loan_amount_automatic = car_cost - prepayment;
        }
        else {
            loan_amount_automatic = "";

            $("#slider-range-max").slider("destroy");
        }

        $("#loan_amount").val(loan_amount_automatic);
    });

    $(".no_plus_symbol").keydown(function (e) {

        console.log(e.key);

        if (e.key == "+") {

            return false;
        }
    });

    $("#seachProductFormSubmitCheck").click(function () {

        $("#seachProductForm").submit();
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
});