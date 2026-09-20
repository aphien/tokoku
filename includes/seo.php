<?php
/**
 * SEO, Canonical & Schema Markup Settings
 *
 * @package TokoKu
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Remove core WP canonical & robots to ensure TokoKu is the single source of truth without duplicates
remove_action( 'wp_head', 'rel_canonical' );
remove_action( 'wp_head', 'wp_robots', 1 );

/**
 * Filter WordPress 5.7+ wp_robots directives
 */
add_filter( 'wp_robots', function( $robots ) {
    if ( is_search() || is_404() ) {
        $robots['noindex'] = true;
        $robots['follow']  = true;
    } else {
        $robots['index']             = true;
        $robots['follow']            = true;
        $robots['max-image-preview'] = 'large';
        $robots['max-snippet']       = -1;
        $robots['max-video-preview'] = -1;
    }
    return $robots;
} );

/**
 * Dapatkan URL Canonical yang akurat untuk halaman saat ini
 *
 * @return string URL canonical
 */
function tokoku_get_canonical_url() {
    global $wp;

    if ( is_front_page() ) {
        $canonical = home_url( '/' );
    } elseif ( is_home() ) {
        $page_for_posts = get_option( 'page_for_posts' );
        $canonical      = $page_for_posts ? get_permalink( $page_for_posts ) : home_url( '/' );
    } elseif ( is_singular() ) {
        $canonical = get_permalink();
    } elseif ( is_post_type_archive( 'produk' ) ) {
        $canonical = get_post_type_archive_link( 'produk' );
    } elseif ( is_category() || is_tag() || is_tax() ) {
        $term = get_queried_object();
        if ( $term && ! is_wp_error( $term ) ) {
            $canonical = get_term_link( $term );
        } else {
            $canonical = home_url( add_query_arg( array(), $wp->request ) );
        }
    } elseif ( is_search() ) {
        $canonical = home_url( '?s=' . rawurlencode( get_search_query() ) );
    } elseif ( is_author() ) {
        $author    = get_queried_object();
        $canonical = $author ? get_author_posts_url( $author->ID ) : home_url( '/' );
    } elseif ( is_date() ) {
        if ( is_day() ) {
            $canonical = get_day_link( get_query_var( 'year' ), get_query_var( 'monthnum' ), get_query_var( 'day' ) );
        } elseif ( is_month() ) {
            $canonical = get_month_link( get_query_var( 'year' ), get_query_var( 'monthnum' ) );
        } elseif ( is_year() ) {
            $canonical = get_year_link( get_query_var( 'year' ) );
        } else {
            $canonical = home_url( '/' );
        }
    } else {
        $canonical = home_url( ! empty( $wp->request ) ? '/' . ltrim( $wp->request, '/' ) . '/' : '/' );
    }

    // Tangani pagination jika halaman > 1
    if ( is_paged() ) {
        $paged = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
        if ( $paged > 1 ) {
            $paged_url = get_pagenum_link( $paged );
            if ( ! empty( $paged_url ) && ! is_wp_error( $paged_url ) ) {
                $canonical = $paged_url;
            }
        }
    }

    return ! empty( $canonical ) && ! is_wp_error( $canonical ) ? $canonical : home_url( '/' );
}

/**
 * Output SEO Meta Tags, Canonical & Robots in <head>
 */
