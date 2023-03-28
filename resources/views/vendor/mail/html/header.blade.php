@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">

@if (file_exists(public_path(('/icons/logo.png'))))
<img src="{{ asset('/icons/logo.png') }}" class="logo" alt="Custom Icon">
@else
<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
@endif
</a>
</td>
</tr>
