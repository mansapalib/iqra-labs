<?php
/**
 * =========================================================================
 * MANSAPA Iqra' Labs — _modal_advanced.php [Advanced Search Modal]
 * Place: template/iqra-labs/parts/_modal_advanced.php
 *
 * Modern Institutional Navy (#0D1B3D) & Gold (#D4AF37) Theme.
 * Fully responsive across mobile, tablet, and desktop devices.
 * Uses pure inline SVGs (100% immune to strict CSP headers).
 * Shared single source of truth for OPAC and Main Homepage/Search.
 * =========================================================================
 */
?>

<style>
/* ── MODAL ADVANCED SEARCH ──── */
#adv-modal {
    z-index: 1060;
}
#adv-modal .modal-dialog {
    max-width: 600px;
    margin: 2rem auto;
}
@media (max-width: 640px) {
    #adv-modal .modal-dialog {
        margin: 0.75rem;
        max-width: calc(100% - 1.5rem);
    }
}
.adv-modal-content {
    border: none;
    border-radius: 16px;
    background: #FFFFFF;
    box-shadow: 0 20px 45px -10px rgba(13, 27, 61, 0.3), 0 0 0 1px rgba(13, 27, 61, 0.06);
    overflow: hidden;
}
.adv-modal-header {
    background: linear-gradient(135deg, #0D1B3D 0%, #162B5E 100%);
    border-bottom: 2px solid #D4AF37;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.adv-header-left {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.adv-icon-badge {
    width: 36px;
    height: 36px;
    border-radius: 9px;
    background: rgba(212, 175, 55, 0.15);
    border: 1px solid rgba(212, 175, 55, 0.35);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #D4AF37;
    flex-shrink: 0;
}
.adv-title-wrap {
    display: flex;
    flex-direction: column;
}
.adv-modal-title {
    font-family: var(--font-h, 'Poppins', sans-serif);
    font-weight: 700;
    font-size: 1.02rem;
    color: #FFFFFF;
    margin: 0;
    line-height: 1.2;
}
.adv-modal-subtitle {
    font-family: var(--font-body, 'Inter', sans-serif);
    font-size: 0.76rem;
    color: rgba(255, 255, 255, 0.78);
    margin: 2px 0 0;
    line-height: 1.25;
}
.adv-close-btn {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    color: rgba(255, 255, 255, 0.85);
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    padding: 0;
    flex-shrink: 0;
    outline: none;
}
.adv-close-btn:hover {
    background: rgba(212, 175, 55, 0.25);
    border-color: #D4AF37;
    color: #FFFFFF;
    transform: rotate(90deg);
}
.adv-modal-body {
    padding: 1.15rem 1.25rem 0.5rem;
    background: #FFFFFF;
}
.adv-sec-divider {
    font-family: var(--font-h, 'Poppins', sans-serif);
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #0D1B3D;
    margin-bottom: 0.75rem;
    padding-bottom: 0.35rem;
    border-bottom: 1px dashed #E2E8F0;
}
.adv-form-group {
    margin-bottom: 0.85rem;
}
.adv-label {
    display: block;
    font-family: var(--font-body, 'Inter', sans-serif);
    font-size: 0.78rem;
    font-weight: 600;
    color: #334155;
    margin-bottom: 0.32rem;
}
.adv-control {
    width: 100%;
    height: 38px;
    padding: 0.45rem 0.75rem;
    font-size: 0.83rem;
    font-family: var(--font-body, 'Inter', sans-serif);
    color: #0F172A;
    background-color: #F8FAFC;
    border: 1px solid #CBD5E1;
    border-radius: 8px;
    transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
    outline: none;
    box-shadow: none;
}
.adv-control::placeholder {
    color: #94A3B8;
    font-size: 0.82rem;
}
.adv-control:focus {
    background-color: #FFFFFF;
    border-color: #0D1B3D;
    box-shadow: 0 0 0 3px rgba(13, 27, 61, 0.12);
    color: #0F172A;
}
select.adv-control {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748B' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    padding-right: 2rem;
    cursor: pointer;
}
select.adv-control::-ms-expand {
    display: none;
}
.adv-modal-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0.6rem;
    padding: 0.85rem 1.25rem;
    background: #F8FAFC;
    border-top: 1px solid #E2E8F0;
}
.adv-btn-reset {
    background: transparent;
    border: none;
    font-family: var(--font-body, 'Inter', sans-serif);
    font-size: 0.8rem;
    font-weight: 500;
    color: #64748B;
    cursor: pointer;
    padding: 0.45rem 0.65rem;
    margin-right: auto;
    transition: color 0.15s ease;
}
.adv-btn-reset:hover {
    color: #0D1B3D;
    text-decoration: underline;
}
.adv-btn-cancel {
    padding: 0.48rem 1rem;
    font-family: var(--font-body, 'Inter', sans-serif);
    font-size: 0.82rem;
    font-weight: 600;
    color: #475569;
    background: #FFFFFF;
    border: 1px solid #CBD5E1;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.15s ease;
}
.adv-btn-cancel:hover {
    background: #F1F5F9;
    color: #1E293B;
    border-color: #94A3B8;
}
.adv-btn-submit {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.48rem 1.25rem;
    font-family: var(--font-body, 'Inter', sans-serif);
    font-size: 0.84rem;
    font-weight: 700;
    color: #FFFFFF;
    background: #0D1B3D;
    border: 1px solid #0D1B3D;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(13, 27, 61, 0.2);
    cursor: pointer;
    transition: all 0.2s ease;
}
.adv-btn-submit:hover {
    background: #162B5E;
    border-color: #D4AF37;
    color: #D4AF37;
    box-shadow: 0 6px 14px rgba(13, 27, 61, 0.28);
    transform: translateY(-1px);
}
.adv-btn-submit:active {
    transform: translateY(0);
}
</style>

