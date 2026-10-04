<?php
/**
 * Template Name: Syarat & Ketentuan
 * Template Post Type: page
 *
 * Halaman Syarat & Ketentuan Layanan JualPlakat.com.
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
        'id'      => 'penerimaan',
        'title'   => 'Penerimaan Ketentuan',
        'content' => '<p>Selamat datang di <strong>' . $brand . '</strong>. Syarat &amp; Ketentuan ini merupakan perjanjian yang mengikat antara Anda (&ldquo;Pelanggan&rdquo;) dan ' . $brand . ' (&ldquo;Kami&rdquo;) atas penggunaan situs web serta seluruh layanan pemesanan plakat, piala, trophy, dan souvenir penghargaan yang kami sediakan.</p>
            <p>Dengan mengakses situs, menghubungi kami, atau melakukan pemesanan, Anda dianggap telah membaca, memahami, dan menyetujui seluruh ketentuan di bawah ini. Apabila Anda tidak menyetujui sebagian atau seluruh isi ketentuan ini, kami sarankan untuk tidak melanjutkan penggunaan layanan.</p>',
    ),
    array(
        'id'      => 'definisi',
        'title'   => 'Definisi',
        'content' => '<ul>
                <li><strong>Produk</strong> &mdash; plakat (akrilik, kayu, resin, logam), piala, trophy, medali, dan souvenir yang ditawarkan melalui situs.</li>
                <li><strong>Pesanan</strong> &mdash; permintaan pembuatan produk yang telah dikonfirmasi oleh kedua belah pihak melalui kanal resmi kami.</li>
                <li><strong>Mockup / Proof Desain</strong> &mdash; pratinjau visual rancangan produk yang dikirimkan untuk ditinjau dan disetujui Pelanggan sebelum produksi.</li>
                <li><strong>Persetujuan Final (ACC)</strong> &mdash; konfirmasi tertulis dari Pelanggan bahwa desain, teks, dan spesifikasi sudah benar dan siap diproduksi.</li>
                <li><strong>Kanal Resmi</strong> &mdash; nomor WhatsApp, email, dan formulir yang tercantum pada halaman Kontak ' . $brand . '.</li>
            </ul>',
    ),
    array(
        'id'      => 'layanan',
        'title'   => 'Ruang Lingkup Layanan',
        'content' => '<p>' . $brand . ' menyediakan jasa perancangan dan produksi produk penghargaan custom sesuai kebutuhan Pelanggan, baik perorangan, instansi, perusahaan, sekolah, maupun komunitas.</p>
            <p>Informasi produk pada situs &mdash; termasuk foto, ukuran, warna, dan deskripsi material &mdash; disajikan seakurat mungkin sebagai referensi. Hasil akhir dapat memiliki sedikit perbedaan karena karakter alami material (misalnya serat kayu atau gelembung mikro pada resin), pengaturan layar perangkat, maupun proses pengerjaan tangan.</p>',
    ),
    array(
        'id'      => 'pemesanan',
        'title'   => 'Proses Pemesanan',
        'content' => '<p>Seluruh pemesanan dilakukan melalui kanal resmi kami, terutama WhatsApp. Alur pemesanan secara umum adalah sebagai berikut:</p>
            <ol>
                <li><strong>Konsultasi</strong> &mdash; Pelanggan menyampaikan jenis produk, jumlah, teks/grafir, logo, dan tanggal kebutuhan.</li>
                <li><strong>Penawaran</strong> &mdash; Kami memberikan rincian harga, estimasi waktu produksi, dan ongkos kirim.</li>
                <li><strong>Pembayaran Awal</strong> &mdash; Pesanan masuk antrean produksi setelah pembayaran uang muka (DP) atau pelunasan diterima sesuai kesepakatan.</li>
                <li><strong>Mockup &amp; Revisi</strong> &mdash; Kami mengirimkan proof desain untuk diperiksa dan direvisi.</li>
                <li><strong>Persetujuan Final</strong> &mdash; Produksi dimulai setelah Pelanggan memberikan ACC tertulis.</li>
                <li><strong>Produksi &amp; Pengiriman</strong> &mdash; Produk dikerjakan, diperiksa kualitasnya, lalu dikemas aman dan dikirim.</li>
            </ol>
            <p>Pelanggan bertanggung jawab memberikan data yang benar, lengkap, dan dapat dihubungi. Keterlambatan respons dari Pelanggan dapat memengaruhi jadwal penyelesaian pesanan.</p>',
    ),
    array(
        'id'      => 'harga-pembayaran',
        'title'   => 'Harga & Pembayaran',
        'content' => '<ul>
                <li>Harga yang tercantum di situs merupakan harga dasar dan dapat berubah sewaktu-waktu tanpa pemberitahuan sebelumnya. Harga yang mengikat adalah harga yang tertera pada penawaran/konfirmasi pesanan resmi.</li>
                <li>Harga belum termasuk ongkos kirim, asuransi pengiriman, maupun biaya tambahan atas permintaan khusus (misalnya material premium, ukuran non-standar, atau pengerjaan ekspres), kecuali dinyatakan lain.</li>
                <li>Pembayaran dilakukan melalui transfer ke rekening resmi atas nama usaha yang dikonfirmasi oleh admin kami. <strong>' . $brand . ' tidak pernah meminta pembayaran ke rekening pribadi pihak lain.</strong></li>
                <li>Mohon simpan dan kirimkan bukti transfer melalui kanal resmi agar pesanan dapat segera diverifikasi.</li>
                <li>Pelunasan wajib diselesaikan sebelum produk dikirim, kecuali terdapat kesepakatan tertulis lain (misalnya untuk instansi dengan mekanisme termin).</li>
            </ul>',
    ),
    array(
        'id'      => 'desain',
        'title'   => 'Desain, Mockup & Persetujuan Final',
        'content' => '<p>Kami menyediakan bantuan desain dan mockup untuk memastikan hasil akhir sesuai harapan Anda. Jumlah revisi wajar disediakan selama tidak mengubah konsep, ukuran, atau material secara mendasar.</p>
            <ul>
                <li>Pelanggan wajib memeriksa dengan teliti seluruh detail pada mockup, termasuk <strong>ejaan nama, gelar, jabatan, tanggal, angka, dan logo</strong>.</li>
                <li>Setelah Persetujuan Final diberikan, kesalahan yang terdapat pada desain yang telah disetujui menjadi tanggung jawab Pelanggan, dan perbaikan setelah produksi dapat dikenakan biaya produksi ulang.</li>
                <li>Untuk hasil cetak dan grafir terbaik, kami menyarankan logo dalam format vektor (AI, CDR, EPS, SVG, atau PDF). Kualitas hasil dari file beresolusi rendah dapat kurang optimal.</li>
                <li>Perbedaan tipis warna antara tampilan layar dan hasil produksi merupakan hal yang wajar dan tidak termasuk cacat produk.</li>
            </ul>',
    ),
    array(
        'id'      => 'produksi',
        'title'   => 'Waktu Produksi',
        'content' => '<p>Estimasi waktu produksi dihitung dalam hari kerja sejak <strong>Persetujuan Final dan pembayaran terverifikasi</strong>, bukan sejak pertama kali menghubungi kami. Durasi bergantung pada jenis produk, jumlah pesanan, kompleksitas desain, dan antrean produksi.</p>
            <p>Untuk kebutuhan dengan tenggat waktu tertentu (misalnya acara wisuda, pelantikan, atau turnamen), mohon informasikan sejak awal agar kami dapat memastikan ketersediaan jadwal. Layanan pengerjaan ekspres dapat tersedia dengan biaya tambahan sesuai kapasitas.</p>
            <p>Kami tidak bertanggung jawab atas keterlambatan yang disebabkan oleh keadaan kahar (force majeure), seperti bencana alam, gangguan listrik berskala luas, kebijakan pemerintah, atau kendala di luar kendali wajar kami.</p>',
    ),
    array(
        'id'      => 'pengiriman',
        'title'   => 'Pengemasan & Pengiriman',
        'content' => '<ul>
                <li>Setiap produk dikemas dengan pelindung yang memadai (bubble wrap, styrofoam, dan/atau kemasan kayu sesuai jenis produk) untuk meminimalkan risiko kerusakan.</li>
                <li>Pengiriman dilakukan melalui jasa ekspedisi pilihan Pelanggan atau rekomendasi kami. Nomor resi akan diinformasikan setelah paket diserahkan ke ekspedisi.</li>
                <li>Estimasi waktu pengiriman sepenuhnya mengikuti layanan ekspedisi. Keterlambatan atau kehilangan selama proses pengiriman menjadi tanggung jawab pihak ekspedisi sesuai ketentuan yang berlaku.</li>
                <li>Kami sangat menyarankan penggunaan <strong>asuransi pengiriman</strong> dan packing kayu untuk produk berbahan kaca, akrilik tebal, atau resin.</li>
                <li>Pengambilan langsung (pick-up) di lokasi workshop dapat dilakukan dengan perjanjian terlebih dahulu.</li>
            </ul>',
    ),
    array(
        'id'      => 'garansi',
        'title'   => 'Garansi, Komplain & Retur',
        'content' => '<p>Kepuasan Anda adalah prioritas kami. Apabila produk yang diterima tidak sesuai dengan desain yang telah disetujui atau mengalami kerusakan, kami akan membantu menyelesaikannya dengan ketentuan berikut:</p>
            <ul>
                <li>Komplain diajukan paling lambat <strong>2 &times; 24 jam</strong> sejak paket diterima, melalui kanal resmi kami.</li>
                <li>Sertakan <strong>video unboxing tanpa jeda</strong> sejak paket masih tersegel, foto produk, serta foto label pengiriman sebagai bukti pendukung.</li>
                <li>Setelah verifikasi, kami akan menawarkan solusi terbaik berupa perbaikan, produksi ulang sebagian/seluruhnya, atau kompensasi yang disepakati bersama.</li>
            </ul>
            <p>Komplain tidak dapat diproses untuk hal-hal berikut:</p>
            <ul>
                <li>Kesalahan yang berasal dari data atau desain yang telah disetujui Pelanggan.</li>
                <li>Perbedaan minor yang wajar akibat karakter material atau perbedaan tampilan layar.</li>
                <li>Kerusakan akibat kelalaian penggunaan, penyimpanan, atau pemindahan setelah produk diterima.</li>
                <li>Pengajuan yang melewati batas waktu atau tanpa bukti pendukung yang memadai.</li>
            </ul>
            <p>Mengingat seluruh produk dibuat secara custom dan personal, kami tidak menerima pengembalian produk atas dasar perubahan keinginan.</p>',
    ),
    array(
        'id'      => 'pembatalan',
        'title'   => 'Pembatalan Pesanan',
        'content' => '<ul>
                <li><strong>Sebelum produksi dimulai:</strong> pembatalan dapat diajukan, dan dana yang telah dibayarkan dapat dikembalikan setelah dikurangi biaya desain atau biaya administrasi yang telah timbul (jika ada).</li>
                <li><strong>Setelah Persetujuan Final / produksi berjalan:</strong> pesanan tidak dapat dibatalkan dan pembayaran tidak dapat dikembalikan, karena material telah dipotong dan dikerjakan khusus untuk Anda.</li>
                <li>Kami berhak membatalkan pesanan apabila Pelanggan tidak memberikan respons atau tidak menyelesaikan pembayaran dalam jangka waktu yang wajar setelah pengingat disampaikan.</li>
            </ul>',
    ),
    array(
        'id'      => 'hak-cipta',
        'title'   => 'Hak Kekayaan Intelektual',
        'content' => '<p>Dengan mengirimkan logo, merek, foto, atau materi lain untuk diproduksi, Pelanggan menyatakan dan menjamin bahwa ia memiliki hak atau izin yang sah untuk menggunakan materi tersebut. Pelanggan membebaskan ' . $brand . ' dari segala tuntutan pihak ketiga terkait pelanggaran hak kekayaan intelektual atas materi yang disediakan Pelanggan.</p>
            <p>Seluruh konten situs &mdash; termasuk teks, foto katalog, desain antarmuka, dan logo ' . $brand . ' &mdash; dilindungi oleh hukum. Penggunaan, penyalinan, atau distribusi tanpa izin tertulis dari kami tidak diperkenankan.</p>
            <p>Kami dapat menampilkan foto hasil produksi sebagai portofolio. Apabila Anda berkeberatan, cukup sampaikan kepada kami sebelum produk dikirim dan kami akan menghormati permintaan tersebut.</p>',
    ),
    array(
        'id'      => 'tanggung-jawab',
        'title'   => 'Batasan Tanggung Jawab',
        'content' => '<p>Sejauh diizinkan oleh hukum yang berlaku, tanggung jawab ' . $brand . ' atas setiap klaim yang berkaitan dengan pesanan terbatas pada nilai pesanan yang telah dibayarkan oleh Pelanggan. Kami tidak bertanggung jawab atas kerugian tidak langsung, termasuk namun tidak terbatas pada kehilangan keuntungan atau terganggunya acara, yang timbul dari penggunaan layanan kami.</p>',
    ),
    array(
        'id'      => 'perubahan',
        'title'   => 'Perubahan Ketentuan',
        'content' => '<p>Kami dapat memperbarui Syarat &amp; Ketentuan ini dari waktu ke waktu untuk menyesuaikan perkembangan layanan maupun peraturan perundang-undangan. Versi terbaru akan selalu tersedia di halaman ini beserta tanggal berlakunya. Pesanan yang telah dikonfirmasi sebelum perubahan tetap mengikuti ketentuan yang berlaku pada saat pesanan tersebut dibuat.</p>',
    ),
    array(
        'id'      => 'hukum',
        'title'   => 'Hukum yang Berlaku & Penyelesaian Sengketa',
        'content' => '<p>Syarat &amp; Ketentuan ini tunduk pada hukum Negara Republik Indonesia, termasuk Undang-Undang Nomor 8 Tahun 1999 tentang Perlindungan Konsumen dan Undang-Undang Informasi dan Transaksi Elektronik beserta perubahannya.</p>
            <p>Setiap perselisihan akan diselesaikan terlebih dahulu secara musyawarah untuk mufakat. Apabila tidak tercapai kesepakatan, para pihak dapat menempuh jalur penyelesaian sesuai ketentuan hukum yang berlaku di Indonesia.</p>',
    ),
    array(
        'id'      => 'kontak',
        'title'   => 'Hubungi Kami',
        'content' => '<p>Jika Anda memiliki pertanyaan mengenai Syarat &amp; Ketentuan ini, silakan hubungi kami melalui:</p>
            <ul>
                <li>WhatsApp: <a href="' . $wa_link . '" target="_blank" rel="noopener noreferrer">' . esc_html( $contact['wa_display'] ) . '</a></li>'
                . ( $email ? '<li>Email: ' . $email . '</li>' : '' )
                . ( $contact['address'] ? '<li>Alamat: ' . esc_html( $contact['address'] ) . '</li>' : '' ) .
            '</ul>',
    ),
);

while ( have_posts() ) :
    the_post();
    get_template_part( 'template-parts/legal-page', null, array(
        'badge'    => 'Dokumen Legal',
        'icon'     => 'file',
        'title'    => get_the_title() ? get_the_title() : 'Syarat & Ketentuan',
        'lead'     => 'Aturan main yang jelas dan adil agar setiap pesanan berjalan lancar, transparan, dan saling menguntungkan. Mohon luangkan waktu sejenak untuk membacanya sebelum melakukan pemesanan.',
        'updated'  => get_the_modified_date( 'j F Y' ),
        'summary'  => array(
            'Pemesanan resmi hanya melalui kanal WhatsApp, email, atau formulir di situs ' . tokoku_page_brand() . '.',
            'Produksi dimulai setelah pembayaran terverifikasi dan Anda memberikan persetujuan final (ACC) desain.',
            'Periksa ejaan nama, gelar, tanggal, dan logo dengan teliti — desain yang sudah di-ACC menjadi acuan produksi.',
            'Komplain diajukan maksimal 2×24 jam sejak paket diterima dengan video unboxing tanpa jeda.',
            'Produk custom tidak dapat dibatalkan setelah proses produksi berjalan.',
        ),
        'sections' => $sections,
        'related'  => array(
            array(
                'label' => 'Kebijakan Privasi',
                'url'   => tokoku_static_page_url( 'page-templates/template-privacy.php' ),
                'icon'  => 'shield',
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
