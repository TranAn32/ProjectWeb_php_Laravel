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

        #filter-search-btn {
            transition: all 0.3s ease;
            font-weight: 500;
        }

        #filter-search-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }

        #filter-search-btn:active {
            transform: translateY(0);
        }
    </style>

    <div class="container pt-5 py-3">
        <div class="row">
            <div class="col-lg-8 col-xl-9">
                <div id="tours-list">
                    @include('client.tours.tours_list_fragment')
                </div>
            </div>
            <div class="col-lg-4 col-xl-3 mt-4 mt-lg-0">
                <form id="tour-filter-form" method="GET" action="{{ url()->current() }}"
                    data-index-url="{{ route('client.tours.index') }}">
                    <div class="border rounded-3 p-3 shadow-sm" style="position: sticky; top: 90px;">
                        <h2 class="h6 mb-3">Bộ lọc</h2>
                        <div class="mb-3">
                            <label class="small text-muted">Từ khóa</label>
                            <input type="text" class="form-control" name="q" value="{{ $keyword ?? request('q') }}"
                                placeholder="Tìm tour theo tên, mô tả..." />
                        </div>
                        @if (!empty($filterCategories))
                            <div class="mb-3">
                                <label class="small text-muted">Danh mục</label>
                                <select class="form-select" name="category">
                                    <option value="">-- Tất cả --</option>
                                    @foreach ($filterCategories as $cat)
                                        <option value="{{ $cat->categoryID }}" @selected((string) ($activeCategory->categoryID ?? request('category')) === (string) $cat->categoryID)>
                                            {{ $cat->categoryName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="mb-3">
                            <label class="small text-muted">Loại tour</label>
                            <select class="form-select" name="type">
                                <option value="">-- Tất cả --</option>
                                <option value="domestic" @selected(($activeType ?? '') === 'domestic')>Trong nước</option>
                                <option value="international" @selected(($activeType ?? '') === 'international')>Nước ngoài</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="small text-muted">Điểm đến</label>
                            <select class="form-select" name="departure">
                                <option value="">-- Tất cả --</option>
                                @foreach ($departures ?? [] as $dep)
                                    <option value="{{ $dep }}" @selected(($activeDeparture ?? '') === $dep)>{{ $dep }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <button type="button" id="filter-search-btn" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>Tìm kiếm
                            </button>
                        </div>
                        <div class="small text-muted">
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('tour-filter-form');
                const listWrap = document.getElementById('tours-list');

                if (!form || !listWrap) {
                    console.error('Form hoặc tours-list không tìm thấy');
                    return;
                }

                const indexUrl = form.getAttribute('data-index-url');
                console.log('Index URL:', indexUrl);

                const spinner = () =>
                    '<div class="text-center py-4"><div class="spinner-border text-secondary" role="status"></div></div>';

                const debounce = (fn, delay) => {
                    let t;
                    return (...args) => {
                        clearTimeout(t);
                        t = setTimeout(() => fn.apply(null, args), delay);
                    };
                };

                const fetchList = (url) => {
                    console.log('Fetching URL:', url);
                    listWrap.innerHTML = spinner();

                    fetch(url, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
                            }
                        })
                        .then(response => {
                            console.log('Response status:', response.status);
                            console.log('Response headers:', response.headers);
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.text();
                        })
                        .then(html => {
                            console.log('Response HTML length:', html.length);
                            listWrap.innerHTML = html;
                            bindPagination();
                            console.log('Tours list updated successfully');
                        })
                        .catch(error => {
                            console.error('Error fetching tours:', error);
                            listWrap.innerHTML =
                                '<div class="alert alert-danger">Không tải được dữ liệu: ' + error.message +
                                '</div>';
                        });
                };

                const buildUrl = (baseOverride) => {
                    const formData = new FormData(form);
                    const params = new URLSearchParams();

                    // Chỉ thêm các parameter có giá trị
                    for (let [key, value] of formData.entries()) {
                        if (value && value.trim() !== '') {
                            params.append(key, value);
                        }
                    }

                    const base = baseOverride || form.getAttribute('action');
                    const qs = params.toString();
                    const finalUrl = qs ? `${base}?${qs}` : base;
                    console.log('Built URL:', finalUrl);
                    return finalUrl;
                };

                const onChange = (opts = {}) => {
                    console.log('onChange triggered with options:', opts);
                    const url = buildUrl(opts.base);
                    window.history.replaceState({}, '', url);
                    fetchList(url);
                };

                // Xử lý nút tìm kiếm cho các dropdown
                const filterSearchBtn = document.getElementById('filter-search-btn');
                if (filterSearchBtn) {
                    filterSearchBtn.addEventListener('click', () => {
                        console.log('Filter search button clicked');
                        onChange();
                    });
                }

                // Chỉ xử lý input text với debounce (tự động)
                const q = form.querySelector('input[name="q"]');
                if (q) {
                    q.addEventListener('input', debounce((e) => {
                        console.log('Input event on q:', e.target.value);
                        onChange();
                    }, 500));
                }

                // Ngăn form submit mặc định và xử lý qua nút tìm kiếm
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    console.log('Form submit prevented, calling onChange');
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

                // Bind pagination khi trang load
                bindPagination();

                console.log('Tour filter script initialized successfully');
            });
        </script>
    </div>
@endsection
