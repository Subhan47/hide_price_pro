window.onload = function () {
    getVariantID();
};

let variantRadioButtons = document.querySelectorAll('fieldset input[type="radio"]');
variantRadioButtons.forEach(radio => {
    radio.addEventListener('change', function () {
        getVariantID();
    });
});


async function getVariantID() {
    const variantID = await getVariantIDWithDelay();
    if (variantID) {
         checkVariantIDinDB(variantID);
    }
}


function getVariantIDWithDelay() {
    return new Promise((resolve) => {
        setTimeout(function() {
            const variantID = jQuery('input[name="id"]').val();
            resolve(variantID);
        }, 100);
    });
}

function checkVariantIDinDB(variantID) {
    var domain = Shopify.shop;
    const APP_BASE_URL = 'https://hidePricePro.test';
    $.ajax({
        url: APP_BASE_URL + "/api/find-variant",
        type: 'GET',
        dataType: 'json',
        data: {
            'domain' : domain,
            'variant_id' : variantID
        },
        async: false,
        crossDomain: true,
        contentType: "json",
        success: function (response) {
           if(response.exists_enabled != null && response.exists_enabled === true){
               setTimeout(function() {
                   $('.product__info-wrapper .price__container').css('display', 'none');
               }, 400);
           }
        },
        error: function (error) {
            console.log('in error');
            console.log(error);
        }
    });
}









