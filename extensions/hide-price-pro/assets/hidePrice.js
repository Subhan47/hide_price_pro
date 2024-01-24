// On Page Load Retrieved All Rules_Variants From DB
window.onload =  async function () {
    await processData();
};
// On Variant Radio button change on Product Page
let variantRadioButtons = document.querySelectorAll('fieldset input[type="radio"]');
variantRadioButtons.forEach(radio => {
    radio.addEventListener('change', async function () {
        await processData();
    });
});

async function processData() {
    $('#price-template--21887848022314__main').css('display', 'block');

    var retrievedCollectionsRulesVariants = await fetchRulesVariants('collections');
    $.each(retrievedCollectionsRulesVariants, function (index, item) {
        predictiveSearchApi(item,'collections');
    });

    var retrievedProductsRulesVariants = await fetchRulesVariants('products');
    $.each(retrievedProductsRulesVariants, function (index, item) {
        predictiveSearchApi(item, 'products');
    });

    const variantId = await getVariantID();
    if (variantId){
        checkVariantIDinDB(variantId);
    }
}


function fetchRulesVariants(type){
    const APP_BASE_URL = 'https://hidePricePro.test';
    return new Promise(function (resolve, reject) {
        $.ajax({
            url: APP_BASE_URL + "/api/all-rulesVariants/" + type,
            type: 'GET',
            dataType: 'json',
            crossDomain: true,
            contentType: "json",
            success: function (response) {
                resolve(response);
            },
            error: function (error) {
                console.log('In error');
                console.log(error);
                reject(error);
            }
        });
    });
}

function predictiveSearchApi(item, type){
    return new Promise(function (resolve, reject) {
        var products;
        $.ajax({
            url: `${window.Shopify.routes.root}search/suggest.json?q=id:${item.variant_id}`,
            type: 'GET',
            dataType: 'json',
            crossDomain: true,
            contentType: "json",
            success: async function (response) {
                if(type === 'collections'){
                    const collectionHandle = response.resources.results.collections[0]?.handle;
                    if(collectionHandle){
                        $.ajax({
                            url: `${window.Shopify.routes.root}collections/${collectionHandle}/products.json`,
                            type: 'GET',
                            dataType: 'json',
                            crossDomain: true,
                            contentType: "json",
                            success: async function (response) {
                                products = response.products;
                                resolve(products);
                            },
                            error: function (error) {
                                console.log('In error');
                                console.log(error);
                                reject(error);
                            }
                        });
                    }
                }
                else{
                    products = response.resources.results.products;
                    resolve(products);
                }
            },
            error: function (error) {
                console.log('In error');
                console.log(error);
                reject(error);
            }
        });
    })
        .then(function (products) {
            if (products && products.length > 0) {
                $('.card__information h3.card__heading.h5 a').each(function () {
                    const url = $(this).attr('href');
                    const pageProductHandle = url.split("products/")[1].split("?")[0];
                    const isInProducts = products.some(product => product.handle === pageProductHandle);
                    if (isInProducts) {
                        $(this).closest('.card__information').find('.card-information').css('display', 'none');
                    }
                });
                const url = $('.product__title a').attr('href');
                if(url){
                    const pageProductHandle = url.split("products/")[1];
                    const isInProducts = products.some(product => product.handle === pageProductHandle);
                    if (isInProducts) {
                        $('#price-template--21887848022314__main').css('display', 'none');
                    }
                }
            }
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
            if (response.rule_exists_enabled === true){
                $('#price-template--21887848022314__main').css('display', 'none');
            }
        },
        error: function (error) {
            console.log('in error');
            console.log(error);
        }
    });
}

