/**
 * @Author: Waris Agung Widodo <user>
 * @Date:   2018-01-21T12:15:49+07:00
 * @Email:  ido.alit@gmail.com
 * @Filename: app.js
 * @Last modified by:   user
 * @Last modified time: 2018-01-23T18:22:24+07:00
 */

'use strict';

Vue.directive('click-outside', {
    priority: 700,
    bind: function (el, binding, vnode) {
        window.event = function (event) {
            if (!(el === event.target || el.contains(event.target))) {
                vnode.context[binding.expression](event);
            }
        };
        document.body.addEventListener('click', window.event)
    },
    unbind: function (el) {
        document.body.removeEventListener('click', window.event)
    },
});

const show_advanced = new Vue({
    el: '#search-wraper',
    data: function () {
        return {
            show: false,
            isFocus: false,
            searchBy: 'keywords',
            keywords: '',
            tmpObj: {}
        }
    },
    computed: {
        lastKeywords: function () {
            let raw = localStorage.getItem('keywords')
            if (raw) {
                try {
                    let keywords = JSON.parse(raw), arr = []
                    for (let key in keywords) {
                        if (keywords.hasOwnProperty(key)) {
                            arr.push(keywords[key].time)
                            keywords[key].text = key
                            this.tmpObj[keywords[key].time] = keywords[key]
                        }
                    }
                    arr.sort()
                    arr.reverse()
                    return arr.slice(0, 5)
                } catch (e) {
                    console.error(e.message)
                    return []
                }
            }
            return []
        }
    },
    methods: {
        searchOnFocus: function (e) {
            this.show = true;
            this.isFocus = true;
            const urlParams = new URLSearchParams(window.location.search);
            const search = urlParams.get('search');
            const page = urlParams.get('p');
            if (!search && !page) window.scrollTo(0, 250)
        },
        searchOnBlur: function (e) {
            this.isFocus = false
        },
        hideSearch: function () {
            if (!this.isFocus) {
                this.show = false;
                this.searchBy = 'keywords'
            }
        },
        searchOnClick: function (searchBy) {
            this.searchBy = searchBy
            this.searchSubmit()
        },
        searchSubmit: function () {
            if (this.keywords !== '') this.saveKeyword()
            window.location.href = `index.php?${this.searchBy}=${this.keywords}&search=search`;
        },
        saveKeyword: function () {
            let rawKeywords = localStorage.getItem('keywords')
            let keywords = {};
            if (rawKeywords) {
                try {
                    keywords = JSON.parse(rawKeywords)
                } catch (e) {
                    console.error(e.message)
                }
            }
            if (keywords.hasOwnProperty(this.keywords)) {
                keywords[this.keywords] = {
                    count: keywords[this.keywords].count + 1,
                    searchBy: this.searchBy,
                    time: Date.now()
                }
            } else {
                keywords[this.keywords] = {
                    count: 1,
                    searchBy: this.searchBy,
                    time: Date.now()
                }
            }
            let strKeyword = JSON.stringify(keywords)
            localStorage.setItem('keywords', strKeyword)
        }
    }
});

Vue.component('slims-subject', {
    props: {
        topic: {
            type: String,
            default: ''
        }
    },
    render: function(createElement) {
        return createElement('a', {
            attrs: {
                href: `index.php?subject="${encodeURIComponent(this.topic).replace(/%20/g, "+")}"&search=search`,
                class: 'btn btn-outline-secondary btn-rounded btn-sm mr-2 mb-2 new-arrival-topic-pill',
                'aria-label': `Koleksi topik ${this.topic}`
            }
        }, [
            createElement('span', { attrs: { class: 'topic-hash', 'aria-hidden': 'true' } }, '#'),
            createElement('span', { attrs: { class: 'topic-text' } }, this.topic)
        ])
    }
});

