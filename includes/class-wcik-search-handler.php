<?php

class WCIK_Search_Handler {
    public static function render_search_form() {
        ob_start();

        // Get plugin settings
        $enable_categories = get_option( 'wcik_enable_categories', false );
        $enable_tags = get_option( 'wcik_enable_tags', false );
        $enable_price = get_option( 'wcik_enable_price', false );

        ?>
        <form id="wcik-search-form" method="POST">
        <input type="text" name="search_query" id="search_query" placeholder="Search for products..." autocomplete="off" required />
        <div id="wcik-suggestions" style="position: absolute; background: #fff; border: 1px solid #ccc; z-index: 1000;"></div>

            <?php if ( $enable_categories ) : ?>
                <select name="category">
                    <option value="">Select Category</option>
                    <?php
                    $categories = get_terms( 'product_cat', [ 'hide_empty' => true ] );
                    foreach ( $categories as $category ) {
                        echo '<option value="' . esc_attr( $category->term_id ) . '">' . esc_html( $category->name ) . '</option>';
                    }
                    ?>
                </select>
            <?php endif; ?>

            <?php if ( $enable_tags ) : ?>
                <input type="text" name="tags" placeholder="Tags (comma separated)" />
            <?php endif; ?>

            <?php if ( $enable_price ) : ?>
                <input type="number" name="price_min" placeholder="Min Price" />
                <input type="number" name="price_max" placeholder="Max Price" />
            <?php endif; ?>

            <?php do_action( 'wcik_add_search_fields' ); ?>

            <button type="submit">Search</button>
        </form>
        <div id="wcik-search-results"></div>
        <?php

        return ob_get_clean();
    }
}
