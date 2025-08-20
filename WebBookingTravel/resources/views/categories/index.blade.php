@extends('layouts.app')

@section('content')
<style>
    .cat-header{display:flex;gap:12px;align-items:center;justify-content:space-between;margin-bottom:12px}
    .cat-toolbar{display:flex;gap:8px;align-items:center}
    .badge{background:#eef2ff;color:#1e40af;border:1px solid #c7d2fe;padding:4px 8px;border-radius:999px;font-size:12px}
    .search{padding:8px 10px;border:1px solid #d5d5df;border-radius:6px}
    .cat-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px}
    .cat-card{padding:16px}
    .cat-card:hover{box-shadow:0 8px 24px rgba(0,0,0,.08);transform:translateY(-2px)}
    .muted{color:#6b7280}
    .actions{display:flex;gap:8px;align-items:center;margin-top:8px}
    .btn-danger{background:#ef4444;color:#fff;border:none;padding:6px 10px;border-radius:6px;cursor:pointer}
    @media (max-width: 480px){.cat-grid{grid-template-columns:1fr}}
</style>

<div class="cat-header">
    <h2 style="margin:0">Danh sách danh mục</h2> <br>
    <div class="cat-toolbar">
        <span class="badge">{{ $categories->count() }} danh mục</span>
        <input type="text" id="cat-search" class="search" placeholder="Tìm danh mục..." aria-label="Tìm danh mục theo tên">
        <a href="{{ route('categories.create') }}" 
            class="btn-primary" 
            style="width: 120px; padding: 5px 10px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">
            + Thêm mới
        </a>

    </div>
</div>


<div class="cat-grid" id="cat-grid">
    @forelse($categories as $category)
        <div class="card cat-card" data-name="{{ $category->name }}">
            <div style="display:flex;justify-content:space-between;align-items:center;gap:8px">
                <h3 style="margin:0;flex:1 1 auto;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $category->name }}</h3>
                <span class="badge">{{ $category->tours->count() }} tour</span>
            </div>
            <p class="muted" style="margin:6px 0 0">Mã: 0{{ $category->id }}</p>
            <div class="actions">
                <a href="{{ route('categories.show', $category->id) }}">Xem</a>
                <a href="{{ route('categories.edit', $category->id) }}">Sửa</a>
                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Xóa danh mục này?')" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    @empty
        <div class="card" style="text-align:center;padding:24px;">
            <p class="muted" style="margin:0 0 12px">Chưa có danh mục nào.</p>
            <a href="{{ route('categories.create') }}" class="btn-primary">+ Tạo danh mục đầu tiên</a>
        </div>
    @endforelse
</div>

<script>
    const catSearch = document.getElementById('cat-search');
    const catCards = Array.from(document.querySelectorAll('#cat-grid .cat-card'));
    if (catSearch && catCards.length){
        catSearch.addEventListener('input', function(){
            const q = this.value.trim().toLowerCase();
            catCards.forEach(c => {
                const name = (c.getAttribute('data-name') || '').toLowerCase();
                c.style.display = name.includes(q) ? '' : 'none';
            });
        });
    }
</script>
@endsection