function tokoku_seo_meta_tags() {
    global $post;

    // Defaults from Customizer
    $default_desc     = get_theme_mod( 'tokoku_seo_desc', get_bloginfo( 'description' ) );
    $default_keywords = get_theme_mod( 'tokoku_seo_keywords', '' );
    $default_image    = get_theme_mod( 'tokoku_seo_og_image', '' );
    if ( ! $default_image ) {
        $site_icon_id = get_option( 'site_icon' );
        if ( $site_icon_id ) {
            $default_image = wp_get_attachment_image_url( $site_icon_id, 'full' );
        }
    }

    $title       = '';
    $description = '';
    $keywords    = $default_keywords;
    $image       = $default_image;
    $url         = tokoku_get_canonical_url();
    $type        = 'website';

    if ( is_singular() ) {
        $title = get_the_title();
        
        $excerpt = wp_strip_all_tags( get_the_excerpt() );
        if ( empty( $excerpt ) ) {
            $excerpt = wp_trim_words( wp_strip_all_tags( get_the_content() ), 30 );
        }
        $description = ! empty( $excerpt ) ? $excerpt : $default_desc;

        if ( has_post_thumbnail() ) {
            $image = get_the_post_thumbnail_url( null, 'full' );
        }
        
        if ( is_singular( 'produk' ) ) {
            $type = 'product';
        } else {
            $type = 'article';
        }
    } elseif ( is_post_type_archive( 'produk' ) || is_tax( 'kategori_produk' ) || is_tax( 'tag_produk' ) ) {
        if ( is_tax() ) {
            $title       = single_term_title( '', false );
            $description = wp_strip_all_tags( term_description() );
        } else {
            $title = __( 'Semua Produk', 'tokoku' );
        }
        if ( empty( $description ) ) {
            $description = $default_desc;
        }
    } elseif ( is_category() || is_tag() ) {
        $title       = single_term_title( '', false );
        $description = wp_strip_all_tags( term_description() );
        if ( empty( $description ) ) {
            $description = $default_desc;
        }
    } elseif ( is_search() ) {
        $title       = sprintf( __( 'Pencarian: &ldquo;%s&rdquo;', 'tokoku' ), get_search_query() );
        $description = sprintf( __( 'Hasil pencarian untuk &ldquo;%s&rdquo; di %s', 'tokoku' ), get_search_query(), get_bloginfo( 'name' ) );
    } elseif ( is_404() ) {
        $title       = __( 'Halaman Tidak Ditemukan', 'tokoku' );
        $description = __( 'Halaman yang Anda cari tidak ditemukan atau telah dipindahkan.', 'tokoku' );
    } else {
        $title       = get_bloginfo( 'name' );
        $description = $default_desc;
    }

    $title       = wp_strip_all_tags( $title );
    $description = wp_strip_all_tags( $description );

    // Output tags
    echo "<!-- TokoKu SEO Meta Tags & Canonical -->\n";

    // Canonical
    if ( $url ) {
        echo '<link rel="canonical" href="' . esc_url( $url ) . '">' . "\n";
    }

    // Robots Meta Tag
    if ( get_option( 'blog_public' ) == '0' ) {
        echo '<meta name="robots" content="noindex, nofollow">' . "\n";
    } elseif ( is_search() || is_404() ) {
        echo '<meta name="robots" content="noindex, follow">' . "\n";
    } else {
        echo '<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">' . "\n";
    }

    if ( $description ) {
        echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
    }
    if ( $keywords ) {
        echo '<meta name="keywords" content="' . esc_attr( $keywords ) . '">' . "\n";
    }

    // Open Graph & Twitter Cards
    if ( $title ) {
        echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
    }
    if ( $description ) {
        echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '">' . "\n";
    }
    if ( $image ) {
        echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
        echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">' . "\n";
        echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    }
    if ( $url ) {
        echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
    }
    echo '<meta property="og:type" content="' . esc_attr( $type ) . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
    echo '<meta property="og:locale" content="' . esc_attr( get_locale() ) . '">' . "\n";
}
add_action( 'wp_head', 'tokoku_seo_meta_tags', 2 );

/**
 * Output Product Schema Markup (JSON-LD) in <head> for Single Product Pages
 */
function tokoku_product_schema_markup() {
    // Hanya tampilkan di halaman single produk
    if ( ! is_singular( 'produk' ) ) {
        return;
    }

    global $post;
    
    // Ambil data meta produk
    $harga       = get_post_meta( $post->ID, '_produk_harga', true );
    $sku         = get_post_meta( $post->ID, '_produk_sku', true );
    $mata_uang   = get_theme_mod( 'tokoku_currency', 'IDR' );
    if ( $mata_uang === 'Rp' ) {
        $mata_uang = 'IDR';
    }
    
    // Stok Status
    $jumlah_stok  = get_post_meta( $post->ID, '_produk_jumlah_stok', true );
    $availability = 'https://schema.org/InStock';
    $stok         = tokoku_get_stok_status();
    if ( isset( $stok['class'] ) && $stok['class'] === 'stok-preorder' ) {
        $availability = 'https://schema.org/PreOrder';
    } elseif ( ( isset( $stok['class'] ) && $stok['class'] === 'stok-habis' ) || ( is_numeric( $jumlah_stok ) && $jumlah_stok <= 0 ) ) {
        $availability = 'https://schema.org/OutOfStock';
    }

    // Gambar Utama
    $image_url = get_the_post_thumbnail_url( $post->ID, 'full' );
    if ( ! $image_url ) {
        $image_url = esc_url( TOKOKU_URI . '/assets/images/placeholder.svg' );
    }

    // Deskripsi (hilangkan tag HTML)
    $description = wp_strip_all_tags( get_the_excerpt() );
    if ( empty( $description ) ) {
        $description = wp_trim_words( wp_strip_all_tags( get_the_content() ), 30 );
    }
    if ( empty( $description ) ) {
        $description = sprintf( __( '%s kualitas terbaik dengan pengerjaan rapi dan harga terjangkau.', 'tokoku' ), get_the_title() );
    }

    // Membuat array Schema Markup
    $schema = array(
        '@context'    => 'https://schema.org/',
        '@type'       => 'Product',
        'name'        => get_the_title(),
        'image'       => $image_url,
        'description' => $description,
        'sku'         => ! empty( $sku ) ? $sku : 'SKU-' . $post->ID,
        'brand'       => array(
            '@type' => 'Brand',
            'name'  => get_bloginfo( 'name' ),
        ),
    );

    // Kategori produk jika ada
    $terms = get_the_terms( $post->ID, 'kategori_produk' );
    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
        $schema['category'] = $terms[0]->name;
    }

    // Guard Offers: Hanya output penawaran harga jika harga valid dan > 0
    if ( ! empty( $harga ) && is_numeric( $harga ) && floatval( $harga ) > 0 ) {
        $schema['offers'] = array(
            '@type'         => 'Offer',
            'url'           => get_permalink(),
            'priceCurrency' => $mata_uang,
            'price'         => floatval( $harga ),
            'availability'  => $availability,
            'itemCondition' => 'https://schema.org/NewCondition',
        );
    }

    // Output JSON-LD
    echo "<!-- TokoKu Product Schema Markup -->\n";
    echo '<script type="application/ld+json">' . "\n";
    echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . "\n";
    echo '</script>' . "\n";
}
add_action( 'wp_head', 'tokoku_product_schema_markup', 10 );

