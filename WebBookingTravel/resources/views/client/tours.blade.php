@extends('client.layouts.app')
@section('content')
@php use Illuminate\Support\Str; @endphp

<style>
    .tour-card {
        border: 1px solid #e9ecef;
        border-radius: 14px;
        overflow: hidden;
        transition: transform .2s ease, box-shadow .2s ease;
        background: #fff
    }

    .tour-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px -12px rgba(0, 0, 0, .2)
    }

    .tour-thumb {
        position: relative;
        width: 100%;
        padding-top: 62%;
        background: #f6f7fb
    }

    .tour-thumb img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover
    }

    .tour-badges {
        position: absolute;
        left: 10px;
        right: 10px;
        top: 10px;
        display: flex;
        gap: 6px;
        flex-wrap: wrap
    }

    .badge-soft {
        background: rgba(255, 255, 255, .9);
        color: #111;
        border: 1px solid #e7e7ef;
        border-radius: 999px;
        font-size: 12px;
        padding: 4px 8px
    }

    .badge-type {
        background: #eef2ff;
        color: #1e40af;
        border: 1px solid #c7d2fe
    }

    .tour-body {
        padding: 12px 14px 10px
    }

    .tour-title {
        font-weight: 700;
        margin: 0 0 6px;
        font-size: 1.02rem;
        line-height: 1.25;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 2.5em;
        line-clamp: 2;
    }

    .tour-desc {
        color: #4b5563;
        font-size: .92rem;
        margin: 0 0 8px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 3.6em;
        line-clamp: 3;
    }

    .tour-meta {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        color: #374151;
        font-size: .9rem;
        margin-bottom: 10px
    }

    .tour-price {
        display: flex;
        flex-direction: column;
        gap: 2px
    }

    .price-main {
        color: #111;
    }

    .price-main .amount {
        font-weight: 700;
    }

    .price-main .unit {
        color: #6b7280;
        font-size: .9rem;
    }

    .price-note {
        color: #6b7280;
        font-size: .9rem;
    }

    .tour-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 14px 14px
    }
</style>

<div class="container pt-5 py-3">
    <div class="row">
        <div class="col-lg-8 col-xl-9">
            <div id="tours-list">
                @include('client.tours_list_fragment')
            </div>
        </div>
        <div class="col-lg-4 col-xl-3 mt-4 mt-lg-0">
            <form id="tour-filter-form" method="GET" action="{{ url()->current() }}" data-index-url="{{ route('client.tours.index') }}">
                <div class="border rounded-3 p-3 shadow-sm" style="position: sticky; top: 90px;">
                    <h2 class="h6 mb-3">Bộ lọc</h2>
                    <div class="mb-3">
                        <label class="small text-muted">Từ khóa</label>
                        <input type="text" class="form-control" name="q" value="{{ $keyword ?? request('q') }}" placeholder="Tìm tour theo tên, mô tả..." />
                    </div>
                    @if(!empty($filterCategories))
                    <div class="mb-3">
                        <label class="small text-muted">Danh mục</label>
                        <select class="form-select" name="category">
                            <option value="">-- Tất cả --</option>
                            @foreach($filterCategories as $cat)
                            <option value="{{ $cat->categoryID }}" @selected((string)($activeCategory->categoryID ?? request('category')) === (string)$cat->categoryID)>{{ $cat->categoryName }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="mb-3">
                        <label class="small text-muted">Loại tour</label>
                        <select class="form-select" name="type">
                            <option value="">-- Tất cả --</option>
                            <option value="domestic" @selected(($activeType ?? '' )==='domestic' )>Trong nước</option>
                            <option value="international" @selected(($activeType ?? '' )==='international' )>Nước ngoài</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="small text-muted">Điểm đến</label>
                        <select class="form-select" name="departure">
                            <option value="">-- Tất cả --</option>
                            @foreach(($departures ?? []) as $dep)
                            <option value="{{ $dep }}" @selected(($activeDeparture ?? '' )===$dep)>{{ $dep }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="small text-muted">Thay đổi sẽ tự động áp dụng.</div>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function() {
            const form = document.getElementById('tour-filter-form');
            const listWrap = document.getElementById('tours-list');
            if (!form || !listWrap) return;
            const indexUrl = form.getAttribute('data-index-url');
            const spinner = () => '<div class="text-center py-4"><div class="spinner-border text-secondary" role="status"></div></div>';
            const debounce = (fn, delay) => {
                let t;
                return (...args) => {
                    clearTimeout(t);
                    t = setTimeout(() => fn.apply(null, args), delay);
                };
            };
            const fetchList = (url) => {
                listWrap.innerHTML = spinner();
                fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(r => r.text())
                    .then(html => {
                        listWrap.innerHTML = html;
                        bindPagination();
                    })
                    .catch(() => {
                        listWrap.innerHTML = '<div class="alert alert-danger">Không tải được dữ liệu.</div>';
                    });
            };
            const buildUrl = (baseOverride) => {
                const params = new URLSearchParams(new FormData(form));
                const base = baseOverride || form.getAttribute('action');
                const qs = params.toString();
                return qs ? `${base}?${qs}` : base;
            };
            const onChange = (opts = {}) => {
                const url = buildUrl(opts.base);
                window.history.replaceState({}, '', url);
                fetchList(url);
            };
            // Change handlers
            form.addEventListener('change', (e) => {
                const target = e.target;
                if (!(target instanceof HTMLSelectElement)) return;
                if (target.name === 'category') {
                    onChange({
                        base: indexUrl
                    });
                } else {
                    onChange();
                }
            });
            const q = form.querySelector('input[name="q"]');
            if (q) q.addEventListener('input', debounce(() => onChange(), 250));
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                onChange();
            });
            const bindPagination = () => {
                listWrap.querySelectorAll('a.page-link').forEach(a => {
                    a.addEventListener('click', (e) => {
                        e.preventDefault();
                        const url = a.getAttribute('href');
                        if (url) {
                            window.history.replaceState({}, '', url);
                            fetchList(url);
                        }
                    });
                });
            };
            bindPagination();
        })();
    </script>
</div>
@endsection