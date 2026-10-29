<?php
# @Author: Waris Agung Widodo <user>
# @Date:   2018-01-25T10:31:54+07:00
# @Email:  ido.alit@gmail.com
# @Filename: _search-form.php
# @Last modified by:   user
# @Last modified time: 2018-01-26T16:53:56+07:00

if (isset($opac) && $opac->invalid_token) {
    //die($opac->error('invalid CSRF token'));
}
?>

<div class="search" id="search-wraper" xmlns:v-bind="http://www.w3.org/1999/xhtml">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card border-0 shadow-sm search-input-card">
                    <div class="card-body p-2 px-3">
                        <form class="d-flex align-items-center w-100 m-0" action="index.php" method="get" @submit.prevent="searchSubmit">
                            <input type="hidden" name="search" value="search">
                            <div class="search-input-field-wrap d-flex align-items-center flex-grow-1">
                                <i class="fas fa-search text-muted mr-2 search-bar-icon" aria-hidden="true"></i>
                                <input ref="keywords" value="<?= htmlentities(getQuery('keywords') ?? '') ?>" v-model.trim="keywords"
                                       @focus="searchOnFocus" @blur="searchOnBlur" type="text" id="search-input"
                                       name="keywords" class="input-transparent w-100" autocomplete="off"
                                       placeholder="<?= __('Enter keyword to search collection...');?>"/>
                            </div>
                            <button type="submit" class="btn btn-search-go ml-2 flex-shrink-0" title="<?= __('Cari') ?>">
                                <span class="d-none d-sm-inline mr-1"><?= __('Cari') ?></span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <transition name="slide-fade">
                    <div v-if="show" class="advanced-wraper shadow mt-3" id="advanced-wraper"
                         v-click-outside="hideSearch">
                        <p class="label search-section-label mb-2">
                            <span><?= __('Search by :');?></span>
                            <i @click="hideSearch"
                               class="far fa-times-circle text-danger cursor-pointer"
                               title="<?= __('Tutup') ?>"></i>
                        </p>
                        <div class="d-flex flex-wrap align-items-center">
                            <a v-bind:class="{'btn-primary text-white': searchBy === 'keywords', 'btn-outline-secondary': searchBy !== 'keywords' }"
                               @click="searchOnClick('keywords')" class="btn search-scope-btn"><?= __('ALL')?></a>
                            <a v-bind:class="{'btn-primary text-white': searchBy === 'author', 'btn-outline-secondary': searchBy !== 'author' }"
                               @click="searchOnClick('author')" class="btn search-scope-btn"><?= __('Author');?></a>
                            <a v-bind:class="{'btn-primary text-white': searchBy === 'subject', 'btn-outline-secondary': searchBy !== 'subject' }"
                               @click="searchOnClick('subject')" class="btn search-scope-btn"><?= __('Subject');?></a>
                            <a v-bind:class="{'btn-primary text-white': searchBy === 'isbn', 'btn-outline-secondary': searchBy !== 'isbn' }"
                               @click="searchOnClick('isbn')" class="btn search-scope-btn"><?= __('ISBN/ISSN');?></a>
                            <button class="btn btn-light search-ortry-btn" disabled><?= __('OR TRY'); ?></button>
                            <a class="btn btn-outline-primary search-adv-btn" data-toggle="modal" data-target="#adv-modal"><?= __('Advanced Search');?></a>
                        </div>
                        <p v-if="lastKeywords.length > 0" class="label search-history-heading mt-3"><?= __('Last search:');?></p>
                        <a :href="`index.php?${tmpObj[k].searchBy}=${tmpObj[k].text}&search=search`"
                           class="search-history-link flex items-center justify-between py-1 text-decoration-none text-grey-darkest hover:text-blue"
                           v-for="k in lastKeywords" :key="k">
                            <span class="d-inline-flex align-items-center">
                                <i class="far fa-clock search-history-icon text-grey-dark mr-2"></i>
                                <span class="italic text-sm search-history-text">{{tmpObj[k].text}}</span>
                            </span>
                            <i class="fas fa-angle-right search-history-arrow text-grey-dark"></i>
                        </a>
                    </div>
                </transition>
            </div>
        </div>
    </div>
</div>