Vue.component('slims-book', {
    props: {
        biblioId: {
            type: String,
            default: ''
        },
        title: {
            type: String,
            default: ''
        },
        image: {
            type: String,
            default: ''
        }
    },
    render: function (createElement) {
        var firstChar = (this.title && this.title.length > 0) ? this.title.trim().charAt(0).toUpperCase() : 'B';
        var shortTitle = (this.title && this.title.length > 28) ? this.title.substring(0, 28) + '...' : (this.title || '');

        return createElement('div', {
            attrs: {
                class: 'w-48 pr-4 pb-4 new-arrival-col'
            }
        }, [
            createElement('a', {
                attrs: {
                    href: `index.php?p=show_detail&id=${this.biblioId}`,
                    class: 'card border-0 hover:shadow cursor-pointer text-decoration-none h-full new-arrival-card',
                    'aria-label': this.title
                },
            }, [
                createElement('div', {
                    attrs: {
                        class: 'card-body new-arrival-card-body'
                    }
                }, [
                    // Top Cover Wrapper with architectural corners & spine lighting
                    createElement('div', {
                        attrs: {
                            class: 'card-image fit-height new-arrival-cover-wrap'
                        }
                    }, [
                        // Corner accents
                        createElement('span', { attrs: { class: 'new-arrival-corner corner-tl', 'aria-hidden': 'true' } }),
                        createElement('span', { attrs: { class: 'new-arrival-corner corner-br', 'aria-hidden': 'true' } }),
                        // 3D Realistic Spine Lighting
                        createElement('div', { attrs: { class: 'new-arrival-spine', 'aria-hidden': 'true' } }),
                        // Floating Badge
                        createElement('span', { attrs: { class: 'new-arrival-badge-pill', 'aria-hidden': 'true' } }, [
                            createElement('span', { attrs: { class: 'badge-sparkle' } }, '✦'),
                            createElement('span', 'Baru')
                        ]),
                        // Cover Image
                        createElement('img', {
                            attrs: {
                                src: this.image,
                                alt: this.title,
                                class: 'img-fluid new-arrival-cover-img',
                                loading: 'lazy'
                            },
                            on: {
                                error: function (e) {
                                    e.target.style.display = 'none';
                                    var placeholder = e.target.parentElement.querySelector('.new-arrival-fallback-cover');
                                    if (placeholder) placeholder.style.display = 'flex';
                                }
                            }
                        }),
                        // Fallback Cover (displayed if img fails)
                        createElement('div', {
                            attrs: {
                                class: 'new-arrival-fallback-cover',
                                style: 'display: none;',
                                'aria-hidden': 'true'
                            }
                        }, [
                            createElement('div', { attrs: { class: 'fallback-spine' } }),
                            createElement('div', { attrs: { class: 'fallback-letter' } }, firstChar),
                            createElement('div', { attrs: { class: 'fallback-title' } }, shortTitle)
                        ]),
                        // Hover Peek Overlay
                        createElement('div', {
                            attrs: {
                                class: 'new-arrival-hover-overlay',
                                'aria-hidden': 'true'
                            }
                        }, [
                            createElement('span', { attrs: { class: 'new-arrival-peek-btn' } }, [
                                createElement('svg', {
                                    attrs: {
                                        width: '12',
                                        height: '12',
                                        viewBox: '0 0 24 24',
                                        fill: 'none',
                                        stroke: 'currentColor',
                                        'stroke-width': '2.4',
                                        'stroke-linecap': 'round',
                                        'stroke-linejoin': 'round'
                                    }
                                }, [
                                    createElement('path', { attrs: { d: 'M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z' } }),
                                    createElement('circle', { attrs: { cx: '12', cy: '12', r: '3' } })
                                ]),
                                createElement('span', 'Lihat Detail')
                            ])
                        ])
                    ]),
                    // Title
                    createElement('h3', {
                        attrs: {
                            class: 'card-text mt-2 text-grey-darker new-arrival-title',
                            title: this.title
                        },
                        domProps: {
                            innerHTML: this.title
                        }
                    }),
                    // Detail Action
                    createElement('div', {
                        attrs: {
                            class: 'new-arrival-meta-row'
                        }
                    }, [
                        createElement('span', { attrs: { class: 'new-arrival-detail-link' } }, [
                            createElement('span', 'Lihat Detail'),
                            createElement('span', { attrs: { class: 'link-arrow', 'aria-hidden': 'true' } }, '→')
                        ])
                    ])
                ])
            ])
        ]);
    }
});

Vue.component('slims-member', {
    props: {
        image: {
            type: String,
            default: ''
        },
        memberName: {
            type: String,
            default: ''
        },
        memberType: {
            type: String,
            default: ''
        },
        totalLoan: {
            type: String,
            default: '0'
        },
        totalBiblio: {
            type: String,
            default: '0'
        }
    },
    render: function (createElement) {
        return createElement('div', {
            attrs: {
                class: 'w-full md:w-1/3 px-3 mb-2'
            }
        }, [
            createElement('div', {
                attrs: {
                    class: 'card hover:shadow-md'
                }
            }, [
                createElement('div', {
                    attrs: {
                        class: 'card-body'
                    }
                }, [
                    createElement('div', {
                        attrs: {
                            class: 'card-image-rounded mx-auto'
                        }
                    }, [
                        createElement('img', {
                            attrs: {
                                class: 'img-fluid h-auto',
                                src: this.image,
                                loading: 'lazy'
                            }
                        })
                    ]),
                    createElement('h5', {
                        attrs: {
                            class: 'card-title text-center mt-3'
                        }
                    }, [
                        createElement('span', this.memberName),
                        createElement('br'),
                        createElement('small', {
                            attrs: {
                                class: 'text-grey-darker'
                            },
                            domProps: {
                                innerHTML: this.memberType
                            }
                        })
                    ]),
                    createElement('p', {
                        attrs: {
                            class: 'card-text text-center'
                        }
                    }, [
                        createElement('b', this.totalLoan),
                        createElement('span', {
                            attrs: {
                                class: 'text-grey-darker ml-1'
                            },
                            domProps: {
                                innerHTML: 'Loans'
                            }
                        }),
                        createElement('span', {
                            attrs: {
                                class: 'inline-block h-4 mx-3 relative bg-grey align-middle',
                                style: 'width: 1px'
                            }
                        }),
                        createElement('b', this.totalBiblio),
                        createElement('span', {
                            attrs: {
                                class: 'text-grey-darker ml-1'
                            },
                            domProps: {
                                innerHTML: 'Title'
                            }
                        }),
                    ])
                ])
            ])
        ]);
    }
});

