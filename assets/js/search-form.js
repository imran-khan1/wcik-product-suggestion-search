jQuery(document).ready(function ($) {


    let typingTimer;
    const typingDelay = 300; // Delay in milliseconds
    const suggestionsBox = $('#wcik-suggestions');

    $('#search_query').on('input', function () {
        clearTimeout(typingTimer);
        const query = $(this).val().trim();

        if (query.length > 2) {
            typingTimer = setTimeout(() => fetchSuggestions(query), typingDelay);
        } else {
            suggestionsBox.empty().hide();
        }
    });

    function fetchSuggestions(query) {
        $.ajax({
            url: wcikAjax.ajax_url,
            method: 'POST',
            data: {
                action: 'wcik_fetch_suggestions',
                security: wcikAjax.ajax_nonce,
                search_query: query,
            },
            success: function (response) {
                if (response.success) {
                    const suggestions = response.data;
                    let html = '';
                    suggestions.forEach((product) => {
                        html += `<div class="suggestion-item" data-id="${product.id}">${product.name}</div>`;
                    });
                    suggestionsBox.html(html).show();
                } else {
                    suggestionsBox.html('<div class="no-suggestions">No products found.</div>').show();
                }
            },
            error: function () {
                suggestionsBox.html('<div class="no-suggestions">Error fetching suggestions.</div>').show();
            },
        });
    }

    // Handle suggestion click
    $(document).on('click', '.suggestion-item', function () {
        const selectedName = $(this).text();
        $('#search_query').val(selectedName);
        suggestionsBox.empty().hide();
    });

    // Close suggestions box when clicking outside
    $(document).on('click', function (e) {
        if (!$(e.target).closest('#wcik-search-form').length) {
            suggestionsBox.empty().hide();
        }
    });











    $('#wcik-search-form').on('submit', function (e) {
        e.preventDefault();

        const data = {
            action: 'wcik_search_products',
            security: wcikAjax.ajax_nonce, // Nonce passed here
            search_query: $('input[name="search_query"]').val(),
            category: $('select[name="category"]').val(),
            tags: $('input[name="tags"]').val(),
            price_min: $('input[name="price_min"]').val(),
            price_max: $('input[name="price_max"]').val(),
        };

        $.ajax({
            url: wcikAjax.ajax_url,
            method: 'POST',
            data: data,
            beforeSend: function () {
                $('#wcik-search-results').html('<p>Loading...</p>');
            },
            success: function (response, textStatus, xhr) {
                console.log(response.data.length);
                if (response.success == true && response.data.length >= 1) {
                    const results = response.data;
                    console.log(response.data);
                    let html = '<ul>';
                    results.forEach((product) => {
                        html += `<li>
                            <a href="${product.link}">${product.name} - ${product.price}</a>
                            "${product.image}"
                        </li>`;
                    });
                    html += '</ul>';
                    $('#wcik-search-results').html(html);
                } else {
                    $('#wcik-search-results').html('<p>No products found.</p>');
                }
            },
            
            error: function () {
                $('#wcik-search-results').html('<p>Error fetching results.</p>');
            },
        });
    });
});