<div class="modal fade" id="adv-modal" tabindex="-1" role="dialog" aria-labelledby="advModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form class="modal-content adv-modal-content" action="index.php" method="get">
            <div class="adv-modal-header">
                <div class="adv-header-left">
                    <div class="adv-icon-badge" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="4" y1="21" x2="4" y2="14"/>
                            <line x1="4" y1="10" x2="4" y2="3"/>
                            <line x1="12" y1="21" x2="12" y2="12"/>
                            <line x1="12" y1="8" x2="12" y2="3"/>
                            <line x1="20" y1="21" x2="20" y2="16"/>
                            <line x1="20" y1="12" x2="20" y2="3"/>
                            <line x1="1" y1="14" x2="7" y2="14"/>
                            <line x1="9" y1="8" x2="15" y2="8"/>
                            <line x1="17" y1="16" x2="23" y2="16"/>
                        </svg>
                    </div>
                    <div class="adv-title-wrap">
                        <h5 class="adv-modal-title" id="advModalLabel"><?= __('Pencarian Lanjutan'); ?></h5>
                        <p class="adv-modal-subtitle"><?= __('Saring katalog buku dengan kriteria spesifik'); ?></p>
                    </div>
                </div>
                <button type="button" class="adv-close-btn" data-dismiss="modal" aria-label="<?= __('Tutup'); ?>">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            <div class="adv-modal-body">
                <!-- Section 1: Bibliografi -->
                <div class="adv-sec-divider"><?= __('Kriteria Bibliografi'); ?></div>
                <div class="row">
                    <div class="col-md-6 adv-form-group">
                        <label for="adv-titles" class="adv-label"><?= __('Judul Buku'); ?></label>
                        <input type="text" name="title" class="adv-control" id="adv-titles"
                               placeholder="<?= __('Ketik judul buku...'); ?>">
                    </div>
                    <div class="col-md-6 adv-form-group">
                        <label for="adv-author" class="adv-label"><?= __('Penulis / Pengarang'); ?></label>
                        <input type="text" name="author" class="adv-control" id="adv-author"
                               placeholder="<?= __('Ketik nama penulis...'); ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 adv-form-group">
                        <label for="adv-subject" class="adv-label"><?= __('Topik / Subjek'); ?></label>
                        <input type="text" name="subject" class="adv-control" id="adv-subject"
                               placeholder="<?= __('Ketik subjek atau topik...'); ?>">
                    </div>
                    <div class="col-md-6 adv-form-group">
                        <label for="adv-isbn" class="adv-label"><?= __('ISBN / ISSN'); ?></label>
                        <input type="text" name="isbn" class="adv-control" id="adv-isbn"
                               placeholder="<?= __('Ketik nomor ISBN / ISSN...'); ?>">
                    </div>
                </div>

                <!-- Section 2: Koleksi & Lokasi -->
                <div class="adv-sec-divider mt-2"><?= __('Filter Koleksi & Lokasi'); ?></div>
                <div class="row">
                    <div class="col-md-6 adv-form-group">
                        <label for="adv-publishyear" class="adv-label"><?= __('Tahun Terbit'); ?></label>
                        <input type="text" name="publishyear" class="adv-control" id="adv-publishyear"
                               placeholder="<?= __('Contoh: 2024'); ?>">
                    </div>
                    <div class="col-md-6 adv-form-group">
                        <label for="adv-location" class="adv-label"><?= __('Lokasi Rak'); ?></label>
                        <select id="adv-location" name="location" class="adv-control">
                            <?= commonList('location'); ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 adv-form-group">
                        <label for="adv-gmd" class="adv-label"><?= __('Bentuk Karya (GMD)'); ?></label>
                        <select id="adv-gmd" name="gmd" class="adv-control">
                            <?= commonList('gmd'); ?>
                        </select>
                    </div>
                    <div class="col-md-6 adv-form-group">
                        <label for="adv-coll-type" class="adv-label"><?= __('Tipe Koleksi'); ?></label>
                        <select id="adv-coll-type" name="colltype" class="adv-control">
                            <?= commonList('collection'); ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="adv-modal-footer">
                <button type="reset" class="adv-btn-reset"><?= __('Reset Filter'); ?></button>
                <button type="button" class="adv-btn-cancel" data-dismiss="modal"><?= __('Batal'); ?></button>
                <button type="submit" name="search" value="search" class="adv-btn-submit">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <span><?= __('Cari Koleksi'); ?></span>
                </button>
            </div>
        </form>
    </div>
</div>
