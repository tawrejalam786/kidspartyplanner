(function ($) {
    "use strict";

    const csrf = $('meta[name="csrf-token"]').attr('content');
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': csrf } });

    if ($('.hero-swiper').length) {
        new Swiper('.hero-swiper', {
            loop: true,
            effect: 'fade',
            speed: 700,
            autoplay: { delay: 3000, disableOnInteraction: false },
            pagination: { el: '.hero-swiper .swiper-pagination', clickable: true },
        });
    }

    if ($('.testimonial-swiper').length) {
        new Swiper('.testimonial-swiper', {
            loop: true,
            spaceBetween: 18,
            autoplay: { delay: 3800, disableOnInteraction: false },
            breakpoints: { 0: { slidesPerView: 1 }, 768: { slidesPerView: 2 }, 1200: { slidesPerView: 3 } },
        });
    }

    if ($('.detail-swiper').length) {
        new Swiper('.detail-swiper', {
            loop: true,
            
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
            pagination: { el: '.detail-swiper .swiper-pagination', clickable: true },
        });
    }

    const activityItems = $('[data-booking-activity]');
    if (activityItems.length) {
        let activityIndex = 0;
        const showActivity = function () {
            activityItems.removeClass('is-visible');
            const $item = activityItems.eq(activityIndex % activityItems.length);
            window.setTimeout(function () { $item.addClass('is-visible'); }, 100);
            window.setTimeout(function () { $item.removeClass('is-visible'); }, 4600);
            activityIndex += 1;
        };
        window.setTimeout(showActivity, 1800);
        window.setInterval(showActivity, 7000);
    }

    $(document).on('input', '[data-price-range]', function () {
        $(this).closest('form').find('[data-price-output]').text($(this).val());
    });

    function loadServices($form, url) {
        const $results = $('#service-results');
        $results.css('opacity', .45);
        $.get(url || $form.attr('action') || window.location.pathname, $form.serialize(), function (response) {
            $results.html(response.html).css('opacity', 1);
            $('#service-count').text(response.count);
            $('#mobile-service-count').text(response.count);
            const searchTerm = $form.find('[name="search"]').val().trim();
            if (searchTerm) $('[data-mobile-result-query]').text('\u201c' + searchTerm + '\u201d');
            const query = $form.serialize();
            history.replaceState(null, '', window.location.pathname + (query ? '?' + query : ''));
            const offcanvasElement = $form.closest('.offcanvas').get(0);
            if (offcanvasElement && window.bootstrap) {
                bootstrap.Offcanvas.getOrCreateInstance(offcanvasElement).hide();
                document.getElementById('service-results')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }).fail(function () {
            $results.css('opacity', 1);
            alert('Unable to load filtered services.');
        });
    }

    $(document).on('submit', '.service-filter-form', function (event) {
        event.preventDefault();
        loadServices($(this), $(this).attr('action') || window.location.pathname);
    });

    $(document).on('change', '.desktop-service-filter select, .desktop-service-filter input[type="range"]', function () {
        $(this).closest('form').trigger('submit');
    });

    $(document).on('click', '#service-results .pagination a', function (event) {
        event.preventDefault();
        const $form = $('.service-filter-form:visible').first();
        loadServices($form, $(this).attr('href'));
    });

    $(document).on('click', '#pay-razorpay', function () {
        const $button = $(this);
        $button.prop('disabled', true).text('Creating order...');

        $.post($button.data('order-url'))
            .done(function (order) {
                const razorpay = new Razorpay({
                    key: order.key,
                    amount: order.amount,
                    currency: order.currency,
                    name: order.name,
                    description: order.description,
                    order_id: order.order_id,
                    handler: function (response) {
                        $.post($button.data('verify-url'), response)
                            .done(function (result) {
                                window.location.href = result.redirect;
                            })
                            .fail(function (xhr) {
                                alert(xhr.responseJSON?.message || 'Payment verification failed.');
                                window.location.href = $button.data('failed-url');
                            });
                    },
                    modal: {
                        ondismiss: function () {
                            $button.prop('disabled', false).html('<i class="fa-solid fa-credit-card"></i> Pay with Razorpay');
                        }
                    },
                    theme: { color: '#ef303e' }
                });
                razorpay.open();
            })
            .fail(function (xhr) {
                alert(xhr.responseJSON?.message || 'Unable to create Razorpay order.');
                $button.prop('disabled', false).html('<i class="fa-solid fa-credit-card"></i> Pay with Razorpay');
            });
    });

    const $bookingForm = $('.booking-form');

    function formatMoney(value) {
        return '\u20B9' + Math.round(Number(value || 0)).toLocaleString('en-IN');
    }

    function updateBookingPreview() {
        if (!$bookingForm.length) return;

        let base = Number($bookingForm.data('base-price') || 0);
        const $service = $bookingForm.find('[data-booking-service]');
        const $package = $bookingForm.find('[data-booking-package]');

        if ($service.length || $package.length) {
            const servicePrice = Number($service.find('option:selected').data('price') || 0);
            const packagePrice = Number($package.find('option:selected').data('price') || 0);
            base = servicePrice || packagePrice;
        }

        $bookingForm.find('[data-addon-price]:checked').each(function () {
            base += Number($(this).data('addon-price') || 0);
        });

        const $city = $bookingForm.find('[data-city-payment] option:selected');
        const fee = Number($city.data('fee') || 0);
        const taxPercent = Number($city.data('tax') || 0);
        const advancePercent = Number($city.data('advance') || 0);
        const minimum = Number($city.data('minimum') || 0);
        const tax = (base + fee) * taxPercent / 100;
        const total = base + fee + tax;
        const paymentType = $bookingForm.find('[name="payment_type"]:checked').val() || 'advance';
        const due = paymentType === 'full' ? total : Math.min(total, Math.max(minimum, total * advancePercent / 100));

        $bookingForm.find('[data-preview-subtotal]').text(formatMoney(base));
        $bookingForm.find('[data-preview-fee]').text(formatMoney(fee));
        $bookingForm.find('[data-preview-tax]').text(formatMoney(tax));
        $bookingForm.find('[data-preview-total]').text(formatMoney(total));
        $bookingForm.find('[data-preview-due]').text(formatMoney(due));
        $bookingForm.find('[data-city-note]').text(($city.data('city') || 'Selected city') + ': ' + ($city.data('note') || 'Payment details will be confirmed before checkout.') + ' Coupon discount is applied after submission.');
    }

    $bookingForm.on('change', '[data-booking-service]', function () {
        if ($(this).val()) $bookingForm.find('[data-booking-package]').val('');
        updateBookingPreview();
    });

    $bookingForm.on('change', '[data-booking-package]', function () {
        if ($(this).val()) $bookingForm.find('[data-booking-service]').val('');
        updateBookingPreview();
    });

    $bookingForm.on('change', '[data-city-payment], [data-addon-price], [name="payment_type"]', updateBookingPreview);
    updateBookingPreview();

    function filterAreaOptions($city, $area, cityDataKey) {
        if (!$city.length || !$area.length) return;
        const cityId = String($city.find('option:selected').data(cityDataKey) || $city.val() || '');
        $area.find('option[data-city]').each(function () {
            const matches = String($(this).data('city')) === cityId;
            $(this).prop('hidden', !matches).prop('disabled', !matches);
            if (!matches && $(this).is(':selected')) $area.val('');
        });
    }

    const $bookingCity = $('[data-city-payment]');
    const $bookingArea = $('[data-booking-area]');
    $bookingCity.on('change', function () { filterAreaOptions($bookingCity, $bookingArea, 'city-id'); });
    filterAreaOptions($bookingCity, $bookingArea, 'city-id');

    function updateCheckoutSummary() {
        const $summary = $('[data-checkout-summary]');
        if (!$summary.length) return;
        const $city = $('[data-checkout-city] option:selected');
        const cityId = String($city.val() || '');
        let subtotal = 0;

        $summary.find('.checkout-item').each(function () {
            const prices = $(this).data('prices') || {};
            const price = Number(prices[cityId] || $(this).data('default-price') || 0);
            subtotal += price * Number($(this).data('quantity') || 1) + Number($(this).data('addons') || 0);
        });

        const travel = Number($('[data-checkout-area] option:selected').data('travel') || 0);
        const fee = Number($city.data('fee') || 0) + travel;
        const tax = (subtotal + fee) * Number($city.data('tax') || 0) / 100;
        const total = subtotal + fee + tax;
        const type = $('[name="payment_type"]:checked').val() || 'advance';
        const due = type === 'full' ? total : Math.min(total, Math.max(Number($city.data('minimum') || 0), total * Number($city.data('advance') || 30) / 100));

        $('[data-checkout-subtotal]').text(formatMoney(subtotal));
        $('[data-checkout-fee]').text(formatMoney(fee));
        $('[data-checkout-tax]').text(formatMoney(tax));
        $('[data-checkout-total]').text(formatMoney(total));
        $('[data-checkout-due]').text(formatMoney(due));
    }

    const $checkoutCity = $('[data-checkout-city]');
    const $checkoutArea = $('[data-checkout-area]');
    $checkoutCity.on('change', function () {
        filterAreaOptions($checkoutCity, $checkoutArea, 'unused');
        updateCheckoutSummary();
    });
    $checkoutArea.on('change', updateCheckoutSummary);
    $('[name="payment_type"]').on('change', updateCheckoutSummary);
    filterAreaOptions($checkoutCity, $checkoutArea, 'unused');
    updateCheckoutSummary();

    $(document).on('click', '[data-qty-minus], [data-qty-plus]', function () {
        const $input = $(this).siblings('input[type="number"]');
        const delta = $(this).is('[data-qty-plus]') ? 1 : -1;
        const min = Number($input.attr('min') || 1);
        const max = Number($input.attr('max') || 10);
        $input.val(Math.max(min, Math.min(max, Number($input.val() || min) + delta)));
    });
})(jQuery);
