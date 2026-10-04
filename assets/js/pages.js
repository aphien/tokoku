/**
 * TokoKu — Static Page Templates Scripts (Tentang, Kontak, Terms, Privacy)
 *
 * Handles:
 * 1. Reading Progress Indicator (Legal Pages)
 * 2. Sticky TOC Scrollspy & Smooth Scrolling (Legal Pages)
 * 3. Interactive WhatsApp Message Builder Form (Contact Page)
 * 4. Copy-to-Clipboard functionality (Workshop Address & Info)
 * 5. Scroll Reveal Intersection Animations
 * 6. Smooth FAQ Accordion Interactions
 *
 * @package TokoKu
 */

(function () {
    'use strict';

    /**
     * DOM Ready Helper
     */
    function ready(fn) {
        if (document.readyState !== 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }

    ready(function () {

        /* ======================================================================
           1. READING PROGRESS BAR (Legal Pages)
           ====================================================================== */
        var progressBar = document.querySelector('.jp-progress__bar');
        if (progressBar) {
            var ticking = false;
            var updateProgress = function () {
                var docElem = document.documentElement;
                var scrollTop = window.scrollY || docElem.scrollTop || 0;
                var docHeight = docElem.scrollHeight - docElem.clientHeight;
                var progress = 0;

                if (docHeight > 0) {
                    progress = Math.min(100, Math.max(0, (scrollTop / docHeight) * 100));
                }

                progressBar.style.width = progress.toFixed(1) + '%';
                ticking = false;
            };

            window.addEventListener('scroll', function () {
                if (!ticking) {
                    window.requestAnimationFrame(updateProgress);
                    ticking = true;
                }
            }, { passive: true });

            window.addEventListener('resize', updateProgress, { passive: true });
            updateProgress();
        }

        /* ======================================================================
           2. TOC SCROLLSPY & SMOOTH SCROLL (Legal Pages)
           ====================================================================== */
        var tocLinks = document.querySelectorAll('.jp-toc__link');
        var legalSections = document.querySelectorAll('.jp-legal__section');

        if (tocLinks.length > 0 && legalSections.length > 0) {
            // Smooth click handling with sticky header offset
            tocLinks.forEach(function (link) {
                link.addEventListener('click', function (e) {
                    var targetId = link.getAttribute('data-target') || (link.getAttribute('href') || '').replace('#', '');
                    var targetElem = document.getElementById(targetId);

                    if (targetElem) {
                        e.preventDefault();
                        var headerOffset = 90; // offset for sticky header & progress bar
                        var elementPosition = targetElem.getBoundingClientRect().top;
                        var offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                        window.scrollTo({
                            top: Math.max(0, offsetPosition),
                            behavior: 'smooth'
                        });

                        if (window.history && window.history.pushState) {
                            window.history.pushState(null, '', '#' + targetId);
                        }

                        // Close details on mobile after selection if desired
                        var detailsParent = link.closest('details.jp-toc__box');
                        if (detailsParent && window.innerWidth < 768) {
                            detailsParent.removeAttribute('open');
                        }
                    }
                });
            });

            // Scrollspy via IntersectionObserver or fallback
            if ('IntersectionObserver' in window) {
                var currentActiveId = null;

                var observerCallback = function (entries) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            currentActiveId = entry.target.id;
                            updateActiveToc(currentActiveId);
                        }
                    });
                };

                var tocObserver = new IntersectionObserver(observerCallback, {
                    root: null,
                    rootMargin: '-15% 0px -70% 0px',
                    threshold: 0
                });

                legalSections.forEach(function (section) {
                    tocObserver.observe(section);
                });
            } else {
                // Fallback scroll listener
                window.addEventListener('scroll', function () {
                    var scrollPos = window.scrollY + 120;
                    legalSections.forEach(function (section) {
                        if (section.offsetTop <= scrollPos && (section.offsetTop + section.offsetHeight) > scrollPos) {
                            updateActiveToc(section.id);
                        }
                    });
                }, { passive: true });
            }

            function updateActiveToc(activeId) {
                if (!activeId) return;
                tocLinks.forEach(function (link) {
                    var targetId = link.getAttribute('data-target') || (link.getAttribute('href') || '').replace('#', '');
                    if (targetId === activeId) {
                        link.classList.add('is-active');
                        link.setAttribute('aria-current', 'true');
                    } else {
                        link.classList.remove('is-active');
                        link.removeAttribute('aria-current');
                    }
                });
            }
        }

        /* ======================================================================
           3. INTERACTIVE WHATSAPP MESSAGE BUILDER (Contact Page)
           ====================================================================== */
        var waForm = document.getElementById('jp-wa-builder-form');
        if (waForm) {
            waForm.addEventListener('submit', function (e) {
                e.preventDefault();

                var nameInput = document.getElementById('jp_client_name');
                var instansiInput = document.getElementById('jp_client_instansi');
                var productInput = document.getElementById('jp_product_type');
                var qtyInput = document.getElementById('jp_quantity');
                var deadlineInput = document.getElementById('jp_deadline');
                var notesInput = document.getElementById('jp_notes');

                var name = nameInput ? nameInput.value.trim() : '';
                var instansi = instansiInput ? instansiInput.value.trim() : '';
                var product = productInput ? productInput.value : '';
                var qty = qtyInput ? qtyInput.value.trim() : '';
                var deadline = deadlineInput ? deadlineInput.value.trim() : '';
                var notes = notesInput ? notesInput.value.trim() : '';

                // Simple validation
                var isValid = true;
                [nameInput, productInput, qtyInput].forEach(function (field) {
                    if (field) {
                        if (!field.value || (field.id === 'jp_quantity' && parseInt(field.value, 10) < 1)) {
                            field.style.borderColor = '#ef4444';
                            field.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.15)';
                            isValid = false;
                        } else {
                            field.style.borderColor = '';
                            field.style.boxShadow = '';
                        }
                    }
                });

                if (!isValid) {
                    if (nameInput && !nameInput.value.trim()) {
                        nameInput.focus();
                    } else if (productInput && !productInput.value) {
                        productInput.focus();
                    } else if (qtyInput) {
                        qtyInput.focus();
                    }
                    return;
                }

                var waRaw = waForm.getAttribute('data-wa') || '6281234567890';
                var brand = waForm.getAttribute('data-brand') || 'JualPlakat.com';
                var cleanWa = waRaw.replace(/\D+/g, '');

                // Format clean, professional message
                var lines = [
                    'Halo ' + brand + ', saya ingin berkonsultasi mengenai pemesanan plakat:',
                    '',
                    '👤 Nama: ' + name,
                    '🏢 Instansi/Perusahaan: ' + (instansi || '-'),
                    '📦 Jenis Produk: ' + product,
                    '🔢 Estimasi Jumlah: ' + qty + ' pcs',
                    '📅 Target Tanggal: ' + (deadline || '-'),
                    '📝 Catatan / Tulisan: ' + (notes || '-')
                ];

                lines.push('');
                lines.push('Mohon info rekomendasi desain dan penawaran harganya. Terima kasih!');

                var messageText = lines.join('\n');
                var waUrl = 'https://wa.me/' + cleanWa + '?text=' + encodeURIComponent(messageText);

                // Open WhatsApp in new tab
                window.open(waUrl, '_blank', 'noopener,noreferrer');
            });

            // Reset field error on input
            waForm.querySelectorAll('input, select, textarea').forEach(function (elem) {
                elem.addEventListener('input', function () {
                    this.style.borderColor = '';
                    this.style.boxShadow = '';
                });
            });
        }

        /* ======================================================================
           4. COPY TO CLIPBOARD BUTTONS
           ====================================================================== */
        var copyButtons = document.querySelectorAll('.jp-copy-btn');
        copyButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var textToCopy = button.getAttribute('data-copy') || '';
                if (!textToCopy) return;

                var originalHtml = button.innerHTML;

                var onSuccess = function () {
                    button.classList.add('is-copied');
                    button.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right:4px;"><polyline points="20 6 9 17 4 12"></polyline></svg> <span>Tersalin!</span>';
                    setTimeout(function () {
                        button.innerHTML = originalHtml;
                        button.classList.remove('is-copied');
                    }, 2200);
                };

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(textToCopy).then(onSuccess).catch(function () {
                        fallbackCopy(textToCopy, onSuccess);
                    });
                } else {
                    fallbackCopy(textToCopy, onSuccess);
                }
            });
        });

        function fallbackCopy(text, cb) {
            var textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.top = '-9999px';
            textArea.style.left = '-9999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                if (cb) cb();
            } catch (err) {
                console.error('Gagal menyalin teks:', err);
            }
            document.body.removeChild(textArea);
        }

        /* ======================================================================
           5. SCROLL REVEAL ANIMATIONS (.jp-reveal)
           ====================================================================== */
        var revealElements = document.querySelectorAll('.jp-reveal');
        if (revealElements.length > 0) {
            if ('IntersectionObserver' in window) {
                var revealObserver = new IntersectionObserver(function (entries, observer) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-revealed');
                            observer.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.08,
                    rootMargin: '0px 0px -40px 0px'
                });

                revealElements.forEach(function (elem) {
                    revealObserver.observe(elem);
                });
            } else {
                revealElements.forEach(function (elem) {
                    elem.classList.add('is-revealed');
                });
            }
        }

        /* ======================================================================
           6. FAQ ACCORDION ENHANCEMENTS
           ====================================================================== */
        var faqItems = document.querySelectorAll('.jp-faq-item');
        if (faqItems.length > 0) {
            faqItems.forEach(function (faq) {
                faq.addEventListener('toggle', function () {
                    if (faq.open) {
                        // Close other open FAQ items for a neat accordion effect
                        faqItems.forEach(function (other) {
                            if (other !== faq && other.open) {
                                other.removeAttribute('open');
                            }
                        });
                    }
                });
            });
        }

    });
})();
