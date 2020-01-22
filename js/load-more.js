jQuery(function($) {
    let blogPosts = $('.product-list-container');
    let alertdiv = $('.alert-div');

    alertdiv.append('<div class="col-ms-12"><a class="c-btn c-btn--pink c-posts__more btn btn-primary" href="#">Load More Product</a></div>');

    let moreButton = $('.c-posts__more'),
        page = 1,
        loading = false,
        maxpage = loadmore.maxpages;

    moreButton.on('click', function(e) {
        e.preventDefault();

        blogPosts.append('<div class="c-posts__loader"></div>');


        let blogLoader = $('.c-posts__loader');

        if (!loading) {
            loading = true;

            let data = {
                action: 'load_more_ajax',
                nonce: loadmore.nonce,
                page: page,
                query: loadmore.query
            };

            $.post(loadmore.url, data, function(res) {
                if (res.success) {
                    console.log('Current Page ' + page);
                    console.log('Max Pages ' + maxpage);

                    blogLoader.remove();

                    if (page >= maxpage) {

                        alertdiv.append('<div class="alert alert-info"><b>Alert info: </b>Sorry, there are no more products.</div>')
                    } else {

                    }

                    page = page + 1;
                    loading = false;
                } else {
                    console.log(res);
                }
            }).fail(function(xhr, textStatus, e) {
                console.log(xhr.responseText);
            });
        }
        return false;
    });

    $(document).ready(function() {
        //     let data = {
        //         action: 'load_more_ajax',
        //         nonce: loadmore.nonce,
        //         page: 1,
        //         query: loadmore.query
        //     };

        //     $.post(loadmore.url, data, function(res) {
        //         if (res.success) {
        //             blogPosts.append(res.data);
        //             blogPosts.append(moreButton);

        //             page = page;
        //             loading = false;
        //         } else {
        //             console.log(res);
        //         }
        //     });
        //     console.log('check');
        //$('.c-posts__more').click();
    });
    let scrollHandling = {
        allow: true,
        reallow: function() {
            scrollHandling.allow = true;
        },
        delay: 1400
    };

    $(window).scroll(function() {
        if (!loading && scrollHandling.allow) {
            scrollHandling.allow = false;

            setTimeout(scrollHandling.reallow, scrollHandling.delay);

            let buttonOffset = moreButton.offset().top - $(window).scrollTop();

            if (buttonOffset < 2000) {
                loading = true;

                let data = {
                    action: 'load_more_ajax',
                    nonce: loadmore.nonce,
                    page: page,
                    query: loadmore.query
                };

                $.post(loadmore.url, data, function(res) {
                    if (res.success) {
                        blogPosts.append(res.data);
                        page = page + 1;
                        loading = false;
                    } else {
                        console.log(res);
                    }
                });
            }
        }
    });

});