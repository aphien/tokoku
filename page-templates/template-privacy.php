<?php
/**
 * Template Name: Kebijakan Privasi
 * Template Post Type: page
 *
 * Halaman Kebijakan Privasi JualPlakat.com.
 * Konten tambahan dari editor halaman akan tampil sebagai "Ketentuan Tambahan".
 *
 * @package TokoKu
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$brand   = esc_html( tokoku_page_brand() );
$contact = tokoku_page_contact();
$wa_link = esc_url( 'https://wa.me/' . $contact['wa'] );
$email   = $contact['email'] ? '<a href="mailto:' . esc_attr( antispambot( $contact['email'] ) ) . '">' . esc_html( antispambot( $contact['email'] ) ) . '</a>' : '';

$sections = array(
    array(
        'id'      => 'pendahuluan',
        'title'   => 'Pendahuluan',
        'content' => '<p>Di <strong>' . $brand . '</strong>, kepercayaan Anda adalah fondasi dari setiap karya yang kami buat. Karena itu, kami berkomitmen untuk menjaga kerahasiaan dan keamanan data pribadi yang Anda percayakan kepada kami.</p>
            <p>Kebijakan Privasi ini menjelaskan secara transparan data apa saja yang kami kumpulkan, bagaimana data tersebut digunakan, dengan siapa data dapat dibagikan, serta hak-hak yang Anda miliki atas data pribadi Anda. Kebijakan ini disusun dengan mengacu pada Undang-Undang Nomor 27 Tahun 2022 tentang Pelindungan Data Pribadi (UU PDP) dan peraturan terkait lainnya di Indonesia.</p>',
    ),
    array(
        'id'      => 'data-dikumpulkan',
        'title'   => 'Data yang Kami Kumpulkan',
        'content' => '<p>Kami hanya mengumpulkan data yang benar-benar diperlukan untuk melayani pesanan Anda dengan baik:</p>
            <ul>
                <li><strong>Data identitas &amp; kontak</strong> &mdash; nama, nomor WhatsApp/telepon, alamat email, serta nama instansi atau perusahaan (jika ada).</li>
                <li><strong>Data pengiriman</strong> &mdash; nama penerima, alamat lengkap, dan nomor telepon penerima.</li>
                <li><strong>Data pesanan</strong> &mdash; jenis produk, jumlah, teks grafir, logo, foto, dan file desain yang Anda kirimkan.</li>
                <li><strong>Data transaksi</strong> &mdash; bukti pembayaran dan informasi rekening pengirim yang tercantum pada bukti transfer.</li>
                <li><strong>Data teknis</strong> &mdash; alamat IP, jenis browser, perangkat, halaman yang dikunjungi, dan waktu akses yang terekam secara otomatis saat Anda menjelajahi situs.</li>
            </ul>
            <p>Kami <strong>tidak</strong> meminta maupun menyimpan PIN, kata sandi perbankan, kode OTP, atau nomor kartu kredit Anda dalam bentuk apa pun.</p>',
    ),
    array(
        'id'      => 'cara-mengumpulkan',
        'title'   => 'Cara Kami Mengumpulkan Data',
        'content' => '<ul>
                <li><strong>Secara langsung</strong> &mdash; ketika Anda menghubungi kami via WhatsApp, email, atau mengisi formulir pemesanan dan kontak di situs.</li>
                <li><strong>Secara otomatis</strong> &mdash; melalui cookies dan teknologi serupa ketika Anda mengunjungi situs kami.</li>
                <li><strong>Dari pihak ketiga</strong> &mdash; misalnya informasi status pengiriman dari mitra ekspedisi.</li>
            </ul>',
    ),
    array(
        'id'      => 'penggunaan-data',
        'title'   => 'Bagaimana Kami Menggunakan Data Anda',
        'content' => '<p>Data Anda kami gunakan secara bertanggung jawab untuk tujuan berikut:</p>
            <ul>
                <li>Memproses pesanan, membuat mockup desain, dan memproduksi produk sesuai permintaan Anda.</li>
                <li>Mengonfirmasi pembayaran serta mengirimkan produk ke alamat tujuan.</li>
                <li>Berkomunikasi terkait status pesanan, revisi desain, maupun layanan purna jual.</li>
                <li>Menanggapi pertanyaan, keluhan, dan permintaan bantuan.</li>
                <li>Meningkatkan kualitas situs, layanan, dan pengalaman berbelanja Anda.</li>
                <li>Mengirimkan informasi promo atau katalog terbaru <strong>hanya apabila Anda bersedia</strong>, dan Anda dapat berhenti berlangganan kapan saja.</li>
                <li>Memenuhi kewajiban hukum, perpajakan, dan administrasi yang berlaku.</li>
            </ul>',
    ),
    array(
        'id'      => 'dasar-pemrosesan',
        'title'   => 'Dasar Pemrosesan Data',
        'content' => '<p>Kami memproses data pribadi Anda berdasarkan:</p>
            <ul>
                <li><strong>Persetujuan</strong> yang Anda berikan saat menghubungi kami atau melakukan pemesanan.</li>
                <li><strong>Pelaksanaan perjanjian</strong>, yaitu untuk menyelesaikan pesanan yang telah Anda konfirmasi.</li>
                <li><strong>Kewajiban hukum</strong> yang berlaku bagi kami sebagai pelaku usaha.</li>
                <li><strong>Kepentingan yang sah</strong>, seperti menjaga keamanan situs dan mencegah penipuan.</li>
            </ul>',
    ),
    array(
        'id'      => 'berbagi-data',
        'title'   => 'Berbagi Data dengan Pihak Ketiga',
        'content' => '<p><strong>Kami tidak pernah menjual, menyewakan, atau memperdagangkan data pribadi Anda.</strong> Data hanya dibagikan secara terbatas kepada pihak yang membantu kami melayani Anda, yaitu:</p>
            <ul>
                <li><strong>Mitra ekspedisi</strong> &mdash; nama, alamat, dan nomor telepon penerima untuk keperluan pengiriman.</li>
                <li><strong>Penyedia layanan komunikasi</strong> &mdash; seperti WhatsApp (Meta Platforms) yang tunduk pada kebijakan privasinya sendiri.</li>
                <li><strong>Penyedia infrastruktur</strong> &mdash; layanan hosting, keamanan, dan analitik situs yang terikat kewajiban kerahasiaan.</li>
                <li><strong>Otoritas berwenang</strong> &mdash; apabila diwajibkan oleh hukum atau perintah pengadilan yang sah.</li>
            </ul>',
    ),
    array(
        'id'      => 'cookies',
        'title'   => 'Cookies & Teknologi Serupa',
        'content' => '<p>Situs kami menggunakan cookies &mdash; berkas kecil yang disimpan di perangkat Anda &mdash; untuk:</p>
            <ul>
                <li>Menyimpan preferensi tampilan, seperti mode terang atau gelap.</li>
                <li>Menjaga situs berfungsi dengan cepat dan aman.</li>
                <li>Memahami cara pengunjung menggunakan situs sehingga kami dapat terus menyempurnakannya.</li>
            </ul>
            <p>Anda dapat mengatur atau menghapus cookies melalui pengaturan browser. Perlu diketahui bahwa menonaktifkan cookies tertentu dapat memengaruhi sebagian fungsi situs.</p>',
    ),
    array(
        'id'      => 'keamanan',
        'title'   => 'Keamanan Data',
        'content' => '<p>Kami menerapkan langkah-langkah pengamanan teknis dan organisasional yang wajar untuk melindungi data Anda dari akses, perubahan, pengungkapan, atau penghapusan yang tidak sah, antara lain:</p>
            <ul>
                <li>Koneksi terenkripsi (HTTPS/SSL) pada seluruh halaman situs.</li>
                <li>Pembatasan akses data hanya kepada tim yang memerlukannya untuk memproses pesanan.</li>
                <li>Pembaruan sistem dan perangkat lunak secara berkala.</li>
            </ul>
            <p>Meskipun demikian, tidak ada metode transmisi atau penyimpanan elektronik yang 100% aman. Apabila terjadi kegagalan pelindungan data yang berdampak pada Anda, kami akan memberitahukannya sesuai ketentuan peraturan yang berlaku.</p>',
    ),
    array(
        'id'      => 'retensi',
        'title'   => 'Masa Penyimpanan Data',
        'content' => '<p>Kami menyimpan data pribadi selama diperlukan untuk menyelesaikan pesanan, memberikan layanan purna jual, dan memenuhi kewajiban hukum maupun administrasi. File desain dan logo dapat kami simpan untuk memudahkan pemesanan ulang (repeat order) di kemudian hari.</p>
            <p>Setelah tidak lagi diperlukan, data akan dihapus atau dianonimkan secara aman. Anda dapat meminta penghapusan file desain Anda kapan saja.</p>',
    ),
    array(
        'id'      => 'hak-anda',
        'title'   => 'Hak-Hak Anda',
        'content' => '<p>Sesuai UU Pelindungan Data Pribadi, Anda memiliki hak untuk:</p>
            <ul>
                <li><strong>Mendapatkan informasi</strong> mengenai tujuan dan cara data Anda diproses.</li>
                <li><strong>Mengakses</strong> dan memperoleh salinan data pribadi Anda.</li>
                <li><strong>Memperbaiki</strong> data yang tidak akurat atau tidak lengkap.</li>
                <li><strong>Menghapus</strong> data pribadi Anda, sepanjang tidak bertentangan dengan kewajiban hukum.</li>
                <li><strong>Menarik persetujuan</strong> atas pemrosesan data, termasuk berhenti menerima pesan promosi.</li>
                <li><strong>Mengajukan keberatan</strong> atas pemrosesan data tertentu.</li>
            </ul>
            <p>Untuk menggunakan hak-hak tersebut, silakan hubungi kami melalui kanal resmi. Kami akan merespons permintaan Anda dalam waktu yang wajar setelah identitas Anda terverifikasi.</p>',
    ),
    array(
        'id'      => 'anak',
        'title'   => 'Privasi Anak',
        'content' => '<p>Layanan kami ditujukan bagi pengguna dewasa. Pemesanan atas nama anak di bawah umur hendaknya dilakukan oleh orang tua atau wali. Kami tidak dengan sengaja mengumpulkan data pribadi anak tanpa persetujuan orang tua/wali.</p>',
    ),
    array(
        'id'      => 'tautan-luar',
        'title'   => 'Tautan ke Situs Pihak Ketiga',
        'content' => '<p>Situs kami dapat memuat tautan ke situs atau platform lain, seperti marketplace, media sosial, dan WhatsApp. Kami tidak bertanggung jawab atas praktik privasi pihak tersebut, sehingga kami menyarankan Anda membaca kebijakan privasi masing-masing platform.</p>',
    ),
    array(
        'id'      => 'perubahan',
        'title'   => 'Perubahan Kebijakan Privasi',
        'content' => '<p>Kebijakan Privasi ini dapat kami perbarui sewaktu-waktu untuk mencerminkan perubahan layanan atau peraturan. Setiap perubahan akan dipublikasikan di halaman ini dengan tanggal berlaku terbaru. Dengan tetap menggunakan layanan kami setelah perubahan, Anda dianggap menyetujui kebijakan yang telah diperbarui.</p>',
    ),
    array(
        'id'      => 'kontak',
        'title'   => 'Hubungi Kami',
        'content' => '<p>Pertanyaan, permintaan, atau keluhan terkait privasi dan data pribadi dapat disampaikan kepada kami melalui:</p>
            <ul>
                <li>WhatsApp: <a href="' . $wa_link . '" target="_blank" rel="noopener noreferrer">' . esc_html( $contact['wa_display'] ) . '</a></li>'
                . ( $email ? '<li>Email: ' . $email . '</li>' : '' )
                . ( $contact['address'] ? '<li>Alamat: ' . esc_html( $contact['address'] ) . '</li>' : '' ) .
            '</ul>
            <p>Kami menghargai setiap masukan dan berkomitmen menjaga privasi Anda dengan sungguh-sungguh.</p>',
    ),
);

while ( have_posts() ) :
    the_post();
    get_template_part( 'template-parts/legal-page', null, array(
        'badge'    => 'Privasi & Keamanan',
        'icon'     => 'shield',
        'title'    => get_the_title() ? get_the_title() : 'Kebijakan Privasi',
        'lead'     => 'Data Anda adalah amanah. Kami menjelaskan secara terbuka bagaimana informasi pribadi Anda dikumpulkan, digunakan, dan dilindungi setiap kali Anda berinteraksi dengan ' . tokoku_page_brand() . '.',
        'updated'  => get_the_modified_date( 'j F Y' ),
        'summary'  => array(
            'Kami hanya mengumpulkan data yang dibutuhkan untuk memproses dan mengirimkan pesanan Anda.',
            'Data pribadi Anda tidak pernah dijual atau diperdagangkan kepada pihak mana pun.',
            'Data hanya dibagikan secara terbatas kepada mitra ekspedisi dan penyedia layanan pendukung.',
            'Kami tidak pernah meminta PIN, kata sandi, atau kode OTP perbankan Anda.',
            'Anda berhak mengakses, memperbaiki, dan meminta penghapusan data pribadi Anda kapan saja.',
        ),
        'sections' => $sections,
        'related'  => array(
            array(
                'label' => 'Syarat & Ketentuan',
                'url'   => tokoku_static_page_url( 'page-templates/template-terms.php' ),
                'icon'  => 'file',
            ),
            array(
                'label' => 'Tentang Kami',
                'url'   => tokoku_static_page_url( 'page-templates/template-tentang.php' ),
                'icon'  => 'award',
            ),
        ),
    ) );
endwhile;

get_footer();
