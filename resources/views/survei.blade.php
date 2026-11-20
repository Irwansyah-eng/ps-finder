@extends('layouts.app')

@section('title', 'Survei PS Finder Samarinda')

@section('content')
<div class="wrap">
  <h1>Survei rental PS Samarinda</h1>
  <p class="note">Nilai satu rental yang pernah kamu kunjungi. Pernah ke beberapa rental? Isi survei ini lagi untuk rental lainnya.</p>

  @if (session('ok'))<div class="ok">{{ session('ok') }}</div>@endif
  @if ($errors->any())<div class="err">Masih ada isian yang belum lengkap. Cek kembali bagian yang ditandai.</div>@endif

  <form method="POST" action="{{ route('survei.store') }}" class="box">
    @csrf
    <label class="f">Rental yang kamu nilai
      <select name="rental_id" required>
        <option value="">Pilih rental</option>
        @foreach ($rentals as $r)
          <option value="{{ $r->id }}" @selected(old('rental_id') == $r->id)>{{ $r->nama }}</option>
        @endforeach
      </select>
    </label>
    @error('rental_id')<div class="err">Pilih salah satu rental.</div>@enderror

    <label class="f">Kecamatan domisili (opsional)
      <select name="kecamatan">
        <option value="">Tidak diisi</option>
        @foreach ($kecamatan as $k)
          <option @selected(old('kecamatan') == $k)>{{ $k }}</option>
        @endforeach
      </select>
    </label>

    <p class="note">Beri nilai 1 (sangat buruk) sampai 5 (sangat baik).</p>
    @foreach ($criteria as $k => $l)
      <fieldset class="rt">
        <legend>{{ $l }}</legend>
        @for ($i = 1; $i <= 5; $i++)
          <label><input type="radio" name="{{ $k }}" value="{{ $i }}" @checked(old($k) == $i) required> {{ $i }}</label>
        @endfor
      </fieldset>
      @error($k)<div class="err">Pilih nilai 1 sampai 5 untuk {{ $l }}.</div>@enderror
    @endforeach

    <button class="pri" type="submit">Kirim penilaian</button>
  </form>
</div>
@endsection