/**
 * Output Breadcrumb Schema Markup (JSON-LD) in <head>
 */
function tokoku_breadcrumb_schema_markup() {
    // Jangan tampilkan di front page atau 404
    if ( is_front_page() || is_404() ) {
        return;
    }

    $breadcrumbs = array();
    $position    = 1;

    // 1. Beranda
    $breadcrumbs[] = array(
        '@type'    => 'ListItem',
        'position' => $position++,
        'name'     => __( 'Beranda', 'tokoku' ),
        'item'     => home_url( '/' ),
    );

    if ( is_singular( 'produk' ) ) {
        // Katalog Produk
        $breadcrumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => __( 'Katalog Produk', 'tokoku' ),
            'item'     => get_post_type_archive_link( 'produk' ) ?: home_url( '/produk/' ),
        );

        // Kategori Produk
        $terms = get_the_terms( get_the_ID(), 'kategori_produk' );
        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            $breadcrumbs[] = array(
                '@type'    => 'ListItem',
                'position' => $position++,
                'name'     => $terms[0]->name,
                'item'     => get_term_link( $terms[0] ),
            );
        }

        // Produk
        $breadcrumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => get_the_title(),
            'item'     => get_permalink(),
        );
    } elseif ( is_singular( 'post' ) ) {
        // Blog
        $page_for_posts = get_option( 'page_for_posts' );
        $blog_url       = $page_for_posts ? get_permalink( $page_for_posts ) : home_url( '/blog/' );
        $breadcrumbs[]  = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => __( 'Blog', 'tokoku' ),
            'item'     => $blog_url,
        );

        // Kategori Post
        $cats = get_the_category();
        if ( ! empty( $cats ) ) {
            $breadcrumbs[] = array(
                '@type'    => 'ListItem',
                'position' => $position++,
                'name'     => $cats[0]->name,
                'item'     => get_category_link( $cats[0]->term_id ),
            );
        }

        // Judul Artikel
        $breadcrumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => get_the_title(),
            'item'     => get_permalink(),
        );
    } elseif ( is_page() ) {
        global $post;
        if ( ! empty( $post->post_parent ) ) {
            $ancestors = array_reverse( get_post_ancestors( $post->ID ) );
            foreach ( $ancestors as $ancestor_id ) {
                $breadcrumbs[] = array(
                    '@type'    => 'ListItem',
                    'position' => $position++,
                    'name'     => get_the_title( $ancestor_id ),
                    'item'     => get_permalink( $ancestor_id ),
                );
            }
        }
        $breadcrumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => get_the_title(),
            'item'     => get_permalink(),
        );
    } elseif ( is_post_type_archive( 'produk' ) ) {
        $breadcrumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => __( 'Katalog Produk', 'tokoku' ),
            'item'     => get_post_type_archive_link( 'produk' ) ?: home_url( '/produk/' ),
        );
    } elseif ( is_tax( 'kategori_produk' ) || is_tax( 'tag_produk' ) ) {
        $breadcrumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => __( 'Katalog Produk', 'tokoku' ),
            'item'     => get_post_type_archive_link( 'produk' ) ?: home_url( '/produk/' ),
        );
        $term = get_queried_object();
        if ( $term && ! is_wp_error( $term ) ) {
            $breadcrumbs[] = array(
                '@type'    => 'ListItem',
                'position' => $position++,
                'name'     => $term->name,
                'item'     => get_term_link( $term ),
            );
        }
    } elseif ( is_category() || is_tag() ) {
        $page_for_posts = get_option( 'page_for_posts' );
        $blog_url       = $page_for_posts ? get_permalink( $page_for_posts ) : home_url( '/blog/' );
        $breadcrumbs[]  = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => __( 'Blog', 'tokoku' ),
            'item'     => $blog_url,
        );
        $term = get_queried_object();
        if ( $term && ! is_wp_error( $term ) ) {
            $breadcrumbs[] = array(
                '@type'    => 'ListItem',
                'position' => $position++,
                'name'     => $term->name,
                'item'     => get_term_link( $term ),
            );
        }
    } elseif ( is_home() ) {
        $breadcrumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => __( 'Blog', 'tokoku' ),
            'item'     => get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/blog/' ),
        );
    }

    if ( count( $breadcrumbs ) < 2 ) {
        return;
    }

    $schema = array(
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $breadcrumbs,
    );

    echo "<!-- TokoKu Breadcrumb Schema Markup -->\n";
    echo '<script type="application/ld+json">' . "\n";
    echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . "\n";
    echo '</script>' . "\n";
}
add_action( 'wp_head', 'tokoku_breadcrumb_schema_markup', 11 );
