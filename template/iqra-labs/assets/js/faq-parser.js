/**
 * ============================================================================
 * MANSAPA Iqra' Labs — FAQ Dynamic 2-Column Minimalist Parser & Filter
 * Path: template/iqra-labs/assets/js/faq-parser.js
 * 
 * SPECIFICATIONS:
 * 1. Minimalist 2-Column Accordion Layout (Emulates image_9.png).
 * 2. Pure Vanilla ES6+, Zero jQuery dependency.
 * 3. Highly rounded accordion cards (24px) with interactive dark toggle (+/-).
 * 4. Case-insensitive live search with auto-expand and category hiding.
 * 5. Robust parsing against CMS formatting variances (H2/H3 or H3/H4).
 * ============================================================================
 */
(function() {
    'use strict';

    function initFaq() {
        const rawEl = document.getElementById('raw-faq-content');
        const renderedContainer = document.getElementById('faq-rendered-container');
        const searchInput = document.getElementById('faq-search');
        const searchClearBtn = document.getElementById('faq-search-clear');
        const searchStatsEl = document.getElementById('faq-search-stats');
        const emptyStateEl = document.getElementById('faq-empty-state');
        const emptyMsgEl = document.getElementById('faq-empty-message');
        const resetFilterBtn = document.getElementById('faq-reset-filter-btn');

        if (!rawEl || !renderedContainer) return;

        // 1. Locate the content container inside rawEl
        // CKEditor in SLiMS wraps content in .ck-content
        const contentContainer = rawEl.querySelector('.ck-content') || rawEl;

        // 2. Identify heading levels dynamically
        const hasH2 = !!contentContainer.querySelector('h2');
        const hasH3 = !!contentContainer.querySelector('h3');
        const hasH4 = !!contentContainer.querySelector('h4');

        let catTag = 'H2';
        let qTag = 'H3';

        if (hasH2 && hasH3) {
            catTag = 'H2';
            qTag = 'H3';
        } else if (!hasH2 && hasH3 && hasH4) {
            catTag = 'H3';
            qTag = 'H4';
        } else if (hasH2 && !hasH3) {
            catTag = null;
            qTag = 'H2';
        } else if (!hasH2 && hasH3 && !hasH4) {
            catTag = null;
            qTag = 'H3';
        }

        // 3. Iteratively parse children into Category groups and Questions
        const categories = [];
        let currentCategory = {
            title: 'Umum & Layanan',
            items: []
        };
        let currentQuestion = null;

        function getHeaderTag(el) {
            if (!el || el.nodeType !== Node.ELEMENT_NODE) return null;
            const tag = el.tagName.toUpperCase();
            if (tag === catTag || tag === qTag) return tag;
            if (el.children && el.children.length === 1) {
                const childTag = el.children[0].tagName.toUpperCase();
                if (childTag === catTag || childTag === qTag) return childTag;
            }
            return null;
        }

        function getHeaderText(el) {
            return (el.textContent || '').trim();
        }

        const childNodes = Array.from(contentContainer.children);

        for (let i = 0; i < childNodes.length; i++) {
            const child = childNodes[i];
            const headerTag = getHeaderTag(child);

            if (catTag && headerTag === catTag) {
                // Save previous question
                if (currentQuestion && currentQuestion.question) {
                    currentCategory.items.push(currentQuestion);
                    currentQuestion = null;
                }
                // Save previous category if it has items
                if (currentCategory.items.length > 0) {
                    categories.push(currentCategory);
                }
                currentCategory = {
                    title: getHeaderText(child),
                    items: []
                };
            } else if (headerTag === qTag) {
                // Save previous question
                if (currentQuestion && currentQuestion.question) {
                    currentCategory.items.push(currentQuestion);
                }
                currentQuestion = {
                    question: getHeaderText(child),
                    answerHtml: ''
                };
            } else {
                // Sibling element belonging to current question answer
                if (currentQuestion) {
                    const txt = (child.textContent || '').replace(/\u00a0/g, ' ').trim();
                    if (txt !== '' || child.querySelector('img, table, iframe')) {
                        currentQuestion.answerHtml += child.outerHTML;
                    }
                }
            }
        }

        // Push remaining question and category
        if (currentQuestion && currentQuestion.question) {
            currentCategory.items.push(currentQuestion);
        }
        if (currentCategory.items.length > 0) {
            categories.push(currentCategory);
        }

        // Fallback: If no categories were parsed, output raw sanitized HTML cleanly
        if (categories.length === 0) {
            const rawHtml = contentContainer.innerHTML.trim();
            if (rawHtml) {
                renderedContainer.innerHTML = `
                    <div class="faq-fallback-card bg-white p-6 md:p-8 rounded-[24px] border border-slate-200 shadow-sm text-slate-800 leading-relaxed">
                        ${rawHtml}
                    </div>
                `;
            }
            return;
        }

        function escapeHtml(str) {
            return String(str || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function stripHtml(html) {
            const tmp = document.createElement('div');
            tmp.innerHTML = html;
            return (tmp.textContent || tmp.innerText || '').trim();
        }

        // 4. Render 2-Column Minimalist Layout (Matching image_9.png)
        let renderedHtml = '';
        categories.forEach((cat, catIdx) => {
            let itemsHtml = '';

            cat.items.forEach((item, itemIdx) => {
                const qClean = escapeHtml(item.question);
                const aCleanText = escapeHtml(stripHtml(item.answerHtml));
                const itemId = `faq-card-${catIdx}-${itemIdx}`;

                itemsHtml += `
                    <details class="faq-card group bg-white rounded-2xl md:rounded-[24px] p-5 sm:p-6 shadow-sm border border-slate-100/90 transition-all duration-300 hover:shadow-md cursor-pointer" id="${itemId}" data-question-text="${qClean}" data-answer-text="${aCleanText}">
                        <summary class="faq-summary flex items-center justify-between gap-4 cursor-pointer list-none select-none">
                            <span class="faq-question-text font-semibold text-[15px] md:text-base text-slate-900 leading-snug flex-1 pr-2">
                                ${qClean}
                            </span>
                            <span class="faq-toggle-btn w-8 h-8 rounded-full flex items-center justify-center bg-slate-900 group-hover:bg-slate-800 text-white flex-shrink-0 transition-transform duration-300 shadow-xs" aria-hidden="true">
                                <svg class="faq-icon-plus w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                <svg class="faq-icon-minus w-3.5 h-3.5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            </span>
                        </summary>
                        <div class="faq-answer-content text-slate-500 text-sm md:text-[14.5px] leading-relaxed mt-4 pt-4 border-t border-slate-100">
                            ${item.answerHtml}
                        </div>
                    </details>
                `;
            });

            renderedHtml += `
                <section class="faq-category mb-12" data-category-title="${escapeHtml(cat.title)}">
                    <!-- Category Header with Gold Accent Underline -->
                    <div class="faq-category-header mb-6">
                        <h2 class="faq-category-title text-xl md:text-2xl font-bold text-[#0F172A] tracking-tight m-0">
                            ${escapeHtml(cat.title)}
                        </h2>
                        <div class="faq-category-stroke w-10 h-1 bg-[#D4AF37] rounded-full mt-2"></div>
                    </div>

                    <!-- 2-Column Responsive Grid -->
                    <div class="faq-accordion-grid grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5 items-start">
                        ${itemsHtml}
                    </div>
                </section>
            `;
        });

        renderedContainer.innerHTML = renderedHtml;

        // 5. Live Search & Filtering Logic
        if (searchInput) {
            const categorySections = renderedContainer.querySelectorAll('.faq-category');

            function performFilter() {
                const term = searchInput.value.trim().toLowerCase();
                let totalMatches = 0;

                if (term === '') {
                    // Reset to full view
                    if (searchClearBtn) searchClearBtn.classList.add('hidden');
                    if (searchStatsEl) searchStatsEl.classList.add('hidden');
                    if (emptyStateEl) emptyStateEl.classList.add('hidden');

                    categorySections.forEach(catEl => {
                        catEl.style.display = '';
                        const items = catEl.querySelectorAll('.faq-card');
                        items.forEach(item => {
                            item.style.display = '';
                            item.open = false; // collapse
                        });
                    });
                    return;
                }

                // Show clear button
                if (searchClearBtn) searchClearBtn.classList.remove('hidden');

                // Filter each category
                categorySections.forEach(catEl => {
                    const items = catEl.querySelectorAll('.faq-card');
                    let catMatches = 0;

                    items.forEach(item => {
                        const q = (item.dataset.questionText || '').toLowerCase();
                        const a = (item.dataset.answerText || '').toLowerCase();
                        const isMatch = q.includes(term) || a.includes(term);

                        if (isMatch) {
                            item.style.display = '';
                            item.open = true; // Auto expand matching items
                            catMatches++;
                            totalMatches++;
                        } else {
                            item.style.display = 'none';
                            item.open = false;
                        }
                    });

                    if (catMatches > 0) {
                        catEl.style.display = '';
                    } else {
                        catEl.style.display = 'none';
                    }
                });

                // Overall Stats & Empty State
                if (totalMatches === 0) {
                    if (emptyStateEl) {
                        emptyStateEl.classList.remove('hidden');
                        if (emptyMsgEl) {
                            emptyMsgEl.innerHTML = `Tidak ada pertanyaan atau jawaban yang memuat kata kunci "<strong>${escapeHtml(searchInput.value.trim())}</strong>". Silakan coba kata kunci lain.`;
                        }
                    }
                    if (searchStatsEl) searchStatsEl.classList.add('hidden');
                } else {
                    if (emptyStateEl) emptyStateEl.classList.add('hidden');
                    if (searchStatsEl) {
                        searchStatsEl.classList.remove('hidden');
                        searchStatsEl.innerHTML = `<span class="inline-flex items-center gap-1.5 text-slate-600 bg-white border border-slate-200/80 px-3 py-1 rounded-full shadow-2xs"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Ditemukan <strong>${totalMatches}</strong> pertanyaan yang sesuai</span>`;
                    }
                }
            }

            searchInput.addEventListener('input', performFilter);

            if (searchClearBtn) {
                searchClearBtn.addEventListener('click', function() {
                    searchInput.value = '';
                    performFilter();
                    searchInput.focus();
                });
            }

            if (resetFilterBtn) {
                resetFilterBtn.addEventListener('click', function() {
                    searchInput.value = '';
                    performFilter();
                    searchInput.focus();
                });
            }
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFaq);
    } else {
        initFaq();
    }
})();
