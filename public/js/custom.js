$(document).ready(function () {
    $('#filter-menu-btn').on('click', function () {
        $(".sidebar-filter").toggleClass("close");
    })

    if ($(".find-doc-result").length > 0) {
        $('.find-doc-result').slick({
            autoplay: false,
            infinite: false,
            arrows: true,
            slidesToShow: 5,
            slidesToScroll: 1,
            prevArrow: "<button type='button' class='slick-prev'><i class='fa fa-chevron-left'></i></button>",
            nextArrow: "<button type='button' class='slick-next'><i class='fa fa-chevron-right'></i></button>",
        });
    }
});