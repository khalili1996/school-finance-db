@switch($status)
    @case('present') <span class="badge bg-success">حاضر</span> @break
    @case('blocked') <span class="badge bg-danger">محروم</span> @break
    @case('temporary') <span class="badge bg-warning text-dark">موقت</span> @break
    @case('three_piece') <span class="badge bg-info">سه‌پارچه</span> @break
    @default <span class="badge bg-secondary">{{ $status }}</span>
@endswitch