Vue.component('slims-collection', {
    props: {
        url: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            biblios: [],
            loading: false
        }
    },
    mounted() {
        this.getData()
    },
    methods: {
        getData() {
            this.loading = true
            fetch(this.url)
                .then(res => res.json())
                .then(res => {
                    this.biblios = res
                })
                .finally(() => {
                    this.loading = false
                })
        }
    },
    render: function (createElement) {
        if (this.loading && this.biblios.length < 1) {
            return createElement('div', {
                attrs: {
                    class: 'new-arrivals-skeleton-grid'
                }
            }, [1, 2, 3, 4, 5, 6].map(function () {
                return createElement('div', {
                    attrs: {
                        class: 'new-arrival-skeleton-card'
                    }
                }, [
                    createElement('div', { attrs: { class: 'skeleton-cover' } }),
                    createElement('div', { attrs: { class: 'skeleton-line skeleton-title' } }),
                    createElement('div', { attrs: { class: 'skeleton-line skeleton-sub' } })
                ]);
            }))
        } else {
            return createElement('div', {
                attrs: {
                    class: 'flex flex-wrap mt-4 collection new-arrivals-grid'
                }
            }, this.biblios.map(function (item) {
                return createElement('slims-book', {
                    attrs: {
                        biblioId: item.biblio_id,
                        image: item.image,
                        title: item.title,
                    }
                })
            }))
        }
    }
});

Vue.component('slims-group-subject', {
    props: {
        url: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            subjects: [],
            loading: false
        }
    },
    mounted() {
        this.getData()
    },
    methods: {
        getData() {
            this.loading = true
            fetch(this.url)
                .then(res => res.json())
                .then(res => {
                    this.subjects = res
                })
                .finally(() => {
                    this.loading = false
                })
        }
    },
    render: function (createElement) {
        if (this.loading && this.subjects.length < 1) {
            return createElement('div', {
                attrs: {
                    class: 'spinner-border text-primary'
                }
            }, [
                createElement('span', {
                    attrs: {
                        class: 'sr-only',
                        role: 'status'
                    },
                    domProps: {
                        innerHTML: 'Loading...'
                    }
                })
            ])
        } else {
            return createElement('div', {
                attrs: {
                    class: 'flex flex-wrap'
                }
            }, this.subjects.map(function (topic) {
                return createElement('slims-subject', {
                    attrs: {
                        topic
                    }
                })
            }))
        }
    }
});

Vue.component('slims-group-member', {
    props: {
        url: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            members: [],
            loading: false
        }
    },
    mounted() {
        this.getData()
    },
    methods: {
        getData() {
            this.loading = true
            fetch(this.url)
                .then(res => res.json())
                .then(res => {
                    this.members = res
                })
                .finally(() => {
                    this.loading = false
                })
        }
    },
    render: function (createElement) {
        if (this.loading && this.members.length < 1) {
            return createElement('div', {
                attrs: {
                    class: 'spinner-border text-primary'
                }
            }, [
                createElement('span', {
                    attrs: {
                        class: 'sr-only',
                        role: 'status'
                    },
                    domProps: {
                        innerHTML: 'Loading...'
                    }
                })
            ])
        } else {
            return createElement('div', {
                attrs: {
                    class: 'flex flex-wrap'
                }
            }, this.members.map(function (member) {
                return createElement('slims-member', {
                    attrs: {
                        memberName: member.name,
                        memberType: member.type,
                        image: member.image,
                        totalLoan: member.total,
                        totalBiblio: member.total_title
                    }
                })
            }))
        }
    }
});

if (document.getElementById('slims-home')) {
    const slimsHome = new Vue({
        el: '#slims-home',
    })
}