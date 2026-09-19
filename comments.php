<?php
/**
 * The template for displaying comments
 *
 * @package TokoKu
 */

if ( post_password_required() ) {
    return;
}
?>

<section id="comments" class="comments-area">

    <?php if ( have_comments() ) : ?>
        <div class="comments-header">
            <h3 class="comments-title">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
                <span>
                    <?php
                    $comments_number = get_comments_number();
                    if ( '1' === $comments_number ) {
                        printf( esc_html__( '1 Komentar pada &ldquo;%s&rdquo;', 'tokoku' ), get_the_title() );
                    } else {
                        printf(
                            /* translators: 1: number of comments, 2: post title */
                            esc_html( _nx( '%1$s Komentar pada &ldquo;%2$s&rdquo;', '%1$s Komentar pada &ldquo;%2$s&rdquo;', $comments_number, 'comments title', 'tokoku' ) ),
                            number_format_i18n( $comments_number ),
                            get_the_title()
                        );
                    }
                    ?>
                </span>
            </h3>
        </div>

        <ol class="comment-list">
            <?php
            wp_list_comments( array(
                'style'       => 'ol',
                'short_ping'  => true,
                'avatar_size' => 48,
                'reply_text'  => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 14 4 9 9 4"></polyline><path d="M20 20v-7a4 4 0 0 0-4-4H4"></path></svg> ' . esc_html__( 'Balas', 'tokoku' ),
            ) );
            ?>
        </ol>

        <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
            <nav class="comment-navigation pagination" aria-label="<?php esc_attr_e( 'Navigasi Komentar', 'tokoku' ); ?>">
                <div class="nav-previous"><?php previous_comments_link( __( '&larr; Komentar Sebelumnya', 'tokoku' ) ); ?></div>
                <div class="nav-next"><?php next_comments_link( __( 'Komentar Selanjutnya &rarr;', 'tokoku' ) ); ?></div>
            </nav>
        <?php endif; ?>

    <?php endif; ?>

    <?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
        <p class="no-comments"><?php esc_html_e( 'Kolom komentar telah ditutup.', 'tokoku' ); ?></p>
    <?php endif; ?>

    <?php
    $commenter     = wp_get_current_commenter();
    $req           = get_option( 'require_name_email' );
    $required_attr = ( $req ? ' required="required"' : '' );

    $fields = array(
        'author' => '<div class="comment-form-row"><div class="comment-form-group comment-form-author">' .
                    '<label for="author">' . esc_html__( 'Nama Lengkap', 'tokoku' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label>' .
                    '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" placeholder="' . esc_attr__( 'Tulis nama Anda...', 'tokoku' ) . '"' . $required_attr . ' />' .
                    '</div>',
        'email'  => '<div class="comment-form-group comment-form-email">' .
                    '<label for="email">' . esc_html__( 'Alamat Email', 'tokoku' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label>' .
                    '<input id="email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" placeholder="' . esc_attr__( 'nama@email.com', 'tokoku' ) . '"' . $required_attr . ' />' .
                    '</div></div>',
        'url'    => '<div class="comment-form-group comment-form-url">' .
                    '<label for="url">' . esc_html__( 'Situs Web (Opsional)', 'tokoku' ) . '</label>' .
                    '<input id="url" name="url" type="url" value="' . esc_attr( $commenter['comment_author_url'] ) . '" placeholder="' . esc_attr__( 'https://domainanda.com', 'tokoku' ) . '" />' .
                    '</div>',
    );

    $comment_form_args = array(
        'title_reply'          => esc_html__( 'Tinggalkan Komentar', 'tokoku' ),
        'title_reply_to'       => esc_html__( 'Balas Komentar kepada %s', 'tokoku' ),
        'title_reply_before'   => '<div class="reply-header"><div class="reply-badge"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg> <span>Ruang Diskusi</span></div><h3 id="reply-title" class="comment-reply-title">',
        'title_reply_after'    => '</h3></div>',
        'cancel_reply_before'  => ' <span class="cancel-reply">',
        'cancel_reply_after'   => '</span>',
        'cancel_reply_link'    => esc_html__( 'Batal Balas', 'tokoku' ),
        'comment_notes_before' => '<p class="comment-notes"><span id="email-notes">' . esc_html__( 'Alamat email Anda tidak akan dipublikasikan. Bagian dengan tanda * wajib diisi.', 'tokoku' ) . '</span></p>',
        'fields'               => $fields,
        'comment_field'        => '<div class="comment-form-group comment-form-comment">' .
                                  '<label for="comment">' . esc_html__( 'Komentar Anda', 'tokoku' ) . ' <span class="required">*</span></label>' .
                                  '<textarea id="comment" name="comment" rows="5" placeholder="' . esc_attr__( 'Tulis komentar, opini, atau pertanyaan Anda terkait artikel ini di sini...', 'tokoku' ) . '" required="required"></textarea>' .
                                  '</div>',
        'submit_button'        => '<button name="%1$s" type="submit" id="%2$s" class="%3$s">' .
                                  '<span>%4$s</span> ' .
                                  '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>' .
                                  '</button>',
        'submit_field'         => '<div class="form-submit">%1$s %2$s</div>',
        'class_submit'         => 'btn btn-primary btn-submit-comment',
        'label_submit'         => esc_html__( 'Kirim Komentar', 'tokoku' ),
    );

    comment_form( $comment_form_args );
    ?>

</section>
