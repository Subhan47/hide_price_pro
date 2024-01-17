window.onload = async function () {
    $('.card-information, #price-template--21887848022314__main').css('display', 'none');
    const recommendationProductsArray = await getProductRecommendationIds();
    const variantId = await getVariantID();

    if (variantId != null) {
        recommendationProductsArray.push(variantId);
    }
    if (recommendationProductsArray.length > 0) {
        checkVariantIDinDB(recommendationProductsArray);
    }
};

let variantRadioButtons = document.querySelectorAll('fieldset input[type="radio"]');
variantRadioButtons.forEach(radio => {
    radio.addEventListener('change', async function () {
        const variantId = [await getVariantID()];
        checkVariantIDinDB(variantId);
    });
});


function getProductRecommendationIds() {
    return new Promise((resolve) => {
        const recommendationProductsArray = [];
        $('.card__information h3.card__heading.h5 a').each(function() {
            const url = $(this).attr('href');
            const productId = url.split("pr_rec_pid=")[1].split("&")[0];
            recommendationProductsArray.push(productId);
        });

        resolve(recommendationProductsArray);
    });
}

async function getVariantID() {
    return await getVariantIDWithDelay();
}

function getVariantIDWithDelay() {
    return new Promise((resolve) => {
        setTimeout(function() {
            const variantID = jQuery('input[name="id"]').val();
            resolve(variantID);
        }, 100);
    });
}

function checkVariantIDinDB(variantIDs) {
    var domain = Shopify.shop;
    const APP_BASE_URL = 'https://hidePricePro.test';
    $.ajax({
        url: APP_BASE_URL + "/api/find-variant",
        type: 'GET',
        dataType: 'json',
        data: {
            'domain' : domain,
            'variant_ids' : variantIDs
        },
        async: false,
        crossDomain: true,
        contentType: "json",
        success: function (response) {
            response.forEach(function (result, index) {
                if (result.rule_exists_enabled === null) {
                    $(`.card-information:eq(${index})`).css('display', 'block');
                }
                if (index === 4 && result.rule_exists_enabled === null) {
                    $('#price-template--21887848022314__main').css('display', 'block');
                }
            });
        },
        error: function (error) {
            console.log('in error');
            console.log(error);
        }
    });
}









