@foreach($tours as $tour)
<div style="border:1px solid #ddd; padding:10px; margin-bottom:10px; display:flex; align-items:center;">
    <div>
        @if($tour->image_path)
            <img src="{{ asset('storage/' . $tour->image_path) }}" alt="{{ $tour->name }}" width="150" style="margin-right:15px;" loading="lazy">
        @else
            <div style="width:150px; height:100px; background:#eee; display:flex; align-items:center; justify-content:center;">No Image</div>
        @endif
    </div>
    <div>
        <h3>{{ $tour->name }}</h3>
        <p>{{ $tour->description }}</p>
        <p>Price: ${{ $tour->price }} | Days: {{ $tour->days }}</p>
        <a href="{{ route('tours.edit', $tour->id) }}">Edit</a>
        <form action="{{ route('tours.destroy', $tour->id) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit">Delete</button>
        </form>
    </div>
</div>
@endforeach
